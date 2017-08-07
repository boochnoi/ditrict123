<?php
/*
	Plugin Name: WooCommerce Shipping Pro with Table Rate
	Plugin URI: https://www.xadapter.com/product/woocommerce-table-rate-shipping-pro-plugin/
	Description: Intuitive Rule Based Shipping Plug-in for WooCommerce. Set shipping rates based on rules based by Country, State, Post Code, Product Category,Shipping Class and Weight.
	Version: 2.8.7
	Author: Xadapter
	Author URI: https://www.xadapter.com/vendor/wooforce/
	Copyright: 2014-2015 Xadapter.
	*/
    
load_plugin_textdomain( 'wf_woocommerce_shipping_pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {	

    include( 'wf-shipping-pro-common.php' );
	if ( is_admin() ) {
		//include api manager
		include_once ( 'includes/wf_api_manager/wf-api-manager-config.php' );
	}
   
    if (!function_exists('wf_plugin_configuration')){
       function wf_plugin_configuration(){
            return array(
                'id' => 'wf_woocommerce_shipping_pro',
                'method_title' => __('Shipping Pro', 'wf_woocommerce_shipping_pro' ),
                'method_description' => __('Intuitive Rule Based Shipping Plug-in for WooCommerce. Set shipping rates based on rules based by Country, State, Post Code, Product Category,Shipping Class and Weight.', 'wf_woocommerce_shipping_pro' ));		
        }
    }

}

register_activation_hook( __FILE__, 'wf_plugin_activate' );

