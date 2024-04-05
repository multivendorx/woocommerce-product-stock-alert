<?php

namespace StockManager\Deprecated;
defined( 'ABSPATH' ) || exit;
/**
* @deprecated 4.0.0
* 	wc_deprecated_function( new, version, old )
*/
function get_dc_plugin_settings() {
	wc_deprecated_function( 'get_dc_plugin_settings', '2.0.0', 'get_woo_product_alert_plugin_settings' );
	return get_woo_product_alert_plugin_settings();
} 
