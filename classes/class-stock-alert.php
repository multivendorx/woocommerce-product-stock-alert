<?php

class Woo_Product_Stock_Alert {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $subscriber;
    public $frontend;
    public $ajax;
    public $restapi;
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
        add_filter('woocommerce_email_classes', array(&$this, 'woo_product_stock_alert_mail'));
    }

    /**
     * initilize plugin on WP init
     */
    function init() {
        // Init Text Domain
        $this->load_plugin_textdomain();

        $this->load_class('subscriber');
        $this->subscriber = new Woo_Product_Stock_Alert_Subscriber();

        $this->load_class('restapi');
        $this->restapi = new Woo_Product_Stock_Alert_Restapi();

        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new Woo_Product_Stock_Alert_Ajax();
        }
        
        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new Woo_Product_Stock_Alert_Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new Woo_Product_Stock_Alert_Frontend();

            $this->load_class('shortcode');
            $this->shortcode = new Woo_Product_Stock_Alert_Shortcode();
        }

        include_once $this->plugin_path . '/includes/class-stock-alert-deprecated-filter-hooks.php';
        include_once $this->plugin_path . '/includes/class-stock-alert-deprecated-action-hooks.php';
        include_once $this->plugin_path . '/includes/stock-alert-deprecated-funtions.php';
        $this->deprecated_hook_handlers['filters'] = new Stock_Alert_Deprecated_Filter_Hooks();
        $this->deprecated_hook_handlers['actions'] = new Stock_Alert_Deprecated_Action_Hooks();

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

    /**
     * Load Localisation files.
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

    /**
     * Load the class present inside classes folder.
     * @param string $class_name Short class name.
     * @return void
     */
    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-stock-alert-'. esc_attr($class_name) . '.php');
        }
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
     * Activation function on register activation hook
     */
    public static function activate_product_stock_alert() {
        global $Woo_Product_Stock_Alert;
        update_option('woo_product_stock_alert_installed', 1);
        $Woo_Product_Stock_Alert->load_class('install');
        new Woo_Product_Stock_Alert_Install();
    }

    /**
     * Deactivation functio on register deactivation hook
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
     */
    function woo_product_stock_alert_mail($emails) {
        require_once('emails/class-stock-alert-admin-email.php');
        require_once('emails/class-stock-alert-subscriber-confirmation-email.php');
        require_once('emails/class-stock-alert-email.php');

        $emails['WC_Admin_Email_Stock_Alert'] = new WC_Admin_Email_Stock_Alert();
        $emails['WC_Subscriber_Confirmation_Email_Stock_Alert'] = new WC_Subscriber_Confirmation_Email_Stock_Alert();        
        $emails['WC_Email_Stock_Alert'] = new WC_Email_Stock_Alert();

        return $emails;
    }
}