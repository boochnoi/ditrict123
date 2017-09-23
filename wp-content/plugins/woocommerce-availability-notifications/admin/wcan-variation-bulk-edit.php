<?php
/**
 * Variation Bulk Edit
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/admin
 * @since 		1.1.4
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('WCAN_Variation_Bulk_Edit')) :

/**
 * WCAN_Variation_Bulk_Edit
 * 
 * @since 1.1.4
 */
class WCAN_Variation_Bulk_Edit {
	
	/**
	 * Construactor
	 * 
	 * @since 1.1.4
	 */
	public function __construct() {
		add_action('woocommerce_variable_product_bulk_edit_actions', array($this, 'output_variation_actions'));
		add_action('woocommerce_bulk_edit_variations', array($this, 'save_variations'), 10, 4);
	}
	
	/**
	 * Ouput option variation actions
	 * 
	 * @since 1.1.4
	 */
	public function output_variation_actions() { ?>
		
		<optgroup label="<?php esc_attr_e('Availability Notifications', 'wc_availability_notifications'); ?>">
			<option value="variable_availability_instock_notification"><?php _e('In Stock Notification', 'wc_availability_notifications'); ?></option>
			<option value="variable_availability_lowstock_notification"><?php _e('Low Stock Notification', 'wc_availability_notifications'); ?></option>
			<option value="variable_availability_outofstock_notification"><?php _e('Out Of Stock Notification', 'wc_availability_notifications'); ?></option>
			<option value="variable_availability_backorder_notification"><?php _e('Backorder Notification', 'wc_availability_notifications'); ?></option>
		</optgroup>
		
		<?php
	}
	
	/**
	 * Save variation action data
	 * 
	 * @since 1.1.4
	 * 
	 * @param string $bulk_action
	 * @param array $data
	 * @param int $product_id
	 * @param array $variations
	 * @return void
	 */
	public function save_variations($bulk_action, $data, $product_id, $variations) {
		
		// Override passed data as its already sanitized
		$data = isset($_POST['data']) ? $_POST['data'] : array();
		
		// This signifies a cancel request from the user for bulk editing
		if ( ! isset($data['value']))
			return;
		
		$meta_key = '';
		$meta_value = isset($data['value']) ? $data['value'] : '';
		
		// Check the bulk action 
		switch ($bulk_action) {
			case 'variable_availability_instock_notification' :
				$meta_key = '_availability_instock_notification';
			break;
				
			case 'variable_availability_lowstock_notification' :
				$meta_key = '_availability_lowstock_notification';
			break;
				
			case 'variable_availability_outofstock_notification' :
				$meta_key = '_availability_outofstock_notification';
			break;
				
			case 'variable_availability_backorder_notification' :
				$meta_key = '_availability_backorder_notification';
			break;
		}
		
		// Check if meta key is present so we know that 1 of the 4 notification types is being bulk edited
		if ( ! empty($meta_key)) {
			if ($variations && is_array($variations)) {
				foreach ($variations as $variation_id) {
					update_post_meta($variation_id, $meta_key, $meta_value);
				}
			}
		}
	}
}

/**
 * Initialize
 * 
 * @since 1.1.4
 */
new WCAN_Variation_Bulk_Edit();

endif;