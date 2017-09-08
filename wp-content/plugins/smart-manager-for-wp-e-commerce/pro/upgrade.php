<?php
global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version, $sm_changelog, $sm_last_checked, $sm_text_domain, $sm_slug, $sm_documentation_link;

$sm_sku = 'sm';
$sm_text_domain = (defined('SM_TEXT_DOMAIN')) ? SM_TEXT_DOMAIN : 'smart-manager-for-wp-e-commerce';

$sm_slug = 'smart-manager-for-e-commerce';
$sm_documentation_link = 'http://www.storeapps.org/support/documentation/smart-manager/';

if (! function_exists( 'get_plugin_data' )) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$sm_base_name = SM_PLUGIN_FILE;
$sm_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . SM_PLUGIN_FILE );

add_site_option( 'smart_manager_license_key', '' );
add_site_option( 'smart_manager_download_url', '' );
add_site_option( 'smart_manager_installed_version', '' );
add_site_option( 'smart_manager_live_version', '' );
add_site_option( 'smart_manager_due_date', '' );
add_site_option( 'smart_manager_login_link', '' );
add_site_option( 'smart_manager_changelog', '' );
add_site_option( 'smart_manager_last_checked', '' );

$sm_check_update_timeout = (24 * 60 * 60); // timeout for making request to StoreApps

if ( get_site_option( 'smart_manager_installed_version' ) != $sm_plugin_data ['Version'] ) {
    update_site_option( 'smart_manager_installed_version', $sm_plugin_data ['Version'] );
}

if ( ( get_site_option( 'smart_manager_live_version' ) == '' ) || ( get_site_option( 'smart_manager_live_version' ) < get_site_option( 'smart_manager_installed_version' ) ) ) {
    update_site_option( 'smart_manager_live_version', $sm_plugin_data['Version'] );
}

$sm_live_version = get_site_option( 'smart_manager_live_version' );
$sm_installed_version = get_site_option( 'smart_manager_installed_version' );

if ( empty( $sm_license_key ) ) {
    $sm_stored_license_key = smart_get_license_key();
    $sm_license_key = ( !empty( $sm_stored_license_key ) ) ? $sm_stored_license_key : get_site_option( 'smart_manager_license_key' );
}

// Actions for License Validation & Upgrade process
add_action( 'admin_footer', 'smart_manager_add_plugin_style_script' );
add_action( 'admin_footer', 'smart_manager_support_ticket_content' );
add_action( "after_plugin_row_".$sm_base_name, 'smart_manager_update_row', 99, 2 );
add_action( 'wp_ajax_smart_manager_validate_license_key', 'smart_manager_validate_license_key' );
add_action( 'wp_ajax_smart_manager_force_check_for_updates', 'smart_manager_force_check_for_updates' );
add_action( 'wp_ajax_smart_manager_reset_license_details', 'smart_manager_reset_license_details' );

// Filters for pushing Smart Manager plugin details in Plugins API of WP
add_filter( 'site_transient_update_plugins', 'smart_manager_overwrite_site_transient', 10, 3 );

add_filter( 'plugin_row_meta', 'smart_manager_add_support_link' , 10, 4 );
add_filter( 'plugin_action_links_' . $sm_base_name, 'smart_manager_plugin_action_links' );


function smart_manager_plugin_action_links( $links ) {

    global $sm_base_name, $sm_text_domain, $sm_sku, $sm_documentation_link;

    $sm_url = '';

    if( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        $sm_url = admin_url( 'edit.php?post_type=product&page=smart-manager-woo' );
    } else if( is_plugin_active( 'wp-e-commerce/wp-shopping-cart.php' ) ) {
        $sm_url = admin_url( 'edit.php?post_type=wpsc-product&page=smart-manager-wpsc' );
    }

    $action_links = array(
        'about' => '<a href="'. esc_url( add_query_arg( array( 'landing-page' => 'sm-about' ), $sm_url ) ) .'" title="' . __( 'About', $sm_text_domain ) . '">' . __( 'About', $sm_text_domain ) . '</a>',
        'settings' => '<a href="options-general.php?page=smart-manager-settings" title="' . __( 'Settings', $sm_text_domain ) . '">' . __( 'Settings', $sm_text_domain ) . '</a>'
    );

    if (!empty($sm_url)) {
        $action_links['need_help'] = '<a href="'. esc_url( add_query_arg( array( 'landing-page' => 'sm-faqs' ), $sm_url ) ) .'" title="' . __( 'Support', $sm_text_domain ) . '">' . __( 'Need Help?', $sm_text_domain ) . '</a>';
    } else {
        if ( !empty( $sm_documentation_link ) ) {
            $documentation_link = add_query_arg( array( 'utm_source' => $sm_sku, 'utm_medium' => 'upgrade', 'utm_campaign' => 'view_docs' ), $sm_documentation_link );
            $action_links['docs'] = '<a href="'.$documentation_link.'" target="_blank" title="' . __( 'Documentation', $sm_text_domain ) . '">' . __( 'Docs', $sm_text_domain ) . '</a>';
        }

        $query_char = ( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ) ? '&' : '?';
        $action_links['need_help'] = '<a href="#TB_inline'.$query_char.'max-height=420px&inlineId=smart_manager_post_query_form" class="thickbox sm_support_link" title="' . __( 'Submit your query', $sm_text_domain ) . '">' . __( 'Need Help?', $sm_text_domain ) . '</a>';
    }

    return array_merge( $action_links, $links );
}

function smart_manager_force_check_for_updates() {

    global $sm_check_update_timeout;

    $current_transient = get_site_transient( 'update_plugins' );
    $new_transient = apply_filters( 'site_transient_update_plugins', $current_transient, 'update_plugins', true );
    set_site_transient( 'update_plugins', $new_transient, $sm_check_update_timeout );
    echo json_encode( 'checked' );
    exit();
}

function smart_manager_reset_license_details() {

    check_ajax_referer( 'storeapps-reset-license', 'security' );

    global $wpdb;

    update_site_option( 'smart_manager_license_key', '' );
    update_site_option( 'smart_manager_download_url', '' );
    update_site_option( 'smart_manager_installed_version', '' );
    update_site_option( 'smart_manager_live_version', '' );
    update_site_option( 'smart_manager_due_date', '' );
    update_site_option( 'smart_manager_login_link', '' );
    update_site_option( 'smart_manager_changelog', '' );
    update_site_option( 'smart_manager_last_checked', '' );

    sm_check_for_updates();

    die();

}


function sm_check_for_updates() {

    global $sm_sku, $sm_installed_version, $sm_live_version, $sm_changelog;

    $sm_license_key = get_site_option( 'smart_manager_license_key');

    $sm_license_query = ( !empty( $sm_license_key ) ) ? '&serial=' . $sm_license_key : '';

    $check_for_update_url = 'http://www.storeapps.org/wp-admin/admin-ajax.php?action=get_products_latest_version&sku=' . $sm_sku . $sm_license_query . '&uuid=' . urlencode( admin_url( '/' ) );
    $check_for_update_link = ( ! empty( $check_for_update_url ) ) ? add_query_arg( array( 'utm_source' => $sm_sku . '-v' . $sm_installed_version, 'utm_medium' => 'upgrade', 'utm_campaign' => 'active_install' ), $check_for_update_url ) : '';

    $result = wp_remote_post( $check_for_update_link );

    if (is_wp_error($result)) {
        return;
    }
    
    $response = json_decode( $result ['body'] );

    $live_version = $response->version;

    if ( isset( $response->link ) ) {
        update_site_option( 'smart_manager_login_link', $response->link );
    }
     
    if ( isset( $response->due_date ) ) {
        update_site_option( 'smart_manager_due_date', $response->due_date );
    }    
    
    if ( $sm_live_version == $live_version || $response == 'false' ) {
        return;
    }
    
    $sm_changelog = $response->changelog;
    $sm_live_version = $live_version;

    update_site_option( 'smart_manager_live_version', $live_version );
    update_site_option( 'smart_manager_changelog', $response->changelog );
    
}


function smart_manager_overwrite_site_transient($plugin_info, $transient = 'update_plugins', $force_check_updates = false) {
    global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version, $sm_slug;
    
    if ( !isset( $plugin_info->response ) || empty( $plugin_info->response ) || empty( $plugin_info->response[$sm_base_name] ) || count( $plugin_info->response ) <= 0 ) return $plugin_info;
    
    $sm_live_version = get_site_option( 'smart_manager_live_version' );
    $download_url       = get_site_option( 'smart_manager_download_url' );
    $download_link      = ( ! empty( $download_url ) ) ? add_query_arg( array( 'utm_source' => $sm_sku . '-v' . $sm_live_version, 'utm_medium' => 'upgrade', 'utm_campaign' => 'update' ), $download_url ) : '';

    if ( empty( $plugin_info->response [$sm_base_name]->package ) || strpos( $plugin_info->response [$sm_base_name]->package, 'downloads.wordpress.org' ) > 0 ) {
        $plugin_info->response [$sm_base_name]->package = $download_link;
    }

    if (empty( $plugin_info->checked ))
        return $plugin_info;

    $time_not_changed = isset( $plugin_info->last_checked ) && $sm_check_update_timeout > ( time() - $plugin_info->last_checked );

    if ( $force_check_updates || !$time_not_changed ) {
        sm_check_for_updates();
    }


    return $plugin_info;
}

//
function smart_manager_validate_license_key() {
    global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version;
    $sm_license_key = (isset($_REQUEST ['license_key']) && !empty($_REQUEST ['license_key'])) ? $_REQUEST ['license_key'] : '';
    $storeapps_validation_url = 'http://www.storeapps.org/wp-admin/admin-ajax.php?action=woocommerce_validate_serial_key&serial=' . urlencode($sm_license_key) . '&is_download=true&sku=' . $sm_sku;
    $resp_type = array('headers' => array('content-type' => 'application/text'));
    $response_info = wp_remote_post($storeapps_validation_url, $resp_type); //return WP_Error on response failure

    if (is_array($response_info)) {
        $response_code = wp_remote_retrieve_response_code($response_info);
        $response_msg = wp_remote_retrieve_response_message($response_info);

        // if ($response_code == 200 && $response_msg == 'OK') {
        if ($response_code == 200) {
            $storeapps_response = wp_remote_retrieve_body($response_info);
            $decoded_response = json_decode($storeapps_response);
            if ($decoded_response->is_valid == 1) {
                update_site_option('smart_manager_license_key', $sm_license_key);
                update_site_option('smart_manager_download_url', $decoded_response->download_url);
                sm_check_for_updates();
            } else {
                sm_remove_license_download_url();
            }
            echo $storeapps_response;
            exit();
        }
        sm_remove_license_download_url();
        echo json_encode(array('is_valid' => 0));
        exit();
    }
    sm_remove_license_download_url();
    echo json_encode(array('is_valid' => 0));
    exit();
}

//
function sm_remove_license_download_url() {
    update_site_option('smart_manager_license_key', '');
    update_site_option('smart_manager_download_url', '');
}

function sm_add_plugin_style() {
    echo '<style type="text/css">';
    ?>
        div#TB_window {
            background: lightgrey;
        }
        <?php if ( version_compare( get_bloginfo( 'version' ), '3.7.1', '>' ) ) { ?>
        tr.sm_license_key .key-icon-column:before {
            content: "\f112";
            display: inline-block;
            -webkit-font-smoothing: antialiased;
            font: normal 1.5em/1 'dashicons';
        }
        tr.sm_due_date .renew-icon-column:before {
            content: "\f463";
            display: inline-block;
            -webkit-font-smoothing: antialiased;
            font: normal 1.5em/1 'dashicons';
        }
        <?php } ?>
        a#sm_reset_license {
            cursor: pointer;
        }
    <?php
    echo '</style>';
}

//
function smart_manager_update_row($file, $sm_plugin_data) {
    global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version, $sm_text_domain;
    $sm_license_key = get_site_option('smart_manager_license_key');
    $sm_due_date = get_site_option('smart_manager_due_date');
    $sm_login_link = get_site_option('smart_manager_login_link');
    $valid_color = '#AAFFAA';
    $invalid_color = '#FFAAAA';
    $color = ($sm_license_key != '') ? $valid_color : $invalid_color;
?>
<?php if ( empty( $sm_license_key ) ) { ?>
       <!--96down.com <tr class="sm_license_key" style="background: <?php echo $color; ?>">
            <td class="key-icon-column" style="vertical-align: middle;"></td>
            <td style="vertical-align: middle;"><label for="sm_license_key"><strong><?php _e( 'License Key', $sm_text_domain ); ?></strong></label></td>
            <td>
                <input type="text" id="sm_license_key" name="sm_license_key" value="<?php echo $sm_license_key; ?>" size="50" style="text-align: center;" />
                <input type="button" class="button" id="sm_validate_license_button" name="sm_validate_license_button" value="<?php _e( 'Validate', $sm_text_domain ); ?>" />
                <input type="button" class="button" id="sm_check_for_updates" name="sm_check_for_updates" value="Check for updates" />
                <img id="sm_license_validity_image" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" style="display: none; vertical-align: middle;" />
            </td>
        </tr>-->
        <?php } ?>
        <?php
            if ( !empty( $sm_due_date ) ) {
                $start = strtotime( $sm_due_date . ' -30 days' );
                $due_date = strtotime( $sm_due_date );
                $now = time();
                if ( $now >= $start ) {
                    $remaining_days = round( abs( $due_date - $now )/60/60/24 );
                    $target_link = 'http://www.storeapps.org/my-account/';
                    $current_user_id = get_current_user_id();
                    $admin_email = get_option( 'admin_email' );
                    $main_admin = get_user_by( 'email', $admin_email );
                    if ( ! empty( $main_admin->ID ) && $main_admin->ID == $current_user_id ) {
                        $target_link = $sm_login_link;
                    }
                    $login_link = add_query_arg( array( 'utm_source' => $sm_sku, 'utm_medium' => 'upgrade', 'utm_campaign' => 'renewal' ), $target_link );
                    ?>
                        <tr class="sm_due_date" style="background: #FFAAAA;">
                            <td class="renew-icon-column" style="vertical-align: middle;"></td>
                            <td style="vertical-align: middle;" colspan="2">
                                <?php
                                    if ( $now > $due_date ) {
                                        echo sprintf(__( 'Your license for %s %s. Please %s to continue receiving updates & support', $sm_text_domain ), 'Smart Manager', '<strong>' . __( 'has expired', $sm_text_domain ) . '</strong>', '<a href="' . $login_link . '" target="_blank">' . __( 'renew your license now', $sm_text_domain ) . '</a>');
                                    } else {
                                        echo sprintf(__( 'Your license for %s %swill expire in %d %s%s. Please %s to get %sdiscount upto 50%%%s', $sm_text_domain ), 'Smart Manager', '<strong>', $remaining_days, _n( 'day', 'days', $remaining_days, $sm_text_domain ), '</strong>', '<a href="' . $login_link . '" target="_blank">' . __( 'renew your license now', $sm_text_domain ) . '</a>', '<strong>', '</strong>');
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php
                }
            }
    }

    function smart_manager_add_plugin_style_script() {

        global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version, $sm_text_domain, $sm_slug;

        $license_key = get_site_option( 'smart_manager_license_key' );
        $valid_color = '#AAFFAA';
        $invalid_color = '#FFAAAA';
        $color = ($license_key != '') ? $valid_color : $invalid_color;
        sm_add_plugin_style();
        ?>

    <script type="text/javascript">
        
        jQuery(function(){
            jQuery('input#sm_validate_license_button').on( 'click', function(){
                jQuery('img#sm_license_validity_image').show();
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'action': 'smart_manager_validate_license_key',
                        'license_key': jQuery('input#sm_license_key').val()
                    },
                    success: function( response ) {
                        if ( response.is_valid == 1 ) {
                            jQuery('tr.sm_license_key').css('background', '<?php echo $valid_color; ?>');
                            jQuery('#sm_license_key').hide();
                        } else {
                            jQuery('tr.sm_license_key').css('background', '<?php echo $invalid_color; ?>');
                            jQuery('input#sm_license_key').val('');
                        }
                        location.reload();
                    }
                });
            });

            jQuery('input#sm_check_for_updates').on( 'click', function(){
                jQuery('img#sm_license_validity_image').show();
                jQuery.ajax({
                    url: '<?php echo admin_url("admin-ajax.php") ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'smart_manager_force_check_for_updates'
                    },
                    success: function( response ) {
                        if ( response == 'checked' ) {
                            location.reload();
                        } else {
                            jQuery('img#sm_license_validity_image').hide();
                        }
                    }
                });
            });

            jQuery('a#sm_reset_license').on( 'click', function(){
                var status_element = jQuery(this).closest('tr');
                status_element.css('opacity', '0.4');
                jQuery.ajax({
                    url: '<?php echo admin_url("admin-ajax.php") ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'smart_manager_reset_license_details',
                        security: '<?php echo wp_create_nonce( "storeapps-reset-license" ); ?>'
                    },
                    success: function( response ) {
                        location.reload();
                    }
                });
            });

            jQuery(document).ready(function(){
                <?php if ( version_compare( get_bloginfo( 'version' ), '3.7.1', '>' ) ) { ?>
                    jQuery('tr.sm_license_key .key-icon-column').css( 'border-left', jQuery('tr.sm_license_key').prev().prev().prev().find('th.check-column').css( 'border-left' ) );
                    jQuery('tr.sm_due_date .renew-icon-column').css( 'border-left', jQuery('tr.sm_license_key').prev().prev().prev().find('th.check-column').css( 'border-left' ) );
                <?php } ?>

                // jQuery('tr#smart-manager').find( 'div.plugin-version-author-uri' ).addClass( 'sm_social_links' );
                jQuery('tr[data-slug="smart-manager-for-wp-e-commerce"]').find( 'div.plugin-version-author-uri' ).addClass( 'sm_social_links' );

                jQuery('tr.sm_license_key').css( 'background', jQuery('tr.sm_due_date').css( 'background' ) );

                <?php if ( version_compare( get_bloginfo( 'version' ), '3.7.1', '>' ) ) { ?>
                    jQuery('tr.sm_license_key .key-icon-column').css( 'border-left', jQuery('tr#<?php echo $sm_slug; ?>').find('th.check-column').css( 'border-left' ) );
                    jQuery('tr.sm_due_date .renew-icon-column').css( 'border-left', jQuery('tr#<?php echo $sm_slug; ?>').find('th.check-column').css( 'border-left' ) );
                <?php } ?>  

            });
        });
    </script>
    
    <?php
}

function smart_manager_add_support_link( $plugin_meta, $plugin_file, $plugin_data, $status ) {

    global $sm_base_name, $sm_text_domain, $sm_sku;

    if ( $sm_base_name == $plugin_file ) {
        $plugin_meta[] = '<a id="sm_reset_license" title="' . __( 'Reset License Details', $sm_text_domain ) . '">' . __( 'Reset License', $sm_text_domain ) . '</a>';
        $plugin_meta[] = '<br>' . sm_add_social_links();
    }
    
    return $plugin_meta;
    
}

if (! function_exists('sm_add_social_links')) {
    function sm_add_social_links() {

        $social_link = '<style type="text/css">
                            div.sm_social_links > iframe {
                                max-height: 1.5em;
                                vertical-align: middle;
                                padding: 5px 2px 0px 0px;
                            }
                            iframe[id^="twitter-widget"] {
                                max-width: 10.3em;
                            }
                            iframe#fb_like_sm {
                                max-width: 6em;
                            }
                            span > iframe {
                                vertical-align: middle;
                            }
                        </style>';
        $social_link .= '<a href="https://twitter.com/storeapps" class="twitter-follow-button" data-show-count="true" data-dnt="true" data-show-screen-name="false">Follow</a>';
        $social_link .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
        $social_link .= '<iframe id="fb_like_sm" src="http://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FStore-Apps%2F614674921896173&width=100&layout=button_count&action=like&show_faces=false&share=false&height=21"></iframe>';
        $social_link .= '<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script><script type="IN/FollowCompany" data-id="3758881" data-counter="right"></script>';

        return $social_link;

    }
}


//
function smart_manager_support_ticket_content() {
    
    global $current_user, $wpdb, $woocommerce, $pagenow;
    global $sm_base_name, $sm_check_update_timeout, $sm_plugin_data, $sm_sku, $sm_license_key, $sm_download_url, $sm_installed_version, $sm_live_version, $sm_text_domain;
    
    if ( !( $current_user instanceof WP_User ) ) return;
    ?>

    <div id="smart_manager_post_query_form" style="display: none;">
        <style>
            table#smart_manager_post_query_table {
                padding: 5px;
            }
            table#smart_manager_post_query_table tr td {
                padding: 5px;
            }
            input.sm_text_field {
                padding: 5px;
            }
            label {
                font-weight: bold;
            }
        </style>
        <?php
            if ( !wp_script_is('jquery') ) {
                wp_enqueue_script('jquery');
                wp_enqueue_style('jquery');
            }

            $first_name = get_user_meta($current_user->ID, 'first_name', true);
            $last_name = get_user_meta($current_user->ID, 'last_name', true);
            $name = $first_name . ' ' . $last_name;
            $customer_name = ( !empty( $name ) ) ? $name : $current_user->data->display_name;
            $customer_email = $current_user->data->user_email;
            
            $ecom_plugin_version = '';
            
            if ( isset( $_GET['post_type'] ) && !empty( $_GET['post_type'] ) ) {
                switch ( $_GET['post_type'] ) {
                    case 'wpsc-product':
                        $ecom_plugin_version = 'WPeC ' . ( defined( 'WPSC_VERSION' ) ? WPSC_VERSION : '' );
                        break;
                    case 'product':
                        $ecom_plugin_version = 'WooCommerce ' . ( ( defined( 'WOOCOMMERCE_VERSION' ) ) ? WOOCOMMERCE_VERSION : $woocommerce->version );
                        break;
                    default:
                        $ecom_plugin_version = '';
                        break;
                }
            }
            
            $wp_version = ( is_multisite() ) ? 'WPMU ' . get_bloginfo('version') : 'WP ' . get_bloginfo('version');
            $admin_url = admin_url();
            $php_version = ( function_exists( 'phpversion' ) ) ? phpversion() : '';
            // $wp_max_upload_size = wp_convert_bytes_to_hr( wp_max_upload_size() );
            $wp_max_upload_size = size_format( wp_max_upload_size() );
            $server_max_upload_size = ini_get('upload_max_filesize');
            $server_post_max_size = ini_get('post_max_size');
            $wp_memory_limit = WP_MEMORY_LIMIT;
            $wp_debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) ? 'On' : 'Off';
            $this_plugins_version = $sm_plugin_data['Name'] . ' ' . $sm_plugin_data['Version'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $additional_information = "===== Additional Information =====
                                       [E-Commerce Plugin: $ecom_plugin_version] =====
                                       [WP Version: $wp_version] =====
                                       [Admin URL: $admin_url] =====
                                       [PHP Version: $php_version] =====
                                       [WP Max Upload Size: $wp_max_upload_size] =====
                                       [Server Max Upload Size: $server_max_upload_size] =====
                                       [Server Post Max Size: $server_post_max_size] =====
                                       [WP Memory Limit: $wp_memory_limit] =====
                                       [WP Debug: $wp_debug] =====
                                       [" . $sm_plugin_data['Name'] . " Version: $this_plugins_version] =====
                                       [License Key: $sm_license_key]=====
                                       [IP Address: $ip_address] =====
                                      ";

            if( isset( $_POST['submit_query'] ) && $_POST['submit_query'] == "Send" ){


                // wp_mail( 'support@storeapps.org', 'subject', 'message' );
               $additional_info = ( isset( $_POST['additional_information'] ) && !empty( $_POST['additional_information'] ) ) ? ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['additional_information'] ) : $_POST['additional_information'] ) : '';
               $additional_info = str_replace( '=====', '<br />', $additional_info );
               $additional_info = str_replace( array( '[', ']' ), '', $additional_info );

               $headers = 'From: ';
               $headers .= ( isset( $_POST['client_name'] ) && !empty( $_POST['client_name'] ) ) ? ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['client_name'] ) : $_POST['client_name'] ) : '';
               $headers .= ' <' . ( ( function_exists( 'woocommerce_clean' ) ) ? woocommerce_clean( $_POST['client_email'] ) : $_POST['client_email'] ) . '>' . "\r\n";
               $headers .= 'MIME-Version: 1.0' . "\r\n";
               $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

               ob_start();
               if ( isset( $_POST['include_data'] ) && $_POST['include_data'] == 'yes' ) {
                   echo $additional_info . '<br /><br />';
                }
               // echo woocommerce_clean( nl2br($_POST['message']) );
               echo nl2br($_POST['message']) ;
               $message = ob_get_clean();
               if ( empty( $_POST['name'] ) ) {
                    wp_mail( 'support@storeapps.org', $_POST['subject'], $message, $headers );
                    header('Location: ' . $_SERVER['HTTP_REFERER'] );
                }
            } 


        ?>
        <!-- <form id="smart_manager_form_post_query" method="POST" action="http://www.storeapps.org/api/supportticket.php" enctype="multipart/form-data"> -->
        <form id="smart_manager_form_post_query" method="POST" action="" enctype="multipart/form-data">
            <script type="text/javascript">
                jQuery(function(){

                    //Code for handling the sizing of the thickbox w.r.to. Window size

                    jQuery(document).ready(function(){
               
                        var width = jQuery(window).width();
                        var H = jQuery(window).height();
                        var W = ( 720 < width ) ? 720 : width;

                        var adminbar_height = 0;

                        if ( jQuery('body.admin-bar').length )
                            adminbar_height = 28;

                        jQuery("#TB_window").css({"max-height": 390 +'px'});

                        ajaxContentW = W - 110;
                        ajaxContentH = H - 130 - adminbar_height;
                        jQuery("#TB_ajaxContent").css({"width": ajaxContentW +'px', "height": ajaxContentH +'px'});

                    });
                
                    jQuery(window).resize(function(){

                        var width = jQuery(window).width();
                        var H = jQuery(window).height();
                        var W = ( 720 < width ) ? 720 : width;

                        var adminbar_height = 0;

                        if ( jQuery('body.admin-bar').length )
                            adminbar_height = 28;

                        jQuery('#TB_window').css('margin-top', '');
                        jQuery("#TB_window").css({"max-height": 390 +'px',"top":48 +'px'});


                        ajaxContentW = W - 110;
                        ajaxContentH = H - 130 - adminbar_height;
                        jQuery("#TB_ajaxContent").css({"width": ajaxContentW +'px', "height": ajaxContentH +'px'});

                    });
           
                    jQuery('input#smart_manager_submit_query').click(function(e){
                        var error = false;

                        var client_name = jQuery('input#client_name').val();
                        if ( client_name == '' ) {
                            jQuery('input#client_name').css('border-color', 'red');
                            error = true;
                        } else {
                            jQuery('input#client_name').css('border-color', '');
                        }

                        var client_email = jQuery('input#client_email').val();
                        if ( client_email == '' ) {
                            jQuery('input#client_email').css('border-color', 'red');
                            error = true;
                        } else {
                            jQuery('input#client_email').css('border-color', '');
                        }

                        var message = jQuery('table#smart_manager_post_query_table textarea#message').val();
                        if ( message == '' ) {
                            jQuery('textarea#message').css('border-color', 'red');
                            error = true;
                        } else {
                            jQuery('textarea#message').css('border-color', '');
                        }

                        var subject = jQuery('table#smart_manager_post_query_table input#subject').val();
                        if ( subject == '' ) {
                            var msg_len = message.length;
                            
                            if (msg_len <= 50) {
                                subject = message;
                            }
                            else
                            {
                                subject = message.substr(0,50) + '...';
                            }
                            
                            jQuery('input#subject').val(subject);
                        } else {
                            jQuery('input#subject').css('border-color', '');
                        }

                        if ( error == true ) {
                            jQuery('label#error_message').text('* All fields are compulsory.');
                            e.preventDefault();
                        } else {
                            jQuery('label#error_message').text('');
                        }

                    });

                    jQuery('input,textarea').keyup(function(){
                        var value = jQuery(this).val();
                        if ( value.length > 0 ) {
                            jQuery(this).css('border-color', '');
                            jQuery('label#error_message').text('');
                        }
                    });

                });
            </script>
            <table id="smart_manager_post_query_table">
                <tr>
                    <td><label for="client_name"><?php _e('Name', $sm_text_domain); ?>*</label></td>
                    <td><input type="text" class="regular-text sm_text_field" id="client_name" name="client_name" value="<?php echo $customer_name; ?>"  autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;" /></td>
                </tr>
                <tr>
                    <td><label for="client_email"><?php _e('E-mail', $sm_text_domain); ?>*</label></td>
                    <td><input type="email" class="regular-text sm_text_field" id="client_email" name="client_email" value="<?php echo $customer_email; ?>"  autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;" /></td>
                </tr>
                <tr>
                    <td><label for="current_plugin"><?php _e('Product', $sm_text_domain); ?></label></td>
                    <td><input type="text" class="regular-text sm_text_field" id="current_plugin" name="current_plugin" value="<?php echo $this_plugins_version; ?>" readonly autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"/><input type="text" name="name" value="" style="display: none;" /></td>
                </tr>
              
                <tr>
                    <td><label for="subject"><?php _e('Subject', $sm_text_domain); ?></label></td>
                    <td><input type="text" class="regular-text sm_text_field" id="subject" name="subject" value="<?php echo ( !empty( $subject ) ) ? $subject : ''; ?>"  autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;" /></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; padding-top: 12px;"><label for="message"><?php _e('Message', $sm_text_domain); ?>*</label></td>
                    <td><textarea id="message" name="message" rows="10" cols="60" autocomplete="off" oncopy="return false;" onpaste="return false;" oncut="return false;"><?php echo ( !empty( $message ) ) ? $message : ''; ?></textarea></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; padding-top: 12px;"></td>
                    <td><input id="include_data" type="checkbox" name="include_data" value="yes" /> <label for="include_data"><?php echo __( 'Include plugins / environment details to help solve issue faster', $sm_text_domain ); ?></label></td>
                </tr>
                <tr>
                    <td></td>
                    <td><label id="error_message" style="color: red;"></label></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" class="button" id="smart_manager_submit_query" name="submit_query" value="Send" /></td>
                </tr>
            </table>
            <?php wp_nonce_field( 'storeapps-submit-query_sm' ); ?>
            <input type="hidden" name="license_key" value="<?php echo $sm_license_key; ?>" />
            <input type="hidden" name="sku" value="<?php echo $sm_sku; ?>" />
            <input type="hidden" class="hidden_field" name="ecom_plugin_version" value="<?php echo $ecom_plugin_version; ?>" />
            <input type="hidden" class="hidden_field" name="wp_version" value="<?php echo $wp_version; ?>" />
            <input type="hidden" class="hidden_field" name="admin_url" value="<?php echo $admin_url; ?>" />
            <input type="hidden" class="hidden_field" name="php_version" value="<?php echo $php_version; ?>" />
            <input type="hidden" class="hidden_field" name="wp_max_upload_size" value="<?php echo $wp_max_upload_size; ?>" />
            <input type="hidden" class="hidden_field" name="server_max_upload_size" value="<?php echo $server_max_upload_size; ?>" />
            <input type="hidden" class="hidden_field" name="server_post_max_size" value="<?php echo $server_post_max_size; ?>" />
            <input type="hidden" class="hidden_field" name="wp_memory_limit" value="<?php echo $wp_memory_limit; ?>" />
            <input type="hidden" class="hidden_field" name="wp_debug" value="<?php echo $wp_debug; ?>" />
            <input type="hidden" class="hidden_field" name="current_plugin" value="<?php echo $this_plugins_version; ?>" />
            <input type="hidden" class="hidden_field" name="ip_address" value="<?php echo $ip_address; ?>" />
            <input type="hidden" class="hidden_field" name="additional_information" value='<?php echo $additional_information; ?>' />
        </form>
    </div>
    <?php
}

function smart_settings_page() {
	global $sm_download_url, $wpdb;

	$is_pro_updated = smart_is_pro_updated ();	
	$sm_license_key    = smart_get_license_key();
	if (isset ( $_POST ['submit'] )) {
		$latest_version 	= smart_get_latest_version ();
		$sm_license_key		= $wpdb->_real_escape( trim( $_POST['license_key'] ) );
		$sm_post_url 	    = STORE_APPS_URL . 'wp-admin/admin-ajax.php?action=woocommerce_validate_serial_key&serial=' . urlencode($sm_license_key) . '&sku=sm';
		$sm_response_result = smart_get_sm_response ( $sm_post_url );
		if ($sm_license_key != '') {
			if ($sm_response_result->is_valid) {
				if ( is_multisite() ) {
					$delete_query = "DELETE FROM $wpdb->sitemeta WHERE meta_key = 'sm_license_key'";
					$wpdb->query ( $delete_query );
					$query  = "REPLACE INTO $wpdb->sitemeta (`meta_key`,`meta_value`) VALUES('sm_license_key','$sm_license_key')";
				} else {
					$query  = "REPLACE INTO `{$wpdb->prefix}options`(`option_name`,`option_value`) VALUES('sm_license_key','$sm_license_key')";
				}
				$result = $wpdb->query ( $query );
				$msg  = __('Your key is valid. Automatic Upgrades and support are now activated.',$sm_text_domain);
				smart_display_notice ( $msg );
			} else {
				smart_display_err ( $sm_response_result->msg );
			}
		} else {
			$msg = __('Please enter license key',$sm_text_domain);
			smart_display_err ( $msg );
		}
	}
	?>
</br>
<form method="post" action="">
<div class="wrap">
<div id="icon-smart-manager" class="icon32"><br/></div>
<h2><?php _e('Smart Manager Pro Settings',$sm_text_domain);?></h2>
<?php _e( "Your Smart Manager Pro license key is used to verify your support package, enable automatic updates and receive support.", $sm_text_domain ); ?> </div>
<br />
<?php _e('License key:',$sm_text_domain); ?> <input id="license_key" type="text" name="license_key" size="45"
	value="<?php echo $sm_license_key; ?>" /> <input class="button" type="submit" name="submit"
	value="<?php _e( 'Validate', $sm_text_domain ); ?>" /></form>
<div id="notification" name="notification"></div>
<?php
}

function smart_get_license_key() {
	global $wpdb;
	$key = '';

	if ( is_multisite() ) {
		$query = "SELECT meta_value FROM $wpdb->sitemeta WHERE meta_key = 'sm_license_key'";
	} else {
		$query = "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'sm_license_key'";
	}
	$records = $wpdb->get_results ( $query, ARRAY_A );
	if ( count($records) == 1 ) {
		$key = is_multisite() ? $records [0] ['meta_value'] : $records [0] ['option_value'];
	}
	return $key;
}

