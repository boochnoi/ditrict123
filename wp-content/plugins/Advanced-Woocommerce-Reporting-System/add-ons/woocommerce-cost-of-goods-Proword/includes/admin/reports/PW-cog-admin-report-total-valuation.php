<?php


defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods Total Valuation Admin Report Class
 *
 * Handles generating and rendering the Total Valuation report
 *
 * @since 2.1.0
 */
class PW_COST_GOOD_TOTAL_VALUATION {


	/**
	 * Render the totals
	 *
	 * @since 2.1.0
	 */
	public function output_report() {

		$valuation = $this->get_valuation();

		if ( empty( $valuation ) ) {
			return;
		}

		?>
		<style type="text/css">
			.wc-cogs-total-valuation div { background-color:#323742; width: 200px; max-width: 48%; padding: 10px; border-radius: 3px; color: #FFF; float: left; margin: 5px; }
			.wc-cogs-total-valuation span.title { font-size: 90%; letter-spacing: .15em; text-transform: uppercase; }
			.wc-cogs-total-valuation h3 { color: inherit; font-size: 22px; }
		</style>
		<div id="poststuff" class="woocommerce-reports-wide wc-cogs-total-valuation">
			<div>
				<span class="title"><?php esc_html_e( 'at cost', PW_COST_GOOD_TEXTDOMAIN ); ?></span>
				<h3 class="amount"><?php echo wc_price( $valuation->cost ); ?></h3>
			</div>
			<div>
				<span class="title"><?php esc_html_e( 'at retail', PW_COST_GOOD_TEXTDOMAIN ); ?></span>
				<h3 class="amount"><?php echo wc_price( $valuation->retail ); ?></h3>
			</div>
		</div>
		<?php
	}


	/**
	 * Get the inventory valuation totals a standard class, with properties
	 * 'cost' and 'retail'
	 *
	 * @since 2.1.0
	 * @return stdClass
	 */
	public function get_valuation() {
		global $wpdb;

		return $wpdb->get_row( "
			SELECT sum(stock.meta_value * cost.meta_value) AS cost, sum(stock.meta_value * price.meta_value) AS retail
			FROM {$wpdb->posts} AS posts
				INNER JOIN {$wpdb->postmeta} AS stock ON posts.ID = stock.post_id
				INNER JOIN {$wpdb->postmeta} AS price ON posts.ID = price.post_id
				INNER JOIN {$wpdb->postmeta} AS cost ON posts.ID = cost.post_id
			WHERE posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND stock.meta_key = '_stock' AND CAST(stock.meta_value AS SIGNED) > 0
			AND cost.meta_key = '_PW_COST_GOOD_FIELD' AND CAST(cost.meta_value AS SIGNED) > 0
			AND price.meta_key = '_price' AND CAST(price.meta_value AS SIGNED) > 0
		" );
	}


}
