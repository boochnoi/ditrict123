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

		}
		
		$date_format		= get_option( 'date_format' );
		$pw_total_shop_day 		= $this->pw_get_dashboard_tsd();
		$datetime= date_i18n("Y-m-d H:i:s");
		
		//echo $pw_total_shop_day;
		$pw_hide_os=explode(',',$pw_hide_os);		
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		//die($pw_shop_order_status.' - '.$pw_hide_os.' - '.$pw_from_date.' - '.$pw_to_date);
		
		$total_part_refund_amt	= $this->dashboard_pw_get_por_amount('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$_total_orders 			= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$total_orders 			= $this->pw_get_dashboard_value($_total_orders,'total_count',0);
		$total_sales 			= $this->pw_get_dashboard_value($_total_orders,'total_amount',0);
		
		$total_sales			= $total_sales - $total_part_refund_amt;
		
		
		//type, color,icon, title, amount, count, progress_amount_1, progress_amount_1
		//
		
		$total_sales_avg		= $this->pw_get_dashboard_avarages($total_sales,$total_orders);
		$total_sales_avg_per_day= $this->pw_get_dashboard_avarages($total_sales,$pw_total_shop_day);

		$_todays_orders 		= $this->pw_get_dashboard_totals_orders('today',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$total_today_order 		= $this->pw_get_dashboard_value($_todays_orders,'total_count',0);
		$total_today_sales 		= $this->pw_get_dashboard_value($_todays_orders,'total_amount',0);

		$total_today_avg		= $this->pw_get_dashboard_avarages($total_today_sales,$total_today_order);
		
		$total_categories  		= $this->pw_get_dashboard_tcc();
		$total_products  		= $this->pw_get_dashboard_tpc();
		$total_orders_shipping	= $this->pw_get_dashboard_toss('total',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);		

		
		$total_refund 			= $this->pw_get_dashboard_tbs("total","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);
		$today_refund 			= $this->pw_get_dashboard_tbs("today","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$today_part_refund_amt	= $this->dashboard_pw_get_por_amount('today',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$total_refund_amount 	= $this->pw_get_dashboard_value($total_refund,'total_amount',0);
		$total_refund_count 	= $this->pw_get_dashboard_value($total_refund,'total_count',0);
		
		$total_refund_amount	= $total_refund_amount + $total_part_refund_amt;
		
		$todays_refund_amount 	= $this->pw_get_dashboard_value($today_refund,'total_amount',0);
		$todays_refund_count 	= $this->pw_get_dashboard_value($today_refund,'total_count',0);
		
		$todays_refund_amount	= $todays_refund_amount + $today_part_refund_amt;
		
		$today_coupon 			= $this->pw_get_dashboard_totals_coupons("today",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$total_coupon 			= $this->pw_get_dashboard_totals_coupons("total",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$today_coupon_amount 	= $this->pw_get_dashboard_value($today_coupon,'total_amount',0);
		$today_coupon_count 	= $this->pw_get_dashboard_value($today_coupon,'total_count',0);
		
		$total_coupon_amount 	= $this->pw_get_dashboard_value($total_coupon,'total_amount',0);
		$total_coupon_count 	= $this->pw_get_dashboard_value($total_coupon,'total_count',0);
	
		$today_order_tax 		= $this->pw_get_dashborad_totals_orders("today","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$total_order_tax 		= $this->pw_get_dashborad_totals_orders("total","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$today_ord_tax_amount	= $this->pw_get_dashboard_value($today_order_tax,'total_amount',0);
		$today_ord_tax_count 	= $this->pw_get_dashboard_value($today_order_tax,'total_count',0);
		
		$total_ord_tax_amount	= $this->pw_get_dashboard_value($total_order_tax,'total_amount',0);
		$total_ord_tax_count 	= $this->pw_get_dashboard_value($total_order_tax,'total_count',0);
		
		$today_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("today","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$total_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("total","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$today_ordshp_tax_amount= $this->pw_get_dashboard_value($today_ord_shipping_tax,'total_amount',0);
		$today_ordshp_tax_count = $this->pw_get_dashboard_value($today_ord_shipping_tax,'total_count',0);
		
		$total_ordshp_tax_amount= $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_amount',0);
		$total_ordshp_tax_count = $this->pw_get_dashboard_value($total_ord_shipping_tax,'total_count',0);

		$ytday_order_tax		= $this->pw_get_dashborad_totals_orders("yesterday","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$ytday_ord_shipping_tax	= $this->pw_get_dashborad_totals_orders("yesterday","_order_shipping_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		
		$ytday_tax_amount		= $this->pw_get_dashboard_value($ytday_order_tax,'total_amount',0);
		$ytday_ordshp_tax_amount= $this->pw_get_dashboard_value($ytday_ord_shipping_tax,'total_amount',0);
		$ytday_total_tax_amount = $ytday_tax_amount + $ytday_ordshp_tax_amount;
		
		$today_tax_amount		= $today_ordshp_tax_amount + $today_ord_tax_amount;
		$today_tax_count 		= '';
		
		$total_tax_amount		= $total_ordshp_tax_amount + $total_ord_tax_amount;
		$total_tax_count 		= '';
		
		$last_order_details 	= $this->pw_get_dashboard_lod($pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
			
		$last_order_date 		= $this->pw_get_dashboard_value($last_order_details,'last_order_date','');
		$last_order_time		= strtotime($last_order_date);
		
		$short_date_format		= str_replace("F","M",$date_format);//Modified 20150209
		
		$current_time 			= strtotime($datetime);
		$last_order_time_diff	= $this->pw_get_dashboard_time($last_order_time, $current_time ,' ago');			
		
		$users_of_blog 			= count_users();			
		$total_customer 		= isset($users_of_blog['avail_roles']['customer']) ? $users_of_blog['avail_roles']['customer'] : 0;

		$total_reg_customer 	= $this->pw_get_dashboard_ttoc('total',false);
		$total_guest_customer 	= $this->pw_get_dashboard_ttoc('total',true);
		
		$today_reg_customer 	= $this->pw_get_dashboard_ttoc('today',false);
		$today_guest_customer 	= $this->pw_get_dashboard_ttoc('today',true);
		
		$yesterday_reg_customer	= $this->pw_get_dashboard_ttoc('yesterday',false);
		$yesterday_guest_customer= $this->pw_get_dashboard_ttoc('yesterday',true);
		
		$yesterday_orders 			= $this->pw_get_dashboard_totals_orders('yesterday',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$total_yesterday_order 		= $this->pw_get_dashboard_value($yesterday_orders,'total_count',0);
		$total_yesterday_sales 		= $this->pw_get_dashboard_value($yesterday_orders,'total_amount',0);

		$total_yesterday_avg		= $this->pw_get_dashboard_avarages($total_yesterday_sales,$total_yesterday_order);
				
		$yesterday_part_refund_amt	= $this->dashboard_pw_get_por_amount('yesterday',$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$yesterday_refund 			= $this->pw_get_dashboard_tbs("yesterday","refunded",$pw_hide_os,$pw_from_date,$pw_to_date);
		
		
		$yesterday_refund_amount 	= $this->pw_get_dashboard_value($yesterday_refund,'total_amount',0);
		$yesterday_refund_amount 	= $yesterday_refund_amount + $yesterday_part_refund_amt;
		
		$yesterday_coupon 			= $this->pw_get_dashboard_totals_coupons("yesterday",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$yesterday_coupon_amount 	= $this->pw_get_dashboard_value($yesterday_coupon,'total_amount',0);
		
		$yesterday_tax 				= $this->pw_get_dashborad_totals_orders("yesterday","_order_tax","tax",$pw_shop_order_status,$pw_hide_os,$pw_from_date,$pw_to_date);
		$yesterday_tax_amount 		= $this->pw_get_dashboard_value($yesterday_tax,'total_amount',0);
		
		$days_in_this_month 		= date('t', mktime(0, 0, 0, date('m', $current_time), 1, date('Y', $current_time)));
		
		
		
		$pw_cur_projected_sales_year=substr($pw_from_date,0,4);
		//$pw_cur_projected_sales_year	= $this->pw_get_dashboard_numbers('cur_projected_sales_year',date('Y',$current_time));
		$projected_pw_from_date		= $pw_cur_projected_sales_year."-01-01";
		$projected_pw_to_date			= $pw_cur_projected_sales_year."-12-31";
		
		$projected_total_orders		= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$projected_pw_from_date,$projected_pw_to_date);
		$projected_order_amount 	= $this->pw_get_dashboard_value($projected_total_orders,'total_amount',0);
		$projected_order_count 		= $this->pw_get_dashboard_value($projected_total_orders,'total_count',0);
		
		$total_projected_amount		= $this->pw_get_dashboard_numbers('total_projected_amount',0);
		
		//////////////////////////////////
		$months = array("January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December");
  		$projected_amounts 			='';
		foreach($months as $month){
			$key= $month;
			$value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'monthes_'.$pw_cur_projected_sales_year.'_'.$month);
			$total_projected_amount+=$value;
		}
		/////////////////////////////////
		
		$projected_percentage		= $this->pw_get_number_percentage($projected_order_amount,$total_projected_amount);
		
		$projected_pw_from_date_cm	= date($pw_cur_projected_sales_year.'-m-01',$current_time);
		$projected_pw_to_date_cm		= date($pw_cur_projected_sales_year.'-m-t',$current_time);
		$projected_sales_month		= date('F',$current_time);
		$projected_sales_month_shrt	= date('M',$current_time);
		
		$projected_total_orders_cm	= $this->pw_get_dashboard_totals_orders('total',$pw_shop_order_status,$pw_hide_os,$projected_pw_from_date_cm,$projected_pw_to_date_cm);
		$projected_order_amount_cm 	= $this->pw_get_dashboard_value($projected_total_orders_cm,'total_amount',0);
		$projected_order_count_cm 	= $this->pw_get_dashboard_value($projected_total_orders_cm,'total_count',0);
		
		
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
		
		
		$total_projected_amount_cm	= isset($projected_amounts[$projected_sales_month]) ? $projected_amounts[$projected_sales_month] : 100;
		
		
		$projected_percentage_cm	= $this->pw_get_number_percentage($projected_order_amount_cm,$total_projected_amount_cm);
		
		$this_month_date			= date('d',$current_time);
		
		$per_day_sales_amount		= $this->pw_get_dashboard_avarages($projected_order_amount_cm,$this_month_date);
		
		$per_day_sales_amount		= round(($per_day_sales_amount),2);
		$sales_forcasted 			= $per_day_sales_amount * $days_in_this_month;
		
		$current_total_sales_apd	= $this->pw_get_dashboard_avarages($projected_order_amount_cm,$this_month_date);
		
		
		//echo '<div class="clearboth"></div><div class="awr-box-title awr-box-title-nomargin">'.__('Total Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';
		
		$type='simple';
		$total_summary ='';
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'red-1', 'chart', __('Total Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales, 'price', $total_orders, 'number');

		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-1', 'category', __('Total Refund',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_refund_amount, 'price', $total_refund_count, 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'piechart', __('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_tax_amount, 'price', $total_tax_count, 'precent');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'brown-1', 'like', __('Total Coupons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_coupon_amount, 'price', $total_coupon_count, 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'red-2', 'category', __('Total Registered',__PW_REPORT_WCREPORT_TEXTDOMAIN__), "#".$total_customer, 'other', '', 'number');
		
		$total_summary .= $this->pw_get_dashboard_boxes_generator($type, 'green-1', 'piechart', __('Total Guest Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), "#".$total_guest_customer, 'other', '', 'number');
				
		
		
		//echo '<div class="clearboth"></div><div class="awr-box-title">'.__('Other Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';
		
		$other_summary='';
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'orange-2', 'filter', __('Cur. Yr Proj. Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$pw_cur_projected_sales_year.')', $total_projected_amount, 'price', $projected_percentage, 'precent');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'piechart', __('Current Year Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$pw_cur_projected_sales_year.')', $projected_order_amount, 'price', $projected_order_count, 'number');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'green-1', 'piechart', __('Average Sales Per Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales_avg, 'price', $total_orders, 'number');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'pink-2', 'filter', __('Average Sales Per Day',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_sales_avg_per_day, 'price', $total_orders, 'number');

		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'pink-2', 'like', __('Current Month Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')', $projected_order_amount_cm, 'price', $projected_percentage_cm, 'number');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'category', __('Cur. Month Proj. Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')', $total_projected_amount_cm, 'price', $projected_order_count_cm, 'number');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'piechart', '('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')'. __('Average Sales/Day',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $current_total_sales_apd, 'price', '', 'number');

		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'brown-2', 'basket', '('.$projected_sales_month_shrt.' '.$pw_cur_projected_sales_year.')'. __('Forecasted Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $sales_forcasted, 'price', '', 'number');

		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'orange-1', 'basket', __('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_ord_tax_amount, 'price', $total_ord_tax_count, 'precent');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'green-2', 'basket', __('Order Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_ordshp_tax_amount, 'price', $total_ordshp_tax_count, 'precent');

		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'red-2', 'filter', __('ORDER SHIPPING TOTAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_orders_shipping, 'price', $total_orders, 'number');
		
		$amount='';
		$count='';
		if ( $last_order_date) $amount= date($short_date_format,$last_order_time); 	  else $amount=__( '0', 'icwoocommerce_textdomains');
       
	   if ( $last_order_time_diff) $count= $last_order_time_diff; else $count=__( '0', 'icwoocommerce_textdomains');
		
		$other_summary .= $this->pw_get_dashboard_boxes_generator($type, 'green-2', 'like', __('Last Order Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $amount, 'other', $count, 'other');
		

		//echo '<div class="clearboth"></div><div class="awr-box-title">'.__('Todays Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';
		$today_summary='';
		$type='progress';
		
		$progress_html= $this->pw_get_dashboard_progress_contents($total_today_sales,$total_yesterday_sales);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'red-1', 'basket', __('Todays Total Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_today_sales, 'price', $total_today_order, 'number',$progress_html);
		
		$progress_html= $this->pw_get_dashboard_progress_contents($total_today_avg,$total_yesterday_avg);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'orange-1', 'chart', __('Todays Average Sales',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $total_today_avg, 'price','', 'number',$progress_html);
		
		
		$progress_html= $this->pw_get_dashboard_progress_contents($todays_refund_amount,$yesterday_refund_amount);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'pink-1', 'category', __('Todays Total Refund',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $todays_refund_amount, 'price', $todays_refund_count, 'number',$progress_html);
		
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_coupon_amount,$yesterday_coupon_amount);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'brown-1', 'filter', __('Todays Total Coupons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_coupon_amount, 'price', $today_coupon_count, 'number',$progress_html);
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_tax_amount,$ytday_tax_amount);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'blue-2', 'piechart', __('Todays Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_ord_tax_amount, 'price', $today_ord_tax_count, 'number',$progress_html);
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_tax_amount,$ytday_ordshp_tax_amount);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'red-2', 'like', __('Todays Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_ordshp_tax_amount, 'price', $today_ordshp_tax_count, 'number',$progress_html);
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_tax_amount,$ytday_total_tax_amount);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'green-2', 'category', __('Todays Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $today_tax_amount, 'price', $today_tax_count, 'number',$progress_html);
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_reg_customer,$yesterday_reg_customer);
		
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'pink-2', 'basket', __('Todays Registered Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__), "#".$today_reg_customer, 'other', '', 'number',$progress_html);
		
		
		$progress_html= $this->pw_get_dashboard_progress_contents($today_guest_customer,$yesterday_guest_customer);
		$today_summary.= $this->pw_get_dashboard_boxes_generator($type, 'orange-2', 'like', __('Todays Guest Customers',__PW_REPORT_WCREPORT_TEXTDOMAIN__),  "#".$today_guest_customer, 'other', '', 'number',$progress_html);
		
		
		//echo '<div class="clearboth"></div><div class="awr-box-title">'.__('Other Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div><div class="clearboth"></div>';
		
		if($this->get_dashboard_capability('summary_boxes')){
		
		$htmls='
		<div class="tabs tabsB tabs-style-underline"> 
			<nav>
				<ul class="tab-uls">';
					
					if($this->get_dashboard_capability('total_summary')){
						$htmls.='
					<li><a href="#section-bar-1" > <div><i class="fa fa-cogs"></i>'.__('Total Summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div></a></li>';
					}
					
					if($this->get_dashboard_capability('other_summary')){
						$htmls.='	
					<li><a href="#section-bar-2" > <div><i class="fa fa-columns"></i>'.__('Other summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div></a></li>';
					}
					
					if($this->get_dashboard_capability('today_summary')){
						$htmls.='
					<li><a href="#section-bar-3" > <div><i class="fa fa-columns"></i>'.__('Today summary',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</div></a></li>';
					}
					$htmls.='
				</ul>
			</nav>
			<div class="content-wrap">';
				
				if($this->get_dashboard_capability('total_summary')){
						$htmls.='
				<section id="section-bar-1">
					'.$total_summary.'
				</section>';
				}
			  
			  	if($this->get_dashboard_capability('other_summary')){
						$htmls.='
				<section id="section-bar-2">
					'.$other_summary.'
				</section>';
				}
				
				if($this->get_dashboard_capability('today_summary')){
						$htmls.='
				<section id="section-bar-3">
					'.$today_summary.'
				</section>';
				}
				$htmls.='
			</div>
		</div>
		';	
			echo $htmls;
		}
		
	}elseif($file_used=="search_form"){
		$pw_from_date=$this->pw_from_date_dashboard;
		$pw_to_date=$this->pw_to_date_dashboard;

		if(isset($_POST['pw_from_date']))
		{
			$this->search_form_fields=$_POST;
			$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
			$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		}
			
	?>
		<form class='alldetails search_form_reports' action='' method='post' id="dashboard_form">
            <input type='hidden' name='action' value='submit-form' />
            <input type='hidden' name="pw_from_date" id="pwr_from_date_dashboard" value="<?php echo $pw_from_date;?>"/>
            <input type='hidden' name="pw_to_date" id="pwr_to_date_dashboard"  value="<?php echo $pw_to_date;?>"/>
            
			<div class="page-toolbar">
				
				<input type="submit" value="Search" class="button-primary"/>	
				<div id="dashboard-report-range" class="pull-right tooltips  btn-fit-height grey-salt" data-placement="top" data-original-title="Change dashboard date range">
					<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
						<span></span> <b class="caret"></b>
					</div>
				</div>
				
				<?php
					$pw_hide_os='trash';
					$pw_publish_order='no';
					
					$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
				?>
				<input type="hidden" name="list_parent_category" value="">
				<input type="hidden" name="group_by_parent_cat" value="0">
				
				<input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
				<input type="hidden" name="publish_order" id="publish_order" value="<?php echo $pw_publish_order;?>" />
			
				<input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format;?>" />
			
				<input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
				<div class="fetch_form_loading dashbord-loading"></div>	  		
				
				
			</div>
            
             
                

            
                                
        </form>
    <?php
	}
	
?>