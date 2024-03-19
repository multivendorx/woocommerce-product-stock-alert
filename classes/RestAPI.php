<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class RestAPI
 {
    function __construct( ) {
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ $this, 'register_restAPI' ] );
        }
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function register_restAPI( ) {
        register_rest_route( SM( ) -> rest_namespace, '/save-stockmanager', [
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => [ $this, 'save_stockmanager_setting' ],
            'permission_callback' => [ $this, 'stockmanager_permission' ],
        ] );
    }

    /**
     * StockManager api permission function.
     * @return bool
     */
    public function stockmanager_permission( ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return array
     */
    public function save_stockmanager_setting( $request ) {
        $all_details = [ ];
        $modulename = $request -> get_param( 'modulename' );
        $modulename = str_replace( "-", "_", $modulename );
        $optionname = 'woo_stock_manager_' . $modulename . '_tab_settings';
        $get_managements_data = $request -> get_param( 'model' );
        update_option( $optionname, $get_managements_data );
        do_action( 'woo_stock_manager_settings_after_save', $modulename, $get_managements_data );
        $all_details[ 'error' ] = __( 'Settings Saved', 'woocommerce-stock-manager' );
        return $all_details;
    }
} 