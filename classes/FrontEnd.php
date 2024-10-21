<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class FrontEnd {
    public function __construct() {
        //enqueue scripts
        add_action( 'wp_enqueue_scripts', [ &$this, 'frontend_scripts' ] );
        //enqueue styles
        add_action( 'wp_enqueue_scripts', [ &$this, 'frontend_styles' ] );

        add_action( 'woocommerce_simple_add_to_cart', [ $this, 'display_product_subscription_form' ], 31 );
        add_action( 'woocommerce_bundle_add_to_cart', [ $this, 'display_product_subscription_form' ], 31 );
        add_action( 'woocommerce_subscription_add_to_cart', [ $this, 'display_product_subscription_form' ], 31 );
        add_action( 'woocommerce_woosb_add_to_cart', [ $this, 'display_product_subscription_form' ], 31 );
        add_action( 'woocommerce_after_variations_form', [ $this, 'display_product_subscription_form' ], 31 );
        //support for grouped products
        add_filter( 'woocommerce_grouped_product_list_column_price', [ $this, 'display_in_grouped_product' ], 10, 2 );
        // Hover style
        add_action( 'wp_head', [ $this, 'frontend_hover_styles' ] );
        
        add_filter( 'stock_manager_display_product_lead_time', [ $this, 'display_product_lead_time' ], 10 );
    } 

    /**
     * Enque Frontend's JavaScript. And Send Localize data.
     * @return void
     */
    function frontend_scripts() {
        $frontend_script_path = SM()->plugin_url . 'frontend/js/';
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $suffix = ''; /// Should be removed.
        $settings_array = Utill::get_form_settings_array();

        $border_size = ( !empty( $settings_array[ 'button_border_size' ] ) ) ? $settings_array[ 'button_border_size' ].'px' : '1px';

        $button_css = '';
        if ( !empty( $settings_array[ 'button_background_color' ] ) )
            $button_css .= "background:" . $settings_array[ 'button_background_color' ] . "; ";
        if ( !empty( $settings_array[ 'button_text_color' ] ) )
            $button_css .= "color:" . $settings_array[ 'button_text_color' ] . "; ";
        if ( !empty( $settings_array[ 'button_border_color' ] ) )
            $button_css .= "border: " . $border_size . " solid " . $settings_array[ 'button_border_color' ] . "; ";
        if ( !empty( $settings_array[ 'button_font_size' ] ) )
            $button_css .= "font-size:" . $settings_array[ 'button_font_size' ] . "px; ";
        if ( !empty( $settings_array[ 'button_border_redious' ] ) )
            $button_css .= "border-radius:" . $settings_array[ 'button_border_redious' ] . "px;";

        $subscribe_button_html = '<button style="' . $button_css .'" class="stock-manager-button alert_button_hover" name="alert_button">' . $settings_array[ 'button_text' ] . '</button>';
        $unsubscribe_button_html = '<button class="unsubscribe-button" style="' . $button_css .'">' . $settings_array[ 'unsubscribe_button_text' ] . '</button>';

        if ( is_product() || is_shop() || is_product_category() ) {
            // Enqueue your frontend javascript from here
            wp_enqueue_script( 'stock_manager_frontend_js', $frontend_script_path . 'frontend' . $suffix . '.js', [ 'jquery' ], SM()->version, true );
        
            wp_localize_script( 'stock_manager_frontend_js', 'localizeData', [
                'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ), 
                'nonce'  => wp_create_nonce( 'stock-manager-security-nonce' ), 
                'additional_fields' => apply_filters( 'woocommerce_stock_manager_form_additional_fields', [] ), 
                'button_html' => $subscribe_button_html, 
                'alert_success' => $settings_array[ 'alert_success' ], 
                'alert_email_exist' => $settings_array[ 'alert_email_exist' ], 
                'valid_email' => $settings_array[ 'valid_email' ], 
                'ban_email_domain_text' => $settings_array[ 'ban_email_domain_text' ], 
                'ban_email_address_text' => $settings_array[ 'ban_email_address_text' ], 
                'double_opt_in_success' => $settings_array[ 'double_opt_in_success' ], 
                'processing' => __( 'Processing...', 'woocommerce-stock-manager' ), 
                'error_occurs' => __( 'Some error occurs', 'woocommerce-stock-manager' ), 
                'try_again' => __( 'Please try again.', 'woocommerce-stock-manager' ), 
                'unsubscribe_button' => $unsubscribe_button_html, 
                'alert_unsubscribe_message' => $settings_array[ 'alert_unsubscribe_message' ], 
                'recaptcha_enabled' => apply_filters( 'stock_manager_recaptcha_enabled', false )
            ]);
        }
    }

    /**
     * Enqueue fronted css. 
     * @return void
     */
    function frontend_styles() {
        $frontend_style_path = SM()->plugin_url . 'frontend/css/';
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        if ( function_exists( 'is_product' ) ) {
            if ( is_product() ) {
                // Enqueue your frontend stylesheet from here
                wp_enqueue_style( 'stock_manager_frontend_css', $frontend_style_path . 'frontend' . $suffix . '.css', [], SM()->version );
            } 
        } 
    } 

    /**
     * Set frontend's button hover style on 'wp_head' hook.
     * @return void
     */
    function frontend_hover_styles() {
        $settings_array = Utill::get_form_settings_array();
        $button_onhover_style = $border_size = '';
        $border_size = ( !empty( $settings_array[ 'button_border_size' ] ) ) ? $settings_array[ 'button_border_size' ].'px' : '1px';

        if ( isset( $settings_array[ 'button_background_color_onhover' ] ) )
            $button_onhover_style .= !empty( $settings_array[ 'button_background_color_onhover' ] ) ? 'background: ' . $settings_array[ 'button_background_color_onhover' ] . ' !important;' : '';
        if ( isset( $settings_array[ 'button_text_color_onhover' ] ) )
            $button_onhover_style .= !empty( $settings_array[ 'button_text_color_onhover' ] ) ? ' color: ' . $settings_array[ 'button_text_color_onhover' ] . ' !important;' : '';
        if ( isset( $settings_array[ 'button_border_color_onhover' ] ) )
            $button_onhover_style .= !empty( $settings_array[ 'button_border_color_onhover' ] ) ? 'border: ' . $border_size . ' solid' . $settings_array[ 'button_border_color_onhover' ] . ' !important;' : '';
        if ( $button_onhover_style ) {
            echo '<style>
                button.alert_button_hover:hover, button.unsubscribe_button:hover {
                '. esc_html( $button_onhover_style ) .'
                } 
            </style>';
        } 
    }

    /**
     * Display product subscription form if product is outof stock
     *
     * @version 1.0.0
     */
    public function display_product_subscription_form($productObj = null) {
        global $product;

        $productObj = is_int($productObj) ? wc_get_product($productObj) : ($productObj ?: $product);

        if ( empty( $productObj ) )
            return;
        $guest_subscription_enabled = SM()->setting->get_setting( 'is_guest_subscriptions_enable' );
        $guest_subscription_enabled = is_array( $guest_subscription_enabled ) ? reset( $guest_subscription_enabled ) : false;
        if (!$guest_subscription_enabled && ! is_user_logged_in()) {
            return;
        }

        $backorders_enabled = SM()->setting->get_setting( 'is_enable_backorders' );
        $backorders_enabled = is_array( $backorders_enabled ) ? reset( $backorders_enabled ) : false;

        $stock_status   = $productObj->get_stock_status();
        if ( $stock_status == 'onbackorder' && !$backorders_enabled )
            return;

        if ( $productObj->is_type( 'variable' ) ) {
            $get_variations = count( $productObj->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $productObj );
            $get_variations = $get_variations ? $productObj->get_available_variations() : false;
            if ( $get_variations ) {
                echo '<div class="stock-notifier-shortcode-subscribe-form" data-product-id="' . esc_attr( $productObj->get_id() ) . '"></div>';
            } else {
                echo ( $this->get_subscribe_form( $productObj ) );
            } 
        } else {
            echo ( $this->get_subscribe_form( $productObj ) );
        } 
    }

    /**
     * Display Request Stock Form for grouped product
     *
     * @param string $value default html
     * @param object $child indivisual child of grouped product
     * 
     * @version 1.0.0
     */
    public function display_in_grouped_product( $value, $child ) {
        $value = $value . $this->get_subscribe_form( $child );
        return $value;
    }
    
    /**
     * Get subscribe from's HTML content for a particular product.
     * If the product is not outofstock it return empty string.
     *
     * @param mixed $product product variable
     * @param mixed $variation variation variable default null
     * @return string HTML of subscribe form
     */
    public function get_subscribe_form( $product, $variation = null ) {
        if ( ! Subscriber::is_product_outofstock( $variation ? $variation : $product ) ) {
            return "";
        } 
        $stock_manager_fields_array = [];
        $stock_manager_fields_html = $user_email = '';
        $separator = apply_filters( 'stock_manager_form_fileds_separator', '<br>' );
        $settings_array = Utill::get_form_settings_array();
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
        } 
        $placeholder = $settings_array[ 'email_placeholder_text' ];
        $alert_fields = apply_filters( 'stock_manager_fileds_array', [ 
            'alert_email' => [ 
                'type' => 'text', 
                'class'=> 'stock-manager-email', 
                'value'=> $user_email, 
                'placeholder' => $placeholder
            ]
        ], $settings_array );
        if ( $alert_fields ) {
            foreach ( $alert_fields as $key => $fvalue ) {
                $type = in_array( $fvalue[ 'type' ], [ 'recaptcha-v3', 'text', 'number', 'email' ] ) ? esc_attr( $fvalue[ 'type' ] ) : 'text';
                $class = isset( $fvalue[ 'class' ] ) ? esc_attr( $fvalue[ 'class' ] ) : 'stock_manager_' . $key;
                $value = isset( $fvalue[ 'value' ] ) ? esc_attr( $fvalue[ 'value' ] ) : '';
                $placeholder = isset( $fvalue[ 'placeholder' ] ) ? esc_attr( $fvalue[ 'placeholder' ] ) : '';
                switch ( $fvalue[ 'type' ] ) {
                    case 'recaptcha-v3':
                        $recaptcha_type = isset( $fvalue[ 'version' ] ) ? esc_attr( $fvalue[ 'version' ] ) : 'v3';
                        $sitekey = isset( $fvalue[ 'sitekey' ] ) ? esc_attr( $fvalue[ 'sitekey' ] ) : '';
                        $secretkey = isset( $fvalue[ 'secretkey' ] ) ? esc_attr( $fvalue[ 'secretkey' ] ) : '';

                        $recaptchaScript = '
                        <script>
                            grecaptcha.ready( function () {
                                grecaptcha.execute( "' . $sitekey . '" ).then( function ( token ) {
                                    var recaptchaResponse = document.getElementById( "recaptchav3_response" );
                                    recaptchaResponse.value = token;
                                }  );
                            }  );
                        </script>';
                        
                        $recaptchaResponseInput = '<input type="hidden" id="recaptchav3_response" name="recaptchav3_response" value="" />';
                        $recaptchaSiteKeyInput = '<input type="hidden" id="recaptchav3_sitekey" name="recaptchav3_sitekey" value="' . esc_html( $sitekey ) . '" />';
                        $recaptchaSecretKeyInput = '<input type="hidden" id="recaptchav3_secretkey" name="recaptchav3_secretkey" value="' . esc_html( $secretkey ) . '" />';

                        $stock_manager_fields_array[] = $recaptchaScript . $recaptchaResponseInput . $recaptchaSiteKeyInput . $recaptchaSecretKeyInput;
                        break;
                    default:
                        $stock_manager_fields_array[] = '<input id="stock_manager_' . $key . '" type="' . $type . '" name="' . $key . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '" >';
                        break;
                } 
            } 
        } 
        if ( $stock_manager_fields_array ) {
            $stock_manager_fields_html = implode( $separator, $stock_manager_fields_array );
        } 

        $alert_text_html = '<h5 style="color:' . esc_html( $settings_array[ 'alert_text_color' ] ) . '" class="subscribe_for_interest_text">' . esc_html( $settings_array[ 'alert_text' ] ) . '</h5>';

        $button_css = "";
        $border_size = ( !empty( $settings_array[ 'button_border_size' ] ) ) ? esc_html( $settings_array[ 'button_border_size' ] ).'px' : '1px';
        if ( !empty( $settings_array[ 'button_background_color' ] ) )
            $button_css .= "background:" . esc_html( $settings_array[ 'button_background_color' ] ) . ";";
        if ( !empty( $settings_array[ 'button_text_color' ] ) )
            $button_css .= "color:" . esc_html( $settings_array[ 'button_text_color' ] ) . ";";
        if ( !empty( $settings_array[ 'button_border_color' ] ) )
            $button_css .= "border: " . $border_size . " solid " . esc_html( $settings_array[ 'button_border_color' ] ) . ";";
        if ( !empty( $settings_array[ 'button_font_size' ] ) )
            $button_css .= "font-size:" . esc_html( $settings_array[ 'button_font_size' ] ) . "px;";
        if ( !empty( $settings_array[ 'button_border_radious' ] ) )
            $button_css .= "border-radius:" . esc_html( $settings_array[ 'button_border_radious' ] ) . "px;";

        $button_html = '<button style="' . $button_css .'" class="stock-manager-button alert_button_hover" name="alert_button">' . esc_html( $settings_array[ 'button_text' ] ) . '</button>';

        $interested_person = get_post_meta( $variation ? $variation->get_id() : $product->get_id(), 'no_of_subscribers', true );
        $interested_person = ( isset( $interested_person ) && $interested_person > 0 ) ? $interested_person : 0;

        $shown_interest_html = '';
        $shown_interest_text = esc_html( $settings_array[ 'shown_interest_text' ] );

        $is_enable_no_interest = SM()->setting->get_setting( 'is_enable_no_interest' );
        $is_enable_no_interest = is_array( $is_enable_no_interest ) ? reset( $is_enable_no_interest ) : false;
        
        if ( $is_enable_no_interest && $interested_person != 0 && $shown_interest_text ) {
            $shown_interest_text = str_replace( "%no_of_subscribed%", $interested_person, $shown_interest_text );
            $shown_interest_html = '<p>' . $shown_interest_text . '</p>';
        } 

        $lead_text_html = apply_filters( 'stock_manager_display_product_lead_time', $variation ? $variation : $product );
        return
        $lead_text_html .
        '<div class="stock-notifier-subscribe-form" style="border-radius:10px;">
            ' . $alert_text_html . '
            <div class="fields_wrap"> ' . $stock_manager_fields_html . '' . $button_html . '
            </div>
            <input type="hidden" class="current-product-id" value="' . esc_attr( $product->get_id() ) . '" />
            <input type="hidden" class="current-variation-id" value="' . esc_attr( $variation ? $variation->get_id() : 0 ) . '" />
            <input type="hidden" class="current-product-name" value="' . esc_attr( $product->get_title() ) . '" />
            ' . $shown_interest_html . '
        </div>';
    }

    /**
     * Display lead time for a product
     *
     * @version 2.5.12
     */
    public function display_product_lead_time( $product ){
        if ( empty( $product ) )
            return;
        $display_lead_times  = SM()->setting->get_setting( 'display_lead_times' );
        if ( !empty($display_lead_times) && in_array($product->get_stock_status(), $display_lead_times) ) {
            $lead_time_static_text = SM()->setting->get_setting( 'lead_time_static_text' );
            return '<p>' . esc_html( $lead_time_static_text ) . '</p>';
        }
        return '';
    }
}