<?php
	
	if($file_used=="sql_table")
	{
		$pw_product_id		= $this->pw_get_woo_requests('pw_product_id','-1',true);
		$category_id	= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_product_subtype= $this->pw_get_woo_requests('pw_sub_product_type','-1',true);
		$pw_sku_number= $this->pw_get_woo_requests('pw_sku_no','-1',true);
		$pw_product_sku= $this->pw_get_woo_requests('pw_sku_products','-1',true);
		$pw_manage_stock= $this->pw_get_woo_requests('pw_stock_manage','-1',true);
		$pw_stock_status= $this->pw_get_woo_requests('pw_status_of_stock','-1',true);
		$pw_product_stock = $this->pw_get_woo_requests('pw_stock_product','-1',true);
		$pw_txt_min_stock = $this->pw_get_woo_requests('pw_stock_min','-1',true);
		$pw_txt_max_stock  = $this->pw_get_woo_requests('pw_stock_max','-1',true);
		
		$pw_zero_stock = $this->pw_get_woo_requests('pw_stock_zero','no',true);
		$pw_product_type= $this->pw_get_woo_requests('pw_products_type','-1',true);
		$pw_zero_sold = $this->pw_get_woo_requests('zero_sold','-1',true);
		$pw_product_name = $this->pw_get_woo_requests('pw_name_of_product','-1',true);
		$pw_basic_column = $this->pw_get_woo_requests('pw_general_cols','no',true);
		$pw_zero_stock = $this->pw_get_woo_requests('pw_stock_zero','no',true);
		
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);

		$pw_variations			= $this->pw_get_woo_requests('pw_variations','-1',true);
		$pw_variation_sku 		= $this->pw_get_woo_requests('pw_sku_variations','-1',true);	
		if($pw_variation_sku != NULL  && $pw_variation_sku != '-1'){
			$pw_variation_sku  		= "'".str_replace(",","','",$pw_variation_sku)."'";
		}


		$pw_product_sku		= $this->pw_get_woo_sm_requests('pw_sku_products',$pw_product_sku, "-1");
		
		$pw_manage_stock		= $this->pw_get_woo_sm_requests('pw_stock_manage',$pw_manage_stock, "-1");
		
		$pw_stock_status		= $this->pw_get_woo_sm_requests('pw_status_of_stock',$pw_stock_status, "-1");
		
		
			
		//GET POSTED PARAMETERS
			
		$pw_product_sku 		= $this->pw_get_woo_requests('pw_sku_products','-1',true);	
		if($pw_product_sku != NULL  && $pw_product_sku != '-1'){
			$pw_product_sku  		= "'".str_replace(",","','",$pw_product_sku)."'";
		}
		
		
		$page				= $this->pw_get_woo_requests('page',NULL);	
		
		$report_name 		= apply_filters($page.'_default_report_name', 'product_page');
		$optionsid			= "per_row_variation_page";

		$report_name 		= $this->pw_get_woo_requests('report_name',$report_name,true);
		$admin_page			= $this->pw_get_woo_requests('admin_page',$page,true);
		$category_id		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		$pw_hide_os='"trash"';
		$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id','-1',true);
		
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		if($pw_variations && $pw_variations!='-1')
		{
			$pw_variations = explode(",",$pw_variations);
			//$this->print_array($pw_variations);
			$var = array();
			foreach($pw_variations as $key => $value):
				$var[] .=  "attribute_pa_".$value;
				$var[] .=  "attribute_".$value;	
			endforeach;
			$pw_variations =  implode("', '",$var);
		}
		
		//$pw_item_meta_key =  implode("', '",$item_att);
		//print_r($pw_variations);
		$pw_variation_attributes='';
		if($pw_variations && $pw_variations!='-1')
			$pw_variation_attributes= $pw_variations;
		
		
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////	
		
		
		//PRODUCT SUBTYPE
		$pw_product_subtype_join='';
		$pw_product_subtype_condition_1='';
		$pw_product_subtype_condition_2='';
		
		//SKU NUMBER
		$pw_sku_number_join='';
		$pw_sku_number_condition_1='';
		$pw_sku_number_condition_2='';
		
		//PRODUCT NAME
		$pw_product_name_condition='';
		
		//PRODUCT ID
		$pw_product_id_condition='';
		
		//PRODUCT SKU
		$pw_product_sku_join='';
		$pw_product_sku_condition_1='';
		$pw_product_sku_condition_2='';
		
		//VARIATION SKU
		$pw_variation_sku_join='';
		$pw_variation_sku_condition_1='';
		$pw_variation_sku_condition_2='';
		
		//PRODUCT STOCK
		$pw_product_stock_join='';
		$pw_product_stock_condition_1='';
		$pw_product_stock_condition_2='';

		//CATEGORY ID
		$category_id_join='';
		$category_id_condition='';
		
		//STOCK STATUS
		$pw_stock_status_join='';
		$pw_stock_status_condition='';
		
		//MANAGE STOCK
		$pw_manage_stock_join='';
		$pw_manage_stock_condition='';
		
		//VARIATION ATTRIBUTE
		$pw_variation_attributes_join='';
		$pw_variation_attributes_condition='';
		
		//MAX,MIN,ZERO STOCK
		$pw_txt_min_stock_condition='';
		$pw_txt_max_stock_condition ='';
		$pw_zero_stock_condition='';
		
		//SOLD
		$sold_variation_ids_condition='';
		
		
		$sql_columns = " 
		pw_posts.ID as id
		,pw_posts.post_title as variation_name						
		,pw_posts.ID as variation_id
		,pw_posts.post_date as product_date
		,pw_posts.post_modified as modified_date												
		,pw_products.ID as product_id
		,pw_products.post_title as product_name
		,pw_posts.post_parent AS variation_parent_id";
						
		$sql_joins = " {$wpdb->prefix}posts as pw_posts LEFT JOIN {$wpdb->prefix}posts as pw_products ON pw_products.ID = pw_posts.post_parent";
		
		if($pw_product_subtype=="virtual") 						
			$pw_product_subtype_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_virtual 			ON pw_virtual.post_id			=pw_posts.ID";
		
		if($pw_product_subtype=="downloadable") 					
			$pw_product_subtype_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_downloadable		ON pw_downloadable.post_id		=pw_posts.ID";
		
		if($pw_sku_number){
			$pw_sku_number_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_product_sku 				ON pw_product_sku.post_id					=	pw_posts.post_parent";
			$pw_sku_number_join .= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_variation_sku 				ON pw_variation_sku.post_id				=	pw_posts.ID";
		}else{
			if($pw_product_sku and $pw_product_sku != '-1'){
				$pw_product_sku_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_product_sku 				ON pw_product_sku.post_id				=	pw_posts.post_parent";
			}
			
			if($pw_variation_sku and $pw_variation_sku != '-1'){
				$pw_variation_sku_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_variation_sku 				ON pw_variation_sku.post_id			=	pw_posts.ID";
			}
		}
		
		if($pw_product_stock || $pw_txt_min_stock || $pw_txt_max_stock || strlen($pw_product_stock) >0 || $pw_zero_stock) 		$pw_product_stock_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_stock 				ON pw_stock.post_id			=pw_posts.ID";
		
		if($category_id and $category_id != "-1"){
			$category_id_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 		as pw_term_relationships 	ON pw_term_relationships.object_id			=pw_posts.post_parent
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 			as term_taxonomy 		ON term_taxonomy.term_taxonomy_id		=pw_term_relationships.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 					as pw_terms 				ON pw_terms.term_id						=term_taxonomy.term_id";
		}
		
		if($pw_stock_status and $pw_stock_status != '-1') 
			$pw_stock_status_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_stock_status 			ON pw_stock_status.post_id			=pw_posts.ID";
			
		if($pw_manage_stock and $pw_manage_stock != '-1') 
			$pw_manage_stock_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_manage_stock 			ON pw_manage_stock.post_id			=pw_posts.ID";
		
		if($pw_variation_attributes != "-1" and strlen($pw_variation_attributes)>2)
			$pw_variation_attributes_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_variation ON pw_postmeta_variation.post_id = pw_posts.ID";
		
		$sql_condition= "pw_posts.post_type='product_variation' AND pw_posts.post_status = 'publish' AND pw_products.post_type='product' AND pw_posts.post_parent > 0";
		
		if($pw_product_stock || $pw_txt_min_stock || $pw_txt_max_stock || strlen($pw_product_stock) >0 || $pw_zero_stock == "no") 		$pw_product_stock_condition_1 = " AND pw_stock.meta_key ='_stock'";
		
		if($pw_sku_number){
			$pw_sku_number_condition_1 = " AND pw_product_sku.meta_key ='_sku' AND pw_variation_sku.meta_key ='_sku'";
		}else{
			if($pw_product_sku and $pw_product_sku != '-1'){
				$pw_product_sku_condition_1 = " AND pw_product_sku.meta_key ='_sku'";
			}
			
			if($pw_variation_sku and $pw_variation_sku != '-1'){
				$pw_variation_sku_condition_1 = " AND pw_variation_sku.meta_key ='_sku'";
			}
		}
		
		if($pw_product_subtype=="downloadable") 		
			$pw_product_subtype_condition_1 = " AND pw_downloadable.meta_key ='_downloadable'";					
		
		if($pw_product_subtype=="virtual") 			
			$pw_product_subtype_condition_1 = " AND pw_virtual.meta_key ='_virtual'";					
		
		if($pw_product_name) 							
			$pw_product_name_condition = " AND pw_posts.post_title like '%{$pw_product_name}%'";
		
		if($pw_product_id and $pw_product_id >0) 			
			$pw_product_id_condition = " AND pw_posts.post_parent IN ({$pw_product_id})";
		
		if(strlen($pw_product_stock) >0){
			if($pw_product_stock == 0){
				$pw_product_stock_condition_2 = " AND pw_stock.meta_value = '{$pw_product_stock}'";
			}elseif($pw_product_stock >= 1){
				$pw_product_stock_condition_2 = " AND pw_stock.meta_value = '{$pw_product_stock}'";
			}
		}
		
		if($pw_txt_min_stock) 							
			$pw_txt_min_stock_condition = " AND pw_stock.meta_value >= {$pw_txt_min_stock}";
			
		if($pw_txt_max_stock) 							
			$pw_txt_max_stock_condition = " AND pw_stock.meta_value <= {$pw_txt_max_stock}";									
			
		if($pw_product_subtype=="downloadable") 		
			$pw_product_subtype_condition_2 = " AND pw_downloadable.meta_value = 'yes'";					
			
		if($pw_product_subtype=="virtual") 			
			$pw_product_subtype_condition_2= " AND pw_virtual.meta_value = 'yes'";					
			
		if($category_id and $category_id != "-1") 	
			$category_id_condition = " AND pw_terms.term_id = {$category_id}";
		
		
		
		if($pw_sku_number){
			$pw_sku_number_condition_2 = " AND (  ( pw_product_sku.meta_value like '%{$pw_sku_number}%'  OR pw_variation_sku.meta_value like '%{$pw_sku_number}%' )";
			
			if(($pw_product_sku and $pw_product_sku != '-1') and ($pw_variation_sku and $pw_variation_sku != '-1')){
				$pw_product_sku_condition_2= " AND ( pw_product_sku.meta_value IN ($pw_product_sku) AND pw_variation_sku.meta_value IN ($pw_variation_sku) )";
			}else{
				
				if($pw_product_sku and $pw_product_sku != '-1'){
					$pw_product_sku_condition_2 = " AND pw_product_sku.meta_value IN ($pw_product_sku)";
				}
				
				if($pw_variation_sku and $pw_variation_sku != '-1'){
					$pw_variation_sku_condition_2 = " AND pw_variation_sku.meta_value IN ($pw_variation_sku)";
				}
			}
			
			
			$pw_sku_number_condition_2 .= $pw_product_sku_condition_2.$pw_variation_sku_condition_2. " )";
			
		}else{
			
			if(($pw_product_sku and $pw_product_sku != '-1') and ($pw_variation_sku and $pw_variation_sku != '-1')){
				$pw_product_sku_condition_2= " AND ( pw_product_sku.meta_value IN ($pw_product_sku) AND pw_variation_sku.meta_value IN ($pw_variation_sku) )";
			}else{
				if($pw_product_sku and $pw_product_sku != '-1'){
					$pw_product_sku_condition_2 = " AND pw_product_sku.meta_value IN ($pw_product_sku)";
				}
				
				if($pw_variation_sku and $pw_variation_sku != '-1'){
					$pw_variation_sku_condition_2 = " AND pw_variation_sku.meta_value IN ($pw_variation_sku)";
				}
			}
			
			$pw_sku_number_condition_2 .= $pw_product_sku_condition_2.$pw_variation_sku_condition_2;
		}
		
		
		if($pw_zero_stock == "no")	
			$pw_zero_stock_condition = " AND (pw_stock.meta_value > 0 OR LENGTH(TRIM(pw_stock.meta_value)) > 0)";
	
		if($pw_stock_status and $pw_stock_status != '-1')		
			$pw_stock_status_condition = " AND pw_stock_status.meta_key ='_stock_status' AND pw_stock_status.meta_value IN ({$pw_stock_status})";
			
		if($pw_manage_stock and $pw_manage_stock != '-1')		
			$pw_manage_stock_condition = " AND pw_manage_stock.meta_key ='_manage_stock' AND pw_manage_stock.meta_value IN ({$pw_manage_stock})";
		
		if($pw_zero_sold=="yes"){
			if(strlen($sold_variation_ids)>0){
				$sold_variation_ids_condition = " AND pw_posts.ID NOT IN ($sold_variation_ids)";
			}
		}
		
		if($pw_variation_attributes != "-1" and strlen($pw_variation_attributes)>2){
			$pw_variation_attributes_condition = " AND pw_postmeta_variation.meta_key IN ('{$pw_variation_attributes}')";
		}
		
		$sql_group_by = " GROUP BY pw_posts.ID";
		
		$sql_order_by = " ORDER BY pw_posts.post_parent ASC, pw_posts.post_title ASC";
		
		
		$sql="SELECT $sql_columns 
			FROM 
			$sql_joins $pw_product_subtype_join $pw_sku_number_join $pw_product_sku_join
			$pw_variation_sku_join $pw_product_stock_join $category_id_join $pw_stock_status_join
			$pw_manage_stock_join $pw_variation_attributes_join 
			WHERE $sql_condition
			$pw_product_stock_condition_1 $pw_sku_number_condition_1 $pw_product_sku_condition_1
			$pw_variation_sku_condition_1 $pw_product_subtype_condition_1 $pw_product_name_condition
			$pw_product_id_condition $pw_product_stock_condition_2 $pw_txt_min_stock_condition
			$pw_txt_max_stock_condition $pw_product_subtype_condition_2 $category_id_condition
			$pw_sku_number_condition_2 $pw_zero_stock_condition $pw_stock_status_condition
			$pw_manage_stock_condition $sold_variation_ids_condition $pw_variation_attributes_condition
			$sql_group_by $sql_order_by";
	
		//echo $sql;
		
		///////////////////
		//EXTRA COLUMNS
		$this->table_cols =$this->table_columns($table_name);
		
		$variation_cols_arr='';
		if($pw_basic_column=='yes'){
			$variation_cols_arr[] = array('lable'=>'Variation ID','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'SKU','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Variation SKU','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Product Name','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Category','status'=>'show');
			
			//variation columns
			$data_variation='';
			
			$attributes_available=$this->pw_get_woo_pv_atts('yes');
			$pw_variation_attributes 	= $this->pw_get_woo_requests('pw_variations',NULL,true);
			
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
			$this->data_variation=$data_variation;
			
			$variation_cols_arr[] = array('lable'=>'Stock','status'=>'show');
			//$variation_cols_arr[] = array('lable'=>'Edit','status'=>'show');
			
		}else if($pw_basic_column!='yes'){
			
			$variation_cols_arr[] = array('lable'=>'Variation ID','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'SKU','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Variation SKU','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Product Name','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Category','status'=>'show');
			
			$variation_cols_arr[] = array('lable'=>'Created Date','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Modified Date','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Downloadable','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Virtual','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Manage Stock','status'=>'show');
			$variation_cols_arr[] = array('lable'=>'Backorders','status'=>'show');
			
			$variation_cols_arr[] = array('lable'=>'Regular Price','status'=>'currency');
			$variation_cols_arr[] = array('lable'=>'Sale Price','status'=>'currency');
			
			//CHECK IF COG IS ENABLE
			if($pw_show_cog=='yes'){
				$variation_cols_arr[] = array('lable'=>'Cog','status'=>'currency');
			}
			
			//variation columns
			$data_variation='';
			
			$attributes_available=$this->pw_get_woo_pv_atts('yes');
			$pw_variation_attributes 	= $this->pw_get_woo_requests('pw_variations',NULL,true);
			
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
			$this->data_variation=$data_variation;
			
			$variation_cols_arr[] = array('lable'=>'Stock','status'=>'show');
			//$variation_cols_arr[] = array('lable'=>'Edit','status'=>'show');
		}
		
		$this->table_cols = $variation_cols_arr;
		
		
		
	}elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
				$pw_basic_column = $this->pw_get_woo_requests('pw_general_cols','-1',true);
				$product_details=$this->pw_get_full_post_meta($items->variation_id);
				$product_details_prod=$this->pw_get_full_post_meta($items->product_id);
				
				/*if($items->variation_id=='134')
					print_r($product_details_prod);
					
					*/
				
				if($pw_basic_column=='yes'){
					
					//Variation ID
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->variation_id;
					$datatable_value.=("</td>");
					
					//SKU
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $product_details_prod['sku'];
					$datatable_value.=("</td>");
					
					//Variation SKU
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=  $product_details['sku'];
					$datatable_value.=("</td>");
					
					//Product Name
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->product_name;
					$datatable_value.=("</td>");
					
					//Category
					$display_class='';
					if($this->table_cols[4]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
					$datatable_value.=("</td>");
					
					//Variation Columns
					$j=5;
					$variation_arr='';
					$variation = $this->pw_get_woo_prod_var($items->variation_id);
					foreach($variation as $var){
						
						$variation_arr=$var;
					}
					
					
					foreach($this->data_variation as $variation_name){
						$pw_table_value=(!empty($variation_arr[$variation_name]) ? $variation_arr[$variation_name] : "-");
						$display_class='';
						if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= urldecode($pw_table_value); //$this->pw_get_woo_variation($items->order_item_id);
						$datatable_value.=("</td>");	
					}
					
					
					//Stock
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= isset($product_details['stock']) && $product_details['stock']!='' ? round($product_details['stock']) : "0";
					$datatable_value.=("</td>");
					
					//Edit
					/*$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= 'Edit';
					$datatable_value.=("</td>");*/
					
				}else if($pw_basic_column!='yes'){
					
					//Variation ID
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->variation_id;
					$datatable_value.=("</td>");
					
					//SKU
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=  $product_details_prod['sku'];
					$datatable_value.=("</td>");
					
					//Variation SKU
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.=  $product_details['sku'];
					$datatable_value.=("</td>");
					
					//Product Name
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->product_name;
					$datatable_value.=("</td>");
					
					//Category
					$display_class='';
					if($this->table_cols[4]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
					$datatable_value.=("</td>");
					
					//Create Date
					$date_format	= get_option( 'date_format' );
					$display_class='';
					if($this->table_cols[5]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->product_date));
					$datatable_value.=("</td>");
					
					//Modified Date
					$display_class='';
					if($this->table_cols[6]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->modified_date));
					$datatable_value.=("</td>");
					
					//Downloadable
					$downloadable=ucwords($product_details['downloadable']);
					if($downloadable=='')
						$downloadable='NO';
					
					$display_class='';
					if($this->table_cols[7]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $downloadable;
					$datatable_value.=("</td>");
					
					//Virtual
					$virtual=ucwords($product_details['virtual']);
					if($virtual=='')
						$virtual='NO';
						
					$display_class='';
					if($this->table_cols[8]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $virtual;
					$datatable_value.=("</td>");
					
					//Manage Stock
					$pw_manage_stock=isset($product_details['manage_stock']) ? $product_details['manage_stock'] :'';
					$pw_manage_stock=ucwords($pw_manage_stock);
					if($pw_manage_stock=='')
						$pw_manage_stock='NO';
						
					$display_class='';
					if($this->table_cols[9]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $pw_manage_stock;
					$datatable_value.=("</td>");
					
					//Backorders
					$display_class='';
					if($this->table_cols[10]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$backorders=isset($product_details['backorders']) ? $product_details['backorders'] :"";
						$datatable_value.= ((isset($product_details['backorders']) && $product_details['backorders']=='no') || (isset($product_details['backorders']) && $product_details['backorders']=='')) ? "Do not allow" : $backorders;
					$datatable_value.=("</td>");
					
					
					$regular_price='';
					$sale_price='';
					
					$regular_price=$product_details['regular_price'];
					$sale_price=$product_details['sale_price'];
					//$cost_price=$product_details[substr(__PW_COG__,1)];
					$cost_price=get_post_meta($items->variation_id,__PW_COG__,true);
					
					//Regualr Price
					$display_class='';
					if($this->table_cols[11]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($regular_price);
					$datatable_value.=("</td>");
					
					//Sale Price
					$display_class='';
					if($this->table_cols[11]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $this->price($sale_price);
					$datatable_value.=("</td>");
					
					
					//COST Price
					$pw_show_cog= $this->pw_get_woo_requests('pw_show_cog',"no",true);	
					if($pw_show_cog=='yes'){
						$display_class='';
						if($this->table_cols[11]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->price($cost_price);
						$datatable_value.=("</td>");
					}
					
					//Variation Columns
					$j=12;
					$variation_arr='';
					$variation = $this->pw_get_woo_prod_var($items->variation_id);
					foreach($variation as $var){
						
						$variation_arr=$var;
					}
					
					
					foreach($this->data_variation as $variation_name){
						$pw_table_value=(!empty($variation_arr[$variation_name]) ? $variation_arr[$variation_name] : "-");
						$display_class='';
						if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= urldecode($pw_table_value); //$this->pw_get_woo_variation($items->order_item_id);
						$datatable_value.=("</td>");	
					}
					
					
					//Stock
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= (isset($product_details['stock']) && $product_details['stock']!='') ? round($product_details['stock']) : "0";
					$datatable_value.=("</td>");
					
					//Edit
					/*$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= 'Edit';
					$datatable_value.=("</td>");*/
					
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
                        <?php _e('SKU No',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <input name="pw_sku_no" type="text" class="sku_no"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Product Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
                    <input name="pw_name_of_product" type="text" class="pw_name_of_product"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Min Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-battery-0"></i></span>
                    <input name="pw_stock_min" type="text" class="pw_stock_min"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Max Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-battery-4"></i></span>
                    <input name="pw_stock_max" type="text" class="pw_stock_max"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Product Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
                    <input name="pw_stock_product" type="text" class="pw_stock_product"/>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Show all sub-types',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <select name="pw_sub_product_type" id="pw_sub_product_type" >
                        <option value="">Show all sub-types</option>
                        <option value="downloadable">Downloadable</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Stock Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <select name="pw_status_of_stock" id="pw_status_of_stock" class="pw_status_of_stock">
                        <option value="-1">All</option>
                        <option value="instock">In stock</option>
                        <option value="outofstock">Out of stock</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Manage Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-crop"></i></span>
                    <select name="pw_stock_manage" id="pw_stock_manage" class="pw_stock_manage">
                        <option value="-1">All</option>
                        <option value="yes">Include items whose stock is mannaged</option>
                        <option value="no">Include items whose stock is not mannaged</option>
                    </select>
                </div>
                
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
                        //$product_data = $this->pw_get_product_woo_data('variable');
                       // $products=$this->pw_get_product_woo_data('0');
					   $products = $this->pw_get_var_product_dropdown('product','publish');
                        $option='';
                        
                        
                        foreach($products as $product){
                            
                            $option.="<option value='".$product -> id."' >".$product -> label." </option>";
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
                            if($current_category==$category->term_id)
                                $selected="selected";
                            
                            $option .= '<option value="'.$category->term_id.'" '.$selected.'>';
                            $option .= $category->name;
                            $option .= ' ('.$category->count.')';
                            $option .= '</option>';
                        }
						
						
						$categories = $this->pw_get_var_category_dropdown('product_cat','no',false);
						$option='';
                        foreach($categories as $category){
                            
                            $option.="<option value='".$category -> id."' >".$category -> label." </option>";
                        }
						
                    ?>
                    <select name="pw_category_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                 
                <?php
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
                    <select name="pw_sku_variations[]" multiple="multiple" size="5"  data-size="5" class="variation_elements chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                <?php 
					}
				?>
                
                
                <?php
                	$pw_product_sku_data = $this->pw_get_woo_var_prod_sku();
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
                            $new_key = str_replace("wcv_","",$key);
                            $option.="<option $selected value='".$new_key."' >".$value." </option>";
                        }
                    ?>
                
                    <select name="pw_variations[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Basic Column',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					
                    <input type="checkbox" name="pw_general_cols" class="pw_general_cols" value="yes">
                </div>
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Zero Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
                    <input type="checkbox" name="pw_stock_zero" class="pw_stock_zero" value="yes" >
                    <label>Include items having 0 stock</label>
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