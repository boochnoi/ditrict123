<?php
	
	if($file_used=="sql_table")
	{
		$pw_create_date =  date("Y-m-d");
		$pw_from_date=$this->pw_from_date_dashboard;
		$pw_to_date=$this->pw_to_date_dashboard;
		
		$pw_hide_os=$this->otder_status_hide;
		$pw_shop_order_status=$this->pw_shop_status;
		
		if(isset($_POST['pw_from_date']))
		{
			//parse_str($_REQUEST, $my_array_of_vars);
			$this->search_form_fields=$_POST;
	
			$pw_from_date		  = $this->pw_get_woo_requests('pw_from_date',NULL,true);
			$pw_to_date			= $this->pw_get_woo_requests('pw_to_date',NULL,true);
			$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
			$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
			$pw_create_date =  $pw_to_date;
		}
		
		
		$pw_url_shop_order_status	= "";
		$pw_in_shop_os	= "";
		$pw_in_post_os	= "";
		
		
		//$pw_hide_os='trash';
		//$pw_hide_os	= $this->pw_get_woo_requests('pw_hide_os',$pw_hide_os,true);
		$pw_hide_os=explode(',',$pw_hide_os);		
		//$pw_shop_order_status="wc-completed,wc-on-hold,wc-processing";
		//$pw_shop_order_status	= $this->pw_get_woo_requests('shop_order_status',$pw_shop_order_status,true);
		if(strlen($pw_shop_order_status)>0 and $pw_shop_order_status != "-1") 
			$pw_shop_order_status = explode(",",$pw_shop_order_status); 
		else $pw_shop_order_status = array();
		
		if(count($pw_shop_order_status)>0){
			$pw_in_post_os	= implode("', '",$pw_shop_order_status);	
		}
		
		$in_pw_hide_os = "";
		if(count($pw_hide_os)>0){
			$in_pw_hide_os		= implode("', '",$pw_hide_os);				
		}	

		/*Today*/
		$sql_columns = " SUM(postmeta.meta_value)AS 'OrderTotal' 
		,COUNT(*) AS 'OrderCount'
		,'Today' AS 'SalesOrder'";
		
		$sql_joins = " {$wpdb->prefix}postmeta as postmeta 
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=postmeta.post_id";
		
		if(strlen($pw_in_shop_os)>0){
			$pw_in_shop_os_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " meta_key='_order_total' 
		AND DATE(pw_posts.post_date) = '".$pw_create_date."' AND pw_posts.post_type IN ('shop_order')";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_condition .= " AND  term_taxonomy.term_id IN ({$pw_in_shop_os})";
		}
		
		if(strlen($pw_in_post_os)>0){
			$sql_condition .= " AND  pw_posts.post_status IN ('{$pw_in_post_os}')";
		}
		
		if(strlen($in_pw_hide_os)>0){
			$sql_condition .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
		}
		
		$pw_today_sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition";
		
			 
		/*Yesterday*/
		$sql_columns = " SUM(postmeta.meta_value)AS 'OrderTotal' 
		,COUNT(*) AS 'OrderCount'
		,'Yesterday' AS 'Sales Order'";
		
		$sql_joins=" {$wpdb->prefix}postmeta as postmeta LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=postmeta.post_id";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_joins .= " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " meta_key='_order_total' AND  DATE(pw_posts.post_date)= DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))
		 AND pw_posts.post_type IN ('shop_order')";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_condition .= " AND  term_taxonomy.term_id IN ({$pw_in_shop_os})";
		}
		
		if(strlen($pw_in_post_os)>0){
			$sql_condition .= " AND  pw_posts.post_status IN ('{$pw_in_post_os}')";
		}
		
		if(strlen($in_pw_hide_os)>0){
			$sql_condition .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
		}
					
		$pw_yesterday_sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition";
			
		/*Week*/	
		$sql_columns = " SUM(postmeta.meta_value)AS 'OrderTotal' 
		,COUNT(*) AS 'OrderCount'
		,'Week' AS 'Sales Order'";
		
		$sql_joins = " {$wpdb->prefix}postmeta as postmeta 
		LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=postmeta.post_id";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_joins .= " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " meta_key='_order_total' AND WEEK(CURDATE()) = WEEK(DATE(pw_posts.post_date)) AND YEAR(CURDATE()) = YEAR(pw_posts.post_date) AND pw_posts.post_type IN ('shop_order')";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_condition .= " AND  term_taxonomy.term_id IN ({$pw_in_shop_os})";
		}
		
		if(strlen($pw_in_post_os)>0){
			$sql_condition .= " AND  pw_posts.post_status IN ('{$pw_in_post_os}')";
		}
		
		
		if(strlen($in_pw_hide_os)>0){
			$sql_condition .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
		}
				
		$pw_week_sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition";
		
		/*Month*/
		$sql_columns = " SUM(postmeta.meta_value)AS 'OrderTotal' ,COUNT(*) AS 'OrderCount','Month' AS 'Sales Order'";
		
		$sql_joins = " {$wpdb->prefix}postmeta as postmeta LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=postmeta.post_id";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_joins .= " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " meta_key='_order_total' 
		AND MONTH(DATE(CURDATE())) = MONTH( DATE(pw_posts.post_date))					
		AND YEAR(DATE(CURDATE())) = YEAR( DATE(pw_posts.post_date))
		AND pw_posts.post_type IN ('shop_order')";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_condition .= " AND  term_taxonomy.term_id IN ({$pw_in_shop_os})";
		}
		
		if(strlen($pw_in_post_os)>0){
			$sql_condition .= " AND  pw_posts.post_status IN ('{$pw_in_post_os}')";
		}
		
		if(strlen($in_pw_hide_os)>0){
			$sql_condition .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
		}
		
		$pw_month_sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition";
				
		/*Year*/
		$sql_columns = " SUM(postmeta.meta_value)AS 'OrderTotal' ,COUNT(*) AS 'OrderCount','Year' AS 'Sales Order'";
		
		$sql_joins = " {$wpdb->prefix}postmeta as postmeta LEFT JOIN  {$wpdb->prefix}posts as pw_posts ON pw_posts.ID=postmeta.post_id";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_joins .= " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_posts.ID
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id";
		}
		
		$sql_condition = " meta_key='_order_total' AND YEAR(DATE(CURDATE())) = YEAR( DATE(pw_posts.post_date)) AND pw_posts.post_type IN ('shop_order')";
		
		if(strlen($pw_in_shop_os)>0){
			$sql_condition .= " AND  term_taxonomy.term_id IN ({$pw_in_shop_os})";
		}
		
		if(strlen($pw_in_post_os)>0){
			$sql_condition .= " AND  pw_posts.post_status IN ('{$pw_in_post_os}')";
		}
			
		if(strlen($in_pw_hide_os)>0){
			$sql_condition .= " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
		}
		
		$pw_year_sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition";
		
		$sql = '';				
		$sql .= $pw_today_sql;
		$sql .= " UNION ";
		$sql .= $pw_yesterday_sql;
		$sql .= " UNION ";
		$sql .= $pw_week_sql;
		$sql .= " UNION ";
		$sql .= $pw_month_sql;
		$sql .= " UNION ";
		$sql .= $pw_year_sql;
		
		//echo $sql;
		
	}elseif($file_used=="data_table"){
		
		foreach($this->results as $items){
		//for($i=1; $i<=20 ; $i++){
							
			$datatable_value.=("<tr>");
									
				//Month
				$display_class='';
				if($this->table_cols[0]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->SalesOrder;
				$datatable_value.=("</td>");
				
				//Target Sales
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->OrderCount;
				$datatable_value.=("</td>");
				
				//Actual Sales
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->price($items->OrderTotal);
				$datatable_value.=("</td>");
				
			$datatable_value.=("</tr>");
		}
	}elseif($file_used=="search_form"){}
	
?>