<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }


    if (!function_exists('wf_plugin_path')){
        function wf_plugin_path() {
                return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
    }
	
    if (!function_exists('wf_pre_loaded_data')){
        $data_file = wf_plugin_path() . '/data/woocommerce-shipping-pro-pre-loaded-data.php';
        if (file_exists($data_file))
            include_once $data_file;
    }

    if (!function_exists('wf_get_settings_url')){
		function wf_get_settings_url(){
			return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
		}
	}
	
	if (!function_exists('wf_plugin_override')){
		add_action( 'plugins_loaded', 'wf_plugin_override' );
		function wf_plugin_override() {
			if (!function_exists('WC')){
				function WC(){
					return $GLOBALS['woocommerce'];
				}
			}
		}
	}

	if (!function_exists('wf_get_shipping_countries')){
		function wf_get_shipping_countries(){
			$woocommerce = WC();
			$shipping_countries = method_exists($woocommerce->countries, 'get_shipping_countries')
					? $woocommerce->countries->get_shipping_countries()
					: $woocommerce->countries->countries;
			return $shipping_countries;
		}
	}
	
	add_action( 'admin_enqueue_scripts', 'wf_scripts' );
	if (!function_exists('wf_scripts')){
        function wf_scripts() {
           wp_enqueue_script( 'jquery' );
        }
    }
    
    if (!function_exists('wf_plugin_url')){
        function wf_plugin_url() {
            return untrailingslashit( plugins_url( '/', __FILE__ ) );
        }
    }

    if (!function_exists('wf_plugin_basename')){
        function wf_plugin_basename() {
            return 'woocommerce-shipping-pro/woocommerce-shipping-pro.php';
        }
    }

    if (!function_exists('wf_plugin_activate')){
        function wf_plugin_activate() {
            wf_pre_load_settings();
        }
    }

    if (!function_exists('wf_pre_load_settings')){
        function wf_pre_load_settings(){
            $wf_shipping_pro_config = wf_plugin_configuration();
            if(get_option( 'woocommerce_wf_woocommerce_shipping_pro_settings') == false){
                $matrix_default_value = wf_get_rate_matrix_default();
                if(!empty($matrix_default_value)){
                    $new_settings = array(
                        'enabled' => 'yes',
                        'title' => $wf_shipping_pro_config['method_title'],
                        'rate_matrix' => $matrix_default_value,
                        'displayed_columns' => array(
                            0 => 'shipping_name',
                            1 => 'method_group',
                            2 => 'country_list',
                            3 => 'shipping_class',
                            4 => 'product_category',
                            5 => 'weight',
                            6 => 'item',
                            7 => 'price',
                            8 => 'cost_based_on',
                            9 => 'fee',
                            10 => 'cost',
                            11 => 'weigh_rounding',
                        ) ,
                        'calculation_mode' => 'per_order_max_cost',
                        'tax_status' => 'none',
                        'remove_free_text' => 'no',
                        'debug' => 'no',
                    );
                    update_option( 'woocommerce_wf_woocommerce_shipping_pro_settings', $new_settings);
                }
            }
        }
    }

    if (!function_exists('wf_get_rate_matrix_default')){
        function wf_get_rate_matrix_default(){
            if (function_exists('wf_pre_loaded_data')) 
              return wf_pre_loaded_data();

            return '';
        }
    }
    
    if (!class_exists('wf_woocommerce_shipping_pro_setup')) {
        class wf_woocommerce_shipping_pro_setup {
            public function __construct() {
                if ( is_admin() ) {
                    add_action( 'init', array( $this, 'wf_admin_includes' ) );
                }
                add_filter( 'plugin_action_links_' . wf_plugin_basename(), array( $this, 'plugin_action_links' ) );
                add_action( 'woocommerce_shipping_init', array( $this, 'wf_woocommerce_shipping_pro_init' ) );
                add_filter( 'woocommerce_shipping_methods', array( $this, 'wf_add_woocommerce_shipping_pro_init' ) );
                include_once( 'includes/class-wf-matrices-exporter.php' );          
                
                add_action( 'init', array( $this, 'wf_includes' ) );
            }

            public function wf_includes(){
                if ( ! class_exists( 'wf_order' ) ) {
                    include_once 'includes/class-wf-legacy.php';
                }
            }

            public function wf_admin_includes() {
                if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
                    include( 'includes/class-wf-admin-importers.php' );
                }
            }

            public function wf_woocommerce_shipping_pro_init() {
                if ( ! class_exists( 'wf_woocommerce_shipping_pro_method' ) ) {
                    include_once( 'core/woocommerce-shipping-pro-core.php' );
                }
            }

            public function wf_add_woocommerce_shipping_pro_init( $methods ){
                $methods[] = 'wf_woocommerce_shipping_pro_method';
                return $methods;
            }

            public function plugin_action_links( $links ) {
                $plugin_links = array(
                    '<a href="' . admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_woocommerce_shipping_pro_method' ) . '">' . __( 'Settings', 'wf_woocommerce_shipping_pro' ) . '</a>',
                    '<a href="https://www.xadapter.com/category/product/woocommerce-table-rate-shipping-pro-plugin/" target="_blank">' . __('Documentation', 'wf_woocommerce_shipping_pro') . '</a>',
					'<a href="https://www.xadapter.com/online-support/" target="_blank">' . __('Support', 'wf_woocommerce_shipping_pro') . '</a>'
                );
                return array_merge( $plugin_links, $links );
            }				
        }
        new wf_woocommerce_shipping_pro_setup();
    }