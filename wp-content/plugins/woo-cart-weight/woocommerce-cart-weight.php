<?php
/**
  Plugin Name: WooCommerce Cart Weight
  Plugin URI:
  Description: Displays total order weight in cart.
  Version: 1.0
  Author: Inspire Labs
  Author URI: http://www.inspirelabs.pl/
  Text Domain: woocommerce-cart-weight
  Domain Path: /lang/
  Tested up to: 4.2.4

  Copyright 2015 Inspire Labs sp. z o.o.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

/**
 * Load Text Domain
 */
function wcw_plugin_load_plugin_textdomain() {
    load_plugin_textdomain( 'woocommerce-cart-weight', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'wcw_plugin_load_plugin_textdomain' );

/**
 * Add Cart Weight to Cart and Checkout
 */
function wcw_cart() {
	global $woocommerce;

	if ( WC()->cart->needs_shipping() ) : ?>
	    <tr class="total-weight">
            <th><?php _e( 'Total Weight', 'woocommerce-cart-weight' ); ?></th>

            <td><?php echo $woocommerce->cart->cart_contents_weight . ' ' . get_option( 'woocommerce_weight_unit' ); ?></td>
	    </tr>
	<?php endif;
}
add_action( 'woocommerce_cart_totals_after_order_total', 'wcw_cart' );
add_action( 'woocommerce_review_order_after_order_total', 'wcw_cart' );

/**
 * Add Cart Weight to Mini Cart
 */
function wcw_mini_cart() {
	if ( WC()->cart->needs_shipping() ) : ?>
	    <p class="total-weight"><strong><?php echo __( 'Total Weight:', 'woocommerce-cart-weight' ); ?></strong> <?php echo WC()->cart->cart_contents_weight . ' ' . get_option( 'woocommerce_weight_unit' ); ?></p>
	<?php endif;
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'wcw_mini_cart' );
