<?php
require_once dirname(__FILE__) . SB_DS . 'integrations.php';

if( !class_exists('AppZab_Woo_Advance_Order_Status') ):
class AppZab_Woo_Advance_Order_Status
{
	function __construct() 
	{
		$this->addActions();
	}
	protected function addActions()
	{
		/*add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );*/
		add_action( 'init', array( &$this, 'setup_status_actions' ), 1);
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_head_1' ) );
		add_action( 'woocommerce_integrations', array( &$this, 'AppZab_woo_advance_order_status_integration' ), 999 );
		add_action( 'admin_footer', array( &$this, 'bulk_admin_footer' ), 10 );
		add_action( 'load-edit.php', array( &$this, 'order_bulk_action' ) );
		add_action( 'admin_notices', array( &$this, 'order_bulk_admin_notices' ) );
		add_filter( 'woocommerce_reports_order_statuses', array( &$this, 'maybe_add_report_statuses' ) );
        add_filter( 'woocommerce_email_actions',array( &$this,'appzab_add_email_actions' ));
        add_filter( 'woocommerce_order_status_changed',array( &$this,'appzab_update_order' ),100,3);
        add_action( 'woocommerce_order_is_download_permitted', array( &$this, 'appzab_is_download_permitted' ), 10,2 );
        add_filter( 'woocommerce_my_account_my_orders_actions',array( &$this,'appzab_add_my_account_my_orders_actions'),100,2);
        add_action( 'wp_ajax_front-request',array($this,'appzab_front_request'));
        //add_action( 'init',array($this,'appzab_status_auto_update'));
        add_filter( 'woocommerce_resend_order_emails_available',array( &$this,'appzab_resend_order_emails_available'),100,1);

	}

    function appzab_update_order($order_id, $old_status, $new_status){

        $_status = ('wc-' == substr($new_status, 0, 3)) ? substr($new_status, 3) : $new_status;
        $ops = get_option('wc_status_'.$_status, array());

        if(isset($ops['woocommerce_woo_advance_order_status_manage_stock']) && $ops['woocommerce_woo_advance_order_status_manage_stock'] == 'reduce')
        {
            $order = new WC_Order( $order_id );
            $items = $order->get_items();
            if(!empty($items)){
                foreach ( $items as $item ) {
                    if($item['variation_id']>0){
                        if(get_post_meta($item['variation_id'],'_manage_stock',true)=='yes'){
                            $new_stock = get_post_meta($item['variation_id'],'_stock',true)-$item['qty'];
                            update_post_meta($item['variation_id'],'_stock',$new_stock);
                        }
                    }
                    else
                    {
                        if(get_post_meta($item['product_id'],'_manage_stock',true)=='yes'){
                            $new_stock = get_post_meta($item['product_id'],'_stock',true)-$item['qty'];
                            update_post_meta($item['product_id'],'_stock',$new_stock);
                        }
                    }
                }
            }
        }
        elseif(isset($ops['woocommerce_woo_advance_order_status_manage_stock']) && $ops['woocommerce_woo_advance_order_status_manage_stock'] == 'increase'){

            $order = new WC_Order( $order_id );
            $items = $order->get_items();
            if(!empty($items)){
                foreach ( $items as $item ) {
                    if($item['variation_id']>0){
                        if(get_post_meta($item['variation_id'],'_manage_stock',true)=='yes'){
                            $new_stock = get_post_meta($item['variation_id'],'_stock',true)+$item['qty'];
                            update_post_meta($item['variation_id'],'_stock',$new_stock);
                        }
                    }
                    else
                    {
                        if(get_post_meta($item['product_id'],'_manage_stock',true)=='yes'){
                            $new_stock = get_post_meta($item['product_id'],'_stock',true)+$item['qty'];
                            update_post_meta($item['product_id'],'_stock',$new_stock);
                        }
                    }
                }
            }

        }

    }

    function appzab_is_download_permitted($permission,$order){

        $_status = ('wc-' == substr($order->get_status(), 0, 3)) ? substr($order->get_status(), 3) : $order->get_status();
        $ops = get_option('wc_status_'.$_status, array());

        if(isset($ops['woocommerce_woo_advance_order_status_allow_download']) && $ops['woocommerce_woo_advance_order_status_allow_download'] == 'yes')
        {
            return true;
        }

        return $permission;
    }

    function appzab_add_my_account_my_orders_actions($actions, $order){

        $_status = ('wc-' == substr($order->get_status(), 0, 3)) ? substr($order->get_status(), 3) : $order->get_status();
        $ops = get_option('wc_status_'.$_status, array());

        if(isset($ops['woocommerce_woo_advance_order_status_customer_status_trigger']) && sizeof($ops['woocommerce_woo_advance_order_status_customer_status_trigger'])>0)
        {
            foreach($ops['woocommerce_woo_advance_order_status_customer_status_trigger'] as $act){
                $action = ('wc-' == substr($act, 0, 3)) ? substr($act, 3) : $act;
                $actions[$action] = array(
                    'url'  => wp_nonce_url(admin_url('admin-ajax.php?action=front-request&order_id='.$order->id.'&status='.$act),'appzab','chk'),
                    'name' => __($action,'AppZab_woo_advance_order_status')
                );
            }

        }
        return $actions;
    }

    function appzab_front_request(){

        if (!is_user_logged_in()){
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'AppZab_woo_advance_order_status' ), '', array( 'response' => 403 ) );
        }
        if (!isset($_GET['chk']) || !wp_verify_nonce($_GET['chk'], 'appzab')) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'AppZab_woo_advance_order_status' ), '', array( 'response' => 403 ) );
        }

        $order_id = isset( $_GET['order_id'] ) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';
        if ( ! $order_id ) {
            die();
        }

        $order = wc_get_order( $order_id );
        $order->update_status($_GET['status']);
        wp_safe_redirect( wp_get_referer() ? wp_get_referer() :  get_permalink( get_option('woocommerce_myaccount_page_id')));
        die();
    }

    function appzab_status_auto_update(){

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

    function appzab_resend_order_emails_available($mails){

        $_statuses = wc_get_order_statuses();
        if(!empty($_statuses)){

            foreach($_statuses as $key=>$status){
                $mails[]=$key;
            }

            return array_unique($mails);
          }

     }

	/*
	function load_plugin_textdomain() 
	{
		$locale = apply_filters( 'plugin_locale', get_locale(), 'AppZab_woo_advance_order_status' );
		load_textdomain( 'AppZab_woo_advance_order_status', WP_LANG_DIR.'/woocommerce/AppZab_woo_advance_order_status-'.$locale.'.mo' );
		$plugin_rel_path = apply_filters( 'AppZab_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		load_plugin_textdomain( 'AppZab_woo_advance_order_status', false, $plugin_rel_path );
	}
*/


    function appzab_add_email_actions($actions){

        $defaults = array('wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled');
        $statuses = wc_get_order_statuses();
        if(!empty($statuses)){
            foreach($statuses as $key=>$status){
                if(!in_array($key,$defaults)){
                    $status_slug = ('wc-' == substr($key, 0, 3)) ? substr($key, 3) : $key;
                    $actions[]='woocommerce_order_status_'.$status_slug;
                }
            }
        }

        return $actions;
    }
    /* Edit by Chirag Agarwal */
	function AppZab_woo_advance_order_status_integration( $integrations ) 
	{
		$integrations[] = 'AppZab_Woo_Advance_Order_Status_Settings';

		return $integrations;
	}
	function admin_head_1()
	{
		$defaults = array( 'wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled' );
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		
		
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$statuses[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$statuses = wc_get_order_statuses();
		}

		?>
		<style>
		<?php
		foreach( $statuses as $s  => $desc) 
		{
			$status = ('wc-' == substr($s, 0, 3)) ? substr($s, 3) : $s;		
			if ( !in_array( $s, $defaults ) ) 
			{
				
				$meta = get_option( 'wc_status_' . $status );
				if ( !$meta || !isset( $meta['woocommerce_woo_advance_order_status_fcolor'] ) || empty( $meta['woocommerce_woo_advance_order_status_fcolor'] ) )
					$fcolor = '#ffffff';
				else
					$fcolor = $meta['woocommerce_woo_advance_order_status_fcolor'];
					
				if ( !$meta || !isset( $meta['woocommerce_woo_advance_order_status_bcolor'] ) || empty( $meta['woocommerce_woo_advance_order_status_bcolor'] ) )
					$bcolor = '#ff0000';
				else
					$bcolor = $meta['woocommerce_woo_advance_order_status_bcolor'];




				?>
				.widefat .column-order_status mark.<?php echo $status ?> {
					background-color: <?php echo $bcolor ?> !important;
					color: <?php echo $fcolor ?> !important;
					font-size:11px !important;
                                        border-radius:3px;
					font-weight:bold !important;
					width: 100% !important;
					text-indent: 0 !important;
					height: auto !important;
				}
				
				/* Edit by Chirag Agarwal */
/* Remove this comment to show the background color on order rows
.widefat tbody tr.status-wc-<?php echo $status ?> {
background-color: <?php echo $bcolor ?> !important;
color:#fff;
}
.widefat tbody tr.status-wc-<?php echo $status ?> small.meta 
{
color: <?php echo $fcolor ?>;
}
*/
				<?php
			}
		}
		?>
		</style>
		<?php
	}
	function maybe_add_report_statuses( $statuses = array() ) 
	{ 
		global $wpdb;

		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$terms[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$terms = wc_get_order_statuses();
		}
		
		if ( empty( $terms ) )
			return $statuses;
	
		foreach( $terms as $t  => $desc) 
		{ 
			$status = ('wc-' == substr($t, 0, 3)) ? substr($t, 3) : $t;

			$meta = get_option( 'wc_status_' . $t, false );
			
			if ( empty( $meta ) || empty( $meta['woocommerce_woo_advance_order_status_reports'] ) )
				continue; 
				
			if ( '1' == absint( $meta['woocommerce_woo_advance_order_status_reports'] ) ) 
				$statuses[] = $t;
		
		}
		return $statuses;
	}
	/* Edit by Chirag Agarwal */
	function setup_status_actions() 
	{
		
		$defaults = array( 'wc-pending', 'wc-failed', 'wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled' );

		
		//$statuses = array();
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$statuses[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$statuses = wc_get_order_statuses();
		}
		
		foreach( $statuses as $s => $desc)
		{
			$status = ('wc-' == substr($s, 0, 3)) ? substr($s, 3) : $s;
			
			if ( in_array( $s, $defaults ) )
			{
				continue;
			}
			
			add_action( 'woocommerce_order_status_' . $status, array( &$this, 'do_status_change' ), 1, 1 );
		}
	}

	function do_status_change( $order_id ) 
	{
		global $woocommerce;

		
		$order = new WC_Order($order_id);
		$status = ('wc-' == substr($order->status, 0, 3)) ? substr($order->status, 3) : $order->status;
		
		$meta = get_option( 'wc_status_' . $status, false );
		if ( !$meta || !isset( $meta['woocommerce_woo_advance_order_status_email'] ) || '1' != $meta['woocommerce_woo_advance_order_status_email'] || !isset( $meta['woocommerce_woo_advance_order_status_message'] ) || '' == trim( $meta['woocommerce_woo_advance_order_status_message'] ) )
			return;

		if ( isset( $meta['woocommerce_woo_advance_order_status_send_to'] ) && '' != trim( $meta['woocommerce_woo_advance_order_status_send_to'] ) )
			$to = $meta['woocommerce_woo_advance_order_status_send_to'];
		else
			$to = get_post_meta( $order_id, '_billing_email', true );
			
		if ( !$to || '' == trim( $to ) )
			return;

		$message = $meta['woocommerce_woo_advance_order_status_message'];

		$message = $this->apply_code_filters( $message, $order_id );

		if ( isset( $meta['woocommerce_woo_advance_order_status_heading'] ) && '' != trim( $meta['woocommerce_woo_advance_order_status_heading'] ) )
			$heading = $meta['woocommerce_woo_advance_order_status_heading'];
		else
			$heading = '';

		$heading = $this->apply_code_filters( $heading, $order_id );

		if ( isset( $meta['woocommerce_woo_advance_order_status_subject'] ) && '' != trim( $meta['woocommerce_woo_advance_order_status_subject'] ) )
			$subject = $meta['woocommerce_woo_advance_order_status_subject'];
		else
			$subject = '';

		$subject = $this->apply_code_filters( $subject, $order_id );

		// Queue additional mail envelope headers
		$headers = apply_filters('woocommerce_email_headers', '', 'rewards_message');
		$attachments = array(); 	
		                 			
		
		$mailer = $woocommerce->mailer();

		$message = $mailer->wrap_message( $heading, $message );
		
		$mailer->send( $to, $subject, $message, $headers, $attachments );

	}

	function apply_code_filters( $text, $order_id ) 
	{

		if ( '' == trim( $text ) )
			return $text;

		$meta = get_post_custom( $order_id, true );

		$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$siteurl = wp_specialchars_decode( get_option( 'home' ), ENT_QUOTES );

		if ( false !== strpos( $text, '%oid' ) ) { 
			$text = str_replace( '%oid', $order_id, $text );

		}
		/* Edit by Chirag Agarwal */
		if ( false !== strpos( $text, '%bfname' ) ) {

			$fname = $meta['_billing_first_name'][0];

			if ( !$fname || empty( $fname ) )
				$text = str_replace( '%bfname', '', $text );
			else
				$text = str_replace( '%bfname', $fname, $text );
		}

		if ( false !== strpos( $text, '%blname' ) ) {

			$lname = $meta['_billing_last_name'][0];

			if ( !$lname || empty( $lname ) )
				$text = str_replace( '%blname', '', $text );
			else
				$text = str_replace( '%blname', $lname, $text );
		}
		
		if ( false !== strpos( $text, '%sfname' ) ) {

			$fname = $meta['_shipping_first_name'][0];

			if ( !$fname || empty( $fname ) )
				$text = str_replace( '%sfname', '', $text );
			else
				$text = str_replace( '%sfname', $fname, $text );
		}

		if ( false !== strpos( $text, '%slname' ) ) {

			$lname = $meta['_shipping_last_name'][0];

			if ( !$lname || empty( $lname ) )
				$text = str_replace( '%slname', '', $text );
			else
				$text = str_replace( '%slname', $lname, $text );
		}
		
		if ( false !== strpos( $text, '%s' ) )
			$text = str_replace( '%s', $sitename, $text );

		if ( false !== strpos( $text, '%u' ) )
			$text = str_replace( '%u', $siteurl, $text );

		if ( false !== strpos( $text, '%m' ) ) {
			$ma_id = woocommerce_get_page_id( 'myaccount' );
			$ma_url = get_permalink( $ma_id );
			$text = str_replace( '%m', $ma_url, $text );
		}

		return $text;
	}
	
	function bulk_admin_footer() 
	{
		global $post_type;
		
		if ( !isset( $post_type ) )
			return;

		if ( 'shop_order' == $post_type ) 
		{
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$terms[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$terms = wc_get_order_statuses();
		}
			?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php foreach( $terms as $t => $desc) { ?>
				<?php if ( in_array( $t, array( 'wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending', 'wc-failed', 'wc-refunded', 'wc-cancelled' ) ) ) continue; ?>
				jQuery('<option>').val('mark_<?php echo $t ?>').text('<?php _e( 'Mark ' . $t, 'woocommerce' )?>').appendTo("select[name='action']");
				jQuery('<option>').val('mark_<?php echo $t ?>').text('<?php _e( 'Mark ' . $t, 'woocommerce' )?>').appendTo("select[name='action2']");
				<?php } ?>
			});
			</script>
			<?php
		}
	}
	/**
	* Process the new bulk actions for changing order status
	*
	* @access public
	* @return void
	*/
	function order_bulk_action() 
	{
		global $post_type;
		if ( empty( $post_type ) )
			$post_type = isset( $_GET['post_type'] ) ?  $_GET['post_type'] : '';

		if ( 'shop_order' != $post_type )
			return;

		if ( !empty( $_GET['post_status'] ) && 'trash' == $_GET['post_status'] )
			return;

		if ( !empty( $_GET['action'] ) && 'trash' == $_GET['action'] )
			return;
			
		if ( !empty( $_GET['action'] ) ) {
		
			if ( false === strpos( $_GET['action'], 'mark_' ) )
				return;
		
		}
		
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		
		$action = $wp_list_table->current_action();

		if ( empty( $action ) )
			return;
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$terms[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$terms = wc_get_order_statuses();
		}
			
		$action = str_replace('wc-', '', $action);
		foreach( $terms as $t => $desc) 
		{
			$status = str_replace('wc-', '', $t);
			if ( 'mark_' . $status == $action ) 
			{ 
				$new_status = $status;
				$report_action = 'marked_' . $status;
				break;
			}
		}
		$changed = 0;
		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

		foreach( $post_ids as $post_id ) 
		{
			$order = new WC_Order( $post_id );
			$order->update_status( $new_status, __( 'Order status changed by bulk edit:', 'woocommerce' ) );
			$changed++;
		}

		$sendback = add_query_arg( array( 'post_type' => 'shop_order', $report_action => $changed, 'ids' => join( ',', $post_ids ) ), '' );
		wp_redirect( $sendback );
		exit();
	}
	function order_bulk_admin_notices() 
	{
		global $post_type, $pagenow;

		if ( !isset( $post_type ) )
			return;
		
		//$terms = array();
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$terms[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$terms = wc_get_order_statuses();
		}
		/* Edit by Chirag Agarwal */
		foreach( $terms as $t => $desc) 
		{ 
			if ( isset( $_REQUEST['marked_' . $t] ) ) 
			{
			
				$number = isset( $_REQUEST['marked_'.$t] ) ? absint( $_REQUEST['marked_' . $t ] ) : '';

				if ( !$number )
					continue;
					
				if ( 'edit.php' == $pagenow && 'shop_order' == $post_type ) {
				
					$message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $number ), number_format_i18n( $number ) );
					
					echo '<div class="updated"><p>' . $message . '</p></div>';
					
				}
			}
		}
	}
}
endif;

add_action('woocommerce_loaded', create_function('', '$GLOBALS["AppZab_woo_advance_order_status"] = new AppZab_Woo_Advance_Order_Status();'));
add_action( 'plugins_loaded', 'AppZab_woo_advance_order_status_init' );

function AppZab_woo_advance_order_status_init() 
{
	if ( !class_exists( 'WC_Integration' ) )
		return;
	
	if( !class_exists('AppZab_Woo_Advance_Order_Status_Settings') ):
	class AppZab_Woo_Advance_Order_Status_Settings extends WC_Integration  
	{
		protected $_plugin_dir;
		protected $_plugin_url;
		
		function __construct() 
		{
			$this->_plugin_dir = dirname(__FILE__);
			$this->_plugin_url = site_url('/wp-content/plugins/') . basename($this->_plugin_dir);
			
			$this->id = 'woo_advance_order_status';
			$this->method_title = __( 'Custom Order Status Pro for WooCommerce', 'AppZab_woo_advance_order_status' ); //display on settings page
			$this->method_description = __( 'Manage custom order statuses for your WooCommerce store', 'AppZab_woo_advance_order_status' );
			$this->init_form_fields();
			$this->init_settings();
			foreach( $this->settings as $k => $v )
				unset( $this->settings[$k] );
			
			$this->enabled = false;
			$this->addActions();
		}
		public function addActions()
		{
			add_action( 'woocommerce_update_options_integration_woo_advance_order_status', array( &$this, 'process_admin_options') );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_head_scripts' ) );
			add_action( 'init', array( &$this, 'maybe_adjust_statuses' ), 9999999 );
			add_filter('wc_order_statuses', array($this, 'filter_wc_order_statuses'));
		}
		public function filter_wc_order_statuses($order_statuses)
		{
			$custom_statuses = get_option('wc_custom_statuses', array());
			if( !is_array($custom_statuses) )
				$custom_statuses = array();
			
			return array_merge($order_statuses, $custom_statuses);
		}
		function admin_head_scripts()
		{
			global $wp_version;

			if ( isset( $_GET['section'] ) && 'woo_advance_order_status' != $_GET['section'] )
				return;
			wp_enqueue_script('wc-smart-js', $this->_plugin_url . '/js/admin.js', array('jquery'),NULL,false);
			wp_enqueue_style('wc-smart-icons', $this->_plugin_url . '/css/icons.css');
			if ( version_compare( $wp_version, '3.5' ) < 0 ) 
			{
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
			} 
			else 
			{
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
			}
			add_thickbox();
		}
		function maybe_adjust_statuses() 
		{
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>' ) )
			{
			foreach(wc_get_order_statuses() as $status => $desc)
			{
				if( in_array($status, array('wc-publish', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled')) ) continue;
				
				register_post_status($status, array(
						'label'                     => $desc,//_x($desc, 'singular', 'AppZab_woo_advance_order_status'),
						'label_count'				=> array(
								'singular'	=> $desc . '<span class="count">(%s)</span>',
								'plural'	=> $desc . '<span class="count">(%s)</span>',
								'context'	=> '',
								'domain'	=> 'AppZab_woo_advance_order_status'
						),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true
				) );
				
			}
			}

			
			if ( !is_admin() )
				return;

			if ( !isset( $_REQUEST['AppZab_woo_advance_order_status'] ) )
				return;

			if ( isset( $_GET['edit_status'] ) && !isset( $_POST['edit_status_id'] ) )
				return;
			if ( isset( $_GET['page'] ) && 'woocommerce_settings' == $_GET['page'] ) 
				$url = admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status_message=' );
			else 			
				$url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status_message=' ); 
	
			if ( !empty( $_POST ) && ( !isset( $_POST['woocommerce_woo_advance_order_status_name'] ) || '' == trim( $_POST['woocommerce_woo_advance_order_status_name'] ) ) ) 
			{
				wp_redirect( $url . 'empty' );
				die;
			}
			$custom_statuses = get_option('wc_custom_statuses', array());
			if( !is_array($custom_statuses) )
				$custom_statuses = array();
			//check to update custom status
			if ( isset( $_POST['edit_status_id'] ) && isset($custom_statuses[$_POST['edit_status_id']]) ) 
			{
				if ( !isset( $_POST['woocommerce_woo_advance_order_status_name'] ) || '' == trim( $_POST['woocommerce_woo_advance_order_status_name'] ) ) {
					wp_redirect( $url . 'empty' );
					die;
				}
				
				$this->save_status_custom_meta(trim($_POST['edit_status_id']));
				wp_redirect( $url . 'updated' );
				die;
			}
			if ( isset( $_GET['remove'] ) ) 
			{
				
				$status = $_GET['remove'];
				
				if( isset($custom_statuses[$status]) )
				{
					unset($custom_statuses[$status]);
					delete_option( 'wc_status_' . $status);
					update_option('wc_custom_statuses', $custom_statuses);
					wp_redirect( $url . 'removed' );
					die;
				}
				else
				{
					wp_redirect( $url . 'removed' );
					die;
				}
			}

            $newstatus = (substr(trim($_POST['woocommerce_woo_advance_order_status_name']),0,3) == 'wc-') ? substr(trim($_POST['woocommerce_woo_advance_order_status_name']),3) : trim($_POST['woocommerce_woo_advance_order_status_name']);
			$status_id = 'wc-'.sanitize_title($newstatus);
			$status_id = substr($status_id, 0, 20);
			$custom_statuses[$status_id] = trim($_POST['woocommerce_woo_advance_order_status_name']);

            if(strlen('wc-'.sanitize_title($newstatus))>22){
                wp_redirect($url . 'long_status');
                exit;
            }



			update_option('wc_custom_statuses', $custom_statuses);
			$this->save_status_custom_meta( $status_id );
			wp_redirect( $url . 'success' );
			die;
		}


		function save_status_custom_meta( $status_id ) 
		{
			$status = ( 'wc-' == substr($status_id, 0, 3) ) ? substr($status_id, 3) : $status_id;
			$meta = array();
			foreach( $_POST as $k => $v ) 
			{
				if ( false !== strpos( $k, 'woocommerce_woo_advance_order_status' ) )
					$meta[ $k ] = $v;
			}
			update_option( 'wc_status_' . $status, $meta );

		}
		function init_form_fields() 
		{
			$this->form_fields = array(
					'name' => array(
						'title' 	=> __( 'Status Name', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'This is the name of your new order status that will be seen everywhere you manage an order', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'default' 	=> '',
					),
					'bcolor' => array(
						'title' 	=> __( 'Background Color', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( '<span class="colorpickdiv" style="display:none"></span>Enter the hex color code to use when displaying this status in the orders page ', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'tip' 		=> 'asdf',
						'default' 	=> '',
						'class' 	=> ' colorpick '
					),
					'fcolor' => array(
						'title' 	=> __( 'Text Color', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( '<span class="colorpickdiv" style="display:none"></span>Enter the hex color code to use when displaying this status in the orders page ', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'tip' 		=> 'asdf',
						'default' 	=> '#ffffff',
						'class' 	=> ' colorpick '
					),
					'reports' => array(
						'title' 	=> __( 'Include in Reports', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'You can add this order status to the Sales Reports', 'AppZab_woo_advance_order_status' ),
						'label' 	=> 'Yes',
						'type' 		=> 'checkbox',
						'default' 	=> 'yes'
					),
					'email' => array(
						'title' 	=> __( 'Send an Email', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'An email will be sent when this order status is set', 'AppZab_woo_advance_order_status' ),
						'label' 	=> 'Yes',
						'type' 		=> 'checkbox',
						'default' 	=> 'yes'
					),
					
					'recipient' => array(
						'title'		=> __('Who receives the email?', 'AppZab_woo_advance_order_status'),
						'description' => __( 'Select a combination of group that receives the email when Send Email is set to Yes', 'AppZab_woo_advance_order_status' ),
						'label'		=> 'Yes',
						'type'		=> 'select',
						'options'	=> array(
									'customer' => __('Customer', 'AppZab_woo_advance_order_status'),
									'admin' => __('Admin', 'AppZab_woo_advance_order_status'),
									'both' => __('Customer and Admin', 'AppZab_woo_advance_order_status'),
                                                                        'ccu' => __('Customer and Custom', 'AppZab_woo_advance_order_status'),
                                                                        'custom' => __('Custom Email Address', 'AppZab_woo_advance_order_status')
						)
					),
					
					'send_to' => array(
						'title' 	=> __( 'Custom Email Address', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'Enter the Email Addresses separated by commas where you wish to send. This works only when Custom group is selected in above option ', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'tip' 		=> 'asdf',
						'default' 	=> ''
					),
					'subject' => array(
						'title' 	=> __( 'Email Subject', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'Enter the Subject of Email', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'default' 	=> ''
					),
					'heading' => array(
						'title' 	=> __( 'Heading', 'AppZab_woo_advance_order_status' ),
						'description' 	=> __( 'This Heading is visible on the Top of the Email Message. ', 'AppZab_woo_advance_order_status' ),
						'type' 		=> 'text',
						'tip' 		=> 'asdf',
						'default' 	=> ''
					),
					
			);

		}
		function excerpt( $content, $maxchars = 125 ) 
		{
			$content = substr( stripslashes( $content ), 0, $maxchars );
			$pos = strrpos( $content, " " );
			if ( $pos > 0 )
				$content = substr( $content, 0, $pos );
			echo $content;
		}
		function admin_options() 
		{
			global $woocommerce, $wpdb, $wp_version;
			
			
			$defaults = array( 'wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled' );
			//$statuses = (array) get_terms( 'shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$statuses[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$statuses = wc_get_order_statuses();
		}			
			if (  isset( $_GET['AppZab_woo_advance_order_status_message'] ) ) 
			{ 
				if ( 'fail' == $_GET['AppZab_woo_advance_order_status_message'] ) {
				?>
				<div class="error">
					<p>
						<?php _e( '*** STATUS NOT ADDED ***','AppZab_woo_advance_order_status' ); ?>

						<?php
						if ( isset( $_GET['error'] ) ) {
							_e( 'ERROR: ','AppZab_woo_advance_order_status' );
							echo $_GET['error'];
						}
						?>
					</p>
				</div>
				<?php
				} else if ( 'empty' == $_GET['AppZab_woo_advance_order_status_message'] ) {
				?>
				<div class="error">
					<p>
						<?php _e( 'Please enter a name for the status','AppZab_woo_advance_order_status' ); ?>
					</p>
				</div>
				<?php

				}
                else if ( 'long_status' == $_GET['AppZab_woo_advance_order_status_message'] ) {
                    ?>
                    <div class="error">
                        <p>
                            <?php _e( 'Status name too long. Not more than 20 characters','AppZab_woo_advance_order_status' ); ?>
                        </p>
                    </div>
                <?php

                }
                else if ( 'success' == $_GET['AppZab_woo_advance_order_status_message'] ) {
				?>
				
				<div class="updated fade">
					<p><?php _e( 'New Order status added','AppZab_woo_advance_order_status' ); ?></p>
				</div>
				<?php
				}
                else if ( 'removed' == $_GET['AppZab_woo_advance_order_status_message'] ) {
				?>
				<div class="updated fade">
					<p>
						<?php _e( 'The Order status has been removed.','AppZab_woo_advance_order_status' ); ?>
					</p>
				</div>
				<?php
				} else if ( 'update' == $_GET['AppZab_woo_advance_order_status_message'] ) {
				?>
				<div class="updated fade">
					<p><?php _e( 'Status updated.', 'AppZab_woo_advance_order_status' ); ?></p>
				</div>
				<?php
				}
			}
			$this->settings = array();

			$editing = false;

			$button_string = __( 'Submit', 'AppZab_woo_advance_order_status' );
			$title_header_new = __( 'Add Custom Order Status', 'AppZab_woo_advance_order_status' );
			$the_status = isset($_GET['edit_status']) ? trim($_GET['edit_status']) : null;
			if ( $the_status && isset($statuses[$the_status]) ) 
			{
				//$term = get_term_by( 'id', absint( $_GET['edit_status'] ), 'shop_order_status' );
				$status = ('wc-' == substr($the_status, 0, 3)) ? substr($the_status, 3) : $the_status;
				//$term_meta = get_option( 'taxonomy_' . $term->term_id, false );
				$term_meta = get_option( 'wc_status_' . $status, false );

                //echo '<pre>'; print_r($term_meta); echo '</pre>'; die;

				$this->settings['name'] = $statuses[$the_status];
				if ( $term_meta ) 
				{
					$this->settings['fcolor'] = $term_meta['woocommerce_woo_advance_order_status_fcolor'];
					
					$this->settings['bcolor'] = $term_meta['woocommerce_woo_advance_order_status_bcolor'];

					if ( isset( $term_meta['woocommerce_woo_advance_order_status_email'] ) && '1' == $term_meta['woocommerce_woo_advance_order_status_email'] ) 
						$this->settings['email'] = 'yes';
					else
						$this->settings['email'] = 'no';

					if ( isset( $term_meta['woocommerce_woo_advance_order_status_reports'] ) && '1' == $term_meta['woocommerce_woo_advance_order_status_reports'] ) 
						$this->settings['reports'] = 'yes';
					else
						$this->settings['reports'] = 'no';
						
					$this->settings['subject'] = stripslashes( $term_meta['woocommerce_woo_advance_order_status_subject'] );

					$this->settings['message'] = '';//stripslashes( $term_meta['woocommerce_woo_advance_order_status_message'] );

					$this->settings['heading'] = stripslashes( $term_meta['woocommerce_woo_advance_order_status_heading'] );

					$this->settings['send_to'] = stripslashes( $term_meta['woocommerce_woo_advance_order_status_send_to'] );
					
					$this->settings['btn_cancel'] = (isset($term_meta['woocommerce_woo_advance_order_status_btn_cancel']) && $term_meta['woocommerce_woo_advance_order_status_btn_cancel'] == 1) ? 'yes' : 'no';
					$this->settings['email_order_info'] = (isset($term_meta['woocommerce_woo_advance_order_status_email_order_info']) && $term_meta['woocommerce_woo_advance_order_status_email_order_info'] == 1) ? 'yes' : 'no';
					$this->settings['email_display_custom_msg'] = ($term_meta['woocommerce_woo_advance_order_status_email_display_custom_msg'] == 1) ? 'yes' : 'no';
					$this->settings['email_custom_msg'] = stripslashes( $term_meta['woocommerce_woo_advance_order_status_email_custom_msg']);
					$this->settings['email_from_name'] = $term_meta['woocommerce_woo_advance_order_status_email_from_name'];
					$this->settings['email_from_email'] = $term_meta['woocommerce_woo_advance_order_status_email_from_email'];
					$this->settings['recipient'] = $term_meta['woocommerce_woo_advance_order_status_recipient'];
					$this->settings['action_icon'] = $term_meta['woocommerce_woo_advance_order_status_action_icon'];
					$this->settings['payment_method'] = $term_meta['woocommerce_woo_advance_order_status_payment_method'];
					$this->settings['send_payment_method'] = (isset($term_meta['woocommerce_woo_advance_order_status_send_payment_method']) && $term_meta['woocommerce_woo_advance_order_status_send_payment_method']) ? 'yes' : 'no';
					$this->settings['send_shipping_method'] = (isset($term_meta['woocommerce_woo_advance_order_status_send_shipping_method']) && $term_meta['woocommerce_woo_advance_order_status_send_shipping_method']) ? 'yes' : 'no';
                    $this->settings['manage_stock'] = (isset($term_meta['woocommerce_woo_advance_order_status_manage_stock'])) ? $term_meta['woocommerce_woo_advance_order_status_manage_stock'] : '';
                    $this->settings['auto_switch'] = (isset($term_meta['woocommerce_woo_advance_order_status_auto_switch'])) ? $term_meta['woocommerce_woo_advance_order_status_auto_switch'] : '';
                    $this->settings['allow_download'] = (isset($term_meta['woocommerce_woo_advance_order_status_allow_download'])) ? $term_meta['woocommerce_woo_advance_order_status_allow_download'] : 'no';
                    $this->settings['customer_status_trigger'] = (isset($term_meta['woocommerce_woo_advance_order_status_customer_status_trigger'])) ? $term_meta['woocommerce_woo_advance_order_status_customer_status_trigger'] : '';

				}
				$editing = true;
				$button_string = __( 'Update status', 'AppZab_woo_advance_order_status' );
				$title_header_new = __( 'Edit Status', 'AppZab_woo_advance_order_status' );
			}

			?>
			<style>
			.stat mark { 
				border-radius: 0px;
				display: block;
				font-size: 11px;
				font-weight: bold;
				line-height: 2;
				margin: 0;
				padding: 0 3px;
				text-align: center;
				white-space: nowrap;
				width: 80px;
			}
			.widefat th {font-weight: bold;}
			.widefat thead, .widefat tbody {background: #fff !important;} 			
			.form-table td, .form-table th {padding: 1.5em;} 
			</style>
			<script>
				jQuery(function() 
				{
					jQuery( ".submit" ).find( "input[type='submit']").val( "<?php echo $button_string ?>" );
					jQuery( ".status_remove" ).click( function() {
						if ( !confirm( '<?php _e( 'Are you sure you want to remove this status?', 'AppZab_woo_advance_order_status' ) ?>' ) )
							return false;
					});
					<?php if ( version_compare( $wp_version, '3.5' ) < 0 ) { ?>
						// Color picker
						jQuery('.colorpick').each(function(){
							jQuery('.colorpickdiv', jQuery(this).parent()).farbtastic(this);
							jQuery(this).click(function() {
								if ( jQuery(this).val() == "" ) jQuery(this).val('#');
								jQuery('.colorpickdiv', jQuery(this).parent() ).show();
							});
						});
						jQuery(document).mousedown(function(){
							jQuery('.colorpickdiv').hide();
						});
					<?php  } else { ?>
							jQuery( ".colorpick" ).wpColorPicker();
							jQuery( document ).mousedown( function( e ) {
								if ( jQuery( e.target ).hasParent( ".wp-picker-holder" ) )
									return;
								if ( jQuery( e.target ).hasParent( "mark" ) )
									return;
								jQuery( ".wp-picker-holder" ).each(function() {
									jQuery( this ).fadeOut();
								});
							});	 
					<?php } ?>
				});
			</script>
			<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings','AppZab_woo_advance_order_status' ) ; ?> | <a href="http://codecanyon.net/user/appzab/portfolio?ref=appzab" target=_blank>View our Complete list of Plugins </a></h3>
			<p><em><?php _e( 'Emails can be customized for Custom Order Statuses from here or by overriding them in your theme. Go to WooCommerce --> Settings --> Emails to customize Emails of default order status', 'AppZab_woo_advance_order_status' )?></em></p>
			<table class="widefat" style="width:auto;  display:inline; clear:none;">
			<thead>
			<tr>
				<th style="min-width:50px;"><?php _e( 'ID','AppZab_woo_advance_order_status' ); ?></th>
				<th style="min-width:100px;"><?php _e( 'Name','AppZab_woo_advance_order_status' ); ?></th>
				<th><?php _e( 'Slug','AppZab_woo_advance_order_status' ); ?></th>
				<th>
					<span class="woocommerce_status_action_font_icon woocommerce_status_actions_e0ec tips status-table-icons" 
								data-tip="<?php _e('Email Sent','AppZab_woo_advance_order_status' ); ?>"></span>
				</th>
				<th style="min-width:100px;"><?php _e( 'Subject','AppZab_woo_advance_order_status' ); ?></th>
				<th><?php _e( 'Header','AppZab_woo_advance_order_status' ); ?></th>
				
				<th>
					<span class="woocommerce_status_action_font_icon woocommerce_status_actions_e0ed tips status-table-icons" 
								data-tip="<?php _e('Email Recipient','AppZab_woo_advance_order_status' ); ?>"></span>
				</th>
				<th><?php _e('Action Icon', 'AppZab_woo_advance_order_status' ); ?></th>
				<th><?php _e('Actions', 'AppZab_woo_advance_order_status'); ?></th>
			</tr>
			</thead>
			<?php
				$did = 1;

				foreach( $statuses as $status => $desc) 
				{
					
					$_status = ('wc-' == substr($status, 0, 3)) ? substr($status, 3) : $status;
					//$meta = get_option( 'taxonomy_' . $status->term_id, false );
					$meta = get_option( 'wc_status_' . $_status, false );
					if ( !$meta || empty( $meta['woocommerce_woo_advance_order_status_fcolor'] ) )
						$fgcolor = '#000000';
					else
						$fgcolor = $meta['woocommerce_woo_advance_order_status_fcolor'];
				
					if ( !$meta || empty( $meta['woocommerce_woo_advance_order_status_bcolor'] ) )
						$bgcolor = '#ffffff';
					else
						$bgcolor = $meta['woocommerce_woo_advance_order_status_bcolor'];

					$send_email_css = '';
					$email_tip = '';
					if ( !$meta )
						$sends_email = '';
					else if ( $meta && isset( $meta['woocommerce_woo_advance_order_status_email'] ) && ( '1' == $meta['woocommerce_woo_advance_order_status_email'] || 'yes' == $meta['woocommerce_woo_advance_order_status_email'] ) )
					{
						$sends_email = 'e370';
						$send_email_css = 'color: #73a724;';
						$email_tip = __('Email Sent', 'AppZab_woo_advance_order_status');
					}
					else
					{
						$sends_email = 'e36b';
						$send_email_css = 'color: #a00;';
						$email_tip = __('Email Not Sent', 'AppZab_woo_advance_order_status');
					}
					?>
					<tr <?php if ( !in_array( $status, $defaults ) ) { } ?>>
						<td><?php echo $did ?></td>
						<td class="stat" style="text-align:center;">
							<mark style="background-color: <?php echo $bgcolor ?>; color: <?php echo $fgcolor ?>; <?php if ( in_array( $status, $defaults ) ){ echo ' font-weight:normal'; }  ?>" >
								<?php echo $desc ?>
							</mark>
						</td>
						<td><?php echo $status ?></td>
						<td style="text-align:center;">
							<span class="woocommerce_status_action_font_icon woocommerce_status_actions_<?php echo $sends_email ?> tips status-table-icons" style="<?php print $send_email_css; ?>" 
								data-tip="<?php print $email_tip; ?>"></span>
						</td>
						<td>
							<?php 
							if ( isset( $meta['woocommerce_woo_advance_order_status_subject'] ) ) {
								echo $meta['woocommerce_woo_advance_order_status_subject']; 
							} 
							?>
						</td>
						<td>
							<?php 
							if ( 	isset( $meta['woocommerce_woo_advance_order_status_heading'] ) ) {
								  echo $meta['woocommerce_woo_advance_order_status_heading'];
							} ?>
						</td>
						
						<td style="text-align:center;">
							<?php 
							//echo $meta['woocommerce_woo_advance_order_status_send_to']
							$icon = 'e185';  
							$tip = '';
							if(@$meta['woocommerce_woo_advance_order_status_recipient'] == 'customer')
							{
								$icon = 'e185';
								$tip = __('Customer', 'AppZab_woo_advance_order_status');
							}
							elseif(@$meta['woocommerce_woo_advance_order_status_recipient'] == 'admin')
							{
								$icon = 'e199';
								$tip = __('Administrator', 'AppZab_woo_advance_order_status');
							}
							elseif(@$meta['woocommerce_woo_advance_order_status_recipient'] == 'both')
							{
								$icon = 'e186';
								$tip = __('Customer and Administrator', 'AppZab_woo_advance_order_status');
							}
							elseif(@$meta['woocommerce_woo_advance_order_status_recipient'] == 'custom')
							{
								$icon = 'e012';
								$tip = __('Custom Email Address', 'AppZab_woo_advance_order_status');
							}
							?>
							<span class="woocommerce_status_action_font_icon woocommerce_status_actions_<?php print $icon; ?> tips status-table-icons" data-tip="<?php print $tip; ; ?>"></span>
						</td>
						<td style="text-align:center;">
							<?php
							//$icon = 'e05c';
							$icon = !empty($meta['woocommerce_woo_advance_order_status_action_icon']) ? 
										'icon-'.$meta['woocommerce_woo_advance_order_status_action_icon'] : 'default-action-icon-selector';
							?>
							<a href="javascript:;" class="button <?php print $icon ?>">&nbsp;</a>
						</td>
						<td>
					<?php
						if ( !in_array( $status, $defaults ) ) 
						{
							if ( isset( $_GET['page'] ) && 'woocommerce_settings' == $_GET['page'] ) 
								$url = admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status=1&remove=' . $status );
							else 			
								$url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status=1&remove=' . $status ); 
							echo '<a class="status_remove" href="'. $url .'">'.__( 'Remove','AppZab_woo_advance_order_status' ).'</a>';
							echo ' | ';
							if ( isset( $_GET['page'] ) && 'woocommerce_settings' == $_GET['page'] ) 
								$url = admin_url( 'admin.php?page=woocommerce_settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status=1&edit_status=' . $status . '#add_edit' );
							else 			
								$url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status=1&edit_status=' . $status . '#add_edit' ); 
							echo '<a href="'. $url .'">'.__( 'Change','AppZab_woo_advance_order_status' ).'</a>';

						} else {
$url = admin_url( 'admin.php?page=wc-settings&tab=integration&section=woo_advance_order_status&AppZab_woo_advance_order_status=1&edit_status=' . $status . '#add_edit' );
							echo '<a href="'. $url .'">'.__( 'Change','AppZab_woo_advance_order_status' ).'</a>';
						}
					?>
						</td>
					</tr>
				<?php 
					$did++;
				}
			?>
			</table>
			<?php if ( $editing ): ?>
			<p style="font-style:italic; color: #ff0000">
				<?php _e( 'The Name of Custom Order Status is Editable. To Change the Slug, delete this status and create a new one.', 'AppZab_woo_advance_order_status'); ?>
			</p>
			<input type="hidden" name="edit_status_id" value="<?php echo absint( $_GET['edit_status'] ) ?>">
			<?php endif; ?>
			<a name="add_edit">&nbsp;</a>
			<table class="form-table" style="clear:both; margin-top: 20px; border: 1px solid #ccc">
			<thead>
			<tr>
				<th colspan="2" style="background-color:#eee"><strong><?php echo $title_header_new ?></strong></th>
			</tr>
			</thead>
			<?php $this->generate_settings_html(); ?>
			</table>
			<input type="hidden" name="AppZab_woo_advance_order_status" value="1">
			<?php  ?>
			<div id="fontawesome-icons-list" style="display:none">
				<div>
		        <section id="web-application">
		        	<h3><?php _e('Click on any of the following icons to set it', 'AppZab_woo_advance_order_status'); ?></h3>
		        	<?php print $this->generate_icons_list_from_img() ?>
		        </section>
		        </div>
			</div>
			<?php  ?>
		<?php 
		}
		public function generate_select_icon_html($key, $data)
		{
			$field    = $this->plugin_id . $this->id . '_' . $key;
			$defaults = array(
					'title'             => '',
					'disabled'          => false,
					'class'             => '',
					'css'               => '',
					'placeholder'       => '',
					'type'              => 'text',
					'desc_tip'          => false,
					'description'       => '',
					'custom_attributes' => array()
			);
			
			$data = wp_parse_args( $data, $defaults );
			
			$icon = $this->get_option($key);
			$icon = empty($icon) ? 'default-action-icon-selector' : 'icon-'.$icon;
			if( !isset($_GET['edit_status']))
				$icon = 'default-action-icon-selector';
			
			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
				<td>
					<a href="javascript:;" id="action-icon-selector" class="button tips <?php print $icon; ?>">&nbsp;</a>
					<input type="hidden" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" 
							value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" />
					<a class="thickbox" href="#TB_inline?width=600&amp;inlineId=fontawesome-icons-list&amp;width=640&amp;height=312" style="margin-left: 10px;">
						<?php _e('Click here to change the icon', 'AppZab_woo_advance_order_status'); ?>
					</a>
					<?php echo $this->get_description_html( $data ); ?>
				</td>
			</tr>
			<?php
			return ob_get_clean();
		}
		/* Edit by Chirag Agarwal */
		/**
		 * Generates the thickbox content that contains all action icons for the user to choose from
		 * @return [string] [HTML to display]
		 */
		function generate_icons_list_from_img( $status = false ) 
		{
			global $woocommerce;

			$class_selector = ($status) ? 'status-icon-selector' : 'action-icon-selector';
			ob_start();
			$icons_dir = $this->_plugin_dir . SB_DS . 'images' . SB_DS . 'icons-16';
			$icons_url = $this->_plugin_url . '/images/icons-16';
			$dh = opendir($icons_dir);
			print '<ul id="icons-list">';
			while( ($file = readdir($dh)) !== false )
			{
				if( $file{0} == '.') continue;
				$icon = $icons_url . '/' . $file;
				$css_class = substr($file, 0, strrpos($file, '.'));
				printf("<li><a href=\"javascript:;\" rel=\"%s\" class=\"%s\"><img src=\"%s\" alt=\"\" /></a></li>", 
						$css_class, $class_selector, $icon);
			}
			closedir($dh);
			print '<ul>';
			return ob_get_clean();
			
		}

        function generate_interval_html($key, $data){
            $field    = $this->plugin_id . $this->id . '_' . $key;
            $int = '0';
            $val = '';
            $sts = '0';

            if(isset($this->settings['auto_switch']['int'])){
                $int = $this->settings['auto_switch']['int'];
            }
            if(isset($this->settings['auto_switch']['val'])){
                $val = $this->settings['auto_switch']['val'];
            }
            if(isset($this->settings['auto_switch']['sts'])){
                $sts = $this->settings['auto_switch']['sts'];
            }
            ob_start();
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>

                </th>
                <td>
                  <fieldset>
                  <select name="<?php echo $field; ?>[int]">
                      <option value="0"<?php if($int==0){ echo 'selected'; } ?>>Select Interval</option>
                      <option value="day"<?php if($int=='day'){ echo 'selected'; } ?>>Day</option>
                      <option value="minute"<?php if($int=='minute'){ echo 'selected'; } ?>>Minute</option>
                      <option value="hour"<?php if($int=='hour'){ echo 'selected'; } ?>>Hour</option>
                      <option value="second"<?php if($int=='second'){ echo 'selected'; } ?>>Second</option>
                  </select>
                    <input type="number" name="<?php echo $field; ?>[val]" value="<?php echo $val;  ?>" placeholder="0">

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                      <select name="<?php echo $field; ?>[sts]">
                          <option value="0"<?php if($sts==0){ echo 'selected'; } ?>>Select Status</option>
                          <?php
                          $_statuses = wc_get_order_statuses();
                          foreach($_statuses as $k=>$s){ ?>
                          <option value="<?php echo $k; ?>"<?php if($k==$sts){ echo 'selected'; } ?>><?php echo $s; ?></option>
                          <?php } ?>
                       </select>

                      <p class="description"><?php echo $data['description']; ?></p>
                    </fieldset>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

        function generate_customer_status_trigger_html($key, $data){
            $field    = $this->plugin_id . $this->id . '_' . $key;
            $allowed = array();

            if(isset($this->settings['customer_status_trigger'])){
                $allowed = $this->settings['customer_status_trigger'];
            }
            ob_start();
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>

                </th>
                <td>
                    <fieldset>
                        <select multiple="multiple" name="<?php echo $field; ?>[]" class="wc-enhanced-select" title="Select Status">
                        <?php
                            $_statuses = wc_get_order_statuses();
                            foreach($_statuses as $k=>$s){
                                $selected ='';
                                if(in_array($k,$allowed)){
                                    $selected = 'Selected="selected"';
                                }?>
                                <option value="<?php echo $k; ?>"<?php  echo $selected;  ?>><?php echo $s; ?></option>
                            <?php } ?>
                        </select>
                        <p class="description"><?php echo $data['description']; ?></p>

                    </fieldset>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }
		
	}
	endif;
}