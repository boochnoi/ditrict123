<?php

defined( 'ABSPATH' ) or exit;

if ( ! interface_exists( 'PW_COST_GOOD_ADMIN_API_REQUEST' ) ) :

/**
 * API Request
 */
interface PW_COST_GOOD_ADMIN_API_REQUEST {

	public function get_method();


	/**
	 * Returns the request path
	 *
	 * @since 4.0.0
	 * @return string the request path, or '' if none
	 */
	public function get_path();


	/**
	 * Returns the string representation of this request
	 *
	 * @since 2.2.0
	 * @return string the request
	 */
	public function to_string();


	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 2.2.0
	 * @return string the request, safe for logging/displaying
	 */
	public function to_string_safe();

}

endif;  // interface exists check
