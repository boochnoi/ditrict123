<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ignition_Updater_Licenses_Table extends WP_List_Table {
	public $per_page = 100;

	public $data;

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			 'singular'  => 'license',     //singular name of the listed records
			  'plural'    => 'licenses',   //plural name of the listed records
			  'ajax'      => false        //does this table support ajax?
		) );
		$status = 'all';

		$page = $this->get_pagenum();

		$this->data = array();

		// Make sure this file is loaded, so we have access to plugins_api(), etc.
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		parent::__construct( $args );
	} // End __construct()

	/**
	 * Text to display if no items are present.
	 * @since  1.0.0
	 * @return  void
	 */
	public function no_items () {
		echo wpautop( __( 'No products found.', 'ignition-updater' ) );
	} // End no_items(0)

	/**
	 * The content of each column.
	 * @param  array $item         The current item in the list.
	 * @param  string $column_name The key of the current column.
	 * @since  1.0.0
	 * @return string              Output for the current column.
	 */
	public function column_default ( $item, $column_name ) {
		switch( $column_name ) {
			case 'product':
			case 'product_status':
			case 'product_version':
			case 'license_expiry':
				return $item[$column_name];
			break;
		}
	} // End column_default()

	/**
	 * Retrieve an array of sortable columns.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_sortable_columns () {
	  return array();
	} // End get_sortable_columns()

	/**
	 * Retrieve an array of columns for the list table.
	 * @since  1.0.0
	 * @return array Key => Value pairs.
	 */
	public function get_columns () {
		$columns = array(
			'product_name' => __( 'Product', 'ignition-updater' ),
			'product_version' => __( 'Version', 'ignition-updater' ),
			'product_status' => __( 'Key', 'ignition-updater' ),
			'product_expiry' => __( 'Renew By', 'ignition-updater' )
		);
		 return $columns;
	} // End get_columns()

	/**
	 * Content for the "product_name" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_name ( $item ) {
		return wpautop( '<strong>' . $item['product_name'] . '</strong>' );
	} // End column_product_name()

	/**
	 * Content for the "product_version" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_version ( $item ) {
		if ( isset( $item['latest_version'], $item['product_version'] ) && version_compare( $item['product_version'], $item['latest_version'], '<' ) ) {
			$version_text = '<strong>' . $item['product_version'] . '<span class="update-available"> - ' . sprintf( __( 'version %1$s available', 'ignition-updater' ), esc_html( $item['latest_version'] ) ) .  '</span></strong>' . "\n";
		} else {
			$version_text = '<strong class="latest-version">' . $item['product_version'] . '</strong>' . "\n";
		}

		return wpautop( $version_text );
	} // End column_product_version()

	/**
	 * Content for the "status" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_status ( $item ) {
		$response = '';
		if ( 'active' == $item['product_status'] ) {
			$deactivate_url = wp_nonce_url( add_query_arg( 'action', 'deactivate-product', add_query_arg( 'filepath', $item['product_file_path'], add_query_arg( 'page', 'ignition-helper', network_admin_url( 'index.php' ) ) ) ), 'bulk-licenses' );
			$response = '<a href="' . esc_url( $deactivate_url ) . '">' . __( 'Deactivate', 'ignition-updater' ) . '</a>' . "\n";
		} else {
			$response .= '<input name="license_keys[' . esc_attr( $item['product_file_path'] ) . ']" id="license_keys-' . esc_attr( $item['product_file_path'] ) . '" type="text" value="" size="37" aria-required="true" placeholder="' . esc_attr__( 'Place your license key here', 'ignition-updater' ) . '" />' . "\n";
		}

		return $response;
	} // End column_status()

	public function column_product_expiry ( $item ) {
		if ( '-' != $item['license_expiry'] && 'Not activated' != $item['license_expiry'] ) {
			$renew_link = add_query_arg( array( 'utm_source' => 'product', 'utm_medium' => 'upsell', 'utm_campaign' => 'licenserenewal' ), 'https://ignitewoo.com/my-account/' );
			$date = new DateTime( $item['license_expiry'] );
			$date_string = $date->format( get_option( 'date_format' ) );

			return $date_string;
		}
		return esc_html( $item['license_expiry'] );
	}

	/**
	 * Retrieve an array of possible bulk actions.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_bulk_actions () {
	  $actions = array();
	  return $actions;
	} // End get_bulk_actions()

	/**
	 * Prepare an array of items to be listed.
	 * @since  1.0.0
	 * @return array Prepared items.
	 */
	public function prepare_items () {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = count( $this->data );

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $total_items                   //WE have to determine how many items to show on a page
		) );
	  	$this->items = $this->data;
	} // End prepare_items()
} // End Class
?>