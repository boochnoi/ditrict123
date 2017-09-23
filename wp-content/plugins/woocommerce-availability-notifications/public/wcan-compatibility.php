<?php
/**
 * Compatibility
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

/**
 * @TODO
 * 
 * @since 1.1.4
 * - In the future version of this plugin, this compatibility blocks will be deprecated and a better implementation in placed.
 */
 
/**
 * Artificially place the availability notification HTML 
 * container which is required from WooCommerce if a theme 
 * doesn't support.
 * 
 * This function was created to fix availability
 * notification for non-WooCommerce HTML standard
 * themes.
 * 
 * @since 1.0.1
 * 
 * @return void
 */
function wc_availability_notification_container() {
	global $product;
	
	// Availability
	$availability = $product->get_availability();
	
	echo '<div id="wc-availability-notification-container" style="display: none;">';
	
		if ( empty( $availability['availability'] ) && empty( $availability['class'] ) ) {
			echo '<div class="single_variation"></div>';
		} else {
			echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
		}
		
	echo '</div>';
}

/**
 * Artificially mimic how WooCommerce show 
 * availability notification for products.
 * 
 * This function was created to fix availability
 * notification for non-WooCommerce HTML standard
 * themes.
 * 
 * @since 1.0.1
 * 
 * @return void
 */
function wc_availability_notification_container_scripts() { 
	global $wc_availability_notifications; ?>
	
	<script id="<?php echo $wc_availability_notifications->prefix( 'scripts', true ); ?>" type="text/javascript">
		jQuery(document).ready(function() {
			if ( jQuery('form.variations_form').length ) {
				jQuery(document).on('change', 'form.variations_form select', function(eventObject) {
					wc_availability_notification();
				});
			} else {
				wc_availability_notification();
			}
		});
		
		function wc_availability_notification() {
			var $stock 		= jQuery('#wc-availability-notification-container .stock');
			var $classes 	= $stock.attr('class');
			var $text 		= $stock.html();
			
			jQuery('#product_info dt:contains(Availablity) + dd').addClass('stock').attr('class', $classes).html($text);
		}
	</script>
	<?php
}

/**
 * Customize styles
 * 
 * @since 1.0.1
 * 
 * @param 	array 	$styles
 * @return 	array 	$styles
 */
function wc_availability_notification_container_styles( $styles ) {
	
	if ( $styles ) {
		foreach ( (array) $styles as $style_id => $style ) {
			switch ( $style_id ) {
				case 'in-stock' :
					$styles[$style_id]['elements'][] = '.woocommerce .product .in-stock';
					break;
					
				case 'low-stock' :
					$styles[$style_id]['elements'][] = '.woocommerce .product .low-stock';
					break;
					
				case 'out-of-stock' :
					$styles[$style_id]['elements'][] = '.woocommerce .product .out-of-stock';
					break;
					
				case 'backorder' :
					$styles[$style_id]['elements'][] = '.woocommerce .product .available-on-backorder';
					break;
			}
		}
	}
	
	/**
	 * Filter
	 * 
	 * @since 1.0.3
	 * 
	 * @param 	array $styles
	 * @return 	array $styles
	 */
	return apply_filters( 'wc_availability_notification_container_styles', $styles );
}

/**
 * Theme compatibility
 * 
 * @since 1.0.1
 */
$current_theme = wp_get_theme();

if ($current_theme && $current_theme->get('Name') == "Shoppica" ) {
	add_action( 'woocommerce_before_add_to_cart_button', 			'wc_availability_notification_container' 			);
	add_action( 'woocommerce_availability_notification_scripts', 	'wc_availability_notification_container_scripts' 	);
	add_filter( 'woocommerce_availability_notification_styles', 	'wc_availability_notification_container_styles' 	);
}