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
	require_once 'includes/class-woo-product-stock-alert-dependencies.php';
require_once 'includes/woo-product-stock-alert-core-functions.php';
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

function woo_product_stock_alert_settings($links) {
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=woo-stock-alert-setting#&tab=settings&subtab=general') . '">' . __('Settings', 'woocommerce-product-stock-alert') . '</a>',
        '<a href="https://multivendorx.com/support-forum/forum/product-stock-manager-notifier-for-woocommerce/?utm_source=wordpress.org&utm_medium=freelandingpage&utm_campaign=products-stock-manager" target="_blank">' . __('Support', 'woocommerce-product-stock-alert') . '</a>',
        '<a href="https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=wordpress.org&utm_medium=freelandingpage&utm_campaign=products-stock-manager" target="_blank">' . __('Docs', 'woocommerce-product-stock-alert') . '</a>'
    );
    if (apply_filters('is_stock_alert_pro_inactive', true)) {
    	$links['go_pro'] = '<a href="' . WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL . '" class="stock-alert-pro-plugin" target="_blank">' . __('Get Pro', 'woocommerce-product-stock-alert') . '</a>';
    }
    return array_merge($plugin_links, $links);
}

if (!class_exists('WOO_Product_Stock_Alert') && WC_Dependencies_Stock_Alert::woocommerce_plugin_active_check()) {
	require_once('classes/class-woo-product-stock-alert.php');
	global $WOO_Product_Stock_Alert;
	$WOO_Product_Stock_Alert = new WOO_Product_Stock_Alert( __FILE__ );
	// Activation Hooks
	register_activation_hook( __FILE__, array('WOO_Product_Stock_Alert', 'activate_product_stock_alert'));
	// Deactivation Hooks
	register_deactivation_hook( __FILE__, array('WOO_Product_Stock_Alert', 'deactivate_product_stock_alert'));
}
else{
	add_action( 'admin_notices', 'woocommerce_inactive_notice' );
}