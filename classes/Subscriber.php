<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;

/**
 * StockManager Subscribe class
 */
class Subscriber {
    public function __construct() {
        add_action( 'stock_manager_start_notification_cron_job', [ $this, 'send_instock_notification_corn' ] );
        add_action( 'woocommerce_update_product', [ $this, 'send_instock_notification' ], 10, 2 );
        add_action( 'delete_post', [ $this, 'delete_subscriber_all' ] );
        add_action( 'stock_manager_start_subscriber_migration', [ Install::class, 'subscriber_migration' ] );

        if ( Install::is_migration_running() ) {
            $this->registers_post_status();
        }
    }

    /**
     * Function to register the post status.
     * @return void
     */
    function registers_post_status() {
        register_post_status( 'woo_mailsent', [ 
            'label' => _x( 'Mail Sent', 'woostockalert', 'woocommerce-stock-manager' ), 
            'public' => true, 
            'exclude_from_search' => true, 
            'show_in_admin_all_list' => true, 
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Mail Sent <span class="count">( %s )</span>', 'Mail Sent <span class="count">( %s )</span>', 'woocommerce-stock-manager' ), 
        ] );

        register_post_status( 'woo_subscribed', [ 
            'label' => _x( 'Subscribed', 'woostockalert', 'woocommerce-stock-manager' ), 
            'public' => true, 
            'exclude_from_search' => true, 
            'show_in_admin_all_list' => true, 
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Subscribed <span class="count">( %s )</span>', 'Subscribed <span class="count">( %s )</span>' ), 
        ] );

        register_post_status( 'woo_unsubscribed', [ 
            'label' => _x( 'Unsubscribed', 'woostockalert', 'woocommerce-stock-manager' ), 
            'public' => true, 
            'exclude_from_search' => true, 
            'show_in_admin_all_list' => true, 
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop( 'Unsubscribed <span class="count">( %s )</span>', 'Unsubscribed <span class="count">( %s )</span>' ), 
        ] );
    }
    
    /**
     * Send instock notification on every product's subscriber if product is instock.
     * It will run every hour through corn job.
     * @return void
     */
    function send_instock_notification_corn() {
    
        $products = wc_get_products( [] );

        if ( ! $products ) {
            return;
        }

        foreach( $products as $product ) {
            self::send_instock_notification( $product->get_id(), $product );
        }
    } 
    
    /**
     * Send instock notification of a product's all subscribers on 'woocommerce_update_product' hook
     * @param int $product_id
     * @param object $product
     * @return void
     */
    function send_instock_notification( $product_id, $product ) {
        $related_products = self::get_related_product( $product );

        foreach( $related_products as $related_product ) {
            $this->notify_all_product_subscribers( wc_get_product( $related_product ) );
        } 
    } 

    /**
     * Send notification to all subscriber, subscribed to a particular product.
     * @param \WC_Product $product
     * @return void
     */
    function notify_all_product_subscribers( $product ) {
        
        if ( ! $product || $product->is_type( 'variable' ) ) {
            return;
        }

        if ( self::is_product_outofstock( $product ) ) {
            return;
        }

        $product_subscribers = self::get_product_subscribers_email( $product->get_id() );

        if ( isset( $product_subscribers ) && ! empty( $product_subscribers ) ) {
            $email = WC()->mailer()->emails[ 'WC_Email_Stock_Manager' ];

            foreach ( $product_subscribers as $subscribe_id => $to ) {
                $email->trigger( $to, $product );
                self::update_subscriber( $subscribe_id, 'mailsent' );
            }

            delete_post_meta( $product->get_id(), 'no_of_subscribers' );
        } 
    }
    
    /**
     * Insert a subscriber to a product.
     * @param mixed $subscriber_email
     * @param mixed $product_id
     * @return \WP_Error|bool|int
     */
    static function insert_subscriber( $subscriber_email, $product_id ) {
        global $wpdb;

        // Get current user id.
        $user_id = wp_get_current_user()->ID;
        
        // Check the email is already register or not
        $subscriber = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}stockalert_subscribers 
                WHERE product_id = %d
                AND email = %s",
                [ $product_id, $subscriber_email ]
            )
        );

        // Update the status and create time of the subscriber row
        if ( $subscriber ) {
            return $response = $wpdb->update(
                "{$wpdb->prefix}stockalert_subscribers",
                [
                    "status"      => 'subscribed',
                    "create_time" => current_time( 'mysql' )
                ],
                [ "id" => $subscriber->id ]
            );
        }

        // Insert new subscriber.
        $response = $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->prefix}stockalert_subscribers
                ( product_id, user_id, email, status )
                VALUES ( %d, %d, %s, %s )
                ON DUPLICATE KEY UPDATE
                status = %s",
                [ $product_id, $user_id, $subscriber_email, 'subscribed', 'subscribed' ]
            )
        );

        // Update the product subscriber count after new subscriber insert.
        if ( $response ) {
            self::update_product_subscriber_count( $product_id );
        }

        return $response;
    }

    /**
     * Function that unsubscribe a particular user if the user is already subscribed
     * @param int $product_id  
     * @param string $customer_email
     * @return bool
     */
    static function remove_subscriber( $product_id, $customer_email ) {
        // Check the user is already subscribed or not
        $unsubscribe_post = self::is_already_subscribed( $customer_email, $product_id );

        if ( $unsubscribe_post ) {
            if ( is_array( $unsubscribe_post ) ) {
                $unsubscribe_post = $unsubscribe_post[ 0 ];
            }

            self::update_subscriber( $unsubscribe_post, 'unsubscribed' );
            self::update_product_subscriber_count( $product_id );

            return true;
        }

        return false;
    }

    /**
     * Delete subscriber on product delete.
     * @param int $post_id
     * @return void
     */
    public static function delete_subscriber_all( $post_id ) {
        global $wpdb;

        if( get_post_type( $post_id ) != 'product' ) return;
        
        // Delete subscriber of deleted product
        $wpdb->delete( $wpdb->prefix . "stockalert_subscribers", [ 'product_id' => $post_id ] );
        delete_post_meta( $post_id, 'no_of_subscribers' );
    }

    /**
     * Delete a subscriber from database.
     * @param mixed $product_id
     * @param mixed $email
     * @return void
     */
    public static function delete_subscriber( $product_id, $email ) {
        global $wpdb;

        // Delete subscriber of deleted product
        $wpdb->delete( $wpdb->prefix . "stockalert_subscribers", [
            'product_id' => $product_id,
            'email' => $email,
        ] );

        self::update_product_subscriber_count( $product_id );
    }

    /**
     * Check if a user subscribed to a product.
     * If the user subscribed to the product it return the subscription ID, Or null.
     * @param mixed $subscriber_email
     * @param mixed $product_id
     * @return array | string Subscription ID | null
     */
    static function is_already_subscribed( $subscriber_email, $product_id ) {
        global $wpdb;
			
        // Get the result from custom subscribers table. 
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}stockalert_subscribers
                WHERE product_id = %d
                AND email = %s
                AND status = %s",
                [ $product_id, $subscriber_email, 'subscribed' ]
            )
        );
    }

    /**
     * Update the subscriber count for a product
     * @param mixed $product_id
     * @return void
     */
    static function update_product_subscriber_count( $product_id ) {
        global $wpdb;

        // Get subscriber count.
        $subscriber_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}stockalert_subscribers
            WHERE product_id = {$product_id}
            AND status = 'subscribed'"
        );

        // Update subscriber count in product's meta.
        update_post_meta( $product_id, 'no_of_subscribers', $subscriber_count );
    } 

    /**
     * Update the status of stockalert subscriber.
     * @param mixed $stockalert_id
     * @param mixed $status
     * @return \WP_Error|int
     */
    static function update_subscriber( $stockalert_id, $status ) {
        global $wpdb;

        // Update subscrib status
        $response = $wpdb->update(
            "{$wpdb->prefix}stockalert_subscribers",
            [ "status" => $status ],
            [ "id"     => $stockalert_id ]
        );

        return $stockalert_id;
    }

    /**
     * Trigger the email for a indivisual customer in time of subscribe.
     * If additional_alert_email setting is set it will send to admin.
     * @param int $product_id
     * @param string $customer_email
     * @return void
     */
    static function insert_subscriber_email_trigger( $product, $customer_email ) {
        // Get email object.
        $admin_mail = WC()->mailer()->emails[ 'WC_Admin_Email_Stock_Manager' ];
        $cust_mail  = WC()->mailer()->emails[ 'WC_Subscriber_Confirmation_Email_Stock_Manager' ];

        // Get additional email from global setting.
        $additional_email = SM()->setting->get_setting( 'additional_alert_email' );

        // Add vendor's email.
        if ( function_exists( 'get_mvx_product_vendors' ) ) {
            $vendor = get_mvx_product_vendors( $product->get_id() );

            // Append vendor's email as additional email.
            if ( $vendor && apply_filters( 'stock_manager_add_vendor', true ) ) {
                $additional_email .= ', '. sanitize_email( $vendor->user_data->user_email );  
            } 
        }
        
        // Trigger the additional email.
        if ( ! empty( $additional_email ) )
            $admin_mail->trigger( $additional_email, $product, $customer_email );

        // Trigger customer email.
        $cust_mail->trigger( $customer_email, $product );
    }

    /**
     * Get the email of all subscriber of a particular product.
     * @param int $product_id
     * @return array array of email
     */
    static function get_product_subscribers_email( $product_id ) {
        global $wpdb;

        if ( ! $product_id || $product_id <= '0' ) {
            return [];
        }
        
        $emails = [];

        // Migration is over use custom subscription table for information
        $emails_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, email from {$wpdb->prefix}stockalert_subscribers
                WHERE product_id = %d AND status = %s",
                [ $product_id, 'subscribed' ]
            )
        );

        // Prepare email data
        foreach ( $emails_data as $email ) {
            $emails[ $email->id ] = $email->email;
        }

        return $emails;
    }

    /**
     * Get all child ids if a prodcut is variable else get product id
     * @param mixed $product
     * @return array
     */
    static function get_related_product( $product ) {
        // If product is not woocommerce product object.
        if ( is_numeric( $product ) ){
            $product = wc_get_product( $product );
        }

        $product_ids = [];

        switch( $product->get_type() ) {
            case 'variable' :
                if ( $product->has_child() ) {
                    $product_ids = $product->get_children();
                } else {
                    $product_ids[] = $product->get_id();
                }
                break;
            case 'simple' :
                $product_ids[] = $product->get_id();
                break;
            default :
                $product_ids[] = $product->get_id(); 
        }

        return $product_ids;
    } 

    /**
     * Bias variable is used to controll biasness of outcome in uncertain input
     * Bias = true->product outofstock | Bias = false->product instock
     * @param \WC_Product $product
     * @return mixed
     */
    static function is_product_outofstock( $product ) {
        
        if ( $product->is_type( 'variation' ) ) {
            $child_obj      = new \WC_Product_Variation( $product->get_id() );
            $manage_stock   = $child_obj->managing_stock();
            $stock_quantity = intval( $child_obj->get_stock_quantity() );
            $stock_status   = $child_obj->get_stock_status();
        } else {
            $manage_stock   = $product->get_manage_stock();
            $stock_quantity = $product->get_stock_quantity();
            $stock_status   = $product->get_stock_status();
        } 

        $is_enable_backorders = SM()->setting->get_setting( 'is_enable_backorders' );
        $is_enable_backorders = is_array( $is_enable_backorders ) ? reset( $is_enable_backorders ) : false;
        
        if ( $manage_stock ) {
            if ( $stock_quantity <= ( int ) get_option( 'woocommerce_notify_no_stock_amount' ) ) {
                return true;
            } elseif ( $stock_quantity <= 0 ) {
                return true;
            } 
        } else {
            if ( $stock_status == 'onbackorder' && $is_enable_backorders ) {
                return true;
            } elseif ( $stock_status == 'outofstock' ) {
                return true;
            } 
        } 
        return false;
    } 
} 
