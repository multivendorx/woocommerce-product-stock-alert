<?php

namespace StockManager;

if ( !defined( 'ABSPATH' ) ) exit;

class Shortcode {

	public function __construct( ) {
		// Product Stock Manager Form Shortcode.
		add_shortcode( 'display_stock_manager_form', [ $this, 'display_stock_manager_form' ] );
        add_shortcode( 'display_stock_alert_form', [ $this, 'display_stock_manager_form' ] );
	} 

    /**
	 * display stock Manager form wrapper function for Shortcode rendering
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts ( default: array( ) )
	 * @return string
	 */
	function display_stock_manager_form( $attr ) {
        ob_start( );
        call_user_func( [ $this, 'output' ] );
        return ob_get_clean( );
    } 

	/**
     * Display Stock Manager Form
     *
     * @access public
     * @param array $atts
     * @return void
     */
	public function output( ) {
        SM( ) -> nocache( );

        do_action( 'woocommerce_stock_manager_form_before' );

        SM( ) -> frontend -> display_product_subscription_form( );

        do_action( 'woocommerce_stock_manager_form_after' );

        // remove default stock manager position
        remove_action( 'woocommerce_simple_add_to_cart',    [ SM( ) -> frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_bundle_add_to_cart',    [ SM( ) -> frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_woosb_add_to_cart',     [ SM( ) -> frontend, 'display_in_simple_product' ], 31 );
        remove_action( 'woocommerce_after_variations_form', [ SM( ) -> frontend, 'display_in_no_variation_product' ] );
        remove_action( 'woocommerce_grouped_add_to_cart',   [ SM( ) -> frontend, 'display_in_simple_product' ], 32 );
        remove_filter( 'woocommerce_available_variation',   [ SM( ) -> frontend, 'display_in_variation' ], 10 );
        remove_filter( 'woocommerce_variation_is_active',   [ SM( ) -> frontend, 'enable_disabled_variation_dropdown' ], 100, );
    } 
} 