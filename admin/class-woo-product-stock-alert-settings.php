<?php
class WOO_Product_Stock_Alert_Settings {
  
  /**
   * Start up
   */
  public function __construct() {
    // Admin menu
    add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
  }
  
  /**
   * Add options page
   */
  public function add_settings_page() {
    add_submenu_page( 'woocommerce', __( 'WC Stock Alert', 'woocommerce-product-stock-alert' ), __( 'WC Stock Alert', 'woocommerce-product-stock-alert' ), 'manage_options', 'woo-product-stock-alert-setting-admin', [ $this, 'create_woo_product_stock_alert_settings' ] );
  }
  
  /**
   * Options page callback
   */
  public function create_woo_product_stock_alert_settings() {
    echo '<div id="mvx-admin-stockalert"></div>';
  }
}