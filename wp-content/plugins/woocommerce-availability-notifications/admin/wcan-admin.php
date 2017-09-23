<?php
/**
 * Admin
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/admin
 * @since 		1.0.3
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WC_Availability_Notifications_Admin' ) ) :

/**
 * WC_Availability_Notifications_Admin
 * 
 * @since 1.0.3
 */
class WC_Availability_Notifications_Admin {
	
	/**
	 * Constructor
	 * 
	 * @since 1.0.3
	 */
	public function __construct() {
		$this->setup_variables();
		$this->setup_includes();
		$this->setup_hooks();
	}
	
	
	/** Setup methods **************************************************/

	/**
	 * Setup the global variables
	 * 
	 * @since 1.0.3
	 */
	protected function setup_variables() {
		$parent = wc_availability_notifications();
		
		$this->admin_dir = $parent->admin_dir;
		$this->admin_url = $parent->admin_url;
	}
	
	/**
	 * Setup the included files
	 * 
	 * @since 1.0.3
	 */
	protected function setup_includes() {
		require_once($this->admin_dir . 'wcan-meta-box.php');
		require_once($this->admin_dir . 'wcan-settings.php');;
		require_once($this->admin_dir . 'wcan-product-bulk-edit.php');
		require_once($this->admin_dir . 'wcan-product-quick-edit.php');
		require_once($this->admin_dir . 'wcan-variation-bulk-edit.php');
	}
	
	/**
	 * Setup action and filter hooks
	 * 
	 * @since 1.0.3
	 */
	protected function setup_hooks() {
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
	}
	
	
	/** Hook methods **************************************************/
	
	/**
	 * Enqueue scripts
	 * 
	 * @since 1.0.3
	 */
	public function enqueue_scripts() {
		wp_enqueue_script('wc-availability-nofications', $this->admin_url . 'assets/js/wc-availability-notifications.js', array('jquery'));
	}
	
	/**
	 * Enqueue scripts
	 * 
	 * @since 1.0.3
	 */
	public function enqueue_styles() {
		wp_enqueue_style('wc-availability-notifications', $this->admin_url . 'assets/css/wc-availability-notifications.css');
	}
}

if ( ! function_exists('wc_availability_notifications_admin')) :

/**
 * WC_Availability_Notifications_Admin
 * 
 * @since 1.0.3
 * 
 * @return object WC_Availability_Notifications_Admin
 */
function wc_availability_notifications_admin() {
	return new WC_Availability_Notifications_Admin();
}

endif;

/**
 * Initialize
 * 
 * @since 1.0.3
 */
wc_availability_notifications()->admin = wc_availability_notifications_admin();
	
endif;
