<?php
/**
 * Product Quick Edit
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

if ( ! class_exists('WCAN_Product_Quick_Edit')) :

/**
 * WCAN_Product_Quick_Edit
 * 
 * @since 1.1.4
 */
class WCAN_Product_Quick_Edit {
	
	/**
	 * Constructor
	 * 
	 * @since 1.1.4
	 */
	public function __construct() {
		add_action('manage_product_posts_custom_column', array($this, 'output_values'));
		add_action('woocommerce_product_quick_edit_end', array($this, 'output_fields'));
		add_action('woocommerce_product_quick_edit_save', array($this, 'save_fields'));
	}
	
	/**
	 * Output field values
	 * 
	 * @since 1.1.4
	 * 
	 * @param string $column
	 * @return void
	 */
	public function output_values($column) {
		global $post, $the_product;
		
		$product_id = $post->ID;
		
		if ($column == 'name') { ?>
			
			<div class="hidden" id="wcan_inline_<?php echo $post->ID; ?>">
				<div class="_availability_instock_notification"><?php echo get_post_meta($product_id, '_availability_instock_notification', true); ?></div>
				<div class="_availability_lowstock_notification"><?php echo get_post_meta($product_id, '_availability_lowstock_notification', true); ?></div>
				<div class="_availability_outofstock_notification"><?php echo get_post_meta($product_id, '_availability_outofstock_notification', true); ?></div>
				<div class="_availability_backorder_notification"><?php echo get_post_meta($product_id, '_availability_backorder_notification', true); ?></div>
			</div>
			
			<?php
		}
	}
	
	/**
	 * Output fields
	 * 
	 * @since 1.1.4
	 */
	public function output_fields() { ?>
		
		<div class="inline-quick-edit availability-notifications-fields">
			<label class="availability-instock-notification">
				<span class="title"><?php _e( 'In Stock Notification', 'wc_availability_notifications' ); ?></span>
				<textarea class="text" name="_availability_instock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-lowstock-notification">
				<span class="title"><?php _e( 'Low Stock Notification', 'wc_availability_notifications' ); ?></span>
				<textarea class="text" name="_availability_lowstock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-outofstock-notification">
				<span class="title"><?php _e( 'Out Of Stock Notification', 'wc_availability_notifications' ); ?></span>
				<textarea class="text" name="_availability_outofstock_notification" rows="2" cols="20"></textarea>
			</label>
			
			<label class="availability-backorder-notification">
				<span class="title"><?php _e( 'Backorder Notification', 'wc_availability_notifications' ); ?></span>
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
			
			if (isset($_REQUEST['_availability_instock_notification'])) {
				update_post_meta($product_id, '_availability_instock_notification', $_REQUEST['_availability_instock_notification']);
			}
			
			if (isset($_REQUEST['_availability_lowstock_notification'])) {
				update_post_meta($product_id, '_availability_lowstock_notification', $_REQUEST['_availability_lowstock_notification']);
			}
			
			if (isset($_REQUEST['_availability_outofstock_notification'])) {
				update_post_meta($product_id, '_availability_outofstock_notification', $_REQUEST['_availability_outofstock_notification']);
			}
			
			if (isset($_REQUEST['_availability_backorder_notification'])) {
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
new WCAN_Product_Quick_Edit();

endif;