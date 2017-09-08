<?php
if( !class_exists('SB_WC_Integrations') ):
class SB_WC_Integrations
{
	public function __construct()
	{
		$this->AddActions();
		$this->AddFilters();
		$this->AddStyles();
	}
	public function AddActions()
	{
		add_action('plugins_loaded', array($this, 'action_plugins_loaded'));
		add_action('init', array($this, 'action_init'));
		add_action('woocommerce_loaded', array($this, 'action_woocommerce_loaded'));
		add_action('woocommerce_init', array($this, 'action_woocommerce_init'));
		add_action('woocommerce_email_classes', array($this, 'action_woocommerce_email'),100,1);
	}
	public function AddFilters()
	{
		add_filter('woocommerce_valid_order_statuses_for_cancel', array($this, 'filter_woocommerce_valid_order_statuses_for_cancel'), 10, 2);
		add_filter('woocommerce_payment_successful_result', array($this, 'filter_woocommerce_payment_successful_result'), 100, 2);
		if( is_admin() )
		{
			add_filter('woocommerce_settings_api_form_fields_woo_advance_order_status', array($this, 'filter_woocommerce_settings_api_form_fields_woo_advance_order_status'));
			add_filter('manage_shop_order_posts_custom_column', array($this,'payment_column_manage'), 11);
			add_filter('woocommerce_admin_order_actions', array($this, 'filter_woocommerce_admin_order_actions'), 10, 2);
			add_filter('woocommerce_resend_order_emails_available', array($this, 'filter_woocommerce_resend_order_emails_available'));
			
		}
		else 
		{
			
		}
	} 
	public function AddStyles()
	{
		if( is_admin() )
		{
			add_thickbox();
			wp_enqueue_style('sb-i-admin-css', SB_ADV_WC_ORDER_STATUS_PLUGIN_URL . '/css/admin.css');
			wp_enqueue_style('sb-i-icons-css', SB_ADV_WC_ORDER_STATUS_PLUGIN_URL . '/css/ai.css');
		}
	}

	public function action_plugins_loaded()
	{
		load_plugin_textdomain('AppZab_woo_advance_order_status', false, SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'languages' . SB_DS );
	}

/* Edit by Chirag Agarwal */
	public function action_woocommerce_loaded()
	{
		
	}
	public function action_woocommerce_init()
	{
		add_action('woocommerce_after_resend_order_email', array($this, 'action_woocommerce_after_resend_order_email'), 10, 2);
	}
	public function action_init()
	{
		if( function_exists('WC') )
		{
			//require_once WC()->plugin_path() . SB_DS . 'includes' . SB_DS . 'abstracts' . SB_DS . 'abstract-wc-email.php';
			//require_once SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'class-wc-email-adv-statuses.php';
			
			$emails = WC()->mailer()->get_emails(); //force reaload all emails classes
			//##add actions handlers
			//$statuses = $this->GetStatuses();
			$defaults = array( 'wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled' );
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
			foreach($statuses as $status => $desc)
			{
				$_status = str_replace('wc-', '', $status);
				//print "wp_ajax_woocommerce_mark_order_{$_status}<br/>";die();
				add_action("wp_ajax_woocommerce_mark_order_{$_status}", array($this, 'process_custom_action'));
			}
		}
		
		if( is_admin() )
		{
			$this->HandleAdminRequest();
		}
	}
	public function action_woocommerce_order_actions_start($order_id)
	{
	}
	public function action_woocommerce_order_actions_end($order_id)
	{
	}
	public function action_woocommerce_after_resend_order_email(WC_Order $order, $email_to_send)
	{
	}
	public function process_custom_action()
	{
		$action = $_REQUEST['action'];
		if(!isset($_GET['status'])) wp_die(__('Invalid order status'));
		$action_name = strip_tags($_GET['status']);
		if ( empty($action_name) ) wp_die(__('Invalid order status'));
		if ( !is_admin() ) die;
		if ( !current_user_can('edit_shop_orders') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce' ) );
		//if ( !check_admin_referer("woocommerce-mark-order-{$action_name}")) wp_die( __( 'You have taken too long. Please go back and retry.', 'woocommerce' ) );
		$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : null;
		if (!$order_id) wp_die(__('Invalid order id'));
		
		$order = new WC_Order( $order_id );
		//$status = get_term_by('slug', $action_name, 'shop_order_status');
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
		//print_r($statuses);die();
		if( isset($statuses[$action_name]) )
		{
			$order->update_status( $action_name );
		}
		$url = admin_url('/edit.php?post_type=shop_order');
		//die(wp_get_referer());
		wp_safe_redirect( wp_get_referer() );
		die();
	}
	public function filter_woocommerce_settings_api_form_fields_woo_advance_order_status($form_fields)
	{
		$email_fields = array(
			'email_order_info' => array(
					'title' 	=> __( 'Show Order Information?', 'AppZab_woo_advance_order_status' ),
					'description' 	=> __('Select whether you would like to include the order information i.e. billing and shipping address, order items, total etc.', 'AppZab_woo_advance_order_status'),
					'type' 		=> 'checkbox',
					'default' 	=> 'no'
			),
			'email_display_custom_msg' => array(
					'title' 	=> __( 'Show a Custom Message', 'AppZab_woo_advance_order_status' ),
					'description' 	=> '',
					'type' 		=> 'checkbox',
					'default' 	=> 'no'
			),
			'email_custom_msg' => array(
					'title' 	=> __('Custom Message', 'AppZab_woo_advance_order_status' ),
					'description' 	=> '',
					'type' 		=> 'textarea',
					'default' 	=> ''
			),
			'email_from_name' => array(
					'title' 	=> __( '
							From Name', 'AppZab_woo_advance_order_status' ),
					'description' 	=> __('Enter the email name which will appear when the email is sent.', 'AppZab_woo_advance_order_status'),
					'type' 		=> 'text',
					'default' 	=> ''
			),
			'email_from_email' => array(
					'title' 	=> __( 'From Email Address', 'AppZab_woo_advance_order_status' ),
					'description' 	=> __('Enter the email address which will appear when the email is sent.', 'AppZab_woo_advance_order_status'),
					'type' 		=> 'text',
					'default' 	=> ''
			)
		);
		$replace_next = false;
		$heap = array();
		foreach($form_fields as $index => $field)
		{
			if( $index == 'send_to' )
			{
				$replace_next = true;
				continue;
			}
			if( $replace_next )
			{
				$heap[$index] = $field;
				unset($form_fields[$index]);
			}
		}
		/* Edit by Chirag Agarwal */
		$form_fields += $email_fields + $heap;
		
		//$form_fields = array_splice($form_fields, 5, 0, $email_fields);
		$form_fields['btn_cancel'] =  array(
			'title' 	=> __( 'Allow Order Cancellations', 'AppZab_woo_advance_order_status'),
			'description' 	=> __('Choose whether the customer can cancel orders from My Account when this status is applied.', 'AppZab_woo_advance_order_status'),
			'type' 		=> 'checkbox',
			'default' 	=> 'no'
		);
		$form_fields['send_payment_method'] =  array(
				'title' 	=> __( 'Include Payment Method Name', 'AppZab_woo_advance_order_status'),
				'description' 	=> __('', 'AppZab_woo_advance_order_status'),
				'type' 		=> 'checkbox',
				'default' 	=> 'no'
		);
		$form_fields['send_shipping_method'] =  array(
				'title' 	=> __( 'Include Shipping Method Name', 'AppZab_woo_advance_order_status'),
				'description' 	=> __('', 'AppZab_woo_advance_order_status'),
				'type' 		=> 'checkbox',
				'default' 	=> 'no'
		);
		$form_fields['action_icon'] =  array(
				'title' 	=> __('Action Icon', 'AppZab_woo_advance_order_status'),
				'description' 	=> __('Select an icon you would like to display as the Action button', 'AppZab_woo_advance_order_status'),
				'type' 		=> 'select_icon',
				'default' 	=> 'e05c'
		);
		$available_gateways = array('-1' => __('-- payment method ---', 'AppZab_woo_advance_order_status'));
		foreach(WC()->payment_gateways()->payment_gateways as $pm)
		{
			$available_gateways[$pm->id] = $pm->title;
		}
		//woocommerce_form_field($key, $args);
		$form_fields['payment_method'] =  array(
				'title' 	=> __('Payment Method', 'AppZab_woo_advance_order_status'),
				'description' 	=> __('Select the payment method to use this status when a new order is placed. When a customer places an order using this payment method, the chosen status will be automatically applied to it', 'AppZab_woo_advance_order_status'),
				'type' 		=> 'select',
				'default' 	=> '-1',
				'options' => $available_gateways
		);


        $form_fields['manage_stock'] =  array(
            'title' 	=> __('Managing stock level', 'AppZab_woo_advance_order_status'),
            'description' 	=> __('Managing stock level when the custom status is applied to an order. It can be Reduced, Increased or left unaffected.', 'AppZab_woo_advance_order_status'),
            'type' 		=> 'select',
            'default' 	=> 'unaffected',
            'options' => array('unaffected'=>'unaffected','reduce'=>'Reduce','increase'=>'Increase')
        );

        $form_fields['auto_switch'] =  array(
            'title' 	=> __('Status auto update interval', 'AppZab_woo_advance_order_status'),
            'description' 	=> __('Order status will be auto update after selected interval.', 'AppZab_woo_advance_order_status'),
            'type' 		=> 'interval',
            'default' 	=> '0'
            //'options' => array('0'=>'Select Interval','day'=>'Day','hour'=>'Hour','minute'=>'Minute','second'=>'Second')
        );

        $form_fields['allow_download'] =  array(
            'title' 	=> __('Allows Digital Downloads', 'AppZab_woo_advance_order_status'),
            'description' 	=> __('Allow Digital Downloads when the custom status is applied to an order.', 'AppZab_woo_advance_order_status'),
            'type' 		=> 'select',
            'default' 	=> 'no',
            'options' => array('yes'=>'Yes','no'=>'No')
        );

        $form_fields['customer_status_trigger'] =  array(
            'title' 	=> __('Customer status trigger', 'AppZab_woo_advance_order_status'),
            'description' 	=> __('Allow Customer to change status when the custom status is applied to an order.', 'AppZab_woo_advance_order_status'),
            'type' 		=> 'customer_status_trigger',
            'default' 	=> ''
        );


        return $form_fields;
	}
	public function filter_woocommerce_valid_order_statuses_for_cancel($statuses/*, $order*/)
	{
		//$_statuses = (array) get_terms('shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
			$order_status_terms = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
			
			if ( !is_wp_error( $order_status_terms ) )
			{
				//$order_status_terms = array();
				foreach ( $order_status_terms as $term )
				{
					$_statuses[ $term->slug ] = $term->name;
				}
			}
		}
		else
		{
			$_statuses = wc_get_order_statuses();
		}
		foreach ( $_statuses as $status => $desc )
		{
			$_status = ('wc-' == substr($status, 0, 3) ) ? substr($status, 3) : $status;
			$ops = get_option('wc_status_'.$_status, array());
			if( isset($ops['woocommerce_woo_advance_order_status_btn_cancel']) && $ops['woocommerce_woo_advance_order_status_btn_cancel'] == 1 )
			{
				$statuses[] = $_status;
			}
		}
		
		return $statuses;
	}
	/**
	 * 
	 * @param unknown $actions
	 * @param WC_Order $the_order
	 */
	public function filter_woocommerce_admin_order_actions($actions, $the_order)
	{
		global $post, $woocommerce;
		
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
		$status = ('wc-' == substr($the_order->status, 0, 3) ) ? $the_order->status : 'wc-' . $the_order->status;
		
		if ( isset($statuses[$status]) ) 
		{
			unset($statuses[$status]);
			//sort($statuses);
		}
		foreach($statuses as $status => $desc)
		{
			$_status = ('wc-' == substr($status, 0, 3) ) ? substr($status, 3) : $status;
			$ops = get_option('wc_status_'.$_status, array());
			$icon = !empty($ops['woocommerce_woo_advance_order_status_action_icon']) ? 
						'icon-'.$ops['woocommerce_woo_advance_order_status_action_icon'] : 'default-action-icon-selector';
			
			$label = !empty($ops['woocommerce_woo_advance_order_status_name']) ?
						$ops['woocommerce_woo_advance_order_status_name'] : $desc;
			
			$url = admin_url('admin-ajax.php?action=woocommerce_mark_order_'.$_status.'&order_id=' . $the_order->id . '&status=' . $status);
			$actions[$status] = array(
					'url' 		=> wp_nonce_url( $url, 'woocommerce-mark-order-' . $status ),
					'name' 		=> $label,
					'action' 	=> $status . ' icon-16 '.$icon
			);
		}		
		return $actions;
	}
	public function filter_woocommerce_resend_order_emails_available($mails)
	{
		$ss = get_option('wc_custom_statuses', array());
		if( !is_array($ss) )
			$ss = array();
		$mails = array_merge($mails, array_keys($ss));
		return $mails;
	}
	public function action_woocommerce_email($email_class)
	{

		require_once SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'class-wc-email-adv-statuses.php';
		$statuses = array();
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

        $defaults = array('wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled');
        //$defaults = array('wc-processing','wc-completed','wc-cancelled');
        foreach($statuses as $status => $desc)
        {
            //if(!in_array($status,$defaults)){
            if( empty($status) ) continue;
            $_status = (substr($status, 0, 3) == 'wc-') ? substr($status, 3) : $status;
            $s = ucfirst($_status);
            $obj = new WC_Email_Adv_Statuses($status);
            $email_class['WC_Email_'.$s] =  $obj;
            //}
        }
        
        return $email_class;
	}
	public function filter_woocommerce_email_classes($emails)
	{
	}
	public function filter_woocommerce_payment_successful_result($result, $order_id)
	{
		$order = new WC_Order($order_id);
		$payment_method = isset( $_POST['payment_method'] ) ? stripslashes( $_POST['payment_method'] ) : '';
		if( empty($payment_method) )
			return $result;

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
		foreach($statuses as $status => $desc)
		{
			$_status = ('wc-' == substr($status, 0, 3)) ? substr($status, 3) : $status;
			$ops = get_option('wc_status_'.$_status, array());
			
			if( !isset($ops['woocommerce_woo_advance_order_status_payment_method']) ) continue;
			if( $ops['woocommerce_woo_advance_order_status_payment_method'] == $payment_method )
			{
				$order->update_status($_status);
				break;
			}
		}
		return $result;
	}
	/**
	 * What data to view in the tables' columns
	 * @param  [string] $column [column name]
	 * @return void
	 */
	function payment_column_manage($column)
	{
		global $post, $woocommerce, $the_order;
	
		if ( empty( $the_order ) || $the_order->id != $post->ID )
			$the_order = new WC_Order( $post->ID );
		
		if( $column == 'order_status' ) 
		{
			//$statuses = wc_get_order_statuses();
			//$status = get_term_by('slug', $the_order->status, 'shop_order_status');
			$status = ('wc-' == substr($the_order->status, 0, 3)) ? substr($the_order->status, 3) : $the_order->status;
			$ops = get_option('wc_status_' . $status, array());
			if( is_object($status) && count($ops) )  
			{ 
				// Style!
				$style = '';
				$class = 'status_orders_page status_text hhh';
				if( isset($ops['woocommerce_woo_advance_order_status_bcolor']) && !empty($ops['woocommerce_woo_advance_order_status_bcolor']) )
				{
					$style .= sprintf("background-color:%s;", $ops['woocommerce_woo_advance_order_status_bcolor']);
				}
				if( isset($ops['woocommerce_woo_advance_order_status_fcolor']) && !empty($ops['woocommerce_woo_advance_order_status_fcolor']) )
				{
					$style .= sprintf("color:%s;", $ops['woocommerce_woo_advance_order_status_fcolor']);
				}
				
				printf("<strong class=\"%s\" style=\"%s\">%s</strong>", $class, $style, $ops['woocommerce_woo_advance_order_status_name']);
				print "<script type=\"text/javascript\">jQuery('.hhh').siblings('mark').hide();</script>";
			} 
		} # end of order status
	}
	public static function GetStatuses()
	{
		if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) )
		{
		$statuses = array();
		$_statuses = (array) get_terms('shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );
		foreach ( $_statuses as $status )
		{
			if( !is_object($status) ) continue;
			$ops = get_option('taxonomy_'.$status->term_id, false);
			if( $ops != false )
			{
				$statuses[] = $status->slug;
			}
		}
		
		return $statuses;
		}
		else
		{
		return wc_get_order_statuses();
		}
	}
	public function HandleAdminRequest()
	{
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : null;
		if( !$task )
			return false;
		if( $task == 'get_payment_methods' )
		{
			die();
		}
	}
}
endif;
new SB_WC_Integrations();