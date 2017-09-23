<?php
	
	if($file_used=="sql_table")
	{
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_from_date=substr($pw_from_date,0,strlen($pw_from_date)-3);
		$pw_to_date=substr($pw_to_date,0,strlen($pw_to_date)-3);

		$pw_product_id			= $this->pw_get_woo_requests('pw_products',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_categories','-1',true);
		$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
		$category_id 				= "-1";
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','item_name',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_variation_attributes 	= $this->pw_get_woo_requests('pw_variations',NULL,true);
		//if($pw_variation_attributes!=NULL)
		if($pw_variation_attributes != NULL  && $pw_variation_attributes != '-1')
		{
			
			$pw_variations = explode(",",$pw_variation_attributes);
			$var='';
			foreach($pw_variations as $key => $value):
				$var[] .=  "attribute_pa_".$value;
				$var[] .=  "attribute_".$value;
			endforeach;
			$pw_variation_attributes =  implode("', '",$var);
		}
		
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
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		//Category ID
		$category_id_join="";
		
		//Variations Attributes
		$pw_variation_attributes_join="";
		
		//ORDER Status
		$pw_id_order_status_join="";
		$pw_id_order_status_condition="";
		
		//Variations Attributes
		$pw_variation_attributes_condition="";
		
		//Start Date
		$pw_from_date_condition="";
		
		//Publish ORDER
		$pw_publish_order_condition="";
		
		//ORDER Status
		$pw_order_status_condition=""; 
		
		//Category ID
		$category_id_condition="";
		
		//Category Product ID
		$pw_cat_prod_id_string_condition="";
		
		//Product ID
		$pw_product_id_condition="";
		
		
		//HIDE ORDER Status
		$pw_hide_os_condition="";
		
		//SQL GROUP
		$sql_group_by="";

		$sql_columns = "
			pw_woocommerce_order_itemmeta_variation.meta_value			as id
			,pw_woocommerce_order_itemmeta_product.meta_value 			as product_id
			,pw_woocommerce_order_items.order_item_id 					as order_item_id
			,pw_woocommerce_order_items.order_item_name 				as product_name
			,pw_woocommerce_order_items.order_item_name 				as item_name
			
			
			,SUM(pw_woocommerce_order_itemmeta_product_total.meta_value) 	as total
			,SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value) 	as quantity
			
			,MONTH(shop_order.post_date) 							as month_number
			,DATE_FORMAT(shop_order.post_date, '%Y-%m')				as month_key
			,pw_woocommerce_order_itemmeta_variation.meta_value		as variation_id
			,pw_woocommerce_order_items.order_id						as order_id
			,shop_order.post_status
			,pw_woocommerce_order_items.order_item_id					as order_item_id";
			
			$sql_joins ="{$wpdb->prefix}woocommerce_order_items		as pw_woocommerce_order_items						
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product 			ON pw_woocommerce_order_itemmeta_product.order_item_id			=	pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product_total 	ON pw_woocommerce_order_itemmeta_product_total.order_item_id	=	pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product_qty		ON pw_woocommerce_order_itemmeta_product_qty.order_item_id		=	pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_variation			ON pw_woocommerce_order_itemmeta_variation.order_item_id 		= 	pw_woocommerce_order_items.order_item_id
			
			LEFT JOIN  {$wpdb->prefix}posts 						as shop_order 									ON shop_order.id											=	pw_woocommerce_order_items.order_id
		";
			
		if($category_id != NULL  && $category_id != "-1"){
			$category_id_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 			as pw_term_relationships 							ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta_product.meta_value
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 				as term_taxonomy 								ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 						as pw_terms 										ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_join  = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 			as pw_term_relationships2 							ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 				as pw_term_taxonomy2 								ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 						as terms2 										ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
		if($pw_variation_attributes != "-1" and strlen($pw_variation_attributes)>1)						
			$pw_variation_attributes_join = "
				LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_variation ON pw_postmeta_variation.post_id = pw_woocommerce_order_itemmeta_variation.meta_value
				";		
		
		$sql_condition = "
			pw_woocommerce_order_itemmeta_product.meta_key		=	'_product_id'
			AND pw_woocommerce_order_items.order_item_type		=	'line_item'
			AND shop_order.post_type						=	'shop_order'

			AND pw_woocommerce_order_itemmeta_product_total.meta_key		='_line_total'
			AND pw_woocommerce_order_itemmeta_product_qty.meta_key			=	'_qty'
			AND pw_woocommerce_order_itemmeta_variation.meta_key 			= '_variation_id'
			AND (pw_woocommerce_order_itemmeta_variation.meta_value IS NOT NULL AND pw_woocommerce_order_itemmeta_variation.meta_value > 0)
		";
		
		if($pw_variation_attributes != "-1" and strlen($pw_variation_attributes)>1)
			$pw_variation_attributes_condition = " AND pw_postmeta_variation.meta_key IN ('{$pw_variation_attributes}')";
			

		if ($pw_from_date != NULL &&  $pw_to_date !=NULL)	
			$pw_from_date_condition= " AND DATE_FORMAT(shop_order.post_date, '%Y-%m') BETWEEN '".$pw_from_date ."' AND '". $pw_to_date ."'";
		
		
		if($category_id  != NULL && $category_id != "-1"){
			
			$category_id_condition = " 
			AND term_taxonomy.taxonomy LIKE('product_cat')
			AND pw_terms.term_id IN (".$category_id .")";
		}
		
		if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1") 
			$pw_cat_prod_id_string_condition = " AND pw_woocommerce_order_itemmeta_product.meta_value IN (".$pw_cat_prod_id_string .")";
		
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_condition = "
			AND pw_term_taxonomy2.taxonomy LIKE('shop_order_status')
			AND terms2.term_id IN (".$pw_id_order_status .")";
		}
		
		if($pw_product_id != NULL  && $pw_product_id != '-1'){
			$pw_product_id_condition = "
			AND pw_woocommerce_order_itemmeta_product.meta_value IN ($pw_product_id)";
		}
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND shop_order.post_status IN (".$pw_order_status.")";
			
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
		
		$sql_group_by = " GROUP BY pw_woocommerce_order_itemmeta_variation.meta_value";
		
		$sql_order_by = " ORDER BY {$pw_sort_by} {$pw_order_by}";
		
		$sql = "SELECT $sql_columns 
				FROM $sql_joins $pw_id_order_status_join $category_id_join $pw_variation_attributes_join 
				WHERE $sql_condition $pw_variation_attributes_condition $pw_from_date_condition 
				$category_id_condition $pw_cat_prod_id_string_condition $pw_id_order_status_condition
				$pw_product_id_condition $pw_order_status_condition 
				$pw_hide_os_condition $sql_group_by $sql_order_by";
				
		//echo $sql;	
		
		
		$data_variation='';
		$array_index=2;
		$this->table_cols =$this->table_columns($table_name);
		//print_r($this->table_cols);
		$data_variation=array();
		$attributes_available	= $this->pw_get_woo_atts('-1');
		
				
		$pw_variation_attributes 	= $this->pw_get_woo_requests('pw_variations','-1',true);
		
		
		$variation_sel_arr	= '';
		if($pw_variation_attributes != NULL  && $pw_variation_attributes != '-1')
		{
			$pw_variation_attributes = str_replace("','", ",",$pw_variation_attributes);
			$variation_sel_arr = explode(",",$pw_variation_attributes);
		}
		
		foreach($attributes_available as $key => $value){
			if($pw_variation_attributes=='-1' || is_array($variation_sel_arr) && in_array($key,$variation_sel_arr))
			{
				$data_variation[]=$key;
				$value=array(array('lable'=>$value,'status'=>'show'));
				array_splice($this->table_cols, $array_index++, 0, $value );
			}
		}
	
		$this->data_variation=$data_variation;
				
		//IMPORTANT : DISTANCE CURRENT YEAR AND NEXT YEAR + DETERMINE YEAR FOR MONTHS
		
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);

		$time1  = strtotime($pw_from_date); 
	   	$time2  = strtotime($pw_to_date); 
	   	$my     = date('mY', $time2); 
		$this->month_start=date('m', $time1);
		$months=array();	
		
		$month_count=0;
		
		$data_month='';
		
		if($my!=date('mY', $time1))
		{	
			$year=date('Y', $time1);
			
			$months = array(array('lable'=>$this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time1).'_translate',date('M', $time1))."-".$year,'status'=>'currency')); 
			$month_count=1;
			$data_month[]=$year."-".date('m', $time1);
				
			while($time1 < $time2) { 
				
				$time1 = strtotime(date('Y-m-d', $time1).' +1 month'); 
			  
				if(date('mY', $time1) != $my && ($time1 < $time2)) 
				{
					if($year!=date('Y', $time1))
					{
						$year=date('Y', $time1);
						$label = $this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time1).'_translate',date('M', $time1))."-".$year; 
					}else
						$label = $this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time1).'_translate',date('M', $time1)); 
				
					$month_count++;
					$months[] = array('lable'=>$label,'status'=>'currency');
					$data_month[]=$year."-".date('m', $time1);
				}
			} 
		
			if($year!=date('Y', $time2)){
				$year=date('Y', $time2);
				$label = $this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time2).'_translate',date('M', $time2))."-".$year; 
			}else
				$label = $this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time2).'_translate',date('M', $time2)); 
			$months[] = array('lable'=>$label,'status'=>'currency');	
			$data_month[]=$year."-".date('m', $time2);
		}else
		{
			$year=date('Y', $time1);
			
			$months = array(array('lable'=>$this->pw_translate_function(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.date('M', $time1).'_translate',date('M', $time1))."-".$year,'status'=>'currency')); 
			$data_month[]=$year."-".date('m', $time1);
			$month_count=1;
		}	
	  	//print_r( $data_month); 
		

		$value=array(array('lable'=>__('Total',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'));
		$value=array_merge($months,$value);			
		
		array_splice($this->table_cols, $array_index, count($this->table_cols), $value);
		
		//print_r($this->table_cols);


		$this->month_count=$month_count;
		$this->data_month=$data_month;	
		
		//print_r($this->month_count);

	}elseif($file_used=="data_table"){
		
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
					$datatable_value.= $items->product_name;
				$datatable_value.=("</td>");
			
				
				////////////////////
				//Variation
				
				$variation_arr='';
				$variation = $this->pw_get_product_var_col_separated($items->order_item_id);
				foreach($variation as $key => $value){
					//$month_arr[$item_product->month_number]['total']=$item_product->total;
					//$month_arr[$item_product->month_number]['qty']=$item_product->quantity;
					$variation_arr[$key]=$value;
				}
				
				$j=2;
				foreach($this->data_variation as $variation_name){
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $pw_table_value=(isset($variation_arr[$variation_name]) ?$variation_arr[$variation_name]:"-"); //$this->pw_get_woo_variation($items->order_item_id);
					$datatable_value.=("</td>");	
				}
				////////////////////
				
			
				$type = 'total_row';$items_only = true; $id = $items->id;
				
				$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
				$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
				$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
				$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
				$pw_from_date=substr($pw_from_date,0,strlen($pw_from_date)-3);
				$pw_to_date=substr($pw_to_date,0,strlen($pw_to_date)-3);
				
				$params=array(
					"pw_from_date"=>$pw_from_date,
					"pw_to_date"=>$pw_to_date,
					"order_status"=>$pw_order_status,
					"pw_hide_os"=>'"trash"'
				);
				
				$items_product=$this->pw_get_product_var_items($type , $items_only, $id,$params);
				
				$month_arr='';
				$month_arr='';
				//print_r($items_product);
				foreach($items_product as $item_product){
					//$month_arr[$item_product->month_number]['total']=$item_product->total;
					//$month_arr[$item_product->month_number]['qty']=$item_product->quantity;
					$month_arr[$item_product->month_key]['total']=$item_product->total;
					$month_arr[$item_product->month_key]['qty']=$item_product->quantity;
				}
				
				//die($this->month_count."ww".$this->month_count);
				
				//$j=3;
				$total=0;
				$qty=0;
			
				//print_r($month_arr);	
				//print_r($this->data_month);
								
				foreach($this->data_month as $month_name){
				//for($i=((int)$this->month_start-1);$i<=($this->month_count+(int)$this->month_start);$i++){
					
					$pw_table_value=$this->price(0);
					if(isset($month_arr[$month_name]['total'])){
						$pw_table_value=$this->price($month_arr[$month_name]['total']) .' #'.$month_arr[$month_name]['qty'];
						$total+=$month_arr[$month_name]['total'];
						$qty+=$month_arr[$month_name]['qty'];
					}
					
					
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $pw_table_value;
					$datatable_value.=("</td>");
				}	
			
				$display_class='';
				if($this->table_cols[$j]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($total) .' #'.$qty;
				$datatable_value.=("</td>");
				

			$datatable_value.=("</tr>");
		}
	}elseif($file_used=="search_form"){
			$now_date= date("Y-m-d");
			$cur_year=substr($now_date,0,4);
			$pw_from_date= $cur_year."-01-01";
			$pw_to_date= $cur_year."-12-31";
		?>
		<form class='alldetails search_form_report' action='' method='post'>
			<input type='hidden' name='action' value='submit-form' />
			<div class="row">
				
				<div class="col-md-6">
					<div>
						<?php _e('From Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
					</div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
					<input name="pw_from_date" id="pwr_from_date" type="text" readonly='true' class="datepick" value="<?php echo $pw_from_date;?>"/>                
				</div>
				<div class="col-md-6">
					<div class="awr-form-title">
						<?php _e('To Date',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
					</div>
					<span class="awr-form-icon"><i class="fa fa-calendar"></i></span>
					<input name="pw_to_date" id="pwr_to_date" type="text" readonly='true' class="datepick"  value="<?php echo $pw_to_date;?>"/>
				</div>
            
            	<?php
               		$col_style='';
					$permission_value=$this->get_form_element_value_permission('pw_categories');
					if($this->get_form_element_permission('pw_categories') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_categories') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-tags"></i></span>
					<?php
                        $categories = $this->pw_get_woo_var_cat_data('product_cat');
                        $option='';
                        foreach($categories as $category){
							$selected="";
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($category -> id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_categories') && $permission_value!='')
								$selected="selected";*/
								
                            $option.="<option value='".$category -> id."' $selected>".$category -> label."</option>";
                        }
                    ?>
                
                    <select name="pw_categories[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_categories') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
					$permission_value=$this->get_form_element_value_permission('pw_products');
					if($this->get_form_element_permission('pw_products') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_products') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-cog"></i></span>
					<?php
                        $products = $this->pw_get_woo_var_data();
                        $option='';
                        foreach($products as $product){
							$selected="";
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($product -> id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_products') &&  $permission_value!='')
								$selected="selected";*/
								
                            $option.="<option value='".$product -> id."' $selected>".$product -> label."</option>";
                        }
                    ?>
                
                    <select name="pw_products[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_products') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
                 
                <div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Variations',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-bolt"></i></span>
					<?php
                        $attributes_available	= $this->pw_get_woo_atts('-1');
                        $option='';
                        foreach($attributes_available as $key => $value){
                            $option.="<option value='".$key."' >".$value."</option>";
                        }
                    ?>
                
                    <select name="pw_variations[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>
   
                </div>	

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