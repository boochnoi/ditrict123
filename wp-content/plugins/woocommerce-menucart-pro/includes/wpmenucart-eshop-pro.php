<?php
if ( ! class_exists( 'WPMenuCart_eShop_Pro' ) ) {
	class WPMenuCart_eShop_Pro extends WPMenuCart_eShop {     
	
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
			global $wpdb,$blog_id,$eshopoptions,$user_ID;
			$pid = '';
			$check = '';
			$currsymbol=$eshopoptions['currency_symbol'];
			if(isset($_SESSION['eshopcart'.$blog_id])) {
				$eshopcartarray=$_SESSION['eshopcart'.$blog_id];
				$eshop_product=get_post_meta( $pid, '_eshop_product',true );
				foreach ($eshopcartarray as $productid => $opt){
					if(is_array($opt)){
						foreach($opt as $qty){
							$check=$check+$qty;
						}
					}
				}
			}
			$get_cart = '';
			if(isset($_SESSION['eshopcart'.$blog_id])) {
				$get_cart = sizeof($_SESSION['eshopcart'.$blog_id]);
			}
			$submenu_items = '';
			
			if ( $get_cart > 0) {
				foreach ( $eshopcartarray as $productid => $opt ) {
					//$_product = $values['data'];
					if ( sizeof($_SESSION['eshopcart'.$blog_id]) > 0 ) {
		 
						$item_thumbnail = get_the_post_thumbnail( $opt['postid'] ); 
						$item_name = get_the_title($opt["postid"]);
						$item_quantity = $opt["qty"];
						//$item_price = $opt['price'];
						$item_price = sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($opt['price'],__('2','eshop')));
					
						// Item permalink if product visible
						$item_permalink = get_permalink($opt['postid']);
		
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
			global $wpdb,$blog_id,$eshopoptions;
	    	$cart_url = get_permalink($eshopoptions['cart']);
			return $cart_url;		
		}
	}
}