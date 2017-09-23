<?php


defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods Admin Reports Class
 *
 * @since 2.0.0
 */
class PW_COST_GOOD_report {


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// add reports to WC
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_reports' ) );

		// clear report transients when orders are updated
		add_action( 'woocommerce_delete_shop_order_transients', array( $this, 'clear_report_transients' ) );
	}


	/**
	 * Adds a 'Profit' tab with associated reports to the WC admin reports area,
	 * as well as inventory valuation reports under the 'Stock' tab
	 *
	 * @since 2.0.0
	 * @param array $core_reports
	 * @return array
	 */
	public function add_reports( $core_reports ) {

		$profit_reports = array(
			'profit' => array(
				'title'   => __( 'Profit', PW_COST_GOOD_TEXTDOMAIN ),
				'reports' => array(
					'profit_by_date'     => array(
						'title'       => __( 'Profit by date', PW_COST_GOOD_TEXTDOMAIN ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
					'profit_by_product'  => array(
						'title'       => __( 'Profit by product', PW_COST_GOOD_TEXTDOMAIN ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
					'profit_by_category' => array(
						'title'       => __( 'Profit by category', PW_COST_GOOD_TEXTDOMAIN ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( $this, 'load_report' )
					),
				),
			),
		);

		$stock_reports = array(
			'product_valuation' => array(
				'title'       => __( 'Product Valuation', PW_COST_GOOD_TEXTDOMAIN ),
				'description' => '',
				'hide_title'  => false,
				'function'    => array( $this, 'load_report' ),
			),
			'total_valuation' => array(
				'title'       => __( 'Total Valuation', PW_COST_GOOD_TEXTDOMAIN ),
				'description' => __( 'Total valuation provides the value of all inventory within your store at both the cost of the good, as well as the total value of inventory at the retail price (regular price, or sale price if set). Stock count must be set to be included in this valuation.', PW_COST_GOOD_TEXTDOMAIN ),
				'hide_title'  => false,
				'function'    => array( $this, 'load_report' ),
			),
		);

		// add Profit reports tab immediately after Orders
		$core_reports = PW_COST_GOOD_ADMIN_HELPER::array_insert_after( $core_reports, 'orders', $profit_reports );

		// add inventory valuation chart
		if ( isset( $core_reports['stock']['reports'] ) ) {
			$core_reports['stock']['reports'] = array_merge( $core_reports['stock']['reports'], $stock_reports );
		}

		return $core_reports;
	}


	/**
	 * Callback to load and output the given report
	 *
	 * @since 2.0.0
	 * @param string $name report name, as defined in the add_reports() array above
	 */
	public function load_report( $name ) {

		$name     = sanitize_title( $name );
		$filename = sprintf( 'PW-cog-admin-report-%s.php', str_replace( '_', '-', $name ) );

		// abstract class first
		require_once( PW_COST_GOODS()->get_plugin_path() . '/includes/admin/reports/abstract-wc-cog-admin-report.php' );

		// then report class
		$report = PW_COST_GOODS()->load_class( "/includes/admin/reports/$filename", 'PW_COST_GOOD_ADMIN_REPORT_' . $name );

		$report->output_report();
	}


	/**
	 * Clear report transients when shop order transients are cleared, e.g. order
	 * update/save, etc. This is also called directly when an order line item cost
	 * is edited manually from the edit order screen.
	 *
	 * @since 2.0.0
	 */
	public function clear_report_transients() {

		foreach ( array( 'date', 'product', 'category' ) as $report ) {

			delete_transient( "wc_cog_admin_report_profit_by_{$report}" );
		}
	}


}
