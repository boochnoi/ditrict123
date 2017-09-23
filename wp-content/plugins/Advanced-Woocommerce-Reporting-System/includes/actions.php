<?php
	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data', 'pw_rpt_fetch_data');
	function pw_rpt_fetch_data() {
		global $wpdb;
		
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);
		
		//echo $sql;
		
		//$products = $wpdb->get_results($sql);
		
		global $pw_rpt_main_class;
		
		//$table_name=$my_array_of_vars['table_name'];
		$table_name=$my_array_of_vars['table_names'];
        $pw_rpt_main_class->table_html($table_name,$my_array_of_vars);
		
		die();
	}
	
	//FETCH CUSTOM FIELD IN SETTINGS
	function get_operation($fields){
		$operators=array(
			"Numeric" 	=> array(
							"eq"=>__('EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"neq"=>__('NOT EQUALS',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"lt"=>__('LESS THEN',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"gt"=>__('MORE THEN',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"meq"=>__('EQUAL AND MORE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"leq"=>__('LESS AND EQUAL',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						),
			"String"	=>  array(
							"elike"=>__('EXACTLY LIKE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"like"=>__('LIKE',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						),			
		);
		$operators_options='';
		foreach($operators as $key=>$value){
			$operators_options.='<optgroup label="'.$key.' operators">';
			foreach($value as $k=>$v){
				
				$selected="";
				if($fields==$k){
					$selected="SELECTED";
				}
				$operators_options.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
			}
			$operators_options.='</optgroup>';
		}
		return $operators_options;
	}
	
	add_action('wp_ajax_pw_rpt_fetch_custom_fields', 'pw_rpt_fetch_custom_fields');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_custom_fields', 'pw_rpt_fetch_custom_fields');
	function pw_rpt_fetch_custom_fields(){
		//print_r($_POST);
		$html='';
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		if(isset($my_array_of_vars[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields']))
		{
			$custom_fiels = $my_array_of_vars[__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields'];
		
			foreach($custom_fiels as $fields){
				$meta_column=isset($my_array_of_vars[$fields.'_column']) ? $my_array_of_vars[$fields.'_column'] : "";
				
				$meta_translate=isset($my_array_of_vars[$fields.'_translate']) ? $my_array_of_vars[$fields.'_translate'] : "";
				$meta_operator=isset($my_array_of_vars[$fields.'_operator']) ? $my_array_of_vars[$fields.'_operator'] : "";
				
				$label=str_replace("@"," ",$fields);
				$html.='
				<div class="col-xs-12">
					<input type="hidden" name="'.$fields.'_column" placeholder="Label for '.$fields.'" value="off">
					<input type="checkbox" name="'.$fields.'_column" placeholder="Label for '.$fields.'" "'.checked("on",$meta_column,0).'"> Show in Grid
					<br />
					<input name="'.$fields.'_translate" placeholder="Label for '.$label.'" value="'.$meta_translate.'">
					<select name="'.$fields.'_operator">
						'.get_operation($meta_operator).'
					</select>
				</div>
				<br />';	
			}
		}else{
			$html=__('Please add custom field to left site',__PW_REPORT_WCREPORT_TEXTDOMAIN__);
		}
		echo $html;
		
		die();
	}
	
	
	//FETCH REPORT DATAGRID
	add_action('wp_ajax_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_data_dashborad', 'pw_rpt_fetch_data_dashborad');
	function pw_rpt_fetch_data_dashborad() {
		global $wpdb;
		
		parse_str($_REQUEST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		//print_r($my_array_of_vars);
		
		//echo $sql;
		
		//$products = $wpdb->get_results($sql);
		
		global $pw_rpt_main_class;
        
		echo '
		<div class="awr-box">
			<div class="awr-title">
				<h3>
					<i class="fa fa-filter"></i>
					
				</h3>
			</div><!--awr-title -->
			<div class="awr-box-content">
				<div class="col-xs-12">
					<div class="awr-box">
						<div class="awr-box-content">					
							<div id="target">'.
									$pw_rpt_main_class->table_html("dashboard_report",$my_array_of_vars).'
							</div>
						</div>
					</div>
				</div>    
			</div>
		</div>		
        
        <div class="col-md-12">'.
            $pw_rpt_main_class->table_html("monthly_summary",$my_array_of_vars).'
        </div>
		';
		
		die();
	}
	
	
	//FETCH CHART DATA
	add_action('wp_ajax_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	add_action('wp_ajax_nopriv_pw_rpt_fetch_chart', 'pw_rpt_fetch_chart');
	function pw_rpt_fetch_chart() {
		
		global $wpdb;
		global $pw_rpt_main_class;
		
		parse_str($_POST['postdata'], $my_array_of_vars);
		
		$nonce = $_POST['nonce'];
		
		$type = $_POST['type'];
		
		if(!wp_verify_nonce( $nonce, 'pw_livesearch_nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			print_r($arr);
			die();
		}
		
		$pw_from_date=$my_array_of_vars['pw_from_date'];
		$pw_to_date=$my_array_of_vars['pw_to_date'];
		$cur_year=substr($pw_from_date,0,4);
		
		$pw_hide_os=array('trash');
		$pw_shop_order_status=$pw_rpt_main_class->pw_shop_status;
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		
			
		/////////////////////////////
		//TOP PRODUCTS PIE CHART
		////////////////////////////
		$order_items_top_product=$pw_rpt_main_class->pw_get_dashboard_top_products_chart_pie($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		/////////////////////////////
		//SALE BY MONTHS
		////////////////////////////
		
		$order_items_months_multiple=$pw_rpt_main_class->pw_get_dashboard_sale_months_multiple_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_months=$pw_rpt_main_class->pw_get_dashboard_sale_months_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_days=$pw_rpt_main_class->pw_get_dashboard_sale_days_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$order_items_3d_months=$pw_rpt_main_class->pw_get_dashboard_sale_months_3d_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		//die($order_items_days);
		
		$order_items_week=$pw_rpt_main_class->pw_get_dashboard_sale_weeks_chart($pw_shop_order_status, $pw_hide_os, $pw_from_date, $pw_to_date);
		
		$final_json='';
		
		$currency_decimal=get_option('woocommerce_price_decimal_sep','.');
		$currency_thousand=get_option('woocommerce_price_thousand_sep',',');
		$currency_thousand=',';
		/////////////////////
		//SALE BY MONTH MULTIPLE CHART
		////////////////////
		
		$pw_fetchs_data='';
		$i=0;
		foreach ($order_items_months_multiple as $key => $order_item) {
			$value  =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0);
			
			$pw_fetchs_data[$i]["date"]=substr($order_item->Month,0,10);		
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			
			$i++;
			
		}
		//$final_json[]=($pw_fetchs_data);
		
		
		///////////////////////
		//MONTH FOR CHART
		////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_3d_months as $key => $order_item) {

			$value            =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0) ;
					
			$pw_fetchs_data[$i]["date"]=$order_item->Month.' '.$order_item->Year;	
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
				
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			
			$i++;			
		}
		$final_json[]=($pw_fetchs_data);	
		
		//////////////////
		//SALE BY DAYS
		//////////////////
		$item_dates = array();
		$item_data  = array();
		$pw_fetchs_data = '';
		$i=0;
		foreach ($order_items_days as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] = $item->TotalAmount;
			
			$value=  (is_numeric($item->TotalAmount) ?  number_format($item->TotalAmount,2):0);
			$pw_fetchs_data[$i]["date"] = trim($item->Date);
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			$i++;
		}
		$final_json[]=$pw_fetchs_data;
		
		////////////////////////////
		//SALE BY WEEK
		/////////////////////////////
		$item_dates = array();
		$item_data  = array();
		
		$weekarray = array();
		$timestamp = time();
		for ($i = 0; $i < 7; $i++) {
			$weekarray[] = date('Y-m-d', $timestamp);
			$timestamp -= 24 * 3600;
		}
		
		foreach ($order_items_week as $item) {
			$item_dates[]           = trim($item->Date);
			$item_data[$item->Date] = (is_numeric($item->TotalAmount) ?  number_format($item->TotalAmount,2):0);
		}
		
		$new_data = array();
		foreach ($weekarray as $date) {
			if (in_array($date, $item_dates)) {
				
				$new_data[$date] = $item_data[$date];
			} else {
				$new_data[$date] = 0;
			}
		}
		
		$pw_fetchs_data = array();
		$i         = 0;
		foreach ($new_data as $key => $value) {
			$pw_fetchs_data[$i]["date"] = $key;
			
			//$value=explode($currency_decimal,$value);
			//$value=$value[0];
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			
			$pw_fetchs_data[$i]["value"] = (is_numeric($value) ? number_format($value,2):0) ;
			$pw_fetchs_data[$i]["volume"] =  (is_numeric($value) ? number_format($value,2):0) ;
			$i++;			
		}
		$final_json[]=array_reverse($pw_fetchs_data);
		
		///////////////////////
		//MONTH FOR CHART
		////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_months as $key => $order_item) {

			$value            =  (is_numeric($order_item->TotalAmount) ?  number_format($order_item->TotalAmount,2):0) ;
					
			$pw_fetchs_data[$i]["date"]=$order_item->Month;		
			
			//$value=str_replace($currency_decimal,"",$value);
			$value=str_replace($currency_thousand,"",$value);
			//$value=$value[0];
			
			$pw_fetchs_data[$i]["value"] = $value;
			$pw_fetchs_data[$i]["volume"] = $value;
			
			$i++;			
		}
		$final_json[]=($pw_fetchs_data);		
		//die(print_r($pw_fetchs_data));
		
		///////////////////////////
		//	PIE CHART TOP PRODUCTS
		//////////////////////////
		$pw_fetchs_data=array();
		$i=0;
		foreach ($order_items_top_product as $items) {
			$pw_fetchs_data[$i]['label']=$items->Label;
			
			$value=(is_numeric($items->Value) ?  number_format($items->Value,2):0);
			$value=explode($currency_decimal,$value);
			$value=$value[0];
			
			$pw_fetchs_data[$i]['value']= $value ;
			
			$i++;
		}
		$final_json[]=($pw_fetchs_data);				
		
		//print_r($final_json);
			
		echo json_encode($final_json);	
		die();	
		
	}
?>