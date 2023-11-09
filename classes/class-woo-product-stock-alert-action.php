<?php
/**
 * Start Checking subscribed customer and alert about stock 
 *
 */
class WOO_Product_Stock_Alert_Action {

    public function __construct() {
        // Call to cron action
        add_action('woo_start_stock_alert', array($this, 'stock_alert_action'));
        // Older data crone
        add_action('woo_stock_alert_older_data_migration', array($this, 'woo_stock_alert_older_data_migration_function'));

    }

    function stock_alert_action() {
        global $WC;
        $get_subscribed_user = get_product_subscribers_array();
        if (!empty($get_subscribed_user) && is_array($get_subscribed_user)) {
            foreach ($get_subscribed_user as $p_id => $subscriber) {
                $product = wc_get_product($p_id);
                $product_availability_stock = $product->get_stock_quantity();
                $manage_stock = $product->get_manage_stock();
                $managing_stock = $product->managing_stock();
                $stock_status = $product->get_stock_status();
                if ( $managing_stock ) {
                    if ($product->backorders_allowed() && get_woo_product_alert_plugin_settings('is_enable_backorders')) {
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
                    if ($stock_status == 'onbackorder' && get_woo_product_alert_plugin_settings('is_enable_backorders')) {
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
    function woo_stock_alert_older_data_migration_function() {
        if (!get_option('_is_updated_woo_product_alert_database')) {
            $get_subscribed_user = get_product_subscribers_array();
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
            update_option( '_is_updated_woo_product_alert_database', true );
        }
    }
}