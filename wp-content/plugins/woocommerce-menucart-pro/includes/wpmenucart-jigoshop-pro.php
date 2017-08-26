<?php
if ( ! class_exists( 'WPMenuCart_Jigoshop_Pro' ) ) {
	class WPMenuCart_Jigoshop_Pro extends WPMenuCart_Jigoshop {     
	
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
			$get_cart = jigoshop_cart::get_cart();
			$submenu_items = '';
			
			
			//see jigoshop/widgets/cart.php
			if (count($get_cart) > 0) {
				foreach ( $get_cart as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->exists() && $values['quantity'] > 0 ) {

						$item_thumbnail = (has_post_thumbnail( $_product->id ) ) ? get_the_post_thumbnail( $_product->id, 'shop_tiny' ) : jigoshop_get_image_placeholder( 'shop_tiny' );
						$item_name = $_product->get_title();

						// Not used: Displays variations and cart item meta
						$item_meta = jigoshop_cart::get_item_data($values);
		
						$item_quantity = esc_attr( $values['quantity'] );
						$item_price = $_product->get_price_html();
					
						// Item permalink
						$item_permalink = esc_attr( get_permalink( $_product->id ) );
		
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
	    	$cart_url = jigoshop_cart::get_cart_url();
			return $cart_url;		
		}
	}
}