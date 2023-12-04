<?php

class WOO_Product_Stock_Alert {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $frontend;
    public $ajax;
    public $template;
    public $admin;
    public $action;
    public $shortcode;
    private $file;
    public $deprecated_hook_handlers = array();

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WOO_PRODUCT_STOCK_ALERT_PLUGIN_TOKEN;
        $this->version = WOO_PRODUCT_STOCK_ALERT_PLUGIN_VERSION;

        add_action('init', array(&$this, 'init'), 0);
        add_action('admin_init', array(&$this, 'woo_admin_init'));
        // Woocommerce Email structure
        add_filter('woocommerce_email_classes', array(&$this, 'woo_product_stock_alert_mail'));
        add_action('woo_stock_alert_start_notification_cron_job', 'woo_stock_alert_notify_subscribed_user');
    }

    /**
     * initilize plugin on WP init
     */
    function init() {
        // Init Text Domain
        $this->load_plugin_textdomain();
        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new WOO_Product_Stock_Alert_Ajax();
        }

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WOO_Product_Stock_Alert_Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new WOO_Product_Stock_Alert_Frontend();

            $this->load_class('shortcode');
            $this->shortcode = new WOO_Product_Stock_Alert_Shortcode();
        }
        $this->load_class('template');
        $this->template = new WOO_Product_Stock_Alert_Template();

        include_once $this->plugin_path . '/includes/class-woo-product-stock-alert-deprecated-filter-hooks.php';
        include_once $this->plugin_path . '/includes/class-woo-product-stock-alert-deprecated-action-hooks.php';
        include_once $this->plugin_path . '/includes/woo-product-stock-alert-deprecated-funtions.php';
        $this->deprecated_hook_handlers['filters'] = new Stock_Alert_Deprecated_Filter_Hooks();
        $this->deprecated_hook_handlers['actions'] = new Stock_Alert_Deprecated_Action_Hooks();

        if (current_user_can('manage_options')) {
            add_action('rest_api_init', array($this, 'stock_alert_rest_routes_react_module'));
        }

        register_post_status('woo_mailsent', array(
            'label' => _x('Mail Sent', 'woostockalert', 'woocommerce-product-stock-alert'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop('Mail Sent <span class="count">(%s)</span>', 'Mail Sent <span class="count">(%s)</span>', 'woocommerce-product-stock-alert'),
        ));

        register_post_status('woo_subscribed', array(
            'label' => _x('Subscribed', 'woostockalert', 'woocommerce-product-stock-alert'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop('Subscribed <span class="count">(%s)</span>', 'Subscribed <span class="count">(%s)</span>'),
        ));

        register_post_status('woo_unsubscribed', array(
            'label' => _x('Unsubscribed', 'woostockalert', 'woocommerce-product-stock-alert'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop('Unsubscribed <span class="count">(%s)</span>', 'Unsubscribed <span class="count">(%s)</span>'),
        ));
    }

    function woo_admin_init(){
        $previous_plugin_version = get_option("woo_product_stock_alert_version", "");
        $current_plugin_version = $this->version;
        woo_stock_alert_data_migrate($previous_plugin_version, $current_plugin_version);
    }


    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'woocommerce-product-stock-alert');
        load_textdomain('woocommerce-product-stock-alert', WP_LANG_DIR . '/woocommerce-product-stock-alert/woocommerce-product-stock-alert-' . $locale . '.mo');
        load_plugin_textdomain('woocommerce-product-stock-alert', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

    /****************************Cache Helpers ******************************/
    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

    /**
     * Install upon activation
     *
     */
    public static function activate_product_stock_alert() {
        global $WOO_Product_Stock_Alert;
        update_option('woo_product_stock_alert_installed', 1);
        // Init install
        $WOO_Product_Stock_Alert->load_class('install');
        $WOO_Product_Stock_Alert->install = new WOO_Product_Stock_Alert_Install();
    }

    /**
     * Install upon deactivation
     *
     */
    public static function deactivate_product_stock_alert() {
        if (get_option('woo_product_stock_alert_cron_start')) :
            wp_clear_scheduled_hook('woo_start_stock_alert');
            delete_option('woo_product_stock_alert_cron_start');
        endif;
        delete_option('woo_product_stock_alert_installed');
    }

    /**
     * Add Stock Alert Email Class
     *
     */
    function woo_product_stock_alert_mail($emails) {
        require_once('emails/class-woo-product-stock-alert-admin-email.php');
        require_once('emails/class-woo-product-stock-alert-subscriber-confirmation-email.php');
        require_once('emails/class-woo-product-stock-alert-email.php');

        $emails['WC_Admin_Email_Stock_Alert'] = new WC_Admin_Email_Stock_Alert();
        $emails['WC_Subscriber_Confirmation_Email_Stock_Alert'] = new WC_Subscriber_Confirmation_Email_Stock_Alert();        
        $emails['WC_Email_Stock_Alert'] = new WC_Email_Stock_Alert();

        return $emails;
    }

    public function stock_alert_rest_routes_react_module() {
        register_rest_route('woo_stockalert/v1', '/fetch_admin_tabs', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_fetch_admin_tabs'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/save_stockalert', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'woo_stockalert_save_stockalert'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/get_button_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_get_button_data'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/close_banner', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_close_banner'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
    }

    public function stockalert_permission() {
        return true;
    }

    public function woo_stockalert_close_banner() {
        update_option('woocommerce_stock_alert_pro_banner_hide', true);
        return rest_ensure_response(false);
    }
    
    public function woo_stockalert_fetch_admin_tabs() {
		$woo_stockalert_tabs_data = woo_stockalert_admin_tabs() ? woo_stockalert_admin_tabs() : [];
        return rest_ensure_response($woo_stockalert_tabs_data);
	}

    public function woo_stockalert_get_button_data() {
        $button_data = array(
            'alert_text_color' => get_woo_product_alert_plugin_settings('alert_text_color', ''),
            'button_background_color' => get_woo_product_alert_plugin_settings('button_background_color', ''),
            'button_border_color' => get_woo_product_alert_plugin_settings('button_border_color', ''),
            'button_text_color' => get_woo_product_alert_plugin_settings('button_text_color', ''),
            'button_background_color_onhover' => get_woo_product_alert_plugin_settings('button_background_color_onhover', ''),
            'button_text_color_onhover' => get_woo_product_alert_plugin_settings('button_text_color_onhover', ''),
            'button_border_color_onhover' => get_woo_product_alert_plugin_settings('button_border_color_onhover', ''),
            'button_font_size' => get_woo_product_alert_plugin_settings('button_font_size', ''),
            'button_border_radious' => get_woo_product_alert_plugin_settings('button_border_radious', ''),
            'button_border_size' => get_woo_product_alert_plugin_settings('button_border_size', ''),
        );
        return rest_ensure_response($button_data);
    }

    public function woo_stockalert_save_stockalert($request) {
        $all_details = [];
        $modulename = $request->get_param('modulename');
        $modulename = str_replace("-", "_", $modulename);
        $get_managements_data = $request->get_param( 'model' );
        $optionname = 'woo_stock_alert_'.$modulename.'_tab_settings';
        update_option($optionname, $get_managements_data);
        do_action('woo_stock_alert_settings_after_save', $modulename, $get_managements_data);
        $all_details['error'] = __('Settings Saved', 'woocommerce-product-stock-alert');
        return $all_details;
        die;
    }
}