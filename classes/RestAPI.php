<?php

namespace StockManager;

if (!defined('ABSPATH')) exit;

class RestAPI
{
    function __construct() {
        if (current_user_can('manage_options')) {
            add_action('rest_api_init', array($this, 'register_restAPI'));
        }
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function register_restAPI() {
        register_rest_route('woo-stockmanager/v1', '/fetch-admin-tabs', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array($this, 'fetch_admin_tabs'),
            'permission_callback' => array($this, 'stockmanager_permission'),
        ]);
        register_rest_route('woo-stockmanager/v1', '/save-stockmanager', [
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => array($this, 'save_stockmanager_setting'),
            'permission_callback' => array($this, 'stockmanager_permission'),
        ]);
        register_rest_route('woo-stockmanager/v1', '/close-banner', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array($this, 'close_banner'),
            'permission_callback' => array($this, 'stockmanager_permission'),
        ]);
    }

    /**
     * StockManager api permission function.
     * @return bool
     */
    public function stockmanager_permission() {
        return true;
    }

    /**
     * Set banner hide setting to true.
     * @return \WP_Error| \WP_REST_Response
     */
    public function close_banner() {
        update_option('woocommerce_stock_manager_pro_banner_hide', true);
        return rest_ensure_response(false);
    }

    public function fetch_admin_tabs() {
        $response = \StockManager\AdminSettingTab::get();
        foreach ($response as $tab_name => $tab_content) {
            $response[$tab_name]['databases_value'] = get_option('woo_stock_manager_' . $tab_name . '_tab_settings');
        }
        $response = wp_json_encode($response, JSON_PRETTY_PRINT);
        return rest_ensure_response($response);
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return array
     */
    public function save_stockmanager_setting($request) {
        $all_details = [];
        $modulename = $request->get_param('modulename');
        $modulename = str_replace("-", "_", $modulename);
        $optionname = 'woo_stock_manager_' . $modulename . '_tab_settings';
        $get_managements_data = $request->get_param('model');
        update_option($optionname, $get_managements_data);
        do_action('woo_stock_manager_settings_after_save', $modulename, $get_managements_data);
        $all_details['error'] = __('Settings Saved', 'woocommerce-stock-manager');
        return $all_details;
    }
}