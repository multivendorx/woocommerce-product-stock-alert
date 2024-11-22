<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Block {
    public function __construct() {
        // Register the block
        add_action( 'init', [$this, 'register_blocks'] );
        // Enqueue the script and style for block editor
        add_action( 'enqueue_block_editor_assets', [ $this,'enqueue_block_assets'] );
    }

    public function enqueue_block_assets() {
        wp_enqueue_script(
            'stock_notification_form',
            SM()->plugin_url . 'build/block/stock-notification-form/index.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
            SM()->version,
            true
        );

        wp_localize_script( 'stock_notification_form', 'stockNotificationForm', [ 
            'apiUrl'  => untrailingslashit( get_rest_url() ),
            'restUrl'                  => 'stockmanager/v1',
            'nonce'   => wp_create_nonce( 'stock-manager-security-nonce' ),
        ]);
        
    }
    
    public function register_blocks() {
        $blocks = [
            [
                'name' => 'woocommerce-stock-manager/stock-notification-block',
                'render_callback' => [$this, 'render_stock_notification_form_block'],
                'script' => 'stock_manager_frontend_js',
            ]
        ];
    
        foreach ($blocks as $block) {
            register_block_type($block['name'], [
                'render_callback' => $block['render_callback'],
                'script'          => $block['script'],
            ]);
        }
    }

    public function render_stock_notification_form_block($attributes) {
        ob_start();
        // Extract the productId from attributes
        $product_id = isset($attributes['productId']) ? intval($attributes['productId']) : null;

        // Display the product subscription form
        SM()->frontend->display_product_subscription_form($product_id, true);
    
        return ob_get_clean();
    }
    
}