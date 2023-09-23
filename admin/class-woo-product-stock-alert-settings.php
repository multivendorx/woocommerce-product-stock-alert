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
      'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g fill="#9EA3A8" fill-rule="nonzero">
      <path d="M19.9,5.7c0.2,0.9-0.3,1.8-1.1,2c-0.2,0.1-0.5,0.1-0.7,0c-0.6-0.1-1.1-0.5-1.3-1.2    c-0.2-0.6,0-1.2,0.4-1.6c0.2-0.2,0.4-0.3,0.7-0.4C18.8,4.3,19.7,4.8,19.9,5.7z M17.8,8.9l-3.2,9.9c-0.2,0.5-0.7,0.7-1.2,0.6
          L0.6,15.2C0.1,15-0.1,14.5,0,14L4.3,1.2c0.2-0.5,0.7-0.7,1.2-0.6L16,4.1c-0.5,0.7-0.7,1.7-0.5,2.6C15.8,7.9,16.7,8.7,17.8,8.9z
           M10.8,4.9c0.5,0.2,1,0.5,1.5,0.7c0.2-0.4,0-0.9-0.4-1.1C11.4,4.4,11,4.5,10.8,4.9z M9.5,15.2c-0.9-0.1-1.7-0.2-2.6-0.2
          c0.1,0.7,0.6,1.2,1.2,1.2C8.7,16.2,9.3,15.8,9.5,15.2z M12.7,9c0-1.7-1.4-3.1-3.1-3.2c-1.2,0-2.2,0.5-2.8,1.5
          c-0.6,0.9-1.1,1.8-1.7,2.7c-0.1,0.1-0.2,0.2-0.3,0.1c-0.5-0.2-0.8,0-1.1,0.6c-0.2,0.4,0,0.8,0.4,1c0.7,0.4,1.4,0.7,2.2,1.1
          c1.4,0.7,2.8,1.4,4.2,2.1c0.4,0.2,0.8,0.1,1.1-0.4c0-0.1,0.1-0.1,0.1-0.2c0.1-0.3,0-0.7-0.3-0.9c-0.2-0.1-0.2-0.2-0.1-0.4
          c0.4-1,0.8-2,1.1-3C12.7,9.7,12.7,9,12.7,9z"/></g></svg>'), 
        50
    );

    add_submenu_page(
      'woo-stock-alert-setting',                              // parent slug
      __( 'Settings', 'woocommerce-product-stock-alert' ),    // page title
      __( 'Settings', 'woocommerce-product-stock-alert' ),    // menu title
      'manage_options',                                       // capability
      'woo-stock-alert-setting#&tab=settings&subtab=general', // callback
      '__return_null'                                         // position
    );

    add_submenu_page( 
      'woo-stock-alert-setting', 
      __( 'Subscriber List', 'woocommerce-catalog-enquiry' ), 
      __( 'Subscriber List', 'woocommerce-catalog-enquiry' ), 
      'manage_woocommerce', 
      'woo-stock-alert-setting#&tab=subscriber-list', 
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