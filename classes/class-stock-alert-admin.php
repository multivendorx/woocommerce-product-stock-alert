<?php

class Woo_Product_Stock_Alert_Admin {
    public $settings;

    public function __construct() {
        // admin pages manu and submenu
        add_action('admin_menu', array($this, 'add_settings_page'), 100);
        //admin script and style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));

        // create custom column
        add_action('manage_edit-product_columns', array($this, 'custom_column_header'));
        // manage stock alert column
        add_action('manage_product_posts_custom_column', array($this, 'custom_column_display_subscriber_count'), 10, 2);

        // show number of subscribers for individual product
        add_action('woocommerce_product_options_inventory_product_data', array($this, 'display_product_subscriber_count_in_metabox'));
        add_action('woocommerce_product_after_variable_attributes', array($this, 'display_product_subscriber_count_in_variation_metabox'), 10, 3);

        // bulk action to remove subscribers
        add_filter('bulk_actions-edit-product', array($this, 'register_subscribers_bulk_actions'));
        add_filter('handle_bulk_actions-edit-product', array($this, 'subscribers_bulk_action_handler'), 10, 3);
        add_action('admin_notices', array($this, 'subscribers_bulk_action_admin_notice'));
        add_action('admin_print_styles-plugins.php', array( $this, 'admin_plugin_page_style'));
    }

    /**
    * Add options page
    */
    public function add_settings_page() {
        $pro_sticker = apply_filters('is_stock_alert_pro_inactive', true) ? '<span class="stock-alert-pro-tag">Pro</span>' : '';

        add_menu_page(
            __('Stock Manager', 'woocommerce-product-stock-alert'),
            __('Stock Manager', 'woocommerce-product-stock-alert'),
            'manage_options',
            'woo-stock-alert-setting',
            [$this, 'create_woo_product_stock_alert_settings'],
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g fill="#9EA3A8" fill-rule="nonzero">
            <path d="M19.9,5.7c0.2,0.9-0.3,1.8-1.1,2c-0.2,0.1-0.5,0.1-0.7,0c-0.6-0.1-1.1-0.5-1.3-1.2    c-0.2-0.6,0-1.2,0.4-1.6c0.2-0.2,0.4-0.3,0.7-0.4C18.8,4.3,19.7,4.8,19.9,5.7z M17.8,8.9l-3.2,9.9c-0.2,0.5-0.7,0.7-1.2,0.6
            L0.6,15.2C0.1,15-0.1,14.5,0,14L4.3,1.2c0.2-0.5,0.7-0.7,1.2-0.6L16,4.1c-0.5,0.7-0.7,1.7-0.5,2.6C15.8,7.9,16.7,8.7,17.8,8.9z
            M10.8,4.9c0.5,0.2,1,0.5,1.5,0.7c0.2-0.4,0-0.9-0.4-1.1C11.4,4.4,11,4.5,10.8,4.9z M9.5,15.2c-0.9-0.1-1.7-0.2-2.6-0.2
            c0.1,0.7,0.6,1.2,1.2,1.2C8.7,16.2,9.3,15.8,9.5,15.2z M12.7,9c0-1.7-1.4-3.1-3.1-3.2c-1.2,0-2.2,0.5-2.8,1.5
            c-0.6,0.9-1.1,1.8-1.7,2.7c-0.1,0.1-0.2,0.2-0.3,0.1c-0.5-0.2-0.8,0-1.1,0.6c-0.2,0.4,0,0.8,0.4,1c0.7,0.4,1.4,0.7,2.2,1.1
            c1.4,0.7,2.8,1.4,4.2,2.1c0.4,0.2,0.8,0.1,1.1-0.4c0-0.1,0.1-0.1,0.1-0.2c0.1-0.3,0-0.7-0.3-0.9c-0.2-0.1-0.2-0.2-0.1-0.4
            c0.4-1,0.8-2,1.1-3C12.7,9.7,12.7,9,12.7,9z"/></g></svg>'), 
            50
        );

        add_submenu_page(
            'woo-stock-alert-setting',                              
            __('Settings', 'woocommerce-product-stock-alert'),      
            __('Settings', 'woocommerce-product-stock-alert'),      
            'manage_options',                                       
            'woo-stock-alert-setting#&tab=settings&subtab=general', 
            '__return_null'                                         
        );
        
        add_submenu_page( 
            'woo-stock-alert-setting', 
            __('Subscriber List', 'woocommerce-product-stock-alert'), 
            __('Subscriber List ' . $pro_sticker, 'woocommerce-product-stock-alert'), 
            'manage_woocommerce', 
            'woo-stock-alert-setting#&tab=subscriber-list', 
            '__return_null' 
        );
        
        add_submenu_page(
            'tools.php', 
            __('WC Stock Alert Export', 'woocommerce-product-stock-alert'), 
            __('WC Stock Alert Export', 'woocommerce-product-stock-alert'), 
            'manage_options',
            'woo-product-stock-alert-export-admin',
            array($this, 'create_woo_product_stock_alert_export')
        );

        remove_submenu_page('woo-stock-alert-setting', 'woo-stock-alert-setting');
    }

    public function create_woo_product_stock_alert_settings() {
        echo '<div id="woo-admin-stockalert"></div>';
    }

    public function create_woo_product_stock_alert_export() { 
        ?>
            <div class="wrap">
            <h1><?php _e('WC Stock Alert Export', 'woocommerce-product-stock-alert') ?></h1>
            <p><?php _e('When you click the button below, this will export all out of stock products with subscribers email.', 'woocommerce-product-stock-alert') ?></p>
            <form class="alert_export_data" id="alert_export_data" method="post" action="<?php echo admin_url('admin-ajax.php?action=export_subscribers') ?>">
                <input type="hidden" name="export_csv" value="1">
                <input type="submit" class="button-primary" value="<?php _e('Export CSV', 'woocommerce-product-stock-alert')  ?>">
            </form>
            </div>
        <?php
    }

    function register_subscribers_bulk_actions($bulk_actions) {
        $bulk_actions['remove_subscribers'] = __('Remove Subscribers', 'woocommerce-product-stock-alert');
        return $bulk_actions;
    }

    function subscribers_bulk_action_handler($redirect_to, $doaction, $post_ids) {
        global $Woo_Product_Stock_Alert;
        if ($doaction !== 'remove_subscribers') {
            return $redirect_to;
        }
        foreach ($post_ids as $post_id) {
            $product_ids = get_related_product(wc_get_product($post_id));
            foreach ($product_ids as $product_id) {
                $emails = $Woo_Product_Stock_Alert->subscriber->get_product_subscribers_email($product_id);
                foreach ($emails as $alert_id => $to) {
                    $Woo_Product_Stock_Alert->subscriber->update_subscriber($alert_id, 'woo_unsubscribed');
                }
                delete_post_meta($product_id, 'no_of_subscribers');
            }
        }
        $redirect_to = add_query_arg('bulk_remove_subscribers', count($post_ids), $redirect_to);
        return $redirect_to;
    }

    function subscribers_bulk_action_admin_notice() {
        if (!empty($_REQUEST['bulk_remove_subscribers'])) {
            $bulk_remove_count = intval($_REQUEST['bulk_remove_subscribers']);
            printf('<div id="message" class="updated fade"><p>' .
                    _n('Removed subscribers from %s product.', 'Removed subscribers from %s products.', $bulk_remove_count, 'woocommerce-product-stock-alert'
                    ) . '</p></div>', $bulk_remove_count);
        }
    }

    public function admin_plugin_page_style() {
        ?>
        <style>
            a.stock-alert-pro-plugin {
                font-weight: 700;
                background: linear-gradient(110deg, rgb(63, 20, 115) 0%, 25%, rgb(175 59 116) 50%, 75%, rgb(219 75 84) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            a.stock-alert-pro-plugin:hover {
                background: #3f1473;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
        <?php
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $Woo_Product_Stock_Alert;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        $columns_subscriber = apply_filters('woo_stock_alert_subscribers_list_headers', array(
            array(
                'name'      =>  __('Date', 'woocommerce-product-stock-alert'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "date",
            ),
            array(
                'name'      =>  __('Product', 'woocommerce-product-stock-alert'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "product",
            ),
            array(
                'name'      =>  __('Email', 'woocommerce-product-stock-alert'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "email",
            ),
            array(
                'name'      =>  __('Registered', 'woocommerce-product-stock-alert'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "reg_user",
            ),
            array(
                'name'      =>  __('Status', 'woocommerce-product-stock-alert'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "status",
            )
        ));

        $subscription_page_string     =   array(
            'all'           =>  __('All', 'woocommerce-product-stock-alert'),
            'subscribe'     =>  __('Subscribe', 'woocommerce-product-stock-alert'),
            'unsubscribe'   =>  __('Unsubscribe', 'woocommerce-product-stock-alert'),
            'mail_sent'     =>  __('Mail Sent', 'woocommerce-product-stock-alert'),
            'search'        =>  __('Search by Email', 'woocommerce-product-stock-alert'),
            'show_product'  =>  __('Search by Product Name', 'woocommerce-product-stock-alert'),
            'daterenge'     =>  __('DD-MM-YYYY ~ DD-MM-YYYY', 'woocommerce-product-stock-alert'),
        );

        $setting_string     =   array(
            'form_dec'              =>  __('Form Description', 'woocommerce-product-stock-alert'),
            'submit_button_text'    =>  __('Submit Button Text', 'woocommerce-product-stock-alert'),
            'background'            =>  __('Background', 'woocommerce-product-stock-alert'),
            'border'                =>  __('Border', 'woocommerce-product-stock-alert'),
            'hover_background'      =>  __('Hover Background', 'woocommerce-product-stock-alert'),
            'hover_border'          =>  __('Hover Border', 'woocommerce-product-stock-alert'),
            'hover_text'            =>  __('Hover Text', 'woocommerce-product-stock-alert'),
            'font_size'             =>  __('Font Size', 'woocommerce-product-stock-alert'),
            'border_radius'         =>  __('Border Radius', 'woocommerce-product-stock-alert'),
            'border_size'           =>  __('Border Size', 'woocommerce-product-stock-alert'),
        );

        $pro_settings_list = apply_filters('woocommerce_stock_alert_pro_settings_lists', array(
            'ban_email_domains',
            'ban_email_domain_text',
            'ban_email_addresses',
            'ban_email_address_text',
            'is_mailchimp_enable',
            'mailchimp_api',
            'get_mailchimp_list_button',
            'selected_mailchimp_list',
            'is_double_optin',
            'is_recaptcha_enable'
        ));

        $woo_admin_massages_fields = array(
            'double_opt_in_success',
            'shown_interest_text',
            'alert_success',
            'alert_email_exist',
            'valid_email',
            'alert_unsubscribe_message',
            'ban_email_domain_text',
            'ban_email_address_text'
        );
        
        if (get_current_screen()->id == 'toplevel_page_woo-stock-alert-setting') {
            wp_enqueue_script( 'woo-stockalert-script', $Woo_Product_Stock_Alert->plugin_url . 'build/index.js', array( 'wp-element' ), $Woo_Product_Stock_Alert->version, true );
            wp_localize_script( 'woo-stockalert-script', 'stockalertappLocalizer', apply_filters('stockalert_settings', [
                'apiUrl'                    => home_url('/wp-json'),
                'nonce'                     => wp_create_nonce('wp_rest'),
                'default_alert_text'        => __('Receive in-stock notifications for this product.', 'woocommerce-product-stock-alert'),
                'default_email_place'       => __('Enter your email', 'woocommerce-product-stock-alert'),
                'default_alert_button'      => __('Notify me', 'woocommerce-product-stock-alert'),
                'banner_img'                => $Woo_Product_Stock_Alert->plugin_url . 'assets/images/stock-alert-pro-banner.jpg',
                'subscriber_list'           => $Woo_Product_Stock_Alert->plugin_url . 'assets/images/subscriber-list.jpg',
                'pro_active'                => apply_filters('woo_stock_alert_pro_active', 'free'),
                'columns_subscriber'        => $columns_subscriber,
                'subscription_page_string'  => $subscription_page_string,
                'download_csv'              => __('Download CSV', 'woocommerce-product-stock-alert'),
                'pro_settings_list'         => $pro_settings_list,
                'pro_coupon_code'           => __('UPGRADE10', 'woocommerce-product-stock-alert'),
                'pro_coupon_text'           => __('Don\'t miss out! Enjoy 10% off on our pro features.', 'woocommerce-product-stock-alert'),
                'pro_url'                   => esc_url(WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL),
                'setting_string'            => $setting_string,
                'banner_show'               => get_option('woocommerce_stock_alert_pro_banner_hide') ? false : true,
                'default_massages_fields'   => $woo_admin_massages_fields,
                'default_massages'          => get_form_settings_array(),
              ]));
            wp_enqueue_style('woo-stockalert-style', $Woo_Product_Stock_Alert->plugin_url . 'build/index.css');
            wp_enqueue_style('woo_admin_rsuite_css', $Woo_Product_Stock_Alert->plugin_url . 'assets/admin/css/rsuite-default' . '.min' . '.css', array(), $Woo_Product_Stock_Alert->version);
        }
        wp_enqueue_style('stock_alert_product_admin_css', $Woo_Product_Stock_Alert->plugin_url . 'assets/admin/css/admin'. $suffix .'.css');
    }

    /**
     * Custom column addition
     */
    function custom_column_header($columns) {
        return array_merge($columns, array('product_subscriber' => __('Interested Person(s)', 'woocommerce-product-stock-alert')));
    }

    /**
     * Manage custom column for Stock Alert
     */
    function custom_column_display_subscriber_count($column_name, $post_id) {
        if($column_name == 'product_subscriber') {
            $no_of_subscriber = get_post_meta($post_id, 'no_of_subscribers', true);
            echo '<div class="product-subscribtion-column">' . ((isset($no_of_subscriber) && $no_of_subscriber > 0) ? $no_of_subscriber : 0) . '</div>';
        }
    }

    /**
     * Stock Alert news on Product edit page (simple)
     */
    function display_product_subscriber_count_in_metabox() {
        global $post, $Woo_Product_Stock_Alert;

        if(is_product_outofstock($post->ID)){
            $no_of_subscriber = get_post_meta($post->ID, 'no_of_subscribers', true);
            ?>
            <p class="form-field _stock_field">
                <label class=""><?php _e('Number of Interested Person(s)', 'woocommerce-product-stock-alert'); ?></label>
                <span class="no_subscriber"><?php echo ((isset($no_of_subscriber) && $no_of_subscriber > 0) ? $no_of_subscriber : 0); ?></span>
            </p>
            <?php
        }
    }

    /**
     * Stock Alert news on Product edit page (variable)
     */
    function display_product_subscriber_count_in_variation_metabox($loop, $variation_data, $variation) {
        global $Woo_Product_Stock_Alert;
        if (is_product_outofstock($variation->ID, 'variation')) {
            $product_subscriber = get_post_meta($variation->ID, 'no_of_subscribers', true);
            ?>
            <p class="form-row form-row-full interested_person">
                <label class="stock_label"><?php _e('Number of Interested Person(s) : ', 'woocommerce-product-stock-alert'); ?></label>
            <div class="variation_no_subscriber"><?php echo ((isset($product_subscriber) && $product_subscriber > 0) ? $product_subscriber : 0); ?></div>
            </p>
            <?php
        }
    }
}