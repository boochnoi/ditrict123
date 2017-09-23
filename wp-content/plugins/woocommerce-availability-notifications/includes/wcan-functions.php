<?php
/**
 * Common functions
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/includes
 * @since 		1.0.0
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieve availability notification
 * 
 * @since 1.0.0
 * 
 * @param 	string 	$type
 * @param 	int 	$product_id
 * @return 	string 	$notification
 */
function woocommerce_get_availability_notification($type, $product_id) {
	
	$notification = '';
	$product = get_product($product_id);
	$parent_id = (isset($product->parent->id) && $product->parent->id > 0) ? $product->parent->id : 0;
	
	switch ($type) {
		case 'instock' :
		case 'in_stock' :
		case 'in-stock' :
			$notification = get_post_meta($product_id, '_availability_instock_notification', true);
			$notification = $notification ? $notification : get_post_meta($parent_id, '_availability_instock_notification', true);
			$notification = $notification ? $notification : get_option( 'woocommerce_availability_instock_notification' );
			break;
			
		case 'lowstock' :
		case 'low_stock' :
		case 'low-stock' :
			$notification = get_post_meta($product_id, '_availability_lowstock_notification', true);
			$notification = $notification ? $notification : get_post_meta($parent_id, '_availability_lowstock_notification', true);
			$notification = $notification ? $notification : get_option('woocommerce_availability_lowstock_notification');
			break;
			
		case 'outofstock' :
		case 'out_of_stock' :
		case 'out-of-stock' :
			$notification = get_post_meta($product_id, '_availability_outofstock_notification', true);
			$notification = $notification ? $notification : get_post_meta($parent_id, '_availability_outofstock_notification', true);
			$notification = $notification ? $notification : get_option('woocommerce_availability_outofstock_notification');
			break;
			
		case 'backorder' :
		case 'back_order' : // Just in case :)
		case 'back-order' : // Just in case :)
			$notification = get_post_meta($product_id, '_availability_backorder_notification', true);
			$notification = $notification ? $notification : get_post_meta($parent_id, '_availability_backorder_notification', true);
			$notification = $notification ? $notification : get_option('woocommerce_availability_backorder_notification');
			break; 

		case 'sale' :
		case 'sale_flash' :
		case 'sale-flash' :
			$notification = get_post_meta($product_id, '_availability_sale_notification', true);
			$notification = $notification ? $notification : get_post_meta($parent_id, '_availability_sale_notification', true);
			$notification = $notification ? $notification : get_option('woocommerce_availability_sale_notification');
			break;
	}
	
	/**
	 * Filter
	 * 
	 * @since 1.0.3
	 * 
	 * @param 	string 	$notification
	 * @param 	string 	$type
	 * @param 	int 	$product_id
	 * @return 	string
	 */
	$notification = apply_filters('woocommerce_availability_notification', $notification, $type, $product_id);

	// @deprecated Back-compatibility and it will be remove in the future
	$notification = apply_filters('woocommcerce_availability_notification', $notification, $type, $product_id);
	
	return $notification;
}

/**
 * Retrieve availability notification by type
 *
 * @since 1.1.5
 *
 * @param string $type
 * @param int $product_id
 * @return string
 */
function wc_get_availability_notification($type, $product_id) {
	return woocommerce_get_availability_notification($type, $product_id);
}

/**
 * Retrieve availability notification CSS styles
 * 
 * @since 1.0.0
 * 
 * @return array $styles
 */
function woocommerce_get_availability_notification_styles() {
	
	$styles = array(
		'in-stock' => array(
			'elements' => array(
				'.woocommerce div.product .in-stock', 
			 	'.woocommerce #content div.product .in-stock', 
			 	'.woocommerce-page div.product .in-stock', 
			 	'.woocommerce-page #content div.product .in-stock'
			),
			'properties' => array(
				'color' 		=> get_option( 'woocommerce_availability_instock_notification_font_color' ),
				'font-weight'	=> get_option( 'woocommerce_availability_instock_notification_font_weight' )
			)
		),
		'low-stock' => array(
			'elements' => array(
				'.woocommerce div.product .low-stock', 
				'.woocommerce #content div.product .low-stock', 
				'.woocommerce-page div.product .low-stock', 
				'.woocommerce-page #content div.product .low-stock'
			),
			'properties' => array(
				'color' 		=> get_option( 'woocommerce_availability_lowstock_notification_font_color' ),
				'font-weight'	=> get_option( 'woocommerce_availability_lowstock_notification_font_weight' )
			)
		),
		'out-of-stock' => array(
			'elements' => array(
				'.woocommerce div.product .out-of-stock', 
				'.woocommerce #content div.product .out-of-stock', 
				'.woocommerce-page div.product .out-of-stock', 
				'.woocommerce-page #content div.product .out-of-stock'
			),
			'properties' => array(
				'color' 		=> get_option( 'woocommerce_availability_outofstock_notification_font_color' ),
				'font-weight'	=> get_option( 'woocommerce_availability_outofstock_notification_font_weight' )
			)
		),
		'backorder' => array(
			'elements' => array(
				'.woocommerce div.product .available-on-backorder', 
				'.woocommerce #content div.product .available-on-backorder', 
				'.woocommerce-page div.product .available-on-backorder', 
				'.woocommerce-page #content div.product .available-on-backorder'
			),
			'properties' => array(
				'color' 		=> get_option( 'woocommerce_availability_backorder_notification_font_color' ),
				'font-weight'	=> get_option( 'woocommerce_availability_backorder_notification_font_weight' )
			)
		)
	);
	
	return apply_filters( 'woocommerce_availability_notification_styles', $styles );
}

if ( ! function_exists( 'tp_styles' ) ) :
	
/**
 * Output CSS styles
 * 
 * @since 1.0.0
 * 
 * @param 	array 	$styles
 * @return 	void
 */
function tp_styles( $styles ) {
	echo tp_get_styles( $styles );
}
	
endif;

if ( ! function_exists( 'tp_get_styles' ) ) :
	
/**
 * Construct multiple CSS styles
 * 
 * @since 1.0.0
 * 
 * @param 	array 	$styles
 * @return 	string 	$_styles
 */
function tp_get_styles( $styles ) {
	$_styles = '';
		
	foreach ( (array) $styles as $style ) {
		if ( isset( $style['properties'] ) && ! empty( $style['properties'] ) ) {
			$_styles .= tp_get_style( $style['elements'], $style['properties'] );		
		}
	}
	
	return $_styles;
}
 
endif;

if ( ! function_exists( 'tp_get_style' ) ) :
	
/**
 * Construct single CSS styles
 * 
 * @since 1.0.0
 * 
 * @param 	array|string 	$elements 		An array of elements|Single-stringed element
 * @param 	array 			$properties 	Style properties
 * @return 	string 			$style 			Constructed single CSS styles
 */
function tp_get_style( $elements = '', $properties ) {
	$style = '';
	
	if ( is_array( $elements ) )
		$elements = implode( ', ', $elements );
	
	if ( ! empty( $elements ) && $properties )
		$style .= $elements . ' { ';
	
	foreach ( (array) $properties as $property_id => $property ) {
		if ( ! empty( $property ) && trim( $property ) != '!important' )
			$style .= "{$property_id}: {$property}; ";
	}
	
	if ( ! empty( $elements ) && $properties )
		$style .= '}';
	
	return $style;
} 
	
endif;
