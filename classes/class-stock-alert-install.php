<?php
/**
 * Start schedule after plugin activation
 *
 */

class Woo_Product_Stock_Alert_Install {
    
    public function __construct() {
        $this->woo_stock_alert_data_migrate();
        $this->start_cron_job();
    }

    function start_cron_job() {
        wp_clear_scheduled_hook('woo_stock_alert_start_notification_cron_job');
        wp_schedule_event(time(), 'hourly', 'woo_stock_alert_start_notification_cron_job');
        update_option('woo_product_stock_alert_cron_start', 1);
    }

    function woo_stock_alert_data_migrate() {
        global $Woo_Product_Stock_Alert;
        $previous_version = get_option("woo_product_stock_alert_version", "");
        $current_version = $Woo_Product_Stock_Alert->version;

        if($previous_version <= "2.2.0") {
            $all_products = wc_get_products([
                'status'    => 'publish',
                'limit' => -1, 
            ]);
    
            foreach($all_products as $key => $product) {
                $current_product_ids = get_related_product($product);
                foreach($current_product_ids as $product_id) {
                    $product_subscribers = get_post_meta($product_id, '_product_subscriber', true);
                    if($product_subscribers && !empty($product_subscribers)) {
                        foreach($product_subscribers as $subscriber_email) {
                            if(! $Woo_Product_Stock_Alert->subscriber->is_already_subscribed($subscriber_email, $product_id)) {
                                $Woo_Product_Stock_Alert->subscriber->customer_stock_alert_subscribe($subscriber_email, $product_id);
                            }
                        }
                    }
                    delete_post_meta($product_id, '_product_subscriber');
                }
            }
        }

        if($previous_version <= "2.3.0") {
            if (!get_option('woo_stock_alert_general_tab_settings')) {
                $general_tab_settings = [
                    'double_opt_in_success' => __('Kindly check your inbox to confirm the subscription.', 'woocommerce-product-stock-alert'),
                    'shown_interest_text' => __('Product in demand: %no_of_subscribed% waiting.', 'woocommerce-product-stock-alert'),
                    'additional_alert_email' => get_option('admin_email')
                ];
                update_option('woo_stock_alert_general_tab_settings', $general_tab_settings);
            }

            if (!get_option('woo_stock_alert_form_submission_tab_settings')) {
                $form_submission_tab_settings = [
                    'alert_success' => __('Thank you for expressing interest in %product_title%. We will notify you via email once it is back in stock.', 'woocommerce-product-stock-alert'),
                    'alert_email_exist' => __('%customer_email% is already registered for %product_title%. Please attempt a different email address.', 'woocommerce-product-stock-alert'),
                    'valid_email' => __('Please enter a valid email ID and try again.', 'woocommerce-product-stock-alert'),
                    'alert_unsubscribe_message' => __('%customer_email% is successfully unregistered.', 'woocommerce-product-stock-alert')
                ];
                update_option('woo_stock_alert_form_submission_tab_settings', $form_submission_tab_settings);
            }

            if(!get_option('woo_stock_alert_form_customization_tab_settings')) {
                $form_customization_tab_settings = [
                    'email_placeholder_text' => __('Enter your email', 'woocommerce-product-stock-alert'),
                    'alert_text' => __('Receive in-stock notifications for this.', 'woocommerce-product-stock-alert'),
                    'button_text' => __('Notify me', 'woocommerce-product-stock-alert'),
                    'unsubscribe_button_text' => __('Unsubscribe', 'woocommerce-product-stock-alert'),
                    'alert_text_color' => '',
                    'button_background_color' => '',
                    'button_border_color' => '',
                    'button_text_color' => '',
                    'button_background_color_onhover' => '',
                    'button_text_color_onhover' => '',
                    'button_border_color_onhover' => '',
                    'button_font_size' => '',
                    'button_border_radious' => '',
                    'button_border_size' => ''
                ];
                update_option('woo_stock_alert_form_customization_tab_settings', $form_customization_tab_settings);
            }

            if(!get_option('woo_stock_alert_email_tab_settings')) {
                $email_tab_settings = [
                    'ban_email_domain_text' => __('This email domain is ban in our site, kindly use another email domain.', 'woocommerce-product-stock-alert'),
                    'ban_email_address_text' => __('This email address is ban in our site, kindly use another email address.', 'woocommerce-product-stock-alert')
                ];
                update_option('woo_stock_alert_email_tab_settings', $email_tab_settings);
            }

            if(!get_option('woo_stock_alert_mailchimp_tab_settings')) {
                $mailchimp_tab_settings = [
                    'is_mailchimp_enable' => false,
                    'mailchimp_api' => null,
                    'get_mailchimp_list_button' => null,
                    'selected_mailchimp_list' => null
                ];
                update_option('woo_stock_alert_mailchimp_tab_settings', $email_tab_settings);
            }
        }

        update_option('woo_product_stock_alert_version', $current_version);
    }
}