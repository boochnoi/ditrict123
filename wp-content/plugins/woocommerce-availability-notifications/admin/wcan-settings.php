<?php
/**
 * Settings
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/admin
 * @since 		1.0.0
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WC_Availability_Notifications_Settings' ) ) :
	
/**
 * WC_Availability_Notifications_Settings
 * 
 * @since 1.0.0
 */
class WC_Availability_Notifications_Settings {
	
	/**
	 * Constructor
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_inventory_settings', array( $this, 'settings' ), 0 );
	}
	
	/**
	 * Add settings in "Product > Inventory Options" in WooCommerce's option settings
	 * 
	 * @since 1.0.0
	 * 
	 * @param 	array 	$settings
	 * @return 	array 	$settings
	 */
	public function settings( $settings ) {
		$settings[] = array(
			'id' 		=> 'availability_notifications',
			'title' 	=> __( 'Availability Notifications', 'wc_availability_notifications' ), 
			'type' 		=> 'title', 
			'desc' 		=> '', 
		);
		
		// In Stock Settings
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_instock_notification',
			'title' 	=> __( 'In Stock Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show an "in stock" default notification on the product.', 'wc_availability_notifications' ),
			'type' 		=> 'textarea',
			'class' 	=> 'large-text',
			'css' 		=> 'height: 75px;'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_instock_notification_font_color',
			'title' 	=> __( 'In Stock Font Color', 'wc_availability_notifications' ),
			'type' 		=> 'color'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_instock_notification_font_weight',
			'title' 	=> __( 'In Stock Font Weight', 'wc_availability_notifications' ),
			'type'		=> 'select',
			'default' 	=> '',
			'options' 	=> self::get_font_weight_options()
		);
		
		// Low Stock Settings
				
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_lowstock_notification',
			'title' 	=> __( 'Low Stock Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show a "low stock" default notification on the product.', 'wc_availability_notifications' ),
			'type' 		=> 'textarea',
			'class' 	=> 'large-text',
			'css' 		=> 'height: 75px;'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_lowstock_notification_font_color',
			'title' 	=> __( 'Low Stock Font Color', 'wc_availability_notifications' ),
			'type' 		=> 'color'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_lowstock_notification_font_weight',
			'title' 	=> __( 'Low Stock Font Weight', 'wc_availability_notifications' ),
			'type'		=> 'select',
			'default' 	=> '',
			'options' 	=> self::get_font_weight_options()
		);
		
		// Out Of Stock
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_outofstock_notification',
			'title' 	=> __( 'Out Of Stock Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show an "out of stock" default notification on the product.', 'wc_availability_notifications' ),
			'type' 		=> 'textarea',
			'class' 	=> 'large-text',
			'css' 		=> 'height: 75px;'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_outofstock_notification_font_color',
			'title' 	=> __( 'Out Of Stock Font Color', 'wc_availability_notifications' ),
			'type' 		=> 'color'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_outofstock_notification_font_weight',
			'title' 	=> __( 'Out Of Stock Font Weight', 'wc_availability_notifications' ),
			'type'		=> 'select',
			'default' 	=> '',
			'options' 	=> self::get_font_weight_options()
		);
		
		// Backorder Settings
				
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_backorder_notification',
			'title' 	=> __( 'Backorder Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show a "backorder" default notification on the product.', 'wc_availability_notifications' ),
			'type' 		=> 'textarea',
			'class' 	=> 'large-text',
			'css' 		=> 'height: 75px;'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_backorder_notification_font_color',
			'title' 	=> __( 'Backorder Font Color', 'wc_availability_notifications' ),
			'type' 		=> 'color'
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_backorder_notification_font_weight',
			'title' 	=> __( 'Backorder Font Weight', 'wc_availability_notifications' ),
			'type'		=> 'select',
			'default' 	=> '',
			'options' 	=> self::get_font_weight_options()
		);
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_display_backorder_if_out_of_stock',
			'title' 	=> __( 'Display Backorder Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show backorder notification if product is out of stock', 'wc_availability_notifications' ),
			'type' 		=> 'checkbox',
			'default' 	=> ''
		);
		
		// Sale Flash
		
		$settings[] = array(
			'id' 		=> 'woocommerce_availability_sale_notification',
			'title' 	=> __( 'Sale Notification', 'wc_availability_notifications' ),
			'desc' 		=> __( 'Show an "sale" default notification on the product.', 'wc_availability_notifications' ),
			'type' 		=> 'textarea',
			'class' 	=> 'large-text',
			'css' 		=> 'height: 75px;'
		);

		$settings[] = array( 
			'type' 		=> 'sectionend', 
			'id' 		=> 'availability_notifications',
		);
		
		return $settings;
	}
	
	/**
	 * Retrieve font weight options
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public static function get_font_weight_options() {
		return array(
			'' 			=> __( 'Normal', 	'wc_availability_notifications' ),
			'bold' 		=> __( 'Bold', 		'wc_availability_notifications' ),
			'bolder' 	=> __( 'Bolder', 	'wc_availability_notifications' ),
			'lighter' 	=> __( 'Lighter', 	'wc_availability_notifications' ),
			'number' 	=> __( 'Number', 	'wc_availability_notifications' ),
			'initial' 	=> __( 'Initial', 	'wc_availability_notifications' ),
			'inherit' 	=> __( 'Inherit', 	'wc_availability_notifications' )
		);
	}
}

/**
 * Initialize
 * 
 * @since 1.0.0
 */
new WC_Availability_Notifications_Settings();

endif;