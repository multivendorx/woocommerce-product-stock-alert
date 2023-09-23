<?php
/**
 * Plugin Name: WooCommerce Product Stock Alert
 * Plugin URI: https://multivendorx.com/
 * Description: Woocommerce plugin using which a customer can subscribe for interest on an out of stock product. When the product becomes available, subscribed customer will get an alert email.
 * Author: MultiVendorX
 * Version: 2.1.0
 * Requires at least: 4.4
 * Tested up to: 6.3.1
 * WC requires at least: 3.0
 * WC tested up to: 8.1.1
 * Author URI: https://multivendorx.com/
 * Text Domain: woocommerce-product-stock-alert
 * Domain Path: /languages/
 */

if ( ! class_exists( 'WC_Dependencies_Stock_Alert' ) )
	require_once 'includes/class-woo-stock-alert-dependencies.php';
require_once 'includes/woo-product-stock-alert-core-functions.php';
require_once 'product_stock_alert_config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WOO_PRODUCT_STOCK_ALERT_PLUGIN_TOKEN')) exit;
if(!defined('WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN')) exit;

if(!WC_Dependencies_Stock_Alert::woocommerce_plugin_active_check()) {
  add_action( 'admin_notices', 'woocommerce_inactive_notice' );
}

/**
 * Declare support for 'High-Performance order storage (COT)' in WooCommerce
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action(
	'before_woocommerce_init',
		function () {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
			}
		}
	);
}



add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woo_product_stock_alert_settings' );
function woo_product_stock_alert_settings( $links ) {
	$plugin_links = array('<a href="' . admin_url( 'admin.php?page=woo-stock-alert-setting#&tab=settings&subtab=general' ) . '">' . __( 'Settings', WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN ) . '</a>','<a href="https://multivendorx.com/support-forum/woocommerce-product-stock-alert">' . __( 'Support', WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN ) . '</a>' ,'<a href="https://multivendorx.com/docs/knowledgebase/woocommerce-product-stock-alert">' . __( 'Docs', WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN ) . '</a>');
	if (apply_filters('is_stock_alert_pro_inactive', true)) {
            $links['go_pro'] = '<a href="'.WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL.'" class="stock-alert-pro-plugin">' . __('Get Stock Alert Pro', WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN) . '</a>';
        }
	return array_merge( $plugin_links, $links );
}

if(!class_exists('WOO_Product_Stock_Alert') && WC_Dependencies_Stock_Alert::woocommerce_plugin_active_check()) {
	require_once( 'classes/class-woo-product-stock-alert.php' );
	global $WOO_Product_Stock_Alert;
	$WOO_Product_Stock_Alert = new WOO_Product_Stock_Alert( __FILE__ );
	$GLOBALS['WOO_Product_Stock_Alert'] = $WOO_Product_Stock_Alert;
	require_once( 'classes/class-woo-product-stock-alert-action.php' );
	// Activation Hooks
	register_activation_hook( __FILE__, array( 'WOO_Product_Stock_Alert', 'activate_product_stock_alert' ) );
	// Deactivation Hooks
	register_deactivation_hook( __FILE__, array( 'WOO_Product_Stock_Alert', 'deactivate_product_stock_alert' ) );
}