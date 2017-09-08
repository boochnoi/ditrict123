<?php
?>
<li class="wide" id="actions">
	<select name="wc_order_action">
		<option value=""><?php _e( 'Actions', 'woocommerce' ); ?></option>
		<optgroup label="<?php _e( 'Resend order emails', 'woocommerce' ); ?>">
			<?php
			$mailer = WC()->mailer(); //new WC_Emails();//WC()->mailer();
			$available_emails = apply_filters( 'woocommerce_resend_order_emails_available', array( 'new_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice' ) );
			$mails = $mailer->get_emails();
			if ( ! empty( $mails ) ) 
			{
				foreach ( $mails as $mail ) 
				{
					if ( in_array( $mail->id, $available_emails ) ) 
					{
						echo '<option value="send_email_'. esc_attr( $mail->id ) .'">' . esc_html( $mail->title ) . '</option>';
					}
				}
			}
			?>
		</optgroup>
		<option value="regenerate_download_permissions"><?php _e( 'Generate Download Permissions', 'woocommerce' ); ?></option>
		<?php foreach( apply_filters( 'woocommerce_order_actions', array() ) as $action => $title ) { ?>
			<option value="<?php echo $action; ?>"><?php echo $title; ?></option>
		<?php } ?>
	</select>

	<button class="button wc-reload" title="<?php _e( 'Apply', 'woocommerce' ); ?>"><span><?php _e( 'Apply', 'woocommerce' ); ?></span></button>
</li>
<!-- Edit by Chirag Agarwal -->
<li class="wide">
	<div id="delete-action"><?php
		if ( current_user_can( "delete_post", $order_id ) ) {
			if ( ! EMPTY_TRASH_DAYS )
				$delete_text = __( 'Delete Permanently', 'woocommerce' );
			else
				$delete_text = __( 'Move to Trash', 'woocommerce' );
			?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $order_id ) ); ?>"><?php echo $delete_text; ?></a><?php
		}
	?></div>

	<input type="submit" class="button save_order button-primary tips" name="save" value="<?php _e( 'Save Order', 'woocommerce' ); ?>" data-tip="<?php _e( 'Save/update the order', 'woocommerce' ); ?>" />
</li>

<?php //do_action( 'woocommerce_order_actions_end', $order_id ); ?>