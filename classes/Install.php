<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;
/**
 * Start schedule after plugin activation
 *
 */

class Install {
    /**
     * Map old status of post table to new status of subscriber table
     * @var array
     */
    const STATUS_MAP = [
        'woo_subscribed'    => 'subscribed',
        'woo_unsubscribed'  => 'unsubscribed',
        'woo_mailsent'      => 'mailsent'
    ];

    /**
     * Used for check migration is running or not.
     * @var bool | null
     */
    public static $migration_running = null;
    
    public function __construct() {
        $this->create_database_table();
        $this->start_cron_job();
    }

    /**
     * If migration is running it return true, otherwise false.
     * @return mixed
     */
    public static function is_migration_running() {
        if ( self::$migration_running === null ) {
            self::$migration_running = get_option( 'stock_manager_migration_running', false );
        }

        return self::$migration_running;
    }

    /**
     * Migrate subscriber from post table to subscribe migration.
     * @return void
     */
    public static function subscriber_migration() {
        global $wpdb;
        self::stock_manager_data_migrate();

        try {
            // Get woosubscribe post and post meta
            $subscribe_datas = $wpdb->get_results(
                "SELECT posts.ID as id,
                    posts.post_date as date,
                    posts.post_title as email,
                    posts.post_status as status,
                    posts.post_author as user_id,
                    pm.meta_value as product_id
                FROM {$wpdb->prefix}posts as posts, {$wpdb->prefix}postmeta as pm
                WHERE posts.post_type = 'woostockalert'
                AND pm.post_id = posts.ID
                AND pm.meta_key = 'wooinstock_product_id'
                ", ARRAY_A
            );
            
            // Prepare insert value
            $VALUES = "";
            
            foreach ( $subscribe_datas as $subscribe_data ) {
                
                $product_id = $subscribe_data[ 'product_id' ];
                $user_id    = $subscribe_data['user_id'];
                $email      = $subscribe_data['email'];
                $status     = self::STATUS_MAP[ $subscribe_data['status'] ];
                $date       = $subscribe_data['date'];
                
                $VALUES .= "( {$product_id}, {$user_id},  '{$email}', '{$status}', '{$date}' ),";
            }

            // If result exist then insert those result into custom table
            if ( $VALUES ) {
                // Remove last ','
                $VALUES = substr( $VALUES, 0, -1 );

                $wpdb->query(
                    "INSERT IGNORE INTO {$wpdb->prefix}stockalert_subscribers (product_id, user_id, email, status, create_time ) VALUES {$VALUES} "
                );
            }

            // Delete the post seperatly, If there is problem in migration post will not delete permanently
            foreach( $subscribe_datas as $subscribe_data ) {
                wp_delete_post( $subscribe_data[ 'id' ] );
            }

            // Get subscriber count
            $subscriber_counts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT product_id, COUNT(*) as count from {$wpdb->prefix}stockalert_subscribers
                    WHERE status = %s
                    GROUP BY product_id",
                    [ 'subscribed' ]
                )
            );

            // Update subscriber count
            foreach ( $subscriber_counts as $count_data ) {
                update_post_meta( $count_data->product_id, 'no_of_subscribers', $count_data->count );
            }

            delete_option( 'stock_manager_migration_running' );
            self::$migration_running = false;

        } catch ( \Exception $e ) {
            Utill::log( $e->getMessage() );
        }
    }

    /**
     * Create database table for subscriber.
     * @return void
     */
    private function create_database_table() {
        global $wpdb;

        $collate = '';

        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "stockalert_subscribers` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `product_id` bigint(20) NOT NULL,
                `user_id` bigint(20) NOT NULL DEFAULT 0,
                `email` varchar(50) NOT NULL,
                `status` varchar(20) NOT NULL,
                `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_product_email_status (product_id, email, status),
                PRIMARY KEY (`id`)
            ) $collate;"
        );
    }

    /**
     * Function that schedule hook for notification corn job.
     * @return void
     */
    private function start_cron_job() {
        // Migrate subscriber data from post table
        wp_clear_scheduled_hook( 'stock_manager_start_subscriber_migration' );
        wp_schedule_single_event( time(), 'stock_manager_start_subscriber_migration' );
        update_option( 'stock_manager_migration_running', true );

        // If corn is disabled
        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
            self::subscriber_migration();
        }

        // Notify user if product is instock
        wp_clear_scheduled_hook( 'stock_manager_start_notification_cron_job' );
        wp_schedule_event( time(), 'hourly', 'stock_manager_start_notification_cron_job' );
        update_option( 'stock_manager_cron_start', true );
    }

    /**
     * Data migration function. Run on installation time.
     * @return void
     */
    private static function stock_manager_data_migrate() {

        $current_version = SM()->version;
        $previous_version = get_option( "woo_stock_manager_version", "" );

        // Default messages for settings array.
        // Those will modify if previous settings was set.
        $appearance_settings = [
            'is_enable_backorders' => false, 
            'is_enable_no_interest' => false, 
            'is_double_optin' => false, 
            'is_remove_admin_email' => false, 
            'double_opt_in_success' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ), 
            'shown_interest_text' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ), 
            'additional_alert_email' => get_option( 'admin_email' ),
            'is_guest_subscriptions_enable' => ['is_guest_subscriptions_enable'],
            'lead_time_format'  => 'static',
            
            // Form customization settings
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

        if ( version_compare( $previous_version, '2.5.0', '<' ) ) {
            // Used to check the plugin version before 2.1.0
            $dc_was_installed = get_option( 'dc_product_stock_alert_activate' );
            // Used to check the plugin version before 2.3.0
            $woo_was_installed = get_option( 'woo_product_stock_alert_activate' );

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
                ]);
                
                // Database migration for subscriber data before version 2.3.0
                foreach( $all_product_ids as $product_id ) {
                    $current_product_ids = Subscriber::get_related_product( wc_get_product( $product_id ) );
                    foreach( $current_product_ids as $product_id ) {
                        $product_subscribers = get_post_meta( $product_id, '_product_subscriber', true );
                        if ( $product_subscribers && !empty( $product_subscribers ) ) {
                            foreach( $product_subscribers as $subscriber_email ) {
                                Subscriber::insert_subscriber( $subscriber_email, $product_id );
                            }
                        }
                        delete_post_meta( $product_id, '_product_subscriber' );
                    }
                }

                // Settings array for version upto 2.0.0
                $dc_plugin_settings = get_option( 'dc_woo_product_stock_alert_general_settings_name', [] );
                
                // Settings array for version from 2.1.0 to 2.2.0
                $mvx_general_tab_settings = get_option( 'mvx_woo_stock_alert_general_tab_settings', [] );
                $mvx_customization_tab_settings = get_option( 'mvx_woo_stock_alert_form_customization_tab_settings', [] );
                $mvx_submition_tab_settings = get_option( 'mvx_woo_stock_alert_form_submission_tab_settings', [] );
                
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
                    $woo_general_tab_settings = get_option( 'woo_stock_alert_general_tab_settings', [] );
                    $woo_customization_tab_settings = get_option( 'woo_stock_alert_form_customization_tab_settings', [] );
                    $woo_submition_tab_settings = get_option( 'woo_stock_alert_form_submission_tab_settings', [] );
                    $woo_email_tab_settings = get_option( 'woo_stock_alert_email_tab_settings', [] );
                    if ( $woo_general_tab_settings )
                        delete_option( 'woo_stock_alert_general_tab_settings' );
                    if ( $woo_customization_tab_settings )
                        delete_option( 'woo_stock_alert_form_customization_tab_settings' );
                    if ( $woo_submition_tab_settings )
                        delete_option( 'woo_stock_alert_form_submission_tab_settings' );
                    if ( $woo_email_tab_settings )
                        delete_option( 'woo_stock_alert_email_tab_settings' );
                }

                // Merge all setting array
                $tab_settings = array_merge(
                    $dc_plugin_settings,
                    $mvx_general_tab_settings,
                    $mvx_customization_tab_settings,
                    $mvx_submition_tab_settings,
                    $woo_general_tab_settings,
                    $woo_customization_tab_settings,
                    $woo_submition_tab_settings,
                    $woo_email_tab_settings
                );
                
                // Replace all default value by previous settings.
                foreach( $appearance_settings as $key => $value ) {
                    if ( isset( $tab_settings[ $key ] ) && $tab_settings[ $key ] != '' ) {
                        $appearance_settings[ $key ] = $tab_settings[ $key ];
                    }
                }

                foreach( $submit_settings as $key => $value ) {
                    if ( isset( $tab_settings[ $key ] ) && $tab_settings[ $key ] != '' ) {
                        $submit_settings[ $key ] = $tab_settings[ $key ];
                    }
                }

                delete_option( 'dc_product_stock_alert_installed' );
                delete_option( 'woo_product_stock_alert_installed' );
                delete_option( 'dc_product_stock_alert_activate' );
                delete_option( 'woo_product_stock_alert_activate' );
            }
            
            if ( version_compare( $previous_version, '2.4.2', '==' ) ) {
                $appearance_settings = get_option( 'woo_stock_manager_general_tab_settings', null ) ?? $appearance_settings;
                $submit_settings     = get_option( 'woo_stock_manager_form_submission_tab_settings', null ) ?? $submit_settings;
                $email_settings      = get_option( 'woo_stock_manager_email_tab_settings', null ) ?? $email_settings;
            }

            // Get customization_tab_setting and merge with general setting
            $customization_tab_setting = get_option( 'woo_stock_manager_form_customization_tab_settings', [] );
            $appearance_settings = array_merge( $appearance_settings, $customization_tab_setting );
            delete_option( 'woo_stock_manager_form_customization_tab_settings' );
        }
         
        if ( version_compare( $previous_version, '2.5.5', '<=' ) ) {
            $appearance_settings['is_guest_subscriptions_enable'] = ['is_guest_subscriptions_enable'];
        }

        if ( version_compare( $previous_version, '2.5.12', '<=' ) ) {
            $appearance_settings['lead_time_format'] = 'static';
        }

        $previous_appearance_settings   = get_option( 'woo_stock_manager_appearance_tab_settings', [] );
        $previous_submit_settings       = get_option( 'woo_stock_manager_form_submission_tab_settings', [] );
        $previous_email_settings        = get_option( 'woo_stock_manager_email_tab_settings', [] );
        
        update_option( 'woo_stock_manager_appearance_tab_settings', array_merge($appearance_settings, $previous_appearance_settings) );
        update_option( 'woo_stock_manager_form_submission_tab_settings', array_merge($submit_settings, $previous_submit_settings) );
        update_option( 'woo_stock_manager_email_tab_settings', array_merge($email_settings, $previous_email_settings) );
       
        update_option( 'woo_stock_manager_version', $current_version );
    }
}