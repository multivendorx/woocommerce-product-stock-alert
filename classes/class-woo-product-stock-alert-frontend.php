<?php

class WOO_Product_Stock_Alert_Frontend {

    public function __construct() {
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));

        if (get_mvx_product_alert_plugin_settings('is_enable')) {
            add_action( 'woocommerce_simple_add_to_cart', array( $this, 'display_in_simple_product' ), 31 );
            add_action( 'woocommerce_bundle_add_to_cart', array( $this, 'display_in_simple_product' ), 31 );
            add_action('woocommerce_subscription_add_to_cart', array($this, 'display_in_simple_product'), 31);
            add_action( 'woocommerce_woosb_add_to_cart', array( $this, 'display_in_simple_product' ), 31 );
            add_action( 'woocommerce_after_variations_form', array( $this, 'display_in_no_variation_product' ) );
            add_filter( 'woocommerce_available_variation', array( $this, 'display_in_variation' ), 10, 3 );
            // Some theme variation disabled by default if it is out of stock so for that workaround solution.
            add_filter( 'woocommerce_variation_is_active', array( $this, 'enable_disabled_variation_dropdown' ), 100, 2 );
            //support for grouped products
            add_filter('woocommerce_grouped_product_list_column_price', array($this, 'display_in_grouped_product'), 10, 2);
            // Hover style
            add_action('wp_head', array($this, 'frontend_style'));
        }
    }

    function frontend_scripts() {
        global $WOO_Product_Stock_Alert;
        $frontend_script_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/js/';
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';
        $stock_interest = $alert_text_html = $button_html = $button_css = '';
        $settings_array = get_woo_form_settings_array();

        if (!empty($settings_array['alert_text'])) {
            $alert_text_html = '<h5 style="color:' . $settings_array['alert_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        } else {
            $alert_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        }

        if (!empty($settings_array['button_background_color']))
            $button_css .= "background:" . $settings_array['button_background_color'] . "; ";
        if (!empty($settings_array['button_text_color']))
            $button_css .= "color:" . $settings_array['button_text_color'] . "; ";
        if (!empty($settings_array['button_border_color']))
            $button_css .= "border: 1px solid " . $settings_array['button_border_color'] . "; ";
        if (!empty($settings_array['button_font_size']))
            $button_css .= "font-size:" . $settings_array['button_font_size'] . "; ";


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
        $settings_array = get_woo_form_settings_array();
        $button_onhover_style = '';
        if (isset($settings_array['button_background_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_background_color_onhover']) ? 'background: ' . $settings_array['button_background_color_onhover'] . ' !important;' : '';
        if (isset($settings_array['button_text_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_text_color_onhover']) ? ' color: ' . $settings_array['button_text_color_onhover'] . ' !important;' : '';
        if (isset($settings_array['button_border_color_onhover']))
            $button_onhover_style .= !empty($settings_array['button_border_color_onhover']) ? 'border: 1px solid' . $settings_array['button_border_color_onhover'] . ' !important;' : '';
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
        echo _e( $this->display_subscribe_box( $product ) );
    }

    public function display_in_grouped_product( $value, $child) {
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
        if ( 'variable' == $product_type ) {
            $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
            $get_variations = $get_variations ? $product->get_available_variations() : false;
            if ( ! $get_variations ) {
                echo _e( $this->display_subscribe_box( $product ) );
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
        $get_option_backorder = get_mvx_product_alert_plugin_settings('is_enable_backorders');
        $visibility_backorder = isset( $get_option_backorder ) ? true : false;

        if ( ! $variation && ! $product->is_in_stock() || ( ( ! $variation && ( ( $product->managing_stock() && $product->backorders_allowed() && $product->is_on_backorder( 1 ) ) || $product->is_on_backorder( 1 ) ) && $visibility_backorder ) ) ) {
            return $this->html_subscribe_form( $product );
        } elseif ( $variation && ! $variation->is_in_stock() || ( ( $variation && ( ( $variation->managing_stock() && $variation->backorders_allowed() && $variation->is_on_backorder( 1 ) ) || $variation->is_on_backorder( 1 ) ) && $visibility_backorder ) ) ) {
            return $this->html_subscribe_form( $product, $variation );
        }
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
    public function html_subscribe_form( $product, $variation = [] ) {
        $stock_notifier_random_code = bin2hex( random_bytes( 12 ) );
        $variation_class = '';
        if ( $variation ) {
            $variation_id = $variation->get_id();
            $interested_person = get_no_subscribed_persons($variation->get_id());
            $variation_class = 'stock_notifier-subscribe-form-' . $variation_id;
        } else {
            $variation_id = 0;
            $interested_person = get_no_subscribed_persons($product->get_id());
        }
        $stock_interest = $alert_text_html = $button_html = $button_css = '';
        $dc_settings = array();
        $alert_text = $button_text = $button_background_color = $button_border_color = $button_text_color = $unsubscribe_button_text = '';
        $alert_success = $alert_email_exist = $valid_email = $alert_unsubscribe_message = '';
        $settings_array = get_woo_form_settings_array();

        if (!empty($settings_array['alert_text'])) {
            $alert_text_html = '<h5 style="color:' . $settings_array['alert_text_color'] . '" class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        } else {
            $alert_text_html = '<h5 class="subscribe_for_interest_text">' . $settings_array['alert_text'] . '</h5>';
        }

        if (!empty($settings_array['button_background_color']))
            $button_css .= "background:" . $settings_array['button_background_color'] . ";";
        if (!empty($settings_array['button_text_color']))
            $button_css .= "color:" . $settings_array['button_text_color'] . ";";
        if (!empty($settings_array['button_border_color']))
            $button_css .= "border: 1px solid " . $settings_array['button_border_color'] . ";";
        if (!empty($settings_array['button_font_size']))
            $button_css .= "font-size:" . $settings_array['button_font_size'] . ";";


        if (!empty($button_css)) {
            $button_html = '<button style="' . $button_css .'" class="stock_alert_button alert_button_hover" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button" style="' . $button_css .'">' . $settings_array['unsubscribe_button_text'] . '</button>';
        } else {
            $button_html = '<button class="stock_alert_button" name="alert_button">' . $settings_array['button_text'] . '</button>';
            $unsubscribe_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
        }

        $shown_interest_section = '';
        $shown_interest_text = $settings_array['shown_interest_text'];
        if (get_mvx_product_alert_plugin_settings('is_enable_no_interest') && $interested_person != 0) {
            if ($shown_interest_text) {
                $shown_interest_text = str_replace("%no_of_subscribed%", $interested_person, $shown_interest_text);
                $shown_interest_section = '<p>' . $shown_interest_text . '</p>';
            }
        }

        wp_localize_script(
            'stock_alert_frontend_js', 'form_submission_text', array(
            'alert_text_html' => $alert_text_html,
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

        $stock_interest .= apply_filters('woocommerce_product_stock_alert_form', '
            <div id="stock_notifier_main_form" style="border-radius:10px;" class="stock_notifier-subscribe-form ' . esc_attr( $variation_class ) .'">
                    ' . $alert_text_html . '
                    <input type="text" class="stock_alert_email" name="alert_email" value="' . $user_email . '" placeholder="' . $placeholder . '" />
                    ' . $button_html . '
                    <input type="hidden" class="current_product_id" value="' . $product->get_id() . '" />
                    <input type="hidden" class="current_variation_id" value="' . $variation_id . '" />
                    <input type="hidden" class="current_product_name" value="' . $product->get_title() . '" />
                    ' . $shown_interest_section . '
                </div>
            </div>', $alert_text_html, $user_email, $button_html, $product, $shown_interest_section);
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
        $atts['availability_html'] = $get_stock . $this->display_subscribe_box( $product, $variation );
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
        $ignore_disabled_variation = isset( $get_disabled_variation ) && '1' == $get_disabled_variation ? true : false;
        if ( ! $ignore_disabled_variation ) {
            $active = true;
        }
        return $active;
    }
}
