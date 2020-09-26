<?php

/**
 * Start Checking subscribed customer and alert about stock 
 *
 */
class WOO_Product_Stock_Alert_Action {

    public function __construct() {
        // Call to cron action
        add_action('dc_start_stock_alert', array($this, 'stock_alert_action'));
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
                if ($product_obj->is_type('variable')) {
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
                // update no of interests
                $interest_persons = get_no_subscribed_persons($all_product_id);
                $product = wc_get_product($all_product_id);
                if (!empty($interest_persons) && $interest_persons != 0) {
                    if ($product->get_parent_id() != 0) {
                        update_post_meta($product->get_parent_id(), 'no_of_subscribers', $interest_persons);
                    } else {
                        update_post_meta($all_product_id, 'no_of_subscribers', $interest_persons);
                    }
                } else {
                    delete_post_meta($all_product_id, '_product_subscriber');
                    delete_post_meta($all_product_id, 'no_of_subscribers');
                }
            }
        }
        

        $dc_settings = get_dc_plugin_settings();
        if (!empty($get_subscribed_user) && is_array($get_subscribed_user)) {
            foreach ($get_subscribed_user as $id => $subscriber) {
                $product = wc_get_product($id);
                $product_availability_stock = $product->get_stock_quantity();
                $manage_stock = $product->get_manage_stock();
                $managing_stock = $product->managing_stock();
                $stock_status = $product->get_stock_status();

                if (isset($product_availability_stock) && $manage_stock) {
                    if ($managing_stock && $product_availability_stock > (int) get_option('woocommerce_notify_no_stock_amount')) {
                        if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                            if ($stock_status != 'outofstock' || $product_availability_stock > 0) {
                                $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                foreach ($subscriber as $to) {
                                    $email->trigger($to, $id);
                                }

                                delete_post_meta($id, '_product_subscriber');
                                delete_post_meta($id, 'no_of_subscribers');
                            }
                        } else {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                            foreach ($subscriber as $to) {
                                $email->trigger($to, $id);
                            }

                            delete_post_meta($id, '_product_subscriber');
                            delete_post_meta($id, 'no_of_subscribers');
                        }
                    } elseif (!$managing_stock && $product_availability_stock > 0) {
                        if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                            if ($stock_status != 'outofstock' || $product_availability_stock > 0) {
                                $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                foreach ($subscriber as $to) {
                                    $email->trigger($to, $id);
                                }

                                delete_post_meta($id, '_product_subscriber');
                                delete_post_meta($id, 'no_of_subscribers');
                            }
                        } else {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                            foreach ($subscriber as $to) {
                                $email->trigger($to, $id);
                            }

                            delete_post_meta($id, '_product_subscriber');
                            delete_post_meta($id, 'no_of_subscribers');
                        }
                    }
                }
            }
        }
    }

}
