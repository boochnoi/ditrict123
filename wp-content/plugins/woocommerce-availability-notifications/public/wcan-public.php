<?php
/**
 * Public
 * 
 * @package 	WooCommerce Availability Notifications
 * @subpackage 	woocommerce-availability-notiications/public
 * @since 		1.0.3
 * @author 		ThemePlugger <themeplugger@gmail.com>
 * @link 		http://themeplugger.com
 * @license 	GNU General Public License, version 3
 * @copyright 	2014 ThemePlugger
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WC_Availability_Notifications_Public' ) ) :

/**
 * WC_Availability_Notifications_Public
 * 
 * @since 1.0.3
 */
class WC_Availability_Notifications_Public {
	
	/**
	 * Constructor
	 * 
	 * @since 1.0.3
	 */
	public function __construct() {
		$this->setup_variables();
		$this->setup_includes();
		$this->setup_hooks();
	}


	/** Setup methods **************************************************/
	
	/**
	 * Setup the global variables
	 * 
	 * @since 1.0.3
	 */
	protected function setup_variables() {
		$parent = wc_availability_notifications();
		
		$this->public_dir = $parent->public_dir;
		$this->public_url = $parent->public_url;
	}
	
	/**
	 * Setup the included files
	 * 
	 * @since 1.0.3
	 */
	protected function setup_includes() {
		require_once($this->public_dir . 'wcan-compatibility.php');
	}
	
	/**
	 * Setup action and filter hooks
	 * 
	 * @since 1.0.3
	 */
	protected function setup_hooks() {
		
		add_filter('woocommerce_get_availability', array($this, 'availability_notification'), 10, 2);
		add_filter('woocommerce_stock_html', array($this, 'availability_notification_html'), 10, 2);
		
		add_filter('woocommerce_sale_flash', array($this, 'woocommerce_sale_flash'), 10, 3);

		add_filter('wp_footer', array($this, 'availability_notification_styles'));
		add_filter('wp_footer', array($this, 'availability_notification_scripts'));
		
		// $this->setup_enqueue_scripts();
		// $this->setup_enqueue_styles();
	}
	
	/** Hook methods **************************************************/
	
	/**
	 * Enqueue scripts
	 * 
	 * @since 1.0.3
	 */
	public function enqueue_scripts() {
		// Do nothing here
	}
	
	/**
	 * Enqueue styles
	 * 
	 * @since 1.0.3
	 */
	public function enqueue_styles() {
		// Do nothing here
	}
	
	/**
	 * Allow HTML in availability notification HTML
	 * 
	 * @since 1.1.1
	 * 
	 * @param string $html
	 * @param string $availability
	 * @param object $product (Optional for 2.3.13 and below)
	 * @return string
	 */
	public function availability_notification_html($html, $availability, $product = null) {
		return html_entity_decode(wp_specialchars_decode($html));
	}
	
	/**
	 * Availability notification
	 * 
	 * @since 1.0.0
	 * 
	 * @param 	array 	$notification
	 * @param 	object 	$product
	 * @return 	array 	$notification
	 */
	public function availability_notification( $notification, $product ) {
		
		// Check if product id is a variable product id
		if ( isset( $product->variation_id ) ) {
			$product_id = $product->variation_id;
		} 
		// If not, lets assume its a product id 
		else {
			$product_id = $product->id;
		}
		
		$notifications = array(
			'in-stock' 					=> array(
				'availability' 	=> woocommerce_get_availability_notification( 'in-stock', $product_id ),
				'class' 		=> 'in-stock'
			),
			'low-stock' 				=> array(
				'availability' 	=> woocommerce_get_availability_notification( 'low-stock', $product_id ),
				'class' 		=> 'low-stock'
			),
			'out-of-stock' 				=> array(
				'availability' 	=> woocommerce_get_availability_notification( 'out-of-stock', $product_id ),
				'class' 		=> 'out-of-stock'
			),
			'available-on-backorder' 	=> array(
				'availability' 	=> woocommerce_get_availability_notification( 'backorder', $product_id ),
				'class' 		=> 'available-on-backorder'
			)
		);
		
		$display_backorder = get_option( 'woocommerce_availability_display_backorder_if_out_of_stock' );
		$display_backorder = $display_backorder == 'yes' ? true : false;
		
		// Check if managing stock is enabled
		if ( $product->managing_stock() ) {
			if ( $product->is_in_stock() ) {
				if ( $product->get_total_stock() > get_option( 'woocommerce_notify_no_stock_amount' ) ) {

					$format_option = get_option( 'woocommerce_stock_format' );

					switch ( $format_option ) {
						case 'no_amount' :
							$availability = $notifications['in-stock']['availability'];
							$class = 'in-stock';
						break;
						
						case 'low_amount' :
							$low_amount = get_option( 'woocommerce_notify_low_stock_amount' );
							
							if ( $product->get_total_stock() <= $low_amount ) {
								$availability = $notifications['low-stock']['availability'];
								$class = 'low-stock';
							} else {
								$availability = $notifications['in-stock']['availability'];
								$class = 'in-stock';
							}
						break;
						
						default :
							$availability = $notifications['in-stock']['availability'];
							$class = 'in-stock';
						break;
					}

					if ( $format_option != 'low_amount' && $product->backorders_allowed() && $product->backorders_require_notification() ) {
						if ( $display_backorder && $product->get_stock_quantity() > 0 ) {
							$availability = $notifications['in-stock']['availability'];
							$class = 'in-stock';
						} else {
							$availability = $notifications['available-on-backorder']['availability'];
							$class = 'available-on-backorder';
						}
					}

				} else {
					
					if ( $product->backorders_allowed() ) {
						if ( $display_backorder && $product->get_stock_quantity() > 0 ) {
							$availability = $notifications['in-stock']['availability'];
							$class = 'in-stock';
						} elseif ( $product->backorders_require_notification() ) {
							$availability = $notifications['available-on-backorder']['availability'];
							$class = 'available-on-backorder';
						}
					} else {
						$availability = $notifications['out-of-stock']['availability'];
						$class = 'out-of-stock';
					}

				}

			} elseif ( $product->backorders_allowed() ) {
				if ( $display_backorder && $product->get_stock_quantity() > 0 ) {
					$availability = $notifications['in-stock']['availability'];
					$class = 'in-stock';
				} else {
					$availability = $notifications['available-on-backorder']['availability'];
					$class = 'available-on-backorder';
				}
			} else {
				$availability = $notifications['out-of-stock']['availability'];
				$class = 'out-of-stock';
			}
		} elseif ( ! $product->is_in_stock() ) {
			$availability = $notifications['out-of-stock']['availability'];
			$class = 'out-of-stock';
		} /* else {
			$availability = $notifications['in-stock']['availability'];
			$class = 'in-stock';
		} */
		
		if ( isset($availability) && $availability ) {
			$patterns = array(
				'%BACKORDERS_ALLOWED%' 	=> '(can be backordered)',
				'%STOCK_COUNT%' 		=> $product->get_stock_quantity()
			);
			
			$patterns['%STOCK%'] = str_replace( $patterns['%BACKORDERS_ALLOWED%'], '', $notification['availability'] );
			
			foreach ( $patterns as $search => $replace ) {
				if ( strpos( $availability, $search ) !== false ) {
					$availability = str_replace( $search, $replace, $availability );
				}
			}
			
			$notification['availability'] = $availability;
		}
		
		if ( isset($class) && $class ) {
			$notification['class'] .= ' ' . $class;
		}
		
		return $notification;
	}

	/**
	 * Sale flash notification
	 *
	 * @since 1.1.5
	 *
	 * @param string $sale_flash
	 * @param WP_Post $post
	 * @param WC_Product $product
	 * @return string $sale_flash
	 */
	public function woocommerce_sale_flash($sale_flash, $post, $product) {

		if ($notification = woocommerce_get_availability_notification('sale-flash', $product->id)) {
			preg_match("/<span class=\".*?\">(.*?)<\/span>/", $sale_flash, $result);

			if (isset($result) && isset($result[1])) {
				$sale_flash = str_replace($result[1], $notification, $sale_flash);
			}
		}

		return $sale_flash;
	}
	
	/**
	 * Print availability notification styles
	 * 
	 * @since 1.0.0
	 */
	public function availability_notification_styles() { 
		global $wc_availability_notifications; ?>
		
		<style id="<?php printf('%sstyles', str_replace('_', '-', $wc_availability_notifications->prefix)); ?>" type="text/css">
			<?php tp_styles(woocommerce_get_availability_notification_styles()); ?>
		</style>
		
		<?php
	}
	
	/**
	 * Print availability notication scripts
	 * 
	 * @since 1.0.1
	 */
	public function availability_notification_scripts() {
		do_action('woocommerce_availability_notification_scripts');
		
		/**
		 * Action hook
		 * 
		 * @since 1.0.3
		 */
		do_action('wc_availability_notification_scripts');
	}
}


if ( ! function_exists('wc_availability_notifications_public')) :

/**
 * WC_Availability_Notifications_Public
 * 
 * @since 1.0.3
 * 
 * @return object WC_Availability_Notifications_Public
 */
function wc_availability_notifications_public() {
	return new WC_Availability_Notifications_Public();
}

endif;

/**
 * Initialize
 * 
 * @since 1.0.3
 */
wc_availability_notifications()->public = wc_availability_notifications_public();

endif;