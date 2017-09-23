<?php
/*
Plugin Name: Send Email From Admin
Plugin URI:
Description: Easily send a simple custom email with an attachment from the WordPress administration screen. Tools -> Send Email.
Version: 1.0
Author: kojak711
Domain Path: /languages
Text Domain: sefa

Send Email From Admin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Send Email From Admin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Send Email From Admin.  If not, see <http://www.gnu.org/licenses/>.
*/


# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEFA_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'SEFA_PLUGIN_VER', '0.9.3' );

/**
 * Add our sub menu in the Tools menu
 *
 * @since 0.9.2
 */
function sefa_plugin_add_admin_page() { 	
	// create sefa submenu page	under the Tools menu
	$sefa_page = add_submenu_page( 'tools.php', 'Send Email From Admin', 'Send Email', 'manage_options', 'sefa_email', 'sefa_plugin_main' );
	// load js and css on sefa page only
	add_action( 'load-' . $sefa_page, 'sefa_plugin_scripts' );
}
add_action( 'admin_menu', 'sefa_plugin_add_admin_page' );

/**
 * Load our css and js.
 *
 * @since 0.9.2
 */
function sefa_plugin_scripts() {
	wp_enqueue_style( 'sefa_admin_css', SEFA_PLUGIN_DIR_URL . 'css/sefa.css', '', SEFA_PLUGIN_VER );
	wp_enqueue_script( 'sefa_admin_js', SEFA_PLUGIN_DIR_URL . 'js/sefa.js', array('jquery'), SEFA_PLUGIN_VER);
}

/**
 * Register our text domain.
 *
 * @since 0.9
 */
function sefa_plugin_load_textdomain() {
	load_plugin_textdomain( 'sefa', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'sefa_plugin_load_textdomain');

/**
 * Our main function to display and process our form
 * 
 * @since 0.9
 */
function sefa_plugin_main() {
	// get site info to construct 'FROM' for email
	$from_name = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
	$from_email = get_bloginfo('admin_email');

	// initialize
	$send_mail_message = false;

	if ( !empty( $_POST ) && check_admin_referer( 'sefa_send_email', 'sefa-form-nonce' ) ) {
		// handle attachment
		$attachment_path = '';
		if ( $_FILES ) {
			if ( !function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			$uploaded_file = $_FILES['attachment'];
			$upload_overrides = array( 'test_form' => false );
			$attachment = wp_handle_upload( $uploaded_file, $upload_overrides );
		    if ( $attachment && !isset( $attachment['error'] ) ) {
			    // file was successfully uploaded
			    $attachment_path = $attachment['file'];
			} else {
			    // echo $attachment['error'];
			}
		}

		// get the posted form values
		$sefa_recipient_emails = isset( $_POST['sefa_recipient_emails'] ) ? trim($_POST['sefa_recipient_emails']) : '';
		$sefa_subject = isset( $_POST['sefa_subject'] ) ? stripslashes(trim($_POST['sefa_subject'])) : '';
		$sefa_body = isset( $_POST['sefa_body'] ) ? stripslashes(nl2br($_POST['sefa_body']))  : '';
		$sefa_group_email = isset( $_POST['sefa_group_email'] ) ? trim($_POST['sefa_group_email']) : 'no';
		$recipients = explode( ',',$sefa_recipient_emails );

		// initialize some vars
		$errors = array();
		$valid_email = true;
		
		// simple form validation
    	if ( empty( $sefa_recipient_emails ) ) {
    		$errors[] = __( "Please enter an email recipient in the To: field.", 'sefa' );
    	} else {
			// Loop through each email and validate it
			foreach( $recipients as $recipient ) {
			    if ( !filter_var( trim($recipient), FILTER_VALIDATE_EMAIL ) ) {
			        $valid_email = false;
			        break;
			    }
			}
			// create appropriate error msg
			if ( !$valid_email ) {
				$errors[] = _n( "The To: email address appears to be invalid.", "One of the To: email addresses appears to be invalid.", count($recipients), 'sefa' );
			} 
	    }
	    if ( empty($sefa_subject) ) $errors[] = __( "Please enter a Subject.", 'sefa' );
	    if ( empty($sefa_body) ) $errors[] = __( "Please enter a Message.", 'sefa' );

	    // send the email if no errors were found
	    if ( empty($errors) ) {
	    	$headers[] = "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
	    	$headers[] = 'From: ' . $from_name . ' <' . $from_email . ">\r\n";
	    	$attachments = $attachment_path;

	    	if ( $sefa_group_email === 'yes' ) {
	    		if ( wp_mail( $sefa_recipient_emails, $sefa_subject, $sefa_body, $headers, $attachments ) ) {
					$send_mail_message = '<div class="updated">' . __( 'Your email has been successfully sent!', 'sefa' ) . '</div>'; 
				} else {
					$send_mail_message = '<div class="error">' . __( 'There was an error sending the email.', 'sefa' ) . '</div>';
				}
		    } else {
		    	foreach( $recipients as $recipient ) {
		    		if ( wp_mail( $recipient, $sefa_subject, $sefa_body, $headers, $attachments ) ) {
						$send_mail_message .= '<div class="updated">' . __( 'Your email has been successfully sent to ', 'sefa' ) . esc_html($recipient) . '!</div>'; 
					} else {
						$send_mail_message .= '<div class="error">' . __( 'There was an error sending the email to ', 'sefa' ) . esc_html($recipient) . '</div>';
					}
		    	}
		    }

		    // delete the uploaded file (attachment) from the server
		    if ( $attachment_path ) {
		    	unlink($attachment_path);
		    }
	    }
	}	
	?>
	<div class="wrap" id="sefa-wrapper">
		<h1><?php _e( 'Send Email From Admin', 'sefa' ); ?></h1>
		<?php 
        if ( !empty($errors) ) {
            echo '<div class="error"><ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul></div>\n";
        }
        if ( $send_mail_message ) {
        	echo $send_mail_message;
        }
        ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<form method="POST" id="sefa-form" enctype="multipart/form-data">
						<?php wp_nonce_field( 'sefa_send_email', 'sefa-form-nonce' ); ?>
						<table cellpadding="0" border="0" class="form-table">
							<tr>
								<th scope=”row”>From:</th>
								<td><input type="text" disabled value="<?php echo "$from_name &lt;$from_email&gt;"; ?>" required><div class="note"><?php _e( 'These can be changed in Settings->General.', 'sefa' ); ?></div></td>
							</tr>
							<tr>
								<th scope=”row”><label for="sefa-recipient-emails">To:</label></th>
								<td><input type="email" multiple id="sefa-recipient-emails" name="sefa_recipient_emails" value="<?php echo esc_attr( sefa_plugin_issetor($sefa_recipient_emails) ); ?>" required><div class="note"><?php _e( 'To send to multiple recipients, enter each email address separated by a comma or choose from the user list below.', 'sefa' ); ?></div>
								<select id="sefa-user-list">
									<option value="">-- <?php _e( 'user list', 'sefa' ); ?> --</option>
									<?php 
									$users = get_users( 'orderby=user_email' );
								    foreach ( $users as $user ) {
								    	if ( $user->first_name && $user->last_name ) {
								    		$user_fullname = ' (' . $user->first_name . ' ' . $user->last_name . ')';
								    	} else {
								    		$user_fullname = '';
								    	}
								    	echo '<option value="' . esc_html( $user->user_email ) . '">' . esc_html( $user->user_email ) . esc_html( $user_fullname) . '</option>';
								    };
									?>						
								</select>
								</td>
							</tr>
							<tr>
								<th scope=”row”></th>
								<td>
									<div class="sefa-radio-wrap">
									    <input type="radio" class="radio" name="sefa_group_email" value="no" id="no"<?php if ( isset($sefa_group_email) && $sefa_group_email === 'no' ) echo ' checked'; ?> required>
									    <label for="no"><?php _e( 'Send each recipient an individual email', 'sefa' ); ?></label>
									</div>
								    &nbsp;&nbsp;
								    <div class="sefa-radio-wrap">
								    <input type="radio" class="radio" name="sefa_group_email" value="yes" id="yes"<?php if ( isset($sefa_group_email) && $sefa_group_email === 'yes' ) echo ' checked'; ?> required>
								    <label for="yes"><?php _e( 'Send a group email to all recipients', 'sefa' ); ?></label>
									</div>
								</td>
							</tr>
							<tr>
								<th scope=”row”><label for="sefa-subject">Subject:</label></th>
								<td><input type="text" id="sefa-subject" name="sefa_subject" value='Problematic Shipment'<?php echo esc_attr( sefa_plugin_issetor($sefa_subject) );?>" required></td>
							</tr>
							<tr>
								<th scope=”row”><label for="sefa_body">Message:</label></th>
								<td align="left">
									<?php 
									$settings = array( "editor_height" => "200" );
                                                                        if (!isset($sefa_body)){
                                                                            $sefa_body = 'Hi ,<br><br>';
                                                                            $sefa_body = $sefa_body. 'We already already received your package containing [please indicate items]<br><br>';
                                                                            $sefa_body = $sefa_body. 'Unfortunately invoice number is missing on the package which is why we cannot process your package. <br><br>';
                                                                            $sefa_body = $sefa_body. 'We request that you email us the missing information so we can process your transaction ASAP<br><br>';
                                                                            $sefa_body = $sefa_body. 'Best regards, <br>';
                                                                            $sefa_body = $sefa_body. 'MYNZ';
                                                                            
                                                                            
                                                                            
                                                                        }
									wp_editor( sefa_plugin_issetor($sefa_body), "sefa_body", $settings ); 
									?>
								</td>
							</tr>
							<tr>
								<th scope=”row”><label for="attachment">Attachment:</label></th>
								<td><input type="file" id="attachment" name="attachment"></td>
							</tr>
							<tr>
								<td colspan="2" align="right">
									<input type="submit" value="<?php _e( 'Send Email', 'sefa' ); ?>" name="submit" class="button button-primary">
								</td>
							</tr>				
						</table>
					</form>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox">
						<h3><span>Like this plugin?</span></h3>
						<div class="inside">
							<ul>
								<li><a href="https://wordpress.org/support/view/plugin-reviews/send-email-from-admin?filter=5" target="_blank">Rate it on WordPress.org</a></li>
								<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8HHLL6WRX9Z68" target="_blank">Donate to the developer</a></li>
							</ul>
						</div> <!-- .inside -->
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php
}

/**
 * Helper function for form values
 *
 * @since 0.9
 *
 * @param string $var Var name to test isset
 *
 * @return string $var value if isset or ''
 */
function sefa_plugin_issetor(&$var) {
    return isset($var) ? $var : '';
}