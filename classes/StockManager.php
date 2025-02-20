<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;
use \Automattic\WooCommerce\Utilities\FeaturesUtil;

class StockManager {

    private static $instance = null;
    private $container       = [];
    private $file;

    /**
     * Class construct
     * @param object $file
     */
    public function __construct( $file ) {
        require_once trailingslashit( dirname( $file ) ) . '/config.php';

        $this->file = $file;
        $this->container[ 'plugin_url' ]     = trailingslashit( plugins_url( '', $plugin = $file ) );
        $this->container[ 'plugin_path' ]    = trailingslashit( dirname( $file ) );
        $this->container[ 'plugin_base' ]    = plugin_basename( $file );

        $this->container[ 'version' ]        = STOCK_MANAGER_PLUGIN_VERSION;
        $this->container[ 'rest_namespace' ] = STOCK_MANAGER_REST_NAMESPACE;
        $this->container[ 'text_domain' ]    = STOCK_MANAGER_TEXT_DOMAIN;
        
        add_action( 'init', [ $this, 'set_default_value' ] );
        // Activation Hooks
        register_activation_hook( $file, [ $this, 'activate' ] );
        // Deactivation Hooks
        register_deactivation_hook( $file, [ $this, 'deactivate' ] );

        add_filter( 'plugin_action_links_' . plugin_basename( $file ), [ &$this, 'stock_manager_settings' ] );
        add_action( 'admin_notices', [ &$this, 'database_migration_notice' ] );
        add_filter( 'woocommerce_email_classes', [ &$this, 'setup_email_class' ] );

        add_action( 'before_woocommerce_init', [ $this, 'declare_compatibility' ] );
        add_action( 'woocommerce_loaded', [ $this, 'init_plugin' ] );
        add_action( 'plugins_loaded', [ $this, 'is_woocommerce_loaded' ] );
        add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
    }

    public function set_default_value() {
        $default_value = [
            'double_opt_in_success' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ), 
            'shown_interest_text' => __( 'Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager' ),
            'email_placeholder_text' => __( 'Enter your email', 'woocommerce-stock-manager' ),
            'alert_text' => __( 'Receive in-stock notifications for this.', 'woocommerce-stock-manager' ),
            'unsubscribe_button_text' => __( 'Unsubscribe', 'woocommerce-stock-manager' ),
            'alert_text_color' => '',
            'customize_btn' => [
                'button_text' => __( 'Notify me', 'woocommerce-stock-manager' ), 
                'button_background_color' => '', 
                'button_border_color' => '', 
                'button_text_color' => '', 
                'button_background_color_onhover' => '', 
                'button_text_color_onhover' => '', 
                'button_border_color_onhover' => '', 
                'button_font_size' => '', 
                'button_border_radious' => '', 
                'button_border_size' => ''
            ],
            'alert_success'  => __( 'Thank you for expressing interest in %product_title%. We will notify you via email once it is back in stock.', 'woocommerce-stock-manager' ), 
            // Translators: This message display already registered user to display already registered message
            'alert_email_exist' => __( '%customer_email% is already registered for %product_title%. Please attempt a different email address.', 'woocommerce-stock-manager' ), 
            'valid_email' => __( 'Please enter a valid email ID and try again.', 'woocommerce-stock-manager' ), 
            // Translators: This message display user sucessfully unregistered
            'alert_unsubscribe_message' => __( '%customer_email% is successfully unsubscribed.', 'woocommerce-stock-manager' ),
            'ban_email_domain_text' => __( 'This email domain is ban in our site, kindly use another email domain.', 'woocommerce-stock-manager' ), 
            'ban_email_address_text' => __( 'This email address is ban in our site, kindly use another email address.', 'woocommerce-stock-manager' )
        ];
        $this->container[ 'default_value' ]    = $default_value;
    }

    /**
     * Add Metadata in plugin row.
     * @param array $links 
     * @param string $file
     * @return array
     */
    public function plugin_row_meta( $links, $file ) {
        if ( SM()->plugin_base === $file ) {
            $row_meta = [
            	'docs'    => '<a href="' . esc_url( STOCK_MANAGER_DOC_URL ) . '" aria-label="' . esc_attr__( 'View WooCommerce documentation', 'woocommerce-stock-manager' ) . '" target="_blank">' . esc_html__( 'Docs', 'woocommerce-stock-manager' ) . '</a>',
            	'support' => '<a href="' . esc_url( STOCK_MANAGER_SUPPORT_URL ) . '" aria-label="' . esc_attr__( 'Visit community forums', 'woocommerce-stock-manager' ) . '" target="_blank">' . esc_html__( 'Support', 'woocommerce-stock-manager' ) . '</a>',
            ];

            return array_merge( $links, $row_meta );
        }

        return $links;
	}

    /**
     * Placeholder for activation function.
     * @return void
     */
    public function activate() {
        update_option( 'stock_manager_installed', 1 );
        $this->set_default_value();
        $this->container[ 'install' ] = new Install();
    }

    /**
     * Placeholder for deactivation function.
     * @return void
     */
    public  function deactivate() {
        if ( get_option( 'stock_manager_cron_start' ) ) {
            wp_clear_scheduled_hook( 'stock_manager_start_notification_cron_job' );
            delete_option( 'stock_manager_cron_start' );
        }

        delete_option( 'stock_manager_installed' );
    }

    /**
     * Add High Performance Order Storage Support
     * @return void
     */
    public function declare_compatibility() {
        FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( $this->file ), true );
    } 

    /**
     * Initilizing plugin on WP init
     * @return void
     */
    public function init_plugin( $file ) {
        $this->load_plugin_textdomain();
        $this->init_classes();
        
        do_action( 'stock_manager_loaded' );
    }
    
    /**
     * Init all Stock Manageer classess.
     * Access this classes using magic method.
     * @return void
     */
    public function init_classes() {
        $this->container[ 'util' ]        = new Utill();
        $this->container[ 'setting' ]     = new Setting();
        $this->container[ 'ajax' ]        = new Ajax();
        $this->container[ 'frontend' ]    = new FrontEnd();
        $this->container[ 'shortcode' ]   = new Shortcode();
        $this->container[ 'subscriber' ]  = new Subscriber();
        $this->container[ 'filters' ]     = new Deprecated\DeprecatedFilterHooks();
        $this->container[ 'actions' ]     = new Deprecated\DeprecatedActionHooks();
        $this->container[ 'admin' ]       = new Admin();
        $this->container[ 'restapi' ]     = new RestAPI();
        $this->container[ 'block' ]       = new Block();
    } 

    /**
     * Add Stock Alert Email Class
     * @return void
     */
    function setup_email_class( $emails ) {
        $emails[ 'WC_Admin_Email_Stock_Manager' ] = new Emails\AdminEmail();
        $emails[ 'WC_Subscriber_Confirmation_Email_Stock_Manager' ] = new Emails\SubscriberConfirmationEmail();
        $emails[ 'WC_Email_Stock_Manager' ] = new Emails\Emails();
        return $emails;
    } 
    
    /**
     * Take action based on if woocommerce is not loaded
     * @return void
     */
    public function is_woocommerce_loaded() {
        if ( did_action( 'woocommerce_loaded' ) || ! is_admin() ) {
            return;
        } 
        add_action( 'admin_notices', [ $this, 'woocommerce_admin_notice' ] );
    }

    /**
     * Load Localisation files.
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain( 'woocommerce-stock-manager', false, plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
        } else {
            load_textdomain( 'woocommerce-stock-manager', WP_LANG_DIR . '/woocommerce-product-stock-alert/woocommerce-product-stock-alert-' . determine_locale() . '.mo' );
        }
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
        return new \WP_Error( sprintf( 'Call to unknown class %s.', $class ) );
    }

    /**
     * Admin notice for woocommerce inactiove
     * @return void
     */
    public static function woocommerce_admin_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sProduct Stock Manager & Notifier for WooCommerce is inactive.%s The %sWooCommerce plugin%s must be active for the Product Stock Manager & Notifier for WooCommerce to work. Please %sinstall & activate WooCommerce%s', 'woocommerce-stock-manager'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', ' &raquo;</a>'); ?></p>
        </div>
        <?php
    }

    /**
     * Html for database migration notice.
     * @return void
     */
    public static function database_migration_notice() {
        // check if plugin vertion in databse is not same to current stock manager version
        $plugin_version = get_option( 'woo_stock_manager_version', '' );

        if ( Install::is_migration_running() ) {
            ?>
            <div id="message" class="notice notice-warning">
                <p><?php _e( "Product Stock Manager is currently updating the database in the background. Please be patient while the process completes.", 'woocommerce-stock-manager' ) ?></p>
            </div>
            <?php
        } else if ( $plugin_version != STOCK_MANAGER_PLUGIN_VERSION ) {
            ?>
            <div id="message" class="error">
                <p><?php _e( "The Product Stock Manager & Notifier for WooCommerce is experiencing configuration issues. To ensure proper functioning, kindly deactivate and then activate the plugin.", 'woocommerce-stock-manager' ) ?></p>
            </div>
            <?php
        }
    }

    /**
     * Set the stoct Manager settings in plugin activation page.
     * @param mixed $links
     * @return array
     */
    public static function stock_manager_settings( $links ) {
        $plugin_links = [ 
            '<a href="' . admin_url( 'admin.php?page=stock-manager#&tab=settings&subtab=general' ) . '">' . __( 'Settings', 'woocommerce-stock-manager' ) . '</a>', 
            '<a href="https://multivendorx.com/support-forum/forum/product-stock-manager-notifier-for-woocommerce/?utm_source=wpadmin&utm_medium=pluginsettings&utm_campaign=stockmanager" target="_blank">' . __( 'Support', 'woocommerce-stock-manager' ) . '</a>', 
            '<a href="https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=wpadmin&utm_medium=pluginsettings&utm_campaign=stockmanager" target="_blank">' . __( 'Docs', 'woocommerce-stock-manager' ) . '</a>'
        ];
        if ( apply_filters( 'is_stock_manager_pro_inactive', true ) ) {
            $links[ 'go_pro' ] = '<a href="' . STOCK_MANAGER_PRO_SHOP_URL . '" class="stock-manager-pro-plugin" target="_blank">' . __( 'Get Pro', 'woocommerce-stock-manager' ) . '</a>';
        }
        return array_merge( $plugin_links, $links );
    }

    /**
     * Initializes the MultiVendorX class.
     * Checks for an existing instance
     * And if it doesn't find one, create it.
     * @param mixed $file
     * @return object | null
     */
    public static function init( $file ) {
        if ( self::$instance === null ) {
            self::$instance = new self( $file );
        } 
        return self::$instance;
    }
    
}     