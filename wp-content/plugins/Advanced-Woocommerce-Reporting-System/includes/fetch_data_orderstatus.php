<?php
	
	if($file_used=="sql_table")
	{
		
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
		
		//ORDER SATTUS
		$pw_id_order_status_join='';
		$pw_order_status_condition='';
		
		//ORDER STATUS
		$pw_id_order_status_condition='';
		
		//DATE
		$pw_from_date_condition='';
		
		//PUBLISH ORDER
		$pw_publish_order_condition='';
		
		//HIDE ORDER STATUS
		$pw_hide_os_condition ='';
		
		$this->constants['post_order_status_found']	= 1;
		
		$sql_columns= "COUNT(postmeta.meta_value) AS 'order_count'
		,SUM(postmeta.meta_value) AS 'total_amount'";
		
		if($this->constants['post_order_status_found'] == 0 ){
			$sql_columns .= "  ,pw_terms.name As 'order_status', term_taxonomy.term_id AS 'StatusID'";
		
			$sql_joins  = " {$wpdb->prefix}posts as pw_posts";
			
			$sql_joins .= "
			LEFT JOIN  {$wpdb->prefix}term_relationships as pw_term_relationships ON pw_term_relationships.object_id=pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy as term_taxonomy ON term_taxonomy.term_taxonomy_id=pw_term_relationships.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms as pw_terms ON pw_terms.term_id=term_taxonomy.term_id";
		}else{
			$sql_columns .= "  , pw_posts.post_status As 'order_status' ,pw_posts.post_status As 'StatusID'";
			$sql_joins = " {$wpdb->prefix}posts as pw_posts";
		}
		
		$sql_joins .= "  
		LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id=pw_posts.ID";
		$sql_condition="postmeta.meta_key = '_order_total'
		AND pw_posts.post_type='shop_order'";	
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
			$pw_from_date_condition  = " AND DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		}
		
		if(strlen($pw_publish_order)>0 && $pw_publish_order != "-1" && $pw_publish_order != "no" && $pw_publish_order != "all"){
			$in_post_status		= str_replace(",","','",$pw_publish_order);
			$pw_publish_order_condition  = " AND  pw_posts.post_status IN ('{$in_post_status}')";
		}
		
		//if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")$sql .= " AND pw_posts.post_status IN (".$pw_order_status.")";
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND pw_posts.post_status NOT IN (".$pw_hide_os.")";
		
		$sql_group_by  = " Group BY order_status ORDER BY total_amount DESC";
		
		$sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition
				 $pw_from_date_condition $pw_publish_order_condition
				$pw_order_status_condition $pw_hide_os_condition 
				$sql_group_by";
		
		//echo $sql;
		
	}elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
				
				//Order Status
				$pw_table_value = isset($items->order_status) ? $items->order_status : '';
					
				if($pw_table_value=='wc-completed')
					$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
				else if($pw_table_value=='wc-refunded')
					$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
				else if($pw_table_value=='auto-draft')
					$pw_table_value = '';	
				else
					$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';	
				
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.=  str_replace("Wc-","",$pw_table_value);
				$datatable_value.=("</td>");
				
				//Order Count
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items -> order_count;
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