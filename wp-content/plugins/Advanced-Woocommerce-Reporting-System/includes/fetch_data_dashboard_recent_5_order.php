<?php
if($file_used=="sql_table")
{
	
	//show_seleted_order_status
	global $wpdb;
	
	$pw_create_date =  date("Y-m-d");
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

	}
	
	$pw_in_shop_os	= "";
	$pw_in_post_os	= "";
	
	
	//$pw_hide_os='trash';
	//$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
	$pw_hide_os=explode(',',$pw_hide_os);		
	//$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
	//$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
	if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
		$pw_shop_order_status = explode(",",$pw_shop_order_status); 
	else $pw_shop_order_status = array();
	
	if(count($pw_shop_order_status)>0){
		$pw_in_post_os	= implode("', '",$pw_shop_order_status);	
	}
	
	$in_pw_hide_os = "";
	if(count($pw_hide_os)>0){
		$in_pw_hide_os		= implode("', '",$pw_hide_os);				
	}
	
	$per_page=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'recent_post_per_page',5);
	
	$pw_shop_order_status_condition='';
	$pw_hide_os_condition ='';

	$sql_columns = " pw_posts.ID AS order_id, pw_posts.post_date AS order_date, pw_posts.post_status AS order_status";
	$sql_joins = "{$wpdb->prefix}posts as pw_posts";

	$sql_condition= " pw_posts.post_type='shop_order' ";
	
	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$sql_condition.= " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
	}
	
	if(count($pw_shop_order_status)>0){
		$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
		$pw_shop_order_status_condition = " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
	}
	
	if(count($pw_hide_os)>0){
		$in_pw_hide_os		= implode("', '",$pw_hide_os);
		$pw_hide_os_condition = " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
	}
	
	
	$sql_group_by= " GROUP BY pw_posts.ID";
	
	$sql_order_by= " Order By pw_posts.post_date DESC ";
	$sql_limit = " LIMIT {$per_page}";
	
	$sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition
			$pw_shop_order_status_condition $pw_hide_os_condition 
			$sql_group_by $sql_order_by $sql_limit";

	//echo $sql;
	
}elseif($file_used=="data_table"){
	
	foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
				
		$order_id= $items->order_id;
		$fetch_other_data='';				
						
		if(!isset($this->order_meta[$order_id])){
			$fetch_other_data= $this->pw_get_full_post_meta($order_id);
		}
		
		//print_r($fetch_other_data);
		
		$total_amount=0;
		$datatable_value.=("<tr>");
			
			$pw_order_total = isset($fetch_other_data['order_total'])		? $fetch_other_data['order_total'] 		: 0;
			
			$order_shipping= isset($fetch_other_data['order_shipping'])	? $fetch_other_data['order_shipping']	: 0;
			
			$pw_cart_discount= isset($fetch_other_data['cart_discount'])		? $fetch_other_data['cart_discount'] 	: 0;
			
			$pw_order_discount= isset($fetch_other_data['order_discount'])	? $fetch_other_data['order_discount'] 	: 0;
			
			$total_discount = isset($fetch_other_data['total_discount'])	? $fetch_other_data['total_discount'] 	: ($pw_cart_discount + $pw_order_discount);
			
			
			$total_amount+=$pw_order_total;
			//order ID
			$display_class='';
			if($this->table_cols[0]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $order_id;
			$datatable_value.=("</td>");

			//Name
			$display_class='';
			if($this->table_cols[1]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $fetch_other_data['billing_first_name'].' '.$fetch_other_data['billing_last_name'];
			$datatable_value.=("</td>");
			
			//Email
			$display_class='';
			if($this->table_cols[2]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $fetch_other_data['billing_email'];
			$datatable_value.=("</td>");
			
			//Date
			$date_format		= get_option( 'date_format' );
			$display_class='';
			if($this->table_cols[3]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= date($date_format,strtotime($items->order_date));
			$datatable_value.=("</td>");
			
			//Status
			$pw_table_value=$items->order_status;
			if($pw_table_value=='wc-completed')
				$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
			else if($pw_table_value=='wc-refunded')
				$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
			else
				$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
			
			$display_class='';
			if($this->table_cols[4]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= str_replace("Wc-","",$pw_table_value);
			$datatable_value.=("</td>");
							
			//Items
			$display_class='';
			$order_items_cnt=$this->pw_get_oi_count($items->order_id,'line_item');
			if($this->table_cols[5]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.=isset($order_items_cnt[$items->order_id]) ? $order_items_cnt[$items->order_id]:0;
			$datatable_value.=("</td>");
			
			//Gross Amt.
			$display_class='';
			if($this->table_cols[6]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price(($pw_order_total + $total_discount) - ($fetch_other_data['order_shipping'] +  $fetch_other_data['order_shipping_tax'] + $fetch_other_data['order_tax'] ),array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Order Discount Amt.
			$display_class='';
			if($this->table_cols[7]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($pw_order_discount,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
				   
			//Cart Discount Amt.
			$display_class='';
			if($this->table_cols[8]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price(isset($fetch_other_data['cart_discount'])		? $fetch_other_data['cart_discount'] 	: 0 ,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
									
			//Total Discount Amt.
			$display_class='';
			if($this->table_cols[9]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($total_discount ,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Shipping Amt.
			$display_class='';
			if($this->table_cols[10]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($fetch_other_data['order_shipping'] ,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Shipping Tax Amt.
			$display_class='';
			if($this->table_cols[11]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price(isset($fetch_other_data['order_shipping_tax'])? $fetch_other_data['order_shipping_tax'] : 0 ,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Order Tax Amt.
			$display_class='';
			if($this->table_cols[12]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price(isset($fetch_other_data['order_tax'])? $fetch_other_data['order_tax'] : 0 ,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Total Tax Amt.
			$display_class='';
			if($this->table_cols[13]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price(isset($fetch_other_data['total_tax'])? $fetch_other_data['total_tax'] 	: ($fetch_other_data['order_tax'] + $fetch_other_data['order_shipping_tax']),array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
			//Part Refund Amt.
			$display_class='';
			$order_refund_amnt=$this->pw_get_por_amount($items->order_id);
			if($this->table_cols[14]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= (isset($order_refund_amnt[$items->order_id])? $this->price($order_refund_amnt[$items->order_id],array("currency" => $fetch_other_data['order_currency'])):$this->price(0,array("currency" => $fetch_other_data['order_currency'])));
			$datatable_value.=("</td>");
			
			//Net Amt.
			$display_class='';
			if($this->table_cols[15]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $this->price($total_amount,array("currency" => $fetch_other_data['order_currency']));
			$datatable_value.=("</td>");
			
		$datatable_value.=("</tr>");
	}
}elseif($file_used=="search_form"){}

?>