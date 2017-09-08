<?php
/*
	Plugin Name: WooCommerce Shipping Pro with Table Rate (BASIC)
	Plugin URI: https://www.xadapter.com/product/woocommerce-table-rate-shipping-pro-plugin/
	Description: User friendly Weight and Country based WooCommerce Shipping plug-in. Upgrade to Shipping Pro No.1 WooCommerce Shipping Plugin. Configure your shipping with the help of our experts!. 30 Day no question asked refund. 
	Version: 1.2.9
	Author: XAdapter
	Author URI: www.xadapter.com/
	Copyright: 2014-2015 WooForce.
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function wf_shipping_pro_basic_activatoin_check(){
    
	//check if basic version is there
	if ( is_plugin_active('woocommerce-shipping-pro/woocommerce-shipping-pro.php') ){
        deactivate_plugins( basename( __FILE__ ) );
		wp_die( __("Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete EasyPost(Basic) Woocommerce Extension and then try again", "wf-easypost" ), "", array('back_link' => 1 ));
	}
}
register_activation_hook( __FILE__, 'wf_shipping_pro_basic_activatoin_check' );

if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {	
	function wf_country_weight_shipping_init() {
		if ( ! class_exists( 'wf_country_weight_shipping_method' ) ) {
			class wf_country_weight_shipping_method extends WC_Shipping_Method {
  				function __construct() {
					$this->id           = 'wf_country_weight_woocommerce_shipping'; 
					
					$this->method_title     = __( 'Shipping Pro (BASIC)', 'wf_country_weight_shipping' );
					$this->method_description = __( 'Define shipping by country and weight', 'wf_country_weight_shipping' );

					$this->wf_country_weight_init_form_fields();
					$this->init_settings();

					$this->title = $this->settings['title'];
					$this->enabled = $this->settings['enabled'];
					//get_option fill default if doesn't exist. other settings also can change to this
					$this->debug = $this->get_option('debug');				
					$this->tax_status       = $this->settings['tax_status'];
					$this->rate_matrix       = $this->settings['rate_matrix'];
					//get_option fill default if doesn't exist. other settings also can change to this
					$this->displayed_columns       = $this->get_option('displayed_columns');
					
					$this->shipping_countries = WC()->countries->get_shipping_countries();
					
					// Save settings in admin
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
				
				function wf_debug($error_message){
					if($this->debug == 'yes')
						wc_add_notice( $error_message, 'notice' );
				}
				
				function wf_country_weight_init_form_fields() {
					
					$this->form_fields = array(
						'enabled'    => array(
							'title'   => __( 'Enable/Disable', 'wf_country_weight_shipping' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable this shipping method', 'wf_country_weight_shipping' ),
							'default' => 'no',
						),						
						'title'      => array(
							'title'       => __( 'Method Title', 'wf_country_weight_shipping' ),
							'type'        => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'wf_country_weight_shipping' ),
							'default'     => __( 'Weight Based Fee', 'wf_country_weight_shipping' ),
						),							
						'rate_matrix' => array(
							'type' 			=> 'rate_matrix'
						),
						'displayed_columns' => array(
							'title'       => __( 'Display/Hide matrix columns', 'wf_country_weight_shipping' ),
							'type'        => 'multiselect',
							'description' => __( 'Select the columns which are used in the matrix. Please Save changes to reflect the modifications.', 'wf_country_weight_shipping' ),
							'class'       => 'wc-enhanced-select enabled',
							'css'         => 'width: 450px;',
							'default'     => array(
								'shipping_name',
								'country_list'   ,
								'min_weight'  ,
								'max_weight'  ,
								'fee'   ,
								'cost' ,
								'weigh_rounding'   
							),
							'options'     => array(
								'shipping_name' => __( 'Method title', 'wf_country_weight_shipping' ),
								'country_list'    => __( 'Country list', 'wf_country_weight_shipping' ),
								'min_weight'    => __( 'Min weight', 'wf_country_weight_shipping' ),
								'max_weight'    => __( 'Max weight', 'wf_country_weight_shipping' ),
								'fee'    => __( 'Base cost', 'wf_country_weight_shipping' ),
								'cost'    => __( 'Cost/weight', 'wf_country_weight_shipping' ),
								'weigh_rounding'    => __( 'Weight round', 'wf_country_weight_shipping' )
							),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Choose matrix columns', 'woocommerce' )
							)
						),
						'tax_status' => array(
							'title'       => __( 'Tax Status', 'wf_country_weight_shipping' ),
							'type'        => 'select',
							'description' => '',
							'default'     => 'taxable',
							'options'     => array(
								'taxable' => __( 'Taxable', 'wf_country_weight_shipping' ),
								'none'    => __( 'None', 'wf_country_weight_shipping' ),
							),
						),
						'debug'    => array(
							'title'   => __( 'Debug', 'wf_country_weight_shipping' ),
							'type'    => 'checkbox',
							'label'   => __( 'Debug this shipping method', 'wf_country_weight_shipping' ),
							'default' => 'no',
						),						
					);					        
				}

				function wf_hidden_matrix_column($column_name){
					return in_array($column_name,$this->displayed_columns) ? '' : 'hidecolumn';	
				}
				
				function wf_rule_to_text($key ,$box){
					$weight_unit 	= strtolower( get_option('woocommerce_weight_unit') );
					$currency_symbol = get_woocommerce_currency_symbol();
					$text = "";
					if(!empty($box['min_weight']))  $text .= " if the order weight is more than ".$box['min_weight']."$weight_unit";	
					if(!empty($box['max_weight'])) $text .= (empty($text) ? "if the order weight is" : " and") . " less than or equal to ".$box['max_weight']."$weight_unit";
					if(!empty($box['fee'])) $text .= (!empty($text) ?  " then" : "") . " shipping cost is $currency_symbol".$box['fee'];					
					if(!empty($box['cost'])) 
					{						
						$text .= (!empty($box['fee']) ?  " +" : " shipping cost is") . " per $weight_unit  $currency_symbol".$box['cost'];
						$text .= empty($box['min_weight']) ? "." : " for the remaining weight above ".$box['min_weight']."$weight_unit.";					
					}
					if(!empty($box['weigh_rounding']))  $text .= "(Order weight is rounded up to the nearest ".$box['weigh_rounding']."$weight_unit).";					
					return $text;
				}
							
				public function validate_rate_matrix_field( $key ) {
					$rate_matrix         = isset( $_POST['rate_matrix'] ) ? $_POST['rate_matrix'] : array();
					return $rate_matrix;
				}

				public function generate_rate_matrix_html() {				
					ob_start();					
					?>
					<tr valign="top">
						<td class="titledesc" colspan="2" style="padding-left:0px">
							<strong><?php _e( 'Rate matrix:', 'wf_country_weight_shipping' ); ?></strong><br><br>
							<style type="text/css">
								.canada_post_boxes td, .canada_post_services td {
									vertical-align: middle;
										padding: 4px 7px;
								}
								.canada_post_boxes th, .canada_post_services th {
									padding: 9px 7px;
								}
								.canada_post_boxes td input {
									margin-right: 4px;
								}
								.canada_post_boxes .check-column {
									vertical-align: middle;
									text-align: left;
									padding: 0 7px;
								}
								.canada_post_services th.sort {
									width: 16px;
								}
								.canada_post_services td.sort {
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
									th.medium_column
									{
									   min-width:90px;	 
									}
									th.big_column
									{
										min-width:300px;
									}									
								}
								th.hidecolumn,
								td.hidecolumn
								{
										display:none;
								}	
							</style>
							
							<table class="canada_post_boxes widefat">
								<thead>
									<tr>
										<th class="check-column tiny_column"><input type="checkbox" /></th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('shipping_name');?>">
										<?php _e( 'Method title', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Would you like this shipping rule to have its own shipping service name? If so, please choose a name. Leaving it blank will use Method Title as shipping service name.', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
										</th>
										<th class="big_column <?php echo $this->wf_hidden_matrix_column('country_list');?>">
										<?php _e( 'Country list', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of countries which this rule will be applicable', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
										</th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('min_weight');?>">
										<?php _e( 'Min', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the value entered is .25 and the order weight is .25 then this rule will be ignored. if the value entered is .25 and the order weight is .26 then this rule will be be applicable for calculating shipping cost.', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>(weight) 
										</th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('max_weight');?>">
										<?php _e( 'Max', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the value entered is .25 and the order weight is .26 then this rule will be ignored. if the value entered is .25 and the order weight is .25 or .24 then this rule will be be applicable for calculating shipping cost.', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>(weight)
										</th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('fee');?>">
										<?php _e( 'Base cost', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Base/Fixed cost of the shipping irrespective of the weight', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
										</th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('cost');?>">
										<?php _e( 'Cost', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Per weight unit cost. This cost will be added on above the base cost. Total shipping Cost = Base cost + (order weight - minimum weight) * cost per unit', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>(per weight)
										</th>
										<th class="medium_column <?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>">
										<?php _e( 'Rounding', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'How would you like to round weight? if the value entered is 0.5 and the order weight is 4.4kg then shipping cost will be calculated for 4.5kg, if the value entered is 1 and the order weight is 4.4kg then shipping cost will be calculated for 5kg, if the value entered is 0 and the order weight is 4.4kg then shipping cost will be calculated for 4.4kg', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>(weight)
										</th>
										<th class="small_column">
										<?php _e( 'Note', 'wf_country_weight_shipping' );  ?>
										<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Summary of the shipping rule defined for better understanding. Save changes to reflect modifications', 'wf_country_weight_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
										</th>																			
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th colspan="4">
											<a href="#" class="button plus insert"><?php _e( 'Add rule', 'wf_country_weight_shipping' ); ?></a>
											<a href="#" class="button minus remove"><?php _e( 'Remove rule(es)', 'wf_country_weight_shipping' ); ?></a>
											<a href="#" class="button duplicate"><?php _e( 'Duplicate rule(es)', 'wf_country_weight_shipping' ); ?></a>
										</th>
										<th colspan="5">
											<small class="description"><?php _e( 'Measurements Weight Unit & Dimensions Unit as per woocommerce settings.', 'wf_country_weight_shipping' ); ?></small>
										</th>
									</tr>
								</tfoot>
								<tbody id="rates">
								<?php								
								$matrix_rowcount = 0;
								if ( $this->rate_matrix ) {
									foreach ( $this->rate_matrix as $key => $box ) {												
										$defined_countries = isset( $box['country_list'] ) ? $box['country_list'] : array();?>
										<tr><td class="check-column"><input type="checkbox" /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type='text' size='10' name='rate_matrix[<?php echo $key;?>][shipping_name]' placeholder='<?php echo $this->title;?>' value='<?php echo isset($box['shipping_name']) ? $box['shipping_name']:"";?>' /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('country_list');?>" style='overflow:visible'>
										<select id="country_list_combo" class="multiselect wc-enhanced-select enabled" multiple="true" style="width:100%;" name='rate_matrix[<?php echo $key;?>][country_list][]'>
											<option value="any_country" <?php selected(in_array('any_country',$defined_countries),true);?>>Any Country</option>
											<option value="rest_world" <?php selected(in_array('rest_world',$defined_countries),true);?>>Rest of the world</option>
											<?php foreach($this->shipping_countries as $countryKey => $countryValue){ ?>
											<option value="<?php echo $countryKey;?>" <?php selected(in_array($countryKey,$defined_countries),true);?>><?php echo $countryValue;?></option>
											<?php } ?>															
										</select>
										</td>										
										<td class="<?php echo $this->wf_hidden_matrix_column('min_weight');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][min_weight]' 		value='<?php echo $box['min_weight']; ?>' /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('max_weight');?>"><input type='text' size='3' name='rate_matrix[<?php echo $key;?>][max_weight]' 		value='<?php echo $box['max_weight']; ?>' /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>"><input type='text' size='5' name='rate_matrix[<?php echo $key;?>][fee]'				value='<?php echo $box['fee']; ?>' /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('cost');?>"><input type='text' size='5' name='rate_matrix[<?php echo $key;?>][cost]'			value='<?php echo $box['cost']; ?>' /></td>
										<td class="<?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>"><input type='text' size='1' name='rate_matrix[<?php echo $key;?>][weigh_rounding]' 	value='<?php echo $box['weigh_rounding']; ?>' /></td>																						
										<td><img class="help_tip" style="float:none;" data-tip="<?php echo $this->wf_rule_to_text($key ,$box);?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /></td>
										</tr>
										<?php
										if(!empty($key) && $key >= $matrix_rowcount)
											$matrix_rowcount = $key;
									}
								}
								?>
								<input type="hidden" id="matrix_rowcount" value="<?php echo $matrix_rowcount;?>" />
								</tbody>
							</table>
							<script type="text/javascript">																	
								jQuery(window).load(function(){									
									jQuery('.canada_post_boxes .insert').click( function() {
										var $tbody = jQuery('.canada_post_boxes').find('tbody');
										var size = $tbody.find('#matrix_rowcount').val();
										if(size){
											size = parseInt(size)+1;
										}
										else
											size = 0;
										
										var code = '<tr class="new"><td class="check-column"><input type="checkbox" /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type="text" size="10" name="rate_matrix['+size+'][shipping_name]" placeholder="<?php echo $this->title;?>" /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('country_list');?>" style="overflow:visible">\
										<select id="country_list_combo" class="multiselect wc-enhanced-select enabled" multiple="true" style="width:100%;" name="rate_matrix['+size+'][country_list][]">\
										<option value="any_country">Any Country</option><option value="rest_world">Rest of the world</option>\
										<?php foreach($this->shipping_countries as $countryKey => $countryValue){ ?><option value="<?php echo esc_attr( $countryKey ); ?>" ><?php echo esc_attr( $countryValue ); ?></option>\
										<?php } ?></select>\
										</td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('min_weight');?>"><input type="text" size="3" name="rate_matrix['+size+'][min_weight]"  /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('max_weight');?>"><input type="text" size="3" name="rate_matrix['+size+'][max_weight]" /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>"><input type="text" size="5" name="rate_matrix['+size+'][fee]" /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('cost');?>"><input type="text" size="5" name="rate_matrix['+size+'][cost]" /></td>\
										<td class="<?php echo $this->wf_hidden_matrix_column('weigh_rounding');?>"><input type="text" size="1" name="rate_matrix['+size+'][weigh_rounding]" /></td>\
										<td></td></tr>';										
										$tbody.append( code );
										jQuery("select.wc-enhanced-select").trigger( 'wc-enhanced-select-init' );
										$tbody.find('#matrix_rowcount').val(size);
										return false;
									} );

									jQuery('.canada_post_boxes .remove').click(function() {
										var $tbody = jQuery('.canada_post_boxes').find('tbody');

										$tbody.find('.check-column input:checked').each(function() {
											jQuery(this).closest('tr').remove();
										});

										return false;
									});
									
									jQuery('.canada_post_boxes .duplicate').click(function() {
										var $tbody = jQuery('.canada_post_boxes').find('tbody');

										var new_trs = [];
										
										$tbody.find('.check-column input:checked').each(function() {
											var $tr    = jQuery(this).closest('tr');
											var $clone = $tr.clone();
											var size = jQuery('#matrix_rowcount').val();
											if(size)
												size = parseInt(size)+1;
											else
												size = 0;
											var div = $clone.find('div.multiselect');
											var selecteddata = $tr.find('select').select2('data');
											if ( selecteddata ) {
												var arr = [];
												jQuery.each( selecteddata, function( id, text ) {
													arr.push(text.id);												
												});
												$clone.find('select').val(arr);
											}
											div.remove();
											$clone.find('.multiselect').show();
											$clone.find('.multiselect').removeClass("enhanced");
											// find all the inputs within your new clone and for each one of those
											$clone.find('input[type=text], select').each(function() {
												var currentNameAttr = jQuery(this).attr('name'); 
												if(currentNameAttr){
													var newNameAttr = currentNameAttr.replace(/\d+/, size);
													jQuery(this).attr('name', newNameAttr);   // set the incremented name attribute 
												}
											});
											//$tr.after($clone);
											new_trs.push($clone);
											jQuery('#matrix_rowcount').val(size);
										});
										if(new_trs)
										{
											var lst_tr    = $tbody.find('.check-column :input:checkbox:checked:last').closest('tr');
											jQuery.each( new_trs.reverse(), function( id, text ) {
													//adcd.after(text);
													lst_tr.after(text);												
												});
										}
										$tbody.find('.check-column input:checked').removeAttr('checked');
										jQuery("select.wc-enhanced-select").trigger( 'wc-enhanced-select-init' );
										return false;
									});									
								});
							</script>
						</td>
					</tr>
					<?php
					return ob_get_clean();
				}
				
				function calculate_shipping( $package = array() ) {
					$woocommerce = function_exists('WC') ? WC() : $GLOBALS['woocommerce'];
					$weight     = $woocommerce->cart->cart_contents_weight;
					$this->wf_debug("Cart weight:".$weight.",Destination country:".$package['destination']['country']);
					$rules = $this->wf_get_rules_by_country( $package['destination']['country'] );
					
					$this->wf_add_calc_cost($rules, $weight);					  
				}
				
				function wf_get_rules_by_country( $country ) {
					$country_rules = array();
					if ( sizeof( $this->rate_matrix ) > 0) {
						foreach ( $this->rate_matrix as $key => $rule ) {
							if($this->wf_is_country_exist($rule["country_list"],$country)){
								if( empty( $rule['shipping_name'] ) ){
									$rule['shipping_name'] = $this->title;
								}
								$country_rules[] = $rule;	
							}
						}					
					}  
					return $country_rules ;
				}
				
				function wf_is_country_exist($country_list,$country){
					//if $country_list is null then shipping rule will be acceptable for any country 
					if (empty($country_list)) return true;
					if (count($country_list) == 1){
						if($country_list[0] == 'rest_world')	
							return $this->wf_partof_rest_of_the_world($country);
						elseif($country_list[0] == 'any_country')
							return true;	
					}
					return in_array($country,$country_list);
				}
				
				function wf_partof_rest_of_the_world( $country ) {
					$defined_countries = array();
					if ( sizeof( $this->rate_matrix ) > 0) {
						foreach ( $this->rate_matrix as $key => $rule ) {
							if(!empty($rule["country_list"]))
								$defined_countries = array_merge($rule["country_list"],$defined_countries);
							
						}					
					}
					//county not defined as part of any other rule and available in shipping countries 
					if(!in_array($country,$defined_countries) && array_key_exists($country,$this->shipping_countries))
						return true;
					return false;				
				}

				function wf_add_calc_cost( $rates, $totalweight) {
					if ( sizeof($rates) > 0) {
						foreach ( $rates as $key => $rate) {
							$weight = $totalweight;
							if ($rate['min_weight'] && $weight <= $rate['min_weight']) 
								continue;
							if ($rate['max_weight'] && $weight > $rate['max_weight']) 
								continue;

							if ($rate['min_weight']) 
								$weight = max(0, $weight - $rate['min_weight']);

							$weightStep = $rate['weigh_rounding'];

							if (trim($weightStep)) 
								$weight = ceil($weight / $weightStep) * $weightStep;

							$price = (float)$rate['fee'] + $weight * (float)$rate['cost'];
							
							if ( $price !== false) {
								$taxable = ($this->tax_status == 'taxable') ? true : false;
								$shipping_label = isset($rate['shipping_name']) ? $rate['shipping_name'] : $this->title;
								$this->add_rate( array(
													'id'        => $this->id . ':' . sanitize_title( $shipping_label ),
													'label'     => $shipping_label,
													'cost'      => $price,
													'taxes'     => '',
													'calc_tax'  => 'per_order'));
							}
						}
				    }				  
				}
				
				public function admin_options() {
					// Check users environment supports this method
					?>
					<div class="wf-banner updated below-h2">
						<!--<img class="scale-with-grid" src="http://www.wooforce.com/wp-content/uploads/2015/07/WooForce-Logo-Admin-Banner-Basic.png" alt="Wordpress / WooCommerce Shipping Pro with Table Rates Plugin | WooForce">-->
						<p class="main">
						<ul>
							<li style='color:red;'><strong>Your Business is precious! Go Premium!</li></strong>
							<li><strong>Upgrade to Shipping Pro. No.1 WooCommerce Shipping Plugin. Configure your shipping with the help of our experts!<br/>30 Day no question asked refund.</strong></li>
							<li><strong>- Timely compatibility updates and bug fixes.</strong ></li>
							<li><strong>- Premium Support:</strong> Faster and time bound response for support requests.</li>
							<li><strong>- More Features:</strong></li>
						</ul>
						&nbsp;-&nbsp;Zone based shipping costs (By Country / State / Post Code).<br>
						&nbsp;-&nbsp;Product Category based shipping costs (By Product Category/Shipping Class).<br>
						&nbsp;-&nbsp;Unit based shipping costs (By Weight/ Quantity/ Price).<br>
						&nbsp;-&nbsp;Provide many shipping service options for buyer to choose(Ex: Regular Delivery, Next Day Delivery, etc).<br>
						&nbsp;-&nbsp;Many Calculation Options(Fixed Cost / Unit Based Cost / Percentage Based Cost / Step-or-Round based cost).<br>
						&nbsp;-&nbsp;Calculate each ‘item’ or ‘product category’ or ‘shipping class’  cost separately and sum to find the final shipping cost, OR Calculate entire order shipping cost together.<br>
						&nbsp;-&nbsp;Per Product Shipping using free AddOn.<br>
						&nbsp;-&nbsp;Advanced Bundle Rate Shipping using free AddOn.<br>
						&nbsp;-&nbsp;International shipping using Rest of the world & Rest of the country & Any Country & Any States short-codes.<br>
						&nbsp;-&nbsp;Use for Flat Rate Shipping and Free Shipping.<br>
						&nbsp;-&nbsp;Add Handling Fees.<br>
						&nbsp;-&nbsp;Shipping Rules Import & Export via CSV.<br>
						&nbsp;-&nbsp;Plain text translation of shipping calculations.<br>
						&nbsp;-&nbsp;Hide the column(s) which are not relevant for the business case.<br>
						&nbsp;-&nbsp;Multilingual support. English & German translation in built available.<br>
						&nbsp;-&nbsp;Excellent Support for setting it up!</p>
						<p><a href="https://www.xadapter.com/product/woocommerce-table-rate-shipping-pro-plugin/" target="_blank" class="button button-primary">Upgrade to Premium Version</a> <a href="http://shippingprowoodemo.wooforce.com/wp-admin/admin.php?page=wc-settings&tab=shipping&section=wf_woocommerce_shipping_pro_method" target="_blank" class="button">Live Demo</a></p>
					</div>
					<style>
					.wf-banner img {
						float: right;
						margin-left: 1em;
						padding: 15px 0
					}
					</style>
					<?php 
					// Show settings
					parent::admin_options();
				}				
			} 
		}		
	}
	add_action( 'woocommerce_shipping_init', 'wf_country_weight_shipping_init' );
	add_action( 'init', 'wf_load_plugin_textdomain');
	

	function wf_add_country_weight_shipping_init( $methods )	{
		$methods[] = 'wf_country_weight_shipping_method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'wf_add_country_weight_shipping_init' );

	function wf_weight_country_plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wf_country_weight_shipping_method' ) . '">' . __( 'Settings', 'wf_country_weight_shipping' ) . '</a>',
			'<a href="https://wordpress.org/support/plugin/weight-country-woocommerce-shipping" target="_blank">' . __( 'Support', 'wf_country_weight_shipping' ) . '</a>',
			'<a href="https://www.xadapter.com/product/woocommerce-table-rate-shipping-pro-plugin/"  target="_blank">' . __( 'Premium Upgrade', 'woocommerce-shipment-tracking' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wf_weight_country_plugin_action_links' );
	
	/*
	 * function to add language conversion
	 *
	 * @ since 1.2.1
	 * @ access Public
	 */
	function wf_load_plugin_textdomain() {
		load_plugin_textdomain( 'wf_country_weight_shipping', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}
}
