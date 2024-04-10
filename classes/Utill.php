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
        return [
            'double_opt_in_success'           => SM()->setting->get_setting( 'double_opt_in_success' ),
            'shown_interest_text'             => SM()->setting->get_setting( 'shown_interest_text' ),
            'alert_success'                   => SM()->setting->get_setting( 'alert_success' ),
            'alert_email_exist'               => SM()->setting->get_setting( 'alert_email_exist' ),
            'valid_email'                     => SM()->setting->get_setting( 'valid_email' ),
            'alert_unsubscribe_message'       => SM()->setting->get_setting( 'alert_unsubscribe_message' ),
            'email_placeholder_text'          => SM()->setting->get_setting( 'email_placeholder_text' ),
            'alert_text'                      => SM()->setting->get_setting( 'alert_text' ),
            'button_text'                     => SM()->setting->get_setting( 'button_text' ),
            'unsubscribe_button_text'         => SM()->setting->get_setting( 'unsubscribe_button_text' ),
            'alert_text_color'                => SM()->setting->get_setting( 'alert_text_color' ),
            'button_background_color'         => SM()->setting->get_setting( 'button_background_color' ),
            'button_border_color'             => SM()->setting->get_setting( 'button_border_color' ),
            'button_text_color'               => SM()->setting->get_setting( 'button_text_color' ),
            'button_background_color_onhover' => SM()->setting->get_setting( 'button_background_color_onhover' ),
            'button_text_color_onhover'       => SM()->setting->get_setting( 'button_text_color_onhover' ),
            'button_border_color_onhover'     => SM()->setting->get_setting( 'button_border_color_onhover' ),
            'button_font_size'                => SM()->setting->get_setting( 'button_font_size' ),
            'button_border_size'              => SM()->setting->get_setting( 'button_border_size' ),
            'button_border_radious'           => SM()->setting->get_setting( 'button_border_radious' ),
            'ban_email_domain_text'           => SM()->setting->get_setting( 'ban_email_domain_text' ),
            'ban_email_address_text'          => SM()->setting->get_setting( 'ban_email_address_text' ),
        ];
    }
} 