<?php

namespace StockManager;

class StockManager {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $frontend;
    public $ajax;
    public $restapi;
    public $template;
    public $admin;
    public $action;
    public $shortcode;
    private $file;
    public $deprecated_hook_handlers = [];

    public function __construct($file) {
        require_once trailingslashit(dirname($file)) . '/config.php';

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WOO_STOCK_MANAGER_PLUGIN_TOKEN;
        $this->version = WOO_STOCK_MANAGER_PLUGIN_VERSION;

        add_filter('plugin_action_links_' . plugin_basename( $file ), [ \StockManager\Utill::class, 'stock_manager_settings']);
        if ( \StockManager\Dependencies::woocommerce_plugin_active_check() ) {
            add_action('init', [&$this, 'init'], 0);
            add_filter('woocommerce_email_classes', [&$this, 'setup_email_class']);
            // Activation Hooks
            register_activation_hook( $file, [$this, 'activate_stock_manager']);
            // Deactivation Hooks
            register_deactivation_hook( $file, [$this, 'deactivate_stock_manager']);
        } else {
            add_action( 'admin_notices', [ \StockManager\Utill::class, 'woocommerce_inactive_notice' ] );
        }
    }
    
    /**
     * initilize plugin on WP init
     */
    function init() {
        // Init Text Domain
        $this->load_plugin_textdomain();

        new \StockManager\Subscriber();

        $this->restapi = new \StockManager\RestAPI();

        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->ajax = new \StockManager\Ajax();
        }
        
        if (is_admin()) {
            $this->admin = new \StockManager\Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->frontend = new \StockManager\Frontend();
            $this->shortcode = new \StockManager\Shortcode();
        }

        $this->deprecated_hook_handlers['filters'] = new \StockManager\Deprecated\DeprecatedFilterHooks();
        $this->deprecated_hook_handlers['actions'] = new \StockManager\Deprecated\DeprecatedActionHooks();

        register_post_status('woo_mailsent', array(
            'label' => _x('Mail Sent', 'woostockalert', 'woocommerce-stock-manager'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop('Mail Sent <span class="count">(%s)</span>', 'Mail Sent <span class="count">(%s)</span>', 'woocommerce-stock-manager'),
        ));

        register_post_status('woo_subscribed', array(
            'label' => _x('Subscribed', 'woostockalert', 'woocommerce-stock-manager'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true, /* translators: %s: count */
            'label_count' => _n_noop('Subscribed <span class="count">(%s)</span>', 'Subscribed <span class="count">(%s)</span>'),
        ));

        register_post_status('woo_unsubscribed', array(
            'label' => _x('Unsubscribed', 'woostockalert', 'woocommerce-stock-manager'),
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
        $locale = apply_filters('plugin_locale', $locale, 'woocommerce-stock-manager');
        load_textdomain('woocommerce-stock-manager', WP_LANG_DIR . '/woocommerce-product-stock-alert/woocommerce-product-stock-alert-' . $locale . '.mo');
        load_plugin_textdomain('woocommerce-stock-manager', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
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
    public static function activate_stock_manager() {
        update_option('woo_stock_manager_installed', 1);
        new \StockManager\Install();
    }

    /**
     * Deactivation function on register deactivation hook
     */
    public static function deactivate_stock_manager() {
        if (get_option('woo_stock_manager_cron_start')) :
            wp_clear_scheduled_hook('woo_stock_manager_start_notification_cron_job');
            delete_option('woo_stock_manager_cron_start');
        endif;
        delete_option('woo_stock_manager_installed');
    }

    /**
     * Add Stock Alert Email Class
     * @return void
     */
    function setup_email_class($emails) {
        $emails['WC_Admin_Email_Stock_Manager'] = new \StockManager\Emails\AdminEmail();
        $emails['WC_Subscriber_Confirmation_Email_Stock_Manager'] = new \StockManager\Emails\SubscriberConfirmationEmail();
        $emails['WC_Email_Stock_Manager'] = new \StockManager\Emails\Emails();

        return $emails;
    }
}