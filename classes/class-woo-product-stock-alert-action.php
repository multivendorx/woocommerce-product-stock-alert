<?php
/**
 * Start Checking subscribed customer and alert about stock 
 *
 */
class WOO_Product_Stock_Alert_Action {

    public function __construct() {
        // Call to cron action
        add_action('dc_start_stock_alert', array($this, 'stock_alert_action'));

        //older data crone
        add_action('mvx_stock_alert_older_data_migration', array($this, 'mvx_stock_alert_older_data_migration_function'));

    }

    function stock_alert_action() {
        global $WC;
        $all_products = array();
        $all_products = get_posts(
                array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'numberposts' => -1
                )
        );
        $all_product_ids = array();
        if (!empty($all_products) && is_array($all_products)) {
            foreach ($all_products as $products_each) {
                $child_ids = $product_obj = array();
                $product_obj = wc_get_product($products_each->ID);
                if ($product_obj && $product_obj->is_type('variable')) {
                    if ($product_obj->has_child()) {
                        $child_ids = $product_obj->get_children();
                        if (isset($child_ids) && !empty($child_ids)) {
                            foreach ($child_ids as $child_id) {
                                $all_product_ids[] = $child_id;
                            }
                        }
                    }
                } else {
                    $all_product_ids[] = $products_each->ID;
                }
            }
        }
        
        $get_subscribed_user = array();
        if (!empty($all_product_ids) && is_array($all_product_ids)) {
            foreach ($all_product_ids as $product_id) {
                $subscribers_email = get_product_subscribers_email($product_id);
                if ($subscribers_email && !empty($subscribers_email)) {
                    $get_subscribed_user[$product_id] = $subscribers_email;
                }
            }
        }
        if (!empty($get_subscribed_user) && is_array($get_subscribed_user)) {
            foreach ($get_subscribed_user as $p_id => $subscriber) {
                $product = wc_get_product($p_id);
                $product_availability_stock = $product->get_stock_quantity();
                $manage_stock = $product->get_manage_stock();
                $managing_stock = $product->managing_stock();
                $stock_status = $product->get_stock_status();
                if ( $managing_stock ) {
                    if ($product->backorders_allowed() && get_mvx_product_alert_plugin_settings('is_enable_backorders')) {
                        $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                        foreach ($subscriber as $post_id => $to) {
                            $email->trigger($to, $p_id);

                            update_subscriber($post_id, 'woo_mailsent');
                            delete_post_meta($p_id, 'no_of_subscribers');
                        }
                        
                    } else {
                        if ($product_availability_stock > (int) get_option('woocommerce_notify_no_stock_amount')) {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                            foreach ($subscriber as $post_id => $to) {
                                $email->trigger($to, $p_id);

                                update_subscriber($post_id, 'woo_mailsent');
                                delete_post_meta($p_id, 'no_of_subscribers');
                            }
                        }
                    }
                } else {
                    if ($stock_status == 'onbackorder' && get_mvx_product_alert_plugin_settings('is_enable_backorders')) {
                        if ($stock_status != 'outofstock' || $product_availability_stock > (int) get_option('woocommerce_notify_no_stock_amount')) {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                            foreach ($subscriber as $post_id => $to) {
                                $email->trigger($to, $p_id);

                                update_subscriber($post_id, 'woo_mailsent');
                                delete_post_meta($p_id, 'no_of_subscribers');
                            }
                        }
                    } elseif ($stock_status == 'instock' ) {
                        $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                        foreach ($subscriber as $post_id => $to) { 
                            $email->trigger($to, $p_id);
                            update_subscriber($post_id, 'woo_mailsent');
                            delete_post_meta($p_id, 'no_of_subscribers');
                        }
                    }
                }
            }
        }
    }


    /*
     * This function migrate older subscription data
     */
    function mvx_stock_alert_older_data_migration_function() {
        if (!get_option('_is_updated_mvx_product_alert_database')) {
            $all_products = array();
            $all_products = get_posts(
                array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'numberposts' => -1
                )
            );
            $all_product_ids = array();
            if (!empty($all_products) && is_array($all_products)) {
                foreach ($all_products as $products_each) {
                    $child_ids = $product_obj = array();
                    $product_obj = wc_get_product($products_each->ID);
                    if ($product_obj && $product_obj->is_type('variable')) {
                        if ($product_obj->has_child()) {
                            $child_ids = $product_obj->get_children();
                            if (isset($child_ids) && !empty($child_ids)) {
                                foreach ($child_ids as $child_id) {
                                    $all_product_ids[] = $child_id;
                                }
                            }
                        }
                    } else {
                        $all_product_ids[] = $products_each->ID;
                    }
                }
            }
            
            $get_subscribed_user = array();
            if (!empty($all_product_ids) && is_array($all_product_ids)) {
                foreach ($all_product_ids as $all_product_id) {
                    $_product_subscriber = get_post_meta($all_product_id, '_product_subscriber', true);
                    if ($_product_subscriber && !empty($_product_subscriber)) {
                        $get_subscribed_user[$all_product_id] = get_post_meta($all_product_id, '_product_subscriber', true);
                    }
                }
            }

            if (!empty($get_subscribed_user) && is_array($get_subscribed_user)) {
                foreach ($get_subscribed_user as $id => $subscriber) {
                    if  (!empty($subscriber)) {
                        foreach ($subscriber as $email) {
                            if (!is_already_subscribed($email, $id)) {
                                insert_subscriber($email, $id);
                            }
                        }
                    }   
                }
            }
            update_option( '_is_updated_mvx_product_alert_database', true );
        }
    }
}