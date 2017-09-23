<?php


defined( 'ABSPATH' ) or exit;

/**
 * Cost of Goods Admin Products Class
 *
 * Handles various modifications to the products list table and edit product screen
 *
 * @since 2.0.0
 */
class PW_COST_GOOD_ADMIN_PRODUCTS {


	/**
	 * Bootstrap class
	 */
	public function __construct() {

		$this->init_hooks();
	}


	/**
	 * Initialize hooks
	 *
	 * @since 2.0.0
	 */
	protected function init_hooks() {

		// add cost field to simple products under the 'General' tab
		add_action( 'woocommerce_product_options_pricing', array( $this, 'add_cost_field_to_simple_product' ) );

		// add cost field to variable products under the 'General' tab
		add_action( 'woocommerce_product_options_sku', array( $this, 'add_cost_field_to_variable_product' ) );

		// save the cost field for simple products
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_simple_product_cost' ), 10, 2 );

		// adds the product variation 'Cost' bulk edit action
		add_action( 'woocommerce_variable_product_bulk_edit_actions', array( $this, 'add_variable_product_bulk_edit_cost_action' ) );

		// save variation cost for bulk edit action
		add_action( 'woocommerce_bulk_edit_variations_default', array( $this, 'variation_bulk_action_variable_cost' ), 10, 4 );

		// add cost field to variable products under the 'Variations' tab after the shipping class select
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_cost_field_to_product_variation' ), 15, 3 );

		// save the cost field for variable products
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_product_cost' ) );

		// save the default cost, cost/min/max costs for variable products
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_product_cost' ), 15 );
		add_action( 'woocommerce_ajax_save_product_variations',  array( $this, 'save_variable_product_cost' ), 15 );

		// add Products list cost bulk edit field
		add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'add_cost_field_bulk_edit' ) );

		// save Products List cost bulk edit field
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_cost_field_bulk_edit' ) );

		// add/save Products list quick edit cost field
		add_action( 'woocommerce_product_quick_edit_end',  array( $this, 'render_quick_edit_cost_field' ) );
		add_action( 'manage_product_posts_custom_column',  array( $this, 'add_quick_edit_inline_values' ) );
		add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_quick_edit_cost_field' ) );

		// Add support for Bookings. Yes, the misspelling here for "woocommmerce" is on purpose ಠ_ಠ
		if ( PW_COST_GOODS()->is_plugin_active( 'woocommmerce-bookings.php' ) ) {

			// add cost field to booking products under the 'General' tab
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_cost_field_to_booking_product' ) );

			// save the cost field for booking products
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_booking_product_cost' ), 10, 2 );
		}

		// Product List Table Hooks

		// Adds a "Cost" column header next to "Price"
		add_filter( 'manage_edit-product_columns', array( $this, 'product_list_table_cost_column_header' ), 11 );

		// Renders the product cost in the product list table
		add_action( 'manage_product_posts_custom_column', array( $this, 'product_list_table_cost_column' ), 11 );

		// Make the "Cost" column display as sortable
		add_filter( 'manage_edit-product_sortable_columns', array( $this, 'product_list_table_cost_column_sortable' ), 11 );

		// Make the "Cost" column sortable
		add_filter( 'request', array( $this, 'product_list_table_cost_column_orderby' ), 11 );
	}


	/**
	 * Add cost field to simple products under the 'General' tab
	 *
	 * @since 1.0
	 */
	public function add_cost_field_to_simple_product() {

		woocommerce_wp_text_input(
			array(
				'id'        => '_PW_COST_GOOD_FIELD',
				'class'     => 'wc_input_price short',
				/* translators: Placeholder: %s - currency symbol */
				'label'     => sprintf( __( 'Cost of Good (%s)', PW_COST_GOOD_TEXTDOMAIN ), get_woocommerce_currency_symbol() ),
				'data_type' => 'price',
				'desc_tip'    => 'true',
				'description' => __( 'Cost of this product when bought or produced.', PW_COST_GOOD_TEXTDOMAIN ) 
			)
		);
	}


	/**
	 * Add cost field to variable products under the 'General' tab
	 *
	 * @since 1.1
	 */
	public function add_cost_field_to_variable_product() {

		woocommerce_wp_text_input(
			array(
				'id'                => '_PW_COST_GOOD_FIELD_VARIABLE',
				'class'             => 'wc_input_price short',
				'wrapper_class'     => 'show_if_variable',
				/* translators: Placeholder: %s - currency symbol */
				'label'             => sprintf( __( 'Cost of Good (%s)', PW_COST_GOOD_TEXTDOMAIN ), get_woocommerce_currency_symbol() ),
				'data_type'         => 'price',
				'desc_tip'          => true,
				'description'       => __( 'Default cost for product variations', PW_COST_GOOD_TEXTDOMAIN ),
			)
		);
	}


	/**
	 * Save cost field for simple product
	 *
	 * @param int $post_id post id
	 * @since 1.0
	 */
	public function save_simple_product_cost( $post_id ) {

		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		// need this check because this action is called *after* the variable product action, meaning the variable product cost would be overridden
		if ( 'variable' !== $product_type ) {
			update_post_meta( $post_id, '_PW_COST_GOOD_FIELD', stripslashes( wc_format_decimal( $_POST['_PW_COST_GOOD_FIELD'] ) ) );
		}

	}


	/**
	 * Renders the 'Cost' bulk edit action on the product admin Variations tab
	 *
	 * @since 1.0
	 */
	public function add_variable_product_bulk_edit_cost_action() {

		$option_label = __( 'Set cost', PW_COST_GOOD_TEXTDOMAIN );

		echo '<option value="_PW_COST_GOOD_FIELD_VAR">' . esc_html( $option_label ) . '</option>';
	}


	/**
	 * Set variation cost for variations via bulk edit
	 *
	 * @since 1.8.0
	 * @param string $bulk_action
	 * @param array $data
	 * @param int $product_id
	 * @param array $variations
	 */
	public function variation_bulk_action_variable_cost( $bulk_action, $data, $product_id, $variations ) {

		if ( empty( $data['value'] ) ) {
			return;
		}

		if ( '_PW_COST_GOOD_FIELD_VAR' !== $bulk_action ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			$this->update_variation_product_cost( $variation_id, wc_clean( $data['value'] ) );
		}
	}


	/**
	 * Add cost field to variable products under the 'Variations' tab after the shipping class dropdown
	 *
	 * @since 1.0
	 * @param int $loop loop counter
	 * @param array $variation_data array of variation data
	 * @param WP_Post $variation product variation post
	 */
	public function add_cost_field_to_product_variation( $loop, $variation_data, $variation ) {

		$default_cost = get_post_meta( $variation->post_parent, '_PW_COST_GOOD_FIELD_VARIABLE', true );
		$cost         = get_post_meta( $variation->ID,          '_PW_COST_GOOD_FIELD', true );

		// if the variation cost is actually the default variable product cost
		if ( 'yes' === get_post_meta( $variation->ID, '_PW_COST_GOOD_FIELD_DEFAULT', true ) ) {
			$cost = '';
		}
		?>
		<div>
			<p class="form-row form-row-first">
				<label><?php
					/* translators: Placeholder: %s - currency symbol */
					printf( esc_html__( 'Cost of Good: (%s)', PW_COST_GOOD_TEXTDOMAIN ), esc_html( get_woocommerce_currency_symbol() ) ); ?></label>
				<input type="text" size="6" name="_PW_COST_GOOD_FIELD_VAR[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $cost ); ?>" class="wc_input_price" placeholder="<?php echo esc_attr( $default_cost ); ?>" />
			</p>
		</div>
		<?php
	}


	/**
	 * Helper method to update cost meta for a variation
	 *
	 * @since 1.8.0
	 * @param $variation_id int The variation ID
	 * @param $cost string The cost
	 */
	public function update_variation_product_cost( $variation_id, $cost ) {

		$parent_id    = null;
		$default_cost = null;

		if ( '' !== $cost ) {
			// setting a non-default cost for this variation
			update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD',         wc_format_decimal( $cost ) );
			update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD_DEFAULT', 'no' );
		} else {
			// get the default cost, if any
			if ( is_null( $default_cost ) ) {
				$parent_id    = wp_get_post_parent_id( $variation_id );
				$default_cost = get_post_meta( $parent_id, '_PW_COST_GOOD_FIELD_VARIABLE', true );
			}

			// and set it if available
			if ( $default_cost ) {
				update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD',         wc_format_decimal( $default_cost ) );
				update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD_DEFAULT', 'yes' );
			} else {
				update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD',         '' );
				update_post_meta( $variation_id, '_PW_COST_GOOD_FIELD_DEFAULT', 'no' );
			}
		}
	}


	/**
	 * Save cost field for the variation product
	 *
	 * @since 1.0
	 * @param $variation_id
	 */
	public function save_variation_product_cost( $variation_id ) {

		// find the index for the given variation ID and save the associated cost
		if ( false !== ( $i = array_search( $variation_id, $_POST['variable_post_id'] ) ) ) {

			$cost = $_POST['_PW_COST_GOOD_FIELD_VAR'][ $i ];

			$this->update_variation_product_cost( $variation_id, $cost );
		}
	}


	/**
	 * Save the overall cost/min/max costs for variable products
	 *
	 * @since 1.1
	 * @param int $post_id
	 */
	public function save_variable_product_cost( $post_id ) {

		// default cost
		if ( isset( $_POST['_PW_COST_GOOD_FIELD_VARIABLE'] ) ) {
			$cost = stripslashes( $_POST['_PW_COST_GOOD_FIELD_VARIABLE'] );
		} else {
			$cost = get_post_meta( $post_id, '_PW_COST_GOOD_FIELD_VARIABLE', true );
		}

		$this->update_variable_product_cost( $post_id, $cost );
	}


	/**
	 * Update the cost meta for a variable product and set its children's costs if needed.
	 *
	 * @since 2.1.1
	 * @param \WC_Product|int $product a variable product or its ID
	 * @param string $cost the new cost
	 */
	protected function update_variable_product_cost( $product, $cost ) {

		$product = wc_get_product( $product );

		if ( ! $product ) {
			return;
		}

		update_post_meta( $product->id, '_PW_COST_GOOD_FIELD_VARIABLE', wc_format_decimal( $cost ) );

		foreach ( $product->get_children() as $child_id ) {

			$child_cost = get_post_meta( $child_id, '_PW_COST_GOOD_FIELD', true );

			$is_default = 'yes' === get_post_meta( $child_id, '_PW_COST_GOOD_FIELD_DEFAULT', true );

			if ( '' === $child_cost || $is_default ) {
				update_post_meta( $child_id, '_PW_COST_GOOD_FIELD', wc_format_decimal( $cost ) );
				update_post_meta( $child_id, '_PW_COST_GOOD_FIELD_DEFAULT', '' !== $cost ? 'yes' : 'no' );
			}
		}

		// get the minimum and maximum costs associated with the product
		list( $min_variation_cost, $max_variation_cost ) = PW_COST_GOOD_PRODUCT::get_variable_product_min_max_costs( $product->id );

		update_post_meta( $product->id, '_PW_COST_GOOD_FIELD',               wc_format_decimal( $min_variation_cost ) );
		update_post_meta( $product->id, '_PW_COST_GOOD_FIELD_VAR_MIN', wc_format_decimal( $min_variation_cost ) );
		update_post_meta( $product->id, '_PW_COST_GOOD_FIELD_VAR_MAX', wc_format_decimal( $max_variation_cost ) );
	}


	/**
	 * Add a cost bulk edit field, this is displayed on the Products list page
	 * when one or more products is selected, and the Edit Bulk Action is applied
	 *
	 * @since 1.0
	 */
	public function add_cost_field_bulk_edit() {
		?>
		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Cost of Good', PW_COST_GOOD_TEXTDOMAIN ); ?></span>
					<span class="input-text-wrap">
						<select class="change_cost_of_good change_to" name="change_cost_of_good">
							<?php
							$options = array(
								''  => __( '— No Change —', PW_COST_GOOD_TEXTDOMAIN ),
								'1' => __( 'Change to:', PW_COST_GOOD_TEXTDOMAIN ),
								'2' => __( 'Increase by (fixed amount or %):', PW_COST_GOOD_TEXTDOMAIN ),
								'3' => __( 'Decrease by (fixed amount or %):', PW_COST_GOOD_TEXTDOMAIN )
							);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
			</label>
			<label class="change-input">
				<input type="text" name="_cost_of_good" class="text cost_of_good" placeholder="<?php esc_attr_e( 'Enter Cost:', PW_COST_GOOD_TEXTDOMAIN ); ?>" value="" />
			</label>
		</div>
		<?php
	}


	/**
	 * Save the cost bulk edit field
	 *
	 * @since 1.0
	 */
	public function save_cost_field_bulk_edit( $product ) {

		if ( ! empty( $_REQUEST['change_cost_of_good'] ) ) {

			$option_selected       = absint( $_REQUEST['change_cost_of_good'] );
			$requested_cost_change = stripslashes( $_REQUEST['_cost_of_good'] );
			$current_cost_value    = PW_COST_GOOD_PRODUCT::get_cost( $product->id );

			switch ( $option_selected ) {

				// change cost to fixed amount
				case 1 :
					$new_cost = $requested_cost_change;
				break;

				// increase cost by fixed amount/percentage
				case 2 :

					if ( false !== strpos( $requested_cost_change, '%' ) ) {
						$percent = str_replace( '%', '', $requested_cost_change ) / 100;
						$new_cost = $current_cost_value + ( $current_cost_value * $percent );
					} else {
						$new_cost = $current_cost_value + $requested_cost_change;
					}

				break;

				// decrease cost by fixed amount/percentage
				case 3 :

					if ( false !== strpos( $requested_cost_change, '%' ) ) {
						$percent = str_replace( '%', '', $requested_cost_change ) / 100;
						$new_cost = $current_cost_value - ( $current_cost_value * $percent );
					} else {
						$new_cost = $current_cost_value - $requested_cost_change;
					}

				break;

			}

			// update to new cost if different than current cost
			if ( isset( $new_cost ) && $new_cost !== $current_cost_value ) {

				if ( $product->is_type( 'variable' ) ) {
					$this->update_variable_product_cost( $product, $new_cost );
				} else {
					update_post_meta( $product->id, '_PW_COST_GOOD_FIELD', wc_format_decimal( $new_cost ) );
				}
			}
		}
	}


	/** Quick Edit support ****************************************************/


	/**
	 * Render the quick edit cost field. Note that the field value is intentionally
	 * empty.
	 *
	 * @since 2.1.0
	 */
	public function render_quick_edit_cost_field() {
		?>
			<br class="clear" />
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Cost', PW_COST_GOOD_TEXTDOMAIN ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_PW_COST_GOOD_FIELD" class="text wc-cog-cost" value="">
				</span>
			</label>
		<?php
	}


	/**
	 * Add markup for the custom product meta values so Quick Edit can fill the inputs.
	 *
	 * @since 2.1.1
	 * @param string $column the current column slug
	 */
	public function add_quick_edit_inline_values( $column ) {
		global $the_product;

		if ( 'name' === $column ) {

			$meta_key = $the_product->is_type( 'variable' ) ? 'wc_cog_cost_variable' : 'wc_cog_cost';

			echo '<div id="wc_cog_inline_' . esc_attr( $the_product->id ) . '" class="hidden">';
				echo '<div class="cost">' . esc_html( $the_product->$meta_key ) . '</div>';
			echo '</div>';
		}
	}


	/**
	 * Save the quick edit cost field, this occurs over Ajax
	 *
	 * @since 2.1.0
	 * @param \WC_Product $product
	 */
	public function save_quick_edit_cost_field( $product ) {

		$cost = isset( $_REQUEST['_PW_COST_GOOD_FIELD'] ) ? $_REQUEST['_PW_COST_GOOD_FIELD'] : '';

		if ( $product->is_type( 'variable' ) ) {
			$this->update_variable_product_cost( $product, $cost );
		} else {
			update_post_meta( $product->id, '_PW_COST_GOOD_FIELD', wc_format_decimal( $cost ) );
		}
	}


	/** Bookings support ******************************************************/


	/**
	 * Add cost field to booking products under the general tab
	 *
	 * @since 1.7.0
	 */
	public function add_cost_field_to_booking_product() {
		global $thepostid;

		$cost = get_post_meta( $thepostid, '_PW_COST_GOOD_FIELD', true );

		woocommerce_wp_text_input(
			array(
				'id'            => '_PW_COST_GOOD_FIELD_BOOKING',
				'name'          => '_PW_COST_GOOD_FIELD_BOOKING',
				'value'         => $cost,
				'class'         => 'wc_input_price short',
				'wrapper_class' => 'show_if_booking',
				/* translators: Placeholder: %s - currency symbol */
				'label'         => sprintf(	__( 'Cost of Good (%s)', PW_COST_GOOD_TEXTDOMAIN ), get_woocommerce_currency_symbol() ),
				'data_type'     => 'price',
			)
		);
	}


	/**
	 * Save cost field for bookable product
	 *
	 * @param int $post_id The post id
	 * @since 1.7.0
	 */
	public function save_booking_product_cost( $post_id ) {

		$product_type = empty( $_POST['product-type'] ) ? 'booking' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		if ( 'booking' === $product_type ) {
			update_post_meta( $post_id, '_PW_COST_GOOD_FIELD', stripslashes( wc_format_decimal( $_POST['_PW_COST_GOOD_FIELD_BOOKING'] ) ) );
		}
	}


	/** Product List table methods ********************************************/


	/**
	 * Adds a "Cost" column header after the core "Price" one, on the Products
	 * list table
	 *
	 * @since 1.1
	 * @param array $existing_columns associative array of column key to name
	 * @return array associative array of column key to name
	 */
	public function product_list_table_cost_column_header( $existing_columns ) {

		$columns = array();

		foreach ( $existing_columns as $key => $value ) {

			$columns[ $key ] = $value;

			// add our cost column after price
			if ( 'price' === $key ) {
				$columns['cost'] = __( 'Cost', PW_COST_GOOD_TEXTDOMAIN );
			}
		}

		return $columns;
	}


	/**
	 * Renders the product cost value in the products list table
	 *
	 * @since 1.1
	 * @param string $column column id
	 */
	public function product_list_table_cost_column( $column ) {
		global $post, $the_product;

		if ( empty( $the_product ) || $the_product->id !== $post->ID ) {
			$the_product = wc_get_product( $post );
		}

		if ( 'cost' === $column ) {

			if ( PW_COST_GOOD_PRODUCT::get_cost_html( $the_product ) ) {
				echo PW_COST_GOOD_PRODUCT::get_cost_html( $the_product );
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		}
	}


	/**
	 * Add the "Cost" column to the list of sortable columns
	 *
	 * @since 1.1
	 * @param array $columns associative array of sortable columns, id to id
	 * @return array sortable columns
	 */
	public function product_list_table_cost_column_sortable( $columns ) {

		$columns['cost'] = 'cost';

		return $columns;
	}


	/**
	 * Add the "Cost" column to the orderby clause if sorting by cost
	 *
	 * @since 1.1
	 * @param array $vars query vars
	 * @return array query vars
	 */
	public function product_list_table_cost_column_orderby( $vars ) {

		if ( isset( $vars['orderby'] ) && 'cost' === $vars['orderby'] ) {

			$vars = array_merge( $vars, array(
				'meta_key' => '_PW_COST_GOOD_FIELD',
				'orderby'  => 'meta_value_num',
			) );
		}

		return $vars;
	}


}
