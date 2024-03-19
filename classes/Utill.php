<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Utill {

    /**
     * Function to console and debug errors.
     */
    public static function log( $str ) {
        $file = SM( ) -> plugin_path . 'log/woo-stock-manager.log';
        if ( file_exists( $file ) ) {
            // Open the file to get existing content
            $str = var_export( $str, true );
            $current = wp_remote_get( $file );
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
    public static function get_form_settings_array( ) {
        $general_tab_settings = get_option( 'woo_stock_manager_general_tab_settings', [ ] );
        $form_customization_tab_settings = get_option( 'woo_stock_manager_form_customization_tab_settings', [ ] );
        $form_submission_tab_settings = get_option( 'woo_stock_manager_form_submission_tab_settings', [ ] );
        $email_tab_settings = get_option( 'woo_stock_manager_email_tab_settings', [ ] );
        
        return [ 
            'double_opt_in_success'             => $general_tab_settings[ 'double_opt_in_success' ] ?? '', 
            'shown_interest_text'               => $general_tab_settings[ 'shown_interest_text' ] ?? '', 
            'alert_success'                     => $form_submission_tab_settings[ 'alert_success' ] ?? '', 
            'alert_email_exist'                 => $form_submission_tab_settings[ 'alert_email_exist' ] ?? '', 
            'valid_email'                       => $form_submission_tab_settings[ 'valid_email' ] ?? '', 
            'alert_unsubscribe_message'         => $form_submission_tab_settings[ 'alert_unsubscribe_message' ] ?? '', 
            'email_placeholder_text'            => $form_customization_tab_settings[ 'email_placeholder_text' ] ?? '', 
            'alert_text'                        => $form_customization_tab_settings[ 'alert_text' ] ?? '', 
            'button_text'                       => $form_customization_tab_settings[ 'button_text' ] ?? '', 
            'unsubscribe_button_text'           => $form_customization_tab_settings[ 'unsubscribe_button_text' ] ?? '', 
            'alert_text_color'                  => $form_customization_tab_settings[ 'alert_text_color' ] ?? '', 
            'button_background_color'           => $form_customization_tab_settings[ 'button_background_color' ] ?? '', 
            'button_border_color'               => $form_customization_tab_settings[ 'button_border_color' ] ?? '', 
            'button_text_color'                 => $form_customization_tab_settings[ 'button_text_color' ] ?? '', 
            'button_background_color_onhover'   => $form_customization_tab_settings[ 'button_background_color_onhover' ] ?? '', 
            'button_text_color_onhover'         => $form_customization_tab_settings[ 'button_text_color_onhover' ] ?? '', 
            'button_border_color_onhover'       => $form_customization_tab_settings[ 'button_border_color_onhover' ] ?? '', 
            'button_font_size'                  => $form_customization_tab_settings[ 'button_font_size' ] ?? '', 
            'button_border_size'                => $form_customization_tab_settings[ 'button_border_size' ] ?? '', 
            'button_border_radious'             => $form_customization_tab_settings[ 'button_border_radious' ] ?? '', 
            'ban_email_domain_text'             => $email_tab_settings[ 'ban_email_domain_text' ] ?? '', 
            'ban_email_address_text'            => $email_tab_settings[ 'ban_email_address_text' ] ?? '',
        ];
    }
} 