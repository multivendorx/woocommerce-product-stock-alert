<?php
/**
 * Start schedule after plugin activation
 *
 */

class WOO_Product_Stock_Alert_Install {
    
    public function __construct() {
        
        if (!get_option('dc_product_stock_alert_activate')) {
            if ($this->stock_alert_activate() == 'true') {
                update_option('dc_product_stock_alert_activate', 1);
            }
        }
        
        if (get_option( 'dc_product_stock_alert_installed' )) :
            $this->start_cron_job();
        endif;

        if (!get_option('_is_updated_mvx_product_alert_settings')) {
            $this->mvx_stock_alert_older_settings_migration();
        }

        if (!get_option('_is_updated_mvx_product_alert_database')) {
            $this->mvx_stock_alert_older_data_migration();
        }
    }

    /*
     * This function migrate older subscription data
     */
    function mvx_stock_alert_older_data_migration() {
        wp_schedule_single_event( time(), 'mvx_stock_alert_older_data_migration' );
    }
    
    /*
     * This function will start the cron job
     */
    function start_cron_job() {
        wp_clear_scheduled_hook('dc_start_stock_alert');    
        wp_schedule_event( time(), 'hourly', 'dc_start_stock_alert' );
        update_option( 'dc_product_stock_alert_cron_start', 1 );
    }

    function stock_alert_activate() {
        global $WOO_Product_Stock_Alert;
        $stock_alert_settings = array();

        if( !get_option('mvx_woo_stock_alert_general_tab_settings') ) {
            if( update_option( 'mvx_woo_stock_alert_general_tab_settings', $stock_alert_settings ) ) {
                return 'true';
            }
        }
    }

    /*
     * This function will migrate older settings
     */
    function mvx_stock_alert_older_settings_migration() {
        if (!get_option('_is_updated_mvx_product_alert_settings')) {
            $genaral_settings = $customization_settings = $submit_settings = [];
            
            if ( get_mvx_product_alert_old_plugin_settings('is_enable_backorders') &&  get_mvx_product_alert_old_plugin_settings('is_enable_backorders') == 'Enable' ) {
                $genaral_settings['is_enable_backorders'] = array('is_enable_backorders');
            }
            if ( get_mvx_product_alert_old_plugin_settings('is_enable_no_interest') &&  get_mvx_product_alert_old_plugin_settings('is_enable_no_interest') == 'Enable' ) {
                $genaral_settings['is_enable_no_interest'] = array('is_enable_no_interest');
            }
            if ( get_mvx_product_alert_old_plugin_settings('shown_interest_text') ) {
                $genaral_settings['shown_interest_text'] = get_mvx_product_alert_old_plugin_settings('shown_interest_text');
            }
            if ( get_mvx_product_alert_old_plugin_settings('is_double_optin') &&  get_mvx_product_alert_old_plugin_settings('is_double_optin') == 'Enable' ) {
                $genaral_settings['is_double_optin'] = array('is_double_optin');
            }

            if ( get_mvx_product_alert_old_plugin_settings('is_remove_admin_email') &&  get_mvx_product_alert_old_plugin_settings('is_remove_admin_email') == 'Enable' ) {
                $genaral_settings['is_remove_admin_email'] = array('is_remove_admin_email');
            }

            if ( get_mvx_product_alert_old_plugin_settings('additional_alert_email') ) {
                $genaral_settings['additional_alert_email'] = get_mvx_product_alert_old_plugin_settings('additional_alert_email');
            }
            if ($genaral_settings) {
                save_mvx_product_alert_settings('mvx_woo_stock_alert_general_tab_settings', $genaral_settings);
            }

            if ( get_mvx_product_alert_old_plugin_settings('alert_text') ) {
                $customization_settings['alert_text'] = get_mvx_product_alert_old_plugin_settings('alert_text');
            }

            if ( get_mvx_product_alert_old_plugin_settings('alert_text_color') ) {
                $customization_settings['alert_text_color'] = get_mvx_product_alert_old_plugin_settings('alert_text_color');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_text') ) {
                $customization_settings['button_text'] = get_mvx_product_alert_old_plugin_settings('button_text');
            }

            if ( get_mvx_product_alert_old_plugin_settings('unsubscribe_button_text') ) {
                $customization_settings['unsubscribe_button_text'] = get_mvx_product_alert_old_plugin_settings('unsubscribe_button_text');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_background_color') ) {
                $customization_settings['button_background_color'] = get_mvx_product_alert_old_plugin_settings('button_background_color');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_border_color') ) {
                $customization_settings['button_border_color'] = get_mvx_product_alert_old_plugin_settings('button_border_color');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_text_color') ) {
                $customization_settings['button_text_color'] = get_mvx_product_alert_old_plugin_settings('button_text_color');

            }

            if ( get_mvx_product_alert_old_plugin_settings('button_background_color_onhover') ) {
                $customization_settings['button_background_color_onhover'] = get_mvx_product_alert_old_plugin_settings('button_background_color_onhover');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_border_color_onhover') ) {
                $customization_settings['button_border_color_onhover'] = get_mvx_product_alert_old_plugin_settings('button_border_color_onhover');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_text_color_onhover') ) {
                $customization_settings['button_text_color_onhover'] = get_mvx_product_alert_old_plugin_settings('button_text_color_onhover');
            }

            if ( get_mvx_product_alert_old_plugin_settings('button_font_size') ) {
                $customization_settings['button_font_size'] = get_mvx_product_alert_old_plugin_settings('button_font_size');
            }
            if ( $customization_settings ) {
                save_mvx_product_alert_settings('mvx_woo_stock_alert_form_customization_tab_settings', $customization_settings);
            }

            if ( get_mvx_product_alert_old_plugin_settings('alert_success') ) {
                $submit_settings['alert_success'] = get_mvx_product_alert_old_plugin_settings('alert_success');
            }
            if ( get_mvx_product_alert_old_plugin_settings('alert_email_exist') ) {
                $submit_settings['alert_email_exist'] = get_mvx_product_alert_old_plugin_settings('alert_email_exist');
            }
            if ( get_mvx_product_alert_old_plugin_settings('valid_email') ) {
                $submit_settings['valid_email'] = get_mvx_product_alert_old_plugin_settings('valid_email');
            }
            if ( get_mvx_product_alert_old_plugin_settings('alert_unsubscribe_message') ) {
                $submit_settings['alert_unsubscribe_message'] = get_mvx_product_alert_old_plugin_settings('alert_unsubscribe_message');    
            }
            if ($submit_settings) {
                save_mvx_product_alert_settings('mvx_woo_stock_alert_form_submission_tab_settings', $submit_settings);
            }

            update_option('_is_updated_mvx_product_alert_settings', true);
        }
    }
}