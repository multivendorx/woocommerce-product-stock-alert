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
        $frontend_script_path = $WOO_Product_Stock_Alert->plugin_url . 'assets/frontend/js/';
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';

        do_action('woocommerce_product_stock_alert_form_before');

        global $product;

        if (empty($product))
            return;

        if ($product->is_type('simple')) {
            echo _e( $WOO_Product_Stock_Alert->frontend->display_subscribe_box( $product ) );
        } else if ($product->is_type('variable')) {
            $stock_interest = $alert_text_html = $button_html = $button_css = '';
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
                $button_css .= "border: 1px solid" . $settings_array['button_border_color'] . ";";
            if (!empty($settings_array['button_text_color']))
                $button_css .= "font-size:" . $settings_array['button_font_size'] . ";";


            if (!empty($button_css)) {
                $button_html = '<button style="' . $button_css .';" class="stock_alert_button alert_button_hover" name="alert_button">' . $settings_array['button_text'] . '</button>';
                $unsubscribe_button_html = '<button class="unsubscribe_button" style="' . $button_css .';">' . $settings_array['unsubscribe_button_text'] . '</button>';
            } else {
                $button_html = '<button class="stock_alert_button" name="alert_button">' . $settings_array['button_text'] . '</button>';
                $unsubscribe_button_html = '<button class="unsubscribe_button">' . $settings_array['unsubscribe_button_text'] . '</button>';
            }
            if (function_exists('is_product')) {
                if (is_product()) {
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    // Enqueue your frontend javascript from here
                    wp_enqueue_script('stock_alert_shortcode_js', $frontend_script_path . 'shortcode' . $suffix . '.js', array('jquery'), $WOO_Product_Stock_Alert->version, true);
                
                    wp_localize_script('stock_alert_shortcode_js', 'stock_alert_sc_data', array('ajax_url' => admin_url('admin-ajax.php', 'relative'),
                        'product_id' => $product->get_id(),
                        'product_title' => $product->get_title(),
                        'additional_fields' => apply_filters('woocommerce_product_stock_alert_form_additional_fields', []),
                        'alert_text_html' => $alert_text_html,
                        'button_html' => $button_html,
                    ));
                }
            }
            echo '<div class="stock_notifier-shortcode-subscribe-form"></div>';
        } else {
            echo _e( $WOO_Product_Stock_Alert->frontend->display_subscribe_box( $product ) );
        }

        do_action('woocommerce_product_stock_alert_form_after');

        // remove default stock alert position
        remove_action( 'woocommerce_simple_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_bundle_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_woosb_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_after_variations_form', array( $WOO_Product_Stock_Alert->frontend, 'display_in_no_variation_product' ) );
        remove_action( 'woocommerce_grouped_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 32 );
        remove_filter( 'woocommerce_available_variation', array( $WOO_Product_Stock_Alert->frontend, 'display_in_variation' ), 10, 3 );
        // Some theme variation disabled by default if it is out of stock so for that workaround solution.
        remove_filter( 'woocommerce_variation_is_active', array( $WOO_Product_Stock_Alert->frontend, 'enable_disabled_variation_dropdown' ), 100, 2 );
    }

}
