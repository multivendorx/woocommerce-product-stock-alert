<?php
/**
 * Plugin Name: Product Stock Manager & Notifier for WooCommerce
 * Plugin URI: https://multivendorx.com/
 * Description: Boost sales with real-time stock alerts! Notify customers instantly when products are back in stock. Simplify data management by exporting and importing stock data with ease.
 * Author: MultiVendorX
 * Version: 2.3.0
 * Requires at least: 5.0
 * Tested up to: 6.4.1
 * WC requires at least: 7.2
 * WC tested up to: 8.3.1
 * Author URI: https://multivendorx.com/
 * Text Domain: woocommerce-product-stock-alert
 * Domain Path: /languages/
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WC_Dependencies_Stock_Alert'))
	require_once 'includes/class-stock-alert-dependencies.php';
require_once 'includes/stock-alert-core-functions.php';
require_once 'config.php';

/**
 * Declare support for 'High-Performance order storage (COT)' in WooCommerce
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option( 'active_plugins')))) {
    add_action(
	'before_woocommerce_init',
		function () {
			if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
			}
		}
	);
}

add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'woo_product_stock_alert_settings');

if (!class_exists('Woo_Product_Stock_Alert') && WC_Dependencies_Stock_Alert::woocommerce_plugin_active_check()) {
	require_once('classes/class-stock-alert.php');
	global $Woo_Product_Stock_Alert;
	$Woo_Product_Stock_Alert = new Woo_Product_Stock_Alert( __FILE__ );
	// Activation Hooks
	register_activation_hook( __FILE__, array('Woo_Product_Stock_Alert', 'activate_product_stock_alert'));
	// Deactivation Hooks
	register_deactivation_hook( __FILE__, array('Woo_Product_Stock_Alert', 'deactivate_product_stock_alert'));
} else {
	add_action( 'admin_notices', 'woocommerce_inactive_notice' );
}
