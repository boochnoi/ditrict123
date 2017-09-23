<?php


defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_COST_GOOD_ADMIN_REQUEST_XML' ) ) :

/**
 * Base XML API request class.
 *
 * @since 4.3.0
 */
abstract class PW_COST_GOOD_ADMIN_REQUEST_XML implements PW_COST_GOOD_ADMIN_API_REQUEST {


	/** @var array request data */
	protected $request_data;

	/** @var string root element for XML */
	protected $root_element;

	/** @var \XMLWriter $xml object */
	protected $xml;

	/** @var string complete request XML */
	protected $request_xml;


	/**
	 * Get the method for this request.
	 *
	 * @since 4.3.0
	 */
	public function get_method() { }


	/**
	 * Get the path for this request.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	public function get_path() {
		return '';
	}


	/**
	 * Convert the request data into XML.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function to_xml() {

		if ( ! empty( $this->request_xml ) ) {
			return $this->request_xml;
		}

		$this->xml = new XMLWriter();

		// Create XML document in memory
		$this->xml->openMemory();

		// Set XML version & encoding
		$this->xml->startDocument( '1.0', 'UTF-8' );

		$request_data = $this->get_request_data();

		PW_COST_GOOD_ADMIN_HELPER::array_to_xml( $this->xml, $this->get_root_element(), $request_data[ $this->get_root_element() ] );

		$this->xml->endDocument();

		return $this->request_xml = $this->xml->outputMemory();
	}


	/**
	 * Return the request data to be converted to XML
	 *
	 * @since 4.3.0
	 * @return array
	 */
	public function get_request_data() {

		return $this->request_data;
	}


	/**
	 * Get the string representation of this request
	 *
	 * @since 4.3.0
	 * @see PW_COST_GOOD_ADMIN_API_REQUEST::to_string()
	 * @return string
	 */
	public function to_string() {

		return $this->to_xml();
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

		return $this->prettify_xml( $this->to_string() );
	}


	/**
	 * Helper method for making XML pretty, suitable for logging or rendering
	 *
	 * @since 4.3.0
	 * @param string $xml_string ugly XML string
	 * @return string
	 */
	public function prettify_xml( $xml_string ) {

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $xml_string ) ) {
			$dom->formatOutput = true;
			$xml_string = $dom->saveXML();
		}

		return $xml_string;
	}


	/**
	 * Concrete classes must implement this method to return the root element
	 * for the XML document
	 *
	 * @since 4.3.0
	 * @return string
	 */
	abstract protected function get_root_element();


}

endif; // class exists check
