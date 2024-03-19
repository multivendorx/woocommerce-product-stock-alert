<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;
use \Automattic\WooCommerce\Utilities\FeaturesUtil;

class StockManager {

    private static $instance = null;
    private $container       = [ ];
    private $file;

    /**
     * Class construct
     * @param object $file
     */
    public function __construct( $file ) {
        require_once trailingslashit( dirname( $file ) ) . '/config.php';

        $this -> file = $file;
        $this -> container[ 'plugin_url' ] = trailingslashit( plugins_url( '', $plugin = $file ) );
        $this -> container[ 'plugin_path' ] = trailingslashit( dirname( $file ) );
        $this -> container[ 'version' ] = STOCK_MANAGER_PLUGIN_VERSION;
        $this -> container[ 'rest_namespace' ] = STOCK_MANAGER_REST_NAMESPACE;
        // Activation Hooks
        register_activation_hook( $file, [ $this, 'activate' ] );
        // Deactivation Hooks
        register_deactivation_hook( $file, [ $this, 'deactivate' ] );

        add_filter( 'plugin_action_links_' . plugin_basename( $file ), [ Utill::class, 'stock_manager_settings' ] );
        add_action( 'admin_notices', [ Utill::class, 'database_migration_notice' ] );
        add_filter( 'woocommerce_email_classes', [ &$this, 'setup_email_class' ] );

        add_action( 'before_woocommerce_init', [ $this, 'declare_compatibility' ] );
        add_action( 'woocommerce_loaded', [ $this, 'init_plugin' ] );
        add_action( 'plugins_loaded', [ $this, 'is_woocommerce_loaded' ] );
    } 

    /**
     * Placeholder for activation function.
     * @return void
     */
    public function activate( ) {
        update_option( 'woo_stock_manager_installed', 1 );
        $this -> container[ 'install' ] = new Install( );
    } 

    /**
     * Placeholder for deactivation function.
     * @return void
     */
    public  function deactivate( ) {
        if ( get_option( 'woo_stock_manager_cron_start' ) ) :
            wp_clear_scheduled_hook( 'woo_stock_manager_start_notification_cron_job' );
            delete_option( 'woo_stock_manager_cron_start' );
        endif;
        delete_option( 'woo_stock_manager_installed' );
    } 

    /**
     * Add High Performance Order Storage Support
     * @return void
     */
    public function declare_compatibility( $file ) {
        FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( $file ), true );
    } 

    /**
     * Initilizing plugin on WP init
     * @return void
     */
    public function init_plugin( $file ) {
        $this -> load_plugin_textdomain( );
        $this -> init_classes( );
        do_action( 'stock_manager_loaded' );
    }
    
    /**
     * Init all Stock Manageer classess.
     * Access this classes using magic method.
     * @return void
     */
    public function init_classes( ) {
        $this -> container[ 'util' ]        = new Utill( );
        $this -> container[ 'ajax' ]        = new Ajax( );
        $this -> container[ 'admin' ]       = new Admin( );
        $this -> container[ 'restapi' ]     = new RestAPI( );
        $this -> container[ 'frontend' ]    = new FrontEnd( );
        $this -> container[ 'shortcode' ]   = new Shortcode( );
        $this -> container[ 'subscriber' ]  = new Subscriber( );
        $this -> container[ 'filters' ]     = new Deprecated\DeprecatedFilterHooks( );
        $this -> container[ 'actions' ]     = new Deprecated\DeprecatedActionHooks( );
    } 

    /**
     * Add Stock Alert Email Class
     * @return void
     */
    function setup_email_class( $emails ) {
        $emails[ 'WC_Admin_Email_Stock_Manager' ] = new Emails\AdminEmail( );
        $emails[ 'WC_Subscriber_Confirmation_Email_Stock_Manager' ] = new Emails\SubscriberConfirmationEmail( );
        $emails[ 'WC_Email_Stock_Manager' ] = new Emails\Emails( );
        return $emails;
    } 
    
    /**
     * Take action based on if woocommerce is not loaded
     * @return void
     */
    public function is_woocommerce_loaded( ) {
        if ( did_action( 'woocommerce_loaded' ) || ! is_admin( ) ) {
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
    public function load_plugin_textdomain( ) {
        $locale = is_admin( ) && function_exists( 'get_user_locale' ) ? get_user_locale( ) : get_locale( );
        $locale = apply_filters( 'plugin_locale', $locale, 'woocommerce-stock-manager' );
        load_textdomain( 'woocommerce-stock-manager', WP_LANG_DIR . '/woocommerce-product-stock-alert/woocommerce-product-stock-alert-' . $locale . '.mo' );
        load_plugin_textdomain( 'woocommerce-stock-manager', false, plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
    } 

    /**
     * Magic getter function to get the reference of class.
     * Accept class name, If valid return reference, else Wp_Error. 
     * @param   mixed $class
     * @return  object | \WP_Error
     */
    public function __get( $class ) {
        if ( array_key_exists( $class, $this -> container ) ) {
            return $this -> container[ $class ];
        } 
        return new \WP_Error( sprintf( 'Call to unknown class %s.', $class ) );
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