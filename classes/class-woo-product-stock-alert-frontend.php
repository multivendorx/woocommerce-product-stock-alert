<?php

class WOO_Product_Stock_Alert_Frontend {

    public function __construct() {
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));

        add_action('woocommerce_simple_add_to_cart', array($this, 'display_in_simple_product'), 31);
        add_action('woocommerce_bundle_add_to_cart', array($this, 'display_in_simple_product'), 31);
        add_action('woocommerce_subscription_add_to_cart', array($this, 'display_in_simple_product'), 31);
        add_action('woocommerce_woosb_add_to_cart', array($this, 'display_in_simple_product'), 31);
        add_action('woocommerce_after_variations_form', array($this, 'display_in_no_variation_product'));
        add_filter('woocommerce_available_variation', array($this, 'display_in_variation'), 10, 3);
        // Some theme variation disabled by default if it is out of stock so for that workaround solution.
        add_filter('woocommerce_variation_is_active', array($this, 'enable_disabled_variation_dropdown'), 100, 2);
        //support for grouped products
        add_filter('woocommerce_grouped_product_list_column_price', array($this, 'display_in_grouped_product'), 10, 2);
        // Hover style
        add_action('wp_head', array($this, 'frontend_style'));    
    }

    function frontend_scripts() {
        global $WOO_Product_Stock_Alert;
        $frontend_script_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/js/';
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $stock_interest = $alert_text_html = $button_html = $border_size = $button_css = '';
        $settings_array = get_woo_form_settings_array();

        if (!empty($settings_array['alert_text'])) {
            $alert_text_html = '<h5 style="color:' . $settings_array['alert_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        } else {
            $alert_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        }

        $border_size = (!empty($settings_array['button_border_size'])) ? $settings_array['button_border_size'].'px' : '1px';

        if (!empty($settings_array['button_background_color']))
            $button_css .= "background:" . $settings_array['button_background_color'] . "; ";
        if (!empty($settings_array['button_text_color']))
            $button_css .= "color:" . $settings_array['button_text_color'] . "; ";
        if (!empty($settings_array['button_border_color']))
            $button_css .= "border: " . $border_size . " solid " . $settings_array['button_border_color'] . "; ";
        if (!empty($settings_array['button_font_size']))
            $button_css .= "font-size:" . $settings_array['button_font_size'] . "px; ";
        if (!empty($settings_array['button_border_redious']))
            $button_css .= "border-radius:" . $settings_array['button_border_redious'] . "px;";


        if (!empty($button_css)) {
            $button_html = '<button style="' . $button_css .'" class="stock_alert_button alert_button_hover" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button" style="' . $button_css .'">' . $settings_array['unsubscribe_button_text'] . '</button>';
        } else {
            $button_html = '<button class="stock_alert_button" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
        }

        if (function_exists('is_product')) {
            if (is_product()) {
                // Enqueue your frontend javascript from here
                wp_enqueue_script('stock_alert_frontend_js', $frontend_script_path . 'frontend' . $suffix . '.js', array('jquery'), $WOO_Product_Stock_Alert->version, true);
            
                wp_localize_script('stock_alert_frontend_js', 'woo_stock_alert_script_data', array('ajax_url' => admin_url('admin-ajax.php', 'relative'),
                    'alert_fields' => woo_stock_alert_fileds(),
                    'additional_fields' => apply_filters('woocommerce_product_stock_alert_form_additional_fields', []),
                    'alert_text_html' => $alert_text_html,
                    'button_html' => $button_html,
                    'alert_success' => $settings_array['alert_success'],
                    'alert_email_exist' => $settings_array['alert_email_exist'],
                    'valid_email' => $settings_array['valid_email'],
                    'ban_email_domain_text' => $settings_array['ban_email_domain_text'],
                    'ban_email_address_text' => $settings_array['ban_email_address_text'],
                    'double_opt_in_success' => $settings_array['double_opt_in_success'],
                    'processing' => __('Processing...', 'woocommerce-product-stock-alert'),
                    'error_occurs' => __('Some error occurs', 'woocommerce-product-stock-alert'),
                    'try_again' => __('Please try again.', 'woocommerce-product-stock-alert'),
                    'unsubscribe_button' => $unsubscribe_button_html,
                    'alert_unsubscribe_message' => $settings_array['alert_unsubscribe_message'],
                    'recaptcha_enabled' => apply_filters('woo_stock_alert_recaptcha_enableed', false)
                ));
            }
        }
    }

    function frontend_styles() {
        global $WOO_Product_Stock_Alert;
        $frontend_style_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/css/';
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        if (function_exists('is_product')) {
            if (is_product()) {
                // Enqueue your frontend stylesheet from here
                wp_enqueue_style('stock_alert_frontend_css', $frontend_style_path . 'frontend' . $suffix . '.css', array(), $WOO_Product_Stock_Alert->version);
            }
        }
    }

    function frontend_style() {
        $settings_array = get_woo_form_settings_array();
        $button_onhover_style = $border_size = '';
        $border_size = (!empty($settings_array['button_border_size'])) ? $settings_array['button_border_size'].'px' : '1px';

        if (isset($settings_array['button_background_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_background_color_onhover']) ? 'background: ' . $settings_array['button_background_color_onhover'] . ' !important;' : '';
        if (isset($settings_array['button_text_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_text_color_onhover']) ? ' color: ' . $settings_array['button_text_color_onhover'] . ' !important;' : '';
        if (isset($settings_array['button_border_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_border_color_onhover']) ? 'border: ' . $border_size . ' solid' . $settings_array['button_border_color_onhover'] . ' !important;' : '';
        if ($button_onhover_style) {
            echo '<style>
                button.alert_button_hover:hover, button.unsubscribe_button:hover {
                '. $button_onhover_style .'
                }
            </style>';
        }
    }

    /**
     * Display Request Stock Button for simple product
     *
     * @version 1.0.0
     */
    public function display_in_simple_product() {
        global $product;
        echo _e($this->display_subscribe_box($product));
    }

    public function display_in_grouped_product($value, $child) {
        $value = $value . $this->display_subscribe_box($child, array());
        return $value;
    }

    /**
     * Display Reuest Stock Button in no variation product
     *
     * @version 1.0.0
     */
    public function display_in_no_variation_product() {
        global $product;
        $product_type = $product->get_type();
        // Get Available variations?
        if ('variable' == $product_type) {
            $get_variations = count($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);
            $get_variations = $get_variations ? $product->get_available_variations() : false;
            if (!$get_variations) {
                echo _e($this->display_subscribe_box($product));
            }
        }
    }

    /**
     * Display Subscribe from in shop page and single product page.
     *
     * @param object $product all product.
     * @param object $variation all Variabtion product.
     *
     * @return html $html
     */
    public function display_subscribe_box( $product, $variation = [] ) {
        $get_option_backorder = get_woo_product_alert_plugin_settings('is_enable_backorders');
        $visibility_backorder = isset($get_option_backorder) ? true : false;

        if (!$variation && $this->is_stock_product($product)) {
            return $this->html_subscribe_form($product);
        } elseif ($variation && $this->is_stock_product($variation)) {
            return $this->html_subscribe_form($product, $variation);
        }
    }

    /**
     * Display Subscribe form chacking.
     *
     * @param object $product
     *
     * @return boolen $flag
     */
    public function is_stock_product($product) {
        $visibility_backorder = get_woo_product_alert_plugin_settings('is_enable_backorders');
        $flag = false;
        if ($product) {
            if (!$product->managing_stock()) {
                $stock_status = $product->get_stock_status();
                if ($stock_status && $stock_status == 'outofstock') {
                    $flag = true;
                } else if ($stock_status && $stock_status == 'onbackorder' && $visibility_backorder) {
                   $flag = true;
                }
            } else {
                if ($product->backorders_allowed() && $visibility_backorder) {
                    $flag = true;
                } else {
                    if (!$product->backorders_allowed() && $product->get_stock_quantity() < 1) {
                        $flag = true;
                    }
                }
            }
        }        
        return apply_filters('woo_stock_alert_is_stock_product', $flag, $product);
    }


    /**
     * Display Subscribe from in shop page and single product page.
     *
     * @param object $product all Product.
     * @param object $variation Variabtion product.
     * @param string $html prev html button.
     * @param int $loopActive check loopActive.
     *
     * @version 1.0.0
     */
    public function html_subscribe_form($product, $variation = []) {
        $variation_class = '';
        if ($variation) {
            $variation_id = $variation->get_id();
            $interested_person = get_no_subscribed_persons($variation->get_id(), 'woo_subscribed');
            $variation_class = 'stock_notifier-subscribe-form-' . $variation_id;
        } else {
            $variation_id = 0;
            $interested_person = get_no_subscribed_persons($product->get_id(), 'woo_subscribed');
        }
        $stock_interest = $alert_text_html = $button_html = $button_css = '';
        $dc_settings = array();
        $alert_text = $button_text = $button_background_color = $button_border_color = $button_text_color = $unsubscribe_button_text = '';
        $alert_success = $alert_email_exist = $valid_email = $alert_unsubscribe_message = $border_size = '';
        $settings_array = get_woo_form_settings_array();
        $alert_fields = woo_stock_alert_fileds();
        if (!empty($settings_array['alert_text'])) {
            $alert_text_html = '<h5 style="color:' . $settings_array['alert_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        } else {
            $alert_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        }

        $border_size = (!empty($settings_array['button_border_size'])) ? $settings_array['button_border_size'].'px' : '1px';

        if (!empty($settings_array['button_background_color']))
            $button_css .= "background:" . $settings_array['button_background_color'] . ";";
        if (!empty($settings_array['button_text_color']))
            $button_css .= "color:" . $settings_array['button_text_color'] . ";";
        if (!empty($settings_array['button_border_color']))
            $button_css .= "border: " . $border_size . " solid " . $settings_array['button_border_color'] . ";";
        if (!empty($settings_array['button_font_size']))
            $button_css .= "font-size:" . $settings_array['button_font_size'] . "px;";
        if (!empty($settings_array['button_border_redious']))
            $button_css .= "border-radius:" . $settings_array['button_border_redious'] . "px;";
            

        if (!empty($button_css)) {
            $button_html = '<button style="' . $button_css .'" class="stock_alert_button alert_button_hover" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button" style="' . $button_css .'">' . $settings_array['unsubscribe_button_text'] . '</button>';
        } else {
            $button_html = '<button class="stock_alert_button" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
        }

        $shown_interest_section = '';
        $shown_interest_text = $settings_array['shown_interest_text'];
        if (get_woo_product_alert_plugin_settings('is_enable_no_interest') && $interested_person != 0) {
            if ($shown_interest_text) {
                $shown_interest_text = str_replace("%no_of_subscribed%", $interested_person, $shown_interest_text);
                $shown_interest_section = '<p>' . $shown_interest_text . '</p>';
            }
        }

        $localization_data = array(
            'alert_text_html' => $alert_text_html,
            'button_html' => $button_html,
            'alert_success' => $settings_array['alert_success'],
            'alert_email_exist' => $settings_array['alert_email_exist'],
            'valid_email' => $settings_array['valid_email'],
            'ban_email_domain_text' => $settings_array['ban_email_domain_text'],
            'ban_email_address_text' => $settings_array['ban_email_address_text'],
            'double_opt_in_success' => $settings_array['double_opt_in_success'],
            'unsubscribe_button' => $unsubscribe_button_html,
            'alert_unsubscribe_message' => $settings_array['alert_unsubscribe_message'],
            'alert_fields' => $alert_fields,
            'recaptcha_enabled' => apply_filters('woo_stock_alert_recaptcha_enableed', false),
            'recaptcha_version' => apply_filters('woo_stock_alert_recaptcha_version', ''),
        );

        wp_localize_script('stock_alert_frontend_js', 'form_submission_text', $localization_data);
        
        $stock_interest .= '
            <div id="stock_notifier_main_form" style="border-radius:10px;" class="stock_notifier-subscribe-form ' . esc_attr($variation_class) .'">
                ' . $alert_text_html . '
                <div class="woo_fields_wrap"> ' . $alert_fields . '' . $button_html . '
                </div>
                <input type="hidden" class="current_product_id" value="' . esc_attr($product->get_id()) . '" />
                <input type="hidden" class="current_variation_id" value="' . esc_attr($variation_id) . '" />
                <input type="hidden" class="current_product_name" value="' . esc_attr($product->get_title()) . '" />
                ' . $shown_interest_section . '
            </div>';
        return $stock_interest;
    }


    /**
     * Display in variation product request stock button
     *
     * @param string $atts default attributes.
     * @param object $product all product.
     * @param object $variation variation product.
     *
     * @version 1.0.0
     */
    public function display_in_variation( $atts, $product, $variation ) {
        $get_stock                 = $atts['availability_html'];
        $atts['availability_html'] = $get_stock . $this->display_subscribe_box($product, $variation);
        return $atts;
    }

    /**
     * Enable disabled variation dropdown
     * @param int $active default 0.
     * @param array $variation variation product.
     *
     * @version 1.0.0
     */
    public function enable_disabled_variation_dropdown( $active, $variation ) {
        $get_disabled_variation    = get_option( 'stock_notifier_ignore_disabled_variation' );
        $ignore_disabled_variation = isset($get_disabled_variation) && '1' == $get_disabled_variation ? true : false;
        if (!$ignore_disabled_variation) {
            $active = true;
        }
        return $active;
    }
}