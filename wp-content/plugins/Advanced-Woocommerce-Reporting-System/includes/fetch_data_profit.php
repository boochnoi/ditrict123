<?php
	if($file_used=="sql_table")
	{
		
		$request 			= array();
		$start				= 0;
		
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);				
		$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid',NULL,true);				
		$txtProduct 		= $this->pw_get_woo_requests('txtProduct',NULL,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		
		$limit 				= $this->pw_get_woo_requests('limit',15,true);
		$p 					= $this->pw_get_woo_requests('p',1,true);
	
		$page 				= $this->pw_get_woo_requests('page',NULL,true);
		$order_id 			= $this->pw_get_woo_requests('pw_id_order',NULL,true);
		$pw_from_date 		= $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date 			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_txt_email 			= $this->pw_get_woo_requests('pw_email_text',NULL,true);
		
		$pw_txt_first_name		= $this->pw_get_woo_requests('pw_first_name_text',NULL,true);
		
		$pw_detail_view		= $this->pw_get_woo_requests('pw_view_details',"no",true);				
		$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code',NULL,true);
		$state_code			= $this->pw_get_woo_requests('pw_states_code','-1',true);
		$pw_payment_method		= $this->pw_get_woo_requests('payment_method',NULL,true);
		$pw_order_item_name	= $this->pw_get_woo_requests('order_item_name',NULL,true);//for coupon
		$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code',NULL,true);//for coupon
		$pw_publish_order		= $this->pw_get_woo_requests('publish_order','no',true);//if publish display publish order only, no or null display all order
		$pw_coupon_used		= $this->pw_get_woo_requests('pw_use_coupon','no',true);				
		$pw_order_meta_key		= $this->pw_get_woo_requests('order_meta_key','-1',true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code','-1',true);
		$pw_coupon_codes		= $this->pw_get_woo_requests('pw_codes_of_coupon','-1',true);
		
		$pw_max_amount			= $this->pw_get_woo_requests('max_amount','-1',true);
		$pw_min_amount			= $this->pw_get_woo_requests('min_amount','-1',true);
		
		$pw_billing_post_code		= $this->pw_get_woo_requests('pw_bill_post_code','-1',true);
		$pw_variation_id		= $this->pw_get_woo_requests('variation_id','-1',true);
		$pw_variation_only		= $this->pw_get_woo_requests('variation_only','-1',true);
		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','"trash"',true);
		
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
		
		///////////HIDDEN FIELDS////////////
		$pw_hide_os='trash';
		$pw_publish_order='no';
		$pw_order_item_name='';
		$pw_coupon_code='';
		$pw_coupon_codes='';
		$pw_payment_method='';
		
		$pw_variation_only=$this->pw_get_woo_requests('variation_only','-1',true);
		$pw_order_meta_key='';
		
		$data_format=$this->pw_get_woo_requests('date_format',get_option('date_format'),true);
		
		
		$pw_variation_id='-1';
		$amont_zero='';
		//////////////////////
		
		
		
		/////////////////CUSTOM FIELDS & TAXONOMY/////////////////
		
		/////////////////////////////////
		
		
		
		
		
		
		
		

		$pw_variations_formated='';
		
		if(strlen($pw_max_amount)<=0) $_REQUEST['max_amount']	= 	$pw_max_amount = '-1';
		if(strlen($pw_min_amount)<=0) $_REQUEST['min_amount']	=	$pw_min_amount = '-1';
		
		if($pw_max_amount != '-1' || $pw_min_amount != '-1'){
			if($pw_order_meta_key == '-1'){
				$_REQUEST['order_meta_key']	= "_order_total";
			}					
		}
		
		$last_days_orders 		= "0";
		if(is_array($pw_id_order_status)){		$pw_id_order_status 	= implode(",", $pw_id_order_status);}
		if(is_array($category_id)){ 		$category_id		= implode(",", $category_id);}
		
		if(!$pw_from_date){	$pw_from_date = date_i18n('Y-m-d');}
		if(!$pw_to_date){
			$last_days_orders 		= apply_filters($page.'_back_day', $last_days_orders);//-1,-2,-3,-4,-5
			$pw_to_date = date('Y-m-d', strtotime($last_days_orders.' day', strtotime(date_i18n("Y-m-d"))));}
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','order_id',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		///
		
		if($p > 1){	$start = ($p - 1) * $limit;}
		
		if($pw_detail_view == "yes"){
			$pw_variations_value		= $this->pw_get_woo_requests('variations_value',"-1",true);
			$pw_variations_formated = '-1';
			if($pw_variations_value != "-1" and strlen($pw_variations_value)>0){
				$pw_variations_value = explode(",",$pw_variations_value);				
				$var = array();
				foreach($pw_variations_value as $key => $value):
					$var[] .=  $value;
				endforeach;
				$result = array_unique ($var);
				//$this->print_array($var);
				$pw_variations_formated = implode("', '",$result);
			}
			$_REQUEST['variations_formated'] = $pw_variations_formated;
		}
		
		
		//pw_first_name_text
		$pw_txt_first_name_cols='';
		$pw_txt_first_name_join = '';
		$pw_txt_first_name_condition_1 = '';
		$pw_txt_first_name_condition_2 = '';
		
		//pw_email_text
		$pw_txt_email_cols ='';
		$pw_txt_email_join = '';
		$pw_txt_email_condition_1 = '';
		$pw_txt_email_condition_2 = '';
		
		//SORT BY
		$pw_sort_by_cols ='';
		
		//CATEGORY
		$category_id_join ='';
		$category_id_condition = '';
		
		//ORDER ID
		$pw_id_order_status_join ='';
		$pw_id_order_status_condition = '';
		
		//COUNTRY
		$pw_country_code_join = '';
		$pw_country_code_condition_1 = '';
		$pw_country_code_condition_2 = '';
		
		//STATE
		$state_code_join= '';
		$state_code_condition_1 = '';
		$state_code_condition_2 = '';
		
		//PAYMENT METHOD
		$pw_payment_method_join= '';
		$pw_payment_method_condition_1 = '';
		$pw_payment_method_condition_2 = '';
		
		//POSTCODE
		$pw_billing_post_code_join = '';
		$pw_billing_post_code_condition= '';
		
		//COUPON USED
		$pw_coupon_used_join = '';
		$pw_coupon_used_condition = '';
		
		//VARIATION ID
		$pw_variation_id_join = '';
		$pw_variation_id_condition = '';
		
		//VARIATION ONLY
		$pw_variation_only_join = '';
		$pw_variation_only_condition = '';
		
		//VARIATION FORMAT
		$pw_variations_formated_join = '';
		$pw_variations_formated_condition = '';
		
		//ORDER META KEY
		$pw_order_meta_key_join = '';
		$pw_order_meta_key_condition = '';
		
		//COUPON CODES
		$pw_coupon_codes_join = '';
		$pw_coupon_codes_condition = '';
		
		//COUPON CODE
		$pw_coupon_code_condition = '';
		
		//DATA CONDITION
		$date_condition = '';
		
		//ORDER ID
		$order_id_condition = '';
		
		//PAID CUSTOMER
		$pw_paid_customer_condition = '';
		
		//PUBLISH ORDER
		$pw_publish_order_condition_1 = '';
		$pw_publish_order_condition_2 = '';
		
		//ORDER ITEM NAME
		$pw_order_item_name_condition = '';
		
		//txt PRODUCT
		$txtProduct_condition = '';
		
		//PRODUCT ID
		$pw_product_id_condition = '';
		
		//CATEGORY ID
		$category_id_condition = '';
		
		//ORDER STATUS ID
		$pw_id_order_status_condition = '';
		
		//ORDER STATUS
		$pw_order_status_condition = '';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition = '';
		
		
		if($pw_sort_by == "status"){
			$pw_sort_by_cols = " terms2.name as status, ";
		}
		$sql_columns = " $pw_txt_first_name_cols $pw_txt_email_cols $pw_sort_by_cols";
		$sql_columns .= "
		DATE_FORMAT(pw_posts.post_date,'%m/%d/%Y') 													AS order_date,
		pw_woocommerce_order_items.order_id 															AS order_id,					
		pw_woocommerce_order_items.order_item_name 													AS product_name,					
		pw_woocommerce_order_items.order_item_id														AS order_item_id,
		woocommerce_order_itemmeta.meta_value 														AS woocommerce_order_itemmeta_meta_value,					
		(pw_woocommerce_order_itemmeta2.meta_value/pw_woocommerce_order_itemmeta3.meta_value) 			AS sold_rate,
		(pw_woocommerce_order_itemmeta4.meta_value/pw_woocommerce_order_itemmeta3.meta_value) 			AS product_rate,
		(pw_woocommerce_order_itemmeta4.meta_value) 													AS item_amount,
		(pw_woocommerce_order_itemmeta2.meta_value) 													AS item_net_amount,
		(pw_woocommerce_order_itemmeta4.meta_value - pw_woocommerce_order_itemmeta2.meta_value) 			AS item_discount,					
		pw_woocommerce_order_itemmeta2.meta_value 														AS total_price,
		count(pw_woocommerce_order_items.order_item_id) 												AS product_quentity,
		woocommerce_order_itemmeta.meta_value 														AS product_id
		
		,woocommerce_order_itemmeta22.meta_value AS variation_id 
		
		,pw_woocommerce_order_itemmeta3.meta_value 													AS 'product_quantity'					
		,pw_posts.post_status 																			AS post_status
		,pw_posts.post_status 																			AS order_status
		
		";
			
		$sql_joins ="{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id				
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta 	ON woocommerce_order_itemmeta.order_item_id		=	pw_woocommerce_order_items.order_item_id
		
		LEFT JOIN wp_woocommerce_order_itemmeta as woocommerce_order_itemmeta22 ON woocommerce_order_itemmeta22.order_item_id	=	pw_woocommerce_order_items.order_item_id
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta2 	ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta3 	ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta4 	ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal'";	
			
		
		
		
		if($category_id  && $category_id != "-1") {
			$category_id_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 			ON pw_term_relationships.object_id		=	woocommerce_order_itemmeta.meta_value
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 				ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
				//LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if(($pw_id_order_status  && $pw_id_order_status != '-1') || $pw_sort_by == "status"){
			$pw_id_order_status_join= "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships2			ON pw_term_relationships2.object_id	= pw_woocommerce_order_items.order_id
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy2				ON pw_term_taxonomy2.term_taxonomy_id	= pw_term_relationships2.term_taxonomy_id";
				if($pw_sort_by == "status"){
					$pw_id_order_status_join .= " LEFT JOIN  {$wpdb->prefix}terms 	as terms2 						ON terms2.term_id					=	pw_term_taxonomy2.term_id";
				}
		}
				
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_woocommerce_order_items.order_id";
		
		if($state_code && $state_code != '-1')
			$state_code_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_state ON pw_postmeta_billing_state.post_id=pw_posts.ID";
		
		if($pw_payment_method)
			$pw_payment_method_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta5 ON pw_postmeta5.post_id=pw_woocommerce_order_items.order_id";
		
	
		$post_type_condition="pw_posts.post_type = 'shop_order'";
		
		$other_condition_1 = "
		AND woocommerce_order_itemmeta.meta_key = '_product_id'
		
		AND woocommerce_order_itemmeta22.meta_key = '_variation_id' 
		
		AND pw_woocommerce_order_itemmeta2.meta_key='_line_total'
		AND pw_woocommerce_order_itemmeta3.meta_key='_qty' ";
		
		
		
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_condition_1 = " AND pw_postmeta4.meta_key='_billing_country'";
		
		if($state_code && $state_code != '-1')
			$state_code_condition_1 = " AND pw_postmeta_billing_state.meta_key='_billing_state'";
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$date_condition = " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		}
		
		if($order_id)
			$order_id_condition = " AND pw_woocommerce_order_items.order_id = ".$order_id;
		
		if($pw_publish_order == 'yes')
			$pw_publish_order_condition_1 = " AND pw_posts.post_status = 'publish'";
			
		if($pw_publish_order == 'publish' || $pw_publish_order == 'trash')
			$pw_publish_order_condition_2 = " AND pw_posts.post_status = '".$pw_publish_order."'";
	
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_condition_2 = " AND pw_postmeta4.meta_value IN ('".$pw_country_code."')";
			
		if($state_code && $state_code != '-1')
			$state_code_condition_2 = " AND pw_postmeta_billing_state.meta_value IN ('".$state_code."')";
	
		if($pw_product_id  && $pw_product_id != "-1")
			$pw_product_id_condition = " AND woocommerce_order_itemmeta.meta_value IN (".$pw_product_id .")";	
		
		if($category_id  && $category_id != "-1")
			$category_id_condition = " AND term_taxonomy.taxonomy LIKE('product_cat') AND term_taxonomy.term_id IN (".$category_id .")";
			
		
		if($pw_id_order_status  && $pw_id_order_status != "-1")
			$pw_id_order_status_condition = " AND pw_term_taxonomy2.taxonomy LIKE('shop_order_status') AND pw_term_taxonomy2.term_id IN (".$pw_id_order_status .")";
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";
		
		
		
		$sql ="SELECT $sql_columns FROM $sql_joins";
		
		$sql .="$category_id_join $pw_id_order_status_join $pw_txt_email_join $pw_txt_first_name_join
				$pw_country_code_join $state_code_join $pw_payment_method_join $pw_billing_post_code_join
				$pw_coupon_used_join $pw_variation_id_join $pw_variation_only_join $pw_variations_formated_join
				$pw_order_meta_key_join $pw_coupon_codes_join";
		
		$sql .= " Where $post_type_condition $pw_txt_email_condition_1 $pw_txt_first_name_condition_1
						$other_condition_1 $pw_country_code_condition_1 $state_code_condition_1
						$pw_billing_post_code_condition $pw_payment_method_condition_1 $date_condition
						$order_id_condition $pw_txt_email_condition_2 $pw_paid_customer_condition
						$pw_txt_first_name_condition_2 $pw_publish_order_condition_1 $pw_publish_order_condition_2
						$pw_country_code_condition_2 $state_code_condition_2 $pw_payment_method_condition_2
						$pw_order_meta_key_condition $pw_order_item_name_condition $txtProduct_condition
						$pw_product_id_condition $category_id_condition $pw_id_order_status_condition
						$pw_coupon_used_condition $pw_coupon_code_condition $pw_coupon_codes_condition
						$pw_variation_id_condition $pw_variation_only_condition $pw_variations_formated_condition
						$pw_order_status_condition $pw_hide_os_condition ";
		
		$sql_group_by = " GROUP BY pw_woocommerce_order_items.order_item_id ";
		
		$sql .=$sql_group_by;
		
		//print_r($search_fields);
		//echo $sql;
		
		
		/*global $wpdb;
		$query_tax_query=array('relation' => 'AND');
		$query_tax_query[]=array(
			'taxonomy' => 'product_cat',
			'field'    => 'id',
			'terms'    => array('9'),
			'operator' => 'IN',
		);
		$query_tax_query[]=array(
			'taxonomy' => 'product_brand',
			'field'    => 'id',
			'terms'    => array('19'),
			'operator' => 'IN',
		);
		
		
		$args = array(
			'post_type'   => array('product'),
			'post_status' =>'publish',
			'posts_per_page'	=> -1,
			'tax_query'=>$query_tax_query
		);
		
		$quer=new WP_Query($args);
		echo $quer->request;*/
		
		
		
		$columns=array(
			array('lable'=>__('Order ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
			array('lable'=>__('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
			array('lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
			array('lable'=>__('Cog',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
			array('lable'=>__('Profit',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
			//array('lable'=>__('Avg Profit/Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
		);
	
		$this->table_cols = $columns;
		
	}
	elseif($file_used=="data_table"){

		$first_order_id='';
		
		$order_items=$this->results;
		$categories = array();
		$order_meta = array();
		if(count($order_items)>0)
		
		foreach ( $order_items as $key => $order_item ) {
			
				$order_id								= $order_item->order_id;
				$order_items[$key]->billing_first_name  = '';//Default, some time it missing
				$order_items[$key]->billing_last_name  	= '';//Default, some time it missing
				$order_items[$key]->billing_email  		= '';//Default, some time it missing
				
				if(!isset($order_meta[$order_id])){
					$order_meta[$order_id]					= $this->pw_get_full_post_meta($order_id);
				}
				
				foreach($order_meta[$order_id] as $k => $v){
					$order_items[$key]->$k			= $v;
				}
				
				
				$order_items[$key]->order_total			= isset($order_item->order_total)		? $order_item->order_total 		: 0;
				$order_items[$key]->order_shipping		= isset($order_item->order_shipping)	? $order_item->order_shipping 	: 0;
				
				
				$order_items[$key]->cart_discount		= isset($order_item->cart_discount)		? $order_item->cart_discount 	: 0;
				$order_items[$key]->order_discount		= isset($order_item->order_discount)	? $order_item->order_discount 	: 0;
				$order_items[$key]->total_discount 		= isset($order_item->total_discount)	? $order_item->total_discount 	: ($order_items[$key]->cart_discount + $order_items[$key]->order_discount);
				
				
				$order_items[$key]->order_tax 			= isset($order_item->order_tax)			? $order_item->order_tax : 0;
				$order_items[$key]->order_shipping_tax 	= isset($order_item->order_shipping_tax)? $order_item->order_shipping_tax : 0;
				$order_items[$key]->total_tax 			= isset($order_item->total_tax)			? $order_item->total_tax 	: ($order_items[$key]->order_tax + $order_items[$key]->order_shipping_tax);
				
				$transaction_id = "ransaction ID";
				$order_items[$key]->transaction_id		= isset($order_item->$transaction_id) 	? $order_item->$transaction_id		: (isset($order_item->transaction_id) ? $order_item->transaction_id : '');
				$order_items[$key]->gross_amount 		= ($order_items[$key]->order_total + $order_items[$key]->total_discount) - ($order_items[$key]->order_shipping +  $order_items[$key]->order_shipping_tax + $order_items[$key]->order_tax );
				
				
				$order_items[$key]->billing_first_name	= isset($order_item->billing_first_name)? $order_item->billing_first_name 	: '';
				$order_items[$key]->billing_last_name	= isset($order_item->billing_last_name)	? $order_item->billing_last_name 	: '';
				$order_items[$key]->billing_name		= $order_items[$key]->billing_first_name.' '.$order_items[$key]->billing_last_name;
				
			
		}
		
		
		$this->results=$order_items;
		
		
		//print_r($this->results);
		
		$items_render=array();
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			
			$order_id= $items->order_id;
			$fetch_other_data='';				
							
			if(!isset($this->order_meta[$order_id])){
				$fetch_other_data= $this->pw_get_full_post_meta($order_id);
			}
			
			$new_order=false;
			if($first_order_id=='')
			{
				$first_order_id=$items->order_id;
				$new_order=true;
			}else if($first_order_id!=$items->order_id)
			{
				$first_order_id=$items->order_id;
				$new_order=true;	
			}
			
			
			if(in_array($items->order_id,$items_render))		
				continue;
			else
				$items_render[]=$items->order_id;	
			
			$datatable_value.=("<tr>");
				
				//order ID
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->order_id;
				$datatable_value.=("</td>");
				
				//Status
				$pw_table_value = isset($items->order_status) ? $items->order_status : '';
				
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
				
				//Order Total
				$display_class='';
				$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
				$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
				if($this->table_cols[8]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']),'multi_currency');
				$datatable_value.=("</td>");
				
				$display_class='';
				$all_cog='';
				
				$order = new WC_Order( $order_id );
				$items_order = $order->get_items();
				
				foreach ( $items_order as $item ) {
					$product_qty = $item['qty'];
					$product_id = $item['product_id'];
					$product_variation_id = $item['variation_id'];
					
					$product_id_cog=$product_id;
					if($product_variation_id!=''){
						$product_id_cog=$product_variation_id;
					}
					$cog=get_post_meta($product_id_cog,__PW_COG__,true);
					$cog*=$product_qty;
					$all_cog+=$cog;
				}
				
				
				
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $all_cog == 0 ? 0 : $this->price($all_cog);
				$datatable_value.=("</td>");
				
				$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $all_cog == 0 ? 0 : $this->price($pw_table_value-$all_cog);
				$datatable_value.=("</td>");
				
				
			$datatable_value.=("</tr>");	
		
			
			
		}
	}elseif($file_used=="search_form"){
	?>
		<form class='alldetails search_form_report' action='' method='post'>
            <input type='hidden' name='action' value='submit-form' />
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Date From',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick"/>
                </div>
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Date To',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"/>
                </div>
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Order ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
                    <input name="pw_id_order" type="text"  class=""/>
                </div>
                
               
                <?php
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_category_id');
                	if($this->get_form_element_permission('pw_category_id') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
							$col_style='display:none';
				?>
                <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-tags"></i></span>
                    <?php
                        $args = array(
                            'orderby'                  => 'name',
                            'order'                    => 'ASC',
                            'hide_empty'               => 1,
                            'hierarchical'             => 0,
                            'exclude'                  => '',
                            'include'                  => '',
                            'child_of'          		 => 0,
                            'number'                   => '',
                            'pad_counts'               => false 
                        
                        ); 
        
                        //$categories = get_categories($args); 
                        $current_category=$this->pw_get_woo_requests_links('pw_category_id','',true);
                        
                        $categories = get_terms('product_cat',$args);
                        $option='';
                        foreach ($categories as $category) {
							
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
								continue;
								
							if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
								$selected="selected";
							
                            if($current_category==$category->term_id)
                                $selected="selected";
                            
                            $option .= '<option value="'.$category->term_id.'" '.$selected.'>';
                            $option .= $category->name;
                            $option .= ' ('.$category->count.')';
                            $option .= '</option>';
                        }
                    ?>
                    <select name="pw_category_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_category_id') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
					$permission_value=$this->get_form_element_value_permission('pw_product_id');
				?>	
                 
                 	
                 
                 <?php
				 	$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_orders_status');
					if($this->get_form_element_permission('pw_orders_status') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
							$col_style='display:none';
				?>
                 
                 <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <?php
                        $pw_order_status=$this->pw_get_woo_orders_statuses();

                        $option='';
                        foreach($pw_order_status as $key => $value){
							
							$selected="selected";
							if(is_array($permission_value) && !in_array($key,$permission_value))
								continue;
								
							if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
								$selected="selected";	
							
                            $option.="<option value='".$key."' >".$value."</option>";
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
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_countries_code');
                	if($this->get_form_element_permission('pw_countries_code') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
							$col_style='display:none'
						
				?> 
                 <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-globe"></i></span>
					<?php
                        $country_data = $this->pw_get_paying_woo_state('billing_country');
                        $country      	= $this->pw_get_woo_countries();
                        $option='';
                        foreach($country_data as $countries){
                            $selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($countries->id,$permission_value))
								continue;
							
							if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
								$selected="selected";	
							                                                    
                            $pw_table_value = $country->countries[$countries->id];
                            $option.="<option value='".$countries->id."' $selected >".$pw_table_value."</option>";
                        }
                        
                        $country_states = $this->pw_get_woo_country_of_state();
                        $json_country_states = json_encode($country_states);
                        //print_r($json_country_states);
                    ?>
                    <select id="pw_adr_country" name="pw_countries_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
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
                    
                    <script type="text/javascript">
                        "use strict";
                        jQuery( document ).ready(function( $ ) {
                            
                            var country_state='';
                            country_state=<?php echo $json_country_states?>;	
                            
                            $("#pw_adr_country").change(function(){
                                var country_val=$(this).val();

								if(country_val==null){
									return false;
								}
								
								var option_data = Array();
								var optionss = '<option value="-1">Select All</option>';
								var i = 1;
								$.each(country_state, function(key,val){

									if(country_val.indexOf(val.parent_id) >= 0 || country_val=="-1"){
										optionss += '<option value="' + val.id + '">' + val.label + '</option>';
										option_data[val.id] = val.label;
									}
									i++;
								});

								$('#pw_adr_state').empty(); //remove all child nodes
								$("#pw_adr_state").html(optionss);
								$('#pw_adr_state').trigger("chosen:updated");
                            });
                            
                            
                            
                        });
                        
                    </script>
                    
                </div>	
                
                <?php
					}
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_states_code');
                	if($this->get_form_element_permission('pw_states_code') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
							$col_style='display:none';
				?> 

                 <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map"></i></span>
					<?php
                        //$state_codes = $this->pw_get_paying_woo_state('shipping_state','shipping_country');
                        //$this->pw_get_woo_country_of_state();
                        //$this->pw_get_woo_bsn($items->billing_country,$items->billing_state_code);
                        $state_codes = $this->pw_get_paying_woo_state('billing_state','billing_country');
                        $option='';
                        foreach($state_codes as $state){
                            $selected="";	
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($state->id,$permission_value))
								continue;
							
							if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
								$selected="selected";	
							                                                    
                            $pw_table_value = $this->pw_get_woo_bsn($state->billing_country,$state->id);
                            $option.="<option $selected value='".$state->id."' >".$pw_table_value." ($state->billing_country)</option>";
                        }
                    ?>
                
                    <select id="pw_adr_state" name="pw_states_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
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
                
                 
           	 	<div class="col-md-12">
				<?php
                    $pw_hide_os='trash';
                    $pw_publish_order='no';
                    $pw_order_item_name='';
                    $pw_coupon_code='';
                    $pw_coupon_codes='';
                    $pw_payment_method='';
                    
                    $pw_variation_only=$this->pw_get_woo_requests_links('variation_only','-1',true);
                    $pw_order_meta_key='';
                    
                    $data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
                    
                   
                    $pw_variation_id='-1';
                    $amont_zero='';
                    
                ?>
            
                <input type="hidden" name="pw_hide_os" value="<?php echo $pw_hide_os;?>" />
                <input type="hidden" name="publish_order" value="<?php echo $pw_publish_order;?>" />
                <input type="hidden" name="order_item_name" value="<?php echo $pw_order_item_name;?>" />
                <input type="hidden" name="coupon_code" value="<?php echo $pw_coupon_code;?>" />
                <input type="hidden" name="pw_codes_of_coupon" value="<?php echo $pw_coupon_codes;?>" />
                <input type="hidden" name="payment_method" value="<?php echo $pw_payment_method;?>" />
                <input type="hidden" name="variation_id" value="<?php echo $pw_variation_id; ?>" />
                <input type="hidden" name="variation_only" value="<?php echo $pw_variation_only; ?>" />
                <input type="hidden" name="date_format" value="<?php echo $data_format; ?>" />
                
                <input type="hidden" name="table_names" value="<?php echo $table_name;?>"/>
                <div class="fetch_form_loading search-form-loading"></div>	
                <input type="submit" value="Search" class="button-primary"/>
                <input type="button" value="Reset" class="button-secondary form_reset_btn"/>
            </div>  
                                
        </form>
    <?php
	}
	
?>