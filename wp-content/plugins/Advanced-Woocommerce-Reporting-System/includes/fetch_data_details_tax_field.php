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
		
		$brand_id 		= $this->pw_get_woo_requests('pw_brand_id','-1',true);
		$model_id 		= $this->pw_get_woo_requests('pw_model_id','-1',true);
		
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
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_paid_customer		= str_replace(",","','",$pw_paid_customer);
		$pw_country_code		= str_replace(",","','",$pw_country_code);
		//$state_code		= str_replace(",","','",$state_code);
		//$pw_country_code		= str_replace(",","','",$pw_country_code);
		
		$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code','-1',true);
		$pw_coupon_codes		= $this->pw_get_woo_requests('pw_codes_of_coupon','-1',true);
		
		$pw_max_amount			= $this->pw_get_woo_requests('max_amount','-1',true);
		$pw_min_amount			= $this->pw_get_woo_requests('min_amount','-1',true);
		
		$pw_billing_post_code		= $this->pw_get_woo_requests('pw_bill_post_code','-1',true);
		$pw_variation_id		= $this->pw_get_woo_requests('variation_id','-1',true);
		$pw_variation_only		= $this->pw_get_woo_requests('variation_only','-1',true);
		$pw_hide_os		= $this->pw_get_woo_requests('pw_hide_os','"trash"',true);
		
		
		
		/////////////////////////
		//APPLY PERMISSION TERMS
		$key=$this->pw_get_woo_requests('table_names','',true);
		
		$permission_value=$this->get_form_element_value_permission('pw_category_id',$key);
		$permission_enable=$this->get_form_element_permission('pw_category_id',$key);
		
		if($permission_enable && $category_id=='-1' && $permission_value!=1){
			$category_id=implode(",",$permission_value);
		}
		
		$permission_value=$this->get_form_element_value_permission('pw_product_id',$key);
		$permission_enable=$this->get_form_element_permission('pw_product_id',$key);
		
		if($permission_enable && $pw_product_id=='-1' && $permission_value!=1){
			$pw_product_id=implode(",",$permission_value);
		}
		
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
		
		
		
		$key=$this->pw_get_woo_requests('table_names','',true);
		$visible_custom_taxonomy='';
		$post_name='product';
		
		$all_tax_joins=$all_tax_conditions='';
		$custom_tax_cols='';
		$all_tax=$this->fetch_product_taxonomies( $post_name );
		$current_value=array();
		if(is_array($all_tax) && count($all_tax)>0){
			//FETCH TAXONOMY
			$i=1;
			foreach ( $all_tax as $tax ) {

				$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
				if($tax_status=='on'){
					
					
					$taxonomy=get_taxonomy($tax);	
					$values=$tax;
					$label=$taxonomy->label;
					
					$show_column=get_option($key.'_'.$tax."_column");
					$translate=get_option($key.'_'.$tax."_translate");
					if($translate!='')
					{
						$label=$translate;
					}
		
					if($show_column=="on")
						$custom_tax_cols[]=array('lable'=>__($label,__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show');	
					
					
					$visible_custom_taxonomy[]=$tax;
					
					${$tax} 		= $this->pw_get_woo_requests('pw_custom_taxonomy_in_'.$tax,'-1',true);
					
					
					/////////////////////////
					//APPLY PERMISSION TERMS
					$permission_value=$this->get_form_element_value_permission($tax,$key);
					$permission_enable=$this->get_form_element_permission($tax,$key);
					
					if($permission_enable && ${$tax}=='-1' && $permission_value!=1){
						${$tax}=implode(",",$permission_value);
					}
					/////////////////////////
					
					
					//echo(${$tax});
					
					if(is_array(${$tax})){ 		${$tax}		= implode(",", ${$tax});}
					
					$lbl_join=$tax."_join";
					$lbl_con=$tax."_condition";
					
					${$lbl_join} ='';
					${$lbl_con} = '';
					
					if(${$tax}  && ${$tax} != "-1") {
						${$lbl_join} = "
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships$i 			ON pw_term_relationships$i.object_id		=	woocommerce_order_itemmeta.meta_value
							LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy$i				ON term_taxonomy$i.term_taxonomy_id	=	pw_term_relationships$i.term_taxonomy_id";
							//LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
					}
					
					$all_tax_joins.=" ".${$lbl_join}." ";
					
					if(${$tax}  && ${$tax} != "-1")
						${$lbl_con} = " AND term_taxonomy$i.taxonomy LIKE('$tax') AND term_taxonomy$i.term_id IN (".${$tax} .")";
					
					$all_tax_conditions.=" ".${$lbl_con}." ";	
					
					$i++;
				}
			}
		}
		
		
		////////////CUSTOM FIELDS////////////
		global $wpdb;
		
		$product_custom_fields='';
		$order_custom_fields='';
		$gravity_form_custom_fields='';
		$extra_addon_form_custom_fields='';
		
		$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='product'  GROUP BY pw_postmeta.meta_key", ARRAY_A);
		
							
		if ($types!=null && is_array($types)) {
			
			foreach($types as $k=>$v) {
				$product_custom_fields[]= $v['meta_key'];
			}
			
		}
		  
		$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='shop_order' GROUP BY pw_postmeta.meta_key", ARRAY_A);
		
		
		if ($types!=null && is_array($types)) {
			foreach($types as $k=>$v) {
				$order_custom_fields[]= substr($v['meta_key'],1);
			}
		} 
		
		
		/////////////GRAVITY FORM CUSTOM FIELD//////////////
		if(defined("GRAVITY_MANAGER_URL"))
		{
			$types = $wpdb->get_results("SELECT display_meta FROM {$wpdb->prefix}rg_form_meta", ARRAY_A);
			if ($types!=null && is_array($types)) {
				foreach($types as $type){
					//print_r($type['display_meta']);
					$form = json_decode( $type['display_meta'] );
					//print_r($form->fields[1]->label);
					foreach($form->fields as $fields){
						$value=($fields->label);
						$value=str_replace(" ","_",$value);
						$gravity_form_custom_fields[]=$value;
					}
				}
			}
		}
		
		
		if(class_exists("WC_Product_Addons"))
		{
			$types = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta where meta_key='_product_addons'", ARRAY_A);
			if ($types!=null && is_array($types)) {
				foreach($types as $type){
					//print_r(unserialize ($type['meta_value']));
					$form = unserialize ( $type['meta_value'] );
					foreach($form as $fields){
						$value=($fields['options']);
						$parent=str_replace(" ","___",$fields['name']);
						if($fields['type']=='checkbox')
							continue;
						//print_r($value);
						foreach($value as $ffield){
							$valuew=str_replace(" ","___",$ffield['label']);
							$final_value='';
							$final_label='';
							if($ffield['label']!='')
							{
								$final_value=$parent.'__'.$valuew;
								$final_label=$parent.'__'.$ffield['label'];
							}else{
								$final_value=$parent;
								$final_label=$parent;
							}
							$extra_addon_form_custom_fields[]=$final_value;
						}
					}
				}
			}
		}
		
		$custom_fields_cols='';
		$order_custom_fields_cols='';
		//print_r($order_custom_fields);
		function get_operator($op,$field){
			switch ($op) {
				case 'eq':
					$operator = "= '$field'";
					break;
				case 'neq':
					$operator = "<> $field";
					break;
				case 'lt':
					$operator = "< $field";
					break;
				case 'gt':
					$operator = "> $field";
					break;
				case 'meq':
					$operator = ">= $field";
					break;
				case 'leq':
					$operator = "<= $field";
					break;		
				case 'elike':
					$operator = "= '$field'";
					$ll_like = "'";
					$rr_like = "'";
					break;
				case 'like':
					//$operator = "LIKE '%$field' OR '$field%'";
					$operator = "LIKE '%$field%'";
					$ll_like = "'%";
					$rr_like = "%'";
					break;
				default:
					$operator = "= '$field'";
					break;
			}
			return $operator;
		}
		
		$all_fields_joins=$all_fields_conditions=$all_fields_cols='';
		$custom_fields=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields');
		$i=9;
		
		if(is_array($custom_fields)){
			foreach($custom_fields as $fields){
				${$fields} 	= $this->pw_get_woo_requests('pw_'.$fields,NULL,true);
				
				$show_column=get_option($fields.'_column');
				$label=get_option($fields.'_translate');
				if($label==''){
					$label=$fields;
				}
				if($show_column=="on"){
					if(in_array($fields,$order_custom_fields))
						$order_custom_fields_cols[]=array('lable'=>__($label,__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show');
					else	
						$custom_fields_cols[]=array('lable'=>__($label,__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show');
				}
				
				if(is_array($gravity_form_custom_fields) && in_array($fields,$gravity_form_custom_fields))
					continue;
					
				//$cond_field=str_replace("___"," ",$fields);	
				if(is_array($extra_addon_form_custom_fields) && in_array($fields,$extra_addon_form_custom_fields))
					continue;	
					
				
				$operator=get_option($fields.'_operator');
				$operator_condition=get_operator($operator,${$fields});
				
				$lbl_join=$fields."_join";
				$lbl_con=$fields."_condition";
				$lbl_col=$fields."_col";
				
				${$lbl_join} ='';
				${$lbl_con} = '';
				${$lbl_col} = '';
				
				
				
				if(${$fields}){
					
				}
				
			
				if(in_array($fields,$product_custom_fields))
				{
					${$lbl_col} = " postmeta$i.meta_value as $fields , ";
					
					${$lbl_join} = " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta$i ON postmeta$i.post_id=woocommerce_order_itemmeta.meta_value";
					
					${$lbl_con} = "AND postmeta$i.meta_key='$fields'";	
				}
				
				if(${$fields} && in_array($fields,$product_custom_fields))
				{
				}
					
				if(in_array($fields,$order_custom_fields))
					
				if(${$fields} && in_array($fields,$order_custom_fields))
				{
					${$lbl_col} = " postmeta$i.meta_value as $fields , ";
					
					${$lbl_join} = " LEFT JOIN  {$wpdb->prefix}postmeta as postmeta$i ON postmeta$i.post_id=pw_woocommerce_order_items.order_id";	
				}
					
				if(${$fields}){
					
				}
				
				$all_fields_cols.=" ".${$lbl_col}." ";
				$all_fields_joins.=" ".${$lbl_join}." ";
				
				if(${$fields}){
					
					${$lbl_con} .= " AND postmeta$i.meta_value $operator_condition ";
				}
				
				$all_fields_conditions.=" ".${$lbl_con}." ";	
				
				$i++;
				
			}
			
			
			
		}
		
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
		
		if(is_array($brand_id)){ 		$brand_id		= implode(",", $brand_id);}
		if(is_array($model_id)){ 		$model_id		= implode(",", $model_id);}
		
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
		
		//BRAND
		$brand_id_join ='';
		$brand_id_condition = '';
		
		//MODEL
		$model_id_join ='';
		$model_id_condition = '';
		
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
		
		//BRAND ID
		$brand_id_condition = '';
		$model_id_condition = '';
		
		//ORDER STATUS ID
		$pw_id_order_status_condition = '';
		
		//ORDER STATUS
		$pw_order_status_condition = '';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition = '';
		
		
		
				
		if(($pw_txt_first_name and $pw_txt_first_name != '-1') || $pw_sort_by == "billing_name"){
			$pw_txt_first_name_cols = " CONCAT(pw_postmeta1.meta_value, ' ', pw_postmeta2.meta_value) AS billing_name," ;
		}
		if($pw_txt_email || ($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'") || $pw_sort_by == "billing_email"){
			$pw_txt_email_cols = " postmeta.meta_value AS billing_email,";
		}
		
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
		,pw_woocommerce_order_itemmeta3.meta_value 													AS 'product_quantity'					
		,pw_posts.post_status 																			AS post_status
		,pw_posts.post_status 																			AS order_status
		
		";
			
		$sql_joins ="{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=pw_woocommerce_order_items.order_id				
		
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta 	ON woocommerce_order_itemmeta.order_item_id		=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta2 	ON pw_woocommerce_order_itemmeta2.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta3 	ON pw_woocommerce_order_itemmeta3.order_item_id	=	pw_woocommerce_order_items.order_item_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta4 	ON pw_woocommerce_order_itemmeta4.order_item_id	=	pw_woocommerce_order_items.order_item_id AND pw_woocommerce_order_itemmeta4.meta_key='_line_subtotal'";	
			
		
		
		
		if($category_id  && $category_id != "-1") {
			$category_id_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 			ON pw_term_relationships.object_id		=	woocommerce_order_itemmeta.meta_value
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 				ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
				//LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if($brand_id  && $brand_id != "-1") {
			$brand_id_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships1 			ON pw_term_relationships1.object_id		=	woocommerce_order_itemmeta.meta_value
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy1				ON term_taxonomy1.term_taxonomy_id	=	pw_term_relationships1.term_taxonomy_id";
				//LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 						ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		if($model_id  && $model_id != "-1") {
			$model_id_join = "
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships2 			ON pw_term_relationships2.object_id		=	woocommerce_order_itemmeta.meta_value
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy2				ON term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id";
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
		
		if($pw_txt_email || ($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'") || $pw_sort_by == "billing_email"){
			$pw_txt_email_join = " 
				LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=pw_woocommerce_order_items.order_id";
		}
		if(($pw_txt_first_name and $pw_txt_first_name != '-1') || $pw_sort_by == "billing_name"){
			$pw_txt_first_name_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_woocommerce_order_items.order_id
			LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_woocommerce_order_items.order_id";
		}
		
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta4 ON pw_postmeta4.post_id=pw_woocommerce_order_items.order_id";
		
		if($state_code && $state_code != '-1')
			$state_code_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_state ON pw_postmeta_billing_state.post_id=pw_posts.ID";
		
		if($pw_payment_method)
			$pw_payment_method_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta5 ON pw_postmeta5.post_id=pw_woocommerce_order_items.order_id";
		
		if($pw_billing_post_code and $pw_billing_post_code != '-1')
			$pw_billing_post_code_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_postcode ON pw_postmeta_billing_postcode.post_id	=	pw_posts.ID";
		
		if($pw_coupon_used == "yes")
			$pw_coupon_used_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta6 ON pw_postmeta6.post_id=pw_woocommerce_order_items.order_id";
			
		if($pw_coupon_used == "yes")
			$pw_coupon_used_join .= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta7 ON pw_postmeta7.post_id=pw_posts.ID";
		
		if($pw_variation_id  && $pw_variation_id != "-1") {
			$pw_variation_id_join = " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_variation			ON pw_woocommerce_order_itemmeta_variation.order_item_id 		= 	pw_woocommerce_order_items.order_item_id";
		}
		
		if($pw_variation_only  && $pw_variation_only != "-1" && $pw_variation_only == "1") {
			$pw_variation_only_join = " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_variation			ON pw_woocommerce_order_itemmeta_variation.order_item_id 		= 	pw_woocommerce_order_items.order_item_id";
		}
		
		if($pw_variations_formated  != "-1" and $pw_variations_formated  != NULL){
			$pw_variations_formated_join = " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta8 ON pw_woocommerce_order_itemmeta8.order_item_id = pw_woocommerce_order_items.order_item_id";
			$pw_variations_formated_join .= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_variation ON pw_postmeta_variation.post_id = pw_woocommerce_order_itemmeta8.meta_value";
		}
			
		if($pw_order_meta_key and $pw_order_meta_key != '-1')
			$pw_order_meta_key_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_order_meta_key ON pw_order_meta_key.post_id=pw_posts.ID";
		
		if(($pw_coupon_codes && $pw_coupon_codes != "-1") or ($pw_coupon_code && $pw_coupon_code != "-1")){
			$pw_coupon_codes_join = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_coupon_item ON pw_woocommerce_order_coupon_item.order_id = pw_posts.ID AND pw_woocommerce_order_coupon_item.order_item_type = 'coupon'";
		}
		
		
		
		
		
		$post_type_condition="pw_posts.post_type = 'shop_order'";
		
		
		
		if($pw_txt_email || ($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'") || $pw_sort_by == "billing_email"){
			$pw_txt_email_condition_1 = " 
				AND postmeta.meta_key='_billing_email'";
		}
		
		if(($pw_txt_first_name and $pw_txt_first_name != '-1') || $pw_sort_by == "billing_name"){
			$pw_txt_first_name_condition_1 = " 
				AND pw_postmeta1.meta_key='_billing_first_name' 
				AND pw_postmeta2.meta_key='_billing_last_name'";
		}
		
		$other_condition_1 = "
		AND (woocommerce_order_itemmeta.meta_key = '_product_id' OR woocommerce_order_itemmeta.meta_key = '_variation_id')
		AND pw_woocommerce_order_itemmeta2.meta_key='_line_total'
		AND pw_woocommerce_order_itemmeta3.meta_key='_qty' ";
		
		
		
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_condition_1 = " AND pw_postmeta4.meta_key='_billing_country'";
		
		if($state_code && $state_code != '-1')
			$state_code_condition_1 = " AND pw_postmeta_billing_state.meta_key='_billing_state'";
		
		if($pw_billing_post_code and $pw_billing_post_code != '-1')
			$pw_billing_post_code_condition= " AND pw_postmeta_billing_postcode.meta_key='_billing_postcode' AND pw_postmeta_billing_postcode.meta_value LIKE '%{$pw_billing_post_code}%' ";
		
		if($pw_payment_method)
			$pw_payment_method_condition_1 = " AND pw_postmeta5.meta_key='_payment_method_title'";
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$date_condition = " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		}
		
		if($order_id)
			$order_id_condition = " AND pw_woocommerce_order_items.order_id = ".$order_id;
		
		if($pw_txt_email)
			$pw_txt_email_condition_2 = " AND postmeta.meta_value LIKE '%".$pw_txt_email."%'";
		
		if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'")
			$pw_paid_customer_condition = " AND postmeta.meta_value IN ('".$pw_paid_customer."')";
		
		//if($pw_txt_first_name and $pw_txt_first_name != '-1') $sql .= " AND (pw_postmeta1.meta_value LIKE '%".$pw_txt_first_name."%' OR pw_postmeta2.meta_value LIKE '%".$pw_txt_first_name."%')";
		if($pw_txt_first_name and $pw_txt_first_name != '-1')
			$pw_txt_first_name_condition_2 = " AND (lower(concat_ws(' ', pw_postmeta1.meta_value, pw_postmeta2.meta_value)) like lower('%".$pw_txt_first_name."%') OR lower(concat_ws(' ', pw_postmeta2.meta_value, pw_postmeta1.meta_value)) like lower('%".$pw_txt_first_name."%'))";
			
		//if($pw_id_order_status  && $pw_id_order_status != "-1") $sql .= " AND terms2.term_id IN (".$pw_id_order_status .")";
		
		if($pw_publish_order == 'yes')
			$pw_publish_order_condition_1 = " AND pw_posts.post_status = 'publish'";
			
		if($pw_publish_order == 'publish' || $pw_publish_order == 'trash')
			$pw_publish_order_condition_2 = " AND pw_posts.post_status = '".$pw_publish_order."'";
			
		//if($pw_country_code and $pw_country_code != '-1')	$sql .= " AND pw_postmeta4.meta_value LIKE '%".$pw_country_code."%'";
			
		//if($state_code and $state_code != '-1')	$sql .= " AND pw_postmeta_billing_state.meta_value LIKE '%".$state_code."%'";
		
		if($pw_country_code and $pw_country_code != '-1')
			$pw_country_code_condition_2 = " AND pw_postmeta4.meta_value IN ('".$pw_country_code."')";
			
		if($state_code && $state_code != '-1')
			$state_code_condition_2 = " AND pw_postmeta_billing_state.meta_value IN ('".$state_code."')";
		
		if($pw_payment_method)
			$pw_payment_method_condition_2 = " AND pw_postmeta5.meta_value LIKE '%".$pw_payment_method."%'";
		
		if($pw_order_meta_key and $pw_order_meta_key != '-1')
			$pw_order_meta_key_condition = " AND pw_order_meta_key.meta_key='{$pw_order_meta_key}' AND pw_order_meta_key.meta_value > 0";
		
		if($pw_order_item_name)
			$pw_order_item_name_condition = " AND pw_woocommerce_order_items.order_item_name LIKE '%".$pw_order_item_name."%'";
		
		if($txtProduct  && $txtProduct != '-1')
			$txtProduct_condition = " AND pw_woocommerce_order_items.order_item_name LIKE '%".$txtProduct."%'";	
		
		if($pw_product_id  && $pw_product_id != "-1")
			$pw_product_id_condition = " AND woocommerce_order_itemmeta.meta_value IN (".$pw_product_id .")";	
		
		//if($category_id  && $category_id != "-1") $sql .= " AND pw_terms.name NOT IN('simple','variable','grouped','external') AND term_taxonomy.taxonomy LIKE('product_cat') AND term_taxonomy.term_id IN (".$category_id .")";	
		if($category_id  && $category_id != "-1")
			$category_id_condition = " AND term_taxonomy.taxonomy LIKE('product_cat') AND term_taxonomy.term_id IN (".$category_id .")";
			
		if($brand_id  && $brand_id != "-1")
			$brand_id_condition = " AND term_taxonomy1.taxonomy LIKE('product_brand') AND term_taxonomy1.term_id IN (".$brand_id .")";	
		if($model_id  && $model_id != "-1")
			$model_id_condition = " AND term_taxonomy2.taxonomy LIKE('product_model') AND term_taxonomy2.term_id IN (".$model_id .")";	
		
			
		
		if($pw_id_order_status  && $pw_id_order_status != "-1")
			$pw_id_order_status_condition = " AND pw_term_taxonomy2.taxonomy LIKE('shop_order_status') AND pw_term_taxonomy2.term_id IN (".$pw_id_order_status .")";
		
		if($pw_coupon_used == "yes")
			$pw_coupon_used_condition = " AND( (pw_postmeta6.meta_key='_order_discount' AND pw_postmeta6.meta_value > 0) ||  (pw_postmeta7.meta_key='_cart_discount' AND pw_postmeta7.meta_value > 0))";
		
		
		if($pw_coupon_code && $pw_coupon_code != "-1"){
			$pw_coupon_code_condition = " AND (pw_woocommerce_order_coupon_item.order_item_name IN ('{$pw_coupon_code}') OR pw_woocommerce_order_coupon_item.order_item_name LIKE '%{$pw_coupon_code}%')";
		}
		
		if($pw_coupon_codes && $pw_coupon_codes != "-1"){
			$pw_coupon_codes_condition = " AND pw_woocommerce_order_coupon_item.order_item_name IN ({$pw_coupon_codes})";
		}
		
		if($pw_variation_id  && $pw_variation_id != "-1") {
			$pw_variation_id_condition = " AND pw_woocommerce_order_itemmeta_variation.meta_key = '_variation_id' AND pw_woocommerce_order_itemmeta_variation.meta_value IN (".$pw_variation_id .")";						
		}
		
		if($pw_variation_only  && $pw_variation_only != "-1" && $pw_variation_only == "1") {
			$pw_variation_only_condition = " AND pw_woocommerce_order_itemmeta_variation.meta_key 	= '_variation_id'
			AND (pw_woocommerce_order_itemmeta_variation.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta_variation.meta_value > 0)";						
		}
		
		
		if($pw_variations_formated  != "-1" and $pw_variations_formated  != NULL){
			$pw_variations_formated_condition = " 
			AND pw_woocommerce_order_itemmeta8.meta_key = '_variation_id' AND (pw_woocommerce_order_itemmeta8.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta8.meta_value > 0)";
			$pw_variations_formated_condition .= " 
			AND pw_postmeta_variation.meta_value IN ('{$pw_variations_formated}')";
		}
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN ('".$pw_hide_os."')";
		
		
		
		$sql ="SELECT $all_fields_cols $sql_columns FROM $sql_joins";
		
		$sql .="$category_id_join  $all_tax_joins $pw_id_order_status_join $pw_txt_email_join $pw_txt_first_name_join $all_fields_joins
				$pw_country_code_join $state_code_join $pw_payment_method_join $pw_billing_post_code_join
				$pw_coupon_used_join $pw_variation_id_join $pw_variation_only_join $pw_variations_formated_join
				$pw_order_meta_key_join $pw_coupon_codes_join";
		
		$sql .= " Where $post_type_condition  $all_fields_conditions $pw_txt_email_condition_1 $pw_txt_first_name_condition_1
						$other_condition_1 $pw_country_code_condition_1 $state_code_condition_1
						$pw_billing_post_code_condition $pw_payment_method_condition_1 $date_condition
						$order_id_condition $pw_txt_email_condition_2 $pw_paid_customer_condition
						$pw_txt_first_name_condition_2 $pw_publish_order_condition_1 $pw_publish_order_condition_2
						$pw_country_code_condition_2 $state_code_condition_2 $pw_payment_method_condition_2
						$pw_order_meta_key_condition $pw_order_item_name_condition $txtProduct_condition
						$pw_product_id_condition $category_id_condition $all_tax_conditions  $pw_id_order_status_condition
						$pw_coupon_used_condition $pw_coupon_code_condition $pw_coupon_codes_condition
						$pw_variation_id_condition $pw_variation_only_condition $pw_variations_formated_condition
						$pw_order_status_condition $pw_hide_os_condition ";
		
		$sql_group_by = " GROUP BY pw_woocommerce_order_items.order_item_id ";
		
		$sql .=$sql_group_by;
		
		//print_r($search_fields);
		//echo $sql;
		//print_r($custom_tax_cols);
		
		
			
		if($pw_detail_view=="yes"){

			$columns=array(
				array('lable'=>__('Order ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
				array('lable'=>__('Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Coupon Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Variation',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Qty.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Rate',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Prod. Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Prod. Discount',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				/*array('lable'=>__('Invoice Action',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Order Detail',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),*/
			);
			
			$product_custom_fields='';
			$order_custom_fields='';
			$gravity_form_custom_fields='';
			$extra_addon_form_custom_fields='';
			
			$array_index=6;
			if(is_array($order_custom_fields_cols)){
				array_splice($columns,$array_index,0,$order_custom_fields_cols);
				$array_index+=count($order_custom_fields_cols)+2;
			}else{
				$array_index=8;
			}
			
			
			
			//
			if(is_array($custom_tax_cols)){
				array_splice($columns,$array_index,0,$custom_tax_cols);
				$array_index+=count($custom_tax_cols);
			}else{
				$array_index=8;
				$array_index+=count($order_custom_fields_cols);
			}	
			
			//$array_index=10;	
			
			if(is_array($custom_fields_cols)){
				array_splice($columns,$array_index,0,$custom_fields_cols);	
			}
				
		}else{
			
			$columns=array(
				array('lable'=>__('Order ID',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
				array('lable'=>__('Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Tax Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Shipping Method',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Payment Method',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
				array('lable'=>__('Order Currency',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Coupon Code',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Items',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Gross Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Order Discount Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Cart Discount Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Total Discount Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Shipping Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Shipping Tax Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Order Tax Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Total Tax Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Part Refund Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Net Amt.',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
			);
			
		}
		
		$this->table_cols = $columns;
		
	}
	elseif($file_used=="data_table"){

		$first_order_id='';
		
		
		//$aaa=$this->pw_get_full_post_meta(537);
		//die(print_r($aaa));
		
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
		
		/////////////CUSTOM TAXONOMY AND FIELDS////////////
		$key=$this->pw_get_woo_requests('table_names','',true);
		$visible_custom_taxonomy='';
		$post_name='product';
		$all_tax=$this->fetch_product_taxonomies( $post_name );
		$current_value=array();
		if(is_array($all_tax) && count($all_tax)>0){
			//FETCH TAXONOMY
			foreach ( $all_tax as $tax ) {
				$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
				$show_column=get_option($key.'_'.$tax.'_column');
				if($show_column=='on'){
					$visible_custom_taxonomy[]=$tax;
				}
			}
		}
						
		$visible_custom_field='';
		$custom_fields=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields');
		if(is_array($custom_fields)){
			foreach($custom_fields as $fields){
				$show_column=get_option($fields.'_column');
				if($show_column=="on")
				//if($this->pw_get_woo_requests('pw_'.$fields,NULL,true))
					$visible_custom_field[]=$fields;
			}
		}
		
		
		global $wpdb;
		$gravity_form_custom_fields='';
		if(defined("GRAVITY_MANAGER_URL"))
		{
			$types = $wpdb->get_results("SELECT display_meta FROM {$wpdb->prefix}rg_form_meta", ARRAY_A);
			if ($types!=null && is_array($types)) {
				foreach($types as $type){
					//print_r($type['display_meta']);
					$form = json_decode( $type['display_meta'] );
					//print_r($form->fields[1]->label);
					foreach($form->fields as $fields){
						$value=($fields->label);
						$value=str_replace(" ","_",$value);
						$gravity_form_custom_fields[]=$value;
					}
				}
			}
		}
		
		$extra_addon_form_custom_fields='';
		if(class_exists("WC_Product_Addons"))
		{
			$types = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}postmeta where meta_key='_product_addons'", ARRAY_A);
			if ($types!=null && is_array($types)) {
				foreach($types as $type){
					//print_r(unserialize ($type['meta_value']));
					$form = unserialize ( $type['meta_value'] );
					foreach($form as $fields){
						$value=($fields['options']);
						$parent=str_replace(" ","___",$fields['name']);
						if($fields['type']=='checkbox')
							continue;
						//print_r($value);
						foreach($value as $ffield){
							$valuew=str_replace(" ","___",$ffield['label']);
							
							$final_value='';
							$final_label='';
							if($ffield['label']!='')
							{
								$final_value=$parent.'__'.$valuew;
								$final_label=$parent.'__'.$ffield['label'];
							}else{
								$final_value=$parent;
								$final_label=$parent;
							}
							
							$extra_addon_form_custom_fields[]=$final_value;
						}
					}
				}
			}
		}
		
		//print_r($extra_addon_form_custom_fields);
		

		function fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields){
			
			global $wpdb,$pw_rpt_main_class;
			if(is_array($gravity_form_custom_fields) && in_array($field,$gravity_form_custom_fields)){
				
				$cond_field=str_replace("_"," ",$field);
				
				${$field} 	= $pw_rpt_main_class->pw_get_woo_requests('pw_'.$field,NULL,true);
		
				$operator=get_option($field.'_operator');
				$operator_condition=get_operator($operator,${$field});
				
				$sql="SELECT woocommerce_order_itemmeta.meta_value as $field FROM {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta Where woocommerce_order_itemmeta.meta_key='$cond_field' AND woocommerce_order_itemmeta.order_item_id='$order_item_id'";
				
				
				
				if(${$field}){
							
					$sql .= " AND woocommerce_order_itemmeta.meta_value $operator_condition ";
				}
				
			//	echo $sql;
				
				$g_fields = $wpdb->get_results($sql, ARRAY_A);

				if(isset($g_fields[0]))
					return $g_fields[0][$field];
				else
					return '-';
					
			}else{
				return false;
			}
		}

		function fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields){
			
			
			//$cond_field=str_replace("___"," ",$field);
			//echo $field;

			global $wpdb,$pw_rpt_main_class;
			if(is_array($extra_addon_form_custom_fields) && in_array($field,$extra_addon_form_custom_fields)){
				
				${$field} 	= $pw_rpt_main_class->pw_get_woo_requests('pw_'.$field,NULL,true);
				
				//echo 'FIELD : '.${$field}."<br />";
		
				$operator=get_option($field.'_operator');
				$operator_condition=get_operator($operator,${$field});
				
				$cond_field=str_replace("___"," ",$field);
				//$cond_field=str_replace("@"," ",$cond_field);
				$cond_field=str_replace("__"," - ",$cond_field);
				
				
				$sql="SELECT woocommerce_order_itemmeta.meta_value as $field FROM {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta Where woocommerce_order_itemmeta.meta_key LIKE '%$cond_field%' AND woocommerce_order_itemmeta.order_item_id='$order_item_id'";
				
				
				if(${$field}){
							
					$sql .= " AND woocommerce_order_itemmeta.meta_value $operator_condition ";
				}
				//echo $sql."<br />";
				//echo $sql;
				
				$g_fields = $wpdb->get_results($sql, ARRAY_A);

				if(isset($g_fields[0])){
					return $g_fields[0][$field];
				}
				else if(${$field}){
					return false;
				}else{
					return '-';
				}
			}else{
				return false;
			}
		}
		
		function is_order_product_field($field,$type){
			global $wpdb;
			$product_custom_fields='';
			$order_custom_fields='';
			
			if($type=='product'){
			
				$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='product'  GROUP BY pw_postmeta.meta_key", ARRAY_A);
			
								
				if ($types!=null && is_array($types)) {
					
					foreach($types as $k=>$v) {
						$product_custom_fields[]= $v['meta_key'];
					}
					
				}
				if(in_array($field,$product_custom_fields)) 
					return true;
				
			}else{
			  
				$types = $wpdb->get_results("SELECT pw_postmeta.meta_key as meta_key FROM ".$wpdb->postmeta." as pw_postmeta INNER JOIN ".$wpdb->posts." as pw_post ON pw_postmeta.post_id=pw_post.ID where pw_post.post_type='shop_order' GROUP BY pw_postmeta.meta_key", ARRAY_A);
				
				
				if ($types!=null && is_array($types)) {
					foreach($types as $k=>$v) {
						$order_custom_fields[]=  substr($v['meta_key'],1);
					}
				}
				
				if(in_array($field,$order_custom_fields)) 
					return true;
			}
			
			return false;
		}
		
		//////////////////////////////////////////////////////
		
		$items_render=array();
		global $pw_rpt_main_class;
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			
			$flag=array(true);
			$fill=false;
			foreach($gravity_form_custom_fields as $gfields){	
				
				${$gfields} 	= $pw_rpt_main_class->pw_get_woo_requests('pw_'.$gfields,NULL,true);
				if(${$gfields}){
					$fill=true;
				}else{
					$fill=false;
				}
				
				$order_item_id= $items->order_item_id;		
				if($fill && fetch_gravity_value($order_item_id,$gfields,$gravity_form_custom_fields) && fetch_gravity_value($order_item_id,$gfields,$gravity_form_custom_fields)!='-' ){
					$flag[]=true;
					
				}else if($fill && (!fetch_gravity_value($order_item_id,$gfields,$gravity_form_custom_fields) || fetch_gravity_value($order_item_id,$gfields,$gravity_form_custom_fields)=='-')){
					$flag[]=false;
				}
				
			}
			
			if(in_array(false,$flag))
				continue;
				
				
			$flag=array(true);
			$fill=false;
			
			foreach($extra_addon_form_custom_fields as $gfields){	
				
				${$gfields} 	= $pw_rpt_main_class->pw_get_woo_requests('pw_'.$gfields,NULL,true);
				if(${$gfields}){
					$fill=true;
				}else{
					$fill=false;
				}
				$order_item_id= $items->order_item_id;		
				if($fill && fetch_extra_addon_value($order_item_id,$gfields,$extra_addon_form_custom_fields) && fetch_extra_addon_value($order_item_id,$gfields,$extra_addon_form_custom_fields)!='-'){
					
					$flag[]=true;
					
				}else if($fill && (!fetch_extra_addon_value($order_item_id,$gfields,$extra_addon_form_custom_fields) || fetch_extra_addon_value($order_item_id,$gfields,$extra_addon_form_custom_fields)=='-')){
					$flag[]=false;
				}
				
			}
			
			//die(print_r($flag));
			
			if(in_array(false,$flag))
				continue;	
			
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
			$pw_detail_view		= $this->pw_get_woo_requests('pw_view_details',"no",true);			
			if($pw_detail_view=="yes")
			{
				if($new_order)
				{ 
					$datatable_value.=("<tr>");
					
						//order ID
						$display_class='';
						if($this->table_cols[0]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items->order_id;
						$datatable_value.=("</td>");
			
						//Name
						$display_class='';
						if($this->table_cols[1]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items->billing_name;
						$datatable_value.=("</td>");
					
						//Email
						$pw_table_value = isset($items->billing_email) ? $items->billing_email : '';
						$pw_table_value = $this->pw_email_link_format($pw_table_value,false);
						$display_class='';
						if($this->table_cols[2]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $pw_table_value;
						$datatable_value.=("</td>");
						
						//Date
						$date_format		= get_option( 'date_format' );
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= date($date_format,strtotime($items->order_date));
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
						
						
						
						
						//Coupon Code
					
						//$pw_table_value= $this->pw_oin_list($items->order_id,'coupon');
						
						$pw_table_value=$this->pw_get_woo_coupons($items->order_id);
						
						$display_class='';
						if($this->table_cols[5]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $pw_table_value;
						$datatable_value.=("</td>");
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(!is_order_product_field($field,'order'))
									continue;
								//CUSTOM FIELDS
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								$order_item_id=$items->order_item_id;
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'order'))
										$datatable_value.= $items->$field;
								}
									
								$datatable_value.=("</td>");
							}
						}
						
						//Products
						$display_class='';
						if($this->table_cols[7]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						
						/*if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								//Category
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
									$datatable_value.= '';
								$datatable_value.=("</td>");
							}
						}*/
						
						//Category
						$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						
						//CUSTOM TAXONOMY
						if(is_array($visible_custom_taxonomy)){
							foreach((array)$visible_custom_taxonomy as $tax){
								//Category
								$display_class='';
								if($this->table_cols[6]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
									$datatable_value.= '';
								$datatable_value.=("</td>");
							}
						}
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(is_order_product_field($field,'order'))
									continue;
								//CUSTOM FIELDS
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								$order_item_id=$items->order_item_id;
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'product'))
										$datatable_value.= '';	
								}
									
								$datatable_value.=("</td>");
							}
						}
						
						//SKU
						$display_class='';
						if($this->table_cols[8]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Variation
						$pw_table_value= $this->pw_get_woo_variation($items->order_item_id);
						$display_class='';
						if($this->table_cols[9]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Qty.
						$display_class='';
						if($this->table_cols[10]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Rate
						$pw_table_value = isset($items -> product_rate) ? $items -> product_rate : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $this->price($pw_table_value);
						
						$display_class='';
						if($this->table_cols[11]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Prod. Amt.
						
						$display_class='';
						if($this->table_cols[12]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Prod. Discount
						$display_class='';
						if($this->table_cols[13]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Net Amt.
						$display_class='';
						if($this->table_cols[13]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						/*//Invoice Action
						$display_class='';
						if($this->table_cols[15]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '<center><i class="fa fa-download"></i></center>';
						$datatable_value.=("</td>");
						
						//Order Detial
						$display_class='';
						if($this->table_cols[16]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '<center><i class="fa fa-search"></i></center>';
						$datatable_value.=("</td>");*/
					
					$datatable_value.=("</tr>");
					
					
					//////////////////////////
					//ITEMS
					//////////////////////////
					$datatable_value.=("<tr>");
					
						//order ID
						$display_class='';
						if($this->table_cols[0]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '<span style="display:none">'.$items->order_id.'<span>';
						$datatable_value.=("</td>");
			
						//Name
						$display_class='';
						if($this->table_cols[1]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
					
						//Email
						$display_class='';
						if($this->table_cols[2]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Date
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Status
						$display_class='';
						if($this->table_cols[4]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						
						
						
						//Coupon Code
					
						//$pw_table_value= $this->pw_oin_list($items->order_id,'coupon');
						
						$pw_table_value=$this->pw_get_woo_coupons($items->order_id);
						
						$display_class='';
						if($this->table_cols[5]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(!is_order_product_field($field,'order'))
									continue;
								//CUSTOM FIELDS
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								$order_item_id=$items->order_item_id;
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'order'))
										$datatable_value.= '';
											
								}
								$datatable_value.=("</td>");
							}
						}	   
						
						//Products
						$display_class='';
						if($this->table_cols[7]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items->product_name;
						$datatable_value.=("</td>");
						
						//Category
						$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
						$datatable_value.=("</td>");
						
						//CUSTOM TAXONOMY
						if(is_array($visible_custom_taxonomy)){
							foreach((array)$visible_custom_taxonomy as $tax){
								//Category
								$display_class='';
								if($this->table_cols[6]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
									$datatable_value.= $this->pw_get_cn_product_id($items->product_id,$tax);
								$datatable_value.=("</td>");
							}
						}
						
						//Category
						/*$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
						$datatable_value.=("</td>");
						*/
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(is_order_product_field($field,'order'))
									continue;
								//CUSTOM FIELDS
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								$order_item_id=$items->order_item_id;
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'product'))
										$datatable_value.= $items->$field;
									else	
										$datatable_value.= '';
											
								}else if(fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields)){
									$datatable_value.= fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields);
								}else if(fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									$datatable_value.= fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields);
								}
									
								$datatable_value.=("</td>");
							}
						}	   
							   
						//SKU
						$display_class='';
						if($this->table_cols[8]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
						$datatable_value.=("</td>");
						
						//Variation
						$pw_table_value= $this->pw_get_woo_variation($items->order_item_id);
						$display_class='';
						if($this->table_cols[9]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $pw_table_value;
						$datatable_value.=("</td>");
						
						//Qty.
						$display_class='';
						if($this->table_cols[10]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items -> product_quantity;
						$datatable_value.=("</td>");
						
						//Rate
						$pw_table_value = isset($items -> product_rate) ? $items -> product_rate : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[11]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						//Prod. Amt.
						$pw_table_value = isset($items -> item_amount) ? $items -> item_amount : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[12]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
	
						//Prod. Discount
						$pw_table_value = isset($items -> item_discount) ? $items -> item_discount : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[13]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						//Net Amt.
						$pw_table_value = isset($items -> total_price) ? $items -> total_price : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[14]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						/*//Invoice Action
						$display_class='';
						if($this->table_cols[15]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Order Detial
						$display_class='';
						if($this->table_cols[16]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");*/
					
					$datatable_value.=("</tr>");
				
				}else{
					$datatable_value.=("<tr>");
					
						//order ID
						$display_class='';
						if($this->table_cols[0]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '<span style="display:none">'.$items->order_id.'<span>';
						$datatable_value.=("</td>");
			
						//Name
						$display_class='';
						if($this->table_cols[1]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
					
						//Email
						$display_class='';
						if($this->table_cols[2]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Date
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Status
						$display_class='';
						if($this->table_cols[4]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						
						
						//Coupon Code
					
						//$pw_table_value= $this->pw_oin_list($items->order_id,'coupon');
						
						$pw_table_value=$this->pw_get_woo_coupons($items->order_id);
						
						$display_class='';
						if($this->table_cols[5]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(!is_order_product_field($field,'order'))
									continue;
								//Category
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								
								$order_item_id=$items->order_item_id;
								/*if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields)){
									$datatable_value.= $items->$field;	
								}else{
									$datatable_value.= fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields);
								}*/
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'product'))
										$datatable_value.= '';	
								}
								
								$datatable_value.=("</td>");
							}
						}
						
						//Products
						$display_class='';
						if($this->table_cols[7]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items->product_name;
						$datatable_value.=("</td>");
						
						//Category
						$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
						$datatable_value.=("</td>");
						
						//CUSTOM TAXONOMY
						if(is_array($visible_custom_taxonomy)){
							foreach((array)$visible_custom_taxonomy as $tax){
								//Category
								$display_class='';
								if($this->table_cols[6]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
									$datatable_value.=$this->pw_get_cn_product_id($items->product_id,$tax);
								$datatable_value.=("</td>");
							}
						}
						
						//Category
						/*$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
						$datatable_value.=("</td>");*/
						
						//CUSTOM FIELDS
						if(is_array($visible_custom_field)){
							foreach((array)$visible_custom_field as $field){
								if(is_order_product_field($field,'order'))
									continue;
								//Category
								$display_class='';
								if($this->table_cols[4]['status']=='hide') $display_class='display:none';
								$datatable_value.=("<td style='".$display_class."'>");
								
								$order_item_id=$items->order_item_id;
								/*if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields)){
									$datatable_value.= $items->$field;	
								}else{
									$datatable_value.= fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields);
								}*/
								
								if(!fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields) && !fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									if(is_order_product_field($field,'product'))
										$datatable_value.= $items->$field;
									else	
										$datatable_value.= '';	
								}else if(fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields)){
									$datatable_value.= fetch_gravity_value($order_item_id,$field,$gravity_form_custom_fields);
								}else if(fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields)){
									$datatable_value.= fetch_extra_addon_value($order_item_id,$field,$extra_addon_form_custom_fields);
								}
								
								
								
								$datatable_value.=("</td>");
							}
						}
							   
						//SKU
						$display_class='';
						if($this->table_cols[8]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
						$datatable_value.=("</td>");
						
						//Variation
						$pw_table_value= $this->pw_get_woo_variation($items->order_item_id);
						$display_class='';
						if($this->table_cols[9]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $pw_table_value;
						$datatable_value.=("</td>");
						
						//Qty.
						$display_class='';
						if($this->table_cols[10]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $items -> product_quantity;
						$datatable_value.=("</td>");
						
						//Rate
						$pw_table_value = isset($items -> product_rate) ? $items -> product_rate : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[11]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						//Prod. Amt.
						$pw_table_value = isset($items -> item_amount) ? $items -> item_amount : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[12]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						//Prod. Discount
						$pw_table_value = isset($items -> item_discount) ? $items -> item_discount : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val :$pw_table_value;
						
						$display_class='';
						if($this->table_cols[13]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						//Net Amt.
						$pw_table_value = isset($items -> total_price) ? $items -> total_price : 0;
						$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
						
						$display_class='';
						if($this->table_cols[14]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
						$datatable_value.=("</td>");
						
						/*//Invoice Action
						$display_class='';
						if($this->table_cols[15]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");
						
						//Order Detial
						$display_class='';
						if($this->table_cols[16]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= '';
						$datatable_value.=("</td>");*/
					
					$datatable_value.=("</tr>");
				}
			
			}else
			{
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
		
					//Name
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->billing_name;
					$datatable_value.=("</td>");
				
					//Email
					$pw_table_value = isset($items->billing_email) ? $items->billing_email : '';
					$pw_table_value = $this->pw_email_link_format($pw_table_value,false);
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $pw_table_value;
					$datatable_value.=("</td>");
					
					//Date
					$date_format		= get_option( 'date_format' );
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
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
					
					//Tax Name
					$tax_name=$this->pw_oin_list($items->order_id,'tax');
					
					$display_class='';
					if($this->table_cols[5]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=isset($tax_name[$items->order_id]) ? $tax_name[$items->order_id] : "";
					$datatable_value.=("</td>");
					
					//Shipping Method
					$shipping_method=$this->pw_oin_list($items->order_id,'shipping');
					$display_class='';
					if($this->table_cols[6]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=isset($shipping_method[$items->order_id]) ? $shipping_method[$items->order_id] : "";
					$datatable_value.=("</td>");
					
					//Payment Method
					$display_class='';
					if($this->table_cols[7]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= isset($items->payment_method_title) ? $items->payment_method_title : "" ;
					$datatable_value.=("</td>");
					
					//print_r($items);
					
					//Order Currency
					$display_class='';
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= isset($items->order_currency) ? $items->order_currency : "";
					$datatable_value.=("</td>");
					
					//Coupon Code
					$display_class='';
					$pw_coupon_code=$this->pw_oin_list($items->order_id,'coupon');
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=isset($pw_coupon_code[$items->order_id]) ? $pw_coupon_code[$items->order_id] : "";
					$datatable_value.=("</td>");
					
					//Items Count
					$display_class='';
					$items_count = $this->pw_get_oi_count($items->order_id,'line_item');
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=isset($items_count[$items->order_id]) ? $items_count[$items->order_id] : "";
					$datatable_value.=("</td>");
					
					//Cross Amt
					$display_class='';
					$pw_table_value = isset($items -> gross_amount) ? $items -> gross_amount : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Order Discount
					$display_class='';
					$pw_table_value = isset($items -> order_discount) ? $items -> order_discount : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=$this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Cart Discount
					$display_class='';
					$pw_table_value = isset($items -> cart_discount) ? $items -> cart_discount : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Total Discount
					$display_class='';
					$pw_table_value = isset($items -> total_discount) ? $items -> total_discount : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Order Shipping
					$display_class='';
					$pw_table_value = isset($items -> order_shipping) ? $items -> order_shipping : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Order Shipping Tax
					$display_class='';
					$pw_table_value = isset($items -> order_shipping_tax) ? $items -> order_shipping_tax : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Order Tax
					$display_class='';
					$pw_table_value = isset($items -> order_tax) ? $items -> order_tax : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Total Tax
					$display_class='';
					$pw_table_value = isset($items -> total_tax) ? $items -> total_tax : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					//Part Refund
					$display_class='';
					$order_refund_amnt= $this->pw_get_por_amount($items -> order_id);

					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= (isset($order_refund_amnt[$items->order_id])? $this->price($order_refund_amnt[$items->order_id],array("currency" => $fetch_other_data['order_currency'])):$this->price(0,array("currency" => $fetch_other_data['order_currency'])));
					$datatable_value.=("</td>");
					$part_refund=(isset($order_refund_amnt[$items->order_id])? $order_refund_amnt[$items->order_id]:0);
					
					
					//Order Total
					$display_class='';
					$pw_table_value = isset($items -> order_total) ? ($items -> order_total)-$part_refund : 0;
					$pw_table_value = $pw_table_value == 0 ? $pw_null_val : $pw_table_value;
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($pw_table_value,array("currency" => $fetch_other_data['order_currency']));
					$datatable_value.=("</td>");
					
					
				$datatable_value.=("</tr>");	
			}
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
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-user"></i></span>
                    <input name="pw_first_name_text" type="text"  class=""/>
                </div>
                
                <?php
					/////////////////
					//CUSTOM TAXONOMY
					$key=isset($_GET['smenu']) ? $_GET['smenu']:$_GET['parent'];
					$args_f=array("page"=>$key);
					echo $this->make_custom_taxonomy($args_f);
                ?>
                
                <?php
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_category_id');

                	if($this->get_form_element_permission('pw_category_id') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
							$col_style='display:none';
							
						//if(count($permission_value)==1) $col_style='display:none';
				?>
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
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
                        
						$current_category=$permission_value;
						
                        $categories = get_terms('product_cat',$args);
                        $option='';
                        foreach ($categories as $category) {
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
								continue;
								
							if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
								$selected="selected";		
							
							
                            if(in_array($category->term_id,$current_category))
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
					if($this->get_form_element_permission('pw_product_id') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
							$col_style='display:none';
				?>
                	
                
                <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-gear"></i></span>
					<?php
                        $products=$this->pw_get_product_woo_data('all');
                        $option='';
                        $current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($products as $product){
							
							$selected='';
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
								$selected="selected";
								
                            
                            //if($current_product==$product->id)
							if(in_array($product->id,$current_product))
                                $selected="selected";*/
                            $option.="<option $selected value='".$product -> id."' >".$product -> label." </option>";
                        }
                        
                        
                    ?>
                    <select name="pw_product_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                    	
                        <?php
                        	if($this->get_form_element_permission('pw_product_id') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
                        <?php _e('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-user"></i></span>
					<?php
                        $customers=$this->pw_get_woo_customers_orders();
                        $option='';
                        foreach($customers as $customer){
                            $option.="<option value='".$customer -> id."' >".$customer -> label." ($customer->counts)</option>";
                        }
                    ?>
                    <select name="pw_customers_paid[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                 
                 <?php
				 	$col_style='';
                 	$permission_value=$this->get_form_element_value_permission('pw_orders_status' ||  $permission_value!='');
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
								
							/*if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
								$selected="selected";*/	
							
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
							$col_style='display:none';
						
				?> 
                 <div class="col-md-6"  style=" <?php echo $col_style;?>">
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
							
							/*if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
								$selected="selected";*/
								
							
								
								
							                                                    
                            $pw_table_value = $country->countries[$countries->id];
                            $option.="<option $selected value='".$countries->id."' >".$pw_table_value."</option>";
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
							
							/*if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
								$selected="selected";*/
							                                                    
                            $pw_table_value = $this->pw_get_woo_bsn($state->billing_country,$state->id);
                            $option.="<option value='".$state->id."' >".$pw_table_value." ($state->billing_country)</option>";
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
           		
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Postcode(Zip)',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map-marker"></i></span>
                    <input name="pw_bill_post_code" type="text"/>
                </div>	

                 
                 <!--<div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Min & Max By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-arrows-h"></i></span>    
                    <select name="order_meta_key[]" id="order_meta_key2" class="order_meta_key normal_view_only">
                        <option value="-1">Select All</option>
                        <option value="_order_total">Order Net Amount</option>
                        <option value="_order_discount">Order Discount Amount</option>
                        <option value="_order_shipping">Order Shipping Amount</option>
                        <option value="_order_shipping_tax">Order Shipping Tax Amount</option>
                    </select>
                    <br />
                    <span class="description"><?php _e("Enable this selection by uncheck 'Show Order Item Details'
",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span>
                </div>	
                 
                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Min Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <span class="awr-form-icon"><i class="fa fa-battery-0"></i></span>     
                    <input name="min_amount" type="text"/>
                    <br />
                    <span class="description"><?php _e("Enable this selection by uncheck 'Show Order Item Details'
",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span>
                </div>	
                 
                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Max Amount',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-battery-4"></i></span>
                    <input name="max_amount" type="text"/>
                    <br />
                    <span class="description"><?php _e("Enable this selection by uncheck 'Show Order Item Details'
",__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></span>
                </div>-->	
                 
                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Email',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-envelope-o"></i></span>
                    <input name="pw_email_text" type="text"/>
                </div>	
                 
                
                <?php
					$custom_fields=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields');
					if(is_array($custom_fields)){
				?>
                
                <div class="col-md-6" style="border:#2CC185 2px solid;width:100%">
                    <div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #2CC185;color: #fff;">
                        <?php _e('Custom Fields',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                
                <?php
                	///////////CUSTOM FIELDS///////////
					$custom_fields=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_fields');
					foreach($custom_fields as $fields){
						echo '
							<div class="col-md-6">
								<div class="awr-form-title">
									'.get_option($fields."_translate",$fields).'
								</div>
								<span class="awr-form-icon"><i class="fa fa-map-marker"></i></span>
								<input name="pw_'.$fields.'" type="text" placeholder="'.get_option($fields."_translate").'"/>
							</div>';
					}
				?> 
                </div>
                <?php } ?> 
                 
                 
                 <!--<div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Order By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-sort-alpha-asc"></i></span>
                    <div class="row">
						<div class="col-md-6">
						
							<select name="sort_by" id="sort_by" class="sort_by">
								<option value="order_id" selected="selected">Order ID</option>
								<option value="billing_name">Name</option>
								<option value="billing_email">Email</option>
								<option value="order_date">Date</option>
								<option value="status">Status</option>
							</select>
						</div>
						<div class="col-md-6">
							<select name="order_by" id="order_by" class="order_by">
								<option value="ASC">Ascending</option>
								<option value="DESC" selected="selected">Descending</option>
							</select> 
						</div>                       
					</div>	
                </div>	-->
                 
                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Show Order Item Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					
                    <input name="pw_view_details" type="checkbox" value="yes" checked/>
                    
                </div>	
                 
                 <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Coupon Used Only',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					
                    <input name="pw_use_coupon" type="checkbox" value="yes"/>
                    
                </div>	
                 
                 
                 
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