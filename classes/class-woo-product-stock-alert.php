<?php

class WOO_Product_Stock_Alert {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $frontend;
    public $ajax;
    public $action;
    public $export;
    private $file;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WOO_PRODUCT_STOCK_ALERT_PLUGIN_TOKEN;
        $this->text_domain = WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN;
        $this->version = WOO_PRODUCT_STOCK_ALERT_PLUGIN_VERSION;

        add_action('init', array(&$this, 'init'), 0);

        // Woocommerce Email structure
        add_filter('woocommerce_email_classes', array(&$this, 'woo_product_stock_alert_mail'));
    }

    /**
     * initilize plugin on WP init
     */
    function init() {

        // Init Text Domain
        $this->load_plugin_textdomain();

        // Init library
        $this->load_class('library');
        $this->library = new WOO_Product_Stock_Alert_Library();

        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new WOO_Product_Stock_Alert_Ajax();
        }

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WOO_Product_Stock_Alert_Admin();

            $this->load_class('export');
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new WOO_Product_Stock_Alert_Frontend();

            $this->load_class('shortcode');
            $this->shortcode = new WOO_Product_Stock_Alert_Shortcode();
        }
        
        // Init action
        $this->load_class('action');
        $this->action = new WOO_Product_Stock_Alert_Action();

        // DC Wp Fields
        $this->dc_wp_fields = $this->library->load_wp_fields();
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

// End load_class()

    /** Cache Helpers ******************************************************** */

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

        update_option('dc_product_stock_alert_installed', 1);

        // Init install
        $WOO_Product_Stock_Alert->load_class('install');
        $WOO_Product_Stock_Alert->install = new WOO_Product_Stock_Alert_Install();
    }

    /**
     * Install upon deactivation
     *
     */
    public static function deactivate_product_stock_alert() {

        if (get_option('dc_product_stock_alert_cron_start')) :
            wp_clear_scheduled_hook('dc_start_stock_alert');
            delete_option('dc_product_stock_alert_cron_start');
        endif;
        delete_option('dc_product_stock_alert_installed');
    }

    /**
     * Add Stock Alert Email Class
     *
     */
    function woo_product_stock_alert_mail($emails) {
        require_once( 'emails/class-woo-product-stock-alert-admin-email.php' );
        $emails['WC_Admin_Email_Stock_Alert'] = new WC_Admin_Email_Stock_Alert();
        require_once( 'emails/class-woo-product-stock-alert-subscriber-confirmation-email.php' );
        $emails['WC_Subscriber_Confirmation_Email_Stock_Alert'] = new WC_Subscriber_Confirmation_Email_Stock_Alert();
        require_once( 'emails/class-woo-product-stock-alert-email.php' );
        $emails['WC_Email_Stock_Alert'] = new WC_Email_Stock_Alert();

        return $emails;
    }

}
