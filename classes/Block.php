<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Block {
    public function __construct() {
        // Register the block
        add_action( 'init', [$this, 'register_block'] );
        // Enqueue the script and style for block editor
        add_action( 'enqueue_block_editor_assets', [ $this,'enqueue_block_assets'] );
    }

    public function enqueue_block_assets() {
        wp_enqueue_script(
            'stock_manager_form',
            SM()->plugin_url . 'build/block/stock-manager-form/index.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            SM()->version,
            true
        );

        wp_localize_script( 'stock_manager_form', 'stockManagerForm', [ 
            'apiUrl'  => untrailingslashit( get_rest_url() ), 
            'nonce'   => wp_create_nonce( 'stock-manager-security-nonce' ),
        ]);
        
    }
    
    public function register_block() {
        // Register the block type with the render callback
        register_block_type('woocommerce-stock-manager/stock-manager-form', [
            'render_callback' => [$this, 'render_stock_manager_form_block'],
            'script'          => 'stock_manager_frontend_js'
        ]);
    }

    public function render_stock_manager_form_block($attributes) {
        ob_start();
        // Extract the productId from attributes
        $product_id = isset($attributes['productId']) ? intval($attributes['productId']) : null;

        // Display the product subscription form
        SM()->frontend->display_product_subscription_form($product_id, true);
    
        return ob_get_clean();
    }
    
}