<?php
/**
 * Start schedule after plugin activation
 *
 */

class WOO_Product_Stock_Alert_Install {
    
    public function __construct() {
        if (!get_option('woo_stock_alert_data_migrate')) {
            $this->woo_stock_alert_option_migration();
        }

        if (!get_option('woo_stock_alert_admin_settings_default')) {
            $this->woo_stock_alert_admin_settings_default();
        }
        
        if (!get_option('woo_product_stock_alert_activate')) {
            $this->stock_alert_activate();
        }
        
        if (get_option('woo_product_stock_alert_installed')) {
            $this->start_cron_job();
        }

        if (!get_option('_is_updated_woo_product_alert_settings')) {
            $this->woo_stock_alert_older_settings_migration();
        }

        if (!get_option('_is_updated_woo_product_alert_database')) {
            $this->woo_stock_alert_older_data_migration();
        }
    }

    function woo_stock_alert_admin_settings_default() {
        $admin_email = get_option('admin_email');
        $default_massages = get_woo_default_massages();
        if (get_option('woo_stock_alert_general_tab_settings')) {
            $genaral_settings = get_option('woo_stock_alert_general_tab_settings');
            if (!get_woo_product_alert_plugin_settings('is_remove_admin_email')) {
                $additional_email_settings = get_woo_product_alert_plugin_settings('additional_alert_email');
                if ($additional_email_settings && !empty($additional_email_settings)) {
                    $genaral_settings['additional_alert_email'] = $admin_email . ', ' . $additional_email_settings;
                } else {
                    $genaral_settings['additional_alert_email'] = $admin_email;
                }
            }
            if (empty(get_woo_product_alert_plugin_settings('double_opt_in_success'))) {
                $genaral_settings['double_opt_in_success'] = $default_massages['double_opt_in_success'];
            }
            if (empty(get_woo_product_alert_plugin_settings('shown_interest_text'))) {
                $genaral_settings['shown_interest_text'] = $default_massages['shown_interest_text'];
            }
            update_option('woo_stock_alert_general_tab_settings', $genaral_settings);
        }

        if (get_option('woo_stock_alert_form_submission_tab_settings')) {
            $form_submission_settings = get_option('woo_stock_alert_form_submission_tab_settings');
            if (empty(get_woo_product_alert_plugin_settings('alert_success'))) {
                $form_submission_settings['alert_success'] = $default_massages['alert_success'];
            }
            if (empty(get_woo_product_alert_plugin_settings('alert_email_exist'))) {
                $form_submission_settings['alert_email_exist'] = $default_massages['alert_email_exist'];
            }
            if (empty(get_woo_product_alert_plugin_settings('valid_email'))) {
                $form_submission_settings['valid_email'] = $default_massages['valid_email'];
            }
            if (empty(get_woo_product_alert_plugin_settings('alert_unsubscribe_message'))) {
                $form_submission_settings['alert_unsubscribe_message'] = $default_massages['alert_unsubscribe_message'];
            }
            update_option('woo_stock_alert_form_submission_tab_settings', $form_submission_settings);
        }

        if (get_option('woo_stock_alert_email_tab_settings')) {
            $form_submission_settings = get_option('woo_stock_alert_email_tab_settings');
            if (empty(get_woo_product_alert_plugin_settings('ban_email_domain_text'))) {
                $form_submission_settings['ban_email_domain_text'] = $default_massages['ban_email_domain_text'];
            }
            if (empty(get_woo_product_alert_plugin_settings('ban_email_address_text'))) {
                $form_submission_settings['ban_email_address_text'] = $default_massages['ban_email_address_text'];
            }
            update_option('woo_stock_alert_email_tab_settings', $form_submission_settings);
        }

        update_option('woo_stock_alert_admin_settings_default', true);
    }

    function woo_stock_alert_option_migration() {
        //cron
        wp_clear_scheduled_hook('dc_start_stock_alert');

        if (get_option('dc_product_stock_alert_activate')) {
            update_option('woo_product_stock_alert_activate', true);
        }

        if (get_option('dc_product_stock_alert_installed')) {
            update_option('woo_product_stock_alert_installed', true);
        }

        if (get_option('_is_updated_woo_product_alert_settings')) {
            update_option('_is_updated_woo_product_alert_settings', true);
        }

        if (get_option('_is_updated_woo_product_alert_database')) {
            update_option('_is_updated_woo_product_alert_database', true);
        }

        if (get_option('dc_product_stock_alert_cron_start')) {
            update_option('woo_product_stock_alert_cron_start', 1);
        }

        if (get_option('mvx_woo_stock_alert_general_tab_settings')) {
            $genaral_option = get_option('mvx_woo_stock_alert_general_tab_settings');
            update_option('woo_stock_alert_general_tab_settings', $genaral_option);
        }

        if (get_option('mvx_woo_stock_alert_form_customization_tab_settings')) {
            $form_option = get_option('mvx_woo_stock_alert_form_customization_tab_settings');
            update_option('woo_stock_alert_form_customization_tab_settings', $form_option);
        }

        if (get_option('mvx_woo_stock_alert_form_submission_tab_settings')) {
            $form_submit_option = get_option('mvx_woo_stock_alert_form_submission_tab_settings');
            update_option('woo_stock_alert_form_submission_tab_settings', $form_submit_option);
        }
        update_option('woo_stock_alert_data_migrate', true);
    }

    /*
     * This function migrate older subscription data
     */
    function woo_stock_alert_older_data_migration() {
        wp_schedule_single_event(time(), 'woo_stock_alert_older_data_migration');
    }
    
    /*
     * This function will start the cron job
     */
    function start_cron_job() {
        wp_clear_scheduled_hook('woo_start_stock_alert');    
        wp_schedule_event(time(), 'hourly', 'woo_start_stock_alert');
        update_option('woo_product_stock_alert_cron_start', 1);
    }

    function stock_alert_activate() {
        $admin_email = get_option('admin_email');
        $default_massages = get_woo_default_massages();
        $stock_alert_general_settings = array(
            'additional_alert_email'    => $admin_email,
            'double_opt_in_success'     => $default_massages['double_opt_in_success'],
            'shown_interest_text'       => $default_massages['shown_interest_text'],
        );

        if (!get_option('woo_stock_alert_general_tab_settings')) {
            update_option('woo_stock_alert_general_tab_settings', $stock_alert_general_settings);
        }

        $stock_alert_form_submission_settings = array(
            'alert_success'             => $default_massages['alert_success'],
            'alert_email_exist'         => $default_massages['alert_email_exist'],
            'valid_email'               => $default_massages['valid_email'],
            'alert_unsubscribe_message' => $default_massages['alert_unsubscribe_message'],
        );

        if (!get_option('woo_stock_alert_form_submission_tab_settings')) {
            update_option('woo_stock_alert_form_submission_tab_settings', $stock_alert_form_submission_settings);
        }

        $stock_alert_email_settings = array(
            'ban_email_domain_text'     => $default_massages['ban_email_domain_text'],
            'ban_email_address_text'    => $default_massages['ban_email_address_text'],
        );

        if (!get_option('woo_stock_alert_email_tab_settings')) {
            update_option('woo_stock_alert_email_tab_settings', $stock_alert_email_settings);
        }
        update_option('woo_product_stock_alert_activate', 1);
    }

    /*
     * This function will migrate older settings
     */
    function woo_stock_alert_older_settings_migration() {
        if (!get_option('_is_updated_woo_product_alert_settings')) {
            $genaral_settings = $customization_settings = $submit_settings = [];
            
            if (get_woo_product_alert_old_plugin_settings('is_enable_backorders') &&  get_woo_product_alert_old_plugin_settings('is_enable_backorders') == 'Enable') {
                $genaral_settings['is_enable_backorders'] = array('is_enable_backorders');
            }
            if (get_woo_product_alert_old_plugin_settings('is_enable_no_interest') &&  get_woo_product_alert_old_plugin_settings('is_enable_no_interest') == 'Enable') {
                $genaral_settings['is_enable_no_interest'] = array('is_enable_no_interest');
            }
            if (get_woo_product_alert_old_plugin_settings('shown_interest_text')) {
                $genaral_settings['shown_interest_text'] = get_woo_product_alert_old_plugin_settings('shown_interest_text');
            }
            if (get_woo_product_alert_old_plugin_settings('is_double_optin') &&  get_woo_product_alert_old_plugin_settings('is_double_optin') == 'Enable') {
                $genaral_settings['is_double_optin'] = array('is_double_optin');
            }

            if (get_woo_product_alert_old_plugin_settings('is_remove_admin_email') &&  get_woo_product_alert_old_plugin_settings('is_remove_admin_email') == 'Enable') {
                $genaral_settings['is_remove_admin_email'] = array('is_remove_admin_email');
            }

            if (get_woo_product_alert_old_plugin_settings('additional_alert_email')) {
                $genaral_settings['additional_alert_email'] = get_woo_product_alert_old_plugin_settings('additional_alert_email');
            }

            if ($genaral_settings) {
                save_woo_product_alert_settings('woo_stock_alert_general_tab_settings', $genaral_settings);
            }

            if (get_woo_product_alert_old_plugin_settings('alert_text')) {
                $customization_settings['alert_text'] = get_woo_product_alert_old_plugin_settings('alert_text');
            }

            if (get_woo_product_alert_old_plugin_settings('alert_text_color')) {
                $customization_settings['alert_text_color'] = get_woo_product_alert_old_plugin_settings('alert_text_color');
            }

            if (get_woo_product_alert_old_plugin_settings('button_text')) {
                $customization_settings['button_text'] = get_woo_product_alert_old_plugin_settings('button_text');
            }

            if (get_woo_product_alert_old_plugin_settings('unsubscribe_button_text')) {
                $customization_settings['unsubscribe_button_text'] = get_woo_product_alert_old_plugin_settings('unsubscribe_button_text');
            }

            if (get_woo_product_alert_old_plugin_settings('button_background_color')) {
                $customization_settings['button_background_color'] = get_woo_product_alert_old_plugin_settings('button_background_color');
            }

            if (get_woo_product_alert_old_plugin_settings('button_border_color')) {
                $customization_settings['button_border_color'] = get_woo_product_alert_old_plugin_settings('button_border_color');
            }

            if (get_woo_product_alert_old_plugin_settings('button_text_color')) {
                $customization_settings['button_text_color'] = get_woo_product_alert_old_plugin_settings('button_text_color');
            }

            if (get_woo_product_alert_old_plugin_settings('button_background_color_onhover')) {
                $customization_settings['button_background_color_onhover'] = get_woo_product_alert_old_plugin_settings('button_background_color_onhover');
            }

            if (get_woo_product_alert_old_plugin_settings('button_border_color_onhover')) {
                $customization_settings['button_border_color_onhover'] = get_woo_product_alert_old_plugin_settings('button_border_color_onhover');
            }

            if (get_woo_product_alert_old_plugin_settings('button_text_color_onhover')) {
                $customization_settings['button_text_color_onhover'] = get_woo_product_alert_old_plugin_settings('button_text_color_onhover');
            }

            if (get_woo_product_alert_old_plugin_settings('button_font_size')) {
                $customization_settings['button_font_size'] = get_woo_product_alert_old_plugin_settings('button_font_size');
            }
            if ($customization_settings) {
                save_woo_product_alert_settings('woo_stock_alert_form_customization_tab_settings', $customization_settings);
            }

            if (get_woo_product_alert_old_plugin_settings('alert_success')) {
                $submit_settings['alert_success'] = get_woo_product_alert_old_plugin_settings('alert_success');
            }
            if (get_woo_product_alert_old_plugin_settings('alert_email_exist')) {
                $submit_settings['alert_email_exist'] = get_woo_product_alert_old_plugin_settings('alert_email_exist');
            }
            if (get_woo_product_alert_old_plugin_settings('valid_email')) {
                $submit_settings['valid_email'] = get_woo_product_alert_old_plugin_settings('valid_email');
            }
            if (get_woo_product_alert_old_plugin_settings('alert_unsubscribe_message')) {
                $submit_settings['alert_unsubscribe_message'] = get_woo_product_alert_old_plugin_settings('alert_unsubscribe_message');    
            }
            if ($submit_settings) {
                save_woo_product_alert_settings('woo_stock_alert_form_submission_tab_settings', $submit_settings);
            }

            update_option('_is_updated_woo_product_alert_settings', true);
        }
    }
}