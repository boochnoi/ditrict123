<?php
if ( ! class_exists( 'WPMenuCart_WPEC_Pro' ) ) {
	class WPMenuCart_WPEC_Pro extends WPMenuCart_WPEC {     
	
	    /**
	     * Construct.
	     */
	    public function __construct() {
	    	parent::__construct();
	    }
	
	    /**
	     * Add Menu Cart to menu
		 * 
		 * @return menu items including cart
	     */
		
		public function submenu_items() {
			global $wpsc_cart, $options;
			$get_cart = wpsc_cart_item_count();
			$submenu_items = '';
			
			
			//see jigoshop/widgets/cart.php
			if (count($get_cart) > 0) {
				//foreach ( $get_cart as $cart_item_key => $values ) {
				while (wpsc_have_cart_items()): wpsc_the_cart_item();
				
					//$_product = $values['data'];
					if ( wpsc_cart_item_count() > 0 ) {
						global $wpsc_cart, $options;
						$item_thumbnail = '<img src=' .wpsc_cart_item_image(). '>';
						$item_name = wpsc_cart_item_name();
						$item_quantity = wpsc_cart_item_quantity();
						$item_price = wpsc_cart_item_price();
					
						// Item permalink
						$item_permalink = wpsc_cart_item_url();
		
					$submenu_items[] = array(
						'item_thumbnail'	=> $item_thumbnail,
						'item_name'			=> $item_name,
						'item_quantity'		=> $item_quantity,
						'item_price'		=> $item_price,
						'item_permalink'	=> $item_permalink,
					);
					
					}
				//}
				endwhile;
			} else {
				$submenu_items = '';
			}
	
			return $submenu_items;	
		}

		public function get_cart_url(){
			global $wpsc_cart, $options;
	    	$cart_url = esc_url( get_option( 'shopping_cart_url' ) );
			return $cart_url;		
		}
	}
}