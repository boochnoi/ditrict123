<?php
	if($file_used=="sql_table")
	{
		
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','item_name',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);
		
		$pw_tax_group_by 			= $this->pw_get_woo_requests('pw_tax_groupby','tax_group_by_state',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code','-1',true);
		
		/*if($pw_country_code != NULL  && $pw_country_code != '-1')
		{
			$pw_country_code = "'".str_replace(",", "','",$pw_country_code)."'";
		}*/
		
		$state_code		= $this->pw_get_woo_requests('pw_states_code','-1',true);
		
		/*if($state_code != NULL  && $state_code != '-1')
		{
			$state_code = "'".str_replace(",", "','",$state_code)."'";
		}*/

		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		/////////////////////////
		//APPLY PERMISSION TERMS
		$key=$this->pw_get_woo_requests('table_names','',true);
		
		$permission_value=$this->get_form_element_value_permission('pw_countries_code',$key);
		$permission_enable=$this->get_form_element_permission('pw_countries_code',$key);
		
		if($permission_enable && $pw_country_code=='-1' && $permission_value!=1){
			$pw_country_code=implode(",",$permission_value);
		}
		if($pw_country_code != NULL  && $pw_country_code != '-1')
			$pw_country_code  		= "'".str_replace(",","','",$pw_country_code)."'";
		
		$permission_value=$this->get_form_element_value_permission('pw_states_code',$key);
		$permission_enable=$this->get_form_element_permission('pw_states_code',$key);
		
		if($permission_enable && $state_code=='-1' && $permission_value!=1){
			$state_code=implode(",",$permission_value);
		}
		if($state_code != NULL  && $state_code != '-1')
			$state_code  		= "'".str_replace(",","','",$state_code)."'";
		
		$permission_value=$this->get_form_element_value_permission('pw_orders_status',$key);
		$permission_enable=$this->get_form_element_permission('pw_orders_status',$key);
		
		if($permission_enable && $pw_order_status=='-1' && $permission_value!=1){
			$pw_order_status=implode(",",$permission_value);
		}
		if($pw_order_status != NULL  && $pw_order_status != '-1')
			$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		///////////////////////////
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		//Start Date
		$pw_from_date_condition ="";
		
		//Order Status
		$pw_id_order_status_join="";
		$pw_id_order_status_condition="";
		
		//Tax Group
		$pw_tax_group_by_join="";
		$tax_based_on_condition="";
		
		//State Code
		$state_code_condition="";
		
		//Coutry Code
		$pw_country_code_condition="";
		
		//Order Status
		$pw_order_status_condition="";
		
		//Hide Order
		$pw_hide_os_condition="";
		
		$sql_columns = "
		SUM(pw_woocommerce_order_itemmeta_tax_amount.meta_value)  AS _order_tax,
		SUM(pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_value)  AS _shipping_tax_amount,
		
		SUM(pw_postmeta1.meta_value)  AS _order_shipping_amount,
		SUM(pw_postmeta2.meta_value)  AS _order_total_amount,
		COUNT(pw_posts.ID)  AS _order_count,
		
		pw_woocommerce_order_items.order_item_name as tax_rate_code, 
		pw_woocommerce_tax_rates.tax_rate_name as tax_rate_name, 
		pw_woocommerce_tax_rates.tax_rate as order_tax_rate, 
		
		pw_woocommerce_order_itemmeta_tax_amount.meta_value AS order_tax,
		pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_value AS shipping_tax_amount,
		pw_postmeta1.meta_value 		as order_shipping_amount,
		pw_postmeta2.meta_value 		as order_total_amount,
		pw_postmeta3.meta_value 		as billing_state,
		pw_postmeta4.meta_value 		as billing_country
		";
		
		if($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary"){
			$sql_columns .= ", pw_postmeta5.meta_value 		as tax_city";
		}
		
		$group_sql='';
		
		switch($pw_tax_group_by){
			case "tax_group_by_city":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value,'-',pw_postmeta3.meta_value,'-',pw_postmeta5.meta_value,'-',lpad(pw_woocommerce_tax_rates.tax_rate,3,'0'),'-',pw_woocommerce_order_items.order_item_name,'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate) as group_column";
				break;
			case "tax_group_by_state":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value,'-',pw_postmeta3.meta_value,'-',lpad(pw_woocommerce_tax_rates.tax_rate,3,'0'),'-',pw_woocommerce_order_items.order_item_name,'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate) as group_column";
				break;
			case "tax_group_by_country":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value,'-',lpad(pw_woocommerce_tax_rates.tax_rate,3,'0'),'-',pw_woocommerce_order_items.order_item_name,'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate) as group_column";
				break;
			case "tax_group_by_tax_name":
				$group_sql = ", CONCAT(pw_woocommerce_tax_rates.tax_rate_name,'-',lpad(pw_woocommerce_tax_rates.tax_rate,3,'0'),'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate,'-',pw_postmeta4.meta_value,'-',pw_postmeta3.meta_value) as group_column";
				break;
			case "tax_group_by_tax_summary":
				$group_sql = ", CONCAT(pw_woocommerce_tax_rates.tax_rate_name,'-',lpad(pw_woocommerce_tax_rates.tax_rate,3,'0'),'-',pw_woocommerce_order_items.order_item_name) as group_column";
				break;
			case "tax_group_by_city_summary":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value,'',pw_postmeta3.meta_value,'',pw_postmeta5.meta_value) as group_column";
				break;
			case "tax_group_by_state_summary":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value,'',pw_postmeta3.meta_value) as group_column";
				break;
			case "tax_group_by_country_summary":
				$group_sql = ", CONCAT(pw_postmeta4.meta_value) as group_column";
				break;
			default:
				$group_sql = ", CONCAT(pw_woocommerce_order_items.order_item_name,'-',pw_woocommerce_tax_rates.tax_rate_name,'-',pw_woocommerce_tax_rates.tax_rate,'-',pw_postmeta4.meta_value,'-',pw_postmeta3.meta_value) as group_column";
				break;
		}
		
		$sql_columns .= $group_sql;				
		
		$sql_joins = "{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=	pw_woocommerce_order_items.order_id";
		
		if(($pw_id_order_status  && $pw_id_order_status != '-1') || $pw_sort_by == "status"){
			$pw_id_order_status_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
			
			if($pw_sort_by == "status"){
				$pw_id_order_status_join .= " LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
			}
		}
			
		$sql_joins.=$pw_id_order_status_join;	
			
		$sql_joins .= " 
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_woocommerce_order_items.order_id 
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_woocommerce_order_items.order_id 
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_tax ON pw_woocommerce_order_itemmeta_tax.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_tax_amount ON pw_woocommerce_order_itemmeta_tax_amount.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_shipping_tax_amount ON pw_woocommerce_order_itemmeta_shipping_tax_amount.order_item_id=pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_tax_rates as pw_woocommerce_tax_rates ON pw_woocommerce_tax_rates.tax_rate_id=pw_woocommerce_order_itemmeta_tax.meta_value
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_woocommerce_order_items.order_id";
		
		
		if($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary"){
			$pw_tax_group_by_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta5 ON pw_postmeta5.post_id=pw_woocommerce_order_items.order_id";
		}
		
		$sql_joins.=$pw_tax_group_by_join;
		
		$sql_condition = "pw_postmeta1.meta_key = '_order_shipping' AND pw_woocommerce_order_items.order_item_type = 'tax'
		AND pw_posts.post_type='shop_order' AND pw_postmeta2.meta_key='_order_total' AND pw_woocommerce_order_itemmeta_tax.meta_key='rate_id' AND pw_woocommerce_order_itemmeta_tax_amount.meta_key='tax_amount' AND pw_woocommerce_order_itemmeta_shipping_tax_amount.meta_key='shipping_tax_amount' AND pw_postmeta3.meta_key='_shipping_state' AND pw_postmeta4.meta_key='_shipping_country'";
		if($pw_tax_group_by == "tax_group_by_city" || $pw_tax_group_by == "tax_group_by_city_summary"){
			$tax_based_on_condition = " AND pw_postmeta5.meta_key='_shipping_city'";
		}
		
		$sql_condition .=$tax_based_on_condition;
		
		if($pw_id_order_status  && $pw_id_order_status != '-1') 
			$pw_id_order_status_condition = " AND term_taxonomy.term_id IN (".$pw_id_order_status .")";
		
		if($state_code and $state_code != '-1')	
			$state_code_condition = " AND pw_postmeta3.meta_value IN (".$state_code.")";
			
		if($pw_country_code and $pw_country_code != '-1')	
			$pw_country_code_condition= " AND pw_postmeta4.meta_value IN (".$pw_country_code.")";
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN (".$pw_hide_os.")";
		
		//20150207
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition = " AND (DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
		}
		
		$sql_group_by= "  GROUP BY group_column";
			
		$sql_order_by= "  ORDER BY group_column ASC";
		
		$sql = "SELECT $sql_columns FROM $sql_joins 
				WHERE $sql_condition $pw_id_order_status_condition $state_code_condition 
				$pw_country_code_condition $pw_order_status_condition $pw_hide_os_condition 
				$pw_from_date_condition 
				$sql_group_by $sql_order_by";
		
		//echo $sql;		
		
		
		$c=$pw_tax_group_by;
		if($c == 'tax_group_by_city'){
			$columns = array(
				
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'billing_state' ,'lable'=>__('Tax State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('id'=>'tax_city' ,'lable'=>__('Tax City',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_name' ,'lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_code' ,'lable'=>__('Tax Rate Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_shipping_amount' ,'lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_amount' ,'lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'order_total_amount' ,'lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
			);
		}elseif($c == 'tax_group_by_state'){
			$columns = array(
			
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'billing_state' ,'lable'=>__('Tax State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('id'=>'tax_rate_name' ,'lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_code' ,'lable'=>__('Tax Rate Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_shipping_amount' ,'lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_amount' ,'lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'order_total_amount' ,'lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
			);
		}elseif($c == 'tax_group_by_country'){
			$columns = array(
			
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_name' ,'lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_code' ,'lable'=>__('Tax Rate Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_shipping_amount' ,'lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_amount' ,'lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'order_total_amount' ,'lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
			);
		}elseif($c == 'tax_group_by_tax_name'){
			$columns = array(		
						
				array('id'=>'tax_rate_name' ,'lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'tax_rate_code' ,'lable'=>__('Tax Rate Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_shipping_amount' ,'lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_amount' ,'lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'order_total_amount' ,'lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')

			);
		}elseif($c == 'tax_group_by_tax_summary'){
			$columns = array(	
							
				array('id'=>'tax_rate_name' ,'lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_shipping_amount' ,'lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_amount' ,'lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'order_total_amount' ,'lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
	
			);
		}elseif($c == 'tax_group_by_city_summary'){
			$columns = array(
			
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'billing_state' ,'lable'=>__('Tax State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('id'=>'tax_city' ,'lable'=>__('Tax City',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
				
			);
		}elseif($c == 'tax_group_by_state_summary'){
			$columns = array(
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'billing_state' ,'lable'=>__('Tax State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')

			);
		}elseif($c == 'tax_group_by_country_summary'){
			$columns = array(
				
				array('id'=>'billing_country' ,'lable'=>__('Tax Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_order_count' ,'lable'=>__('Order Count',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')
			
			);
		}else{
			$columns = array(					
				
				array('id'=>'order_tax_rate' ,'lable'=>__('Tax Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_shipping_tax_amount' ,'lable'=>__('Shipping Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_order_tax' ,'lable'=>__('Order Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),	
				array('id'=>'_total_tax' ,'lable'=>__('Total Tax',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency')

			);
		}
		
		$this->table_cols = $columns;

		
	}elseif($file_used=="data_table"){
		
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
				$shipping_tax='';
				$order_tax='';
				$country_billing='';
				
				$j=0;			
				foreach($this->table_cols as $cols){
					
					$value=$items->$cols['id'];
					
					switch($cols['id']){
						
						case "billing_country":
							$country_billing=$value;
							$country      	= $this->pw_get_woo_countries();														
							$value = isset($country->countries[$value]) ? $country->countries[$value]: $items->country_name;
						break;
						
						case "billing_state":
							$value =$this->pw_get_woo_bsn($country_billing,$value);
						break;
						
						case "order_tax_rate":
							$value=round($value,2).'%';
						break;
						
						case "_order_shipping_amount":
							$value=$this->price($value);
						break;
						
						case "_order_amount":
							$val=$this->pw_get_number_percentage($items->_order_tax,$items->order_tax_rate);
						
							$value=$this->price($val);
						break;
						
						case "order_total_amount":
							$value=$this->price($value);
						break;
						
						case "_shipping_tax_amount":
							$shipping_tax=$items->$cols['id'];
							$value=$this->price($shipping_tax);
						break;
						
						case "_order_tax":
							$order_tax=$items->$cols['id'];
							$value=$this->price($order_tax);
						break;
						
						case "_total_tax":
							$value=$this->price($shipping_tax+$order_tax);
						break;
					}
					
					//Tax Country
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $value;
					$datatable_value.=("</td>");
					
				}
				
			$datatable_value.=("</tr>");
		}
	}elseif($file_used=="search_form"){
	?>
		<form class='alldetails search_form_report' action='' method='post'>
            <input type='hidden' name='action' value='submit-form' />
            <div class="row">
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('From Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('To Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"/>                        
                </div>
                <?php
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_countries_code');
					if($this->get_form_element_permission('pw_countries_code') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-globe"></i></span>
					<?php
                        $country_data = $this->pw_get_paying_woo_state('shipping_country');
                        
                        $option='';
                        //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($country_data as $country){
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($country->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
								$selected="selected";*/
                            
                            /*if($current_product==$country->id)
                                $selected="selected";*/
                            $option.="<option $selected value='".$country -> id."' >".$country -> label." </option>";
                        }
                    ?>
                
                    <select name="pw_countries_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_countries_code') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
							{
						?>
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
							}
						?>
                       <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
                <?php
					}
					$col_style='';		
					$permission_value=$this->get_form_element_value_permission('pw_states_code');
					if($this->get_form_element_permission('pw_states_code') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map"></i></span>
					<?php
                        $state_code = '-1';
                        //$state_data = $this->pw_get_paying_woo_state('billing_state','billing_country');
                        $state_data = $this->pw_get_paying_woo_state('shipping_state','shipping_country');	
                        //print_r($state_data);
                        $option='';
                        //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($state_data as $state){
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($state->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
								$selected="selected";*/
                            
                            /*if($current_product==$country->id)
                                $selected="selected";*/
                            $option.="<option $selected value='".$state -> id."' >".$state -> label." </option>";
                        }
                    ?>
                
                    <select name="pw_states_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_states_code') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
							{
						?>
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
							}
						?>
                        
                       <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
                <?php
					}
				?>
            
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Tax Group By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-suitcase"></i></span>
                    <select name="pw_tax_groupby" id="pw_tax_groupby" class="pw_tax_groupby">
                        <option value="tax_group_by_city">City</option>
                        <option value="tax_group_by_state" selected="selected">State</option>
                        <option value="tax_group_by_country">Country</option>
                        <option value="tax_group_by_tax_name">Tax Name</option>
                        <option value="tax_group_by_tax_summary">Tax Summary</option>
                        <option value="tax_group_by_city_summary">City Summary</option>
                        <option value="tax_group_by_state_summary">State Summary</option>
                        <option value="tax_group_by_country_summary">Country Summary</option>
                    </select>
                    
                </div>	
                
                <?php
	                $col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_orders_status');
					if($this->get_form_element_permission('pw_orders_status') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">	
                    <div class="awr-form-title">
                        <?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
					<?php
                        $pw_order_status=$this->pw_get_woo_orders_statuses();

                        $option='';
                        foreach($pw_order_status as $key => $value){
							$selected='';	
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($key,$permission_value))
								continue;
							
							/*if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
								$selected="selected";*/
								
                            $option.="<option value='".$key."' $selected>".$value."</option>";
                        }
                    ?>
                
                    <select name="pw_orders_status[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_orders_status') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
							{
						?>
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
							}
						?>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                </div>	
                <?php
					}
				?>
                
            </div>
            
            <div class="col-md-12">
                    <?php
                    	$pw_hide_os='trash';
						$pw_publish_order='no';
						
						$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
					?>
                    <input type="hidden" name="list_parent_category" value="">
                    <input type="hidden" name="group_by_parent_cat" value="0">
                    
                	<input type="hidden" name="pw_hide_os" id="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
                   
                    <input type="hidden" name="date_format" id="date_format" value="<?php echo $data_format;?>" />
                
                	<input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
                    <div class="fetch_form_loading search-form-loading"></div>	
                    <input type="submit" value="Search" class="button-primary"/>
					<input type="button" value="Reset" class="button-secondary form_reset_btn"/>
            </div>  
        </form>
    <?php
	}
	
?>