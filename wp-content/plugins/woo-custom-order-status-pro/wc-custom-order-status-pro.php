<?php
/*
Plugin Name: Custom Order Status Pro for WooCommerce
Plugin URI: http://www.appzab.com
Description: Manage your Orders more efficiently by creating customized order statuses, add notifications and customize emails completely. Works on Multisite environment and Allows easy Translations.
Version: 2.0.1
Author: Chris Barnett
Author URI: http://www.appzab.com
*/

defined('SB_DS') or define('SB_DS', DIRECTORY_SEPARATOR);
define('SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR', dirname(__FILE__));
define('SB_ADV_WC_ORDER_STATUS_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename(SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR));

require_once dirname(__FILE__) . SB_DS . 'the-plugin.php';
require_once dirname(__FILE__) . SB_DS . 'auto-update-status.php';
