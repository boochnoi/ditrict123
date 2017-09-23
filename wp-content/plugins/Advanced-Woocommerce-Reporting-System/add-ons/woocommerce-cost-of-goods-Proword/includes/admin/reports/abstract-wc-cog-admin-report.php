<?php

defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods Abstract Admin Report Class
 *
 * Enhances the default WC Admin Report base class with some COG goodness
 *
 * @since 2.0.0
 */
abstract class PW_COST_GOOD_ADMIN_REPORT extends WC_Admin_Report {


	/** @var array chart colors */
	protected $chart_colors;

	/** @var stdClass for caching multiple calls to get_report_data() */
	protected $report_data;


	/**
	 * Render the report data, including legend and chart
	 *
	 * @since 2.0.0
	 */
	public function output_report() {

		$current_range = $this->get_current_range();

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		// used in view
		$ranges = array(
			'year'         => __( 'Year', PW_COST_GOOD_TEXTDOMAIN ),
			'last_month'   => __( 'Last Month', PW_COST_GOOD_TEXTDOMAIN ),
			'month'        => __( 'This Month', PW_COST_GOOD_TEXTDOMAIN ),
			'7day'         => __( 'Last 7 Days', PW_COST_GOOD_TEXTDOMAIN )
		);

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
	}


	/**
	 * Render the export CSV button
	 *
	 * @since 2.0.0
	 * @param array $args optional arguments for adjusting the exported CSV
	 */
	public function output_export_button( $args = array() ) {

		$defaults = array(
			'filename'       => sprintf( '%1$s-report-%2$s-%3$s.csv',
					strtolower( str_replace( array( 'PW_COST_GOOD_ADMIN_REPORT_', '_' ), array( '', '-' ), get_class( $this ) ) ),
					$this->get_current_range(), date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ),
			'xaxes'          => __( 'Date', PW_COST_GOOD_TEXTDOMAIN ),
			'exclude_series' => '',
			'groupby'        => $this->chart_groupby,
		);

		$args = wp_parse_args( $args, $defaults );

		?>
		<a
			href="#"
			download="<?php echo esc_attr( $args['filename'] ); ?>"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php echo esc_attr( $args['xaxes'] ); ?>"
			data-exclude_series="<?php echo esc_attr( $args['exclude_series'] ); ?>"
			data-groupby="<?php echo esc_attr( $args['groupby'] ); ?>"
		>
			<?php esc_html_e( 'Export CSV', PW_COST_GOOD_TEXTDOMAIN ); ?>
		</a>
		<?php
	}


	/**
	 * Return the currently selected date range for the report
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_current_range() {

		return ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
	}


	/**
	 * Return true if fees should be excluded from net sales/profit calculations
	 *
	 * Note that taxes on fees are already included in the order tax amount.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_fees() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_gateway_fees' );
	}


	/**
	 * Return true if taxes should be excluded from net sales/profit calculations
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_taxes() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_taxes' );
	}


	/**
	 * Return true if shipping should be excluded from net sales/profit calculations
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_shipping() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_shipping_costs' );
	}


	/**
	 * Helper to format an amount using wc_format_decimal() for both strings/floats
	 * and arrays
	 *
	 * @since 2.0.0
	 * @param string|float|array $amount
	 * @return array|string
	 */
	protected function format_decimal( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}


}
