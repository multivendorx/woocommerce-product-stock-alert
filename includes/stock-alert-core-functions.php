<?php

function woo_product_stock_alert_settings($links) {
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=woo-stock-alert-setting#&tab=settings&subtab=general') . '">' . __('Settings', 'woocommerce-product-stock-alert') . '</a>',
        '<a href="https://multivendorx.com/support-forum/forum/product-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __('Support', 'woocommerce-product-stock-alert') . '</a>',
        '<a href="https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __('Docs', 'woocommerce-product-stock-alert') . '</a>'
    );
    if (apply_filters('is_stock_alert_pro_inactive', true)) {
    	$links['go_pro'] = '<a href="' . WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL . '" class="stock-alert-pro-plugin" target="_blank">' . __('Get Pro', 'woocommerce-product-stock-alert') . '</a>';
    }
    return array_merge($plugin_links, $links);
}

if (!function_exists('woocommerce_inactive_notice')) {
    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sProduct Stock Manager & Notifier for WooCommerce is inactive.%s The %sWooCommerce plugin%s must be active for the Product Stock Manager & Notifier for WooCommerce to work. Please %sinstall & activate WooCommerce%s', 'woocommerce-product-stock-alert'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }
}

if (!function_exists('get_form_settings_array')) {
    function get_form_settings_array() {
        $general_tab_settings = get_option('woo_stock_alert_general_tab_settings');
        $form_submission_tab_settings = get_option('woo_stock_alert_form_submission_tab_settings');
        $form_customization_tab_settings = get_option('woo_stock_alert_form_customization_tab_settings');
        $email_tab_settings = get_option('woo_stock_alert_email_tab_settings');
        
        return [
            'double_opt_in_success'             => $general_tab_settings['double_opt_in_success'],
            'shown_interest_text'               => $general_tab_settings['shown_interest_text'],
            'alert_success'                     => $form_submission_tab_settings['alert_success'],
            'alert_email_exist'                 => $form_submission_tab_settings['alert_email_exist'],
            'valid_email'                       => $form_submission_tab_settings['valid_email'],
            'alert_unsubscribe_message'         => $form_submission_tab_settings['alert_unsubscribe_message'],
            'email_placeholder_text'            => $form_customization_tab_settings['email_placeholder_text'],
            'alert_text'                        => $form_customization_tab_settings['alert_text'],
            'button_text'                       => $form_customization_tab_settings['button_text'],
            'unsubscribe_button_text'           => $form_customization_tab_settings['unsubscribe_button_text'],
            'alert_text_color'                  => $form_customization_tab_settings['alert_text_color'],
            'button_background_color'           => $form_customization_tab_settings['button_background_color'],
            'button_border_color'               => $form_customization_tab_settings['button_border_color'],
            'button_text_color'                 => $form_customization_tab_settings['button_text_color'],
            'button_background_color_onhover'   => $form_customization_tab_settings['button_background_color_onhover'],
            'button_text_color_onhover'         => $form_customization_tab_settings['button_text_color_onhover'],
            'button_border_color_onhover'       => $form_customization_tab_settings['button_border_color_onhover'],
            'button_font_size'                  => $form_customization_tab_settings['button_font_size'],
            'button_border_size'                => $form_customization_tab_settings['button_border_size'],
            'button_border_radious'             => $form_customization_tab_settings['button_border_radious'],
            'ban_email_domain_text'             => $email_tab_settings['ban_email_domain_text'],
            'ban_email_address_text'            => $email_tab_settings['ban_email_address_text'],
        ];
    }
}

if(!function_exists('woo_stock_product_data')) {
    function woo_stock_product_data($product_id) {
        $product_data = array();
        $product_obj = wc_get_product( $product_id );
        $product_data['link'] = $product_obj->get_permalink();
        $product_data['name'] = $product_obj ? $product_obj->get_name() : '';
        $product_data['price'] = $product_obj ? wc_price( wc_get_price_to_display( $product_obj ) ) . '  ' : '';
        return apply_filters('woo_stock_alert_product_data', $product_data, $product_id);
    }
}

/**
     * Get all child ids if a prodcut is variable else get product id
     * @param mixed $product
     * @return array
     */
    function get_related_product($product) {
        $product_ids = [];
        switch($product->get_type()){
            case 'variable' :
                if($product->has_child()){
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
     * Bias = true -> product outofstock | Bias = false -> product instock
     * @param mixed $product_id
     * @param mixed $type
     * @param mixed $bias
     * @return mixed
     */
    function is_product_outofstock($product_id, $type = '', $bias = false) {
        if (!$product_id) return $bias;

        if ($type == 'variation') {
            $child_obj = new WC_Product_Variation($product_id);
            $manage_stock = $child_obj->managing_stock();
            $stock_quantity = intval($child_obj->get_stock_quantity());
            $stock_status = $child_obj->get_stock_status();
        } else {
            $product = wc_get_product($product_id);
            $manage_stock = $product->get_manage_stock();
            $stock_quantity = $product->get_stock_quantity();
            $stock_status = $product->get_stock_status();
        }

        $general_tab_settings = get_option('woo_stock_alert_general_tab_settings');
        $is_enable_backorders = (isset($general_tab_settings['is_enable_backorders'])) ? $general_tab_settings['$is_enable_backorders'] : false;
        if ($manage_stock) {
            if ($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                return true;
            } elseif ($stock_quantity <= 0) {
                return true;
            }
        } else {
            if ($stock_status == 'onbackorder' && $is_enable_backorders) {
                return true;
            } elseif ($stock_status == 'outofstock') {
                return true;
            }
        }
        return false;
    }