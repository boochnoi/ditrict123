<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="fmera-wrap" class="wrap fmera-settings">
    <h2><?php _e( 'Registration Attribute Settings', 'fmera' ); ?></h2>
    <?php 
        global $wpdb;
        settings_errors();

        //print_r($this->module_settings);
    ?>

    <form id="fmera-settings-form" method="post" action="options.php" accept-charset="utf-8">
        <h2></h2>
        
        <div id="info">
            <div id="general" class="hide">
                <br /><br />
                <?php _e('Enter your settings below:', 'fmera'); ?>

                <table class="form-table">
                    <tbody>

                        <tr>
                            <th scope="row">
                                <?php _e('Account Section Title:','fmera'); ?>
                                <p class="description">(<?php _e('Main heading of the account section.', 'fmera'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php if(isset($this->module_settings['account_title'])!='') echo esc_attr( $this->module_settings['account_title'] ); ?>" name="fmera_module[account_title]" placeholder="<?php _e( 'Account Section Title', 'fmera' ); ?>" class="textinput" id="fmera-account-title" />
                            </td>
                        </tr> 
                        
                        <tr>
                            <th scope="row">
                                <?php _e('Profile Section Title:','fmera'); ?>
                                <p class="description">(<?php _e('Main heading of the profile section.', 'fmera'); ?>)</p>
                            </th>
                            <td>
                                <input type="text" value="<?php if(isset($this->module_settings['profile_title'])!='') echo esc_attr( $this->module_settings['profile_title'] ); ?>" name="fmera_module[profile_title]" placeholder="<?php _e( 'Profile Section Title', 'fmera' ); ?>" class="textinput" id="fmera-profile-title" />
                            </td>
                        </tr> 

                        

                    </tbody>
                </table>
            </div>
            
            <p class="submit">
                <input type="submit" value="<?php _e( 'Save Changes', 'fmera' ); ?>" class="button-primary" name="fmera-save-settings" id="fmera-save-settings">
            <?php settings_fields( 'fmera_settings' ); ?>
            </p>
        </div>
    </form>


</div>


