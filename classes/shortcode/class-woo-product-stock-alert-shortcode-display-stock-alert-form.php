<?php

class WOO_Product_Stock_Alert_Display_Form {

    public function __construct() {
        
    }

    /**
     * Display Stock Alert Form
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $WOO_Product_Stock_Alert;
        $WOO_Product_Stock_Alert->nocache();

        do_action('woocommerce_product_stock_alert_form_before');

        global $product;

        if (empty($product))
            return;

        $stock_interest = $alert_text_html = $button_html = '';
        $dc_settings = array();
        $alert_text = $button_text = $button_background_color = $button_border_color = $button_text_color = $unsubscribe_button_text = '';
        $alert_success = $alert_email_exist = $valid_email = $alert_unsubscribe_message = '';

        $dc_settings = get_dc_plugin_settings();

        if (isset($dc_settings) && !empty($dc_settings)) {
            $alert_text = isset($dc_settings['alert_text']) ? $dc_settings['alert_text'] : __('Get an alert when the product is in stock:', 'woocommerce-product-stock-alert');
            $alert_text_color = isset($dc_settings['alert_text_color']) ? $dc_settings['alert_text_color'] : '';
            $button_text = isset($dc_settings['button_text']) ? $dc_settings['button_text'] : __('Get an alert', 'woocommerce-product-stock-alert');
            $unsubscribe_button_text = isset($dc_settings['unsubscribe_button_text']) ? $dc_settings['unsubscribe_button_text'] : __('Unsubscribe', 'woocommerce-product-stock-alert');
            $button_background_color = isset($dc_settings['button_background_color']) ? $dc_settings['button_background_color'] : '';
            $button_border_color = isset($dc_settings['button_border_color']) ? $dc_settings['button_border_color'] : '';
            $button_text_color = isset($dc_settings['button_text_color']) ? $dc_settings['button_text_color'] : '';
            $button_background_color_onhover = isset($dc_settings['button_background_color_onhover']) ? $dc_settings['button_background_color_onhover'] : '';
            $button_text_color_onhover = isset($dc_settings['button_text_color_onhover']) ? $dc_settings['button_text_color_onhover'] : '';
            $button_border_color_onhover = isset($dc_settings['button_border_color_onhover']) ? $dc_settings['button_border_color_onhover'] : '';
            $alert_success = isset($dc_settings['alert_success']) ? $dc_settings['alert_success'] : '';
            $alert_email_exist = isset($dc_settings['alert_email_exist']) ? $dc_settings['alert_email_exist'] : '';
            $valid_email = isset($dc_settings['valid_email']) ? $dc_settings['valid_email'] : '';
            $alert_unsubscribe_message = isset($dc_settings['alert_unsubscribe_message']) ? $dc_settings['alert_unsubscribe_message'] : '';
            $shown_interest_text = isset($dc_settings['shown_interest_text']) ? $dc_settings['shown_interest_text'] : __('Already %no_of_subscribed% persons shown interest.', 'woocommerce-product-stock-alert');
        }
        if (empty($alert_text)) {
            $alert_text = __('Get an alert when the product is in stock:', 'woocommerce-product-stock-alert');
        }
        if (empty($button_text)) {
            $button_text = __('Get an alert', 'woocommerce-product-stock-alert');
        }
        if (empty($alert_success)) {
            $alert_success = __('Thank you for your interest in <b>%product_title%</b>, you will receive an email alert when it becomes available.', 'woocommerce-product-stock-alert');
        }
        if (empty($alert_email_exist)) {
            $alert_email_exist = __('<b>%customer_email%</b> is already registered with <b>%product_title%</b>.', 'woocommerce-product-stock-alert');
        }
        if (empty($valid_email)) {
            $valid_email = __('Please enter a valid email id and try again.', 'woocommerce-product-stock-alert');
        }
        if (empty($alert_unsubscribe_message)) {
            $alert_unsubscribe_message = __('<b>%customer_email%</b> is successfully unregistered.', 'woocommerce-product-stock-alert');
        }
        $shown_interest_section = '';
        if (isset($dc_settings['is_enable_no_interest']) && $dc_settings['is_enable_no_interest'] == 'Enable' && get_no_subscribed_persons($product->get_id()) != 0) {
            if (!empty($shown_interest_text)) {
                $shown_interest_text = str_replace("%no_of_subscribed%", get_no_subscribed_persons($product->get_id()), $shown_interest_text);
                $shown_interest_section = '<p>' . $shown_interest_text . '</p>';
            }
        }


        if (!empty($alert_text)) {
            $alert_text_html = '<h6 style="color:' . $alert_text_color . '" class="subscribe_for_interest_text">' . $alert_text . '</h6>';
        } else {
            $alert_text_html = '<h6 class="subscribe_for_interest_text">' . $alert_text . '</h6>';
        }

        if (!empty($button_background_color) && !empty($button_border_color) && !empty($button_text_color) && !empty($button_background_color_onhover) && !empty($button_text_color_onhover) && !empty($button_border_color_onhover)) {
            $button_html = '<input type="button" style="background: ' . $button_background_color . '; color: ' . $button_text_color . '; border-color: ' . $button_border_color . '" class="stock_alert_button alert_button_hover" name="alert_button" value="' . $button_text . '" />';
            $unsubscribe_button_html = '<button class="unsubscribe_button" style="background: ' . $button_background_color . '; color: ' . $button_text_color . '; border-color: ' . $button_border_color . '">' . $unsubscribe_button_text . '</button>';
        } else {
            $button_html = '<input type="button" class="stock_alert_button" name="alert_button" value="' . $button_text . '" />';
            $unsubscribe_button_html = '<button class="unsubscribe_button">' . $unsubscribe_button_text . '</button>';
        }
        
        wp_localize_script('stock_alert_frontend_js', 'woo_stock_alert_script_data', array('ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'alert_text_html' => $alert_text_html,
            'button_html' => $button_html,
            'alert_success' => $alert_success,
            'alert_email_exist' => $alert_email_exist,
            'valid_email' => $valid_email,
            'processing' => __('Processing...', 'woocommerce-product-stock-alert'),
            'error_occurs' => __('Some error occurs', 'woocommerce-product-stock-alert'),
            'try_again' => __('Please try again.', 'woocommerce-product-stock-alert'),
            'unsubscribe_button' => $unsubscribe_button_html,
            'alert_unsubscribe_message' => $alert_unsubscribe_message
        ));

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
            $stock_interest = '<div class="alert_container">
									' . $alert_text_html . '
									<input type="text" class="stock_alert_email" name="alert_email" value="' . $user_email . '" />
									' . $button_html . '
									<input type="hidden" class="current_product_id" value="' . $product->get_id() . '" />
									<input type="hidden" class="current_product_name" value="' . $product->get_title() . '" />
									' . $shown_interest_section . '
								</div>';
        } else {
            $stock_interest = '<div class="alert_container">
									' . $alert_text_html . '
									<input type="text" class="stock_alert_email" name="alert_email" placeholder="abc@example.com" />
									' . $button_html . '
									<input type="hidden" class="current_product_id" value="' . $product->get_id() . '" />
									<input type="hidden" class="current_product_name" value="' . $product->get_title() . '" />
									' . $shown_interest_section . '
								</div>';
        }

        if ($product->is_type('simple')) {
            if (display_stock_alert_form($product)) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        } else if ($product->is_type('variable')) {
            $child_ids = array();
            $flag = 0;
            if ($product->has_child()) {
                $child_ids = $product->get_children();
                if (isset($child_ids) && !empty($child_ids)) {
                    if (isset($child_ids['visible'])) {
                        foreach ($child_ids['visible'] as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);

                            if (display_stock_alert_form($child_obj)) {
                                $flag = 1;
                            }
                        }
                    } else if (isset($child_ids['all'])) {
                        foreach ($child_ids['all'] as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);

                            if (display_stock_alert_form($child_obj)) {
                                $flag = 1;
                            }
                        }
                    } else {
                        foreach ($child_ids as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);
                            if (display_stock_alert_form($child_obj)) {
                                $flag = 1;
                            }
                        }
                    }
                }
            }

            if ($flag == 1) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        } else if ($product->is_type('subscription')) {
            if (display_stock_alert_form($product)) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        } else {
            if (display_stock_alert_form($product)) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        }

        do_action('woocommerce_product_stock_alert_form_after');

        // remove default stock alert position
        remove_action('woocommerce_single_product_summary', array($WOO_Product_Stock_Alert->frontend, 'get_alert_form'), 30);
    }

}
