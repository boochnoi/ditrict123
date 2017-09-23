<?php
	
	if($file_used=="sql_table")
	{
	
		//GET POSTED PARAMETERS
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','product_name',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		$group_by 			= $this->pw_get_woo_requests('pw_groupby','variation_id',true);
		
		$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid',"-1",true);
		
		if($pw_paid_customer != NULL  && $pw_paid_customer != '-1')
		{
			$pw_paid_customer = "'".str_replace(",", "','",$pw_paid_customer)."'";
		}
		
		$pw_billing_post_code	= $this->pw_get_woo_requests('pw_bill_post_code',"-1",true);
		
		$pw_product_sku 		= $this->pw_get_woo_requests('pw_sku_products','-1',true);	
		if($pw_product_sku != NULL  && $pw_product_sku != '-1'){
			$pw_product_sku  		= "'".str_replace(",","','",$pw_product_sku)."'";
		}
		
		$pw_variation_sku 		= $this->pw_get_woo_requests('pw_sku_variations','-1',true);	
		if($pw_variation_sku != NULL  && $pw_variation_sku != '-1'){
			$pw_variation_sku  		= "'".str_replace(",","','",$pw_variation_sku)."'";
		}
		
		$page				= $this->pw_get_woo_requests('page',NULL);	
		$pw_show_variation 	= get_option($page.'_show_variation','variable');
		$report_name 		= apply_filters($page.'_default_report_name', 'product_page');

		$report_name 		= $this->pw_get_woo_requests('report_name',$report_name,true);
		$admin_page			= $this->pw_get_woo_requests('admin_page',$page,true);

		$pw_EndDate				= $this->pw_get_woo_requests('pw_to_date',false);
		$pw_StareDate			= $this->pw_get_woo_requests('pw_from_date',false);
		$category_id		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		$pw_hide_os='"trash"';
		$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id','-1',true);
		$pw_variations			= $this->pw_get_woo_requests('pw_variations','-1',true);
		$pw_variation_column	= $this->pw_get_woo_requests('pw_variation_cols','1',true);
		$pw_show_variation		= $this->pw_get_woo_requests('pw_show_adr_variaton',$pw_show_variation,true);
		$count_generated	= $this->pw_get_woo_requests('count_generated',0,true);				
		
		
		
		$item_att = array();
		$pw_item_meta_key =  '-1';
		if($pw_show_variation=='variable' && $pw_variations != '-1' and strlen($pw_variations) > 0){

				$pw_variations = explode(",",$pw_variations);
				//$this->print_array($pw_variations);
				$var = array();
				foreach($pw_variations as $key => $value):
					$var[] .=  "attribute_pa_".$value;
					$var[] .=  "attribute_".$value;
					$item_att[] .=  "pa_".$value;
					$item_att[] .=  $value;
				endforeach;
				$pw_variations =  implode("', '",$var);
				$pw_item_meta_key =  implode("', '",$item_att);
		}
		$pw_variation_attributes= $pw_variations;
		$pw_variation_item_meta_key= $pw_item_meta_key;
		
		
		
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
		
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		
		/////////////////////////
		//APPLY PERMISSION TERMS
		$key=$this->pw_get_woo_requests('table_names','',true);
		
		$permission_value=$this->get_form_element_value_permission('pw_category_id',$key);
		$permission_enable=$this->get_form_element_permission('pw_category_id',$key);
		
		if($permission_enable && $category_id=='-1' && $permission_value!=1){
			$category_id=implode(",",$permission_value);
		}
		
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
		
		
		//CATEGORY
		$category_id_join='';
		$category_id_condition='';
		$pw_cat_prod_id_string_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PRODUCT ID
		$pw_product_id_condition='';
		
		//ORDER  
		$pw_id_order_status_join='';
		
		//VARIATION 
		$pw_variation_item_meta_key_join='';
		$sql_variation_join='';
		$pw_show_variation_join='';
		$pw_variation_item_meta_key_condition='';
		$sql_variation_condition='';
		
		//SKU
		$product_variation_sku_condition='';
		$pw_variation_sku_condition='';
		$pw_product_sku_condition='';
		
		//PAID CUSTOMER
		$pw_paid_customer_join='';
		$pw_paid_customer_condition='';
		
		//BILLING CODE
		$pw_billing_post_code_join='';
		$pw_billing_post_code_condition='';
		
		//ORDER STATUS
		$pw_id_order_status_condition='';
		$pw_order_status_condition='';
		
		//HIDE ORDER
		$pw_hide_os_condition='';
		
		
		$sql_columns = "
		pw_woocommerce_order_items.order_item_name			AS 'product_name'
		,SUM(woocommerce_order_itemmeta.meta_value)		AS 'quantity'
		,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'amount'";
		
		//COST OF GOOD
		if($pw_show_cog=='yes'){
			$sql_columns .= " ,SUM(woocommerce_order_itemmeta.meta_value * pw_woocommerce_order_itemmeta22.meta_value) AS 'total_cost'";
		}
		
		$sql_columns .= "
		,DATE(shop_order.post_date)						AS post_date
		,pw_woocommerce_order_itemmeta7.meta_value			AS product_id
		,pw_woocommerce_order_items.order_item_id 			AS order_item_id";

		if($pw_show_variation == 'variable') {
		
			$sql_columns .= ", pw_woocommerce_order_itemmeta8.meta_value AS 'variation_id'";	
							
			if($pw_sort_by == "sku")
				$sql_columns .= ", IF(pw_postmeta_sku.meta_value IS NULL or pw_postmeta_sku.meta_value = '', IF(pw_postmeta_product_sku.meta_value IS NULL or pw_postmeta_product_sku.meta_value = '', '', pw_postmeta_product_sku.meta_value), pw_postmeta_sku.meta_value) as pw_sku ";
				
			}else{
			if($pw_sort_by == "sku")
				$sql_columns .= ", IF(pw_postmeta_product_sku.meta_value IS NULL or pw_postmeta_product_sku.meta_value = '', '', pw_postmeta_product_sku.meta_value) as pw_sku";
				
		}
		
		
		if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
			$sql_columns .= " , pw_woocommerce_order_itemmeta_variation.meta_key AS variation_key";
			$sql_columns .= " , pw_woocommerce_order_itemmeta_variation.meta_value AS variation_value";
		}		
		
		
		$sql_joins =  "
			{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id	= pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id= pw_woocommerce_order_items.order_item_id";
			
			//COST OF GOOD
			if($pw_show_cog=='yes'){
				$sql_joins .=	"	
				LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta22 ON pw_woocommerce_order_itemmeta22.order_item_id=pw_woocommerce_order_items.order_item_id ";
			}
			
			$sql_joins .=	"
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id= pw_woocommerce_order_items.order_item_id";
					
		
		
		if($category_id  && $category_id != "-1") {
			$category_id_join= " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if($pw_id_order_status  && $pw_id_order_status != "-1") {
			$pw_id_order_status_join= " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
				LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
		
		$sql_joins.=$category_id_join.$pw_id_order_status_join;
		
		if($pw_show_variation == 'variable'){
			$sql_joins .= " 
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta8 ON pw_woocommerce_order_itemmeta8.order_item_id = pw_woocommerce_order_items.order_item_id
					";
			if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1') || $pw_variation_sku != '-1')
				$sql_joins .= "	LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_sku 		ON pw_postmeta_sku.post_id		= pw_woocommerce_order_itemmeta8.meta_value";
					
			if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
				$pw_variation_item_meta_key_join= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_variation ON pw_woocommerce_order_itemmeta_variation.order_item_id= pw_woocommerce_order_items.order_item_id";
			}
			
			$sql_variation_join='';
			if(isset($this->search_form_fields['pw_new_value_variations']) and count($this->search_form_fields['pw_new_value_variations'])>0){
				foreach($this->search_form_fields['pw_new_value_variations'] as $key => $value){
					$new_v_key = "wcvf_".$this->pw_woo_filter_chars($key);
					$sql_variation_join= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_{$new_v_key} ON woocommerce_order_itemmeta_{$new_v_key}.order_item_id = pw_woocommerce_order_items.order_item_id";
				}
			}
			
		}
		
		$sql_joins.=$pw_variation_item_meta_key_join.$sql_variation_join;
		
		if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1'))
			$sql_joins .= "	LEFT JOIN  {$wpdb->prefix}postmeta		 as pw_postmeta_product_sku 		ON pw_postmeta_product_sku.post_id 			= pw_woocommerce_order_itemmeta7.meta_value	";				
		
		$sql_joins .= " LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id";//For shop_order
		
		if($pw_show_variation == 2 || ($pw_show_variation == 'grouped' || $pw_show_variation == 'external' || $pw_show_variation == 'simple' || $pw_show_variation == 'variable_')){
			$pw_show_variation_join= " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_type 	ON pw_term_relationships_product_type.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_type 		ON pw_term_taxonomy_product_type.term_taxonomy_id		=	pw_term_relationships_product_type.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_type 				ON pw_terms_product_type.term_id						=	pw_term_taxonomy_product_type.term_id";
		}
		
		if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'"){
			$pw_paid_customer_join= " 
				LEFT JOIN  {$wpdb->prefix}postmeta 			as pw_postmeta_billing_email				ON pw_postmeta_billing_email.post_id=pw_woocommerce_order_items.order_id";
		}
		
		if($pw_billing_post_code and $pw_billing_post_code != '-1'){
			$pw_billing_post_code_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_postcode ON pw_postmeta_billing_postcode.post_id	=	pw_woocommerce_order_items.order_id";
		}
		
		$sql_joins.=$pw_show_variation_join.$pw_paid_customer_join.$pw_billing_post_code_join;
					
		$sql_condition= "
			woocommerce_order_itemmeta.meta_key	= '_qty'
			AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' ";
			
			//COST OF GOOD
			if($pw_show_cog=='yes'){	
				$sql_condition .="
				AND pw_woocommerce_order_itemmeta22.meta_key	= '".__PW_COG_TOTAL__."' ";
			}
			
			$sql_condition .="
			AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'						
			AND shop_order.post_type					= 'shop_order'
			";
					
		if($pw_show_variation == 'variable'){
			$sql_condition.= "
					AND pw_woocommerce_order_itemmeta8.meta_key = '_variation_id' 
					AND (pw_woocommerce_order_itemmeta8.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta8.meta_value > 0)
					";
			
			if(($pw_sort_by == "sku") || ($pw_variation_sku and $pw_variation_sku != '-1'))
				$sql_condition .=	" AND pw_postmeta_sku.meta_key	= '_sku'";
			
			
			
			if(($pw_variation_item_meta_key != "-1" and strlen($pw_variation_item_meta_key)>1)){
				$pw_variation_item_meta_key_condition= " AND pw_woocommerce_order_itemmeta_variation.meta_key IN ('{$pw_variation_item_meta_key}')";
			}
			
			$sql_variation_condition='';
			if(isset($this->search_form_fields['pw_new_value_variations']) and count($this->search_form_fields['pw_new_value_variations'])>0){
				foreach($this->search_form_fields['pw_new_value_variations'] as $key => $value){
					$new_v_key = "wcvf_".$this->pw_woo_filter_chars($key);
					$key = str_replace("'","",$key);
					$sql .= " AND woocommerce_order_itemmeta_{$new_v_key}.meta_key = '{$key}'";
					$vv = is_array($value) ? implode(",",$value) : $value;
					//$vv = str_replace("','",",",$vv);
					$vv = str_replace(",","','",$vv);
					$sql_variation_condition= " AND woocommerce_order_itemmeta_{$new_v_key}.meta_value IN ('{$vv}') ";
				}
			}
		}
		
		$sql_condition.=$pw_variation_item_meta_key_condition.$sql_variation_condition;
		
		if(($pw_sort_by == "sku") || ($pw_product_sku and $pw_product_sku != '-1'))
			$sql_condition .= " AND pw_postmeta_product_sku.meta_key			= '_sku'";
		
		if($pw_show_variation == 'variable'){
			
			if(($pw_product_sku and $pw_product_sku != '-1') and ($pw_variation_sku and $pw_variation_sku != '-1')){
				$product_variation_sku_condition= " AND (pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.") AND pw_postmeta_sku.meta_value IN (".$pw_variation_sku."))";
			}else if ($pw_variation_sku and $pw_variation_sku != '-1'){
				$pw_variation_sku_condition= " AND pw_postmeta_sku.meta_value IN (".$pw_variation_sku.")";
			}else{
				if($pw_product_sku and $pw_product_sku != '-1') 
					$pw_product_sku_condition= " AND pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.")";
			}
			
		}else{
			
			if($pw_product_sku and $pw_product_sku != '-1') 
				$pw_product_sku_condition= " AND pw_postmeta_product_sku.meta_value IN (".$pw_product_sku.")";
			
		}
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition= " 
					AND (DATE(shop_order.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
		}
		
		if($pw_product_id  && $pw_product_id != "-1") 
			$pw_product_id_condition= "
					AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_product_id .")";	
		
		if($category_id  && $category_id != "-1") 
			$category_id_condition= "
					AND pw_terms.term_id IN (".$category_id .")";	
		
		if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1") 
			$pw_cat_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_cat_prod_id_string .")";
		
		if($pw_id_order_status  && $pw_id_order_status != "-1") 
			$pw_id_order_status_condition= " 
					AND terms2.term_id IN (".$pw_id_order_status .")";
		
		
		$sql_condition.=$product_variation_sku_condition.$pw_variation_sku_condition.$pw_product_sku_condition.$pw_from_date_condition.$pw_product_id_condition.$category_id_condition.$pw_cat_prod_id_string_condition.$pw_id_order_status_condition;
		
		
		if($pw_show_variation == 'grouped' || $pw_show_variation == 'external' || $pw_show_variation == 'simple' || $pw_show_variation == 'variable_'){
			$sql_condition .= " AND pw_terms_product_type.name IN ('{$pw_show_variation}')";
		}
		
		
		
		if($pw_show_variation == 2){
			$sql_condition .= " AND pw_terms_product_type.name IN ('simple')";
		}
		
		if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'"){
			$pw_paid_customer_condition= " AND pw_postmeta_billing_email.meta_key='_billing_email'";
			$pw_paid_customer_condition .= " AND pw_postmeta_billing_email.meta_value IN (".$pw_paid_customer.")";
		}
		
		if($pw_billing_post_code and $pw_billing_post_code != '-1'){
			$pw_billing_post_code_condition= " AND pw_postmeta_billing_postcode.meta_key='_billing_postcode' AND pw_postmeta_billing_postcode.meta_value IN ({$pw_billing_post_code}) ";
		}
		
		
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND shop_order.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")			
			$pw_hide_os_condition= " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
		
		$sql_condition.=$pw_paid_customer_condition.$pw_billing_post_code_condition.$pw_order_status_condition.$pw_hide_os_condition;
		
		$sql_group_by='';
		if($pw_show_variation == 'variable'){
			switch ($group_by) {
				case "variation_id":
					$sql_group_by= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
					break;
				case "order_item_id":
					$sql_group_by= " GROUP BY pw_woocommerce_order_items.order_item_id ";
					break;
				default:
					$sql_group_by= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
					break;
				
			}
			//$sql .= " GROUP BY pw_woocommerce_order_itemmeta8.meta_value ";
		}else{
			$sql_group_by= " 
					GROUP BY  pw_woocommerce_order_itemmeta7.meta_value";
		}
		
		$sql_order_by='';
		switch ($pw_sort_by) {
			case "sku":
				$sql_order_by= " ORDER BY sku " .$pw_order_by;
				break;
			case "product_name":
				$sql_order_by= " ORDER BY product_name " .$pw_order_by;
				break;
			case "ProductID":
				$sql_order_by= " ORDER BY CAST(product_id AS DECIMAL(10,2)) " .$pw_order_by;
				break;
			case "amount":
				$sql_order_by= " ORDER BY amount " .$pw_order_by;
				break;
			case "variation_id":
				if($pw_show_variation == 'variable'){
					$sql_order_by= " ORDER BY CAST(variation_id AS DECIMAL(10,2)) " .$pw_order_by;
				}
				break;
			default:
				$sql_order_by= " ORDER BY amount DESC";
				break;
		}				
		
		$sql="SELECT $sql_columns FROM $sql_joins WHERE $sql_condition $sql_group_by $sql_order_by";
		
		///////////////////
		//VARIATIONS COLUMNS
		$this->table_cols =$this->table_columns($table_name);
		if($pw_show_variation=='variable')
		{
			$array_index=3;
			$data_variation='';
			$variation_cols_arr='';
			$attributes_available=$this->pw_get_woo_pv_atts('yes');
			$pw_variation_attributes 	= $this->pw_get_woo_requests('pw_variations',NULL,true);
			
			$pw_variation_column	= $this->pw_get_woo_requests('pw_variation_cols','1',true);
			if($pw_variation_column=='1')
			{
				$variation_sel_arr	= '';
				if($pw_variation_attributes != NULL  && $pw_variation_attributes != '-1')
				{
					$pw_variation_attributes = str_replace("','", ",",$pw_variation_attributes);
					$variation_sel_arr = explode(",",$pw_variation_attributes);
				}
				
				foreach($attributes_available as $key => $value){
					$new_key = str_replace("wcv_","",$key);
					if($pw_variation_attributes=='-1' || is_array($variation_sel_arr) && in_array($new_key,$variation_sel_arr))
					{
						$data_variation[]=$new_key;
						$variation_cols_arr[] = array('lable'=>$value,'status'=>'show');
					}
				}
			}else
			{
				$variation_cols_arr[]=array('lable'=>'variation','status'=>'show');
			}
			
			
			$head = array_slice($this->table_cols, 0, $array_index);
	
			$tail = array_slice($this->table_cols, $array_index);
	
			$this->table_cols = array_merge($head, $variation_cols_arr, $tail);
			
			$this->data_variation=$data_variation;
		}
		
		//CHECK IF COST OF GOOD IS ENABLE
		if($pw_show_cog!='yes'){
			unset($this->table_cols[count($this->table_cols)-1]);
			unset($this->table_cols[count($this->table_cols)-1]);
		}
				
		//echo $sql;
		
	}elseif($file_used=="data_table"){
		
		$pw_show_variation		= $this->pw_get_woo_requests('pw_show_adr_variaton','variable',true);
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
													
				//Product ID
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->product_id;
				$datatable_value.=("</td>");
				
				//Product SKU
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id/*,"wcx_wcreport_plugin_variation",$pw_show_variation*/);
				$datatable_value.=("</td>");
				
				//Product Name
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->product_name;
				$datatable_value.=("</td>");
			
				$j=3;
				//VARIATION COLUMNS
				$pw_show_variation	= $this->pw_get_woo_requests('pw_show_adr_variaton','simple',true);
				if($pw_show_variation=='variable')
				{
					
					$pw_variation_column	= $this->pw_get_woo_requests('pw_variation_cols','1',true);
					if($pw_variation_column=='1')
					{
						$variation_arr='';
						$variation = $this->pw_get_product_var_col_separated($items->order_item_id);
						foreach($variation as $key => $value){
							$variation_arr[$key]=$value;
						}
						
						
						foreach($this->data_variation as $variation_name){
							$pw_table_value=(!empty($variation_arr[$variation_name]) ? $variation_arr[$variation_name] : "-");
							$display_class='';
							if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
							$datatable_value.=("<td style='".$display_class."'>");
								$datatable_value.= $pw_table_value; //$this->pw_get_woo_variation($items->order_item_id);
							$datatable_value.=("</td>");	
						}
					}
					else{
						$variation_arr='';
						$variation = $this->pw_get_product_var_col_separated($items->order_item_id);
						foreach($variation as $key => $value){
							$variation_arr[]=$value;
						}
						
						$display_class='';
						if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.=implode(',',$variation_arr); //$this->pw_get_woo_variation($items->order_item_id);
						$datatable_value.=("</td>");
					}
				}
				
				//Sales Qty.
				$display_class='';
				if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->quantity;
				$datatable_value.=("</td>");
				
				//Current Stock
				$display_class='';
				if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_prod_stock_($items->order_item_id, $items->product_id);
				$datatable_value.=("</td>");
				
				//Amount
				$display_class='';
				if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->amount);
				$datatable_value.=("</td>");
				
				//COST OF GOOD
				$pw_show_cog= $this->pw_get_woo_requests('pw_show_cog',"no",true);	
				if($pw_show_cog=='yes'){
					$display_class='';
					/*$cog=get_post_meta($items->product_id,__PW_COG__,true);
					$cog*=$items->quantity;*/
					if($this->table_cols[5]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						//$datatable_value.= $cog == 0 ? 0 : $this->price($cog);
						$datatable_value.= $items->total_cost == 0 ? 0 : $this->price($items->total_cost);
					$datatable_value.=("</td>");
					
					if($this->table_cols[5]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						//$datatable_value.= $cog == 0 ? 0 : $this->price($cog);
						$datatable_value.= ($items->amount-$items->total_cost) == 0 ? 0 : $this->price($items->amount-$items->total_cost);
					$datatable_value.=("</td>");
				}
				
				
				//Edit
				/*$display_class='';
				if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Edit';
				$datatable_value.=("</td>");*/
				
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
					$permission_value=$this->get_form_element_value_permission('pw_category_id');
					if($this->get_form_element_permission('pw_category_id') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
							$col_style='display:none';
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
                        
                        $categories = get_terms('product_cat',$args);
                        $option='';
                        foreach ($categories as $category) {
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_category_id') &&  $permission_value!='')
								$selected="selected";
                            
                            if($current_category==$category->term_id)
                                $selected="selected";*/
                            
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
				?>
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
                        //$product_data = $this->pw_get_product_woo_data('variable');
                        $products=$this->pw_get_product_woo_data('simple');
                        $option='';
                        $current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        
                        //echo $current_product;
                        
                        foreach($products as $product){
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;
							
                            $selected='';
                            if($current_product==$product->id)
                                $selected="selected";
                            $option.="<option $selected value='".$product -> id."' >".$product -> label." </option>";
                        }
                        
                        
                    ?>
                    <select id="pw_adr_product" name="pw_product_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
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
                
                <div class="col-md-6" >
                    <div class="awr-form-title">
                        <?php _e('Postcode(zip)',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map-marker"></i></span>
                    <input name="pw_bill_post_code" type="text" class="postcode"/>
                </div>
                
                <?php
                	$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_orders_status');
					if($this->get_form_element_permission('pw_orders_status')||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_orders_status') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map"></i></span>
					<?php
                        $pw_order_status=$this->pw_get_woo_orders_statuses();

                        $option='';
                        foreach($pw_order_status as $key => $value){
							$selected="";
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
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Variations',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-bolt"></i></span>
					<?php
                        $option='';
                        $pw_variations=$this->pw_get_woo_pv_atts('yes');

            
                        foreach($pw_variations as $key=>$value){
							$selected='';
                            $new_key = str_replace("wcv_","",$key);
                            $option.="<option value='".$new_key."' >".$value." </option>";
                        }
                    ?>
                
                    <select name="pw_variations[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search variation_elements">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                </div>
                
                <?php
                	echo $this->pw_get_woo_var_dropdowns();
				?>
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Show ',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-eye"></i></span>
					<?php
                        
                        $products=array(
                            "all" => $this->pw_get_product_woo_data('0'),
                            "simple" => $this->pw_get_product_woo_data('simple'),
                            "variable" => $this->pw_get_product_woo_data('1'),
                        );
                        
                        $json_products = json_encode($products);
                        //print_r($json_products);
                    ?>
                    <select id="pw_adr_show_variation" name="pw_show_adr_variaton">
                        <option value="variable" selected>Variation Products</option>
                        <option value="simple" >Simple Products</option>
                        <option value="-1">All Products</option>
                    </select>
                    
                    <script type="text/javascript">
						"use strict";
						jQuery( document ).ready(function( $ ) {
							
							var products='';
							products=<?php echo $json_products?>;	

							$("#pw_adr_show_variation").change(function(){
								var show_val=$(this).val();
								
								if(show_val=='variable')
								{
									$(".variation_elements").each(function(){
										$(this).prop("disabled", false);
										$(this).trigger("chosen:updated");
									});
								}else{
									$(".variation_elements").each(function(){
										$(this).prop("disabled", true);
										$(this).trigger("chosen:updated");
									});
								}
								
								var datas='';
								if(show_val=="-1")
								{
									datas=products.all;	
								}else if(show_val=="simple"){
									datas=products.simple;
								}else{
									datas=products.variable;
								}
								
								var option_data = Array();
								
								var options = '<option value="-1">Select All</option>';
								var i = 1;
								$.each(datas, function(key,val){

									options += '<option value="' + val.id + '">' + val.label + '</option>';
									option_data[val.id] = val.label;
									i++;
								});

								//$("#pw_adr_product").html(options);
								$('#pw_adr_product').empty(); //remove all child nodes
								$("#pw_adr_product").html(options);
								$('#pw_adr_product').trigger("chosen:updated");
							});
							
							$("#pw_adr_show_variation").trigger("change");
							
						});
						
					</script>
                    	
                </div>
                
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Style',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-paint-brush"></i></span>
                    <select name="pw_variation_cols[]" id="pw_variation_cols" class="pw_variation_cols variation_elements" disabled>
                        <option value="1" selected="selected">Columner</option>
                        <option value="0">Comma Separated</option>
                    </select>
                </div>
                
                <?php
                	$pw_product_sku_data = $this->pw_get_woo_prod_sku();
					if($pw_product_sku_data){
				?>
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Product SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
                        $option='';
                        foreach($pw_product_sku_data as $sku){
                            $option.="<option value='".$sku->id."' >".$sku->label."</option>";
                        }
                    ?>
                
                    <select name="pw_sku_products[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
                <?php
					}
					$pw_variation_sku_data = $this->pw_get_woo_var_sku();									
					if($pw_variation_sku_data){
				?>
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Variation SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-bolt"></i></span>
					<?php
                        $option='';
                        foreach($pw_variation_sku_data as $sku){
                            $option.="<option value='".$sku->id."' >".$sku->label."</option>";
                        }
                    ?>
                    <select name="pw_sku_variations[]" class="variation_elements chosen-select-search" multiple="multiple" size="5"  data-size="5">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
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
						<?php _e('Order By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-sort-alpha-asc"></i></span>
                    <div class="row">
						<div class="col-md-5">
							<select name="sort_by" id="sort_by" class="sort_by"><option value="product_name">Product Name</option><option value="ProductID">Product ID</option><option value="variation_id">Variation ID</option><option value="amount">Amount</option></select>
						</div>	
						<div class="col-md-7 ">
							<select name="order_by" id="order_by" class="order_by">
								<option value="ASC">Ascending</option>
								<option value="DESC" selected="selected">Descending</option>
							</select>
						</div>
					</div>
                </div>	
                
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Group By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-suitcase"></i></span>
                    <select name="pw_groupby" id="variation_group_by" class="group_by variation_elements" disabled>
                        <option value="variation_id">Variation ID</option>
                        <option value="order_item_id">Order Item ID</option>
                    </select>
                </div>	
                
                <?php
            	if(__PW_COG__!=''){
				?>
				
					<div class="col-md-6">
						<div class="awr-form-title">
							<?php _e('Show Cog & Profit',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
						</div>
						
						<input name="pw_show_cog" type="checkbox" value="yes"/>
						
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