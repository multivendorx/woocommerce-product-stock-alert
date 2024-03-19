<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;

class Subscriber {
    public function __construct( ) {
        add_action( 'woo_stock_manager_start_notification_cron_job', [ $this, 'send_instock_notification_corn' ] );
        add_action( 'woocommerce_update_product', [ $this, 'send_instock_notification' ], 10, 2 );
    } 
    
    /**
     * Send instock notification on every product's subscriber if product is instock.
     * It will run every hour through corn job.
     * @return void
     */
    function send_instock_notification_corn( ) {
        $posts = get_posts( [ 
            'post_type' => 'product', 
            'post_status' => 'publish', 
            'numberposts' => -1
        ] );
        if ( $posts ) {
            foreach( $posts as $posts ) {
                self::send_instock_notification( $posts -> ID, wc_get_product( $posts -> ID ) );
            } 
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
        foreach( $related_products as $product_id ) {
            $this -> notify_all_product_subscribers( $product_id );
        } 
    } 

    /**
     * Send notification to all subscriber, subscribed to a particular product.
     * @param int $product_id
     * @return void
     */
    function notify_all_product_subscribers( $product_id ) {
        if ( ! $product_id ) {
            return;
        } 
        $product_object = wc_get_product( $product_id );
        if ( ! $product_object ) {
            return;
        } 
        if ( ! $product_object -> is_type( 'variable' ) ) {
            if ( ! self::is_product_outofstock( $product_id, $product_object -> is_type( 'variation' ) ? 'variation' : '', true ) ) {
                $product_subscribers = self::get_product_subscribers_email( $product_id );
                if ( isset( $product_subscribers ) && !empty( $product_subscribers ) ) {
                    $email = WC( ) -> mailer( ) -> emails[ 'WC_Email_Stock_Manager' ];
                    foreach ( $product_subscribers as $subscribe_id => $to ) {
                        $email -> trigger( $to, $product_object );
                        self::update_subscriber( $subscribe_id, 'woo_mailsent' );
                    } 
                    delete_post_meta( $product_id, 'no_of_subscribers' );
                } 
            } 
        } 
    } 
    
    /**
     * Insert a subscriber to a product.
     * @param mixed $subscriber_email
     * @param mixed $product_id
     * @return \WP_Error|bool|int
     */
    static function subscribe_user( $subscriber_email, $product_id ) {
        $args = [ 
            'post_title' => $subscriber_email, 
            'post_type' => 'woostockalert', 
            'post_status' => 'woo_subscribed', 
        ];

        $id = wp_insert_post( $args );
        if ( !is_wp_error( $id ) ) {
            $default_data = [ 
                'wooinstock_product_id' => $product_id, 
                'wooinstock_subscriber_email' => $subscriber_email, 
            ];
            foreach ( $default_data as $key => $value ) {
                update_post_meta( $id, $key, $value );
            } 
            self::update_product_subscriber_count( $product_id );
            return $id;
        } else {
            return false;
        } 
    } 

    /**
     * Function that unsubscribe a particular user if the user is already subscribed
     * @param int $product_id  
     * @param string $customer_email
     * @return bool
     */
    static function unsubscribe_user( $product_id, $customer_email ) {
        $unsubscribe_post = self::is_already_subscribed( $customer_email, $product_id );
        if ( $unsubscribe_post ) {
            if ( is_array( $unsubscribe_post ) ) {
                $unsubscribe_post = $unsubscribe_post[ 0 ];
            } 
            self::update_subscriber( $unsubscribe_post, 'woo_unsubscribed' );
            self::update_product_subscriber_count( $unsubscribe_post );
            return true;
        } 
        return false;
    } 

    /**
     * Check if a user subscribed to a product.
     * If the user subscribed to the product it return the subscription ID, Or null.
     * @param mixed $subscriber_email
     * @param mixed $product_id
     * @return array Subscription ID | null
     */
    static function is_already_subscribed( $subscriber_email, $product_id ) {
        $args = [ 
            'post_type'    => 'woostockalert', 
            'fields'       => 'ids', 
            'posts_per_page' => 1, 
            'post_status'  => 'woo_subscribed', 
			'meta_query'   => [ 
				'relation' => 'AND', 
				[ 
					'key'   => 'wooinstock_product_id', 
					'value' => $product_id, 
                ], 
				[ 
					'key'   => 'wooinstock_subscriber_email', 
					'value' => $subscriber_email, 
                ], 
            ]
        ];
        $get_posts = get_posts( $args );
        return $get_posts;
    } 

    /**
     * Update the subscriber count for a product
     * @param mixed $product_id
     * @return void
     */
    static function update_product_subscriber_count( $product_id ) {
        $args = [ 
            'post_type'   => 'woostockalert', 
            'post_status' => 'woo_subscribed', 
            'meta_query'  => [ 
                [ 
                    'key'     => 'wooinstock_product_id', 
                    'value'   => [ $product_id ], 
                    'compare' => 'IN', 
                ] ], 
            'numberposts' => -1, 
        ];
        $query = get_posts( $args );
        update_post_meta( $product_id, 'no_of_subscribers', count( $query ) );
    } 

    /**
     * Update the status of stockalert subscriber.
     * @param mixed $stockalert_id
     * @param mixed $status
     * @return \WP_Error|int
     */
    static function update_subscriber( $stockalert_id, $status ) {
        $args = [ 
            'ID' => $stockalert_id, 
            'post_type' => 'woostockalert', 
            'post_status' => $status, 
        ];
        $id = wp_update_post( $args );
        return $id;
    } 

    /**
     * Trigger the email for a indivisual customer in time of subscribe.
     * If additional_alert_email setting is set it will send to admin.
     * @param int $product_id
     * @param string $customer_email
     * @return void
     */
    static function insert_subscriber_email_trigger( $product, $customer_email ) {
        $admin_mail = WC( ) -> mailer( ) -> emails[ 'WC_Admin_Email_Stock_Manager' ];
        $cust_mail = WC( ) -> mailer( ) -> emails[ 'WC_Subscriber_Confirmation_Email_Stock_Manager' ];
        $general_tab_settings = get_option( 'woo_stock_manager_general_tab_settings' );
        $additional_email = ( isset( $general_tab_settings[ 'additional_alert_email' ] ) ) ? $general_tab_settings[ 'additional_alert_email' ] : '';

        if ( function_exists( 'get_mvx_product_vendors' ) ) {
            $vendor = get_mvx_product_vendors( wc_get_product( $product ) -> get_id( ) );
            if ( $vendor && apply_filters( 'woo_stock_manager_add_vendor', true ) ) {
                $additional_email .= ', '. sanitize_email( $vendor -> user_data -> user_email );  
            } 
        } 
        
        if ( !empty( $additional_email ) )
            $admin_mail -> trigger( $additional_email, $product, $customer_email );
        $cust_mail -> trigger( $customer_email, $product );
    } 

    /**
     * Get the email of all subscriber of a particular product.
     * @param int $product_id
     * @return array array of email
     */
    static function get_product_subscribers_email( $product_id ) {
        if ( !$product_id || $product_id <= '0' ) {
            return [ ];
        } 
        $emails = [ ];
        $args = [ 
            'post_type'     => 'woostockalert', 
            'fields'        => 'ids', 
            'posts_per_page'=> -1, 
            'post_status'   => 'woo_subscribed', 
            'meta_query'    => [ 
                [ 
                    'key'     => 'wooinstock_product_id', 
                    'value'   => $product_id, 
                    'compare' => '='
                ]
            ]
        ];
        $subsciber_post = get_posts( $args );
        if ( $subsciber_post && count( $subsciber_post ) > 0 ) {
            foreach ( $subsciber_post as $subsciber_id ) {
                $email = get_post_meta( $subsciber_id, 'wooinstock_subscriber_email', true );
                $emails[ $subsciber_id ] = $email ? $email : '';
            } 
        } 
        return $emails;
    } 

    /**
     * Get all child ids if a prodcut is variable else get product id
     * @param mixed $product
     * @return array
     */
    static function get_related_product( $product ) {
        $product_ids = [ ];
        switch( $product -> get_type( ) ) {
            case 'variable' :
                if ( $product -> has_child( ) ) {
                    $product_ids = $product -> get_children( );
                } else {
                    $product_ids[ ] = $product -> get_id( );
                } 
                break;
            case 'simple' :
                $product_ids[ ] = $product -> get_id( );
                break;
            default :
                $product_ids[ ] = $product -> get_id( ); 
        } 
        return $product_ids;
    } 

    /**
     * Bias variable is used to controll biasness of outcome in uncertain input
     * Bias = true -> product outofstock | Bias = false -> product instock
     * @param mixed $product_id
     * @param mixed $type
     * @param mixed $bias
     * @return mixed
     */
    static function is_product_outofstock( $product_id, $type = '', $bias = false ) {
        if ( !$product_id ) return $bias;

        if ( $type == 'variation' ) {
            $child_obj      = new \WC_Product_Variation( $product_id );
            $manage_stock   = $child_obj -> managing_stock( );
            $stock_quantity = intval( $child_obj -> get_stock_quantity( ) );
            $stock_status   = $child_obj -> get_stock_status( );
        } else {
            $product        = wc_get_product( $product_id );
            $manage_stock   = $product -> get_manage_stock( );
            $stock_quantity = $product -> get_stock_quantity( );
            $stock_status   = $product -> get_stock_status( );
        } 

        $general_tab_settings = get_option( 'woo_stock_manager_general_tab_settings' );
        $is_enable_backorders = isset( $general_tab_settings[ 'is_enable_backorders' ] ) ? $general_tab_settings[ 'is_enable_backorders' ] : false;
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
