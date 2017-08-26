<?php
if ( ! class_exists( 'WPMenuCart_EDD_Pro' ) ) {
	class WPMenuCart_EDD_Pro extends WPMenuCart_EDD {     
	
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
			global $post;
			$get_cart = edd_get_cart_contents();
			$submenu_items = '';
			
			if (edd_get_cart_contents() > 0) {
				foreach ( $get_cart as $key => $item ) {
					//$_product = $item['data'];
					if (count($get_cart) > 0) {
		
						$item_thumbnail = get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 25,25 ) ) );
						$item_name = get_the_title( $item['id'] );
						$item_quantity = '1';
						$item_price = edd_cart_item_price( $item['id'], $item['options'] );
					
						// Item permalink if product visible
						$item_permalink = esc_url( get_permalink( $item['id'] ) );
		
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
			global $post;
	    	$cart_url = edd_get_checkout_uri();
			return $cart_url;		
		}
	}
}