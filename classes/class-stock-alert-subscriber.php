<?php
if (!defined('ABSPATH')) exit;

class Woo_Product_Stock_Alert_Subscriber {
    public function __construct() {
        add_action('woo_stock_alert_start_notification_cron_job', array($this, 'send_instock_notification_corn'));
        add_action('woocommerce_update_product', array($this, 'send_instock_notification'), 10, 2);
    }

    function send_instock_notification_corn() {
        $posts = get_posts([
            'post_type' => 'product',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);
        if($posts) {
            foreach($posts as $posts) {
                $this->send_instock_notification($posts->ID, wc_get_product($posts->ID));
            }
        }
    }

    function send_instock_notification($product_id, $product) {
        global $Woo_Product_Stock_Alert;
        $related_products = get_related_product($product);
        foreach($related_products as $product_id){
            $this->notify_all_product_subscribers($product_id);
        }
    }

    function notify_all_product_subscribers($product_id) {
        global $Woo_Product_Stock_Alert;
        if(! $product_id) {
            return;
        }
        $product_object = wc_get_product($product_id);
        if(! $product_object) {
            return;
        }
        if(! $product_object->is_type('variable')) {
            if(! is_product_outofstock($product_id, $product_object->is_type('variation') ? 'variation' : '', true)) {
                $product_subscribers = $this->get_product_subscribers_email( $product_id );
                if (isset($product_subscribers) && !empty($product_subscribers)) {
                    $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                    foreach ($product_subscribers as $subscribe_id => $to) {
                        $email->trigger($to, $product_id);
                        $this->update_subscriber($subscribe_id, 'woo_mailsent');
                    }
                    delete_post_meta($product_id, 'no_of_subscribers');
                }
            }
        }
    }
    
    /**
     * Insert a subscriber to a product.
     * @param mixed $subscriber_email
     * @param mixed $product_id
     * @return WP_Error|bool|int
     */
    function customer_stock_alert_subscribe($subscriber_email, $product_id) {
        $args = array(
            'post_title' => $subscriber_email,
            'post_type' => 'woostockalert',
            'post_status' => 'woo_subscribed',
        );

        $id = wp_insert_post($args);
        if (!is_wp_error($id)) {
            $default_data = array(
                'wooinstock_product_id' => $product_id,
                'wooinstock_subscriber_email' => $subscriber_email,
            );
            foreach ($default_data as $key => $value) {
                update_post_meta($id, $key, $value);
            }
            $this->update_product_subscriber_count($product_id);
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
    function customer_stock_alert_unsubscribe($product_id, $customer_email) {
        $unsubscribe_post = $this->is_already_subscribed($customer_email, $product_id);
        if ($unsubscribe_post) {
            if(is_array($unsubscribe_post)){
                $unsubscribe_post = $unsubscribe_post[0];
            }
            $this->update_subscriber($unsubscribe_post, 'woo_unsubscribed');
            $this->update_product_subscriber_count($unsubscribe_post);
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
    function is_already_subscribed($subscriber_email, $product_id) {
        $args = array(
            'post_type' => 'woostockalert',
            'fields' => 'ids',
            'posts_per_page' => 1,
            'post_status' => 'woo_subscribed',
        );
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => 'wooinstock_product_id',
                'value' => $product_id,
            ),
            array(
                'key' => 'wooinstock_subscriber_email',
                'value' => $subscriber_email,
            ),
        );
        $args['meta_query'] = $meta_query;
        $get_posts = get_posts($args);
        return $get_posts;
    }

    /**
     * Update the subscriber count for a product
     * @param mixed $product_id
     * @return void
     */
    function update_product_subscriber_count($product_id ) {
        $args = array(
            'post_type' => 'woostockalert',
            'post_status' => 'woo_subscribed',
            'meta_query' => array(
                array(
                    'key' => 'wooinstock_product_id',
                    'value' => array($product_id),
                    'compare' => 'IN',
                )),
            'numberposts' => -1,
        );
        $query = get_posts($args);
        update_post_meta($product_id, 'no_of_subscribers', count($query));
    }

    /**
     * Update the status of stockalert subscriber.
     * @param mixed $stockalert_id
     * @param mixed $status
     * @return WP_Error|int
     */
    function update_subscriber($stockalert_id, $status) {
        $args = array(
            'ID' => $stockalert_id,
            'post_type' => 'woostockalert',
            'post_status' => $status,
        );
        $id = wp_update_post($args);
        return $id;
    }

    /**
     * Trigger the email for a indivisual customer in time of subscribe.
     * If additional_alert_email setting is set it will send to admin.
     * @param int $product_id
     * @param string $customer_email
     * @return void
     */
    function insert_subscriber_email_trigger($product_id, $customer_email) {
        $admin_mail = WC()->mailer()->emails['WC_Admin_Email_Stock_Alert'];
        $cust_mail = WC()->mailer()->emails['WC_Subscriber_Confirmation_Email_Stock_Alert'];
        $general_tab_settings = get_option('woo_stock_alert_general_tab_settings');
        $additional_email = (isset($general_tab_settings['additional_alert_email'])) ? $general_tab_settings['additional_alert_email'] : '';

        if (function_exists('get_mvx_product_vendors')) {
            $vendor = get_mvx_product_vendors($product_id);
            if ($vendor && apply_filters( 'woo_product_stock_alert_add_vendor', true)) {
                $additional_email .= ','. sanitize_email($vendor->user_data->user_email);  
            }
        }
        if (!empty($additional_email))
            $admin_mail->trigger($additional_email, $product_id, $customer_email);
        $cust_mail->trigger($customer_email, $product_id);
    }

    /**
     * Get the email of all subscriber of a particular product.
     * @param int $product_id
     * @return array array of email
     */
    function get_product_subscribers_email($product_id) {
        if(!$product_id || $product_id <= '0') {
            return [];
        }
        $emails = array();
        $args = array(
            'post_type'     => 'woostockalert',
            'fields'        => 'ids',
            'posts_per_page'=> -1,
            'post_status'   => 'woo_subscribed',
            'meta_query'    => array(
                array(
                    'key'     => 'wooinstock_product_id',
                    'value'   => $product_id,
                    'compare' => '='
                )
            )
        );
        $subsciber_post = get_posts($args);
        if ($subsciber_post && count($subsciber_post) > 0) {
            foreach ($subsciber_post as $subsciber_id) {
                $email = get_post_meta($subsciber_id, 'wooinstock_subscriber_email', true);
                $emails[$subsciber_id] = $email ? $email : '';
            }
        }
        return $emails;
    }
}
