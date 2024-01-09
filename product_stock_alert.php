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
 * Text Domain: woocommerce-stock-manager
 * Domain Path: /languages/
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once 'config.php';
require_once 'classes/StockManager.php';

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

global $Woo_Stock_Manager;
$Woo_Stock_Manager = new Woo_Stock_Manager( __FILE__ );
