<?php
	
	if($file_used=="sql_table")
	{
	
		$pw_product_id		= $this->pw_get_woo_requests('pw_product_id','-1',true);
		$category_id	= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$ProductTypeID	= $this->pw_get_woo_requests('ProductTypeID',NULL,true);			
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


		$pw_product_sku		= $this->pw_get_woo_sm_requests('pw_sku_products',$pw_product_sku, "-1");
		
		$pw_manage_stock		= $this->pw_get_woo_sm_requests('pw_stock_manage',$pw_manage_stock, "-1");
		
		$pw_stock_status		= $this->pw_get_woo_sm_requests('pw_status_of_stock',$pw_stock_status, "-1");
		
		
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
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships$i 			ON pw_term_relationships$i.object_id=pw_posts.ID
							LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy$i				ON term_taxonomy$i.term_taxonomy_id	=	pw_term_relationships$i.term_taxonomy_id
							";
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
		
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		
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
		///////////////////////////
					
	
		//PRODUCT SUBTYPE
		$pw_product_subtype_join='';
		$pw_product_subtype_conditoin_1='';
		$pw_product_subtype_conditoin_2='';
		
		
		//SKU NUMBER
		$pw_sku_number_join='';
		$pw_sku_number_conditoin=''; 
		
		//PRODUCT STOCK
		$pw_product_stock_join =''; 
		$pw_product_stock_conditoin='';
		$product_other_conditoin='';
		
		//CATEGORY
		$category_id_join ='';
		$category_id_conditoin ='';
		
		//PRODUCT TYPE
		$pw_product_type_join='';
		$pw_product_type_conditoin='';
		
		//ZERO SOLD
		$pw_zero_sold_join='';
		$pw_zero_stock_conditoin='';
		$pw_zero_sold_conditoin='';
		
		//STOCK STATUS
		$pw_stock_status_join='';
		$pw_stock_status_conditoin='';
		
		//MANAGE STOCK
		$pw_manage_stock_join ='';
		$pw_manage_stock_conditoin='';
		
		//PRODUCT NAME
		$pw_product_name_conditoin='';
		
		//PRODUCT ID
		$pw_product_id_conditoin='';
		$pw_product_sku_conditoin='';
				
		
		//MAX & MIN
		$pw_txt_min_stock_conditoin ='';
		$pw_txt_max_stock_conditoin='';
		
		
		$sql_columns = " 
		pw_posts.post_title as product_name
		,pw_posts.post_date as product_date
		,pw_posts.post_modified as modified_date
		,pw_posts.ID as product_id";
		
		$sql_joins = "{$wpdb->prefix}posts as pw_posts ";
		
		if($pw_product_subtype =="virtual") 						
			$pw_product_subtype_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_virtual 			ON pw_virtual.post_id			=pw_posts.ID";
			
		if($pw_product_subtype=="downloadable") 					
			$pw_product_subtype_join = " LEFT JOIN  {$wpdb->prefix}postmeta as pw_downloadable		ON pw_downloadable.post_id		=pw_posts.ID";
			
		if($pw_sku_number || ($pw_product_sku and $pw_product_sku != '-1')) 
			$pw_sku_number_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_sku 				ON pw_sku.post_id				=pw_posts.ID";
		
		if($pw_product_stock || $pw_txt_min_stock || $pw_txt_max_stock || $pw_zero_stock) 		
			$pw_product_stock_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_stock 				ON pw_stock.post_id			=pw_posts.ID";
		
		if($category_id and $category_id != "-1"){
			$category_id_join= " LEFT JOIN  {$wpdb->prefix}term_relationships as pw_term_relationships ON pw_term_relationships.object_id=pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=pw_term_relationships.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms as pw_terms ON pw_terms.term_id=term_taxonomy.term_id";
		}
		
		if($pw_product_type and $pw_product_type != "-1"){
			$pw_product_type_join= " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_type 	ON pw_term_relationships_product_type.object_id		=	pw_posts.ID 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_type 		ON pw_term_taxonomy_product_type.term_taxonomy_id		=	pw_term_relationships_product_type.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_type 				ON pw_terms_product_type.term_id						=	pw_term_taxonomy_product_type.term_id";
		}
		
		
		if($pw_zero_sold=="yes") 						
			$pw_zero_sold_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_total_sales 			ON pw_total_sales.post_id			=pw_posts.ID";			
			
		if($pw_stock_status and $pw_stock_status != '-1') 
			$pw_stock_status_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_stock_status 			ON pw_stock_status.post_id			=pw_posts.ID";
			
		if($pw_manage_stock and $pw_manage_stock != '-1') 
			$pw_manage_stock_join= " LEFT JOIN  {$wpdb->prefix}postmeta as pw_manage_stock 			ON pw_manage_stock.post_id			=pw_posts.ID";												
		
		$sql_condition= "  pw_posts.post_type='product' AND pw_posts.post_status = 'publish'";
		
		if($pw_product_stock || $pw_txt_min_stock || $pw_txt_max_stock || $pw_zero_stock) 		
			$product_other_conditoin= " AND pw_stock.meta_key ='_stock'";
			
		if($pw_sku_number || ($pw_product_sku and $pw_product_sku != '-1'))						
			$pw_sku_number_conditoin= " AND pw_sku.meta_key ='_sku'";
			
		if($pw_product_subtype=="downloadable") 		
			$pw_product_subtype_conditoin_1= " AND pw_downloadable.meta_key ='_downloadable'";					
			
		if($pw_product_subtype=="virtual") 			
			$pw_product_subtype_conditoin_1= " AND pw_virtual.meta_key ='_virtual'";	
						
		
		
		if($pw_product_name) 							
			$pw_product_name_conditoin= " AND pw_posts.post_title like '%{$pw_product_name}%'";
			
		if($pw_product_id and $pw_product_id >0) 			
			$pw_product_id_conditoin= " AND pw_posts.ID IN ({$pw_product_id})";
			
		if($pw_product_stock) 							
			$pw_product_stock_conditoin= " AND pw_stock.meta_value IN ({$pw_product_stock})";
			

		if($pw_txt_min_stock) 							
			$pw_txt_min_stock_conditoin= " AND pw_stock.meta_value >= {$pw_txt_min_stock}";
			
		if($pw_txt_max_stock) 							
			$pw_txt_max_stock_conditoin= " AND pw_stock.meta_value <= {$pw_txt_max_stock}";
			
		if($pw_product_subtype=="downloadable") 		
			$pw_product_subtype_conditoin_2= " AND pw_downloadable.meta_value = 'yes'";					
			
		if($pw_product_subtype=="virtual") 			
			$pw_product_subtype_conditoin_2= " AND pw_virtual.meta_value = 'yes'";					
		
		if($category_id and $category_id != "-1") 	
			$category_id_conditoin= " AND pw_terms.term_id IN ({$category_id})";
		
		if($pw_product_type and $pw_product_type != "-1")	
			$pw_product_type_conditoin= " AND pw_terms_product_type.name IN ('{$pw_product_type}')";						
		
		if($pw_product_sku and $pw_product_sku != '-1'){
			if(strlen($pw_sku_number) > 0) {
				$pw_product_sku_conditoin= " AND (pw_sku.meta_value like '%{$pw_sku_number}%' OR  pw_sku.meta_value IN (".$pw_product_sku.") )";
			}else{
				$pw_product_sku_conditoin= " AND pw_sku.meta_value IN (".$pw_product_sku.")";
				//$sql .= " AND pw_sku.meta_value = ".$pw_product_sku;
			}
		}else{
			if(strlen($pw_sku_number) > 0) 	
				$pw_product_sku_conditoin= " AND pw_sku.meta_value like '%{$pw_sku_number}%'";
		}
		
		if($pw_zero_stock == "no")	
			$pw_zero_stock_conditoin= " AND (pw_stock.meta_value > 0 AND LENGTH(pw_stock.meta_value) > 0)";
		
		if($pw_zero_sold=="yes")		
			$pw_zero_sold_conditoin= " AND pw_total_sales.meta_key ='total_sales' AND (pw_total_sales.meta_value <= 0 OR LENGTH(pw_total_sales.meta_value) <= 0)";				
			
		if($pw_stock_status and $pw_stock_status != '-1')		
		$pw_stock_status_conditoin= " AND pw_stock_status.meta_key ='_stock_status' AND pw_stock_status.meta_value IN ({$pw_stock_status})";
		
		if($pw_manage_stock and $pw_manage_stock != '-1')		
		$pw_manage_stock_conditoin= " AND pw_manage_stock.meta_key ='_manage_stock' AND pw_manage_stock.meta_value IN ({$pw_manage_stock})";
		
		$current_lng_cond='';
		$current_lng_join='';

		/*if(defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE!=''){
			$current_lng_join=' wp_icl_translations language_code , ';
			$current_lng_cond=" AND language_code = '".ICL_LANGUAGE_CODE."' AND element_id = pw_posts.ID";	
		}*/
		
		$sql="SELECT $sql_columns 
			FROM $current_lng_join $sql_joins $pw_product_subtype_join $pw_sku_number_join $pw_product_stock_join 
			$category_id_join $all_tax_joins $pw_product_type_join $pw_zero_sold_join $pw_stock_status_join 
			$pw_manage_stock_join 
			WHERE $sql_condition $product_other_conditoin $pw_sku_number_conditoin 
			$pw_product_subtype_conditoin_1 $pw_product_name_conditoin $pw_product_id_conditoin
			$pw_product_stock_conditoin $pw_txt_min_stock_conditoin $pw_txt_max_stock_conditoin
			$pw_product_subtype_conditoin_2 $category_id_conditoin $all_tax_conditions 
			$pw_product_type_conditoin $pw_product_sku_conditoin $pw_zero_stock_conditoin
			$pw_zero_sold_conditoin $pw_stock_status_conditoin $pw_manage_stock_conditoin $current_lng_cond GROUP BY product_id";
		
		//echo $sql;
		
		///////////////////
		//EXTRA COLUMNS
		$this->table_cols =$this->table_columns($table_name);
		
		$variation_cols_arr=array();
		if($pw_basic_column=='yes'){
			
			
			$columns=array(
				array('lable'=>__('SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Product Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),				
				array('lable'=>__('Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
			);
		
			$array_index=3;
			if(is_array($custom_tax_cols))
				array_splice($columns,$array_index,0,$custom_tax_cols);
				
		}else if($pw_basic_column!='yes'){
			
			$columns=array(
				array('lable'=>__('SKU',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Product Name',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Product Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				
				array('lable'=>__('Created Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Modified Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Regular Price',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				array('lable'=>__('Sale Price',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'),
				
				array('lable'=>__('Downloadable',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Virtual',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Manage Stock',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Backorders',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show'),
				array('lable'=>__('Stock Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'show')
			);
			
			
			/*$variation_cols_arr[] = array('lable'=>'Edit','status'=>'show');*/
			
			$array_index=4;
			if(is_array($custom_tax_cols))
				array_splice($columns,$array_index,0,$custom_tax_cols);
			
		}
		
		$this->table_cols = $columns;

				
	}elseif($file_used=="data_table"){
		
		
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
		//////////////////////////////////////////////////////
		
		foreach($this->results as $items){	
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
			
			$pw_basic_column = $this->pw_get_woo_requests('pw_general_cols','-1',true);
			$pw_product_type= $this->pw_get_woo_requests('pw_products_type','-1',true);
			
			$product_details=$this->pw_get_full_post_meta($items->product_id);
			//print_r($product_details);
			if($pw_basic_column=='yes'){
				//SKU
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= isset($product_details['sku']) && $product_details['sku']!='' ? round($product_details['sku']) : " ";
				$datatable_value.=("</td>");
				
				//Product Name
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= " <a href=\"".get_permalink($items->product_id)."\" target=\"_blank\">{$items->product_name}</a>";
				$datatable_value.=("</td>");
				
				//Category
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
				$datatable_value.=("</td>");
				
				if(is_array($visible_custom_taxonomy)){
					foreach((array)$visible_custom_taxonomy as $tax){
						//TAXONOMY
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,$tax);
						$datatable_value.=("</td>");
					}
				}
				
				//Stock
				$display_class='';
				if($this->table_cols[3]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= isset($product_details['stock']) && $product_details['stock']!='' ? round($product_details['stock'])  : "0";
				$datatable_value.=("</td>");
				
				
				
				//Edit
				/*$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Edit';
				$datatable_value.=("</td>");*/
				
			}else if($pw_basic_column!='yes'){
				
				//SKU
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.=  isset($product_details['sku']) ? $product_details['sku'] : "";
				$datatable_value.=("</td>");
				
				//Product Name
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= " <a href=\"".get_permalink($items->product_id)."\" target=\"_blank\">{$items->product_name}</a>";
				$datatable_value.=("</td>");
				
				//Product Type
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_pt_product_id($items->product_id);;
				$datatable_value.=("</td>");
				
				//Category
				$display_class='';
				if($this->table_cols[3]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
				$datatable_value.=("</td>");
				
				
				if(is_array($visible_custom_taxonomy)){
					foreach((array)$visible_custom_taxonomy as $tax){
						//Category
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,$tax);
						$datatable_value.=("</td>");
					}
				}
				
				
				//Create Date
				$date_format	= get_option( 'date_format' );
				$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= date($date_format,strtotime($items->product_date));
				$datatable_value.=("</td>");
				
				
				
				//Modified Date
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= date($date_format,strtotime($items->modified_date));
				$datatable_value.=("</td>");
				
				//Stock
				$display_class='';
				if($this->table_cols[6]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $product_details['stock']!='' ? number_format($product_details['stock'],0,'','') : "0";
				$datatable_value.=("</td>");
				
				
				
				$regular_price='';
				$sale_price='';
				
				$regular_price=$product_details['regular_price'];
				$sale_price=$product_details['sale_price'];
				
				//Regualr Price
				$display_class='';
				if($this->table_cols[7]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($regular_price);
				$datatable_value.=("</td>");
				
				//Sale Price
				$display_class='';
				if($this->table_cols[8]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($sale_price);
				$datatable_value.=("</td>");
				
				//Downloadable
				$display_class='';
				if($this->table_cols[9]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= ucwords($product_details['downloadable']);
				$datatable_value.=("</td>");
				
				//Virtual
				$display_class='';
				if($this->table_cols[10]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= ucwords($product_details['virtual']);
				$datatable_value.=("</td>");
				
				//Manage Stock
				$display_class='';
				if($this->table_cols[11]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= ucwords($product_details['manage_stock']);
				$datatable_value.=("</td>");
				
				//Backorders
				$display_class='';
				if($this->table_cols[12]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $product_details['backorders']=='no' ? "Do not allow" : $product_details['backorders'];
				$datatable_value.=("</td>");
				
				//Stock Status
				$pw_stock_status = array("instock" => __("In stock",__PW_REPORT_WCREPORT_TEXTDOMAIN__), "outofstock" => __("Out of stock",__PW_REPORT_WCREPORT_TEXTDOMAIN__));
				
				$display_class='';
				if($this->table_cols[13]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $pw_stock_status[$product_details['stock_status']];
				$datatable_value.=("</td>");
				
				//Edit
				/*$display_class='';
				if($this->table_cols[14]['status']=='hide') $display_class='display:none';
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
                    <select name="pw_sub_product_type" id="pw_sub_product_type">
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
                
                <?php
					$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_product_id');
                	if($this->get_form_element_permission('pw_product_id') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
                        //$product_data = $this->pw_get_product_woo_data('variable');
                        $products=$this->pw_get_product_woo_data('0');
                        $option='';
                        
                        
                        foreach($products as $product){
							$selected='';	
                            //CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
								$selected="selected";*/
                            $option.="<option value='".$product -> id."' $selected>".$product -> label." </option>";
                        }
                        
                        
                    ?>
                    <select id="pw_adr_product" name="pw_product_id[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
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
					
					/////////////////
					//CUSTOM TAXONOMY
					$key=isset($_GET['smenu']) ? $_GET['smenu']:$_GET['parent'];
					$args_f=array("page"=>$key);
					echo $this->make_custom_taxonomy($args_f);
					
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
                        <?php _e('Product Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <select name="pw_products_type" id="pw_products_type">
                        <option value="-1">Show all product types</option>
                        <option value="simple">Simple product</option>
                        <option value="variable">Variable</option>
                    </select>
                </div>
                
                <?php
                	$pw_product_sku_data = $this->pw_get_woo_all_prod_sku();
					//echo ($pw_product_sku_data);
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