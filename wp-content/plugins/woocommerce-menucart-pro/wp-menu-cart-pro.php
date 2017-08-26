<?php
/*
Plugin Name: WP Menu Cart Pro
Plugin URI: www.wpovernight.com/plugins
Description: Extension for your e-commerce plugin (WooCommerce, WP-Ecommerce, Easy Digital Downloads, Eshop or Jigoshop) that places a cart icon with number of items and total cost in the menu bar. Activate the plugin, set your options and you're ready to go! Will automatically conform to your theme styles.
Version: 2.5.7
Author: Jeremiah Prummer, Ewout Fernhout
Author URI: www.wpovernight.com/about
License: GPL2
*/

class WpMenuCartPro {	 

	public static $plugin_slug;
	public static $plugin_basename;

	/**
	 * Construct.
	 */
	public function __construct() {
		self::$plugin_slug = basename(dirname(__FILE__));
		self::$plugin_basename = plugin_basename(__FILE__);

		$this->options = get_option('wpmenucart');

		// Init updater data
		$this->item_name	= 'Menu Cart Pro';
		$this->file			= __FILE__;
		$this->license_slug	= 'wpmenucart_pro_license';
		$this->version		= '2.5.7';
		$this->author		= 'Jeremiah Prummer, Ewout Fernhout';

		add_action( 'plugins_loaded', array( &$this, 'load_updater' ), 0 );

		// load the localisation & classes
		add_action( 'plugins_loaded', array( &$this, 'languages' ), 0 ); // or use init?
		add_action( 'init', array( &$this, 'wpml' ), 0 );
		add_action( 'init', array( $this, 'load_classes' ) );

		// enqueue scripts & ajax
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_scripts_styles' ) ); // Load scripts
		add_action( 'wp_ajax_wpmenucart_ajax', array( &$this, 'wpmenucart_ajax' ), 0 );
		add_action( 'wp_ajax_nopriv_wpmenucart_ajax', array( &$this, 'wpmenucart_ajax' ), 0 );

		// add filters to selected menus to add cart item <li>
		add_action( 'init', array( $this, 'filter_nav_menus' ) );
		// $this->filter_nav_menus();
		
		add_shortcode('wpmenucart', array( &$this, 'shortcode' ) );
	}

	/**
	 * Run the updater scripts from the Sidekick
	 * @return void
	 */
	public function load_updater() {
		// Check if sidekick is loaded
		if (class_exists('WPO_Updater')) {
			$this->updater = new WPO_Updater( $this->item_name, $this->file, $this->license_slug, $this->version, $this->author );
		}
	}

	/**
	 * Load classes
	 * @return void
	 */
	public function load_classes() {
		include_once( 'includes/wpmenucart-settings-pro.php' );
		$this->settings = new WpMenuCartPro_Settings();

		if ( $this->good_to_go() ) {			
			if (isset($this->options['shop_plugin'])) {
				switch ($this->options['shop_plugin']) {
					case 'woocommerce':
						include_once( 'includes/wpmenucart-woocommerce.php' );
						include_once( 'includes/wpmenucart-woocommerce-pro.php' );
						if ( isset($this->options['builtin_ajax']) ) {
							add_action("wp_enqueue_scripts", array( &$this, 'load_custom_ajax' ), 0 );
						} else {
							add_filter( 'add_to_cart_fragments', array( &$this, 'woocommerce_ajax_fragments' ) );
						}
						$this->shop = new WPMenuCart_WooCommerce_Pro();
						break;
					case 'jigoshop':
						include_once( 'includes/wpmenucart-jigoshop.php' );
						include_once( 'includes/wpmenucart-jigoshop-pro.php' );
						$this->shop = new WPMenuCart_Jigoshop_Pro();
						if ( isset($this->options['builtin_ajax']) ) {
							add_action("wp_enqueue_scripts", array( &$this, 'load_custom_ajax' ), 0 );
						} else {
							add_filter( 'add_to_cart_fragments', array( &$this, 'woocommerce_ajax_fragments' ) );
						}
						break;
					case 'wp-e-commerce':
						include_once( 'includes/wpmenucart-wpec.php' );
						include_once( 'includes/wpmenucart-wpec-pro.php' );
						$this->shop = new WPMenuCart_WPEC_Pro();
						add_action("wp_enqueue_scripts", array( &$this, 'load_custom_ajax' ), 0 );
						break;
					case 'eshop':
						include_once( 'includes/wpmenucart-eshop.php' );
						include_once( 'includes/wpmenucart-eshop-pro.php' );
						$this->shop = new WPMenuCart_eShop_Pro();
						add_action("wp_enqueue_scripts", array( &$this, 'load_custom_ajax' ), 0 );
						break;
					case 'easy-digital-downloads':
						include_once( 'includes/wpmenucart-edd.php' );
						include_once( 'includes/wpmenucart-edd-pro.php' );
						$this->shop = new WPMenuCart_EDD_Pro();
						add_action("wp_enqueue_scripts", array( &$this, 'load_custom_ajax' ), 0 );
						break;
				}
			}
		}
	}

	/**
	 * Check if a shop is active or if conflicting old versions of the plugin are active
	 * @return boolean
	 */
	public function good_to_go() {
		$wpmenucart_shop_check = get_option( 'wpmenucart_shop_check' );
		$active_plugins = $this->get_active_plugins();

		// check for shop plugins
		if ( !$this->is_shop_active() && $wpmenucart_shop_check != 'hide' ) {
			add_action( 'admin_notices', array ( $this, 'need_shop' ) );
			return FALSE;
		}

		// check for old versions
		if ( count( $this->get_active_old_versions() ) > 0 ) {
			add_action( 'admin_notices', array ( $this, 'woocommerce_version_active' ) );
			return FALSE;
		}

		// check for free version
		if ( in_array( 'wp-menu-cart/wp-menu-cart.php', $active_plugins ) ) {
			add_action( 'admin_notices', array ( $this, 'free_version_active' ) );
			return FALSE;
		}

		// we made it! good to go :o)
		return TRUE;
	}

	/**
	 * Return true if one ore more shops are activated.
	 * @return boolean
	 */
	public function is_shop_active() {
		if ( count($this->get_active_shops()) > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	/**
	 * Get an array of all active plugins, including multisite
	 * @return array active plugin paths
	 */
	public static function get_active_plugins() {
		$active_plugins = (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		if (is_multisite()) {
			// get_site_option( 'active_sitewide_plugins', array() ) returns a 'reversed list'
			// like [hello-dolly/hello.php] => 1369572703 so we do array_keys to make the array
			// compatible with $active_plugins
			$active_sitewide_plugins = (array) array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			// merge arrays and remove doubles
			$active_plugins = (array) array_unique( array_merge( $active_plugins, $active_sitewide_plugins ) );
		}

		return $active_plugins;
	}
	
	/**
	 * Get array of active shop plugins
	 * 
	 * @return array plugin name => plugin path
	 */
	public static function get_active_shops() {
		$active_plugins = self::get_active_plugins();

		$shop_plugins = array (
			'WooCommerce'				=> 'woocommerce/woocommerce.php',
			'Jigoshop'					=> 'jigoshop/jigoshop.php',
			'WP e-Commerce'				=> 'wp-e-commerce/wp-shopping-cart.php',
			'eShop'						=> 'eshop/eshop.php',
			'Easy Digital Downloads'	=> 'easy-digital-downloads/easy-digital-downloads.php',
		);
		
		// filter shop plugins & add shop names as keys
		$active_shop_plugins = array_intersect( $shop_plugins, $active_plugins );

		return $active_shop_plugins;
	}

	/**
	 * Get array of active old WooCommerce Menu Cart plugins
	 * 
	 * @return array plugin paths
	 */
	public function get_active_old_versions() {
		$active_plugins = $this->get_active_plugins();
		
		$old_versions = array (
			'woocommerce-menu-bar-cart/wc_cart_nav.php',				//first version
			'woocommerce-menu-bar-cart/woocommerce-menu-cart.php',		//last free version
			'woocommerce-menu-cart/woocommerce-menu-cart.php',			//never actually released? just in case...
			'woocommerce-menu-cart-pro/woocommerce-menu-cart-pro.php',	//old pro version
		);
			
		$active_old_plugins = array_intersect( $old_versions, $active_plugins );
				
		return $active_old_plugins;
	}	

	/**
	 * Fallback admin notices
	 *
	 * @return string Fallack notice.
	 */
	public function need_shop() {
		$error = __( 'WP Menu Cart Pro could not detect an active shop plugin. Make sure you have activated at least one of the supported plugins.' , 'wpmenucart' );
		$message = sprintf('<div class="error"><p>%1$s <a href="%2$s">%3$s</a></p></div>', $error, add_query_arg( 'hide_wpmenucart_shop_check', 'true' ), __( 'Hide this notice', 'wpmenucart' ) );
		echo $message;
	}

	public function woocommerce_version_active() {
		$error = __( 'An old version of WooCommerce Menu Cart is currently activated, you need to disable or uninstall it for WP Menu Cart to function properly' , 'wpmenucart' );
		$message = '<div class="error"><p>' . $error . '</p></div>';
		echo $message;
	}

	public function free_version_active() {
		$error = __( 'The free version of WP Menu Cart is currently activated, you need to disable or uninstall it for WP Menu Cart Pro to function properly' , 'wpmenucart' );
		$message = '<div class="error"><p>' . $error . '</p></div>';
		echo $message;
	}

	/**
	 * Load translations.
	 */
	public function languages() {
		load_plugin_textdomain( 'wpmenucart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	* Register strings for WPML String Translation
	*/
	public function wpml() {
		if ( isset($this->options['wpml_string_translation']) && function_exists( 'icl_register_string' ) ) {
			icl_register_string('WP Menu Cart', 'item text', 'item');
			icl_register_string('WP Menu Cart', 'items text', 'items');
			icl_register_string('WP Menu Cart', 'empty cart text', 'your cart is currently empty');
			icl_register_string('WP Menu Cart', 'hover text', 'View your shopping cart');
			icl_register_string('WP Menu Cart', 'empty hover text', 'Start shopping');
		}
	}


	/**
	 * Load custom ajax
	 */
	public function load_custom_ajax() {
		wp_enqueue_script(
			'wpmenucart',
			plugins_url( '/javascript/wpmenucart.js' , __FILE__ ),
			array( 'jquery' ),
			$this->version,
			true
		);

		// get URL to WordPress ajax handling page  
		if ( $this->options['shop_plugin'] == 'easy-digital-downloads' && function_exists( 'edd_get_ajax_url' ) ) {
			// use EDD function to prevent SSL issues http://git.io/V7w76A
			$ajax_url = edd_get_ajax_url();
		} else {
			$ajax_url = admin_url( 'admin-ajax.php' );
		}

		wp_localize_script(
			'wpmenucart',
			'wpmenucart_ajax',
			array(
				'ajaxurl' => $ajax_url,
				'nonce' => wp_create_nonce('wpmenucart')
			)
		);
	}

	/**
	 * Load CSS
	 */
	public function load_scripts_styles() {
		if (isset($this->options['icon_display'])) {
			wp_register_style( 'wpmenucart-icons', plugins_url( '/css/wpmenucart-icons-pro.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'wpmenucart-icons' );
		}
		
		$css = file_exists( get_stylesheet_directory() . '/wpmenucart-main.css' )
			? get_stylesheet_directory_uri() . '/wpmenucart-main.css'
			: plugins_url( '/css/wpmenucart-main.css', __FILE__ );

		wp_register_style( 'wpmenucart', $css, array(), '', 'all' );
		wp_enqueue_style( 'wpmenucart' );

		//Load Stylesheet if twentytwelve is active
		if ( wp_get_theme() == 'Twenty Twelve' ) {
			wp_register_style( 'wpmenucart-twentytwelve', plugins_url( '/css/wpmenucart-twentytwelve.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'wpmenucart-twentytwelve' );
		}

		//Load Stylesheet if twentyfourteen is active
		if ( wp_get_theme() == 'Twenty Fourteen' ) {
			wp_register_style( 'wpmenucart-twentyfourteen', plugins_url( '/css/wpmenucart-twentyfourteen.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'wpmenucart-twentyfourteen' );
		}
	}

	/**
	 * Add filters to selected menus to add cart item <li>
	 */
	public function filter_nav_menus() {
		// exit if no shop class is active
		if ( !isset($this->shop) )
			return;

		// exit if no menus set
		if ( !isset( $this->options['menu_slugs'] ) || empty( $this->options['menu_slugs'] ) )
			return;

		//grab menu slugs
		$menu_slugs = apply_filters( 'wpmenucart_menu_slugs', $this->options['menu_slugs'] );

		// Loop through $menu_slugs array and add cart <li> item to each menu
		foreach ($menu_slugs as $menu_slug) {
			if ( $menu_slug != '0' ) {
				add_filter( 'wp_nav_menu_' . $menu_slug . '_items', array( &$this, 'add_itemcart_to_menu' ) , 10, 2 );
			}
		}
	}
	
	/**
	 * Add Menu Cart to menu
	 * 
	 * @return menu items + Menu Cart item
	 */
	public function add_itemcart_to_menu( $items ) {
		$item_data = $this->shop->menu_item();

		$classes = 'wpmenucartli wpmenucart-display-'.$this->options['items_alignment'];
		
		if ($this->get_common_li_classes($items) != '')
			$classes .= ' ' . $this->get_common_li_classes($items);

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		if ( in_array( 'ubermenu/ubermenu.php', $active_plugins ) ) {
			if(defined('UBERMENU_VERSION') && (version_compare(UBERMENU_VERSION, '3.0.0') >= 0)){
				$classes .= ' ubermenu-item-has-children ubermenu-has-submenu-drop ubermenu-has-submenu-mega';
			} else {
				$classes .= ' mega-with-sub';
			}
		}
		
		$classes .= (isset($this->options['custom_class']) && $this->options['custom_class'] != '') ? ' '. $this->options['custom_class'] : '';
	
		if ($item_data['cart_contents_count'] == 0) {
			$classes .= ' empty';
		}
		// Filter for <li> item classes
		/* Usage (in the themes functions.php):
		add_filter('wpmenucart_menu_item_classes', 'add_wpmenucart_item_class', 1, 1);
		function add_wpmenucart_item_class ($classes) {
			$classes .= ' yourclass';
			return $classes;
		}
		*/
		$classes = apply_filters( 'wpmenucart_menu_item_classes', $classes );

		$this->menu_items['menu']['menu_item_li_classes'] = $classes;

		// DEPRICATED: These filters are now deprecated in favour of the more precise filters in the functions!
		$wpmenucart_menu_item = apply_filters( 'wpmenucart_menu_item_filter', $this->wpmenucart_menu_item() );
		$wpmenucart_submenu_item = apply_filters( 'wpmenucart_submenu_item', $this->wpmenucart_submenu_item() );


		$menu_item_li = '<li class="'.$classes.'" id="wpmenucartli">' . $wpmenucart_menu_item . $wpmenucart_submenu_item . '</li>';

		
		if ( apply_filters('wpmenucart_prepend_menu_item', false) ) {
			$items = apply_filters( 'wpmenucart_menu_item_wrapper', $menu_item_li ) . $items;
		} else {
			$items .= apply_filters( 'wpmenucart_menu_item_wrapper', $menu_item_li );
		}

		return $items;
	}

	/**
	 * Get a flat list of common classes from all menu items in a menu
	 * @param  string $items nav_menu HTML containing all <li> menu items
	 * @return string        flat (imploded) list of common classes
	 */
	public function get_common_li_classes($items) {
		if (empty($items)) return;
		
		$libxml_previous_state = libxml_use_internal_errors(true); // enable user error handling

		$dom_items = new DOMDocument;
		$dom_items->loadHTML( $items );
		$lis = $dom_items->getElementsByTagName('li');
		
		if (empty($lis)) {
			libxml_clear_errors();
			libxml_use_internal_errors($libxml_previous_state);
			return;
		}
		
		foreach($lis as $li) {
			if ($li->parentNode->tagName != 'ul')
				$li_classes[] = explode( ' ', $li->getAttribute('class') );
		}
		
		// Uncomment to dump DOM errors / warnings
		//$errors = libxml_get_errors();
		//print_r ($errors);
		
		// clear errors and reset to previous error handling state
		libxml_clear_errors();
		libxml_use_internal_errors($libxml_previous_state);
		
		if ( !empty($li_classes) ) {
			$common_li_classes = array_shift($li_classes);
			foreach ($li_classes as $li_class) {
				$common_li_classes = array_intersect($li_class, $common_li_classes);
			}
			$common_li_classes_flat = implode(' ', $common_li_classes);
		}
		return $common_li_classes_flat;
	}

	/**
	 * Ajaxify Menu Cart
	 */
	public function woocommerce_ajax_fragments( $fragments ) {
		$fragments['a.wpmenucart-contents'] = $this->wpmenucart_menu_item();
		$fragments['ul.wpmenucart'] = $this->wpmenucart_submenu_item();
		return $fragments;
	}

	/**
	 * Create HTML for Menu Cart item
	 */
	public function wpmenucart_menu_item() {
		$item_data = $this->shop->menu_item();

		// Check empty cart settings
		if ($item_data['cart_contents_count'] == 0 && ( !isset($this->options['always_display']) ) ) {
			$empty_menu_item = '<a class="wpmenucart-contents empty-wpmenucart" style="display:none">&nbsp;</a>';
			return $empty_menu_item;
		}
		
		if ( isset($this->options['wpml_string_translation']) && function_exists( 'icl_t' ) ) {
			//use WPML
			$viewing_cart = icl_t('WP Menu Cart', 'hover text', 'View your shopping cart');
			$start_shopping = icl_t('WP Menu Cart', 'empty hover text', 'Start shopping');
			$cart_contents = $item_data['cart_contents_count'] .' '. ( $item_data['cart_contents_count'] == 1 ?  icl_t('WP Menu Cart', 'item text', 'item') :  icl_t('WP Menu Cart', 'items text', 'items') );
		} else {
			//use regular WP i18n
			$viewing_cart = __('View your shopping cart', 'wpmenucart');
			$start_shopping = __('Start shopping', 'wpmenucart');
			$cart_contents = sprintf(_n('%d item', '%d items', $item_data['cart_contents_count'], 'wpmenucart'), $item_data['cart_contents_count']);
		}

		$this->menu_items['menu']['cart_contents'] = $cart_contents;

		if ($item_data['cart_contents_count'] == 0) {
			$menu_item_href = apply_filters ('wpmenucart_emptyurl', $item_data['shop_page_url'] );
			$menu_item_title = apply_filters ('wpmenucart_emptytitle', $start_shopping );
			$menu_item_classes = 'wpmenucart-contents empty-wpmenucart-visible';
		} else {
			$menu_item_href = apply_filters ('wpmenucart_fullurl', $item_data['cart_url'] );
			$menu_item_title = apply_filters ('wpmenucart_fulltitle', $viewing_cart );
			$menu_item_classes = 'wpmenucart-contents';
		}

		$this->menu_items['menu']['menu_item_href'] = $menu_item_href;
		$this->menu_items['menu']['menu_item_title'] = $menu_item_title;

		if(defined('UBERMENU_VERSION') && (version_compare(UBERMENU_VERSION, '3.0.0') >= 0)){
			$menu_item_classes .= ' ubermenu-target';
		}

		$menu_item = '<a class="'.$menu_item_classes.'" href="'.$menu_item_href.'" title="'.$menu_item_title.'">';

		$menu_item_a_content = '';	
		if (isset($this->options['icon_display'])) {
			$icon = isset($this->options['cart_icon']) ? $this->options['cart_icon'] : '0';
			$menu_item_icon = '<i class="wpmenucart-icon-shopping-cart-'.$icon.'"></i>';
			$menu_item_a_content .= $menu_item_icon;
		}
		
		switch ($this->options['items_display']) {
			case 1: //items only
				$menu_item_a_content .= '<span class="cartcontents">'.$cart_contents.'</span>';
				break;
			case 2: //price only
				$menu_item_a_content .= '<span class="amount">'.$item_data['cart_total'].'</span>';
				break;
			case 3: //items & price
				$menu_item_a_content .= '<span class="cartcontents">'.$cart_contents.'</span><span class="amount">'.$item_data['cart_total'].'</span>';
				break;
		}
		$menu_item_a_content = apply_filters ('wpmenucart_menu_item_a_content', $menu_item_a_content, $menu_item_icon, $cart_contents, $item_data );

		$this->menu_items['menu']['menu_item_a_content'] = $menu_item_a_content;

		$menu_item .= $menu_item_a_content . '</a>';
		
		$menu_item = apply_filters ('wpmenucart_menu_item_a', $menu_item,  $item_data, $this->options, $menu_item_a_content, $viewing_cart, $start_shopping, $cart_contents);

		if( !empty( $menu_item ) ) return $menu_item;		
	}
	
	/**
	 * Create HTML for Menu Cart submenu
	 */
	public function wpmenucart_submenu_item() {
		$submenu_data = $this->shop->submenu_items();
		$item_data = $this->shop->menu_item();
		$cart_url = $this->shop->get_cart_url();

		// Check empty cart settings
		if ($item_data['cart_contents_count'] == 0 && ( !isset($this->options['always_display']) ) ) {
			$empty_submenu_item = '<ul class="sub-menu wpmenucart empty-wpmenucart" style="display:none"></ul>';
			return $empty_submenu_item;
		}

						
		if ( isset($this->options['wpml_string_translation']) && function_exists( 'icl_t' ) ) {
			//use WPML
			$viewing_cart = icl_t('WP Menu Cart', 'hover text', 'View your shopping cart');
			$empty_cart = icl_t('WP Menu Cart', 'empty cart text', 'your cart is currently empty');
		} else {
			//use regular WP i18n
			$viewing_cart = __('View your shopping cart', 'wpmenucart');
			$empty_cart = __('your cart is currently empty', 'wpmenucart');		
		}

		$this->menu_items['submenu']['viewing_cart'] = $viewing_cart;
		$this->menu_items['submenu']['empty_cart'] = $empty_cart;
		
		// Filter for <ul> submenu classes & style
		// IMPORTANT! Needs browser reset because of AJAX!
		/* Usage (in the themes functions.php):
		add_filter('wpmenucart_submenu_classes', 'add_wpmenucart_submenu_class', 1, 1);
		function add_wpmenucart_submenu_class ($submenu_classes) {
			$submenu_classes .= ' yourclass';
			return $submenu_classes;
		}
		*/
		$submenu_classes = apply_filters( 'wpmenucart_submenu_classes', 'sub-menu wpmenucart' );
		$submenu_style = apply_filters( 'wpmenucart_submenu_style', '' );

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		if ( in_array( 'ubermenu/ubermenu.php', $active_plugins ) ) {
			if(defined('UBERMENU_VERSION') && (version_compare(UBERMENU_VERSION, '3.0.0') >= 0)){
				$submenu_classes .= ' ubermenu-submenu ubermenu-submenu-type-auto ubermenu-submenu-type-mega ubermenu-submenu-drop';
			} else {
				$submenu_classes .= ' sub-menu-1';
				$submenu_style .= ' display:none;';
			}
		}

		$this->menu_items['submenu']['submenu_classes'] = $submenu_classes;
		$this->menu_items['submenu']['submenu_style'] = $submenu_style;

		$cart_submenu = '';
		if (isset($this->options['flyout_display'])) {
			// Based on woocommerce/templates/cart/cart.php		
			if ( $submenu_data != '' ) {
				$cart_submenu = '<ul class="' . $submenu_classes . '"' . (!empty($submenu_style) ? ' style="'.$submenu_style.'"' : '') . '>';

				$i = 0;
				
				$cart_submenu_items = '';
				
				foreach ( $submenu_data as $submenu_item_data ) {
					// Format submenu item content (without permalink or li yet!)
					
					// The thumbnail
					$cart_submenu_item_content = '<span class="wpmenucart-thumbnail">'.$submenu_item_data['item_thumbnail'].'</span>';

					// Item info wrapper
					$cart_submenu_item_content .= '<span class="wpmenucart-order-item-info">';
						// Product Name
						
						// remove any HTML formatting from the item name
						$submenu_item_data['item_name'] = strip_tags($submenu_item_data['item_name']);

						// strip out / truncate $item_name
						if (strlen($submenu_item_data['item_name']) > apply_filters( 'wpmenucart_submenu_name_truncate', '20') ) {
							$submenu_item_data['item_name'] = $this->truncate_name($submenu_item_data['item_name']);
						}
		
						$cart_submenu_item_content .= '<span class="wpmenucart-product-name">'.$submenu_item_data['item_name'].'</span>';

						// Quantity x price
						$cart_submenu_item_content .= '<span class="wpmenucart-product-quantity-price">';
						$cart_submenu_item_content .=  $submenu_item_data['item_quantity'] .' x '. $submenu_item_data['item_price'];
						$cart_submenu_item_content .= '</span>';
					$cart_submenu_item_content .= '</span>';

					$this->menu_items['submenu']['items'][$i]['cart_submenu_item_content'] = $cart_submenu_item_content;
				
					$submenu_item_data['item_permalink'] = apply_filters( 'wpmenucart_submenu_item_link', $submenu_item_data['item_permalink'], $submenu_item_data );

					$this->menu_items['submenu']['items'][$i]['item_permalink'] = $submenu_item_data['item_permalink'];

					// Permalink wrap
					if ( $submenu_item_data['item_permalink'] != '' )
						$cart_submenu_item_content = sprintf('<a href="%s" class="clearfix">%s</a>', $submenu_item_data['item_permalink'], $cart_submenu_item_content );

					// Set <li> classes
					$cart_submenu_item_li_classes = apply_filters( 'wpmenucart_submenu_item_li_classes', 'menu-item wpmenucart-submenu-item clearfix', $submenu_item_data );
					$this->menu_items['submenu']['items'][$i]['item_li_classes'] = $cart_submenu_item_li_classes;

					// Wrap in <li>
					$cart_submenu_item_li = '<li class="' . $cart_submenu_item_li_classes . '">'.$cart_submenu_item_content.'</li>';
					$cart_submenu_item_li = apply_filters( 'wpmenucart_submenu_item_li', $cart_submenu_item_li, $submenu_item_data );
					
					// Add li to item list
					$cart_submenu_items .= $cart_submenu_item_li;

					$flyout_itemnumber = apply_filters( 'wpmenucart_flyout_itemnumber', $this->options['flyout_itemnumber'] );
					if ( $flyout_itemnumber > 0 ) {
						if (++$i == $flyout_itemnumber ) break; //stop at set number
					}
					else {
						if (++$i == 5) break; //stop at 5
					}
				}

				$cart_submenu .= apply_filters('wpmenucart_submenu_items', $cart_submenu_items);

				$viewing_cart = apply_filters('wpmenucart_viewcarttext', $viewing_cart);
				$cart_link = sprintf('<a href="%s" title="%s">%s &rarr;</a>', $cart_url, $viewing_cart, $viewing_cart );
				$cart_link_item = '<li class="menu-item wpmenucart-submenu-item cart-link">'.$cart_link.'</li>';
				
				$cart_submenu .= apply_filters('wpmenucart_cart_link_item', $cart_link_item);

				$cart_submenu .= '</ul>';
			} else {
				$cart_submenu = sprintf('<ul class="%s" style="display:none"><li class="menu-item wpmenucart-submenu-item clearfix"><a href="%s">%s</a></li></ul>', $submenu_classes, apply_filters ('wpmenucart_emptyurl', $item_data['shop_page_url'] ), apply_filters('wpmenucart_emptyul', $empty_cart));
			}
		}
		return $cart_submenu;	
	}
	
	/**
	 * Create HTML for shortcode
	 * @param  array $atts shortcode attributes
	 * @return string      'menucart' html
	 */
	public function shortcode($atts) {
		extract(shortcode_atts( array('style' => '', 'flyout' => 'hover', 'before' => '', 'after' => '') , $atts));

		$menu = $before . '<span class="reload_shortcode">'.$this->wpmenucart_menu_item() . $this->wpmenucart_submenu_item() . '</span>' . $after;
		$html = '<div class="wpmenucart-shortcode '.$flyout.'" style="'.$style.'">'.$menu.'</div>';
		return $html;
	}
	
	public function wpmenucart_ajax() {
		$variable = $this->wpmenucart_menu_item();
		$variable.= $this->wpmenucart_submenu_item();
		echo $variable;
		die();
	}

	private function truncate_name($item_name) {
		$encoding = function_exists('mb_detect_encoding') && (mb_detect_encoding($item_name) != 'ASCII') ? mb_detect_encoding($item_name) : 'utf-8';
		$item_name = html_entity_decode($item_name, ENT_QUOTES, $encoding); // avoiding to truncate html characters into garbled text
		$separator = '...';
		$separatorlength = strlen($separator);
		$maxlength = apply_filters( 'wpmenucart_submenu_name_truncate', '20');
		$lastcharacters = 0; // option to keep last characters, like "WooCommerce Me...art"
		$start = $maxlength - $separatorlength - $lastcharacters;
		$trunc = strlen($item_name) - $maxlength + $separatorlength;
		$item_name = substr_replace($item_name, $separator, $start, $trunc);
	
		$item_name = htmlentities($item_name, ENT_QUOTES, $encoding); // restore html characters
		return $item_name;
	}
}

$wpMenuCart = new WpMenuCartPro();

/**
 * Hide notifications
 */

if ( ! empty( $_GET['hide_wpmenucart_shop_check'] ) ) {
	update_option( 'wpmenucart_shop_check', 'hide' );
}

/**
 * WPOvernight updater admin notice
 */
if ( ! class_exists( 'WPO_Updater' ) && ! function_exists( 'wpo_updater_notice' ) ) {

	if ( ! empty( $_GET['hide_wpo_updater_notice'] ) ) {
		update_option( 'wpo_updater_notice', 'hide' );
	}

	/**
	 * Display a notice if the "WP Overnight Sidekick" plugin hasn't been installed.
	 * @return void
	 */
	function wpo_updater_notice() {
		$wpo_updater_notice = get_option( 'wpo_updater_notice' );

		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = get_site_option( 'active_sitewide_plugins', array() );
		$plugin = 'wpovernight-sidekick/wpovernight-sidekick.php';

		if ( in_array( $plugin, $blog_plugins ) || isset( $site_plugins[$plugin] ) || $wpo_updater_notice == 'hide' ) {
			return;
		}

		echo '<div class="updated fade"><p>Install the <strong>WP Overnight Sidekick</strong> plugin to receive updates for your WP Overnight plugins - check your order confirmation email for more information. <a href="'.add_query_arg( 'hide_wpo_updater_notice', 'true' ).'">Hide this notice</a></p></div>' . "\n";
	}

	add_action( 'admin_notices', 'wpo_updater_notice' );
}