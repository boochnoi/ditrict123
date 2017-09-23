<?php
/*
Plugin Name: PW Advanced Woo Reporting
Plugin URI: http://proword.net/Advanced_Reporting/
Description: WooCommerce Advance Reporting plugin is a comprehensive and the most complete reporting system.
Version: 3.2
Author: Proword
Author URI: http://proword.net/
Text Domain: pw_report_wcreport_textdomain
Domain Path: /languages/
*/

/*
V3.2
Added : Cost of Good Reports
Upgrade : Ajax structure
V3.1
fixed : Chart icons broken in dashboard
fixed : Not appear some columns in chart
fixed : Stock List report for 0 stock
fixed : Currency Columns Order
*/

if(!class_exists('pw_report_wcreport_class')){

	//USE IN INCLUDE
	define( '__PW_REPORT_WCREPORT_ROOT_DIR__', dirname(__FILE__));
	
	//USE IN ENQUEUE AND IMAGE
	define( '__PW_REPORT_WCREPORT_CSS_URL__', plugins_url('assets/css/',__FILE__));
	define( '__PW_REPORT_WCREPORT_JS_URL__', plugins_url('assets/js/',__FILE__));
	define ('__PW_REPORT_WCREPORT_URL__',plugins_url('', __FILE__));
	
	//PERFIX
	define ('__PW_REPORT_WCREPORT_FIELDS_PERFIX__', 'custom_report_' );
	
	//TEXT DOMAIN FOR MULTI LANGUAGE
	define ('__PW_REPORT_WCREPORT_TEXTDOMAIN__', 'pw_report_wcreport_textdomain' );
	
	//COST OF GOOF PRICE
	//define ('__PW_COG__','_PW_COST_GOOD_FIELD');
	
	include('includes/datatable_generator.php');
	load_plugin_textdomain( __PW_REPORT_WCREPORT_TEXTDOMAIN__, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	//MAIN CLASS
	class pw_report_wcreport_class extends pw_rpt_datatable_generate{
		
		public $pw_plugin_status='';
		
		public $pw_plugin_main_url='';
		
		public $pw_shop_status='';
		
		public $otder_status_hide='';
		
		//public $menu_fields='';
				
		function __construct(){
			include('includes/actions.php');
			//include('class/customefields.php');
			
			add_action('admin_init', array( $this,'pw_standalone_report'));
			add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
			
			add_action('admin_head',array($this,'pw_report_backend_enqueue'));
			add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );
			add_action('admin_menu', array( $this,'pw_report_setup_menus'));
			
			$field=__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'activate_purchase_code';
			$this->pw_plugin_status=get_option($field);
			
			if(get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_status')=='false' && get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_alt')!='dashboard'){
				$pw_plugin_main_url=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'dashboard_alt');
				$pw_plugin_main_url=explode("admin.php?page=",$pw_plugin_main_url);
				$this->pw_plugin_main_url=$pw_plugin_main_url[1];
			}else{
				$this->pw_plugin_main_url='wcx_wcreport_plugin_dashboard&parent=dashboard';
			}
			
			
			//DEFAULT ORDER STATUS AND HIDE STATUS
			$pw_shop_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'order_status');
			if($pw_shop_status!=''){
				$this->pw_shop_status=implode(",",$pw_shop_status);
			}else{
				$this->pw_shop_status='wc-completed,wc-on-hold,wc-processing';
			}
			
			$otder_status_hide=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'otder_status_hide');
			if($otder_status_hide=='on'){
				$this->otder_status_hide='trash';
			}
						
			//////////ADD COST OF GOOD CUSTOM FIELD//////////
			///add_action( 'woocommerce_product_options_general_product_data', array($this,'pw_add_custom_price_box') );
			///add_action( 'woocommerce_process_product_meta',  array($this,'pw_custom_woocommerce_process_product_meta'), 2 );
			///add_action( 'woocommerce_process_product_meta_variable',  array($this,'pw_custom_woocommerce_process_product_meta'), 2 );
			
			
			// Add Variation Settings
			///add_action( 'woocommerce_product_after_variable_attributes', array($this,'variation_settings_fields'), 10, 3 );
			// Save Variation Settings
			
			///add_action( 'woocommerce_save_product_variation', array($this,'save_variation_settings_fields'), 10, 2 );
			
			
			//add_filter( 'woocommerce_get_price_html', array($this,'pw_add_custom_price_front'), 10, 2 );
			//add_filter( 'woocommerce_get_variation_price_html', array($this,'add_custom_price_front'), 10, 2 );
			
			//
			//add_action( 'woocommerce_before_calculate_totals', array($this,'woo_add_donation'));
			
			
			//SET THE COST OF GOOD CUSTOM FIELD
			$enable_cog=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'enable_cog',"no");
			if($enable_cog=='yes_another'){
				$cog_plugin=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_plugin',"woo_profit");
				$profit_fields=array(
					'woo_profit' => array(
						'field' => '_wc_cog_cost',
						'total' => '_wc_cog_item_total_cost',
					),
					'indo_profit' => array(
						'field' => '_posr_cost_of_good',
						'total' => '_posr_line_cog_total',
					),
					
				);
				
				if($cog_plugin=='other'){
					$cog_field=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field',"_PW_COST_GOOD_FIELD");
					define ('__PW_COG__',$cog_field);
					
					$cog_field=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'cog_field_total',"_PW_COST_GOOD_FIELD");
					define ('__PW_COG_TOTAL__',$cog_field);
				}else{			
					$cog_field=$profit_fields[$cog_plugin]['field'];
					define ('__PW_COG__',$cog_field);
					
					$cog_total=$profit_fields[$cog_plugin]['total'];
					define ('__PW_COG_TOTAL__',$cog_total);
				}
				
			}else if($enable_cog=='yes_this'){
				
				include('add-ons/woocommerce-cost-of-goods-Proword/main.php');
	
				
				define ('__PW_COG__','_PW_COST_GOOD_FIELD');
				define ('__PW_COG_TOTAL__','_PW_COST_GOOD_ITEM_TOTAL_COST');
			}else{
				define ('__PW_COG__','');
				define ('__PW_COG_TOTAL__','');
			}
			
						
		}
		
		function variation_settings_fields( $loop, $variation_data, $variation ) {
			// NUMBER Field
			woocommerce_wp_text_input( 
				array( 
					'id'          => 'pw_cost_of_good[' . $variation->ID . ']', 
					'label'       => __( 'Cost og Good($)', pw_report_wcreport_textdomain ), 
					'desc_tip'    => 'true',
					'description' => __( 'Enter Cost of Good for this product', pw_report_wcreport_textdomain),
					'value'       => get_post_meta( $variation->ID, 'pw_cost_of_good', true ),
					'custom_attributes' => array(
									'step' 	=> 'any',
									'min'	=> '0'
								) 
				)
			);
			
		}
		/**
		 * Save new fields for variations
		 *
		*/
		function save_variation_settings_fields( $post_id ) {
			
			// Number Field
			$number_field = $_POST['pw_cost_of_good'][ $post_id ];
			if( ! empty( $number_field ) ) {
				update_post_meta( $post_id, 'pw_cost_of_good', esc_attr( $number_field ) );
			}
		}
		
		
		function woo_add_donation() {
			global $woocommerce;
			global $current_user;
			$current_user = wp_get_current_user();
			
			$user_info = get_userdata($current_user->ID);
			
			$role = get_role( strtolower($user_info->roles[0]));
			
			$role=($role->name);
			
			//die(print_r($_REQUEST));
			
			$cost_of_good=isset($_REQUEST['cost_of_good']) ? $_REQUEST['cost_of_good']:'';

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$post_id=$cart_item['data']->id;
			
				$cost_of_good = get_post_meta( $post_id, '_cost_of_good', true );
				
				$additional_price='';
				
				if($main_price=='cash_role')
					$additional_price= $cash_price;
				
				if($additional_price!=''){
					$cart_item['data']->set_price($additional_price);
				}
			}
			return true;
		}
		
		function pw_add_custom_price_box() {
			woocommerce_wp_text_input( array( 'id' => 'pw_cost_of_good', 'class' => 'wc_input_price short', 'label' => __( 'Cost of Good($)', 'woocommerce' ) ) );
		
		}

		function pw_custom_woocommerce_process_product_meta( $post_id ) {
			update_post_meta( $post_id, 'pw_cost_of_good', stripslashes( $_POST['pw_cost_of_good'] ) );
		}
		
		function add_custom_price_front( $p, $obj ) {
			global $current_user,$product;
			$post_id = $obj->post->ID;
			$additional_price='';
			
			
			
			$current_user = wp_get_current_user();
			
			$user_info = get_userdata($current_user->ID);
			
			$role = get_role( strtolower($user_info->roles[0]));
			
			$role=($role->name);
			//$role = get_role( strtolower('Administrator'));
		//	echo $role;
			
			$credit_price = get_post_meta( $post_id, 'pro_credit_price_extra', true );
			$wholesale_price= get_post_meta( $post_id, 'pro_wholesale_price_extra', true );
			
			$credit_prices=wc_price(floatval($credit_price));
			$wholesale_prices=wc_price(floatval($wholesale_price));
			
			if ( is_admin() ) {
				//show in new line
				$tag = 'div';
			} else {
				$tag = 'span';
			}
			
			if(is_product()){
			
				
				if ( !empty( $credit_price ) && ($role=='credit_role' || $role=='cash_role' || $role=='administrator')) {
					$additional_price.= "$credit_prices";
				}
				
				if ( !empty( $wholesale_price )  && ($role=='wholesale_role' || $role=='administrator')) {
					$additional_price.= "$wholesale_prices";
				}
				
				/*if ( !empty( $additional_price ) ) {
					return  $additional_price;
				}
				else {
					return  $p ;
				}*/
				
				$total_price = get_post_meta( $post_id, '_price',true);
				 
				$html="<input value='cash_role' class='pw_prices' type='radio' name='role_price' /><label>$p</label><br />
				<input value='credit_role' class='pw_prices' type='radio' name='role_price' /><label>$credit_prices</label><br />
				<input value='wholesale_role' class='pw_prices' type='radio' name='role_price' /><label>$wholesale_prices</label><br />
				
				<script>
					jQuery(document).ready(function(){
						
						jQuery('.pw_prices').click(function(){
							price=(jQuery(this).val());
							jQuery('.pw_main_price_input').remove();
							jQuery('.cart').append('<input class=\'pw_main_price_input\' name=\'main_price\' value=\''+price+'\' />');
						});
						
					});
				</script>
				
				";
				
				return $html;
				
			}
			
			return $p;
		}
		

        function array_insert(&$array, $insert, $position) {
            settype($array, "array");
            settype($insert, "array");
            settype($position, "int");

            //if pos is start, just merge them
            if($position==0) {
                $array = array_merge($insert, $array);
            } else {

                //if pos is end just merge them
                if($position >= (count($array)-1)) {
                    $array = array_merge($array, $insert);
                } else {
                    //split into head and tail, then merge head+inserted bit+tail
                    $head = array_slice($array, 0, $position);
                    $tail = array_slice($array, $position);
                    $array = array_merge($head, $insert, $tail);
                }
            }
        }

		function menu_fields($index=''){
			$menu_fields=array(
				'all_orders' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_states_code" => __('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
		
				'product' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),
				'category' => array(
					'fields' => array(
						"pw_parent_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				
				'variation' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'stock_list' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),
				/*'variation_stock' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),*/
				'tax_reports' => array(
					'fields' => array(
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_states_code" => __('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				
				///////CUSTOM TAXONOMY ADD_ON////////
				'details_tax_field' => array(
					'fields' => array(
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_states_code" => __('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),
				'brand_tax_field' => array(
					'fields' => array(
						"pw_parent_brand_id" => __('Brand',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				
				'custom_tax_field' => array(
					'fields' => array(
						"pw_customy_taxonomies" => __('Product Taxonimies',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				
				///////CUSTOM COUNTRY ADD_ON////////
				'details_order_country' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_states_code" => __('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),
				
				///////CROSSTAB ADD_ON////////	
				'prod_per_month' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'variation_per_month' => array(
					'fields' => array(
						"pw_categories" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_products" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'prod_per_country' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'prod_per_state' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_states_code" => __('State',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'country_per_month' => array(
					'fields' => array(
						"pw_countries_code" => __('Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'payment_per_month' => array(
					'fields' => array(
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
				'order_status_per_month' => array(
					'fields' => array(
						"pw_orders_status" => __('Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(
						
					),
				),	
			);
			
			///////////////////////////////////////
			////GENERATE CUSTOM TAXONOMY FIELDS////
			global $pw_rpt_main_class;
			$visible_custom_taxonomy='';
			$post_name='product';
			//$all_tax=get_object_taxonomies( $post_name );
			$all_tax=$pw_rpt_main_class->fetch_product_taxonomies( $post_name );
			
			$current_value=array();
			if(is_array($all_tax) && count($all_tax)>0){
				//FETCH TAXONOMY
				foreach ( $all_tax as $tax ) {
					$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$index.'_'.$tax);
					
					if($tax_status=='on'){
						$visible_custom_taxonomy[]=$tax;
					}
				}
			}
			
			$custom_taxonomy_fields='';
			
			if(defined("__PW_TAX_FIELD_ADD_ON__") && is_array($visible_custom_taxonomy) && count($visible_custom_taxonomy)>0){
		
				//FETCH TAXONOMY
				foreach ( $visible_custom_taxonomy as $tax ) {
					$taxonomy=get_taxonomy($tax);	
					$values=$tax;
					$label=$taxonomy->label;
					$translate=get_option($index.'_'.$tax."_translate");
					if($translate!='')
					{
						$label=$translate;
					}
					$menu_fields['details_tax_field']['fields'][$tax]=$label;

					$menu_fields['product']['fields'][$tax]=$label;
					$menu_fields['prod_per_month']['fields'][$tax]=$label;
					$menu_fields['prod_per_country']['fields'][$tax]=$label;
					$menu_fields['prod_per_state']['fields'][$tax]=$label;
					$menu_fields['stock_list']['fields'][$tax]=$label;
				}
			}
			//////////////////////////////////////
			
			return $menu_fields;
		}
		
		function pw_report_backend_enqueue(){
			if(isset($_GET['parent']) || (isset($_GET['page']) && $_GET['page']=='wcx_wcreport_plugin_mani_settings')  || (isset($_GET['page']) && $_GET['page']=='permission_report'))
			{
				include ("includes/admin-embed.php");
			}
		}	
		
		function loadTextDomain() {
			load_plugin_textdomain( 'pw_report_wcreport_textdomain' , false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}
		
		function fetch_product_taxonomies($post_name){
			$all_tax=get_object_taxonomies( $post_name );
			$taxonomies=array();
			if(is_array($all_tax) && count($all_tax)>0){
				//FETCH TAXONOMY
				$i=1;
				foreach ( $all_tax as $tax ) {
					if($tax=='product_cat')
						continue;
					$taxonomies[]=$tax;
				}
			}
			return $taxonomies;
		}
		
		function make_custom_taxonomy($args){
			$key=$args['page'];
			$visible_custom_taxonomy='';
			$post_name='product';
			$all_tax=$this->fetch_product_taxonomies( $post_name );
			$current_value=array();
			if(is_array($all_tax) && count($all_tax)>0){
				//FETCH TAXONOMY
				foreach ( $all_tax as $tax ) {
					$tax_status=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
					/*$tax_value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
					pw_elm_value_all_orders_pw_category_id[]*/
					if($tax_status=='on' ){
						$visible_custom_taxonomy[]=$tax;
					}
				}
			}

			$option_data='';
			$param_line='';
			$show_custom_tax_block=false;
			
			$current_value=array();
			if(defined("__PW_TAX_FIELD_ADD_ON__") && is_array($visible_custom_taxonomy) && count($visible_custom_taxonomy)>0){
				
				$post_type_label=get_post_type_object( $post_name );
				$label=$post_type_label->label ; 
				
				//FETCH TAXONOMY
				foreach ( $visible_custom_taxonomy as $tax ) {
					$taxonomy=get_taxonomy($tax);	
					$values=$tax;
					$label=$taxonomy->label;
					$translate=get_option($key.'_'.$tax."_translate");
					if($translate!='')
					{
						$label=$translate;
					}
		
					$attribute_taxonomies = wc_get_attribute_taxonomies();
					
					////////////////////////////////////
					//PERMISSION CHECK
					$col_style='';
					$permission_value=$this->get_form_element_value_permission($tax);
					if(!$this->get_form_element_permission($tax) && $permission_value==''){
						continue;	
					}
					
					$permission_value=$this->get_form_element_value_permission($tax);
					//////////////////////////////////////
					
					if(!$this->get_form_element_permission($tax) &&  $permission_value!='')
						$col_style='display:none';
					else
						$show_custom_tax_block=true;
						
					$param_line .=' 
					
					<div class="col-md-6" style="'.$col_style.'">
						<div class="awr-form-title">'.$label.'</div>
						<span class="awr-form-icon"><i class="fa fa-tags"></i></span>
							<div class="full-lbl-cnt more-padding">';
		
						$param_line_exclude =$param_line_include = '<select name="pw_custom_taxonomy_in_'.$tax.'[]" class="chosen-select-search" multiple="multiple" style="width: 531px;" data-placeholder="'.__('Choose Inclulde ',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' '.$label.' ..." id="pw_'.$tax.'">';
						
						if($this->get_form_element_permission($tax) && ((!is_array($permission_value)) || (is_array($permission_value) && in_array('all',$permission_value))))
						{
							$param_line_include.='<option value="-1">'.__('Select All',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'</option>';
						}
						
						$param_line_exclude = '<select name="pw_custom_taxonomy_ex_'.$tax.'[]" class="chosen-select-search" multiple="multiple" style="width: 531px;" data-placeholder="'.__('Choose Exclude',__PW_REPORT_WCREPORT_TEXTDOMAIN__).' '.$label.' ..." id="pw_'.$tax.'">';
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
		
						$categories = get_terms($tax,$args); 
						foreach ($categories as $category) {
							$selected='';
							
							//CHECK IF IS IN PERMISSION
							if(is_array($permission_value) && !in_array($category->term_id,$permission_value))
								continue;
								
							if(!$this->get_form_element_permission($tax) &&  $permission_value!='')
								$selected="selected";	
							
							$option = '<option value="'.$category->term_id.'" '.$selected.'>';
							$option .= $category->name;
							$option .= ' ('.$category->count.')';
							$option .= '</option>';
							$param_line_include .= $option;

						}
						$param_line_include .='</select>';
						
						$categories = get_terms($tax,$args); 
						foreach ($categories as $category) {
							
							$option = '<option value="'.$category->term_id.'" '.$selected.'>';
							$option .= $category->name;
							$option .= ' ('.$category->count.')';
							$option .= '</option>';
							$param_line_exclude .= $option;
						}
						$param_line_exclude .='</select>';
						$param_line_exclude='';
						$param_line .= $param_line_include.$param_line_exclude.'
					</div></div> ';	
				}
				
			}
			
			if($show_custom_tax_block)
			{
				$param_line='
					<div class="col-md-6" style="border:#2CC185 2px solid;width:100%">
						<div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #2CC185;color: #fff;">
							'.__('Custom Taxonomy',__PW_REPORT_WCREPORT_TEXTDOMAIN__).'
						</div>'.$param_line.'</div>';
			}
			
			return $param_line;
		}
		
		function pw_standalone_report(){
			if(defined("__PW_PERMISSION_ADD_ON__"))
			{
				global $current_user;
				$current_user = wp_get_current_user();
				
				$user_info = get_userdata($current_user->ID);
				
				$role = get_role( strtolower($user_info->roles[0]));
				
				//$role->has_cap( 'pw_report_appear' ); 
				
				if(isset($role->capabilities['pw_report_appear']) && $role->capabilities['pw_report_appear']){
					$role_capability='pw_report_appear';
				}
				
				if(strtolower($user_info->roles[0])=='woo_report_role'){
					add_action( 'admin_head', array($this,'custom_menu_page_removing') );
					//echo $_SERVER["PHP_SELF"].' - '.strpos('admin-ajax.php',$_SERVER["PHP_SELF"]);
					//echo strpos($_SERVER["PHP_SELF"],'admin-ajax.php')=== true;
					if(!isset($_GET['parent']) && strpos($_SERVER["PHP_SELF"],'admin-ajax.php')=== false){
						die ( '
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-addons-cnt awr-addones-deactive">
												<div class="awr-descicon"><i class="fa fa-times"></i></div>
												<div class="awr-desc-content">	
													<h3 class="awr-addones-title" style="color:#333;border-bottom:1px solid #ccc;padding-bottom:5px">Access Denied!</h3>
													<div class="awr-addnoes-desc">You have no permisson !!</div>
													<a class="awr-addons-btn" href="http://proword.net/request/" target="_blank" style="background: #eee;"><i class="fa fa-paper-plane"></i>Send Your Request</a>
												</div>
												<div class="awr-clearboth"></div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->');	
					}
				}
			}
		}
		
		function my_login_redirect( $redirect_to, $request, $user ) {
			
			//is there a user to check?
			global $user;
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				//check for admins
				if ( in_array( 'woo_report_role', $user->roles ) ) {
					// redirect them to the default place
					$url=$this->pw_plugin_main_url;
					return admin_url("admin.php?page=$url");
				} 
				
			} else {
				return $redirect_to;
			}
			return $redirect_to;
		}
		
		function custom_menu_page_removing() {
			echo  '<style>#adminmenuwrap,#wp-admin-bar-root-default{display:none;}</style>';
				
			echo '<script >
				jQuery(document).ready(function($){
					jQuery("#adminmenuwrap, #adminmenuback, #wp-admin-bar-root-default").remove();
					jQuery("body").addClass("woo_report_role");
				});
			</script>';
		}

		function get_capability(){
			//$role_capability='manage_options';
			$role_capability='edit_pages';
			
			if(defined("__PW_PERMISSION_ADD_ON__"))
			{
				global $current_user;
				$current_user = wp_get_current_user();
				
				$user_info = get_userdata($current_user->ID);
				
				$role = get_role( strtolower($user_info->roles[0]));
				
				if(strtolower($user_info->roles[0])=='administrator'){
					return 'manage_options';
				}
				
				//$role->has_cap( 'pw_report_appear' ); 
				
				if(isset($role->capabilities['pw_report_appear']) && $role->capabilities['pw_report_appear']){
					$role_capability='pw_report_appear';
				}
			}
			return $role_capability;
		}
		
		function get_dashboard_capability($menu_id){
			$permission=true;
			if(defined("__PW_PERMISSION_ADD_ON__")){
				
				global $current_user;
				$current_user = wp_get_current_user();
				$user_info = $current_user->user_login;
				
				$user_infos = get_userdata($current_user->ID);
				if(strtolower($user_infos->roles[0])=='administrator'){
					return true;
				}	
				
				if(get_option("pw_".$user_info."_dashborad_permission")!=''){
					$menu_permission=get_option("pw_".$user_info."_dashborad_permission");
				}else{
					$user_info = get_userdata($current_user->ID);
					$menu_permission=get_option("pw_".$user_info->roles[0]."_dashborad_permission");
					if(strtolower($user_info->roles[0])=='administrator'){
						return true;
					}
				}
				
				$fetched_value=json_decode($menu_permission);
				$keys="pw_elm_enable_".$menu_id;
				$current_value=isset($fetched_value->$keys) ? $fetched_value->$keys:"";
				//echo $current_value;
				if($current_value=='off' || $current_value=='')
					$permission=false;
			}
			return $permission;
		}
		
		function get_menu_capability($menu_id){
			$permission=true;
			if(defined("__PW_PERMISSION_ADD_ON__")){
				
				global $current_user;
				$current_user = wp_get_current_user();
				$user_info = $current_user->user_login;
				
				$user_infos = get_userdata($current_user->ID);
				if(strtolower($user_infos->roles[0])=='administrator'){
					return true;
				}		
				
				if(get_option("pw_".$user_info."_permission")!=''){
					$menu_permission=get_option("pw_".$user_info."_permission");
				}else{
					$user_info = get_userdata($current_user->ID);
					$menu_permission=get_option("pw_".$user_info->roles[0]."_permission");
					if(strtolower($user_info->roles[0])=='administrator'){
						return true;
					}					
				}
				
				
				$fetched_value=json_decode($menu_permission);
				$keys="pw_elm_enable_".$menu_id;
				$current_value=isset($fetched_value->$keys) ? $fetched_value->$keys:"";
				//echo $current_value;
				if($current_value=='off' || $current_value=='')
					$permission=false;
			}
			return $permission;
		}
		
		function get_form_element_permission($field_id,$key=''){
			$permission=true;
			if(defined("__PW_PERMISSION_ADD_ON__")){
				global $current_user;
				$current_user = wp_get_current_user();
				$user_info = $current_user->user_login;
				
				$user_infos = get_userdata($current_user->ID);
				if(strtolower($user_infos->roles[0])=='administrator'){
					return true;
				}	
				
				if(get_option("pw_".$user_info."_permission")!=''){
					$menu_permission=get_option("pw_".$user_info."_permission");
				}else{
					$user_info = get_userdata($current_user->ID);
					$menu_permission=get_option("pw_".$user_info->roles[0]."_permission");
					if(strtolower($user_info->roles[0])=='administrator'){
						return true;
					}
				}
				
				$fetched_value=json_decode($menu_permission);
				$parent=isset($_GET['smenu']) ? $_GET['smenu'] :$_GET['parent'];
				if($key!='')	$parent=$key;
				$keys="pw_elm_checkbox_".$parent."_".$field_id;
				//print_r($fetched_value);
				$current_value=isset($fetched_value->$keys) ? $fetched_value->$keys:"";
				//echo $current_value;
				if($current_value=='')
					$permission=false;
			}
			return $permission;
		}
		
		function get_form_element_value_permission($field_id,$key=''){
			$permission=true;
			if(defined("__PW_PERMISSION_ADD_ON__")){
				global $current_user;
				$current_user = wp_get_current_user();
				$user_info = $current_user->user_login;
				
				$user_infos = get_userdata($current_user->ID);
				if(strtolower($user_infos->roles[0])=='administrator'){
					return true;
				}	
				
				if(get_option("pw_".$user_info."_permission")!=''){
					$menu_permission=get_option("pw_".$user_info."_permission");
				}else{
					$user_info = get_userdata($current_user->ID);
					$menu_permission=get_option("pw_".$user_info->roles[0]."_permission");
					if(strtolower($user_info->roles[0])=='administrator'){
						return true;
					}
					
				}
								
				$fetched_value=json_decode($menu_permission);
				$parent=isset($_GET['smenu']) ? $_GET['smenu'] :$_GET['parent'];
				if($key!='')	$parent=$key;
				$keys="pw_elm_value_".$parent."_".$field_id;
				//print_r($fetched_value->$keys);
				if(isset($fetched_value->$keys) && !in_array("all",$fetched_value->$keys))
					return $fetched_value->$keys;	
			}
			return $permission;
		}
		
		function pw_report_setup_menus() {
			
			global $submenu;
			
			//IF WANT TO SHOW MENU FOR ALL USER USE 'edit_pages' 
			
			$role_capability=$this->get_capability();
			//echo $role_capability;
			
			add_menu_page(__('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Woo Reporting',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, $this->pw_plugin_main_url,  array($this,'wcx_plugin_dashboard'),'dashicons-chart-pie' );
			
			add_submenu_page($this->pw_plugin_main_url, __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_setting_report&parent=setting',  array($this,'wcx_plugin_mani_settings' ));
			
			
			add_submenu_page(null, __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_dashboard',  array($this,'wcx_plugin_dashboard' ));
			
			add_submenu_page(null, __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('My Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_plugin_menu_my_dashboard',  array($this,'wcx_plugin_menu_my_dashboard' ));
			
			add_submenu_page(null, __('Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_details',   array($this,'wcx_plugin_menu_details' ) );	
			
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			//CUSTOM TAX & FIELDS	
			
			//ALL DETAILS
			add_submenu_page(null, __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_product',   array($this,'wcx_plugin_menu_product' ) );		
			add_submenu_page(null, __('Profit',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Profit',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_profit',   array($this,'wcx_plugin_menu_profit' ) );
			add_submenu_page(null, __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_category',   array($this,'wcx_plugin_menu_category' ) );
			add_submenu_page(null, __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_customer',   array($this,'wcx_plugin_menu_customer' ) );	
			add_submenu_page(null, __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Billing Country',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_billingcountry',   array($this,'wcx_plugin_menu_billingcountry' ) );	
			add_submenu_page(null, __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Billing State',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_billingstate',   array($this,'wcx_plugin_menu_billingstate' ) );
			add_submenu_page(null, __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Payment Gateway',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_paymentgateway',   array($this,'wcx_plugin_menu_paymentgateway' ) );
			add_submenu_page(null, __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Order Status',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_orderstatus',   array($this,'wcx_plugin_menu_orderstatus' ) );
			add_submenu_page(null, __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Recent Order',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_recentorder',   array($this,'wcx_plugin_menu_recentorder' ) );
			add_submenu_page(null, __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Tax Report',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_taxreport',   array($this,'wcx_plugin_menu_taxreport' ) );
			add_submenu_page(null, __('Customer Buy Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Customer Buy Products',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_customrebuyproducts',   array($this,'wcx_plugin_menu_customrebuyproducts' ) );
			add_submenu_page(null, __('Refund Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Refund Details',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_refunddetails',   array($this,'wcx_plugin_menu_refunddetails' ) );
			add_submenu_page(null, __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Coupon',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_coupon',   array($this,'wcx_plugin_menu_coupon' ) );
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			//CROSSTAB
			
			//////////////////////////////////////////////
			//////////////////////
			//////////////////////////////////////////////
			//VARIATION
			add_submenu_page(null, __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_stock_list',   array($this,'wcx_plugin_menu_stock_list' ) );
			//STOCK VARIATION
			add_submenu_page(null, __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_projected_actual_sale',   array($this,'wcx_plugin_menu_projected_actual_sale' ) );
			add_submenu_page(null, __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_tax_reports',   array($this,'wcx_plugin_menu_tax_reports' ) );	
			
			/////////////////////////////
			//SETTINGS
			/////////////////////////////////
			add_submenu_page(null, __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Report Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_setting_report',   array($this,'wcx_plugin_menu_setting_report' ) );
			
			add_submenu_page(null, __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Report Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_addons_report',   array($this,'wcx_plugin_menu_addons_report' ) );
			
			add_submenu_page(null, __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Other Useful Plugins',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_proword_report',   array($this,'wcx_plugin_menu_proword_report' ) );	
			
			add_submenu_page(null, __('Activate Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Active Plugin',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_active_report',   array($this,'wcx_plugin_menu_active_report' ) );	
			
			//CUSTOMIZE MENUS
			do_action( 'pw_report_wcreport_admin_menu' );
			
		}
		
		function wcx_plugin_dashboard($display="all"){
			$this->pages_fetch("dashboard_report.php",$display);
		}
		
		function wcx_plugin_mani_settings($display="all"){
			include("class/setting_general.php");
		}
		
		function wcx_plugin_menu_my_dashboard(){
			$this->pages_fetch("reports.php");
		}
		
		//Details
		function wcx_plugin_menu_details(){
			$this->pages_fetch("details.php");
		}
		
		//////////////////////ALL DETAILS//////////////////////
		
		function pages_fetch($page,$display="all"){
			$pw_plugin_main_url='';
			if($this->pw_plugin_main_url)
			{
				$pw_plugin_main_url='admin.php?page='.$this->pw_plugin_main_url;
			}
			
			$visible_menu=array(
				
				array(
					"parent" => "main",
					"childs" => array(
						array(
							"label" => __('All Menus',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_menu",
							"link" => "#",
							"icon" => "fa-bars",
						),
						array(
							"label" => __('Dashboard',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "dashboard",
							"link" => $pw_plugin_main_url,
							"icon" => "fa-bookmark",
						),
						array(
							"label" => __('All Orders',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "all_orders",
							"link" => "admin.php?page=wcx_wcreport_plugin_details&parent=all_orders",
							"icon" => "fa-file-text",
						),
						//CUSTOM TAX & FIELD
						array(
							"label" => __('More Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "more_reports",
							"link" => "#",
							"icon" => "fa-files-o",
							"submenu_id" => "more_reports",
						),
						//CROSSTAB
						//VARIATION
						array(
							"label" => __('Stock List',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "stock_list",
							"link" => "admin.php?page=wcx_wcreport_plugin_stock_list&parent=stock_list",
							"icon" => "fa-cart-arrow-down",
						),
						//VARIATION STOCK 
						array(
							"label" => __('Target Sale vs Actual Sale',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proj_actual_sale",
							"link" => "admin.php?page=wcx_wcreport_plugin_projected_actual_sale&parent=proj_actual_sale",
							"icon" => "fa-calendar-check-o",
						),
						array(
							"label" => __('Tax Reports',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_reports",
							"link" => "admin.php?page=wcx_wcreport_plugin_tax_reports&parent=tax_reports",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __('Settings',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "setting",
							"link" => "admin.php?page=wcx_wcreport_plugin_setting_report&parent=setting",
							"icon" => "fa-cogs",
						),
						array(
							"label" => __('Add-ons',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "addons",
							"link" => "admin.php?page=wcx_wcreport_plugin_addons_report&parent=addons",
							"icon" => "fa-plug",
						),
						array(
							"label" => __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "proword",
							"link" => "admin.php?page=wcx_wcreport_plugin_proword_report&parent=proword",
							"icon" => "fa-product-hunt",
						),
					)
 				),
				array(
					"parent" => "more_reports",
					"childs" => array(
						array(
							"label" => __("Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "product",
							"link" => "admin.php?page=wcx_wcreport_plugin_product&parent=more_reports&smenu=product",
							"icon" => "fa-cog",
						),
						
						array(
							"label" => __("Category" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "category",
							"link" => "admin.php?page=wcx_wcreport_plugin_category&parent=more_reports&smenu=category",
							"icon" => "fa-tags",
						),
						array(
							"label" => __("Customer" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer",
							"link" => "admin.php?page=wcx_wcreport_plugin_customer&parent=more_reports&smenu=customer",
							"icon" => "fa-user",
						),
						array(
							"label" => __("Billing Country" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_country",
							"link" => "admin.php?page=wcx_wcreport_plugin_billingcountry&parent=more_reports&smenu=billing_country",
							"icon" => "fa-globe",
						),
						array(
							"label" => __("Billing State" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "billing_state",
							"link" => "admin.php?page=wcx_wcreport_plugin_billingstate&parent=more_reports&smenu=billing_state",
							"icon" => "fa-map",
						),
						array(
							"label" => __("Payment Gateway" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "payment_gateway",
							"link" => "admin.php?page=wcx_wcreport_plugin_paymentgateway&parent=more_reports&smenu=payment_gateway",
							"icon" => "fa-credit-card",
						),
						array(
							"label" => __("Order Status" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "order_status",
							"link" => "admin.php?page=wcx_wcreport_plugin_orderstatus&parent=more_reports&smenu=order_status",
							"icon" => "fa-check",
						),
						array(
							"label" => __("Recent Order" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "recent_order",
							"link" => "admin.php?page=wcx_wcreport_plugin_recentorder&parent=more_reports&smenu=recent_order",
							"icon" => "fa-shopping-cart",
						),
						array(
							"label" => __("Tax Report" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "tax_report",
							"link" => "admin.php?page=wcx_wcreport_plugin_taxreport&parent=more_reports&smenu=tax_report",
							"icon" => "fa-pie-chart",
						),
						array(
							"label" => __("Customer Buy Product" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "customer_buy_prod",
							"link" => "admin.php?page=wcx_wcreport_plugin_customrebuyproducts&parent=more_reports&smenu=customer_buy_prod",
							"icon" => "fa-users",
						),
						array(
							"label" => __("Refund Detail" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "refund_detail",
							"link" => "admin.php?page=wcx_wcreport_plugin_refunddetails&parent=more_reports&smenu=refund_detail",
							"icon" => "fa-eye-slash",
						),
						array(
							"label" => __("Coupon" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "coupon",
							"link" => "admin.php?page=wcx_wcreport_plugin_coupon&parent=more_reports&smenu=coupon",
							"icon" => "fa-hashtag",
						),
					)
 				),
				//TAX  & CUSTIM FIELDS REPORT
			);
			$profict_arr[]=array(
							"label" => __("Profit" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
							"id" => "profit",
							"link" => "admin.php?page=wcx_wcreport_plugin_profit&parent=more_reports&smenu=profit",
							"icon" => "fa-money",
						);	
			if(__PW_COG__!=''){
				array_splice($visible_menu[1]['childs'],1,0,$profict_arr);			
			}
						
			include("class/pages_fetch_dashboards.php");
		}
		
		function dashboard($item_id){

			$username = 'proword'; 
			$api_key = 't0kbg3ez6pl5yo1ojhhoja9d64swh6wi';
			
			$item_valid_id='12042129'; //8218941
		
			//CHECK IF THE CALL FOR THE FUNCTION WAS EMPTY
			if ( $item_id != '' ):
				
				$api_url='http://marketplace.envato.com/api/edge/'.$username.'/'.$api_key.'/verify-purchase:'.$item_id.'.json';
				
				
				$response = wp_remote_get(  $api_url );
				
				/* Check for errors, if there are some errors return false */
				if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
					//$html.='There is another way, you can goto Proword and then past the url of proword here';
					return false;
				}
				
				/* Transform the JSON string into a PHP array */
				$result = json_decode( wp_remote_retrieve_body( $response ), true );
				
				//print_r($result);
				if (isset($result['verify-purchase']['item_id']) && $result['verify-purchase']['item_id']==$item_valid_id && isset($result['verify-purchase']['item_name']) &&  $result['verify-purchase']['item_name'] ) :
					return $result;
					//
				else:
					return false;
				endif;
			endif; 
			
		}
		
		
		//1-PRODUCTS
		function wcx_plugin_menu_product(){
			$this->pages_fetch("product.php");
		}
		//2-PROFIT
		function wcx_plugin_menu_profit(){
			$this->pages_fetch("profit.php");
		}
		//2-CATEGORY
		function wcx_plugin_menu_category(){
			$this->pages_fetch("category.php");
		}
		//3-CUSTOMER
		function wcx_plugin_menu_customer(){
			$this->pages_fetch("customer.php");
		}
		//4-BILLING COUNTRY
		function wcx_plugin_menu_billingcountry(){
			$this->pages_fetch("billingcountry.php");
		}
		//5-BILLING STATE
		function wcx_plugin_menu_billingstate(){
			$this->pages_fetch("billingstate.php");
		}
		//6-PAYMENT GATEWAY
		function wcx_plugin_menu_paymentgateway(){
			$this->pages_fetch("paymentgateway.php");
		}
		//7-ORDER STATUS
		function wcx_plugin_menu_orderstatus(){
			$this->pages_fetch("orderstatus.php");
		}
		//8-RECENT ORDER
		function wcx_plugin_menu_recentorder(){
			$this->pages_fetch("recentorder.php");
		}
		//9-TAX REPORT
		function wcx_plugin_menu_taxreport(){
			$this->pages_fetch("taxreport.php");
		}
		//10-CUSTOMER BUY PRODUCT
		function wcx_plugin_menu_customrebuyproducts(){
			$this->pages_fetch("customerbuyproducts.php");
		}
		//11-REFUND DETAILS
		function wcx_plugin_menu_refunddetails(){
			$this->pages_fetch("refunddetails.php");
		}
		//12-COUPON
		function wcx_plugin_menu_coupon(){
			$this->pages_fetch("coupon.php");
		}
		
		//////////////////////CROSS TABS//////////////////////
		
		//VARIATION		
		function wcx_plugin_menu_variation(){
			$this->pages_fetch("variation.php");
		}
		//STOCK LIST
		function wcx_plugin_menu_stock_list(){
			$this->pages_fetch("stock_list.php");
		}
		//VARIATION STOCK
		function wcx_plugin_menu_variation_stock(){
			$this->pages_fetch("variation_stock.php");
		}
		//PROJECTED VS ACTUAL SALE
		function wcx_plugin_menu_projected_actual_sale(){
			$this->pages_fetch("projected_actual_sale.php");
		}
		//TAX REPORT
		function wcx_plugin_menu_tax_reports(){
			$this->pages_fetch("tax_reports.php");
		}
		
		//SETTING
		function wcx_plugin_menu_setting_report(){
			$this->pages_fetch("setting_report.php");
		}
		
		//ADD-ONS
		function wcx_plugin_menu_addons_report(){
			$this->pages_fetch("addons_report.php");
		}
		
		//ADD-ONS
		function wcx_plugin_menu_proword_report(){
			$this->pages_fetch("advertise_other_plugins.php");
		}
		
		//ACTIVE
		function wcx_plugin_menu_active_report(){
			$this->pages_fetch("plugin_active.php");
		}
	}
	
	$GLOBALS['pw_rpt_main_class'] = new pw_report_wcreport_class;
	
	
	//THE PLUGIN PAGES IS CREATED IN THIS FILE
	//include('class/custommenu.php');
}
?>