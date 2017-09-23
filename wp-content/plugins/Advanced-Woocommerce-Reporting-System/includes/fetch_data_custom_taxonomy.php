<?php
	
	if($file_used=="sql_table")
	{
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_parent_cat_id		= $this->pw_get_woo_requests('pw_parent_category_id','-1',true);
		if($pw_parent_cat_id!='-1')
			$pw_parent_cat_id  		= "'".str_replace(",","','",$pw_parent_cat_id)."'";
			
	
		$pw_customy_taxonomies	= $this->pw_get_woo_requests('pw_customy_taxonomies','-1',true);
			
		$pw_child_cat_id	= $this->pw_get_woo_requests('child_category_id','-1',true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_list_parent_cat			= $this->pw_get_woo_requests('list_parent_category',NULL,false);
		$category_id			= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_group_by_parent_cat			= $this->pw_get_woo_requests('group_by_parent_cat','-1',true);
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		
		
	
		///////////////////////////
		
		
		//////////////////////
		//pw_customy_taxonomies
				 
		//DATE
		$pw_from_date_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		$pw_id_order_status_join='';
		$pw_id_order_status_condition='';
		
		//CATEGORY
		$category_id_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		
		//PARENT CATEGORY
		$pw_parent_cat_id_condition='';
		
		//CHILD CATEGORY
		$pw_child_cat_id_condition='';
		
		//LIST PARENT CATEGORY
		$pw_list_parent_cat_condition='';
		
		//PUBLISH STATUS
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition='';
		
		$sql_columns = " 
		SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value) AS quantity
		,SUM(pw_woocommerce_order_itemmeta_product_line_total.meta_value) AS total_amount
		,pw_terms_product_id.term_id AS category_id
		,pw_terms_product_id.name AS category_name
		,pw_term_taxonomy_product_id.parent AS parent_category_id
		,pw_terms_parent_product_id.name AS parent_category_name";
		
		$sql_joins= "{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items
		
		 LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_id ON pw_woocommerce_order_itemmeta_product_id.order_item_id=pw_woocommerce_order_items.order_item_id
		 LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_qty ON pw_woocommerce_order_itemmeta_product_qty.order_item_id=pw_woocommerce_order_items.order_item_id
		 LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta_product_line_total ON pw_woocommerce_order_itemmeta_product_line_total.order_item_id=pw_woocommerce_order_items.order_item_id";
		
		
		$sql_joins .= " 	LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships_product_id 	ON pw_term_relationships_product_id.object_id		=	pw_woocommerce_order_itemmeta_product_id.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy_product_id 		ON pw_term_taxonomy_product_id.term_taxonomy_id	=	pw_term_relationships_product_id.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_product_id 				ON pw_terms_product_id.term_id						=	pw_term_taxonomy_product_id.term_id
		
		 LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms_parent_product_id 				ON pw_terms_parent_product_id.term_id						=	pw_term_taxonomy_product_id.parent
		
		 LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.id=pw_woocommerce_order_items.order_id";
		
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
				$pw_id_order_status_join= " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " 1*1 
		AND pw_woocommerce_order_items.order_item_type 					= 'line_item'
		AND pw_woocommerce_order_itemmeta_product_id.meta_key 			= '_product_id'
		AND pw_woocommerce_order_itemmeta_product_qty.meta_key 			= '_qty'
		AND pw_woocommerce_order_itemmeta_product_line_total.meta_key 	= '_line_total'
		AND pw_term_taxonomy_product_id.taxonomy 						= '$pw_customy_taxonomies'
		AND pw_posts.post_type 											= 'shop_order'";				
		
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
			$pw_id_order_status_condition= " AND  term_taxonomy.term_id IN ({$pw_id_order_status})";
		}
		
		if($pw_parent_cat_id != NULL and $pw_parent_cat_id != "-1"){
			$pw_parent_cat_id_condition= " AND pw_term_taxonomy_product_id.parent IN ($pw_parent_cat_id)";
		}
		
		if($pw_child_cat_id != NULL and $pw_child_cat_id != "-1"){
			$pw_child_cat_id_condition= " AND pw_terms_product_id.term_id IN ($pw_child_cat_id)";
		}
		
		if($pw_list_parent_cat != NULL and $pw_list_parent_cat > 0){
			$pw_list_parent_cat_condition= " AND pw_term_taxonomy_product_id.parent > 0";
		}
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition= " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		}
		
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition= " AND  pw_posts.post_status IN ('{$in_post_status}')";
		}
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition= " AND pw_posts.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition= " AND pw_posts.post_status NOT IN (".$pw_hide_os.")";
		
		
		if($category_id  && $category_id != "-1") {
			$category_id_condition= " AND pw_terms_product_id.term_id IN ($category_id)";
		}
		
		
		$sql_group_by='';
		
		if($pw_group_by_parent_cat == 1){
			$sql_group_by= " GROUP BY parent_category_id";
		}else{
			$sql_group_by= " GROUP BY category_id";
		};
		
		$sql_order_by= "  Order By total_amount DESC";
		
		$sql = "SELECT $sql_columns FROM $sql_joins $pw_id_order_status_join WHERE $sql_condition
				$pw_id_order_status_condition $pw_parent_cat_id_condition $pw_child_cat_id_condition
				$pw_list_parent_cat_condition $pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition $category_id_condition
				$sql_group_by $sql_order_by
				";
		
		//echo $sql;
		
	}elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
									
						
				//Category Name
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->category_name;
				$datatable_value.=("</td>");
				
				//Quantity
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->quantity;
				$datatable_value.=("</td>");
				
				//Amount
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
				$datatable_value.=("</td>");
				
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
                    
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
                    <input type="hidden" name="pw_orders_status[]" id="order_status" value="<?php echo $this->pw_shop_status; ?>">

                </div>
                
                <?php
					$permission_value=$this->get_form_element_value_permission('pw_customy_taxonomies');
				
					$tax_items=$json_taxs='';
					$post_name='product';
					$options='';
					$custom_tax_cols='';
					//$all_tax=get_object_taxonomies( $post_name );
					$all_tax=$this->fetch_product_taxonomies( $post_name );
					$current_value=array();
					if(is_array($all_tax) && count($all_tax)>0){
						//FETCH TAXONOMY
						$i=1;
						foreach ( $all_tax as $tax ) {
							
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($tax,$permission_value))
								continue;
			
							$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$tax);
							if($tax_status=='on' ||  $permission_value!=''){
								
								
								if(!$this->get_form_element_permission('pw_customy_taxonomies') &&  $permission_value!='')
									$selected="selected";	
								
								$taxonomy=get_taxonomy($tax);	
								$values=$tax;
								$label=$taxonomy->label;
								
								$options.="<option $selected value='$values'>$label</option>";
								
								$args = array(
                                    'orderby'                  => 'name',
                                    'order'                    => 'ASC',
                                    'hide_empty'               => 0,
                                    'hierarchical'             => 1,
                                    'exclude'                  => '',
                                    'include'                  => '',
                                    'child_of'          		 => 0,
                                    'number'                   => '',
                                    'pad_counts'               => false 
                                
                                ); 
                
								$items='';
                                $categories = get_terms($tax,$args); 
								$i=0;
                                foreach ($categories as $category) {
                                    $items[$i]['id']=$category->term_id;
									$items[$i]['label']=$category->name;
									$i++;
                                }
                                $tax_items[$tax]=$items;
							}
						}
						
						$json_taxs = json_encode($tax_items);
					}			
					
					//echo ($json_taxs);
				
				$col_style='';
				$permission_value=$this->get_form_element_value_permission('pw_customy_taxonomies');
				if($this->get_form_element_permission('pw_customy_taxonomies') ||  $permission_value!=''){
						
					if(!$this->get_form_element_permission('pw_customy_taxonomies') &&  $permission_value!='')
						$col_style='display:none';
							
					if($options!=''){		
				?>
                
                <div class="col-md-6" style=" <?php echo $col_style;?>">
                    <div class="awr-form-title">
                        <?php _e('Custom Taxonomy',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
						
                        <select name="pw_customy_taxonomies" id="pw_customy_taxonomies">
                            <?php
                                echo $options;
                            ?>
                        </select>  
                      	<script type="text/javascript">
							"use strict";
							jQuery( document ).ready(function( $ ) {
								
								var taxs='';
								taxs=<?php echo $json_taxs?>;	
								
								$("#pw_customy_taxonomies").change(function(){
									
									var datas='';
									var option_data = Array();
									datas=taxs[$(this).val()];
									var options = '<option value="-1">Select All</option>';
									var i = 1;
									$.each(datas, function(key,val){
	
										options += '<option value="' + val.id + '">' + val.label + '</option>';
										option_data[val.id] = val.label;
										i++;
									});
										
									//$("#pw_adr_product").html(options);
									$('#pw_adr_customy_taxonomy_id').empty(); //remove all child nodes
									$("#pw_adr_customy_taxonomy_id").html(options);
									$('#pw_adr_customy_taxonomy_id').trigger("chosen:updated");
								});
								
							});
                        </script>
                </div>
                
                <!--<div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Items',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-tags"></i></span>
					
                    <select name="pw_customy_taxonomy_id[]" id="pw_adr_customy_taxonomy_id" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                       
                    </select>  
            	</div>-->
                
				<?php }                
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
                    <input type="hidden" name="pw_category_id" value="-1">
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