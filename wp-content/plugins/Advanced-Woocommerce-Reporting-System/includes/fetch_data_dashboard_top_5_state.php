<?php
if($file_used=="sql_table")
{
	
	//show_seleted_order_status
	global $wpdb;

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

	}
		
	$pw_create_date =  date("Y-m-d");
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
	
	$per_page=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'top_state_post_per_page',5);
	
	$pw_shop_order_status_condition ='';
	$pw_from_date_condition ='';
	$pw_hide_os_condition ='';

	$sql_columns = " SUM(pw_postmeta1.meta_value) AS 'Total' 
	,pw_postmeta2.meta_value AS 'billing_state'
	,pw_postmeta3.meta_value AS 'billing_country'
	,Count(*) AS 'OrderCount'";
	
	$sql_joins = " {$wpdb->prefix}posts as pw_posts
	LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta1 ON pw_postmeta1.post_id=pw_posts.ID
	LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta2 ON pw_postmeta2.post_id=pw_posts.ID
	LEFT JOIN  {$wpdb->prefix}postmeta as pw_postmeta3 ON pw_postmeta3.post_id=pw_posts.ID";
	
	$sql_condition = " pw_posts.post_type 	=	'shop_order'  
	AND pw_postmeta1.meta_key	=	'_order_total' 
	AND pw_postmeta2.meta_key	=	'_billing_state'
	AND pw_postmeta3.meta_key	=	'_billing_country'";
	
	if(count($pw_shop_order_status)>0){
		$pw_in_shop_os		= implode("', '",$pw_shop_order_status);
		$pw_shop_order_status_condition = " AND  pw_posts.post_status IN ('{$pw_in_shop_os}')";
	}
		
	if ($pw_from_date != NULL &&  $pw_to_date !=NULL){
		$pw_from_date_condition = " AND DATE(pw_posts.post_date) BETWEEN '{$pw_from_date}' AND '{$pw_to_date}'";
	}
	
	if(count($pw_hide_os)>0){
		$in_pw_hide_os		= implode("', '",$pw_hide_os);
		$pw_hide_os_condition = " AND  pw_posts.post_status NOT IN ('{$in_pw_hide_os}')";
	}
	
	$sql_group_by = " GROUP BY  pw_postmeta2.meta_value";
	$sql_order_by = " Order By Total DESC";
	$sql_limit =" LIMIT {$per_page}";
	
	$sql = "SELECT  $sql_columns FROM $sql_joins WHERE $sql_condition
			$pw_shop_order_status_condition $pw_from_date_condition $pw_hide_os_condition 
			$sql_group_by $sql_order_by $sql_limit";
	
	//echo $sql;
	
}elseif($file_used=="data_table"){
	
	foreach($this->results as $items){
	//for($i=1; $i<=20 ; $i++){
						
		$datatable_value.=("<tr>");
			
			//STATE	
			$pw_table_value=$this->pw_get_woo_bsn($items->billing_country,$items->billing_state);				
			$display_class='';
			if($this->table_cols[0]['status']=='hide') $display_class='display:none';
			$datatable_value.=("<td style='".$display_class."'>");
				$datatable_value.= $pw_table_value;
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
				$datatable_value.= $this->price($items->Total);
			$datatable_value.=("</td>");
			
		$datatable_value.=("</tr>");
	}
}elseif($file_used=="search_form"){}

?>