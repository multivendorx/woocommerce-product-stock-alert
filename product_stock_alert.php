<?php
/**
 * Plugin Name: Product Stock Manager & Notifier for WooCommerce
 * Plugin URI: https://multivendorx.com/
 * Description: Boost sales with real-time stock alerts! Notify customers instantly when products are back in stock. Simplify data management by exporting and importing stock data with ease.
 * Author: MultiVendorX
 * Version: 2.4.2
 * Requires at least: 5.4
 * Tested up to: 6.4.2
 * WC requires at least: 8.2.2
 * WC tested up to: 8.5.1
 * Author URI: https://multivendorx.com/
 * Text Domain: woocommerce-stock-manager
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once __DIR__ . '/vendor/autoload.php';

function SM() {
    return \StockManager\StockManager::init( __FILE__ );
}

SM();
