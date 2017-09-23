<?php
/**
 * WooCommerce helper functions
 * 
 * @package 	TP Framework
 * @subpackage 	tp-framework\libraries\woocommerce
 * @since 		1.0.0
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	© 2014 ThemePlugger
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WC_Dependencies' ) ) {
	require_once( dirname( __FILE__ ) . '/classes/class-wc-dependencies.php' );
}

if ( ! function_exists( 'is_woocommerce_active' ) ) :

/**
 * Check if WooCommerce plugin is active
 * 
 * @since 1.0.0
 * 
 * @return boolean
 */
function is_woocommerce_active() {
	return WC_Dependencies::woocommerce_active_check();
}
	
endif;
