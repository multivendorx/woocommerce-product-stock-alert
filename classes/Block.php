<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Block {
    private $blocks;

    public function __construct() {
        // Register the block
        add_action( 'init', [$this, 'register_blocks'] );
        // Enqueue the script and style for block editor
        add_action( 'enqueue_block_editor_assets', [ $this,'enqueue_block_assets'] );


        $this->blocks = [
            [
                'name' => 'stock-notification-block', // block name
                'render_php_callback_function' => [$this, 'render_stock_notification_form_block'], // php render calback function
                'required_script' => 'stock_manager_frontend_js', // the script which is required in the frontend of the block
                'required_style'   => '', // the style which is required in the frontend of the block
                // src link is generated (which is append from block name) within the function
				'react_dependencies'   => ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'], // the react dependencies which required in js
                'localize' => [
					'object_name' => 'stockNotificationBlock', // the localized variable name
                    // all the data that is required in index.js
					'data' => [
                        'apiUrl'  => '', // this set blank because in scope the get_rest_url() is not defined
                        'restUrl' => SM()->rest_namespace,
                        'nonce'   => wp_create_nonce( 'stock-manager-security-nonce' )
					],
				],
            ]
        ];
    }

    public function enqueue_block_assets() {

        foreach ($this->blocks as $block_script) {
			wp_enqueue_script($block_script['name'], SM()->plugin_url . 'build/block/' . $block_script['name'] . '/index.js', $block_script['react_dependencies'], SM()->version, true);
			if (isset($block_script['localize'])) {
                $block_script['localize']['data']['apiUrl'] = untrailingslashit( get_rest_url() );
				wp_localize_script($block_script['name'], $block_script['localize']['object_name'], $block_script['localize']['data']);
			}
		}
    }
    
    public function register_blocks() {
    
        foreach ($this->blocks as $block) {
            register_block_type(SM()->text_domain . '/' . $block['name'], [
                'render_callback' => $block['render_php_callback_function'],
                'script'          => $block['required_script'],
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