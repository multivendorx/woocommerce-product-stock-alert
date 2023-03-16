<?php

class WOO_Product_Stock_Alert_Frontend {

    public function __construct() {
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));
        if (get_dc_plugin_settings('is_enable')) {
            // Hover style
            add_action('wp_head', array($this, 'frontend_style'));
            //HTML for getting customer email
            add_action('woocommerce_single_product_summary', array($this, 'get_alert_form'), 30);
        } 
    }

    function frontend_scripts() {
        global $WOO_Product_Stock_Alert;
        $frontend_script_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/js/';
        $stock_interest = $alert_text_html = $button_html = '';
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

        if (function_exists('is_product')) {
            if (is_product()) {
                // Enqueue your frontend javascript from here
                wp_enqueue_script('stock_alert_frontend_js', $frontend_script_path . 'frontend.js', array('jquery'), $WOO_Product_Stock_Alert->version, true);
            
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
            }
        }
    }

    function frontend_styles() {
        global $WOO_Product_Stock_Alert;
        $frontend_style_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/css/';

        if (function_exists('is_product')) {
            if (is_product()) {
                // Enqueue your frontend stylesheet from here
                wp_enqueue_style('stock_alert_frontend_css', $frontend_style_path . 'frontend.css', array(), $WOO_Product_Stock_Alert->version);
            }
        }
    }

    function frontend_style() {
        $button_background_color_onhover = $button_text_color_onhover = '';

        $button_background_color_onhover = get_dc_plugin_settings('button_background_color_onhover') ? get_dc_plugin_settings('button_background_color_onhover') : '';
        $button_text_color_onhover = get_dc_plugin_settings('button_text_color_onhover') ? get_dc_plugin_settings('button_text_color_onhover') : '';
        $button_border_color_onhover = get_dc_plugin_settings('button_border_color_onhover') ? get_dc_plugin_settings('button_border_color_onhover') : '';
        

        echo '<style>
			button.alert_button_hover:hover, button.unsubscribe_button:hover {
				background: ' . $button_background_color_onhover . ' !important;
				color: ' . $button_text_color_onhover . ' !important;
				border-color: ' . $button_border_color_onhover . ' !important;
			}
		</style>';
    }

    function get_alert_form() {
        global $WOO_Product_Stock_Alert, $product;

        if (empty($product))
            return;
        //var_dump(get_post_meta( $product->get_id(), 'no_of_subscribers', true ));
        $stock_interest = $alert_text_html = $button_html = '';
        $dc_settings = array();
        $alert_text = $button_text = $button_background_color = $button_border_color = $button_text_color = $unsubscribe_button_text = '';
        $alert_success = $alert_email_exist = $valid_email = $alert_unsubscribe_message = '';
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
        if (get_dc_plugin_settings('is_enable_no_interest') && get_no_subscribed_persons($product->get_id()) != 0) {
            if ($shown_interest_text) {
                $shown_interest_text = str_replace("%no_of_subscribed%", get_no_subscribed_persons($product->get_id()), $shown_interest_text);
                $shown_interest_section = '<p>' . $shown_interest_text . '</p>';
            }
        }

        wp_localize_script(
            'stock_alert_frontend_js', 'form_submission_text', array('alert_text_html' => $alert_text_html,
            'button_html' => $button_html,
            'alert_success' => $settings_array['alert_success'],
            'alert_email_exist' => $settings_array['alert_email_exist'],
            'valid_email' => $settings_array['valid_email'],
            'unsubscribe_button' => $unsubscribe_button_html,
            'alert_unsubscribe_message' => $settings_array['alert_unsubscribe_message']
        ));

        $user_email = '';
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
        }
        $placeholder = __('Enter your email', 'woocommerce-product-stock-alert');
        $placeholder = apply_filters('wc_product_stock_alert_box_email_placeholder', $placeholder);

        $stock_interest .= apply_filters('woocommerce_product_stock_alert_form', '<div class="alert_container">
					' . $alert_text_html . '
					<input type="text" class="stock_alert_email" name="alert_email" value="' . $user_email . '" placeholder="' . $placeholder . '" />
					' . $button_html . '
					<input type="hidden" class="current_product_id" value="' . $product->get_id() . '" />
					<input type="hidden" class="current_product_name" value="' . $product->get_title() . '" />
					' . $shown_interest_section . '
				</div>', $alert_text_html, $user_email, $button_html, $product, $shown_interest_section);


        if ($product->is_type('simple')) {
            if ($this->display_stock_alert_form($product)) {
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

                            if ($this->display_stock_alert_form($child_obj)) {
                                $flag = 1;
                            }
                        }
                    } elseif (isset($child_ids['all'])) {
                        foreach ($child_ids['all'] as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);

                            if ($this->display_stock_alert_form($child_obj)) {
                                $flag = 1;
                            }
                        }
                    } else {
                        foreach ($child_ids as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);
                            if ($this->display_stock_alert_form($child_obj)) {
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
        } elseif ($product->is_type('subscription')) {
            if ($this->display_stock_alert_form($product)) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        } else {
            if ($this->display_stock_alert_form($product)) {
                do_action('woocommerce_product_stock_alert_form_before');
                echo $stock_interest;
                do_action('woocommerce_product_stock_alert_form_after');
            }
        }
    }

    function display_stock_alert_form($product) {
        $display_stock_alert_form = false;

        if ($product) { 
            $managing_stock = $product->managing_stock();
            $stock_quantity = $product->get_stock_quantity();
            $manage_stock = $product->get_manage_stock();
            $stock_status = $product->get_stock_status();
            $is_in_stock = $product->is_in_stock();
            $is_on_backorder = $product->is_on_backorder( 1 );
            
            if ( ! $is_in_stock ) {
                    $display_stock_alert_form = true;
            } elseif ( $managing_stock && $is_on_backorder && get_dc_plugin_settings('is_enable_backorders') ) {
                    $display_stock_alert_form = true;
            } elseif ( $managing_stock ) {
                if(get_option('woocommerce_notify_no_stock_amount')){
                    if($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount') && get_dc_plugin_settings('is_enable_backorders') ){
                        $display_stock_alert_form = true;
                    }
                }
            }
        }

        return $display_stock_alert_form;
    }

}
