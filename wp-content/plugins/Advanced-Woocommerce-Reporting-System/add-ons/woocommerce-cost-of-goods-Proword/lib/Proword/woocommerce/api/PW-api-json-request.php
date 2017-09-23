<?php

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_COST_GOOD_ADMIN_API_JSON' ) ) :

/**
 * Base JSON API request class.
 *
 * @since 4.3.0
 */
abstract class PW_COST_GOOD_ADMIN_API_JSON implements PW_COST_GOOD_ADMIN_API_REQUEST {


	/** @var string The request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string The request path */
	protected $path;

	/** @var array The request parameters, if any */
	protected $params = array();


	/**
	 * Get the request method.
	 *
	 * @since 4.3.0
	 * @see PW_COST_GOOD_ADMIN_API_REQUEST::get_method()
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}


	/**
	 * Get the request path.
	 *
	 * @since 4.3.0
	 * @see PW_COST_GOOD_ADMIN_API_REQUEST::get_path()
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 * Get the request parameters.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}


	/** API Helper Methods ******************************************************/


	/**
	 * Get the string representation of this request.
	 *
	 * @since 4.3.0
	 * @see PW_COST_GOOD_ADMIN_API_REQUEST::to_string()
	 * @return string
	 */
	public function to_string() {

		return json_encode( $this->get_params() );
	}


	/**
	 * Get the string representation of this request with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 4.3.0
	 * @see PW_COST_GOOD_ADMIN_API_REQUEST::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		return $this->to_string();
	}


}

endif; // class exists check
