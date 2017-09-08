<?php

register_activation_hook(__FILE__,'appzab_auto_update_status_activate');
function appzab_auto_update_status_activate() {

}

register_deactivation_hook( __FILE__, 'appzab_auto_update_status_deactivation' );
function appzab_auto_update_status_deactivation() {
    wp_clear_scheduled_hook('appzab_call_event');
}

add_filter('cron_schedules','appzab_status_interval');
function appzab_status_interval($interval){
    $interval['appzab_everyminute'] = array('interval' => 1*60, 'display' => 'Once a minutes');
    return $interval;
}


add_action('init','appzab_setup_schedule' );
function appzab_setup_schedule() {
    if(!wp_next_scheduled('appzab_call_event')){
        wp_schedule_event(current_time('timestamp'),'appzab_everyminute','appzab_call_event');
    }
}


add_action( 'appzab_call_event', 'appzab_auto_update_status' );
function appzab_auto_update_status(){

    global $wpdb;
    $_statuses = wc_get_order_statuses();
    foreach($_statuses as $key=>$status){

        if($key=='wc-cancelled' || $key=='wc-refunded' || $key=='wc-failed'){
            continue;
        }

        $_status = ('wc-' == substr($key, 0, 3)) ? substr($key, 3) : $key;
        $ops = get_option('wc_status_'.$_status, array());


        if(isset($ops['woocommerce_woo_advance_order_status_auto_switch']) && sizeof($ops['woocommerce_woo_advance_order_status_auto_switch']['val'])>0)
        {
            $newstatus = (substr(trim($ops['woocommerce_woo_advance_order_status_name']),0,3) == 'wc-') ? substr(trim($ops['woocommerce_woo_advance_order_status_name']),3) : trim($ops['woocommerce_woo_advance_order_status_name']);
            $status_id = 'wc-'.sanitize_title($newstatus);
            $status_id = substr($status_id, 0, 20);
            ///echo '<pre>'; print_r($ops); die;

            $date = date("Y-m-d H:i:s", strtotime( '-' . absint($ops['woocommerce_woo_advance_order_status_auto_switch']['val']) . ' '.strtoupper($ops['woocommerce_woo_advance_order_status_auto_switch']['int']), current_time( 'timestamp' ) ) );

            $orders = $wpdb->get_col($wpdb->prepare("
                                        SELECT posts.ID
                                        FROM {$wpdb->posts} AS posts
                                        WHERE 	posts.post_type='shop_order'
                                        AND 	posts.post_status = '".$status_id."'
                                        AND 	posts.post_date < %s", $date ) );



            if(!empty($orders) && sizeof($orders)>0){
                foreach($orders as $order){
                    $ord = wc_get_order( $order );
                    if($order==$ord->id && $ord->id>0){
                        $ord->update_status(trim($ops['woocommerce_woo_advance_order_status_auto_switch']['sts']));
                        unset($ord);
                    }
                }
            }

        }


    }


}


?>