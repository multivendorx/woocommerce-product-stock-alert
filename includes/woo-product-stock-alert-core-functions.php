<?php
if (!function_exists('woocommerce_inactive_notice')) {

    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sWoocommerce Product Stock Alert is inactive.%s The %sWooCommerce plugin%s must be active for the Woocommerce Product Stock Alert to work. Please %sinstall & activate WooCommerce%s', WCS_TEXT_DOMAIN), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('get_dc_plugin_settings')) {

    function get_dc_plugin_settings() {
        $dc_plugin_settings = array();
        $dc_plugin_settings = get_option('dc_woo_product_stock_alert_general_settings_name');
        if (isset($dc_plugin_settings) && !empty($dc_plugin_settings)) {
            return $dc_plugin_settings;
        }
        return array();
    }

}

if (!function_exists('get_no_subscribed_persons')) {

    function get_no_subscribed_persons($product_id) {
        $dc_settings = get_dc_plugin_settings();
        $no_of_subscriber = 0;
        if (!empty($product_id)) {
            $product = wc_get_product($product_id);
            if($product) :
            if ($product->is_type('variable')) {
                $child_ids = $product->get_children();
                if (isset($child_ids) && !empty($child_ids)) {
                    foreach ($child_ids as $child_id) {
                        $child_obj = new WC_Product_Variation($child_id);
                        $managing_stock = $child_obj->managing_stock();
                        $stock_status = $child_obj->get_stock_status();
                        $product_availability_stock = intval($child_obj->get_stock_quantity());
                        $manage_stock = $child_obj->get_manage_stock();

                        if (isset($product_availability_stock) && $manage_stock) {
                            if ($managing_stock && $product_availability_stock <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                                if ($child_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                    if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                        $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                        if (!empty($product_subscriber)) {
                                            $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                        }
                                    }
                                } else {
                                    $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                    if (!empty($product_subscriber)) {
                                        $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                    }
                                }
                            } elseif ($product_availability_stock <= 0) {
                                if ($child_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                    if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                        $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                        if (!empty($product_subscriber)) {
                                            $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                        }
                                    }
                                } else {
                                    $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                    if (!empty($product_subscriber)) {
                                        $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $managing_stock = $product->managing_stock();
                $stock_status = $product->get_stock_status();
                $product_availability_stock = intval($product->get_stock_quantity());
                $manage_stock = $product->get_manage_stock();

                if (isset($product_availability_stock) && $manage_stock) {
                    if ($managing_stock && $product_availability_stock <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                        if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                            if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                $product_subscriber = get_post_meta($product->get_id(), '_product_subscriber', true);
                                if ($product_subscriber) {
                                    $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                }
                            }
                        } else {
                            $product_subscriber = get_post_meta($product->get_id(), '_product_subscriber', true);
                            if ($product_subscriber) {
                                $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                            }
                        }
                    } elseif ($product_availability_stock <= 0) {
                        if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                            if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                $product_subscriber = get_post_meta($product->get_id(), '_product_subscriber', true);
                                if (!empty($product_subscriber)) {
                                    $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                }
                            }
                        } else {
                            $product_subscriber = get_post_meta($product->get_id(), '_product_subscriber', true);
                            if (!empty($product_subscriber)) {
                                $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                            }
                        }
                    }
                }
            }
            endif;
        }
        return $no_of_subscriber;
    }

}
if (!function_exists('display_stock_alert_form')) {

    function display_stock_alert_form($product) {
        $display_stock_alert_form = false;
        $dc_settings = get_dc_plugin_settings();

        if ($product) {
            $managing_stock = $product->managing_stock();
            $stock_quantity = $product->get_stock_quantity();
            $manage_stock = $product->get_manage_stock();
            $stock_status = $product->get_stock_status();

            if (isset($stock_quantity) && $manage_stock) {
                if ($managing_stock && $stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                    if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                        if ($stock_status == 'outofstock' || $stock_quantity <= 0) {
                            $display_stock_alert_form = true;
                        }
                    } else {
                        $display_stock_alert_form = true;
                    }
                } elseif ($stock_quantity <= 0) {
                    if ($product->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                        if ($stock_status == 'outofstock' || $stock_quantity <= 0) {
                            $display_stock_alert_form = true;
                        }
                    } else {
                        $display_stock_alert_form = true;
                    }
                }
            }
        }

        return $display_stock_alert_form;
    }

}

/**
 * Write to log file
 */
if (!function_exists('doWooStockAlertLOG')) {

    function doWooStockAlertLOG($str) {
        global $WOO_Product_Stock_Alert;
        $file = $WOO_Product_Stock_Alert->plugin_path . 'log/stock_alert_log.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
            if ($current) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents($file, $current);
        }
    }

}