<?php

$product_name = 'shippingpro'; // name should match with 'Software Title' configured in server, and it should not contains white space
$product_version = '2.8.7';
$product_slug = 'woocommerce-shipping-pro/woocommerce-shipping-pro.php'; //product base_path/file_name
$serve_url = 'https://www.xadapter.com/';
$plugin_settings_url = admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_woocommerce_shipping_pro_method');

//include api manager
include_once ( 'wf_api_manager.php' );
new WF_API_Manager($product_name, $product_version, $product_slug, $serve_url, $plugin_settings_url);
?>