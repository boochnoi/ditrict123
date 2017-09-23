<?php

defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods Product Class
 *
 * Product utility class
 *
 * @since 1.1
 */
class PW_COST_GOOD_PRODUCT {


	/** API methods ******************************************************/


	/**
	 * Returns the product cost, if any
	 *
	 * @since 1.1
	 * @param WC_Product|int $product the product or product id
	 * @return float|string product cost if configured, the empty string otherwise
	 */
	public static function get_cost( $product ) {

		// get the product object
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		// bail for deleted products
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		// get the product id
		$product_id = PW_COST_GOOD_ASSIST_COMPATIBLE::product_get_id( $product );

		// get the product cost
		if ( $product->is_type( 'variable' ) ) {
			$cost = get_post_meta( $product_id, '_PW_COST_GOOD_FIELD_VARIABLE', true );
		} else {
			$cost = get_post_meta( $product_id, '_PW_COST_GOOD_FIELD', true );
		}

		// if no cost set for product variation, check if a default cost exists for the parent variable product
		if ( '' === $cost && $product->is_type( 'variation' ) ) {
			$cost = get_post_meta( $product->id, '_PW_COST_GOOD_FIELD_VARIABLE', true );
		}

		/**
		 * Filters the prdouct cost
		 *
		 * @since 2.2.3
		 * @param float|string product cost if configured, the empty string otherwise
		 * @param WC_Product the product
		 */
		return apply_filters( 'wc_cost_of_goods_product_cost', $cost, $product );
	}


	/**
	 * Returns the minimum/maximum costs associated with the child variations
	 * of $product
	 *
	 * @since 1.1
	 * @param WC_Product_Variable|int $product the variable product
	 * @return array containing the minimum and maximum costs associated with $product
	 */
	public static function get_variable_product_min_max_costs( $product ) {

		// get the product id
		$product_id = is_object( $product ) ? $product->id : $product;

		// get all child variations
		$children = get_posts( array(
			'post_parent'    => $product_id,
			'posts_per_page' => -1,
			'post_type'      => 'product_variation',
			'fields'         => 'ids',
			'post_status'    => 'publish',
		) );

		// determine the minimum and maximum child costs
		$min_variation_cost = '';
		$max_variation_cost = '';

		if ( $children ) {

			foreach ( $children as $child_product_id ) {

				$child_cost = self::get_cost( $child_product_id );

				if ( '' === $child_cost ) {
					continue;
				}

				$min_variation_cost = '' === $min_variation_cost ? $child_cost : min( $min_variation_cost, $child_cost );
				$max_variation_cost = '' === $max_variation_cost ? $child_cost : max( $max_variation_cost, $child_cost );
			}
		}

		return array( $min_variation_cost, $max_variation_cost );
	}


	/**
	 * Returns the product cost html, if any
	 *
	 * @since 1.1
	 * @param WC_Product|int $product the product or product id
	 * @return string product cost markup
	 */
	public static function get_cost_html( $product ) {

		$cost = '';

		// get the product
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		if ( $product->is_type( 'variable' ) ) {

			// get the minimum and maximum costs associated with the product
			list( $min_variation_cost, $max_variation_cost ) = self::get_variable_product_min_max_costs( $product );

			if ( '' === $min_variation_cost ) {

				$cost = apply_filters( 'wc_cost_of_goods_variable_empty_cost_html', '', $product );

			} else {

				if ( $min_variation_cost !== $max_variation_cost ) {
					$cost .= $product->get_price_html_from_text();
				}

				$cost .= wc_price( $min_variation_cost );
				$cost = apply_filters( 'wc_cost_of_goods_variable_cost_html', $cost, $product );

			}

		} else {

			// simple product
			$cost = self::get_cost( $product );

			if ( '' === $cost ) {

				$cost = apply_filters( 'wc_cost_of_goods_empty_cost_html', '', $product );

			} else {

				$cost = wc_price( $cost );
				$cost = apply_filters( 'wc_cost_of_goods_cost_html', $cost, $product );

			}

		}

		return apply_filters( 'wc_cost_of_goods_get_cost_html', $cost, $product );
	}


} // end \PW_COST_GOOD_PRODUCT class
