<?php
/**
 * Booster for WooCommerce - Module - URL Coupons
 *
 * @version 2.9.1
 * @since   2.9.1
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_URL_Coupons' ) ) :

class WCJ_URL_Coupons extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.9.1
	 * @since   2.9.1
	 */
	function __construct() {

		$this->id         = 'url_coupons';
		$this->short_desc = __( 'URL Coupons', 'woocommerce-jetpack' );
		$this->desc       = __( 'WooCommerce URL coupons.', 'woocommerce-jetpack' );
		$this->link_slug  = 'woocommerce-url-coupons';
		parent::__construct();

		if ( $this->is_enabled() ) {
			add_action( 'wp_loaded', array( $this, 'maybe_apply_url_coupon' ), PHP_INT_MAX );
		}
	}

	/**
	 * is_product_in_cart.
	 *
	 * @version 2.9.1
	 * @since   2.9.1
	 */
	function is_product_in_cart( $product_id ) {
		if ( 0 != $product_id ) {
			if ( isset( WC()->cart->cart_contents ) && is_array( WC()->cart->cart_contents ) ) {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item_data ) {
					if (
						( isset( $cart_item_data['product_id'] )   && $product_id == $cart_item_data['product_id'] ) ||
						( isset( $cart_item_data['variation_id'] ) && $product_id == $cart_item_data['variation_id'] )
					) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * get_redirect_url.
	 *
	 * @version 2.9.1
	 * @since   2.9.1
	 */
	function get_redirect_url( $arg_key ) {
		switch ( apply_filters( 'booster_get_option', 'no', get_option( 'wcj_url_coupons_redirect', 'no' ) ) ) {
			case 'cart':
				return wc_get_cart_url();
			case 'checkout':
				return wc_get_checkout_url();
			case 'custom':
				return get_option( 'wcj_url_coupons_redirect_custom_url', '' );
			default: // 'no'
				return remove_query_arg( $arg_key );
		}
	}

	/**
	 * maybe_add_products_to_cart.
	 *
	 * @version 2.9.1
	 * @since   2.9.1
	 * @todo    (maybe) check if coupon is valid
	 */
	function maybe_add_products_to_cart( $coupon_code ) {
		if ( 'no' === apply_filters( 'booster_get_option', 'no', get_option( 'wcj_url_coupons_fixed_product_discount_add_products', 'no' ) ) ) {
			return false;
		}
		// Coupons are globally disabled.
		if ( ! wc_coupons_enabled() ) {
			return false;
		}
		// Sanitize coupon code.
		$coupon_code = wc_format_coupon_code( $coupon_code );
		// Get the coupon.
		$the_coupon = new WC_Coupon( $coupon_code );
		if ( 'fixed_product' === $the_coupon->get_discount_type() ) {
			$product_ids = $the_coupon->get_product_ids();
			if ( ! empty( $product_ids ) ) {
				foreach ( $product_ids as $product_id ) {
					if ( ! $this->is_product_in_cart( $product_id ) ) {
						WC()->cart->add_to_cart( $product_id );
					}
				}
			}
		}
	}

	/**
	 * maybe_apply_url_coupon.
	 *
	 * @version 2.9.1
	 * @since   2.7.0
	 * @todo    (maybe) options to add products to cart with query arg
	 * @todo    (maybe) if ( ! WC()->cart->has_discount( $coupon_code ) ) {}
	 */
	function maybe_apply_url_coupon() {
		$arg_key = get_option( 'wcj_url_coupons_key', 'wcj_apply_coupon' );
		if ( isset( $_GET[ $arg_key ] ) && '' != $_GET[ $arg_key ] ) {
			$coupon_code = sanitize_text_field( $_GET[ $arg_key ] );
			$this->maybe_add_products_to_cart( $coupon_code );
			WC()->cart->add_discount( $coupon_code );
			wp_safe_redirect( $this->get_redirect_url( $arg_key ) );
			exit;
		}
	}

}

endif;

return new WCJ_URL_Coupons();
