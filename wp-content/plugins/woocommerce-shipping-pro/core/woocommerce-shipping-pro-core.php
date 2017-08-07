<?php
class wf_woocommerce_shipping_pro_method extends WC_Shipping_Method {
	function __construct() {
		$plugin_config = wf_plugin_configuration();
		$this->id           = $plugin_config['id']; 
		$this->method_title     = __( $plugin_config['method_title'], 'wf_woocommerce_shipping_pro' );
		$this->method_description = __( $plugin_config['method_description'], 'wf_woocommerce_shipping_pro' );


		$this->wf_shipping_pro_init_form_fields();
		$this->init_settings();

		$this->title 					= $this->settings['title'];
		$this->enabled 					= $this->settings['enabled'];
		//get_option fill default if doesn't exist. other settings also can change to this
		$this->debug = $this->get_option('debug');				
		$this->tax_status       		= $this->settings['tax_status'];
		$this->rate_matrix       		= $this->settings['rate_matrix'];
		
		//get_option fill default if doesn't exist. other settings also can change to this
		$this->displayed_columns      	= $this->get_option('displayed_columns');
		$calculation_mode       		= $this->get_option('calculation_mode');
		$this->remove_free_text       	= $this->get_option('remove_free_text');
		$this->and_logic 				= $this->get_option('and_logic')== 'yes' ? true : false;
		
		if($this->get_option('performance_mode')=='no'){
			$this->multiselect_act_class	=	'multiselect';
			$this->drop_down_style	=	'chosen_select ';			
		}else{
			$this->multiselect_act_class	=	'no_multiselect';
			$this->drop_down_style	=	'';			
		}
		$this->drop_down_style.=	$this->multiselect_act_class;
		
		if ( ! class_exists( 'WF_Calc_Strategy' ) )
			include_once 'abstract-wf-calc-strategy.php' ;
		$this->calc_mode_strategy =  WF_Calc_Strategy::get_calc_mode($calculation_mode,$this->rate_matrix);
		$this->row_selection_choice = $this->calc_mode_strategy->wf_row_selection_choice();
		
		$this->col_count = count($this->displayed_columns)+1;
		$this->shipping_countries = wf_get_shipping_countries();
		
		$this->shipping_classes =WC()->shipping->get_shipping_classes();
		
		$this->product_category  = get_terms( 'product_cat', array('fields' => 'id=>name'));

		//variable to get decimal separator used.
		$separator = stripslashes( get_option( 'woocommerce_price_decimal_sep' ) );
		$this->decimal_separator = $separator ? $separator : '.';
				
		// Save settings in admin
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		
		if($this->remove_free_text == 'yes'){
			add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'wf_remove_local_pickup_free_label'), 10, 2 );
		}
		//filter to add states for Ireland
		add_filter( 'woocommerce_states', array( $this,'wf_custom_woocommerce_states') );
	}
	
	public function wf_state_dropdown_options( $selected_states = array(), $escape = false ) {
		if ( $this->shipping_countries ) foreach ( $this->shipping_countries as $key=>$value) :
			if ( $states =  WC()->countries->get_states( $key ) ) :
				echo '<optgroup label="' . esc_attr( $value ) . '">';
    				foreach ($states as $state_key=>$state_value) :
    					echo '<option value="' . esc_attr( $key ) . ':'.$state_key.'"';
    					if (!empty($selected_states) && in_array(esc_attr( $key ) . ':'.$state_key,$selected_states)) echo ' selected="selected"';
    					//echo '>'.$value.' &mdash; '. ($escape ? esc_js($state_value) : $state_value) .'</option>';
						echo '>'. ($escape ? esc_js($state_value) : $state_value) .'</option>';
    				endforeach;
    			echo '</optgroup>';
			endif;
		endforeach;
	}
	
	public function wf_product_category_dropdown_options( $selected_categories = array()) {
		if ($this->product_category) foreach ( $this->product_category as $product_id=>$product_name) :
			echo '<option value="' . $product_id .'"';
			if (!empty($selected_categories) && in_array($product_id,$selected_categories)) echo ' selected="selected"';
			echo '>' . esc_js( $product_name ) . '</option>';
		endforeach;
	}
	
	public function wf_shipping_class_dropdown_options( $selected_class = array()) {
		if ($this->shipping_classes) foreach ( $this->shipping_classes as $class) :
			echo '<option value="' . esc_attr($class->slug) .'"';
			if (!empty($selected_class) && in_array($class->slug,$selected_class)) echo ' selected="selected"';
			echo '>' . esc_js( $class->name ) . '</option>';
		endforeach;
	}
	
	function wf_debug($error_message){
		if($this->debug == 'yes')
			wc_add_notice( $error_message, 'notice' );
	}
	
	public function generate_activate_box_html() {
		ob_start();
		$plugin_name = 'shippingpro';
		include( dirname(__FILE__).'/../includes/wf_api_manager/html/html-wf-activation-window.php' ); //without diname() getting error due to some resone.
		return ob_get_clean();
	}

	function wf_shipping_pro_init_form_fields() {
		
		$this->form_fields = array(
		   'licence'  => array(
				'type'            => 'activate_box'
			),
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'wf_woocommerce_shipping_pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this shipping method', 'wf_woocommerce_shipping_pro' ),
				'default' => 'no',
			),						
			'title'      => array(
				'title'       => __( 'Method Title', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'wf_woocommerce_shipping_pro' ),
				'default'     => __( $this->method_title, 'wf_woocommerce_shipping_pro' ),
			),
			'performance_mode'    => array(
				'title'   	  => __( 'Performance Mode', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'checkbox',
				'default' 	  => 'no',
				'label'	  =>  __( 'Check if you have large number of rows for rate matrix.', 'wf_woocommerce_shipping_pro' ),
			),
			'and_logic'    => array(
				'title'   	  => __( 'Calculation Logic (AND)', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'checkbox',
				'default' 	  => 'no',
				'description' => __( 'On enabling this, the calculation logic for "Shipping Class" and "Product Category" fields will follow AND logic. By default the plugin follows OR logic', 'wf_woocommerce_shipping_pro' ),
				'label'	  =>  __( 'Enable', 'wf_woocommerce_shipping_pro' ),
			),			
			'rate_matrix' => array(
				'type' 			=> 'rate_matrix'
			),
			'displayed_columns' => array(
				'title'       => __( 'Display/Hide matrix columns', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'multiselect',
				'description' => __( 'Select the columns which are used in the matrix. Please Save changes to reflect the modifications.', 'wf_woocommerce_shipping_pro' ),
				'class'       => 'chosen_select',
				'css'         => 'width: 450px;',
				'default'     => array(
					'shipping_name',
					'zone_list'   ,
					'weight' , 
					'fee'   ,
					'cost' ,
					'weigh_rounding'   
				),
				'options'     => array(
					'shipping_name' => __( 'Method title', 'wf_woocommerce_shipping_pro' ),
					'method_group' => __( 'Method group', 'wf_woocommerce_shipping_pro' ),					
					'zone_list' => __( 'Zone list', 'wf_woocommerce_shipping_pro' ),					
					'country_list'    => __( 'Country list', 'wf_woocommerce_shipping_pro' ),
					'state_list'    => __( 'State list', 'wf_woocommerce_shipping_pro' ),
					'postal_code'    => __( 'Postal code', 'wf_woocommerce_shipping_pro' ),
					'shipping_class'    => __( 'Shipping class', 'wf_woocommerce_shipping_pro' ),
					'product_category'    => __( 'Product category', 'wf_woocommerce_shipping_pro' ),
					'weight'    => __( 'Weight', 'wf_woocommerce_shipping_pro' ),
					'item'    => __( 'Item', 'wf_woocommerce_shipping_pro' ),
					'price'    => __( 'Price', 'wf_woocommerce_shipping_pro' ),
					'cost_based_on'    => __( 'Rate Based on', 'wf_woocommerce_shipping_pro' ),
					'fee'    => __( 'Base cost', 'wf_woocommerce_shipping_pro' ),
					'cost'    => __( 'Cost/unit', 'wf_woocommerce_shipping_pro' ),
					'weigh_rounding'    => __( 'Round', 'wf_woocommerce_shipping_pro' )					
				),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Choose matrix columns', 'wf_woocommerce_shipping_pro' )
				)
			),
			'calculation_mode' => array(
				'title'       => __( 'Calculation Mode', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'select',
				'description' => __( 'Per Order: Shipping calculation will be done on the entire cart together. Total weight of the cart will be used with the selected rule to calculate the shipping.  Rule will be satisfied if one of the items in the cart meets the criteria. Per Item: Shipping calculation will happen on item wise, item weight multiply with the quantity will be used with the selected rule to calculate the shipping cost for the item, all the item cost will be summed to find the final cost.' , 'wf_woocommerce_shipping_pro' ),
				'default'     => 'per_order_max_cost',
				'options'     => array(
					'per_line_item_max_cost' => __( 'Per Line Item Max Cost: Calculate shipping cost per Line item. Choose maximum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_line_item_min_cost' => __( 'Per Line Item Min Cost: Calculate shipping cost per Line item. Choose minimum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_item_max_cost' => __( 'Per Item Max Cost: Calculate shipping cost per item. Choose maximum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_item_min_cost' => __( 'Per Item Min Cost: Calculate shipping cost per item. Choose minimum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_order_max_cost'=> __( 'Per Order Max Cost: Calculate shipping cost per order. Choose maximum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_order_min_cost'=> __( 'Per Order Min Cost: Calculate shipping cost per order. Choose minimum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_category_max_cost'=> __( 'Per Category Max Cost: Calculate shipping cost per category. Choose maximum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_category_min_cost'=> __( 'Per Category Min Cost: Calculate shipping cost per category. Choose minimum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_shipping_class_max_cost'=> __( 'Per Shipping class Max Cost: Calculate shipping cost per shipping class. Choose maximum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
					'per_shipping_class_min_cost'=> __( 'Per Shipping class Min Cost: Calculate shipping cost per shipping class. Choose minimum rate in case multiple rule.', 'wf_woocommerce_shipping_pro' ),
				),
			),			
			'tax_status' => array(
				'title'       => __( 'Tax Status', 'wf_woocommerce_shipping_pro' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'none',
				'options'     => array(
					'taxable' => __( 'Taxable', 'wf_woocommerce_shipping_pro' ),
					'none'    => __( 'None', 'wf_woocommerce_shipping_pro' ),
				),
			),
			'remove_free_text'    => array(
				'title'   => __( 'Remove Free Text', 'wf_woocommerce_shipping_pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Remove default (Free) text from shipping label', 'wf_woocommerce_shipping_pro' ),
				'default' => 'no',
			),
			'debug'    => array(
				'title'   => __( 'Debug', 'wf_woocommerce_shipping_pro' ),
				'type'    => 'checkbox',
				'label'   => __( 'Debug this shipping method', 'wf_woocommerce_shipping_pro' ),
				'default' => 'no',
			),						
		);
				
	}
	
	public function wf_remove_local_pickup_free_label($full_label, $method){
		if( strpos($method->id, $this->id) !== false) $full_label = str_replace(' (Free)','',$full_label);
		return $full_label;
	}
	
	function wf_hidden_matrix_column($column_name){
		return in_array($column_name,$this->displayed_columns) ? '' : 'hidecolumn';	
	}
	
	function wf_rule_to_text($key ,$box){
		$weight_unit 	= strtolower( get_option('woocommerce_weight_unit') );
		$currency_symbol = get_woocommerce_currency_symbol();
		$text = "";
		//TODO country_list state_list shipping_class postal_code  
		if(!empty($box['min_weight']))  $text .= " If the order weight is more than ".$box['min_weight']."$weight_unit";	
		if(!empty($box['max_weight'])) $text .= (empty($box['min_weight']) ? "If the order weight is" : " and") . " less than or equal to ".$box['max_weight']."$weight_unit";
		if(!empty($box['min_item']))  $text .= (!empty($text) ?  " and" : " If") . " the order item count is more than ".$box['min_item'];	
		if(!empty($box['max_item'])) $text .= (empty($box['min_item']) ? "If the order item count is" : " and") . " less than or equal to ".$box['max_item'];
		if(!empty($box['min_price']))  $text .= (!empty($text) ?  " and" : " If") . " the price is more than ".$box['min_price']."$currency_symbol";	
		if(!empty($box['max_price'])) $text .= (empty($box['min_price']) ? "If the price  is" : " and") . " less than or equal to ".$box['max_price']."$currency_symbol";
		if(!empty($box['fee'])) $text .= (!empty($text) ?  " then" : "") . " shipping cost is $currency_symbol".$box['fee'];					
		if(!empty($box['cost'])){	
			if(!empty($box['cost_based_on']) && $box['cost_based_on'] == "item"){
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per item  $currency_symbol".$box['cost'];
				$text .= empty($box['min_item']) ? "." : " for the remaining item count above ".$box['min_item'];					
			}
			elseif(!empty($box['cost_based_on']) && $box['cost_based_on'] == "price"){
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per $currency_symbol $currency_symbol".$box['cost'];
				$text .= empty($box['min_price']) ? "." : " for the remaining price above ".$box['min_price']."$currency_symbol.";					
			}else{
				$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per $weight_unit  $currency_symbol".$box['cost'];
				$text .= empty($box['min_weight']) ? "." : " for the remaining weight above ".$box['min_weight']."$weight_unit.";					
			}			
		}
		if(!empty($box['weigh_rounding'])){
			if(!empty($box['cost_based_on']) && $box['cost_based_on'] == "item"){
				$text .= "(Item count is rounded up to the nearest ".$box['weigh_rounding'].").";
			}
			elseif(!empty($box['cost_based_on']) && $box['cost_based_on'] == "price"){
				$text .= "(Price is rounded up to the nearest ".$box['weigh_rounding']."$currency_symbol).";
			}else{
				$text .= "(Weight is rounded up to the nearest ".$box['weigh_rounding']."$weight_unit).";
			}			
			
		}			
		return $text;
	}
	
	public function validate_rate_matrix_field( $key ) {
		$rate_matrix         = isset( $_POST['rate_matrix'] ) ? $_POST['rate_matrix'] : array();
		return $rate_matrix;
	}

	public function generate_rate_matrix_html() {
		
		ob_start();					
		?>
		<tr valign="top" id="packing_rate_matrix">
			<td class="titledesc" colspan="2" style="padding-left:0px">
				<strong><?php _e( 'Rate matrix:', 'wf_woocommerce_shipping_pro' ); ?></strong><br><br>
				<style type="text/css">
					.shipping_pro_boxes .row_data td
					{
						border-bottom: 1pt solid #e1e1e1;
					}
					
					.shipping_pro_boxes input, 
					.shipping_pro_boxes select, 
					.shipping_pro_boxes textarea,
					.shipping_pro_boxes .select2-container-multi .select2-choices{
						background-color: #fbfbfb;
						border: 1px solid #e9e9e9;
					}
					 					
					.shipping_pro_boxes td, .shipping_pro_services td {
						vertical-align: top;
							padding: 4px 7px;
							
					}
					.shipping_pro_boxes th, .shipping_pro_services th {
						padding: 9px 7px;
					}
					.shipping_pro_boxes td input {
						margin-right: 4px;
					}
					.shipping_pro_boxes .check-column {
						vertical-align: top;
						text-align: left;
						padding: 4px 7px;
					}
					.shipping_pro_services th.sort {
						width: 16px;
					}
					.shipping_pro_services td.sort {
						cursor: move;
						width: 16px;
						padding: 0 16px;
						cursor: move;
						background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
					}
					@media screen and (min-width: 781px) 
					{
						th.tiny_column
						{
						  width:2em;
						  max-width:2em;
						  min-width:2em;									  
						}
						th.small_column
						{
						   width:4em;	
						   max-width:4em; 	
						   min-width:4em;
						}
						th.smallp_column
						{
						   width:4.5em;	
						   max-width:4.5em; 	
						   min-width:4.5em;
						}
						th.medium_column
						{
						   min-width:90px;	 
						}
						th.big_column
						{
							min-width:250px;
						}									
					}
					th.hidecolumn,
					td.hidecolumn
					{
							display:none;
					}
								
				</style>
				
				<table class="shipping_pro_boxes widefat" style="background-color:#f6f6f6;">
					<thead>
						<tr>
							<th class="check-column tiny_column"><input type="checkbox" /></th>
							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('shipping_name');?>">
							<?php _e( 'Method title', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Would you like this shipping rule to have its own shipping service name? If so, please choose a name. Leaving it blank will use Method Title as shipping service name.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('method_group');?>">
							<?php _e( 'Method group', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Useful if multiple shipping method needs to be returned. All the shipping rule can be grouped to air and ground by providing Method Group appropriate. And different shipping rates for air and ground will be provided to the users. Leaving it blank will only return one shipping rate.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="big_column <?php echo $this->wf_hidden_matrix_column('zone_list');?>">
							<?php _e( 'zone list', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'You can choose the zones here once you configured', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="big_column <?php echo $this->wf_hidden_matrix_column('country_list');?>">
							<?php _e( 'Country list', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of countries which this rule will be applicable', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="big_column <?php echo $this->wf_hidden_matrix_column('state_list');?>">
							<?php _e( 'State list', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of states which this rule will be applicable', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="small_column <?php echo $this->wf_hidden_matrix_column('postal_code');?>">
							<?php _e( 'Postal code', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Post/Zip code for this rule. Semi-colon (;) separate multiple values. Leave blank to apply to all areas. Wildcards (*) can be used. Ranges for numeric postcodes (e.g. 12345-12350) will be expanded into individual postcodes.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>
							</th>
							<th class="big_column <?php echo $this->wf_hidden_matrix_column('shipping_class');?>">
							<?php _e( 'Shipping class', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of shipping class which this rule will be applicable', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="big_column <?php echo $this->wf_hidden_matrix_column('product_category');?>">
							<?php _e( 'Product category', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of product category which this rule will be applicable. Only the product category directly assigned to the products will be considered.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>
							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('weight');?>">
							<?php _e( 'Min-Max', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the min value entered is .25 and the order weight is .25 then this rule will be ignored. if the min value entered is .25 and the order weight is .26 then this rule will be be applicable for calculating shipping cost. if the max value entered is .25 and the order weight is .26 then this rule will be ignored. if the max value entered is .25 and the order weight is .25 or .24 then this rule will be be applicable for calculating shipping cost.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(weight)', 'wf_woocommerce_shipping_pro' );  ?> 
							</th>
							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('item');?>">
							<?php _e( 'Min-Max', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the min value entered is 25 and the item count is 25 then this rule will be ignored. if the min value entered is 25 and the item count is 26 then this rule will be be applicable for calculating shipping cost. if the max value entered is 25 and the item count is 26 then this rule will be ignored. if the max value entered is 25 and the item count is 25 or 24 then this rule will be be applicable for calculating shipping cost.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(item count)', 'wf_woocommerce_shipping_pro' );  ?> 
							</th>
							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('price');?>">
							<?php _e( 'Min-Max', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the min value entered is 25 and the price is 25 then this rule will be ignored. if the min value entered is 25 and the price is 26 then this rule will be be applicable for calculating shipping cost. if the max value entered is 25 and the price is 26 then this rule will be ignored. if the max value entered is 25 and the price is 25 or 24 then this rule will be be applicable for calculating shipping cost.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(price)', 'wf_woocommerce_shipping_pro' );  ?>  
							</th>
							<th class="small_column <?php echo $this->wf_hidden_matrix_column('cost_based_on');?>">
							<?php _e( 'Based on', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Shipping rate calculation based on Weight/Item/Price.', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>
							</th>
							<th class="small_column <?php echo $this->wf_hidden_matrix_column('fee');?>">
							<?php _e( 'Cost', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Base/Fixed cost of the shipping irrespective of the weight/item count/price', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(base)', 'wf_woocommerce_shipping_pro' );  ?> 
							</th>
							<th class="small_column <?php echo $this->wf_hidden_matrix_column('cost');?>">
							<?php _e( 'Cost', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Per weight/item count/price unit cost. This cost will be added on above the base cost.If select Based on as weight, Total shipping Cost = Base cost + (order weight - minimum weight) * cost per unit', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(per unit)', 'wf_woocommerce_shipping_pro' );  ?> 
							</th>
							<th class="smallp_column <?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>">
							<?php _e( 'Round', 'wf_woocommerce_shipping_pro' );  ?>
							<img class="help_tip" style="float:none;" data-tip="<?php _e( 'How would you like to round weight/item count/price? Lets take an example with weight. if the value entered is 0.5 and the order weight is 4.4kg then shipping cost will be calculated for 4.5kg, if the value entered is 1 and the order weight is 4.4kg then shipping cost will be calculated for 5kg, if the value entered is 0 and the order weight is 4.4kg then shipping cost will be calculated for 4.4kg', 'wf_woocommerce_shipping_pro' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>
							</th>																										
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="4">
								<a href="#" class="button insert"><?php _e( 'Add rule', 'wf_woocommerce_shipping_pro' ); ?></a>
								<a href="#" class="button remove"><?php _e( 'Remove rule(es)', 'wf_woocommerce_shipping_pro' ); ?></a>
								<a href="#" class="button duplicate"><?php _e( 'Duplicate rule(es)', 'wf_woocommerce_shipping_pro' ); ?></a>
							</th>
							<th colspan="<?php echo $this->col_count-4;?>">
								<small class="description"><a href="<?php echo admin_url( 'admin.php?import=shippingpro_rate_matrix_csv' ); ?>" class="button"><?php _e( 'Import CSV', 'wf_woocommerce_shipping_pro' ); ?></a>
								<a href="<?php echo admin_url( 'admin.php?wf_export_shippingpro_rate_matrix_csv=true' ); ?>" class="button"><?php _e( 'Export CSV', 'wf_woocommerce_shipping_pro' ); ?></a>&nbsp;<?php _e( 'Weight Unit & Dimensions Unit as per WooCommerce settings.', 'wf_woocommerce_shipping_pro' ); ?>
								</small>
							</th>
						</tr>
					</tfoot>
					<tbody id="rates">
					<?php								
					$matrix_rowcount = 0;
					if ( $this->rate_matrix ) {
						foreach ( $this->rate_matrix as $key => $box ) {												
							$defined_countries = isset($box['country_list']) ? $box['country_list'] : array();
							$defined_zones = isset($box['zone_list']) ? $box['zone_list'] : array();
							$defined_states = isset($box['state_list']) ? $box['state_list'] : array();
							$defined_shipping_classes = isset($box['shipping_class']) ? $box['shipping_class'] : array();
							$defined_product_category = isset($box['product_category']) ? $box['product_category'] : array();
							?>
							<tr class="rule_text"><td colspan="<?php echo $this->col_count;?>" style="font-style:italic; color:#a8a8a8;"><strong><?php echo $this->wf_rule_to_text($key ,$box);?></strong></td></tr>
							<tr class="row_data"><td class="check-column"><input type="checkbox" /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type='text' size='20' name='rate_matrix[<?php echo $key;?>][shipping_name]' placeholder='<?php echo $this->title;?>' title='<?php echo isset($box['shipping_name']) ? $box['shipping_name']:$this->title;?>' value='<?php echo isset($box['shipping_name']) ? $box['shipping_name']:"";?>' /></td>
							
							<td class="<?php echo $this->wf_hidden_matrix_column('method_group');?>"><input type='text' size='10' name='rate_matrix[<?php echo $key;?>][method_group]' title='<?php echo isset($box['method_group']) ? $box['method_group']:'';?>' value='<?php echo isset($box['method_group']) ? $box['method_group']:"";?>' />
							</td>
							
							<td class="<?php echo $this->wf_hidden_matrix_column('zone_list');?>" style='overflow:visible'>
							<select id="zone_list_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="zone_list_combo" multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][zone_list][]'>
									<?php 
									$zone_list = $this->wf_get_zone_list();
									foreach($zone_list as $zoneKey => $zoneValue){ ?>
									<option value="<?php echo $zoneKey;?>" <?php selected(in_array($zoneKey,$defined_zones),true);?>><?php echo $zoneValue;?>
									</option>
									<?php } ?>															
								</select>
							</td>
							
							<td class="<?php echo $this->wf_hidden_matrix_column('country_list');?>" style='overflow:visible'>
								<select id="country_list_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="country_list_combo" multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][country_list][]'>
									<option value="any_country" <?php selected(in_array('any_country',$defined_countries),true);?>>Any Country</option>
									<option value="rest_world" <?php selected(in_array('rest_world',$defined_countries),true);?>>Rest of the world</option>
									<?php foreach($this->shipping_countries as $countryKey => $countryValue){ ?>
									<option value="<?php echo $countryKey;?>" <?php selected(in_array($countryKey,$defined_countries),true);?>><?php echo $countryValue;?></option>
									<?php } ?>															
								</select>
							</td>

							<td class="<?php echo $this->wf_hidden_matrix_column('state_list');?>" style='overflow:visible'>
								<select id="state_list_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="state_list_combo" multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][state_list][]'>
									<option value="any_state" <?php selected(in_array('any_state',$defined_states),true);?>>Any State</option>
									<option value="rest_country" <?php selected(in_array('rest_country',$defined_states),true);?>>Rest of the country</option>
									<?php $this->wf_state_dropdown_options($defined_states); ?>															
								</select>
							</td>
							<td class="<?php echo $this->wf_hidden_matrix_column('postal_code');?>"><input type='text' size='10' name='rate_matrix[<?php echo $key;?>][postal_code]' 		value='<?php echo isset($box['postal_code']) ? $box['postal_code']:'';?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('shipping_class');?>" style='overflow:visible'>
							<select id="shipping_class_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="shipping_class_combo" multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][shipping_class][]'>
								<option value="any_shipping_class" <?php selected(in_array('any_shipping_class',$defined_shipping_classes),true);?>>Any Shipping Class</option>
								<option value="rest_shipping_class" <?php selected(in_array('rest_shipping_class',$defined_shipping_classes),true);?>>Rest of the shipping classes</option>
								<?php $this->wf_shipping_class_dropdown_options($defined_shipping_classes); ?>															
							</select>
							</td>
							<td class="<?php echo $this->wf_hidden_matrix_column('product_category');?>" style='overflow:visible'>
							<select id="product_category_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="product_category_combo"  multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][product_category][]'>
								<option value="any_product_category" <?php selected(in_array('any_product_category',$defined_product_category),true);?>>Any Product category</option>
								<option value="rest_product_category" <?php selected(in_array('rest_product_category',$defined_product_category),true);?>>Rest of the Product categories</option>
								<?php $this->wf_product_category_dropdown_options($defined_product_category); ?>															
							</select>
							</td>
							<td class="<?php echo $this->wf_hidden_matrix_column('weight');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][min_weight]' 		value='<?php echo $box['min_weight']; ?>' /><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][max_weight]' 		value='<?php echo $box['max_weight']; ?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('item');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][min_item]' 		value='<?php echo isset($box['min_item']) ? $box['min_item'] : ''; ?>' /><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][max_item]' 		value='<?php echo isset($box['max_item']) ? $box['max_item'] : ''; ?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('price');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][min_price]' 		value='<?php echo isset($box['min_price']) ? $box['min_price'] : ''; ?>' /><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][max_price]' 		value='<?php echo isset($box['max_price']) ? $box['max_price'] : ''; ?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('cost_based_on');?>">
							<select id="cost_based_on_<?php echo $key;?>" class="select singleselect" name="rate_matrix[<?php echo $key;?>][cost_based_on]" data-identifier="cost_based_on">
							<option value="weight" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','weight');?> >Weight</option>
							<option value="item" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','item');?>>Item</option>
							<option value="price" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','price');?>>Price</option></select></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>"><input type='text' size='5' name='rate_matrix[<?php echo $key;?>][fee]'				value='<?php echo $box['fee']; ?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('cost');?>"><input type='text' size='5' name='rate_matrix[<?php echo $key;?>][cost]'			value='<?php echo $box['cost']; ?>' /></td>
							<td class="<?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][weigh_rounding]' 	value='<?php echo $box['weigh_rounding']; ?>' /></td>																						
							</tr>
							<?php
							if(!empty($key) && $key >= $matrix_rowcount)
								$matrix_rowcount = $key;
						}
					}
					?>
					<input type="hidden" id="matrix_rowcount" value="<?php echo$matrix_rowcount;?>" />
					</tbody>
				</table>
				<script type="text/javascript">																	
					jQuery(window).load(function(){									
						jQuery('.shipping_pro_boxes .insert').click( function() {
							var $tbody = jQuery('.shipping_pro_boxes').find('tbody');
							var size = $tbody.find('#matrix_rowcount').val();
							if(size){
								size = parseInt(size)+1;
							}
							else
								size = 0;
							
							var code = '<tr class="new row_data"><td class="check-column"><input type="checkbox" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type="text" size="20" name="rate_matrix['+size+'][shipping_name]" placeholder="<?php echo $this->title;?>" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('method_group');?>"><input type="text" size="10" name="rate_matrix['+size+'][method_group]"/></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('zone_list');?>" style="overflow:visible">\
								<select id="zone_list_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="zone_list_combo" multiple="true" style="width:100%;" name="rate_matrix['+size+'][zone_list][]">\
									<?php 
									$zone_list = $this->wf_get_zone_list();
									foreach($zone_list as $zoneKey => $zoneValue){ ?><option value="<?php echo esc_attr( $zoneKey ); ?>" ><?php echo esc_attr( $zoneValue ); ?></option>\
									<?php } ?>
								</select>\
							</td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('country_list');?>" style="overflow:visible">\
								<select id="country_list_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="country_list_combo" multiple="true" style="width:100%;" name="rate_matrix['+size+'][country_list][]">\
								<option value="any_country">Any Country</option><option value="rest_world">Rest of the world</option>\
								<?php foreach($this->shipping_countries as $countryKey => $countryValue){ ?><option value="<?php echo esc_attr( $countryKey ); ?>" ><?php echo esc_attr( $countryValue ); ?></option>\
								<?php } ?></select>\
							</td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('state_list');?>" style="overflow:visible">\
							<select id="state_list_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="state_list_combo"  multiple="true" style="width:100%;" name="rate_matrix['+size+'][state_list][]">\
							<option value="any_state">Any State</option><option value="rest_country">Rest of the Country</option>\
							<?php $this->wf_state_dropdown_options(array(),true); ?></select>\
							</td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('postal_code');?>"><input type="text" size="10" name="rate_matrix['+size+'][postal_code]"  /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('shipping_class');?>" style="overflow:visible">\
							<select id="shipping_class_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="shipping_class_combo" multiple="true" style="width:100%;" name="rate_matrix['+size+'][shipping_class][]">\
							<option value="any_shipping_class">Any Shipping class</option><option value="rest_shipping_class">Rest of the shipping classes</option>\
							<?php $this->wf_shipping_class_dropdown_options(); ?></select>\
							</td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('product_category');?>" style="overflow:visible">\
							<select id="product_category_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="product_category_combo"  multiple="true" style="width:100%;" name="rate_matrix['+size+'][product_category][]">\
							<option value="any_product_category">Any Product category</option><option value="rest_product_category">Rest of the Product categories</option>\
							<?php $this->wf_product_category_dropdown_options(); ?></select>\
							</td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('weight');?>"><input type="text" size="3" name="rate_matrix['+size+'][min_weight]"  /><input type="text" size="3" name="rate_matrix['+size+'][max_weight]" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('item');?>"><input type="text" size="3" name="rate_matrix['+size+'][min_item]"  /><input type="text" size="3" name="rate_matrix['+size+'][max_item]" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('price');?>"><input type="text" size="3" name="rate_matrix['+size+'][min_price]"  /><input type="text" size="3" name="rate_matrix['+size+'][max_price]" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('cost_based_on');?>"><select id="cost_based_on_'+size+'" class="select singleselect" data-identifier="cost_based_on" name="rate_matrix['+size+'][cost_based_on]"><option value="weight" selected>Weight</option><option value="item">Item</option><option value="price">Price</option></select></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>"><input type="text" size="5" name="rate_matrix['+size+'][fee]" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('cost');?>"><input type="text" size="5" name="rate_matrix['+size+'][cost]" /></td>\
							<td class="<?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>"><input type="text" size="3" name="rate_matrix['+size+'][weigh_rounding]" /></td>\
							</tr>';										
							$tbody.append( code );
							if(typeof wc_enhanced_select_params == 'undefined')
								$tbody.find('tr:last').find("select.chosen_select").chosen();
							else
								$tbody.find('tr:last').find("select.chosen_select").trigger( 'wc-enhanced-select-init' );
							
								
							$tbody.find('#matrix_rowcount').val(size);
							return false;
						} );

						jQuery('.shipping_pro_boxes .remove').click(function() {
							var $tbody = jQuery('.shipping_pro_boxes').find('tbody');

							$tbody.find('.check-column input:checked').each(function() {
								jQuery(this).closest('tr').prev('.rule_text').remove();
								jQuery(this).closest('tr').remove();
								});

							return false;
						});
						
						jQuery('.shipping_pro_boxes .duplicate').click(function() {
							var $tbody = jQuery('.shipping_pro_boxes').find('tbody');

							var new_trs = [];
							
							$tbody.find('.check-column input:checked').each(function() {
								var $tr    = jQuery(this).closest('tr');
								var $clone = $tr.clone();
								var size = jQuery('#matrix_rowcount').val();
								if(size)
									size = parseInt(size)+1;
								else
									size = 0;
								
								
								$tr.find('select.multiselect').each(function(i){
									var selecteddata;
									if(typeof wc_enhanced_select_params == 'undefined')
										selecteddata = jQuery(this).chosen().val();
									else
										selecteddata = jQuery(this).select2('data');
									
									if ( selecteddata ) {
										var arr = [];
										jQuery.each( selecteddata, function( id, text ) {
											if(typeof wc_enhanced_select_params == 'undefined')
												arr.push(text);
											else
												arr.push(text.id);											
										});
										var currentIdentifierAttr = jQuery(this).attr('data-identifier'); 
										if(currentIdentifierAttr){
											$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(arr);
											//$clone.find('select#' + this.id).val(arr);
										}										
									}
								});
								
								$tr.find('select.no_multiselect').each(function(i){
									var selecteddata = [];
									jQuery.each(jQuery(this).find("option:selected"), function(){         
										selecteddata.push(jQuery(this).val());
									});
									
									var currentIdentifierAttr = jQuery(this).attr('data-identifier'); 
									if(currentIdentifierAttr){
										$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(selecteddata);
									}
								});
								
								$tr.find('select.singleselect').each(function(i){
									var selecteddata = jQuery(this).val();
									if ( selecteddata ) {
										var currentIdentifierAttr = jQuery(this).attr('data-identifier'); 
										if(currentIdentifierAttr){
											$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(selecteddata);
											//$clone.find('select#' + this.id).val(selecteddata);										
										}
									}
								});
								
								
								if(typeof wc_enhanced_select_params == 'undefined')
									$clone.find('div.chosen-container, div.chzn-container').remove();									
								else
									$clone.find('div.multiselect').remove();								
								
								$clone.find('.multiselect').show();
								$clone.find('.multiselect').removeClass("enhanced chzn-done");
								// find all the inputs within your new clone and for each one of those
								$clone.find('input[type=text], select').each(function() {
									var currentNameAttr = jQuery(this).attr('name'); 
									if(currentNameAttr){
										var newNameAttr = currentNameAttr.replace(/\d+/, size);
										jQuery(this).attr('name', newNameAttr);   // set the incremented name attribute 
									}
									var currentIdAttr = jQuery(this).attr('id'); 
									if(currentIdAttr){
										var currentIdAttr = currentIdAttr.replace(/\d+/, size);
										jQuery(this).attr('id', currentIdAttr);   // set the incremented name attribute 
									}
								});
								//$tr.after($clone);
								//$clone.find('select.chosen_select').trigger( 'chosen_select-init' );
								new_trs.push($clone);
								jQuery('#matrix_rowcount').val(size);
								//jQuery("select.chosen_select").trigger( 'chosen_select-init' );							
							});
							if(new_trs)
							{
								var lst_tr    = $tbody.find('.check-column :input:checkbox:checked:last').closest('tr');
								jQuery.each( new_trs.reverse(), function( id, text ) {
										//adcd.after(text);
										lst_tr.after(text);
										if(typeof wc_enhanced_select_params == 'undefined')
											text.find('select.chosen_select').chosen();			
										else
											text.find('select.chosen_select').trigger( 'wc-enhanced-select-init' );																	
									});
							}
							$tbody.find('.check-column input:checked').removeAttr('checked');
							return false;
						});									
					});
				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	private function wf_get_zone_list(){
		$zone_list = array();
		if( class_exists('WC_Shipping_Zones') ){
			$zones_obj = new WC_Shipping_Zones;
			$zones = $zones_obj::get_zones();
			$zone_list[0] = 'Rest of the World'; //rest of the zone always have id 0, which is not available in the method get_zone()
			foreach ($zones as $key => $zone) {
				$zone_list[$key] = $zone['zone_name'];
			}
		}
		return $zone_list;
	}

	function calculate_shipping( $package = array() ) {
		$rules = $this->wf_filter_rules( $this->wf_find_zone($package), $package['destination']['country'], $package['destination']['state'], $package['destination']['postcode'], $this->calc_mode_strategy->wf_find_shipping_classes($package), $this->calc_mode_strategy->wf_find_product_category($package), $package );
		$costs = $this->wf_calc_cost($rules, $package);	
		$this->wf_add_rate(apply_filters( 'wf_woocommerce_shipping_pro_shipping_costs', $costs),$package);	
	}

	public function wf_find_zone($package){
		$matching_zones=array();		
		if( class_exists('WC_Shipping_Zones') ){
			$zones_obj = new WC_Shipping_Zones;
			$matches = $zones_obj::get_zone_matching_package($package);
			if( method_exists ( $matches, 'get_id' ) ){ //if WC 3.0+
				$zone_id = $matches->get_id();
			}else{
				$zone_id =  $matches->get_zone_id();
			}
			array_push( $matching_zones, $zone_id );
		}
		return $matching_zones;
	}
	
	function wf_get_weight($package_items){
		$total_weight = 0;
		foreach($package_items as $package_item){		
			$_product = $package_item['data'];
			$total_weight += $_product->get_weight() * $package_item['quantity'];
		}
		return $total_weight;
	}
	
	function wf_get_price($package_items){
		$total_price = 0;
		foreach($package_items as $package_item){		
			$_product = $package_item['data'];
			$total_price += $_product->get_price() * $package_item['quantity'];
		}
		return $total_price;
	}
	
	function wf_get_item_count($package_items){
		$total_count = 0;
		foreach($package_items as $package_item){		
			$_product = $package_item['data'];
			$total_count += apply_filters( 'wf_shipping_pro_item_quantity', $package_item['quantity'],$_product->id);			
		}
		return $total_count;
	}
	
	function wf_filter_rules( $zone, $country, $state, $post_code, $shipping_classes,$product_category,$package ) {
		$selected_rules = array();
		if(sizeof($this->rate_matrix) > 0) {
			foreach($this->rate_matrix as $key => $rule ) {
				$satified_general=false;
				if( $this->wf_compare_array_rule_field($rule,'zone_list',$zone,'','')
					&& $this->wf_compare_array_rule_field($rule,'country_list',$country,'rest_world','any_country')
					&& $this->wf_compare_array_rule_field($rule,'state_list',$country.':'.strtoupper($state),'rest_country','any_state')
					&& $this->wf_compare_post_code($rule,'postal_code',$post_code,'','*')){
						$satified_general=true;	
				}
				
				if($satified_general){
					foreach ( $this->calc_mode_strategy->wf_get_grouped_package($package) as $item_id => $values ) {
						if(	$this->wf_compare_array_rule_field($rule,'shipping_class',$shipping_classes,'rest_shipping_class','any_shipping_class',$item_id)
							&& $this->wf_compare_array_rule_field($rule,'product_category',$product_category,'rest_product_category','any_product_category',$item_id)
							&& $this->wf_compare_range_field($rule,'weight',$this->wf_get_weight($values))
							&& $this->wf_compare_range_field($rule,'item',$this->wf_get_item_count($values))
							&& $this->wf_compare_range_field($rule,'price',$this->wf_get_price($values)) ){
								if(!isset($rule['item_ids'])) $rule['item_ids'] = array(); 
								$rule['item_ids'][] = $item_id;
						}												
					}
					if(isset($rule['item_ids'])) $selected_rules[] = $rule;						
				}					
			}					
		}
		return $selected_rules;	 
	}
	
	function wf_compare_post_code($rule,$field_name,$input_value, $const_rest, $const_any ){
		//if rule_value is null then shipping rule will be acceptable for all
		if (!empty($rule[$field_name]) && in_array($field_name,$this->displayed_columns) ){
			$rule_value = $rule[$field_name];
			$this->wf_debug("rule_value : $rule_value");
		}
		else	
			return true;
		
		if($rule_value == $const_any)
				return true;
			
		if ( ! empty( $rule_value ) ) {
			
			$postcodes = explode( ';', $rule_value );
			$postcodes = array_map( 'strtoupper', array_map( 'wc_clean', $postcodes ) );
			$input_value = strtoupper($input_value);

			foreach( $postcodes as $postcode ) {
				if ( strstr( $postcode, '-' ) ) {
					$this->wf_debug("$postcode - $input_value ");
					$postcode_parts = explode( '-', $postcode );
					if ( is_numeric( $postcode_parts[0] ) && is_numeric( $postcode_parts[1] ) && $postcode_parts[1] > $postcode_parts[0] ) {
						for ( $i = $postcode_parts[0]; $i <= $postcode_parts[1]; $i ++ ) {
							if ( ! $i )
								continue;

							if ( strlen( $i ) < strlen( $postcode_parts[0] ) )
								$i = str_pad( $i, strlen( $postcode_parts[0] ), "0", STR_PAD_LEFT );

							if($input_value == $i)
							{
								$this->wf_debug("$i matched $input_value ");							
								return true;
							}								
						}
					}
				}
				elseif ( strstr( $postcode, '*' ) )
				{
					$this->wf_debug("$postcode * $input_value ");
					if(preg_match('/\A'.str_replace('*', '.', $postcode).'/',$input_value))
						return true;
				}
				else {
					$this->wf_debug("$postcode == $input_value ");
					if ( $postcode == $input_value)
						return true;
				}
			}
		}
		return false;
	}
	
	function wf_compare_array_rule_field( $rule, $field_name, $input_value, $const_rest, $const_any, $item_id=false ){
		//if rule_value is null then shipping rule will be acceptable for all
		global $rule_value;
		if (!empty($rule[$field_name]) && in_array($field_name,$this->displayed_columns) ){
			$rule_value = $rule[$field_name];
			$this->wf_debug("rule_value : $rule_value[0]");
		}
		else	
			return true;
		
		if (is_array($rule_value) && count($rule_value) == 1){
			if($rule_value[0] == $const_rest)	
				return $this->wf_partof_rest_of_the($input_value,$field_name,$item_id,$rule);
			elseif($rule_value[0] == $const_any)
				return true;	
		}
		
		if(!is_array($input_value)){
			return in_array($input_value,$rule_value);
		}
		else{			
			if( $item_id ){
				if( isset($input_value[$item_id]) && is_array($input_value[$item_id]) ){
					if( $this->and_logic && ($field_name == 'product_category' || $field_name == 'shipping_class' ) ){
						// return $input_value[$item_id] == $rule_value; //If both arrays are equal, for strict AND logic.
						return count(array_intersect($rule_value, $input_value[$item_id])) == count($rule_value);
					}else{
						return count( array_intersect($input_value[$item_id],$rule_value) ) > 0;
					}
				}
				else
					return false;
			}else{ //case of zone.
				return count(array_intersect($input_value,$rule_value)) > 0;
			}
		}
	}
	
	function wf_compare_range_field( $rule,$field_name, $totalweight) {
		$weight = $totalweight;
		if (!empty($rule['min_'.$field_name]) && $weight <= $rule['min_'.$field_name]) 
			return false;
		if (!empty($rule['max_'.$field_name]) && $weight > $rule['max_'.$field_name]) 
			return false;					
		return true;	
	}	

	function wf_partof_rest_of_the( $input_value,$field_name,$item_id=false ,$current_rule) {
		global $combined_rule_value;
		$combined_rule_value = array();
		if ( sizeof( $this->rate_matrix ) > 0) {
			foreach ( $this->rate_matrix as $key => $rule ) {
				if(!empty($rule[$field_name]) && ($rule['method_group'] == $current_rule['method_group']))
					$combined_rule_value = array_merge($rule[$field_name],$combined_rule_value);
				
			}					
		}
		
		if(!is_array($input_value)){
			//county not defined as part of any other rule 
			if(!in_array($input_value,$combined_rule_value))
				return true;
			return false;
		}
		else{
			//returns true if at least one product category doesn't exist combined list.
			if($item_id !== false && isset($input_value[$item_id]) && is_array($input_value[$item_id])){
				//This is a case where product with NO shipping class in the cart. 
				//This will not handle the case where multiple product in the group and some products are not 'NO Shipping Class' 
				//So finally if its NO Shipping Class case we will consider it as matching with Rest of the shipping class. 
				if(empty($input_value[$item_id]))
					return true;
				return count(array_diff($input_value[$item_id],$combined_rule_value)) > 0;
			}
				
			return false;				
		}						
	}
	
	function wf_calc_cost( $rules ,$package) {
		$cost = array();
		if ( sizeof($rules) > 0) {
			$grouped_package = $this->calc_mode_strategy->wf_get_grouped_package($package);
			foreach ( $rules as $key => $rule) {
				$method_group = isset($rule['method_group']) ? $rule['method_group'] : null;	
				$item_ids = isset($rule['item_ids']) ? $rule['item_ids'] : null;
				if(!empty($item_ids)){
					foreach($item_ids as $item_key => $item_id){
						if(empty($grouped_package[$item_id])) continue;
						$shipping_cost = $this->wf_get_rule_cost($rule,$grouped_package[$item_id]);
						if($shipping_cost !== false){
							if(isset($cost[$method_group]['cost'][$item_id])){
								if($cost[$method_group]['cost'][$item_id] > $shipping_cost && $this->row_selection_choice == 'min_cost'
								|| $cost[$method_group]['cost'][$item_id] < $shipping_cost && $this->row_selection_choice == 'max_cost'){
									 $cost[$method_group]['cost'][$item_id] = $shipping_cost;
									 $cost[$method_group]['shipping_name'] = !empty($rule['shipping_name']) ? $rule['shipping_name'] : $this->title;
								}								
							}
							else{
								if(!isset($cost[$method_group])) {
									$cost[$method_group] = array();
									$cost[$method_group]['cost'] = array();
								}
								
								$cost[$method_group]['shipping_name'] = !empty($rule['shipping_name']) ? $rule['shipping_name'] : $this->title;
								$cost[$method_group]['cost'][$item_id] = $shipping_cost;																								
							}
						}			
					}
				}							
			}
		}		
		return 	$cost;
	}	

	function wf_get_rule_cost( $rate,$grouped_package) {
		$based_on = 'weight';
		if(!empty($rate['cost_based_on'])) $based_on = $rate['cost_based_on'];
		
		if($based_on == 'price'){
			$totalweight = $this->wf_get_price($grouped_package);
		}
		elseif($based_on == 'item'){
			$totalweight = $this->wf_get_item_count($grouped_package);
		}
		else{
			$totalweight = $this->wf_get_weight($grouped_package);		
		}
		
		
		$weight = $totalweight;
		
		if ($rate['min_'.$based_on]) 
			$weight = max(0, $weight - $rate['min_'.$based_on]);

		$weightStep = $rate['weigh_rounding'];

		if (trim($weightStep)) 
			$weight = ceil($weight / $weightStep) * $weightStep;

		$rate_fee = str_replace($this->decimal_separator, '.', $rate['fee']);
		$rate_cost = str_replace($this->decimal_separator, '.', $rate['cost']);
		$price = $rate_fee + $weight * $rate_cost;
		
		if ( $price !== false) return $price;
		
		return false;		
	}	
	
	function wf_check_all_item_exists($costs,$package_content){
		return count(array_intersect_key($costs,$package_content)) == count($package_content);
	}
	
	function wf_add_rate($costs,$package) {
		if ( sizeof($costs) > 0) {
			$grouped_package = $this->calc_mode_strategy->wf_get_grouped_package($package);
			foreach ($costs as $method_group => $method_cost) {
				if($this->wf_check_all_item_exists($method_cost['cost'],$grouped_package)){
					if(isset($method_cost['shipping_name']) && isset($method_cost['cost'])){
		
						$method_id = sanitize_title( $method_group . $method_cost['shipping_name'] );
						$method_id = preg_replace( '/[^A-Za-z0-9\-]/', '', $method_id ); //Omit unsupported charectors
						$this->add_rate( array(
										'id'        => $this->id . ':' . $method_id,
										'label'     => $method_cost['shipping_name'],
										'cost'      => $method_cost['cost'],
										'taxes'     => '',
										'calc_tax'  => $this->calc_mode_strategy->wf_calc_tax()));
					}
				}								
			}
		}				  
	}
	
	//function to add states to the woocommerce states
	public function wf_custom_woocommerce_states( $states ) {
	  $states['IE'] = array(
		'CARLOW' => 'Carlow',
		'CAVAN' => 'Cavan',
		'CLARE' => 'Clare',
		'CORK' => 'Cork',
		'DONEGAL' => 'Donegal',
		'DUBLIN' => 'Dublin',
		'GALWAY' => 'Galway',
		'KERRY' => 'Kerry',
		'KILDARE' => 'Kildare',
		'KILKENNY' => 'Kilkenny',
		'LAOIS' => 'Laois',
		'LEITRIM' => 'Leitrim',
		'LIMERICK' => 'Limerick',
		'LONGFORD' => 'Longford',
		'LOUTH' => 'Louth',
		'MAYO' => 'Mayo',
		'MONAGHAM' => 'Monaghan',
		'OFFALY' => 'Offaly',
		'ROSCOMMON' => 'Roscommon',
		'SLIGO' => 'Sligo',
		'TRIPPERARY' => 'Tipperary',
		'WTERFORD' => 'Waterford',
		'WESTMEATH' => 'Westmeath',
		'WEXFORD' => 'Wexford',
		'WVICKLOW' => 'Wicklow'
	  );
	  return $states;
	}
} 
?>