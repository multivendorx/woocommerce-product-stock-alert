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

        $stock_interest = $alert_text_html = $button_html = $user_email = '';

        $settings_array = get_woo_form_settings_array();
        if (!empty($settings_array['alert_text'])) {
            $alert_text_html = '<h5 style="color:' . $settings_array['alert_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        } else {
            $alert_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        }

        if (!empty($settings_array['button_background_color']) && !empty($settings_array['button_border_color']) && !empty($settings_array['button_text_color']) && !empty($settings_array['button_background_color_onhover']) && !empty($settings_array['button_text_color_onhover']) && !empty($settings_array['button_border_color_onhover'])) {
            $button_html = '<button style="background: ' . $settings_array['button_background_color'] . '; color: ' . $settings_array['button_text_color'] . '; border-color: ' . $settings_array['button_border_color'] . '; font-size: '. $settings_array['button_font_size'] .';" class="stock_alert_button alert_button_hover" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button" style="background: ' . $settings_array['button_background_color'] . '; color: ' . $settings_array['button_text_color'] . '; border-color: ' . $settings_array['button_border_color'] . '; font-size: '. $settings_array['button_font_size'] .';">' . $settings_array['unsubscribe_button_text'] . '</button>';
        } else {
            $button_html = '<button class="stock_alert_button" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
        }

        $shown_interest_section = '';
        $shown_interest_text = $settings_array['shown_interest_text'];
        if (get_mvx_product_alert_plugin_settings('is_enable_no_interest') && get_no_subscribed_persons($product->get_id()) != 0) {
            if ($shown_interest_text) {
                $shown_interest_text = str_replace("%no_of_subscribed%", get_no_subscribed_persons($product->get_id()), $shown_interest_text);
                $shown_interest_section = '<p>' . $shown_interest_text . '</p>';
            }
        }

        wp_localize_script('stock_alert_frontend_js', 'woo_stock_alert_script_data', array('ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'additional_fields' => apply_filters('woocommerce_product_stock_alert_form_additional_fields', []),
            'alert_text_html' => $alert_text_html,
            'button_html' => $button_html,
            'alert_success' => $settings_array['alert_success'],
            'alert_email_exist' => $settings_array['alert_email_exist'],
            'valid_email' => $settings_array['valid_email'],
            'processing' => __('Processing...', 'woocommerce-product-stock-alert'),
            'error_occurs' => __('Some error occurs', 'woocommerce-product-stock-alert'),
            'try_again' => __('Please try again.', 'woocommerce-product-stock-alert'),
            'unsubscribe_button' => $unsubscribe_button_html,
            'alert_unsubscribe_message' => $settings_array['alert_unsubscribe_message']
        ));
        

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
        }

        $stock_interest = apply_filters('woocommerce_product_stock_alert_form', '<div class="alert_container">
			' . $alert_text_html . '
			<input type="text" class="stock_alert_email" name="alert_email" placeholder="abc@example.com" value="' . $user_email . '" />
			' . $button_html . '
			<input type="hidden" class="current_product_id" value="' . $product->get_id() . '" />
			<input type="hidden" class="current_product_name" value="' . $product->get_title() . '" />
			' . $shown_interest_section . '
		</div>', $alert_text_html, $user_email, $button_html, $product, $shown_interest_section);

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
