<?php
/**
 * Meta box
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/admin/classes
 * @since 		1.0.0
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WC_Availability_Notifications_Meta_Box' ) ) :

/**
 * WC_Availability_Notifications_Meta_Box
 * 
 * @since 1.0.0
 */
class WC_Availability_Notifications_Meta_Box {
	
	/**
	 * Constructor
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_product_options_stock_status', array( $this, 'fields' 	) );
		add_action( 'woocommerce_process_product_meta', 		array( $this, 'save' 	) );
		
		add_action( 'woocommerce_product_after_variable_attributes', 	array( $this, 'variable_fields' ), 10, 3 );
		add_action( 'woocommerce_process_product_meta_variable', 		array( $this, 'variable_save' 	), 10, 1 );
		add_action( 'woocommerce_ajax_save_product_variations', 		array( $this, 'variable_save' 	), 10, 1 );
	}
	
	/**
	 * Output product meta fields
	 * 
	 * @since 1.0.0
	 */
	public function fields() {
		// In stock notification
		woocommerce_wp_textarea_input( 
			array( 
				'id' 			=> '_availability_instock_notification', 
				'label' 		=> __( 'In Stock Notification', 'wc_availability_notifications' ), 
				'desc_tip' 		=> true, 
				'description' 	=> __( 'Show an "in stock" notification on the product.', 'wc_availability_notifications' ), 
				'type' 			=> 'text', 
			) 
		);
		
		// Low stock notification
		woocommerce_wp_textarea_input( 
			array( 
				'id' 			=> '_availability_lowstock_notification', 
				'label' 		=> __( 'Low Stock Notification', 'wc_availability_notifications' ), 
				'desc_tip' 		=> true, 
				'description' 	=> __( 'Show a "low stock" notification on the product.', 'wc_availability_notifications' ), 
				'type' 			=> 'text', 
			) 
		);
		
		// Out of stock notification
		woocommerce_wp_textarea_input( 
			array( 
				'id' 			=> '_availability_outofstock_notification', 
				'label' 		=> __( 'Out Of Stock Notification', 'wc_availability_notifications' ), 
				'desc_tip' 		=> true, 
				'description' 	=> __( 'Show an "out of stock" notification on the product.', 'wc_availability_notifications' ), 
				'type' 			=> 'text', 
			) 
		);
		
		// Backorder notification
		woocommerce_wp_textarea_input( 
			array( 
				'id' 			=> '_availability_backorder_notification', 
				'label' 		=> __( 'Backorder Notification', 'wc_availability_notifications' ), 
				'desc_tip' 		=> true, 
				'description' 	=> __( 'Show a "backorder" notification on the product.', 'wc_availability_notifications' ), 
				'type' 			=> 'text', 
			) 
		);
		
		// Sale notification
		woocommerce_wp_textarea_input( 
			array( 
				'id' 			=> '_availability_sale_notification', 
				'label' 		=> __( 'Sale Notification', 'wc_availability_notifications' ), 
				'desc_tip' 		=> true, 
				'description' 	=> __( 'Show a "sale" notification on the product.', 'wc_availability_notifications' ), 
				'type' 			=> 'text', 
			) 
		);
	}
	
	/**
	 * Save product meta fields
	 * 
	 * @since 1.0.0
	 * 
	 * @param int $post_id
	 */
	public function save( $post_id ) {
		
		$fields = array(
			'_availability_instock_notification',
			'_availability_lowstock_notification',
			'_availability_outofstock_notification',
			'_availability_backorder_notification',
			'_availability_sale_notification'
		);

		foreach ($fields as $field) {
			update_post_meta($post_id, $field, apply_filters("woocommerce_{$field}", $_POST[$field], $post_id));
		}
	}
	
	/**
	 * Output product variation fields
	 * 
	 * @since 1.0.0
	 * 
	 * @param 	int 	$loop
	 * @param 	array 	$variation_data
	 * @param 	WP_Post $variation
	 * @return 	void 
	 */
	public function variable_fields( $loop, $variation_data, $variation ) {
		$variation_data['_availability_instock_notification'] 		= get_post_meta($variation->ID, '_availability_instock_notification', true); 
		$variation_data['_availability_lowstock_notification'] 		= get_post_meta($variation->ID, '_availability_lowstock_notification', true); 
		$variation_data['_availability_outofstock_notification'] 	= get_post_meta($variation->ID, '_availability_outofstock_notification', true); 
		$variation_data['_availability_backorder_notification'] 	= get_post_meta($variation->ID, '_availability_backorder_notification', true); ?>
		
		<tr>
			<td colspan="2">
				<?php 
					// In stock notification
					woocommerce_wp_textarea_input( 
						array( 
							'id' 			=> "variable_availability_instock_notification[{$loop}]", 
							'label' 		=> __( 'In Stock Notification', 'wc_availability_notifications' ), 
							'desc_tip' 		=> true, 
							'description' 	=> __( 'Show an "in stock" notification on the variable product.', 'wc_availability_notifications' ), 
							'type' 			=> 'text', 
							'value' 		=> $variation_data['_availability_instock_notification']
						) 
					);
				?>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<?php 
					// Low stock notification
					woocommerce_wp_textarea_input( 
						array( 
							'id' 			=> "variable_availability_lowstock_notification[{$loop}]", 
							'label' 		=> __( 'Low Stock Notification', 'wc_availability_notifications' ), 
							'desc_tip' 		=> true, 
							'description' 	=> __( 'Show a "low stock" notification on the variable product.', 'wc_availability_notifications' ), 
							'type' 			=> 'text', 
							'value' 		=> $variation_data['_availability_lowstock_notification']
						) 
					);
				?>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<?php 
					// Out of stock notification
					woocommerce_wp_textarea_input( 
						array( 
							'id' 			=> "variable_availability_outofstock_notification[{$loop}]", 
							'label' 		=> __( 'Out Of Stock Notification', 'wc_availability_notifications' ), 
							'desc_tip' 		=> true, 
							'description' 	=> __( 'Show an "out of stock" notification on the variable product.', 'wc_availability_notifications' ), 
							'type' 			=> 'text', 
							'value' 		=> $variation_data['_availability_outofstock_notification']
						) 
					);
				?>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<?php 
					// Backorder notification
					woocommerce_wp_textarea_input( 
						array( 
							'id' 			=> "variable_availability_backorder_notification[{$loop}]", 
							'label' 		=> __( 'Backorder Notification', 'wc_availability_notifications' ), 
							'desc_tip' 		=> true, 
							'description' 	=> __( 'Show a "backorder" notification on the variable product.', 'wc_availability_notifications' ), 
							'type' 			=> 'text', 
							'value' 		=> $variation_data['_availability_backorder_notification']
						) 
					);
				?>
			</td>
		</tr>
		
		<?php
	}
	
	/**
	 * Save product variation fields
	 * 
	 * @since 1.0.0
	 * 
	 * @param int $post_id
	 */
	public function variable_save( $post_id ) {
			
		if ( isset( $_POST['variable_sku'] ) ) {
			$variable_sku          = $_POST['variable_sku'];
			$variable_post_id      = $_POST['variable_post_id'];
			
			// In stock notification
			$stock_instock_notification = $_POST['variable_availability_instock_notification'];
			
			// Low stock notification
			$stock_lowstock_notification = $_POST['variable_availability_lowstock_notification'];
			
			// Out of stock notification
			$stock_outofstock_notification = $_POST['variable_availability_outofstock_notification'];
			
			// Backorder notification
			$stock_backorder_notification = $_POST['variable_availability_backorder_notification'];
			
			$max = max(array_keys($variable_post_id));
			
			for ($i = 0; $i <= $max; $i++) {
				
				if ( ! isset($variable_post_id[$i])) {
					continue;
				}
				
				$variation_id = absint($variable_post_id[$i]);
				
				if (isset($stock_instock_notification[$i])) {
					update_post_meta($variation_id, '_availability_instock_notification', $stock_instock_notification[$i]);
				}
				
				if (isset($stock_lowstock_notification[$i])) {
					update_post_meta($variation_id, '_availability_lowstock_notification', $stock_lowstock_notification[$i]);
				}

				if (isset($stock_outofstock_notification[$i])) {
					update_post_meta($variation_id, '_availability_outofstock_notification', $stock_outofstock_notification[$i]);
				}
				
				if (isset($stock_backorder_notification[$i])) {
					update_post_meta($variation_id, '_availability_backorder_notification', $stock_backorder_notification[$i]);
				}
			}
		}
	}
}

/**
 * Initialize
 * 
 * @since 1.0.0
 */
new WC_Availability_Notifications_Meta_Box();

endif;