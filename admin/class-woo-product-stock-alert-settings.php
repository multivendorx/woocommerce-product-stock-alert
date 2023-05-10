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
    add_menu_page(
      __( 'Stock Alert', 'woocommerce-product-stock-alert' ),
      __( 'Stock Alert', 'woocommerce-product-stock-alert' ),
      'manage_options',
      'woo-stock-alert-setting',
      [ $this, 'create_woo_product_stock_alert_settings' ],
      'dashicons-clipboard',
       50
    );

    add_submenu_page(
      'woo-stock-alert-setting',                              // parent slug
      __( 'Settings', 'woocommerce-product-stock-alert' ),    // page title
      __( 'Settings', 'woocommerce-product-stock-alert' ),    // menu title
      'manage_options',                                       // capability
      'woo-stock-alert-setting#&tab=settings&subtab=general', // callback
      '__return_null'
    );
    remove_submenu_page( 'woo-stock-alert-setting', 'woo-stock-alert-setting' );
  }
  
  /**
   * Options page callback
   */
  public function create_woo_product_stock_alert_settings() {
    echo '<div id="mvx-admin-stockalert"></div>';
  }
}