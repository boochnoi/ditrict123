<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWSBS_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription Class
 *
 * @class   YWSBS_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YWSBS_Subscription' ) ) {

    class YWSBS_Subscription {

        /**
         * Single instance of the class
         *
         * @var \YWSBS_Subscription
         */
        protected static $instance;

        public $post_type_name = 'ywsbs_subscription';

        protected $subscription_meta_data = array(
            'status'                 => 'pending',
            'start_date'             => '',
            'payment_due_date'       => '',
            'expired_date'           => '',
            'cancelled_date'           => '',
            'payed_order_list'       => array(),
            'product_id'             => '',
            'variation_id'           => '',
            'product_name'           => '',
            'quantity'               => '',
            'line_subtotal'          => '',
            'line_total'             => '',
            'line_subtotal_tax'      => '',
            'line_tax'               => '',
            'line_tax_data'          => '',

            'cart_discount'          => '',
            'cart_discount_tax'      => '',

            'order_total'            => '',
            'order_currency'         => '',
            'renew_order'         => 0,

            'prices_include_tax'     => '',

            'payment_method'         => '',
            'payment_method_title'   => '',

            'subscriptions_shippings'          => '',

            'price_is_per'           => '',
            'price_time_option'      => '',
            'max_length'             => '',

            'order_ids'              => array(),
            'order_id'               => '',
            'user_id'                => 0,
            'customer_ip_address'    => '',
            'customer_user_agent'    => '',

            'billing_first_name'     => '',
            'billing_last_name'      => '',
            'billing_company'        => '',
            'billing_address_1'      => '',
            'billing_address_2'      => '',
            'billing_city'           => '',
            'billing_state'          => '',
            'billing_postcode'       => '',
            'billing_country'        => '',
            'billing_email'          => '',
            'billing_phone'          => '',

            'shipping_first_name'    => '',
            'shipping_last_name'     => '',
            'shipping_company'       => '',
            'shipping_address_1'     => '',
            'shipping_address_2'     => '',
            'shipping_city'          => '',
            'shipping_state'         => '',
            'shipping_postcode'      => '',
            'shipping_country'       => '',
        );

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Subscription
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {
            add_action( 'init', array( $this, 'register_post_type' ) );
        }



        /**
         * Register ywsbs_subscription post type
         *
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function register_post_type(  ) {
            $labels = array(
                'name'               => _x( 'Subscriptions', 'Post Type General Name', 'yith-woocommerce-subscription' ),
                'singular_name'      => _x( 'Subscription', 'Post Type Singular Name', 'yith-woocommerce-subscription' ),
                'menu_name'          => __( 'Subscription', 'yith-woocommerce-subscription' ),
                'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-subscription' ),
                'all_items'          => __( 'All Subscriptions', 'yith-woocommerce-subscription' ),
                'view_item'          => __( 'View Subscriptions', 'yith-woocommerce-subscription' ),
                'add_new_item'       => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
                'add_new'            => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
                'edit_item'          => __( 'Edit Subscription', 'yith-woocommerce-subscription' ),
                'update_item'        => __( 'Update Subscription', 'yith-woocommerce-subscription' ),
                'search_items'       => __( 'Search Subscription', 'yith-woocommerce-subscription' ),
                'not_found'          => __( 'Not found', 'yith-woocommerce-subscription' ),
                'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-subscription' ),
            );

            $args = array(
                'label'               => __( 'ywsbs_subscription', 'yith-woocommerce-subscription' ),
                'description'         => __( 'Subscription Description', 'yith-woocommerce-subscription' ),
                'labels'              => $labels,
                'supports'            => array( '' ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'exclude_from_search' => true,
                'capability_type'     => 'post',
                'map_meta_cap'        => true
            );

            register_post_type($this->post_type_name, $args);
        }


	    /**
	     * @param $args
	     *
	     * @return int|WP_Error
	     */
	    public function add_subscription( $args ) {

            $subscription_id = wp_insert_post( array(
                'post_status' => 'publish',
                'post_type'   => $this->post_type_name,
            ) );

            if( $subscription_id ){
                $meta = wp_parse_args( $args, $this->subscription_meta_data );
                $this->update_subscription_meta($subscription_id, $meta);
            }

            return $subscription_id;
        }

        /**
         * Update post meta in subscription
         *
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         * @return void
         */
        function update_subscription_meta( $subscription_id, $meta ){
            foreach( $meta as $key => $value ){
                update_post_meta( $subscription_id, '_'.$key, $value);
            }
        }

	    /**
	     * @param $subscription_id
	     * @param $order_id
	     */
	    public function start_subscription( $subscription_id, $order_id) {

	        $payed = get_post_meta( $subscription_id, '_payed_order_list', true );

	        //do not nothing if this subscription has payed with this order
	        if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
		        return;
	        }

            $start_date = get_post_meta( $subscription_id, '_start_date', true );
            if ( $start_date == '' ) {
                update_post_meta( $subscription_id, '_start_date', date( "Y-m-d H:i:s" ) );
            }

	        $payment_due_date  = get_post_meta( $subscription_id, '_payment_due_date', true );
	        $price_is_per      = get_post_meta( $subscription_id, '_price_is_per', true );
	        $price_time_option = get_post_meta( $subscription_id, '_price_time_option', true );

            if ( $payment_due_date == '' ) {
                $timestamp = time() + ywsbs_get_timestamp_from_option( $price_is_per, $price_time_option );
                update_post_meta( $subscription_id, '_payment_due_date', date( "Y-m-d H:i:s", $timestamp ) );
            }

            $expired_date = get_post_meta( $subscription_id, '_expired_date', true );
            if ( $expired_date == '' ) {
                $max_length             = get_post_meta( $subscription_id, '_max_length', true );

                $timestamp              = time() + ywsbs_get_timestamp_from_option( $max_length, $price_time_option );
                update_post_meta( $subscription_id, '_expired_date', date( "Y-m-d H:i:s", $timestamp ) );
            }

            update_post_meta( $subscription_id, '_status', 'active');

            $payed[] = $order_id;

            update_post_meta( $subscription_id, '_payed_order_list', $payed);
        }


        /**
         * Update the subscription if a payment is done manually from user
         *
         * order_id is the id of the last order created
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         * @return void
         */
        public function update_subscription( $subscription_id, $order_id ) {

            $payed = get_post_meta( $subscription_id, '_payed_order_list', true );
            //do not nothing if this subscription has payed with this order
            if ( !empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
                return;
            }

            //Change the status to active
            update_post_meta( $subscription_id, '_status', 'active' );

            //Change the next payment_due_date
            $price_is_per      = get_post_meta( $subscription_id, '_price_is_per', true );
            $price_time_option = get_post_meta( $subscription_id, '_price_time_option', true );
            $timestamp         = time() + ywsbs_get_timestamp_from_option( $price_is_per, $price_time_option );
            update_post_meta( $subscription_id, '_payment_due_date', date( "Y-m-d H:i:s", $timestamp ) );

            //update _payed_order_list
            $payed[] = $order_id;
            update_post_meta( $subscription_id, '_payed_order_list', $payed );

            //reset _renew_order
            update_post_meta( $subscription_id, '_renew_order', 0 );

        }


	    /**
	     * @param $subscription_id
	     *
	     * @return array
	     */
	    function get_subscription_meta( $subscription_id ) {
            $subscription_meta = array();
            foreach ( $this->subscription_meta_data as $key => $value ) {
                $subscription_meta[$key] = get_post_meta( $subscription_id, '_' . $key, true );
            }
            return $subscription_meta;
        }

	    /**
	     * @param $subscription_id
	     */
	    function cancel_subscription( $subscription_id ){
            //Change the status to active
            update_post_meta( $subscription_id,  '_status', 'cancelled' );
            update_post_meta( $subscription_id,  '_cancelled_date', date( "Y-m-d H:i:s" ) );

            do_action('ywsbs_subscription_cancelled', $subscription_id);

            //if there's a pending order for this subscription change the status of the order to cancelled
            $order_in_pending = get_post_meta( $subscription_id, '_renew_order', true);
            if( $order_in_pending ){
                $order = wc_get_order( $order_in_pending );
                if( $order ){
                    $order->update_status('failed');
                }
            }

        }

    }




}



/**
 * Unique access to instance of YWSBS_Subscription class
 *
 * @return \YWSBS_Subscription
 */
function YWSBS_Subscription() {
    return YWSBS_Subscription::get_instance();
}
