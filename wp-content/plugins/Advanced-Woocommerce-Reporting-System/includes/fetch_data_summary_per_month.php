<?php
	
	if($file_used=="sql_table")
	{
		//GET POSTED PARAMETERS
		$start				= 0;
		$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
		$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
		
		$pw_from_date=substr($pw_from_date,0,strlen($pw_from_date)-3);
		$pw_to_date=substr($pw_to_date,0,strlen($pw_to_date)-3);

		$pw_product_id			= $this->pw_get_woo_requests('pw_product_id',"-1",true);
		$category_id 		= $this->pw_get_woo_requests('pw_category_id','-1',true);
		$pw_cat_prod_id_string = $this->pw_get_woo_pli_category($category_id,$pw_product_id);
		$category_id 				= "-1";
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','item_name',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
				
		///////////HIDDEN FIELDS////////////
		//$pw_hide_os	= $this->pw_get_woo_sm_requests('pw_hide_os',$pw_hide_os, "-1");
		$pw_hide_os='"trash"';
		$pw_publish_order='no';
		
		$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
		//////////////////////
		
		//ORDER Status ID
		$pw_id_order_status_condition='';
		$pw_id_order_status_join ='';
		
		//START DATE CONDITION
		$pw_from_date_condition='';
		
		//ORDER STATUS
		$pw_order_status_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition='';
	
		
		$sql_columns = "
		SUM(pw_postmeta2.meta_value)						as total
		,COUNT(shop_order.ID) 							as quantity
		
		,MONTH(shop_order.post_date) 					as month_number
		,DATE_FORMAT(shop_order.post_date, '%Y-%m')		as month_key";
		
		$sql_joins="
		{$wpdb->prefix}posts as shop_order					
		LEFT JOIN	{$wpdb->prefix}postmeta as pw_postmeta2 on pw_postmeta2.post_id = shop_order.ID";
		
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	shop_order.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 				as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
		$sql_condition = "shop_order.post_type	= 'shop_order'";
	
		$sql_condition .= " AND pw_postmeta2.meta_value > 0";

		
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND shop_order.post_status IN (".$pw_order_status.")";
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition= " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL)	
			$pw_from_date_condition= " AND DATE_FORMAT(shop_order.post_date, '%Y-%m') BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
			
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_condition= "
			AND pw_term_taxonomy2.taxonomy LIKE('shop_order_status')
			AND terms2.term_id IN (".$pw_id_order_status .")";
		}	
						
		$sql_group_by = " group by month_number ";
		$sql_order_by = "ORDER BY month_number";
		
		$sql = "SELECT $sql_columns FROM $sql_joins $pw_id_order_status_join
			WHERE $sql_condition $pw_order_status_condition $pw_hide_os_condition 
			$pw_from_date_condition $pw_id_order_status_condition 
			$sql_group_by $sql_order_by
		";
		
		//echo $sql;
		
	}elseif($file_used=="data_table"){
		
		$reports		= $this->pw_get_woo_requests('reports','-1',true);
		$array = array(
			"0"  => array("item_name"=>__("Order Total",			'icwoocommerce_textdomains'),"id"=>"_order_total")
			,"1" => array("item_name"=>__("Order Tax",				'icwoocommerce_textdomains'),"id"=>"_order_tax")					
			,"2" => array("item_name"=>__("Order Discount",			'icwoocommerce_textdomains'),"id"=>"_order_discount")
			,"3" => array("item_name"=>__("Cart Discount",			'icwoocommerce_textdomains'),"id"=>"_cart_discount")
			,"4" => array("item_name"=>__("Order Shipping",			'icwoocommerce_textdomains'),"id"=>"_order_shipping")
			,"5" => array("item_name"=>__("Order Shipping Tax",		'icwoocommerce_textdomains'),"id"=>"_order_shipping_tax")
			,"6" => array("item_name"=>__("Product Sales",			'icwoocommerce_textdomains'),"id"=>"_by_product")
		);
		
		if($reports != '-1'){
			$reports = explode(",", $reports);
				$new_array = array();
				foreach($reports as $key => $value)
					$new_array[] = $array[$value];
				$array = $new_array;
		}
		
		//foreach($this->results as $items){
		for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				//Reports
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Reports';
				$datatable_value.=("</td>");
				
				
				//$items = $this->pw_get_woo_items_sale($type,$items_only,$id);
				
				//Jan
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Jan';
				$datatable_value.=("</td>");
							
				//Feb
				$display_class='';
				if($this->table_cols[3]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Feb';
				$datatable_value.=("</td>");
				
				//Mar
				$display_class='';
				if($this->table_cols[4]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Mar';
				$datatable_value.=("</td>");
				
				//Apr
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Apr';
				$datatable_value.=("</td>");
				
				//May
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'May';
				$datatable_value.=("</td>");
				
				//Jun
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Jun';
				$datatable_value.=("</td>");
										
				//Jul
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Jul';
				$datatable_value.=("</td>");
				
				//Aug
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Aug';
				$datatable_value.=("</td>");
				
				//Sep
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Sep';
				$datatable_value.=("</td>");
				
				//Oct
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Oct';
				$datatable_value.=("</td>");
				
				//Nov
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Nov';
				$datatable_value.=("</td>");
						
				//Dec
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Dec';
				$datatable_value.=("</td>");
				
				//Total
				$display_class='';
				if($this->table_cols[5]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= 'Total';
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
             
             	<div class="col-md-6">
                	<div class="awr-form-title">
						<?php _e('Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-flag"></i></span>
					<?php
                        $reports = array(
                            "0"=>__("Order Total",				'icwoocommerce_textdomains'),
                            "1"=>__("Order Tax",				'icwoocommerce_textdomains'),
                            "2"=>__("Order Discount",			'icwoocommerce_textdomains'),
                            "3"=>__("Cart Discount",			'icwoocommerce_textdomains'),
                            "4"=>__("Order Shipping",			'icwoocommerce_textdomains'),
                            "5"=>__("Order Shipping Tax",		'icwoocommerce_textdomains'),
                            "6"=>__("Product Sales",			'icwoocommerce_textdomains')
                        );
                        
                        $option='';
                        foreach($reports as $key => $value){
                            $option.="<option value='".$key."' >".$value."</option>";
                        }
                    ?>
                    <select name="reports[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    
                </div>	
                
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
					<?php
                        $pw_order_status=$this->pw_get_woo_orders_statuses();

                        $option='';
                        foreach($pw_order_status as $key => $value){
                            $option.="<option value='".$key."' >".$value."</option>";
                        }
                    ?>
                
                    <select name="pw_orders_status[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <option value="-1"><?php _e('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?></option>
                        <?php
                            echo $option;
                        ?>
                    </select>  
                    <input type="hidden" name="pw_id_order_status[]" id="pw_id_order_status" value="-1">
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