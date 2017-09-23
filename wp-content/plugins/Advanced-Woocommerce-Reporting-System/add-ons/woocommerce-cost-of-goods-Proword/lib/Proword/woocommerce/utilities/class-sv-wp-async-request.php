<?php

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_COST_GOOD_ADMIN_REQUEST_ASYNC' ) ) :

abstract class PW_COST_GOOD_ADMIN_REQUEST_ASYNC {


	/** @var string request prefix */
	protected $prefix = 'wp';

	/** @var string request action name */
	protected $action = 'async_request';

	/** @var string request identifier */
	protected $identifier;

	/** @var array request data */
	protected $data = array();


	/**
	 * Initiate a new async request
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->identifier = $this->prefix . '_' . $this->action;

		add_action( 'wp_ajax_' . $this->identifier,        array( $this, 'maybe_handle' ) );
		add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
	}


	/**
	 * Set data used during the async request
	 *
	 * @since 4.4.0
	 * @param array $data
	 * @return \PW_COST_GOOD_ADMIN_REQUEST_ASYNC
	 */
	public function set_data( $data ) {
		$this->data = $data;

		return $this;
	}


	/**
	 * Dispatch the async request
	 *
	 * @since 4.4.0
	 * @return array|WP_Error
	 */
	public function dispatch() {

		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_post_args();

		return wp_safe_remote_post( esc_url_raw( $url ), $args );
	}


	/**
	 * Get query args
	 *
	 * @since 4.4.0
	 * @return array
	 */
	protected function get_query_args() {

		if ( property_exists( $this, 'query_args' ) ) {
			return $this->query_args;
		}

		return array(
			'action' => $this->identifier,
			'nonce'  => wp_create_nonce( $this->identifier ),
		);
	}


	/**
	 * Get query URL
	 *
	 * @since 4.4.0
	 * @return string
	 */
	protected function get_query_url() {

		if ( property_exists( $this, 'query_url' ) ) {
			return $this->query_url;
		}

		return admin_url( 'admin-ajax.php' );
	}


	/**
	 * Get post args
	 *
	 * @since 4.4.0
	 * @return array
	 */
	protected function get_post_args() {

		if ( property_exists( $this, 'post_args' ) ) {
			return $this->post_args;
		}

		return array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'body'      => $this->data,
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);
	}


	/**
	 * Maybe handle
	 *
	 * Check for correct nonce and pass to handler.
	 * @since 4.4.0
	 */
	public function maybe_handle() {
		check_ajax_referer( $this->identifier, 'nonce' );

		$this->handle();

		wp_die();
	}


	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 *
	 * @since 4.4.0
	 */
	abstract protected function handle();


}

endif;
