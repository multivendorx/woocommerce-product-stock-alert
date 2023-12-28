<?php
if (!defined('ABSPATH')) exit;


class WOO_Product_Stock_Alert_Shortcode {

	public function __construct() {
		// Product Stock Alert Form Shortcode
		add_shortcode('display_stock_alert_form', array($this, 'display_stock_alert_form'));
	}

    /**
	 * display
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	function display_stock_alert_form($attr) {
        ob_start();
        call_user_func(['WOO_Product_Stock_Alert_Shortcode', 'output']);
        return ob_get_clean();
    }

	/**
     * Display Stock Alert Form
     *
     * @access public
     * @param array $atts
     * @return void
     */
	public static function output() {
        global $WOO_Product_Stock_Alert;
        $WOO_Product_Stock_Alert->nocache();

        do_action('woocommerce_product_stock_alert_form_before');

        global $product;

        if (empty($product))
            return;

        if ($product->is_type('variable')) {
            echo '<div class="stock_notifier-shortcode-subscribe-form" data-product-id="' . esc_attr($product->get_id()) . '"></div>';
        } else {
            echo $WOO_Product_Stock_Alert->frontend->get_subscribe_form($product);
        }

        do_action('woocommerce_product_stock_alert_form_after');

        // remove default stock alert position
        remove_action( 'woocommerce_simple_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_bundle_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_woosb_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 31 );
        remove_action( 'woocommerce_after_variations_form', array( $WOO_Product_Stock_Alert->frontend, 'display_in_no_variation_product' ) );
        remove_action( 'woocommerce_grouped_add_to_cart', array( $WOO_Product_Stock_Alert->frontend, 'display_in_simple_product' ), 32 );
        remove_filter( 'woocommerce_available_variation', array( $WOO_Product_Stock_Alert->frontend, 'display_in_variation' ), 10 );
        remove_filter( 'woocommerce_variation_is_active', array( $WOO_Product_Stock_Alert->frontend, 'enable_disabled_variation_dropdown' ), 100, );
    }
}