<?php
if ( ! class_exists( 'WPMenuCart_WooCommerce_Pro' ) ) {
	class WPMenuCart_WooCommerce_Pro extends WPMenuCart_WooCommerce {     
	
		/**
		 * Construct.
		 */
		public function __construct() {
		}
	
		/**
		 * Add Menu Cart to menu
		 * 
		 * @return menu items including cart
		 */
		
		public function submenu_items() {
			global $woocommerce;
			$cart = $woocommerce->cart->get_cart();
			// die('<pre>'.print_r( $cart, true ).'</pre>');
			$submenu_items = '';
			
			if (count($cart) > 0) {
				foreach ( $cart as $cart_item_key => $cart_item ) {
					$_product = $cart_item['data'];
					if ( $_product->exists() && $cart_item['quantity'] > 0 ) {
		
						$item_thumbnail = apply_filters( 'woocommerce_in_cart_product_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$item_name = apply_filters( 'woocommerce_in_cart_product_title', $_product->get_title(), $cart_item, $cart_item_key );
						$item_quantity = esc_attr( $cart_item['quantity'] );

						if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
							// version 2.1 & upwards
							$item_price = apply_filters( 'woocommerce_cart_item_price', $woocommerce->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						} else {
							// pre 2.1
							$item_price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price( $item_price ), $cart_item, $cart_item_key );
						}
				
						// Item permalink if product visible
						if ( version_compare( WOOCOMMERCE_VERSION, '2.0.12', '>=' ) ) {
							// version 2.0.12 & upwards
							if ( $_product->is_visible() )
								$item_permalink = esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $cart_item['product_id'] ) ) );
						} else {
							// pre version 2.0.12
							if ( $_product->is_visible() || ( empty( $_product->variation_id ) && $_product->parent_is_visible() ) )
								$item_permalink = esc_url( get_permalink( apply_filters('woocommerce_in_cart_product_id', $cart_item['product_id'] ) ) );
						}
		
					$submenu_items[] = array(
						'item_thumbnail'	=> $item_thumbnail,
						'item_name'			=> $item_name,
						'item_quantity'		=> $item_quantity,
						'item_price'		=> $item_price,
						'item_permalink'	=> $item_permalink,
					);
					
					}
				}
			} else {
				$submenu_items = '';
			}
	
			return $submenu_items;	
		}

		public function get_cart_url(){
			global $woocommerce;
			$cart_url = $woocommerce->cart->get_cart_url();
			return $cart_url;		
		}
	}
}