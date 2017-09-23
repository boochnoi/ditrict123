<?php
/**
 * WooCommerce Availability Notifications
 * 
 * Plugin Name: WooCommerce Availability Notifications
 * Plugin URI: 	http://themeplugger.com/products/woocommerce-availability-notifications
 * Description: Enables your WooComerce to have customized stock availability notifications.
 * Author:  	ThemePlugger
 * Author URI: 	http://themeplugger.com
 * Version: 	1.1.5
 * Text Domain: wc_availability_notifications
 * License: 	GPL3+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * 
 * @package 	WooCommerce Availability Notifications
 * @version 	1.1.5
 * @author  	ThemePlugger <themeplugger@gmail.com>
 * @link     	http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'WC_Availability_Notifications' ) ) :

/**
 * WooCommerce Availability Notifications
 * 
 * @since 1.0.3
 */
final class WC_Availability_Notifications {
	
	/**
	 * Singleton
	 * 
	 * @since 1.0.3
	 * 
	 * @return object One true instance of WC_Availability_Notifications object
	 */
	public static function init() {
		static $instance = null;
		
		if ( $instance === null ) {
			$instance = new self();
			$instance->setup_variables();
			$instance->setup_dependencies();
			$instance->setup_includes();
			$instance->setup_hooks();
		}
		
		return $instance;
	}
	
	/**
	 * Setup dependencies
	 * 
	 * @since 1.0.3
	 */
	protected function setup_dependencies() {
		if ( ! function_exists('is_woocommerce_active')) {
			require_once( $this->libraries_dir . 'woocommerce/wc-functions.php');
		}
		
		if ( ! is_woocommerce_active()) {
			sprintf(__('Hey there! This plugin requires <a target="_blank" href="%s">WooCommerce</a> 1.6.6 or greater (and prefers the latest version - 2.x!). Please install and/or activate it first!', 'wc_availability_notifications'), admin_url( 'plugin-install.php?tab=search&s=woocommerce'));
		}
	}
	
	/**
	 * Setup the global variables
	 * 
	 * @since 1.0.3
	 */
	protected function setup_variables() {
		
		// Plugin unique identifier
		$this->id = 'wc-availability-notifications';
		
		// Plugin name
		$this->name = 'WooCommerce Availability Notifications';
		
		// Plugin prefix
		$this->prefix = 'wc_availability_notifications_';
		
		// Plugin version
		$this->version = '1.1.5';
		
		// Plugin textdomain
		$this->textdomain = 'wc_availability_notifications';
		
		// Plugin class name
		$this->class = __CLASS__;
		
		// Plugin relative file
		$this->file = __FILE__;
		
		// Plugin base directory path
		$this->base_dir = plugin_dir_path($this->file);
		
		// Plugin base directory URL
		$this->base_url = plugin_dir_url($this->file);
		
		// Plugin includes directory path
		$this->includes_dir = $this->base_dir . trailingslashit('includes');
		
		// Plugin includes directory URL
		$this->includes_url = $this->base_url . trailingslashit('includes');
		
		// Plugin libraries directory path
		$this->libraries_dir = $this->base_dir . trailingslashit('libraries');
		
		// Plugin libraries directory URL
		$this->libraries_url = $this->base_url . trailingslashit('libraries');
		
		// Plugin admin directory path
		$this->admin_dir = $this->base_dir . trailingslashit('admin');
		
		// Plugin admin directory URL
		$this->admin_url = $this->base_url . trailingslashit('admin');
		
		// Plugin public directory path
		$this->public_dir = $this->base_dir . trailingslashit('public');
		
		// Plugin public directory URL
		$this->public_url = $this->base_url . trailingslashit('public');
	}
	
	/**
	 * Setup the included files
	 * 
	 * @since 1.0.3
	 */
	protected function setup_includes() {
		
		// Include important files
		require_once($this->includes_dir . 'wcan-functions.php');
		require_once($this->includes_dir . 'wcan-shortcodes.php');
		
		// Public-facing
		require_once($this->public_dir . 'wcan-public.php');
		
		// Admin-facing
		if (is_admin()) {
			require_once($this->admin_dir . 'wcan-admin.php');
		}
	}
	
	/**
	 * Setup action and filter hooks
	 * 
	 * @since 1.0.3
	 */
	protected function setup_hooks() {
		// Do nothing here
	}
}
	
endif;

if ( ! function_exists( 'wc_availability_notifications' ) && class_exists( 'WC_Availability_Notifications' ) ) :

/**
 * Singleton function
 * 
 * @since 1.0.3
 * 
 * @return object One true instance of WC_Availability_Notifications object
 */
function wc_availability_notifications() {
	return WC_Availability_Notifications::init();
}

endif;

/**
 * Globalize
 * 
 * @since 1.0.3
 */
global $wc_availability_notifications;

/**
 * Initiliaze
 * 
 * @since 1.0.3
 */
$wc_availability_notifications = wc_availability_notifications();