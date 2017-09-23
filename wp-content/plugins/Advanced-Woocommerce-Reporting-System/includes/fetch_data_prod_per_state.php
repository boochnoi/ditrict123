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
		
		$pw_sort_by 			= $this->pw_get_woo_requests('sort_by','item_name',true);
		$pw_order_by 			= $this->pw_get_woo_requests('order_by','ASC',true);
		
		$pw_id_order_status 	= $this->pw_get_woo_requests('pw_id_order_status',NULL,true);
		$pw_order_status		= $this->pw_get_woo_requests('pw_orders_status','-1',true);
		//$pw_order_status  		= "'".str_replace(",","','",$pw_order_status)."'";
		
		$pw_country_code		= $this->pw_get_woo_requests('pw_countries_code','-1',true);
		
		/*if($pw_country_code != NULL  && $pw_country_code != '-1')
		{
			$pw_country_code = str_replace(",", "','",$pw_country_code);
		}*/
		
		$state_code		= $this->pw_get_woo_requests('pw_states_code','-1',true);
		
		/*if($state_code != NULL  && $state_code != '-1')
		{
			$state_code = str_replace(",", "','",$state_code);
		}*/
		
		$pw_region_code="-1";

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
		
		$permission_value=$this->get_form_element_value_permission('pw_countries_code',$key);
		$permission_enable=$this->get_form_element_permission('pw_countries_code',$key);
		
		if($permission_enable && $pw_country_code=='-1' && $permission_value!=1){
			$pw_country_code=implode(",",$permission_value);
		}
		if($pw_country_code != NULL  && $pw_country_code != '-1')
			$pw_country_code  		= "'".str_replace(",","','",$pw_country_code)."'";
		
		$permission_value=$this->get_form_element_value_permission('pw_states_code',$key);
		$permission_enable=$this->get_form_element_permission('pw_states_code',$key);
		
		if($permission_enable && $state_code=='-1' && $permission_value!=1){
			$state_code=implode(",",$permission_value);
		}
		if($state_code != NULL  && $state_code != '-1')
			$state_code  		= "'".str_replace(",","','",$state_code)."'";
		
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
					
					$lbl_join=$tax."_join";
					$lbl_con=$tax."_condition";
					
					${$lbl_join} ='';
					${$lbl_con} = '';
					
					if(${$tax}  && ${$tax} != "-1") {
						${$lbl_join} = "
							LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships$i 	ON pw_term_relationships$i.object_id		=	pw_woocommerce_order_itemmeta_product.meta_value 
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
		
		//Start Date
		$pw_from_date_condition ="";
		$category_id_condition="";
		$category_id_join=""; 
		
		//CATEGORY PRODUCT ID STRING
		$pw_cat_prod_id_string_condition ="";
		
		//ORDER Status
		$pw_id_order_status_condition ="";
		$pw_id_order_status_join="";
		
		//PRODUCT ID
		$pw_product_id_condition ="";
		
		//COUNTRY CODE
		$pw_country_code_condition ="";
		
		//State CODE
		$state_code_condition ="";
		$pw_order_status_condition ="";
		$pw_hide_os_condition="";
			
		$sql_columns = "
			pw_woocommerce_order_itemmeta_product.meta_value 			as id
			,pw_woocommerce_order_items.order_item_name 				as product_name
			,pw_woocommerce_order_itemmeta_product.meta_value 			as product_id
			,pw_woocommerce_order_items.order_item_id 					as order_item_id
			,pw_woocommerce_order_items.order_item_name 				as item_name
			
			
			,SUM(pw_woocommerce_order_itemmeta_product_total.meta_value) 	as total
			,SUM(pw_woocommerce_order_itemmeta_product_qty.meta_value) 	as quantity";
			
		
		$sql_columns .= "
			,billing_state.meta_value as billing_state
			,CONCAT(billing_country.meta_value,'-',billing_state.meta_value) as month_key
			,CONCAT(billing_country.meta_value,'-',billing_state.meta_value) as state_code
			,billing_state.meta_value as month_number ";
			
		$sql_columns .= " 	
			,billing_country.meta_value as billing_country";
		
		$sql_joins  = " {$wpdb->prefix}woocommerce_order_items 			as pw_woocommerce_order_items
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product 			ON pw_woocommerce_order_itemmeta_product.order_item_id=pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}posts 						as shop_order 									ON shop_order.id								=	pw_woocommerce_order_items.order_id
			
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product_total 	ON pw_woocommerce_order_itemmeta_product_total.order_item_id=pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta 	as pw_woocommerce_order_itemmeta_product_qty		ON pw_woocommerce_order_itemmeta_product_qty.order_item_id		=	pw_woocommerce_order_items.order_item_id
			LEFT JOIN  {$wpdb->prefix}postmeta 						as billing_country 								ON billing_country.post_id									=	shop_order.ID 
			LEFT JOIN  {$wpdb->prefix}postmeta 						as billing_state 								ON billing_state.post_id									=	shop_order.ID
			
			";
		
		
		if($category_id != NULL  && $category_id != "-1"){
			$category_id_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships 	ON pw_term_relationships.object_id		=	pw_woocommerce_order_itemmeta_product.meta_value
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	pw_term_relationships.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 				as pw_terms 				ON pw_terms.term_id					=	term_taxonomy.term_id";
		}
		
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_join = " 
			LEFT JOIN  {$wpdb->prefix}term_relationships 	as pw_term_relationships2 	ON pw_term_relationships2.object_id	=	pw_woocommerce_order_items.order_id
			LEFT JOIN  {$wpdb->prefix}term_taxonomy 		as pw_term_taxonomy2 		ON pw_term_taxonomy2.term_taxonomy_id	=	pw_term_relationships2.term_taxonomy_id
			LEFT JOIN  {$wpdb->prefix}terms 				as terms2 				ON terms2.term_id					=	pw_term_taxonomy2.term_id";
		}
		
				
		
		$sql_condition  = "
		pw_woocommerce_order_itemmeta_product.meta_key		=	'_product_id'
		AND pw_woocommerce_order_items.order_item_type		=	'line_item'
		AND shop_order.post_type						=	'shop_order'
		
		AND billing_country.meta_key							=	'_billing_country'
		AND pw_woocommerce_order_itemmeta_product_total.meta_key		='_line_total'
		AND pw_woocommerce_order_itemmeta_product_qty.meta_key			=	'_qty'
		AND billing_state.meta_key							=	'_billing_state'";
		
		if ($pw_from_date != NULL &&  $pw_to_date !=NULL)
			$pw_from_date_condition = " AND DATE_FORMAT(shop_order.post_date, '%Y-%m') BETWEEN '".$pw_from_date."' AND '". $pw_to_date ."'";
		
		
		if($category_id  != NULL && $category_id != "-1"){
			
			$category_id_condition = " 
			AND term_taxonomy.taxonomy LIKE('product_cat')
			AND pw_terms.term_id IN (".$category_id .")";
		}
		
		if($pw_cat_prod_id_string  && $pw_cat_prod_id_string != "-1") 
			$pw_cat_prod_id_string_condition = " AND pw_woocommerce_order_itemmeta_product.meta_value IN (".$pw_cat_prod_id_string .")";
		
		if($pw_id_order_status != NULL  && $pw_id_order_status != '-1'){
			$pw_id_order_status_condition .= "
			AND pw_term_taxonomy2.taxonomy LIKE('shop_order_status')
			AND terms2.term_id IN (".$pw_id_order_status .")";
		}
		
		if($pw_product_id != NULL  && $pw_product_id != '-1'){
			$pw_product_id_condition  = "
			AND pw_woocommerce_order_itemmeta_product.meta_value IN ($pw_product_id)";
		}
		
		if($pw_country_code != NULL  && $pw_country_code != '-1')
			$pw_country_code_condition = " 
				AND	billing_country.meta_value	IN ('{$pw_country_code}')";
			
		if($state_code != NULL  && $state_code != '-1')
			$state_code_condition = " 
				AND	billing_state.meta_value	IN ('{$state_code}')";
				
		if($pw_order_status  && $pw_order_status != '-1' and $pw_order_status != "'-1'")
			$pw_order_status_condition = " AND shop_order.post_status IN (".$pw_order_status.")";
		if($pw_hide_os  && $pw_hide_os != '-1' and $pw_hide_os != "'-1'")
			$pw_hide_os_condition = " AND shop_order.post_status NOT IN (".$pw_hide_os.")";
		
		$sql_group_by = " group by pw_woocommerce_order_itemmeta_product.meta_value ";
		$sql_order_by ="ORDER BY {$pw_sort_by} {$pw_order_by}";

		$sql = "SELECT $sql_columns 
			FROM $sql_joins $category_id_join $all_tax_joins $pw_id_order_status_join 
			WHERE $sql_condition  $pw_from_date_condition $category_id_condition $all_tax_conditions
			$pw_cat_prod_id_string_condition $pw_id_order_status_condition $pw_product_id_condition $pw_country_code_condition $state_code_condition $pw_order_status_condition $pw_hide_os_condition
			$sql_group_by $sql_order_by";
		
		//echo $sql;	
			
		$array_index=3;
		$this->table_cols =$this->table_columns($table_name);
		
		if(is_array($custom_tax_cols))
		{
			array_splice($this->table_cols,$array_index,0,$custom_tax_cols);
			$array_index+=count($custom_tax_cols);
		}
			
		$data_state='';
		//$country_data = $this->pw_get_paying_woo_country();
		$state_codes = $this->pw_get_paying_woo_state('billing_state','billing_country');
		
		
		//IF STATE SELETED
		$state_sel_arr	= '';
		if($state_code != NULL  && $state_code != '-1')
		{
			$state_code = str_replace("','", ",",$state_code);
			$state_sel_arr = explode(",",$state_code);
			
			foreach($state_codes as $state){
				if($state_code=='-1' || is_array($state_sel_arr) && in_array($state -> id,$state_sel_arr))
				{
					$data_state[]=$state -> id;
					$value=array(array('lable'=>$state -> label,'status'=>'currency'));
					array_splice($this->table_cols, $array_index++, 0, $value );
				}
			}
						
		}else if($pw_country_code && $pw_country_code!='-1') //IF STATE NOT SELECTED AND COUNTRY SELETED
		{
			$country_states = $this->pw_get_woo_country_of_state();
			$state_codes_per_country='';
			$pw_country_code		= explode(",",$this->pw_get_woo_requests('pw_countries_code','-1',true));
			if($pw_country_code && $pw_country_code!='-1')
			{		
				$index=0;
				foreach($country_states as $country_states_val){
					if(in_array($country_states_val->parent_id,$pw_country_code)){
						$state_codes_per_country[$index]["id"]=$country_states_val->id;
						$state_codes_per_country[$index]["label"]=$country_states_val->label;
						$index++;
					}
				}
			}
			
			//print_r($state_codes_per_country);
			
			$state_codes=$state_codes_per_country;
			foreach($state_codes as $state){
				$data_state[]=$state ["id"];
				$value=array(array('lable'=>$state ["label"],'status'=>'currency'));
				array_splice($this->table_cols, $array_index++, 0, $value );
			}
		}else{
			foreach($state_codes as $state){
				if($state_code=='-1' || is_array($state_sel_arr) && in_array($state -> id,$state_sel_arr))
				{
					$data_state[]=$state -> id;
					$value=array(array('lable'=>$state -> label,'status'=>'currency'));
					array_splice($this->table_cols, $array_index++, 0, $value );
				}
			}
		}
		
		$value=array(array('lable'=>__('Total',__PW_REPORT_WCREPORT_TEXTDOMAIN__),'status'=>'currency'));
		array_splice($this->table_cols, $array_index, 0, $value );
		$this->data_state=$data_state;
			
		
	}elseif($file_used=="data_table"){
		
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
				
				//Product NAME
				$display_class='';
				if($this->table_cols[1]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $items->product_name;
				$datatable_value.=("</td>");
				
				//Categories
				$display_class='';
				if($this->table_cols[2]['status']=='hide') $display_class='display:none';
				$datatable_value.=("<td style='".$display_class."'>");
					$datatable_value.= $this->pw_get_cn_product_id($items->product_id,"product_cat");
				$datatable_value.=("</td>");
				
				if(is_array($visible_custom_taxonomy)){
					foreach((array)$visible_custom_taxonomy as $tax){
						//Category
						$display_class='';
						if($this->table_cols[3]['status']=='hide') $display_class='display:none';
						$datatable_value.=("<td style='".$display_class."'>");
							$datatable_value.= $this->pw_get_cn_product_id($items->product_id,$tax);
						$datatable_value.=("</td>");
					}
				}
				
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
				//print_r($arr);
				$items_product=$this->pw_get_woo_cp_items($type , $items_only, $id,$params,'_billing_state');
								
				$state_arr='';
				foreach($items_product as $item_product){
					$state_arr[$item_product->billing_state]['total']=$item_product->total;
					$state_arr[$item_product->billing_state]['qty']=$item_product->quantity;
				}
				
				$j=2;
				$total=0;
				$qty=0;
				foreach($this->data_state as $state_name){
					$pw_table_value=$this->price(0);
					if(isset($state_arr[$state_name]['total'])){
						$pw_table_value=$this->price($state_arr[$state_name]['total']) .' #'.$state_arr[$state_name]['qty'];
						$total+=$state_arr[$state_name]['total'];
						$qty+=$state_arr[$state_name]['qty'];
					}
					
					
					$display_class='';
					if($this->table_cols[$j++]['status']=='hide') $display_class='display:none';
					$datatable_value.=("<td style='".$display_class."'>");
						$datatable_value.= $pw_table_value;
					$datatable_value.=("</td>");
				}
				
				
				//Total
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
							if(is_array($permission_value) && !in_array($category -> id,$permission_value))
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
							$selected='';
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
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($product->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_product_id') &&  $permission_value!='')
								$selected="selected";
                            
                            if($current_product==$product->id)
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
					$permission_value=$this->get_form_element_value_permission('pw_countries_code');
					if($this->get_form_element_permission('pw_countries_code') ||  $permission_value!=''){
						if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-globe"></i></span>
					<?php
                        $country_data = $this->pw_get_paying_woo_country();
                        
                        $option='';
                        //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($country_data as $country){
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($country->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_countries_code') &&  $permission_value!='')
								$selected="selected";*/
                            
                            /*if($current_product==$country->id)
                                $selected="selected";*/
                            $option.="<option $selected value='".$country -> id."' >".$country -> label." </option>";
                        }
                    ?>
                
                    <select name="pw_countries_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_countries_code') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
					$permission_value=$this->get_form_element_value_permission('pw_states_code');
					if($this->get_form_element_permission('pw_states_code') ||  $permission_value!=''){
						
						if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
							$col_style='display:none';
				?> 
                
                <div class="col-md-6"  style=" <?php echo $col_style;?>">
                	<div class="awr-form-title">
						<?php _e('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__);?>
                    </div>
					<span class="awr-form-icon"><i class="fa fa-map"></i></span>
					<?php
                        $state_code = '-1';
                        $state_data = $this->pw_get_paying_woo_state('billing_state','billing_country');
                        //print_r($state_data);
                        $option='';
                        //$current_product=$this->pw_get_woo_requests_links('pw_product_id','',true);
                        //echo $current_product;
                        
                        foreach($state_data as $state){
							$selected='';
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($state->id,$permission_value))
								continue;
							/*if(!$this->get_form_element_permission('pw_states_code') &&  $permission_value!='')
								$selected="selected";*/	
                            
                            /*if($current_product==$country->id)
                                $selected="selected";*/
                            $option.="<option $selected value='".$state -> id."' >".$state -> label." </option>";
                        }
                    ?>
                
                    <select name="pw_states_code[]" multiple="multiple" size="5"  data-size="5" class="chosen-select-search">
                        <?php
                        	if($this->get_form_element_permission('pw_states_code') && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
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
				?>
                
            </div>
            
            <div class="col-md-12">
                    <?php
                    	$pw_hide_os='trash';
						$pw_publish_order='no';
						
						$data_format=$this->pw_get_woo_requests_links('date_format',get_option('date_format'),true);
					?>
                    <input type="hidden" name="list_parent_category" value="">
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