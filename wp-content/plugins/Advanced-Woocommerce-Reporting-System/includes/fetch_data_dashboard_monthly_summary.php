<?php

if($file_used=="sql_table")
{
	
	
}elseif($file_used=="data_table"){
	
	$pw_from_date=$this->pw_from_date_dashboard;
	$pw_to_date=$this->pw_to_date_dashboard;
	
	$pw_hide_os=$this->otder_status_hide;
	$pw_shop_order_status=$this->pw_shop_status;
	
	if(isset($_POST['pw_from_date']))
	{
		//parse_str($_REQUEST, $my_array_of_vars);
		$this->search_form_fields=$_POST;

		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
		$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
		$pw_create_date =  $pw_from_date;
	}
	
	//$pw_hide_os='trash';
	//$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
	$pw_hide_os=explode(',',$pw_hide_os);		
	//$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
	//$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
	$pw_shop_order_status=explode(',',$pw_shop_order_status);
	$pw_cur_projected_sales_year=substr($pw_from_date,0,4);
	
	$pw_cur_projected_sales_year= $this->pw_get_woo_requests('pw_proj_sale_year',$pw_cur_projected_sales_year,true);
				
	$pw_from_date			= $pw_cur_projected_sales_year."-01-01";
	$start_month_time	= strtotime($pw_from_date);
	$month_count		= 12;
	$end_month_time		= strtotime("+{$month_count} month", $start_month_time);
	$pw_to_date			= date("Y-m-d",$end_month_time);
	
	$parameters = array('shop_order_status'=>$pw_shop_order_status,'pw_hide_os'=>$pw_hide_os,'pw_from_date'=>$pw_from_date,'pw_to_date'=>$pw_to_date);

	$pw_shop_order_status	= isset($parameters['shop_order_status']) ? $parameters['shop_order_status']	: array();
	$pw_hide_os	= isset($parameters['pw_hide_os']) ? $parameters['pw_hide_os']	: array();
	$pw_from_date			= isset($parameters['pw_from_date'])		? $parameters['pw_from_date']			: NULL;
	$pw_to_date			= isset($parameters['pw_to_date'])			? $parameters['pw_to_date']				: NULL;
	
				
	$pw_from_date			= $pw_cur_projected_sales_year."-01-01";
	$start_month_time	= strtotime($pw_from_date);
	$month_count		= 12;
	$end_month_time		= strtotime("+{$month_count} month", $start_month_time);
	$pw_to_date			= date("Y-m-d",$end_month_time);
	
	
	
	$pw_null_val				= $this->price(0);
	
	//$pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date, $month_count = 12, $pw_cur_projected_sales_year = 2010
	
	$pw_refunded_id 		= $this->pw_get_woo_old_orders_status(array('refunded'), array('wc-refunded'));
	$pw_order_total		= $this->pw_get_woo_ts_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date,"_order_total");
	
	//print_r($pw_order_total);
	
	$pw_order_discount		= $this->pw_get_woo_ts_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date,"_order_discount");
	$pw_cart_discount		= $this->pw_get_woo_ts_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date,"_cart_discount");
	$pw_refund_order_total = $this->pw_get_woo_tss_months($pw_refunded_id, $pw_hide_os, $pw_from_date, $pw_to_date, $month_count,"_order_total");
	
	$order_shipping_tax	= $this->pw_get_woo_ts_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date,"_order_shipping_tax");
	$order_tax			= $this->pw_get_woo_ts_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date,"_order_tax");		

	$part_refund		= $this->pw_get_woo_pora_months($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date, $month_count);
	//,"_cart_discount"
	//$this->print_array($pw_order_discount);
	//$this->print_array($pw_cart_discount);
	
	$start_month		= $pw_from_date;
	$start_month_time	= strtotime($start_month);			
	$month_list			= array();
	$pw_fetchs_data			= array();
	$total_projected	= 0;
	$i         			= 0;
	$m         			= 0;			
	
	for ($m=0; $m < ($month_count); $m++){
		$c					= 	strtotime("+$m month", $start_month_time);
		$key				= 	date('F-Y', $c);
		$value				= 	date('F', 	$c);
		$month_list[$key]	=	$value;
	}
	
	$months_translate = array("January"=>"jan", "February"=>"feb", "March"=>"mar", "April"=>"apr", "May"=>"may", "June"=>"jun",
  "July"=>"jul", "August"=>"agu", "September"=>"sep", "October"=>"oct", "November"=>"nov", "December"=>"dec");
	
	$months = array("January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December");
	$projected_amounts 			='';
	foreach($months as $month){
		$key= $month;
		$value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$pw_cur_projected_sales_year.'_'.$month);
		if($value=='')
			$value=$month;
		$projected_amounts[$key]=$value;	
	}
				
	foreach ($month_list as $key => $value) {
		
		$projected_sales_month					=	$value;
		$projected_sales_month_amount			=	isset($projected_amounts[$projected_sales_month]) ? $projected_amounts[$projected_sales_month] : 100;
		$pw_fetchs_data[$i]["projected"] 			=	$projected_sales_month_amount;
		
		////////TRANSLATE MONTHS////////
		$m_name=explode("-",$key);
		$translate_month=$months_translate[$m_name[0]];
		$keys				= 	$this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.$translate_month.'_translate',$m_name[0]).'-'.$m_name[1];
		////////////////////
		
		$pw_fetchs_data[$i]["month_name"] 			=	$keys;
		$pw_fetchs_data[$i]["monthname"] 			=	$value;
		$pw_fetchs_data[$i]["part_order_refund_amount"] 	=	isset($part_refund[$key]) 		? $part_refund[$key] 			: 0;
		
		$this_order_total 						=	isset($pw_order_total[$key]) 			? $pw_order_total[$key] 			: 0;
		$this_order_total						=	strlen($this_order_total)>0			? $this_order_total 			: 0;				
		$this_order_total						=	$this_order_total - $pw_fetchs_data[$i]["part_order_refund_amount"];
		$pw_fetchs_data[$i]["order_total"] 			=	$this_order_total;
		
		$pw_fetchs_data[$i]["refund_order_total"]	=	isset($pw_refund_order_total[$key]) 	? $pw_refund_order_total[$key]		: 0;
		
		$pw_fetchs_data[$i]["actual_min_porjected"]	=	$pw_fetchs_data[$i]["order_total"] - $projected_sales_month_amount;
		$pw_fetchs_data[$i]["order_discount"] 		=	isset($pw_order_discount[$key]) 		? $pw_order_discount[$key] 		: 0;
		$pw_fetchs_data[$i]["cart_discount"] 		=	isset($pw_cart_discount[$key]) 		? $pw_cart_discount[$key] 		: 0;
		$pw_fetchs_data[$i]["total_discount"] 		=	$pw_fetchs_data[$i]["order_discount"] + $pw_fetchs_data[$i]["cart_discount"];
		
		
		$pw_fetchs_data[$i]["order_shipping_tax"] 	=	isset($order_shipping_tax[$key]) 	? $order_shipping_tax[$key] 	: 0;
		$pw_fetchs_data[$i]["order_tax"] 			=	isset($order_tax[$key]) 			? $order_tax[$key] 				: 0;
		$pw_fetchs_data[$i]["refund_order_total"]	=	isset($pw_refund_order_total[$key]) 	? $pw_refund_order_total[$key]		: 0;
		
		$pw_fetchs_data[$i]["total_shipping_tax"] 	=	$pw_fetchs_data[$i]["order_tax"] + $pw_fetchs_data[$i]["order_shipping_tax"];
		$pw_fetchs_data[$i]["actual_min_porjected"]	=	$pw_fetchs_data[$i]["order_total"] - $projected_sales_month_amount;
		
		$total_projected						=	$total_projected + $pw_fetchs_data[$i]["projected"];
		$i++;
	}
	
	foreach ($pw_fetchs_data as $key => $value) {
		$pw_order_total							=	isset($value["order_total"]) 	? trim($value["order_total"])	: 0;
		$pw_order_total							=	strlen(($pw_order_total)) > 0 		? $pw_order_total	: 0;
		
		$pw_fetchs_data[$key]["totalsalse"]			=	$this->pw_get_number_percentage($pw_order_total,$total_projected);
		$pw_fetchs_data[$key]["actual_porjected_per"]=	$this->pw_get_number_percentage($pw_order_total,$value["projected"]);
	}
	
	//die(p);
	
	$pw_order_total 				=	$this->pw_get_woo_total($pw_fetchs_data,'order_total');
	$pw_order_discount 			=	$this->pw_get_woo_total($pw_fetchs_data,'order_discount');
	$pw_cart_discount 				=	$this->pw_get_woo_total($pw_fetchs_data,'cart_discount');
	$total_discount 			=	$this->pw_get_woo_total($pw_fetchs_data,'total_discount');
	$order_shipping_tax 		=	$this->pw_get_woo_total($pw_fetchs_data,'order_shipping_tax');
	$order_tax 					=	$this->pw_get_woo_total($pw_fetchs_data,'order_tax');
	$pw_refund_order_total 		=	$this->pw_get_woo_total($pw_fetchs_data,'refund_order_total');
	$total_shipping_tax 		=	$this->pw_get_woo_total($pw_fetchs_data,'total_shipping_tax');
	$part_order_refund_amount	=	$this->pw_get_woo_total($pw_fetchs_data,'part_order_refund_amount');
	
	$actual_min_porjected 		=	$this->pw_get_woo_total($pw_fetchs_data,'actual_min_porjected');
	
	$pw_order_total				= trim($pw_order_total);
	$pw_order_total				= strlen($pw_order_total) > 0 ? $pw_order_total : 0; 
	
	$pw_fetchs_data[$i]["month_name"] 				=	"Total";
	$pw_fetchs_data[$i]["order_total"] 				=	$pw_order_total;
	$pw_fetchs_data[$i]["order_discount"] 			=	$pw_order_discount;
	$pw_fetchs_data[$i]["cart_discount"] 			=	$pw_cart_discount;
	$pw_fetchs_data[$i]["total_discount"] 			=	$total_discount;
	$pw_fetchs_data[$i]["order_shipping_tax"] 		=	$order_shipping_tax;
	$pw_fetchs_data[$i]["order_tax"] 				=	$order_tax;
	$pw_fetchs_data[$i]["refund_order_total"] 		=	$pw_refund_order_total;
	$pw_fetchs_data[$i]["total_shipping_tax"] 		=	$total_shipping_tax;
	$pw_fetchs_data[$i]["projected"] 				=	$total_projected;
	$pw_fetchs_data[$i]["couppevment"] 				=	$this->pw_get_number_percentage($pw_order_total,$total_projected);
	$pw_fetchs_data[$i]["totalsalse"] 				=	$this->pw_get_number_percentage($pw_order_total,$total_projected);
	
	$pw_fetchs_data[$i]["actual_min_porjected"]		=	$actual_min_porjected;
	$pw_fetchs_data[$i]["actual_porjected_per"]		=	$this->pw_get_number_percentage($pw_order_total,$total_projected);
	$pw_fetchs_data[$i]["part_order_refund_amount"]	=	$part_order_refund_amount;
	
	
	$projected_sales_month					=	$value;

	
	foreach($pw_fetchs_data as $items){
	//for($i=1; $i<=20 ; $i++){
		
		if($items['month_name']=='Total') continue;
						
		$datatable_value.=("<tr>");
								
			//Month
			$display_class='';
			if($this->table_cols[0]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $items['month_name'];
			$datatable_value.=("</td>");
			
			//Target Sales
			$display_class='';
			if($this->table_cols[1]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['projected']);
			$datatable_value.=("</td>");
			
			//Actual Sales
			$display_class='';
			if($this->table_cols[2]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['order_total']);
			$datatable_value.=("</td>");
			
			//Difference
			$display_class='';
			if($this->table_cols[3]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['actual_min_porjected']);
			$datatable_value.=("</td>");
			
			//%
			$display_class='';
			if($this->table_cols[4]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= round($items['actual_porjected_per'],2)."%";
			$datatable_value.=("</td>");
			
			//Total % To Sale
			$display_class='';
			if($this->table_cols[4]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= round($items['totalsalse'],2)."%";
			$datatable_value.=("</td>");
			
			//Refund Amt.
			$display_class='';
			if($this->table_cols[6]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['refund_order_total']);
			$datatable_value.=("</td>");
			
										
			//Part Refund Amount
			$display_class='';
			if($this->table_cols[7]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['part_order_refund_amount']);
			$datatable_value.=("</td>");
			
			//Total Discount Amt.
			$display_class='';
			if($this->table_cols[8]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['total_discount']);
			$datatable_value.=("</td>");
			
			//Tax Amt.
			$display_class='';
			if($this->table_cols[9]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['order_tax']);
			$datatable_value.=("</td>");
			
			//Shipping Order Tax
			$display_class='';
			if($this->table_cols[10]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['order_shipping_tax']);
			$datatable_value.=("</td>");
			
			//Total Shipping Tax
			$display_class='';
			if($this->table_cols[11]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($items['total_shipping_tax']);
			$datatable_value.=("</td>");
			
			
			
			
		$datatable_value.=("</tr>");
	}
}elseif($file_used=="search_form"){}

?>