<?php
/**
 * Shortcodes
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/includes
 * @since 		1.0.3
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCAN_Product_Stock_Shortcode' ) ) :

/**
 * WCAN_Product_Stock_Shortcode
 * 
 * @since 1.0.3
 */
class WCAN_Product_Stock_Shortcode {
	
	public function __construct() {
		$aliases = array(
			'stock',
			'product-stock',
			'product_stock',
			'woocommerce-stock',
			'woocommerce_stock'
		);
		
		foreach ($aliases as $alias) {
			add_shortcode($alias, array($this, 'shortcode'));
		}
	}
	
	/**
	 * Shortcode callback
	 * 
	 * @since 1.0.3
	 * 
	 * @param 	array 	$atts 		Shortcode attributes
	 * @param 	string 	$content
	 * @return 	string 	$content
	 */
	public function shortcode($atts, $content = null) {
		global $product;
		
		$product_id = 0;
	
		// Backward-compatibility
		if (isset($atts['id']) && $atts['id'] > 0) {
			$product_id = $atts['id'];
		}
		
		// Product ID may refer to both simple and variable IDs
		if (isset($atts['product_id']) && $atts['product_id'] > 0) {
			$product_id = $atts['product_id'];
		}
		
		if ($product_id > 0) {
			$product = get_product($product_id);
		
			// Retrieve product's availability notification
			$availability = $product->get_availability();
		
			if ($availability['availability']) {
				$content = apply_filters('woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability']);
			}
		}
		
		return $content;
	}
}

/**
 * Initialize
 * 
 * @since 1.0.3
 */
new WCAN_Product_Stock_Shortcode();

endif;