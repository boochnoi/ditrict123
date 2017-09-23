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
		
		$group_by = $this->pw_get_woo_requests('pw_groupby','refund_id',true);
		$refund_status_type = $this->pw_get_woo_requests('pw_refund_status_type','part_refunded',true);
		if($refund_status_type == "part_refunded"){
			if($group_by == "order_id"){
				//$_REQUEST['group_by'] = 'refund_id';
			}
		}else{
			if($group_by == "refund_id"){
				$group_by = 'order_id';
			}
		}
		
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
		
		$sql_columns='';
		$sql_joins='';
		$sql_condition='';
		
		
		if($refund_status_type == "part_refunded"){
			$sql_columns = "
			pw_posts.ID 						as refund_id
			
			,pw_posts.post_status 				as refund_status
			,pw_posts.post_date 				as refund_date				
			,pw_posts.post_excerpt 			as refund_note				
			,pw_posts.post_author				as customer_user
			
			,postmeta.meta_value 			as refund_amount
			,SUM(postmeta.meta_value) 		as total_amount
			
			,shop_order.ID 					as order_id
			,shop_order.ID 					as order_id_number
			,shop_order.post_status 		as order_status
			,shop_order.post_date 			as order_date
			,COUNT(pw_posts.ID) 				as refund_count";
							
			//echo $group_by;
			$group_sql = "";
			switch($group_by){
				case "refund_id":
					$group_sql .= ", pw_posts.ID as group_column";
					$group_sql .= ", pw_posts.ID as order_column";
					break;
				case "order_id":
					$group_sql .= ", shop_order.ID as group_column";
					$group_sql .= ", shop_order.post_author as order_column";
					break;
				case "refunded":
					$group_sql .= ", pw_posts.post_author as group_column";
					$group_sql .= ", pw_posts.post_author as order_column";
					break;
				case "daily":
					$group_sql .= ", DATE(pw_posts.post_date) as group_column";
					$group_sql .= ", DATE(pw_posts.post_date) as group_date";
					$group_sql .= ", DATE(pw_posts.post_date) as order_column";
					break;
				case "monthly":
					$group_sql .= ", CONCAT(MONTHNAME(pw_posts.post_date) , ' ',YEAR(pw_posts.post_date)) as group_column";
					$group_sql .= ", DATE(pw_posts.post_date) as order_column";
					break;
				case "yearly":
					$group_sql .= ", YEAR(pw_posts.post_date)as group_column";
					$group_sql .= ", DATE(pw_posts.post_date) as order_column";
					break;
				default:
					$group_sql .= ", pw_posts.ID as group_column";
					$group_sql .= ", pw_posts.ID as order_column";
					break;
				
			}
			//echo  $group_sql;;
			$sql_columns .= $group_sql;				
			
			$sql_joins= "
			
			{$wpdb->prefix}posts as pw_posts
							
			LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id	=	pw_posts.ID LEFT JOIN  {$wpdb->prefix}posts as shop_order ON shop_order.ID	=	pw_posts.post_parent";

		}else{
						
			$sql_columns = "
			SUM(postmeta.meta_value) 		as total_amount
			,shop_order.post_author			as customer_user
			,shop_order.ID 					as order_id
			,shop_order.ID 					as order_id_number
			,shop_order.post_status 		as order_status
			,shop_order.post_modified		as order_date
			,COUNT(shop_order.ID) 			as refund_count
			";
			
			$group_sql = "";
			switch($group_by){
				case "order_id":
					$group_sql .= ", shop_order.ID as group_column";
					$group_sql .= ", shop_order.ID as order_column";
					break;
				case "refunded":
					$group_sql .= ", shop_order.post_author as group_column";
					$group_sql .= ", shop_order.post_author as order_column";
					break;
				case "daily":
					$group_sql .= ", DATE(shop_order.post_modified) as group_column";
					$group_sql .= ", DATE(shop_order.post_modified) as group_date";
					$group_sql .= ", DATE(shop_order.post_modified) as order_column";
					break;
				case "monthly":
					$group_sql .= ", CONCAT(MONTHNAME(shop_order.post_modified) , ' ',YEAR(shop_order.post_modified)) as group_column";
					$group_sql .= ", DATE(shop_order.post_modified) as order_column";
					break;
				case "yearly":
					$group_sql .= ", YEAR(shop_order.post_modified)as group_column";
					$group_sql .= ", DATE(shop_order.post_modified) as order_column";
					break;
				default:
					$group_sql .= ", shop_order.ID as group_column";
					$group_sql .= ", shop_order.ID as order_column";
					break;
				
			}
			//echo  $group_sql;;
			$sql_columns .= $group_sql;
			
			$sql_joins = "						
			{$wpdb->prefix}posts as shop_order
			LEFT JOIN  {$wpdb->prefix}postmeta as postmeta ON postmeta.post_id	=	shop_order.ID ";	
		}
			
			
		if($pw_id_order_status  && $pw_id_order_status != "-1") {
			$pw_id_order_status_join = " 	
				LEFT JOIN  {$wpdb->prefix}term_relationships	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	shop_order.ID
				LEFT JOIN  {$wpdb->prefix}term_taxonomy			as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
				LEFT JOIN  {$wpdb->prefix}terms					as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
		$sql_joins.=$pw_id_order_status_join;
		
		if($refund_status_type == "part_refunded"){
			$sql_condition = "pw_posts.post_type = 'shop_order_refund' AND  postmeta.meta_key='_refund_amount'";
			
			if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
				$pw_from_date_condition = " 
						AND (DATE(pw_posts.post_date) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
			}
			
			if($pw_id_order_status  && $pw_id_order_status != "-1"){
				$pw_refunded_id 	= $this->pw_get_woo_old_orders_status(array('refunded'), array('wc-refunded'));
				$pw_refunded_id    = implode(",".$pw_refunded_id);
				$pw_id_order_status_condition = " AND terms2.term_id NOT IN (".$pw_refunded_id .")";
				
				if($pw_id_order_status  && $pw_id_order_status != "-1"){
					$pw_id_order_status_condition .= " AND terms2.term_id IN (".$pw_id_order_status .")";
				}
			}else{
				$pw_id_order_status_condition = " AND shop_order.post_status NOT IN ('wc-refunded')";
				if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'"){
					$pw_id_order_status_condition .= " AND shop_order.post_status IN (".$pw_order_status.")";
				}
			}
		}else{
			$sql_condition = " shop_order.post_type = 'shop_order' AND  postmeta.meta_key='_order_total'";
			
			if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
				$pw_from_date_condition = " 
						AND (DATE(shop_order.post_modified) BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."')";
			}
			
			if($pw_id_order_status  && $pw_id_order_status != "-1"){
				$pw_refunded_id 	= $this->pw_get_woo_old_orders_status(array('refunded'), array('wc-refunded'));
				$pw_refunded_id    = implode(",".$pw_refunded_id);
				$pw_id_order_status_condition = " AND terms2.term_id IN (".$pw_refunded_id .")";
			}else{
				$pw_id_order_status_condition .= " AND shop_order.post_status IN ('wc-refunded')";
			}
		}					
		
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND shop_order.post_status NOT IN (".$pw_hide_os.")";//changed 20141013
		
		$sql_group_by = " GROUP BY  group_column";			
		
		$sql_order_by = " ORDER BY order_column DESC";
		
		$sql = "SELECT $sql_columns
				FROM $sql_joins
				WHERE $sql_condition $pw_from_date_condition $pw_id_order_status_condition
				$pw_hide_os_condition $sql_group_by $sql_order_by";
		
		//echo $sql;
		
		if($refund_status_type=='part_refunded')
		{	
			switch($group_by){
				case "refund_id":
					$this->refund_status="refunddetails_part_refunded_main";
				break;
				
				case "order_id":
					$this->refund_status="refunddetails_part_refunded_order_id";
				break;
				
				case "refunded":
					$this->refund_status="refunddetails_part_refunded";
				break;
				
				case "daily":
					$this->refund_status="refunddetails_part_refunded_daily";
				break;
				
				case "monthly":
					$this->refund_status="refunddetails_part_refunded_monthly";
				break;
				
				case "yearly":
					$this->refund_status="refunddetails_part_refunded_yearly";
				break;
			}
		}
		else{
			switch($group_by){
				case $group_by=="refund_id" || $group_by=="order_id":
					$this->refund_status="refunddetails_status_refunded_main";
				break;
				
				case "refunded":
					$this->refund_status="refunddetails_status_refunded";
				break;
				
				case "daily":
					$this->refund_status="refunddetails_status_daily";
				break;
				
				case "monthly":
					$this->refund_status="refunddetails_status_monthly";
				break;
				
				case "yearly":
					$this->refund_status="refunddetails_status_yearly";
				break;
			}
		}
		
		//echo $sql;
		
	}
	elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
			$datatable_value.=("<tr>");
								
								
			$type_refund=$this->refund_status;
			
			
			switch($type_refund){
				case "refunddetails_status_refunded_main":
				{
					//Order ID
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->order_id;
					$datatable_value.=("</td>");
		
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
					$datatable_value.=("</td>");
					
					//Order Status
					$pw_table_value = isset($items->order_status) ? $items->order_status : '';
						
					if($pw_table_value=='wc-completed')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else if($pw_table_value=='wc-refunded')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';	
							
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= str_replace("Wc-","",$pw_table_value);
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[4]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_status_refunded":
				{
					
					$customer = new WP_User( $items->customer_user );
					//Refund By
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $customer->user_nicename;// $items->customer_user;
						//$this->get_items_id_list($original_data,'customer_user','','string');
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_status_daily":
				{
					
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_status_monthly":
				{
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_status_yearly":
				{
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->group_column;
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				///////////////////////
				
				case "refunddetails_part_refunded_main":
				{
					//Refund ID
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_id;
					$datatable_value.=("</td>");
		
					//Refund Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->refund_date));
					$datatable_value.=("</td>");
					
					//Refund Status
					$pw_table_value = isset($items->refund_status) ? $items->refund_status : '';
						
					if($pw_table_value=='wc-completed')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else if($pw_table_value=='wc-refunded')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';	
							
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= str_replace("Wc-","",$pw_table_value);
					$datatable_value.=("</td>");
					
					
					//Refund By
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->customer_user; //$this->get_items_id_list($original_data,'customer_user','','string');
					$datatable_value.=("</td>");
					
					//Order id
					$display_class='';
					if($this->table_cols[4]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->order_id;
					$datatable_value.=("</td>");
					
					
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[5]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
					$datatable_value.=("</td>");
					
					//Order Status
					$pw_table_value = isset($items->order_status) ? $items->order_status : '';
						
					if($pw_table_value=='wc-completed')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else if($pw_table_value=='wc-refunded')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';	
							
					$display_class='';
					if($this->table_cols[6]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= str_replace("Wc-","",$pw_table_value);
					$datatable_value.=("</td>");
					
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[7]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_note;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[7]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");

				}
				break;
				
				case "refunddetails_part_refunded_order_id":
				{
					//Order ID
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->order_id;
					$datatable_value.=("</td>");
		
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->order_date));
					$datatable_value.=("</td>");
					
					//Order Status
					$pw_table_value = isset($items->order_status) ? $items->order_status : '';
						
					if($pw_table_value=='wc-completed')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else if($pw_table_value=='wc-refunded')
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';
					else
						$pw_table_value = '<span class="awr-order-status awr-order-status-'.sanitize_title($pw_table_value).'" >'.ucwords(__($pw_table_value, __PW_REPORT_WCREPORT_TEXTDOMAIN__)).'</span>';	
							
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= str_replace("Wc-","",$pw_table_value);
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[3]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[4]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_part_refunded":
				{
					
					//Refund By
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->customer_user; //$this->get_items_id_list($original_data,'customer_user','','string');
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_part_refunded_daily":
				{
					
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->refund_date));
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_part_refunded_monthly":
				{
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= date($date_format,strtotime($items->refund_date));
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
				
				case "refunddetails_part_refunded_yearly":
				{
					//Order Date
					$date_format		= get_option( 'date_format' );
	
					$display_class='';
					if($this->table_cols[0]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->group_column;
					$datatable_value.=("</td>");
					
					//Order Counts
					$display_class='';
					if($this->table_cols[1]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->refund_count;
					$datatable_value.=("</td>");
					
					//Refund Amount
					$display_class='';
					if($this->table_cols[2]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $items->total_amount == 0 ? 0 : $this->price($items->total_amount);
					$datatable_value.=("</td>");
				}
				break;
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
                        <?php _e('Refund Type',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-check"></i></span>
                    <select name="pw_refund_status_type" id="refund_type" class="refund_type">
                        <option value="part_refunded">Part Refund - Order status not refunded</option>
                        <option value="status_refunded" selected="selected">Order Status Refunded</option>
                    </select>	
                </div>
                
                
                <!--<div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Show Refund Note',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					
                    <div class="col-md-9 sor">
                        <input name="pw_note_show" type="checkbox" class="show_note"/>
                    </div>
                </div>-->
                <div class="col-md-6">
                    <div class="awr-form-title">
                        <?php _e('Group By',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-suitcase"></i></span>
                    <select name="pw_groupby" id="pw_groupby">
                        <option value="refund_id" selected="selected">Refund ID</option>
                        <option value="order_id">Order ID</option>
                        <option value="refunded">Refunded</option>
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
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