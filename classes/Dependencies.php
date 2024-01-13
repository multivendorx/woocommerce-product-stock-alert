<?php

namespace StockManager;

/**
 * WC Dependency Checker
 *
 */
class Dependencies {
	private static $active_plugins;
	static function init() {
		self::$active_plugins = (array) get_option('active_plugins', array());
		if (is_multisite())
			self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
	}
	public static function woocommerce_plugin_active_check() {
		if (!self::$active_plugins) self::init();
		return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
	}
}