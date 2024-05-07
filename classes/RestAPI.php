<?php

namespace StockManager;

defined( 'ABSPATH' ) || exit;

class RestAPI
 {
    function __construct() {
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ $this, 'register_restAPI' ] );
        }
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function register_restAPI() {
        register_rest_route( SM()->rest_namespace, '/save-stockmanager', [
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => [ $this, 'save_stockmanager_setting' ],
            'permission_callback' => [ $this, 'stockmanager_permission' ],
        ] );
    }

    /**
     * StockManager api permission function.
     * @return bool
     */
    public function stockmanager_permission() {
        // return current_user_can( 'manage_options' );
        return true;
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return array
     */
    public function save_stockmanager_setting( $request ) {
        $all_details = [];
        $get_settings_data = $request->get_param( 'setting' );
        $settingsname = $request->get_param( 'settingName' );
        $settingsname = str_replace( "-", "_", $settingsname );
        $optionname = 'woo_stock_manager_' . $settingsname . '_tab_settings';

        // save the settings in database
        SM()->setting->update_option( $optionname, $get_settings_data );

        do_action( 'stock_manager_settings_after_save', $settingsname, $get_settings_data );

        $all_details[ 'error' ] = __( 'Settings Saved', 'woocommerce-stock-manager' );

        return $all_details;
    }
}