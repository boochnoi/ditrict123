<?php
	
	if($file_used=="sql_table")
	{
		$limit 				= $this->pw_get_woo_requests('limit',3,true);
		$p 					= $this->pw_get_woo_requests('p',1,true);				
		$page				= $this->pw_get_woo_requests('page',NULL);				
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status',"-1",true);
		$category_id		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id','-1',true);
		$pw_id_order_status	= $this->pw_get_woo_requests('pw_id_order_status','-1',true);	

		$pw_paid_customer		= $this->pw_get_woo_requests('pw_customers_paid','-1',true);

		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','-1',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','DESC',true);
		
		$pw_paid_customer		= $this->pw_get_woo_sm_requests('pw_customers_paid',$pw_paid_customer, "-1");
		$pw_order_status		= $this->pw_get_woo_sm_requests('pw_orders_status',$pw_order_status, "-1");
		$pw_hide_os='"trash"';
		$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");				
		$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
		$category_id 				= "-1";
		
		//GET POSTED PARAMETERS
		$request 			= array();
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		//PAID CUSTOMERS 
		$pw_paid_customer_condition='';	
		
		//PRODUCT ID
		$pw_product_id_condition='';
		$pw_cat_prod_id_string_condition='';
		
		//ORDER SATTUS
		$pw_id_order_status_join='';
		$pw_order_status_condition='';
		

		//CATEGORY ID
		$category_id_condition='';
		$category_id_join='';
		
		//ORDER STATUS
		$pw_id_order_status_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PUBLISH ORDER
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition ='';
		
		
		$sql_columns = "pw_woocommerce_order_items.order_item_name				AS 'product_name'
					,pw_woocommerce_order_items.order_item_id				AS order_item_id
					,SUM(woocommerce_order_itemmeta.meta_value)			AS 'quantity'
					,SUM(pw_woocommerce_order_itemmeta6.meta_value)		AS 'total_amount'
					,pw_woocommerce_order_itemmeta7.meta_value				AS product_id
					,pw_postmeta_customer_user.meta_value					AS customer_id
					,DATE(shop_order.post_date) 						AS post_date 
					,pw_postmeta_billing_billing_email.meta_value			AS billing_email
					,CONCAT(pw_postmeta_billing_billing_email.meta_value,' ',pw_woocommerce_order_itemmeta7.meta_value,' ',pw_postmeta_customer_user.meta_value)			AS group_column
					,CONCAT(pw_postmeta_billing_first_name.meta_value,' ',postmeta_billing_last_name.meta_value)		AS billing_name							
					";
					
		$sql_joins = "{$wpdb->prefix}woocommerce_order_items as pw_woocommerce_order_items						
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id
					LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id						
					";
		
		if($category_id  && $category_id != "-1") {
				$category_id_join = " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta7.meta_value 
					LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if($pw_id_order_status  && $pw_id_order_status != "-1") {
				$pw_id_order_status_join = " 	
					LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
					LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
					LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
		
		$sql_joins.="$category_id_join $pw_id_order_status_join ";
		
		$sql_joins .= "
		LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_first_name ON pw_postmeta_billing_first_name.post_id		=	pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as postmeta_billing_last_name ON postmeta_billing_last_name.post_id			=	pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_billing_billing_email ON pw_postmeta_billing_billing_email.post_id	=	pw_woocommerce_order_items.order_id
		LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta_customer_user ON pw_postmeta_customer_user.post_id	=	pw_woocommerce_order_items.order_id";
					
		$sql_condition = "
					woocommerce_order_itemmeta.meta_key	= '_qty'
					AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' 
					AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'
					AND pw_woocommerce_order_itemmeta7.meta_key 	= '_product_id'
					AND pw_postmeta_billing_first_name.meta_key	= '_billing_first_name'
					AND postmeta_billing_last_name.meta_key		= '_billing_last_name'
					AND pw_postmeta_billing_billing_email.meta_key	= '_billing_email'
					AND pw_postmeta_customer_user.meta_key			= '_customer_user'
					";
					
		
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition = " 
					AND (DATE(shop_order.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
		}
		
		if($pw_product_id  && $pw_product_id != "-1") 
			$pw_product_id_condition = "
					AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_product_id .")";	
		
		if($category_id  && $category_id != "-1") 
			$category_id_condition = "
					AND pw_terms.term_id IN (".$category_id .")";	
		
		
		if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1") 
			$pw_cat_prod_id_string_condition = " AND pw_woocommerce_order_itemmeta7.meta_value IN (".$pw_cat_prod_id_string .")";
		
		if($pw_id_order_status  && $pw_id_order_status != "-1") 
			$pw_id_order_status_condition = " 
					AND terms2.term_id IN (".$pw_id_order_status .")";
					
		
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition = " AND  shop_order.post_status IN ('{$in_post_status}')";
		}
		
		//echo $pw_order_status;
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND shop_order.post_status IN (".$pw_order_status.")";
			
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
				
		if($pw_paid_customer  && $pw_paid_customer != '-1' and $pw_paid_customer != "'-1'")
			$pw_paid_customer_condition = " AND pw_postmeta_billing_billing_email.meta_value IN (".$pw_paid_customer.")";
		
		$sql_group_by = " GROUP BY  group_column";
		
		$sql_order_by = " ORDER BY billing_name ASC, product_name ASC, total_amount DESC";
		
		$sql = "SELECT $sql_columns
				FROM $sql_joins
				WHERE $sql_condition $pw_from_date_condition $pw_product_id_condition $category_id_condition
				$pw_cat_prod_id_string_condition $pw_id_order_status_condition 		
				$pw_publish_order_condition $pw_order_status_condition $pw_hide_os_condition 
				$pw_paid_customer_condition $sql_group_by $sql_order_by";
		
		//echo $sql;
		
		/*
			SELECT pw_woocommerce_order_items.order_item_name	AS 'product_name' ,pw_woocommerce_order_items.order_item_id	AS order_item_id ,SUM(woocommerce_order_itemmeta.meta_value)	AS 'quantity' ,SUM(pw_woocommerce_order_itemmeta6.meta_value)	AS 'total_amount' ,pw_woocommerce_order_itemmeta7.meta_value	AS product_id ,pw_postmeta_customer_user.meta_value	AS customer_id ,DATE(shop_order.post_date) AS post_date ,pw_postmeta_billing_billing_email.meta_value	AS billing_email ,CONCAT(pw_postmeta_billing_billing_email.meta_value,' ',pw_woocommerce_order_itemmeta7.meta_value,' ',pw_postmeta_customer_user.meta_value)	AS group_column ,CONCAT(pw_postmeta_billing_first_name.meta_value,' ',postmeta_billing_last_name.meta_value)	AS billing_name,CONCAT(pw_postmeta_shipping_first_name.meta_value,' ',postmeta_shipping_last_name.meta_value)	AS shipping_name	FROM wp_woocommerce_order_items as pw_woocommerce_order_items	LEFT JOIN wp_woocommerce_order_itemmeta as woocommerce_order_itemmeta ON woocommerce_order_itemmeta.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta6 ON pw_woocommerce_order_itemmeta6.order_item_id=pw_woocommerce_order_items.order_item_id LEFT JOIN wp_woocommerce_order_itemmeta as pw_woocommerce_order_itemmeta7 ON pw_woocommerce_order_itemmeta7.order_item_id=pw_woocommerce_order_items.order_item_id	LEFT JOIN wp_posts as shop_order ON shop_order.id=pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_billing_first_name ON pw_postmeta_billing_first_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as postmeta_billing_last_name ON postmeta_billing_last_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_shipping_first_name ON pw_postmeta_shipping_first_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as postmeta_shipping_last_name ON postmeta_shipping_last_name.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_billing_billing_email ON pw_postmeta_billing_billing_email.post_id	=	pw_woocommerce_order_items.order_id LEFT JOIN wp_postmeta as pw_postmeta_customer_user ON pw_postmeta_customer_user.post_id	=	pw_woocommerce_order_items.order_id WHERE woocommerce_order_itemmeta.meta_key	= '_qty' AND pw_woocommerce_order_itemmeta6.meta_key	= '_line_total' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_woocommerce_order_itemmeta7.meta_key = '_product_id' AND pw_postmeta_billing_first_name.meta_key	= '_billing_first_name' AND postmeta_billing_last_name.meta_key	= '_billing_last_name' AND pw_postmeta_shipping_first_name.meta_key	= '_shipping_first_name' AND postmeta_shipping_last_name.meta_key	= '_shipping_last_name' AND pw_postmeta_billing_billing_email.meta_key	= '_billing_email' AND pw_postmeta_customer_user.meta_key	= '_customer_user' AND (DATE(shop_order.post_date) BETWEEN '2015-01-01' AND '2016-01-19') AND shop_order.post_status IN ('wc-completed','wc-on-hold','wc-processing') AND shop_order.post_status NOT IN ("trash") GROUP BY group_column ORDER BY billing_name ASC, product_name ASC, total_amount DESC

		*/
		
	}
	elseif($file_used=="data_table"){
							
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
				//Product SKU
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_prod_sku($items->order_item_id, $items->product_id);
				$datatable_value.=("</td>");
	
				//Customer Name
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->billing_name;
				$datatable_value.=("</td>");
				
				//Customer Email
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->billing_email;
				$datatable_value.=("</td>");
				
				//Product Name
				$display_class='';
				if($this->table_cols[3]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->product_name;
				$datatable_value.=("</td>");
				
				//Sales Qty.
				$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->quantity;
				$datatable_value.=("</td>");
				
				//Current Stock
				$pw_table_value = $this->pw_get_prod_stock_($items->order_item_id, $items->product_id);
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $pw_table_value;
				$datatable_value.=("</td>");
								
				//Amount
				$display_class='';
				if($this->table_cols[6]['status']=='hide') $display_class='display:none';
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
                
            </div>
            
            <div class="col-md-12">
                
                	
                    <?php
                    	$pw_hide_os='trash';
						$pw_publish_order='no';
						$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
					?>
                    <input type="hidden" name="list_parent_category" value="">
                    <input type="hidden" name="pw_category_id" value="-1">
                    
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