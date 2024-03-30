<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;
/**
 * Start schedule after plugin activation
 *
 */

class Install {
    
    public function __construct() {
        $this->create_database_table();
        $this->stock_manager_data_migrate();
        $this->start_cron_job();
    }

    public function create_database_table() {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "stockalert_subscribers` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `product_id` bigint(20) NOT NULL,
                `email` varchar(50) NOT NULL,
                `status` varchar(20) NOT NULL,
                `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $collate;"
        );
    }

    /**
     * Function that schedule hook for notification corn job.
     * @return void
     */
    function start_cron_job() {
        wp_clear_scheduled_hook( 'woo_stock_manager_start_notification_cron_job' );
        wp_schedule_event( time(), 'hourly', 'woo_stock_manager_start_notification_cron_job' );
        update_option( 'woo_stock_manager_cron_start', true );
    }

    /**
     * Data migration function. Run on installation time.
     * @return void
     */
    function stock_manager_data_migrate() {
        $current_version = SM()->version;
        $previous_version = get_option( "woo_stock_manager_version" );

        // Used to check the plugin version before 2.1.0
        $dc_was_installed = get_option( 'dc_product_stock_alert_activate' );
        // Used to check the plugin version before 2.3.0
        $woo_was_installed = get_option( 'woo_product_stock_alert_activate' );

        // Default messages for settings array.
        // Those will modify if previous settings was set.
        $general_settings = [ 
            'is_enable_backorders' => false, 
            'is_enable_no_interest' => false, 
            'is_double_optin' => false, 
            'is_remove_admin_email' => false, 
            'double_opt_in_success' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ), 
            'shown_interest_text' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ), 
            'additional_alert_email' => get_option( 'admin_email' )
        ];
        $customization_settings = [ 
            'email_placeholder_text' => __( 'Enter your email', 'woocommerce-stock-manager' ), 
            'alert_text' => __( 'Receive in-stock notifications for this.', 'woocommerce-stock-manager' ), 
            'button_text' => __( 'Notify me', 'woocommerce-stock-manager' ), 
            'unsubscribe_button_text' => __( 'Unsubscribe', 'woocommerce-stock-manager' ), 
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
        $submit_settings = [ 
            'alert_success'  => __( 'Thank you for expressing interest in %product_title%. We will notify you via email once it is back in stock.', 'woocommerce-stock-manager' ), 
			// Translators: This message display already registered user to display already registered message
            'alert_email_exist' => __( '%customer_email% is already registered for %product_title%. Please attempt a different email address.', 'woocommerce-stock-manager' ), 
            'valid_email' => __( 'Please enter a valid email ID and try again.', 'woocommerce-stock-manager' ), 
			// Translators: This message display user sucessfully unregistered
            'alert_unsubscribe_message' => __( '%customer_email% is successfully unregistered.', 'woocommerce-stock-manager' ), 
        ];
        $email_settings = [ 
            'ban_email_domain_text' => __( 'This email domain is ban in our site, kindly use another email domain.', 'woocommerce-stock-manager' ), 
            'ban_email_address_text' => __( 'This email address is ban in our site, kindly use another email address.', 'woocommerce-stock-manager' )
        ];

        // Equevelent to check plugin version <= 2.3.0
        if ( $dc_was_installed || $woo_was_installed ) {
            $all_product_ids = get_posts( [ 
                'post_type'     => 'product', 
                'post_status'   => 'publish', 
                'fields'        => 'ids', 
	            'meta_query'    => [ 
                    [ 
                        'key'     => '_product_subscriber', 
                        'compare' => 'EXISTS', 
                    ], 
                ], 
            ] );
            
            // Database migration for subscriber data before version 2.3.0
            foreach( $all_product_ids as $product_id ) {
                $current_product_ids = Subscriber::get_related_product( wc_get_product( $product_id ) );
                foreach( $current_product_ids as $product_id ) {
                    $product_subscribers = get_post_meta( $product_id, '_product_subscriber', true );
                    if ( $product_subscribers && !empty( $product_subscribers ) ) {
                        foreach( $product_subscribers as $subscriber_email ) {
                            Subscriber::subscribe_user( $subscriber_email, $product_id );
                        }
                    }
                    delete_post_meta( $product_id, '_product_subscriber' );
                }
            }

            // Settings array for version upto 2.0.0
            $dc_plugin_settings = get_option( 'dc_woo_product_stock_alert_general_settings_name' );
            
            // Settings array for version from 2.1.0 to 2.2.0
            $mvx_general_tab_settings = get_option( 'mvx_woo_stock_alert_general_tab_settings' );
            $mvx_customization_tab_settings = get_option( 'mvx_woo_stock_alert_form_customization_tab_settings' );
            $mvx_submition_tab_settings = get_option( 'mvx_woo_stock_alert_form_submission_tab_settings' );
            
            if ( $dc_plugin_settings )
                delete_option( 'dc_woo_product_stock_alert_general_settings_name' );
            if ( $mvx_general_tab_settings )
                delete_option( 'mvx_woo_stock_alert_general_tab_settings' );
            if ( $mvx_customization_tab_settings )
                delete_option( 'mvx_woo_stock_alert_form_customization_tab_settings' );
            if ( $mvx_submition_tab_settings )
                delete_option( 'mvx_woo_stock_alert_form_submission_tab_settings' );

            // Settings arrays for version 2.3.0, 
            // For version 2.3.0 'woo_product_stock_alert_version' was set.
            $woo_general_tab_settings = $woo_customization_tab_settings = $woo_submition_tab_settings = $woo_email_tab_settings = [];
            if ( get_option( 'woo_product_stock_alert_version' ) ) {
                delete_option( 'woo_product_stock_alert_version' );
                $woo_general_tab_settings = get_option( 'woo_stock_alert_general_tab_settings' );
                $woo_customization_tab_settings = get_option( 'woo_stock_alert_form_customization_tab_settings' );
                $woo_submition_tab_settings = get_option( 'woo_stock_alert_form_submission_tab_settings' );
                $woo_email_tab_settings = get_option( 'woo_stock_alert_email_tab_settings' );
                if ( $woo_general_tab_settings )
                    delete_option( 'woo_stock_alert_general_tab_settings' );
                if ( $woo_customization_tab_settings )
                    delete_option( 'woo_stock_alert_form_customization_tab_settings' );
                if ( $woo_submition_tab_settings )
                    delete_option( 'woo_stock_alert_form_submission_tab_settings' );
                if ( $woo_email_tab_settings )
                    delete_option( 'woo_stock_alert_email_tab_settings' );
            }
            
            // Replace all default value by previous settings.
            foreach( $general_settings as $key => $value ) {
                if ( $woo_general_tab_settings && isset( $woo_general_tab_settings[ $key ] ) && $woo_general_tab_settings[ $key ] != '' ) {
                    $general_settings[ $key ] = $woo_general_tab_settings[ $key ];
                } elseif ( $mvx_general_tab_settings && isset( $mvx_general_tab_settings[ $key ] ) && $mvx_general_tab_settings[ $key ] != '' ) {
                    $general_settings[ $key ] = $mvx_general_tab_settings[ $key ];
                } elseif ( $dc_plugin_settings && isset( $dc_plugin_settings[ $key ] ) && $dc_plugin_settings[ $key ] != '' ) {
                    $general_settings[ $key ] = $dc_plugin_settings[ $key ];
                }
            }
    
            foreach( $customization_settings as $key => $value ) {
                if ( $woo_customization_tab_settings && isset( $woo_customization_tab_settings[ $key ] ) && $woo_customization_tab_settings[ $key ] != '' ) {
                    $customization_settings[ $key ] = $woo_customization_tab_settings[ $key ];
                } elseif ( $mvx_customization_tab_settings && isset( $mvx_customization_tab_settings[ $key ] ) && $mvx_customization_tab_settings[ $key ] != '' ) {
                    $customization_settings[ $key ] = $mvx_customization_tab_settings[ $key ];
                } elseif ( $dc_plugin_settings && isset( $dc_plugin_settings[ $key ] ) && $dc_plugin_settings[ $key ] != '' ) {
                    $customization_settings[ $key ] = $dc_plugin_settings[ $key ];
                }
            }
    
            foreach( $submit_settings as $key => $value ) {
                if ( $woo_submition_tab_settings && isset( $woo_submition_tab_settings[ $key ] ) && $woo_submition_tab_settings[ $key ] != '' ) {
                    $submit_settings[ $key ] = $woo_submition_tab_settings[ $key ];
                } elseif ( $mvx_submition_tab_settings && isset( $mvx_submition_tab_settings[ $key ] ) && $mvx_submition_tab_settings[ $key ] != '' ) {
                    $submit_settings[ $key ] = $mvx_submition_tab_settings[ $key ];
                } elseif ( $dc_plugin_settings && isset( $dc_plugin_settings[ $key ] ) && $dc_plugin_settings[ $key ] != '' ) {
                    $submit_settings[ $key ] = $dc_plugin_settings[ $key ];
                }
            }

            delete_option( 'dc_product_stock_alert_installed' );
            delete_option( 'woo_product_stock_alert_installed' );
            delete_option( 'dc_product_stock_alert_activate' );
            delete_option( 'woo_product_stock_alert_activate' );
        }

        update_option( 'woo_stock_manager_general_tab_settings', $general_settings );
        update_option( 'woo_stock_manager_form_customization_tab_settings', $customization_settings );
        update_option( 'woo_stock_manager_form_submission_tab_settings', $submit_settings );
        update_option( 'woo_stock_manager_email_tab_settings', $email_settings );

        update_option( 'woo_stock_manager_version', $current_version );
    }
}