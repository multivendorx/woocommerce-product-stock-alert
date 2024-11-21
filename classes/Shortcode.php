<?php
namespace StockManager;

defined( 'ABSPATH' ) || exit;

class Shortcode {

    public function __construct() {
        // Product Stock Manager Form Shortcode.
        add_shortcode( 'display_stock_manager_form', [ $this, 'display_stock_manager_form' ] );
        add_shortcode( 'display_stock_alert_form', [ $this, 'display_stock_manager_form' ] );
    }

    /**
     * display stock Manager form wrapper function for Shortcode rendering
     *
     * @access public
     * @param mixed $function
     * @param array $atts ( default: array() )
     * @return string
     */
    public function display_stock_manager_form( $attr ) {
        ob_start();     

        // Product ID from shortcode attributes
        $product_id = isset( $attr['product_id'] ) ? (int)$attr['product_id'] : 0;

        do_action( 'woocommerce_stock_manager_form_before' );

        // Display the product subscription form
        SM()->frontend->display_product_subscription_form($product_id);

        do_action( 'woocommerce_stock_manager_form_after' );

        return ob_get_clean();
    }
}
