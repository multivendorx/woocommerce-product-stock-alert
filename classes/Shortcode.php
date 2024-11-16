<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;

class Shortcode {

	public function __construct() {
		// Product Stock Manager Form Shortcode.
		add_shortcode( 'display_stock_manager_form', [ $this, 'display_stock_manager_form' ] );
        add_shortcode( 'display_stock_alert_form', [ $this, 'display_stock_manager_form' ] );

        // Hook to ensure actions are removed at the right time
        add_action( 'wp', [ $this, 'conditionally_remove_actions' ] );
	} 

    /**
	 * display stock Manager form wrapper function for Shortcode rendering
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts ( default: array() )
	 * @return string
	 */
	function display_stock_manager_form( $attr ) {
        ob_start();        
        $product_id = isset( $attr['product_id'] ) ? (int)$attr['product_id'] : 0;

        do_action( 'woocommerce_stock_manager_form_before' );

        SM()->frontend->display_product_subscription_form($product_id);

        do_action( 'woocommerce_stock_manager_form_after' );

        return ob_get_clean();
    }

    /**
     * Conditionally remove actions if the shortcode is present.
     */
    public function conditionally_remove_actions() {
        global $post;

        if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'display_stock_manager_form' ) ) {
            // remove default stock manager position
            remove_action( 'woocommerce_simple_add_to_cart',    [ SM()->frontend, 'display_product_subscription_form' ], 31 );
            remove_action( 'woocommerce_bundle_add_to_cart',    [ SM()->frontend, 'display_product_subscription_form' ], 31 );
            remove_action( 'woocommerce_woosb_add_to_cart',     [ SM()->frontend, 'display_product_subscription_form' ], 31 );
            remove_action( 'woocommerce_after_variations_form', [ SM()->frontend, 'display_product_subscription_form' ], 31 );        
            
            // remove_action( 'woocommerce_grouped_add_to_cart',   [ SM()->frontend, 'display_in_simple_product' ], 32 );
            // remove_filter( 'woocommerce_available_variation',   [ SM()->frontend, 'display_in_variation' ], 10 );
            // remove_filter( 'woocommerce_variation_is_active',   [ SM()->frontend, 'enable_disabled_variation_dropdown' ], 100, );
        }
    }
} 