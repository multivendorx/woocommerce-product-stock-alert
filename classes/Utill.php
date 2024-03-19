<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Utill {
    /**
     * Set the stoct Manager settings in plugin activation page.
     * @param mixed $links
     * @return array
     */
    public static function stock_manager_settings( $links ) {
        $plugin_links = [ 
            '<a href="' . admin_url( 'admin.php?page=woo-stock-manager-setting#&tab=settings&subtab=general' ) . '">' . __( 'Settings', 'woocommerce-stock-manager' ) . '</a>', 
            '<a href="https://multivendorx.com/support-forum/forum/product-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __( 'Support', 'woocommerce-stock-manager' ) . '</a>', 
            '<a href="https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __( 'Docs', 'woocommerce-stock-manager' ) . '</a>'
        ];
        if ( apply_filters( 'is_stock_manager_pro_inactive', true ) ) {
            $links[ 'go_pro' ] = '<a href="' . STOCK_MANAGER_PRO_SHOP_URL . '" class="stock-manager-pro-plugin" target="_blank">' . __( 'Get Pro', 'woocommerce-stock-manager' ) . '</a>';
        } 
        return array_merge( $plugin_links, $links );
    }

    /**
     * Html for database migration notice.
     * @return void
     */
    public static function database_migration_notice( ) {
        // check if plugin vertion in databse is not same to current stock manager version
        $plugin_version = get_option( 'woo_stock_manager_version', '' );
        if ( $plugin_version != STOCK_MANAGER_PLUGIN_VERSION ) {
            ?>
            <div id="message" class="error">
                <p><?php esc_html( "The Product Stock Manager & Notifier for WooCommerce is experiencing configuration issues. To ensure proper functioning, kindly deactivate and then activate the plugin." ) ?></p>
            </div>
            <?php
        } 
    }  
} 