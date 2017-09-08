<?php
if ( ! class_exists( 'WC_Email_Adv_Statuses' ) ) :


class WC_Email_Adv_Statuses extends WC_Email
{
	public $status_ops = array();

	function __construct($status_slug = null)
	{


        $_status = (substr($status_slug, 0, 3) == 'wc-') ? substr($status_slug, 3) : $status_slug;
        $s = ucfirst($_status);
        $this->id 				= $status_slug;
        $this->title = sprintf(__("%s order"), $s);
        $this->description		= __( 'Some description goes here.', 'AppZab_woo_advance_order_status');
        $this->heading = sprintf(__("Your order is %s", 'AppZab_woo_advance_order_status'), $s);
        $this->subject = sprintf(__( 'Your {site_title} order from {order_date} is %s', 'AppZab_woo_advance_order_status'), $s);
        $this->template_base		= SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'templates' . SB_DS;

		$this->template_html 	= 'emails/'.$this->id.'-order.php';
		$this->template_plain 	= 'emails/plain/'.$this->id.'-order.php';
        $this->template_base = SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'templates' . SB_DS;

        $defaults = array('wc-pending','wc-failed','wc-on-hold','wc-processing','wc-completed','wc-refunded','wc-cancelled');
        if(!in_array($status_slug,$defaults)){
            $_status = (substr($status_slug, 0, 3) == 'wc-') ? substr($status_slug, 3) : $status_slug;
            add_action('woocommerce_order_status_'.$_status.'_notification',array($this,'trigger'));
        }

		parent::__construct();
        
	}

	function trigger( $order_id )
	{

        if( file_exists(get_template_directory().'/woocommerce/emails/customer-processing-order.php') && $this->template_html=='emails/wc-processing-order.php'){
            $this->template_base = get_template_directory();
            $this->template_html = '/woocommerce/emails/customer-processing-order.php';
        }
        elseif( file_exists(get_template_directory().'/woocommerce/emails/customer-completed-order.php') && $this->template_html=='emails/wc-completed-order.php'){
            $this->template_base = get_template_directory();
            $this->template_html = '/woocommerce/emails/customer-completed-order.php';
        }
        elseif( file_exists(get_template_directory().'/woocommerce/emails/admin-cancelled-order.php') && $this->template_html=='emails/wc-cancelled-order.php'){
            $this->template_base = get_template_directory();
            $this->template_html = '/woocommerce/emails/admin-cancelled-order.php';
        }
        elseif(file_exists(get_template_directory().'/woocommerce/'.$this->template_html)){
            $this->template_base = get_template_directory();
            $this->template_html = '/woocommerce/'.$this->template_html;
        }
        elseif(file_exists(get_template_directory().'/woocommerce/emails/default-email.php')){
            $this->template_base = get_template_directory();
            $this->template_html = '/woocommerce/emails/default-email.php';
        }
		elseif(file_exists($this->template_base . $this->template_html) )
		{
            $this->template_html = $this->template_html;
		}
        else
        {
            //$this->template_base = SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'templates' . SB_DS;
            $this->template_html = 'emails' . SB_DS . 'default-email.php';
        }

        if(file_exists(get_template_directory().'/woocommerce/'.$this->template_plain)){
            $this->template_base = get_template_directory();
            $this->template_plain = '/woocommerce/'.$this->template_plain;

        }
        elseif(file_exists(get_template_directory().'/woocommerce/emails/plain/default-email.php')){
            $this->template_base = get_template_directory();
            $this->template_plain = '/woocommerce/emails/plain/default-email.php';

        }
        elseif(file_exists($this->template_base . $this->template_plain) )
        {
            //$this->template_base = SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'templates' . SB_DS;
            $this->template_plain = $this->template_plain;
        }
        else
        {
            //$this->template_base = SB_ADV_WC_ORDER_STATUS_PLUGIN_DIR . SB_DS . 'templates' . SB_DS;
            $this->template_plain = 'emails' . SB_DS . 'plain' . SB_DS . 'default-email.php';
        }

		if ( $order_id ) 
		{
			$this->object = new WC_Order( $order_id );

			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );

			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();

            $this->find[] = '{billing_first_name}';
            $this->replace[] = $this->object->billing_first_name;

            $this->find[] = '{billing_last_name}';
            $this->replace[] = $this->object->billing_last_name;

            $this->find[] = '{billing_email}';
            $this->replace[] = $this->object->billing_email;

            $this->find[] = '{billing_phone}';
            $this->replace[] = $this->object->billing_phone;

			$status = ('wc-' == substr($this->id, 0, 3)) ? substr($this->id, 3) : $this->id;
			$this->status_ops = get_option('wc_status_'.$status, array());
		}

		$subject = empty($this->status_ops['woocommerce_woo_advance_order_status_subject']) ?
							$this->get_subject() :
							$this->status_ops['woocommerce_woo_advance_order_status_subject'];

		$headers = $this->get_headers();

		$this->heading = empty($this->status_ops['woocommerce_woo_advance_order_status_heading']) ?
								$this->heading :
								$this->status_ops['woocommerce_woo_advance_order_status_heading'];
		
		$recipient = $this->status_ops['woocommerce_woo_advance_order_status_recipient'];

		if( $recipient == 'customer' )
		{
			$email_to = $this->object->billing_email;
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}
		elseif( $recipient == 'admin' )
		{
			$email_to = get_bloginfo('admin_email');
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}
		elseif( $recipient == 'both' )
		{
			$email_to = $this->object->billing_email;
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
                       	$email_to = get_bloginfo('admin_email');
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}
        elseif( $recipient == 'ccu' )
		{
			$email_to = $this->object->billing_email;
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
                       	$email_to = $this->status_ops['woocommerce_woo_advance_order_status_send_to'];
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}
		elseif( $recipient == 'custom' )
		{
			$email_to = $this->status_ops['woocommerce_woo_advance_order_status_send_to'];
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}
		else 
		{
			$email_to = $this->object->billing_email;
			$this->sendbefore($email_to, $subject, $this->get_content(), $headers, $this->get_attachments() );
		}

	}

    function sendbefore($to, $subject, $message, $headers, $attachments){

        if(!empty($this->find)){
            foreach($this->find as $key=>$str){
                $subject = str_replace($str,$this->replace[$key],$subject);
                $message = str_replace($str,$this->replace[$key],$message);
            }
        }
        $this->send($to, $subject, $message, $headers, $attachments );
    }

	function get_from_name()
	{
		$from_name = (!empty($this->status_ops['woocommerce_woo_advance_order_status_email_from_name'])) ?
							$this->status_ops['woocommerce_woo_advance_order_status_email_from_name'] :
							get_option( 'woocommerce_email_from_name' );
		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	function get_from_address()
	{
		$from_email = (!empty($this->status_ops['woocommerce_woo_advance_order_status_email_from_email'])) ? 
											$this->status_ops['woocommerce_woo_advance_order_status_email_from_email'] : 
											get_option( 'woocommerce_email_from_address' );
		return sanitize_email( $from_email );
	}

	function get_content_html()
	{
		ob_start();
		wc_get_template($this->template_html, 
						array(
							'order' 		=> $this->object,
							'email_heading' => $this->get_heading(),
							'sent_to_admin' => true,
							'plain_text'    => false,
							'ops' 			=> $this->status_ops),
						'', 
						$this->template_base);
		return ob_get_clean();
	}

	function get_content_plain() {

		ob_start();
		wc_get_template( $this->template_plain, 
						array(
							'order' 		=> $this->object,
							'email_heading' => $this->get_heading(),
							'sent_to_admin' => true,
							'plain_text'    => true
						),
						'', 
						$this->template_base 
		);
		return ob_get_clean();
	}

	function init_form_fields() {
		$this->form_fields = array(
				'enabled' => array(
						'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Enable this email notification', 'woocommerce' ),
						'default' 		=> 'yes'
				),
				'recipient' => array(
						'title' 		=> __( 'Recipient(s)', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option('admin_email') ) ),
						'placeholder' 	=> '',
						'default' 		=> ''
				),
				'subject' => array(
						'title' 		=> __( 'Subject', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
						'placeholder' 	=> '',
						'default' 		=> ''
				),
				'heading' => array(
						'title' 		=> __( 'Email Heading', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
						'placeholder' 	=> '',
						'default' 		=> ''
				),
				'email_type' => array(
						'title' 		=> __( 'Email type', 'woocommerce' ),
						'type' 			=> 'select',
						'description' 	=> __( 'Choose which format of email to send.', 'woocommerce' ),
						'default' 		=> 'html',
						'class'			=> 'email_type',
						'options'		=> array(
								'plain'		 	=> __( 'Plain text', 'woocommerce' ),
								'html' 			=> __( 'HTML', 'woocommerce' ),
								'multipart' 	=> __( 'Multipart', 'woocommerce' ),
						)
				)
		);
	}

	public function log($str)
	{
        return;
		$log_file = dirname(__FILE__) . SB_DS . 'emails.log';
		$fh = null;
		if( file_exists($log_file) )
		{
			$fh = fopen($log_file, 'a+');
		}
		else
		{
			$fh = fopen($log_file, 'w+');
		}
		$date = date('Y-m-d H:i:s');
		fwrite($fh, "[$date]#".print_r($str, 1) . "\n");
		fclose($fh);
	}
}
endif;