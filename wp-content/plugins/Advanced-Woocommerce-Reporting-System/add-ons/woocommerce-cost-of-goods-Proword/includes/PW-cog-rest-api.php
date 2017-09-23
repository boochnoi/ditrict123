<?php

defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods REST API Class
 *
 * Adds order and product cost data to the WC REST API responses. Eventually this
 * could be extended to add specific endpoints for profit reports and other
 * functionality.
 *
 * @since 2.0.0
 */
class PW_COST_GOOD_APINTERFACE {


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// include order / product cost information in API responses
		add_action( 'woocommerce_api_order_response',   array( $this, 'insert_order_cost_data' ), 25, 3 );
		add_action( 'woocommerce_api_product_response', array( $this, 'insert_product_cost_data' ), 25, 3 );
	}


	/**
	 * Inserts order & order item cost data into the REST API response. Note that
	 * data is *always* inserted as it's important that API consumers can rely
	 * on a consistent data format regardless of whether the meta exists for
	 * a particular order/line item.
	 *
	 * @since 2.0.0
	 * @param array $data current response data
	 * @param \WC_Order $resource WC_Order instance that the REST API is pulling data from
	 * @return array
	 */
	public function insert_order_cost_data( $data, $resource ) {

		// sanity check
		if ( ! is_array( $data ) || ! $resource instanceof WC_Order ) {
			return $data;
		}

		$order_total_cost = get_post_meta( $resource->id, '_wc_cog_order_total_cost', true );

		// order total cost
		$data['cogs_total_cost'] = floatval( $order_total_cost ) > 0.00 ? wc_format_decimal( $order_total_cost ) : null;

		// add line item costs
		if ( ! empty( $data['line_items'] ) ) {

			foreach ( $data['line_items'] as $index => $item ) {

				// item cost
				$item_cost = wc_get_order_item_meta( $item['id'], '_PW_COST_GOOD_ITEM_COST', true );

				$data['line_items'][ $index ]['cogs_cost'] = floatval( $item_cost ) > 0.00 ? wc_format_decimal( $item_cost, wc_get_price_decimals() ) : null;

				// item total cost
				$item_total_cost = wc_get_order_item_meta( $item['id'], '_PW_COST_GOOD_ITEM_TOTAL_COST', true );

				$data['line_items'][ $index ]['cogs_total_cost'] = floatval( $item_total_cost ) > 0.00 ? wc_format_decimal( $item_total_cost, wc_get_price_decimals() ) : null;
			}
		}

		return $data;
	}


	/**
	 * Inserts the product cost into the REST API response data.
	 *
	 * @since 2.0.0
	 * @param array $data current response data
	 * @param \WC_Product $resource WC_Product instance that the REST API is pulling data from
	 * @return array
	 */
	public function insert_product_cost_data( $data, $resource ) {

		// sanity check
		if ( ! is_array( $data ) || ! $resource instanceof WC_Product ) {
			return $data;
		}

		$product_cost = PW_COST_GOOD_PRODUCT::get_cost( $resource );

		$data['cogs_cost'] = floatval( $product_cost ) > 0.00 ? wc_format_decimal( $product_cost ) : null;

		return $data;
	}


}
