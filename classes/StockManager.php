<?php

namespace StockManager;
use \Automattic\WooCommerce\Utilities\FeaturesUtil;

class StockManager {

    private static $instance = null;
    private $plugin_url  = '';
    private $plugin_path = '';
    private $container   = [];
    private $file;

    public function __construct($file) {
        require_once trailingslashit(dirname($file)) . '/config.php';

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));

        // Activation Hooks
        register_activation_hook( $file, [ $this, 'activate' ] );
        // Deactivation Hooks
        register_deactivation_hook( $file, [ $this, 'deactivate' ] );

        add_action( 'before_woocommerce_init', [ $this, 'declare_compatibility' ] );
        add_action( 'woocommerce_loaded', [ $this, 'init_plugin' ] );
        add_action( 'plugins_loaded', [ $this, 'is_woocommerce_loaded'] );

        add_filter('plugin_action_links_' . plugin_basename( $file ), [ Utill::class, 'stock_manager_settings']);
        if ( Dependencies::woocommerce_plugin_active_check() ) {
            add_action('init', [&$this, 'init'], 0);
            add_filter('woocommerce_email_classes', [&$this, 'setup_email_class']);

            // Add notice for database migration
            add_action( 'admin_notices', [ Utill::class, 'database_migration_notice' ] );
        } else {
            add_action( 'admin_notices', [ Utill::class, 'woocommerce_inactive_notice' ] );
        }
    }
    public function init_classes() {
        $this->container['restapi']     = new RestAPI();
        $this->container['ajax']        = new Ajax();
        $this->container['admin']       = new Admin();
        $this->container['frontend']    = new FrontEnd();
        $this->container['shortcode']   = new Shortcode();
    }
    


    /**
     * initilize plugin on WP init
     */
    public static function init_plugin($file) {

        
        // Init Text Domain
        // $this->load_plugin_textdomain();

        // new Subscriber();

        // $this->restapi = new RestAPI();

        // // Init ajax
        // if (defined('DOING_AJAX')) {
        //     $this->ajax = new Ajax();
        // }
        
        // if (is_admin()) {
        //     $this->admin = new Admin();
        // }

        // if (!is_admin() || defined('DOING_AJAX')) {
        //     $this->frontend = new FrontEnd();
        //     $this->shortcode = new Shortcode();
        // }

        // $this->deprecated_hook_handlers['filters'] = new Deprecated\DeprecatedFilterHooks();
        // $this->deprecated_hook_handlers['actions'] = new Deprecated\DeprecatedActionHooks();

        // register_post_status('woo_mailsent', array(
        //     'label' => _x('Mail Sent', 'woostockalert', 'woocommerce-stock-manager'),
        //     'public' => true,
        //     'exclude_from_search' => false,
        //     'show_in_admin_all_list' => true,
        //     'show_in_admin_status_list' => true, /* translators: %s: count */
        //     'label_count' => _n_noop('Mail Sent <span class="count">(%s)</span>', 'Mail Sent <span class="count">(%s)</span>', 'woocommerce-stock-manager'),
        // ));

        // register_post_status('woo_subscribed', array(
        //     'label' => _x('Subscribed', 'woostockalert', 'woocommerce-stock-manager'),
        //     'public' => true,
        //     'exclude_from_search' => false,
        //     'show_in_admin_all_list' => true,
        //     'show_in_admin_status_list' => true, /* translators: %s: count */
        //     'label_count' => _n_noop('Subscribed <span class="count">(%s)</span>', 'Subscribed <span class="count">(%s)</span>'),
        // ));

        // register_post_status('woo_unsubscribed', array(
        //     'label' => _x('Unsubscribed', 'woostockalert', 'woocommerce-stock-manager'),
        //     'public' => true,
        //     'exclude_from_search' => false,
        //     'show_in_admin_all_list' => true,
        //     'show_in_admin_status_list' => true, /* translators: %s: count */
        //     'label_count' => _n_noop('Unsubscribed <span class="count">(%s)</span>', 'Unsubscribed <span class="count">(%s)</span>'),
        // ));
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

    public function declare_compatibility() {
        FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename($this->$file), true );
    }

    // public function init_plugin(){
        //     $file = $this->file;
        //     global $SA;
        // }
        

    /**
     * Add Stock Alert Email Class
     * @return void
     */
    function setup_email_class($emails) {
        $emails['WC_Admin_Email_Stock_Manager'] = new Emails\AdminEmail();
        $emails['WC_Subscriber_Confirmation_Email_Stock_Manager'] = new Emails\SubscriberConfirmationEmail();
        $emails['WC_Email_Stock_Manager'] = new Emails\Emails();
        
        return $emails;
    }
    /**
     * Activation function on register activation hook
     */
    public function activate() {
        update_option('woo_stock_manager_installed', 1);
        $this->container['install'] = new Install();
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
     * Magic getter function to get the reference of class.
     * Accept class name, If valid return reference, else Wp_Error. 
     * @param   mixed $class
     * @return  object | \WP_Error
     */
    public function __get( $class ) {
        if ( array_key_exists( $class, $this->container ) ) {
            return $this->container[ $class ];
        }
        return new \WP_Error(sprintf('Call to unknown class %s.', $class));
    }

    /**
     * Initializes the MultiVendorX class.
     * Checks for an existing instance
     * And if it doesn't find one, create it.
     * @param mixed $file
     * @return object | null
     */
    // public static function init($file) {
    //     if ( self::$instance === null ) {
    //         self::$instance = new self($file);
    //     }
    //     return self::$instance;
    // }
}