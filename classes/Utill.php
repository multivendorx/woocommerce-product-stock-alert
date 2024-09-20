<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;

class Utill {

    /**
     * Function to console and debug errors.
     */
    public static function log( $str ) {
        $file = SM()->plugin_path . 'log/woo-stock-manager.log';

        if ( file_exists( $file ) ) {
            // Open the file to get existing content
            $str = var_export( $str, true );

            // Wp_remote_gate replacement required
            $current = file_get_contents( $file );

            if ( $current ) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            
            // Write the contents back to the file
            file_put_contents( $file, $current );
        }
    }

    /**
     * Get the settings arry. Non set value is replaced with default value.
     * @return array
     */
    public static function get_form_settings_array() {
        // Initialize the settings keys with default values
        $setting_keys = [
            'double_opt_in_success' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ),
            'shown_interest_text' => __( 'Thank you for your interest.', 'woocommerce-stock-manager' ),
            'alert_success' => __( 'Thank you for expressing interest in %product_title%. We will notify you once it is back in stock.', 'woocommerce-stock-manager' ),
            'alert_email_exist' => __( '%customer_email% is already registered for %product_title%.', 'woocommerce-stock-manager' ),
            'valid_email' => __( 'Please enter a valid email address.', 'woocommerce-stock-manager' ),
            'alert_unsubscribe_message' => __( '%customer_email% is successfully unsubscribed.', 'woocommerce-stock-manager' ),
            'email_placeholder_text' => __( 'Enter your email', 'woocommerce-stock-manager' ),
            'alert_text' => __( 'Receive notifications when back in stock.', 'woocommerce-stock-manager' ),
            'button_text' => __( 'Notify me', 'woocommerce-stock-manager' ),
            'unsubscribe_button_text' => __( 'Unsubscribe', 'woocommerce-stock-manager' ),
            'alert_text_color' => '', // Default value
            'button_background_color' => '', // Default value
            'button_border_color' => '', // Default value
            'button_text_color' => '', // Default value
            'button_background_color_onhover' => '', // Default value
            'button_text_color_onhover' => '', // Default value
            'button_border_color_onhover' => '', // Default value
            'button_font_size' => '', // Default value
            'button_border_size' => '', // Default value
            'button_border_radious' => '', // Default value
            'ban_email_domain_text' => __( 'This email domain is not allowed.', 'woocommerce-stock-manager' ),
            'ban_email_address_text' => __( 'This email address is banned.', 'woocommerce-stock-manager' ),
        ];
    
        $form_settings = [];
    
        foreach ( $setting_keys as $setting_key => $default_value ) {
            // Overwrite with actual settings from the database first
            $form_settings[ $setting_key ] = SM()->setting->get_setting( $setting_key, $default_value );
            
            // Register string using WPML's icl_register_string function if available
            if ( function_exists( 'icl_register_string' ) ) {
                icl_register_string( 'woocommerce-stock-manager', $setting_key, $form_settings[ $setting_key ] );
            }
            
            // Store registered or default value in settings array
            if ( function_exists( 'icl_t' ) ) {
                $form_settings[ $setting_key ] = icl_t( 'woocommerce-stock-manager', $setting_key, $form_settings[ $setting_key ] ) ;
            }
        }
        return $form_settings;
    }
    /**
     * Check pro plugin is acrive or not
     * @return bool
     */
    public static function is_pro_active() {
        return defined( 'STOCK_MANAGER_PRO_PLUGIN_VERSION' );
    }
}
