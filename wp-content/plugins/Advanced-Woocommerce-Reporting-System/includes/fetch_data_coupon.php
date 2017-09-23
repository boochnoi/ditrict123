<?php
	
	if($file_used=="sql_table")
	{
		
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_coupon_code		= $this->pw_get_woo_requests('coupon_code','-1',true);	
		$pw_coupon_codes	= $this->pw_get_woo_requests('pw_codes_of_coupon','-1',true);	
		if($pw_coupon_codes!="-1")
			$pw_coupon_codes  		= "'".str_replace(",","','",$pw_coupon_codes)."'";
		$coupon_discount_types		= $this->pw_get_woo_requests('pw_coupon_discount_types','-1',true);	
		if($coupon_discount_types!="-1")
			$coupon_discount_types  		= "'".str_replace(",","','",$coupon_discount_types)."'";
		$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code','-1',true);	
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		if($pw_order_status!="-1")
			$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		
		//COPUN DISCOUNT
		$coupon_discount_types_join='';
		$coupon_discount_types_condition_1='';
		$coupon_discount_types_condition_2='';
		
		//DATE
		$pw_from_date_condition='';
		
		//ORDER STATUS ID
		$pw_id_order_status_join='';
		$pw_id_order_status_condition='';
		$pw_order_status_condition='';
		
		//PUBLISH
		$pw_publish_order_condition='';
		
		//COUPON COED
		$pw_coupon_code_condition='';
		$pw_coupon_codes_condition ='';
		
		//COUNTRY
		$pw_country_code_condition='';
		
		//HIDE ORDER
		$pw_hide_os_condition ='';
		
		$sql_columns = "
		pw_woocommerce_order_items.order_item_name				AS		'order_item_name',
		pw_woocommerce_order_items.order_item_name				AS		'coupon_code', 
		SUM(woocommerce_order_itemmeta.meta_value) 			AS		'total_amount', 
		SUM(woocommerce_order_itemmeta.meta_value) 				AS 		'coupon_amount' , 
		Count(*) 											AS 		'coupon_count'";
		
		$sql_joins = "
		{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items 
		LEFT JOIN	{$wpdb->prefix}posts	as pw_posts ON pw_posts.ID = pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
		";
		
		
		if($coupon_discount_types && $coupon_discount_types != "-1"){
			$coupon_discount_types_join = " LEFT JOIN	{$wpdb->prefix}posts	as coupons ON coupons.post_title = pw_woocommerce_order_items.order_item_name";
			$coupon_discount_types_join .= " LEFT JOIN	{$wpdb->prefix}postmeta	as pw_coupon_discount_type ON pw_coupon_discount_type.post_id = coupons.ID";
		}
		
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
				$pw_id_order_status_join = " 
				LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = "
		pw_posts.post_type 								=	'shop_order'
		AND pw_woocommerce_order_items.order_item_type		=	'coupon' 
		AND woocommerce_order_itemmeta.meta_key			=	'discount_amount'";
		
		if($coupon_discount_types && $coupon_discount_types != "-1"){
			$coupon_discount_types_condition_1 = " AND coupons.post_type 				=	'shop_coupon'";
			$coupon_discount_types_condition_1 .= " AND pw_coupon_discount_type.meta_key		=	'discount_type'";
		}
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition = " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		}
		
		if(strlen($pw_id_order_status)>0 && $pw_id_order_status != "-1" && $pw_id_order_status != "no" && $pw_id_order_status != "all"){
			$pw_id_order_status_condition = " AND  term_taxonomy.term_id IN ({$pw_id_order_status})";
		}
		
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition= " AND  pw_posts.post_status IN ('{$in_post_status}')";
		}
		
		if($pw_coupon_code && $pw_coupon_code != "-1"){
			$pw_coupon_code_condition = " AND (pw_woocommerce_order_items.order_item_name IN ('{$pw_coupon_code}') OR pw_woocommerce_order_items.order_item_name LIKE '%{$pw_coupon_code}%')";
		}
		
		if($pw_coupon_codes && $pw_coupon_codes != "-1"){
			$pw_coupon_codes_condition = " AND pw_woocommerce_order_items.order_item_name IN ({$pw_coupon_codes})";
		}
		
		if($coupon_discount_types && $coupon_discount_types != "-1"){
			$coupon_discount_types_condition_2 = " AND pw_coupon_discount_type.meta_value IN ({$coupon_discount_types})";
		}
		
		if($pw_country_code && $pw_country_code != "-1"){
			$pw_country_code_condition= " AND billing_country.meta_value IN ({$pw_country_code})";
		}
		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND pw_posts.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN (".$pw_hide_os.")";
		
		/*if($request['report_name'] == "coupon_couontry_page"){
			$sql_group_by = " Group BY billing_country.meta_value, pw_woocommerce_order_items.order_item_name";
			//$sql .= " Group BY   pw_woocommerce_order_items.order_id";
			$sql_order_by = " ORDER BY {$pw_sort_by} {$pw_order_by}";
		}else{
			$sql_group_by = " Group BY pw_woocommerce_order_items.order_item_name";
			$sql_order_by = " ORDER BY total_amount DESC";
		}	*/
		$sql_group_by = " Group BY pw_woocommerce_order_items.order_item_name";
		$sql_order_by = " ORDER BY total_amount DESC";
			
		$sql = "SELECT $sql_columns
				FROM $sql_joins $coupon_discount_types_join $pw_id_order_status_join
				WHERE $sql_condition $coupon_discount_types_condition_1 $pw_from_date_condition 
				$pw_id_order_status_condition $pw_publish_order_condition $pw_coupon_code_condition
				$pw_coupon_codes_condition $coupon_discount_types_condition_2
				$pw_country_code_condition $pw_order_status_condition $pw_hide_os_condition 
				$sql_group_by $sql_order_by";			
		
		//echo $sql;
	}
	elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
										
				//Coupon Code
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->coupon_code;
				$datatable_value.=("</td>");
	
				//Coupon Count
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->coupon_count;
				$datatable_value.=("</td>");
				
				//Coupon Amount
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->coupon_amount == 0 ? 0 : $this->price($items->coupon_amount);
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
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                    	<?php
                        	$pw_coupon_codes=$this->pw_get_woo_coupons_codes();
							$option='';
							foreach($pw_coupon_codes as $coupon){
								$selected='';
								/*if($current_product==$product->id)
									$selected="selected";*/
								$option.="<option $selected value='".$coupon -> id."' >".$coupon -> label." </option>";
							}
						?>
                        <?php _e('Coupon Codes',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-key"></i></span>
                    <select name="pw_codes_of_coupon[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  	
                </div>
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Discount Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-money"></i></span>
                    <select name="pw_coupon_discount_types" >
                        <option value="-1">Select One</option>
                        <option value="fixed_cart">Cart Discount</option>
                        <option value="percent">Cart % Discount</option>
                        <option value="fixed_product">Product Discount</option>
                        <option value="percent_product">Product % Discount</option>
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