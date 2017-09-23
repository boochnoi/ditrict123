<?php
/**
 * Product Bulk Edit
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

if ( ! class_exists('WCAN_Product_Bulk_Edit')) :

/**
 * WCAN_Product_Bulk_Edit
 * 
 * @since 1.1.4
 */
class WCAN_Product_Bulk_Edit {
	
	/**
	 * Constructor
	 * 
	 * @since 1.1.4
	 */
	public function __construct() {
		add_action('woocommerce_product_bulk_edit_end', array($this, 'output_fields'));
		add_action('woocommerce_product_bulk_edit_save', array($this, 'save_fields'));
	}
	
	/**
	 * Output fields
	 * 
	 * @since 1.1.4
	 */
	public function output_fields() { ?>
		
		<div class="inline-edit-group availability-notifications">
			<label class="availability-instock-notification">
				<span class="title"><?php _e( 'In Stock Notification', 'wc_availability_notifications' ); ?></span>
				<input type="checkbox" name="change_availability_instock_notification" value="1" />
				<textarea class="text" name="_availability_instock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-lowstock-notification">
				<span class="title"><?php _e( 'Low Stock Notification', 'wc_availability_notifications' ); ?></span>
				<input type="checkbox" name="change_availability_lowstock_notification" value="1" />
				<textarea class="text" name="_availability_lowstock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-outofstock-notification">
				<span class="title"><?php _e( 'Out Of Stock Notification', 'wc_availability_notifications' ); ?></span>
				<input type="checkbox" name="change_availability_outofstock_notification" value="1" />
				<textarea class="text" name="_availability_outofstock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-backorder-notification">
				<span class="title"><?php _e( 'Backorder Notification', 'wc_availability_notifications' ); ?></span>
				<input type="checkbox" name="change_availability_backorder_notification" value="1" />
				<textarea class="text" name="_availability_backorder_notification" rows="2" cols="20"></textarea>
			</label>
		</div>
		
		<?php
	}
	
	/**
	 * Save field values
	 * 
	 * @since 1.1.4
	 * 
	 * @param object $product
	 * @return void
	 */
	public function save_fields($product) {
		$product_id = $product->id;
		
		if ($product_id > 0) {
			
			if ( ! empty($_REQUEST['change_availability_instock_notification'])) {
				update_post_meta($product_id, '_availability_instock_notification', $_REQUEST['_availability_instock_notification']);
			}
			
			if ( ! empty($_REQUEST['change_availability_lowstock_notification'])) {
				update_post_meta($product_id, '_availability_lowstock_notification', $_REQUEST['_availability_lowstock_notification']);
			}
			
			if ( ! empty($_REQUEST['change_availability_outofstock_notification'])) {
				update_post_meta($product_id, '_availability_outofstock_notification', $_REQUEST['_availability_outofstock_notification']);
			}
			
			if ( ! empty($_REQUEST['change_availability_backorder_notification'])) {
				update_post_meta($product_id, '_availability_backorder_notification', $_REQUEST['_availability_backorder_notification']);
			}
		}
	}
}

/**
 * Initialize
 * 
 * @since 1.1.4
 */
new WCAN_Product_Bulk_Edit();

endif;