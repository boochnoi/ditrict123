<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WF_Admin_Importers' ) ) :

class WF_Admin_Importers {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_importers' ) );
		add_action( 'import_start', array( $this, 'post_importer_compatibility' ) );		
	}

	/**
	 * Add menu items
	 */
	public function register_importers() {
		register_importer( 'shippingpro_rate_matrix_csv', __( 'Shipping Pro Rate Matrix (CSV)', 'wf_woocommerce_shipping_pro' ), __( 'Import <strong>Shipping Pro Rate Matrix</strong> to your store via a csv file.', 'wf_woocommerce_shipping_pro'), array( $this, 'rate_matrix_importer' ) );
	}

	/**
	 * Add menu item
	 */
	public function rate_matrix_importer() {
		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) )
				require $class_wp_importer;
		}

		// includes
		require 'class-wf-rate-matrix-importer.php';

		// Dispatch
		$importer = new WF_Rate_Matrix_Importer();
		$importer->dispatch();
	}	
}

endif;

return new WF_Admin_Importers();
