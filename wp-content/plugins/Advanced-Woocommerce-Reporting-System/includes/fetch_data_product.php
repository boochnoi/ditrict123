<?php
	
	if($file_used=="sql_table")
	{
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
		$pw_show_cog		= $this->pw_get_woo_requests('pw_show_cog','no',true);
		
		///////////HIDDEN FIELDS////////////
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		//$category_id 				= "-1";
		if(is_array($category_id)){ 		$category_id		= implode(",", $category_id);}	
			
		/////////CUSTOM TAXONOMY//////////
		$key=$this->pw_get_woo_requests('table_names','',true);
		
		$visible_custom_taxonomy='';
		$post_name='product';
		
		$all_tax_cols=$all_tax_joins=$all_tax_conditions='';
		$custom_tax_cols='';
		$all_tax=$this->fetch_product_taxonomies( $post_name );
		$current_value=array();
		if(defined("__PW_TAX_FIELD_ADD_ON__") && is_array($all_tax) && count($all_tax)>0){
			//FETCH TAXONOMY
			$i=10;
			foreach ( $all_tax as $tax ) {

				$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
				if($tax_status=='on'){
					
					
					$taxonomy=get_taxonomy($tax);	
					$values=$tax;
					$label=$taxonomy->label;
					
					$translate=get_option($key.'_'.$tax."_translate");
					$show_column=get_option($key.'_'.$tax."_column");
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
					
					$lbl_col=$tax."_cols";
					$lbl_join=$tax."_join";
					$lbl_con=$tax."_condition";
					
					
					${$lbl_col} ='';
					${$lbl_join} ='';
					${$lbl_con} = '';
					
					if(${$tax}  && ${$tax} != "-1") {
						${$lbl_col} = "
							
							,term_taxonomy$i.parent						AS term_parent";
					}
					
					$all_tax_cols=" ".${$lbl_col}." ";
					
					if(${$tax}  && ${$tax} != "-1") {
						${$lbl_join} = "
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships$i 	ON pw_term_relationships$i.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy$i 		ON term_taxonomy$i.term_taxonomy_id	=	pw_term_relationships$i.term_taxonomy_id
					";
					}
					
					$all_tax_joins.=" ".${$lbl_join}." ";
					
					if(${$tax}  && ${$tax} != "-1")
						${$lbl_con} = "AND term_taxonomy$i.taxonomy LIKE('$tax') AND term_taxonomy$i.term_id IN (".${$tax} .")";
					
					$all_tax_conditions.=" ".${$lbl_con}." ";	
					
					$i++;
				}
			}
		}
		//////////////////		
		
		
		//CATEGORY
		$category_id_cols='';
		$category_id_join='';	
		$category_id_condition='';
		
		//PRODUCT
		$pw_product_id_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PRODUCT STRING
		$pw_cat_prod_id_string_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		$pw_id_order_status_join='';
		$pw_id_order_status_condition='';
		
		//PUBLISH STATUS
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition='';
		
		
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
		
		$permission_value=$this->get_form_element_value_permission('pw_orders_status',$key);
		$permission_enable=$this->get_form_element_permission('pw_orders_status',$key);
		
		if($permission_enable && $pw_order_status=='-1' && $permission_value!=1){
			$pw_order_status=implode(",",$permission_value);
		}
		if($pw_order_status != NULL  && $pw_order_status != '-1')
			$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		///////////////////////////
		
				
		$sql = " SELECT ";
		
		$sql_columns = "							
					pw_woocommerce_order_items.order_item_name		AS 'product_name'
					,pw_woocommerce_order_items.order_item_id		AS order_item_id
					,pw_woocommerce_order_itemmeta7.meta_value		AS product_id							
					,DATE(shop_order.post_date)					AS post_date
					";

		if($category_id  && $category_id != "-1") {
			
			$category_id_cols = "
					,pw_terms.term_id								AS term_id
					,pw_terms.name									AS term_name
					,term_taxonomy.parent						AS term_parent
				";						
		}
						
		//$sql .= " ,woocommerce_order_itemmeta.meta_value AS 'quantity' ,pw_woocommerce_order_itemmeta6.meta_value AS 'total_amount'";
		$sql_columns .= "$category_id_cols ,SUM(woocommerce_order_itemmeta.meta_value) AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value) AS 'total_amount' ";
		
		//COST OF GOOD
		if($pw_show_cog=='yes'){
			$sql_columns .= " ,SUM(woocommerce_order_itemmeta.meta_value * pw_woocommerce_order_itemmeta22.meta_value) AS 'total_cost'";
		}
		
				
		$sql_joins = "
					{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id ";
				
				//COST OF GOOD
				if($pw_show_cog=='yes'){
					$sql_joins .=	"	
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta22 ON pw_woocommerce_order_itemmeta22.order_item_id=pw_woocommerce_order_items.order_item_id ";
				}
					$sql_joins .=	"	
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id						
					";
		
		if($category_id  && $category_id != "-1") {
				$category_id_join = " 	
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
		
		
		
		
		$sql_joins .= " $category_id_join $pw_id_order_status_join
					LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id";
					
		$sql_condition = "
					1*1
					AND woocommerce_order_itemmeta.meta_key	= '_qty'
					AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total'";
				
				//COST OF GOOD
				if($pw_show_cog=='yes'){	
					$sql_condition .="
					AND pw_woocommerce_order_itemmeta22.meta_key	= '".__PW_COG_TOTAL__."' ";
				}
					$sql_condition .="	 
					AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'						
					AND shop_order.post_type					= 'shop_order'
					";
					
		
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition= " 
					AND (DATE(shop_order.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
		}
		
		if($pw_product_id  && $pw_product_id != "-1") 
			$pw_product_id_condition = "
					AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_product_id .")";
		
		if($category_id  && $category_id != "-1") 
			$category_id_condition = "
					AND pw_terms.term_id IN (".$category_id .")";	
		
		if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1") 
			$pw_cat_prod_id_string_condition= " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_cat_prod_id_string .")";	
		
		if($pw_id_order_status  && $pw_id_order_status != "-1") 
			$pw_id_order_status_condition = " 
					AND terms2.term_id IN (".$pw_id_order_status .")";
					
		
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition= " AND  shop_order.post_status IN ('{$in_post_status}')";
		}
		//echo $pw_order_status;
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND shop_order.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
		
		$sql_group_by = " GROUP BY  pw_woocommerce_order_itemmeta7.meta_value";			
		
		$sql_order_by = " ORDER BY total_amount DESC";
		
		$sql = "SELECT $sql_columns $all_tax_cols FROM $sql_joins $all_tax_joins WHERE 
							$sql_condition $pw_from_date_condition $pw_product_id_condition
							$category_id_condition $all_tax_conditions  $pw_cat_prod_id_string_condition
							$pw_id_order_status_condition $pw_publish_order_condition $pw_order_status_condition
							$pw_hide_os_condition $sql_group_by $sql_order_by";
		
	
		//echo $sql;
		$this->table_cols =$this->table_columns($table_name);
		$array_index=3;
		if(is_array($custom_tax_cols))
			array_splice($this->table_cols,$array_index,0,$custom_tax_cols);
		
		//CHECK IF COST OF GOOD IS ENABLE
		if($pw_show_cog!='yes'){
			unset($this->table_cols[count($this->table_cols)-1]);
			unset($this->table_cols[count($this->table_cols)-1]);
		}
		
		//$this->table_cols = $columns;
		
	}elseif($file_used=="data_table"){
		//print_r($this->results);
		
		/////////CUSTOM TAXONOMY////////
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
				//if($tax_status=='on'){
					$visible_custom_taxonomy[]=$tax;
				}
			}
		}
		///////////////////////////
		
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
									
				
				//Product SKU
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
				$datatable_value.=("</td>");
				
				//Product Name
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= " <a href=\"".get_permalink($items->product_id)."\" target=\"_blank\">{$items->product_name}</a>";
				$datatable_value.=("</td>");
				
				//Categories
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
				$datatable_value.=("</td>");
				
				
				///////////////CUSTOM TAXONOMY/////////////////
				if(defined("__PW_TAX_FIELD_ADD_ON__") &&  is_array($visible_custom_taxonomy)){
					foreach((array)$visible_custom_taxonomy as $tax){
						//Category
						$display_class='';
						if($this->table_cols[6]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.=$this->pw_get_cn_product_id($items->product_id,$tax);
						$datatable_value.=("</td>");
					}
				}
				////////////////////////////
				
				
				//Sale Qty.
				$display_class='';
				if($this->table_cols[3]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."' >");
					$datatable_value.= $items->quantity;
				$datatable_value.=("</td>");
				
				//Current Stock
				$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_prod_stock_($items->order_item_id, $items->product_id);
				$datatable_value.=("</td>");
				
				//Amount
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
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
						$datatable_value.= ($items->total_amount-$items->total_cost) == 0 ? 0 : $this->price($items->total_amount-$items->total_cost);
					$datatable_value.=("</td>");
				}
				
				
			$datatable_value.=("</tr>");
		}
	}elseif($file_used=="search_form"){
	?>
		<form class='alldetails search_form_report' action='' method='post' id="product_form">
            <input type='hidden' name='action' value='submit-form' />
            <div class="row">
                
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
                        $products=$this->pw_get_product_woo_data('all');
                        $option='';
                        $current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($products as $product){
							$selected='';
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;
							if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
								$selected="selected";
                            
                           /* if($current_product==$product->id)
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