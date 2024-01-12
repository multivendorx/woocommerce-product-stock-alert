<?php

namespace StockManager;

class Admin {
    public $settings;

    public function __construct() {
        // admin pages manu and submenu
        add_action('admin_menu', array($this, 'add_settings_page'), 100);
        //admin script and style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));

        // create custom column
        add_action('manage_edit-product_columns', array($this, 'set_custom_column_header'));
        // manage stock manager column
        add_action('manage_product_posts_custom_column', array($this, 'display_subscriber_count_in_custom_column'), 10, 2);

        // show number of subscribers for individual product
        add_action('woocommerce_product_options_inventory_product_data', array($this, 'display_product_subscriber_count_in_metabox'), 10);
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
        $pro_sticker = apply_filters('is_stock_manager_pro_inactive', true) ? '<span class="stock-manager-pro-tag">Pro</span>' : '';

        add_menu_page(
            __('Stock Manager', 'woocommerce-stock-manager'),
            __('Stock Manager', 'woocommerce-stock-manager'),
            'manage_options',
            'woo-stock-manager-setting',
            [$this, 'create_setting_page'],
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PGcgZmlsbD0iIzlFQTNBOCIgZmlsbC1ydWxlPSJub256ZXJvIj4KICAgICAgICAgICAgPHBhdGggZD0iTTE5LjksNS43YzAuMiwwLjktMC4zLDEuOC0xLjEsMmMtMC4yLDAuMS0wLjUsMC4xLTAuNywwYy0wLjYtMC4xLTEuMS0wLjUtMS4zLTEuMiAgICBjLTAuMi0wLjYsMC0xLjIsMC40LTEuNmMwLjItMC4yLDAuNC0wLjMsMC43LTAuNEMxOC44LDQuMywxOS43LDQuOCwxOS45LDUuN3ogTTE3LjgsOC45bC0zLjIsOS45Yy0wLjIsMC41LTAuNywwLjctMS4yLDAuNgogICAgICAgICAgICBMMC42LDE1LjJDMC4xLDE1LTAuMSwxNC41LDAsMTRMNC4zLDEuMmMwLjItMC41LDAuNy0wLjcsMS4yLTAuNkwxNiw0LjFjLTAuNSwwLjctMC43LDEuNy0wLjUsMi42QzE1LjgsNy45LDE2LjcsOC43LDE3LjgsOC45egogICAgICAgICAgICBNMTAuOCw0LjljMC41LDAuMiwxLDAuNSwxLjUsMC43YzAuMi0wLjQsMC0wLjktMC40LTEuMUMxMS40LDQuNCwxMSw0LjUsMTAuOCw0Ljl6IE05LjUsMTUuMmMtMC45LTAuMS0xLjctMC4yLTIuNi0wLjIKICAgICAgICAgICAgYzAuMSwwLjcsMC42LDEuMiwxLjIsMS4yQzguNywxNi4yLDkuMywxNS44LDkuNSwxNS4yeiBNMTIuNyw5YzAtMS43LTEuNC0zLjEtMy4xLTMuMmMtMS4yLDAtMi4yLDAuNS0yLjgsMS41CiAgICAgICAgICAgIGMtMC42LDAuOS0xLjEsMS44LTEuNywyLjdjLTAuMSwwLjEtMC4yLDAuMi0wLjMsMC4xYy0wLjUtMC4yLTAuOCwwLTEuMSwwLjZjLTAuMiwwLjQsMCwwLjgsMC40LDFjMC43LDAuNCwxLjQsMC43LDIuMiwxLjEKICAgICAgICAgICAgYzEuNCwwLjcsMi44LDEuNCw0LjIsMi4xYzAuNCwwLjIsMC44LDAuMSwxLjEtMC40YzAtMC4xLDAuMS0wLjEsMC4xLTAuMmMwLjEtMC4zLDAtMC43LTAuMy0wLjljLTAuMi0wLjEtMC4yLTAuMi0wLjEtMC40CiAgICAgICAgICAgIGMwLjQtMSwwLjgtMiwxLjEtM0MxMi43LDkuNywxMi43LDksMTIuNyw5eiIvPjwvZz48L3N2Zz4=', 
            50
        );

        add_submenu_page(
            'woo-stock-manager-setting',                              
            __('Settings', 'woocommerce-stock-manager'),      
            __('Settings', 'woocommerce-stock-manager'),      
            'manage_options',                                       
            'woo-stock-manager-setting#&tab=settings&subtab=general', 
            '__return_null'                                         
        );
        
        add_submenu_page( 
            'woo-stock-manager-setting', 
            __('Subscriber List', 'woocommerce-stock-manager'), 
            __('Subscriber List ' . $pro_sticker, 'woocommerce-stock-manager'), 
            'manage_woocommerce', 
            'woo-stock-manager-setting#&tab=subscriber-list', 
            '__return_null' 
        );
        
        add_submenu_page(
            'tools.php', 
            __('WC Stock Manager Export', 'woocommerce-stock-manager'), 
            __('WC Stock Manager Export', 'woocommerce-stock-manager'), 
            'manage_options',
            'woo-product-stock-manager-export-admin',
            [$this, 'create_csv_export_page']
        );

        remove_submenu_page('woo-stock-manager-setting', 'woo-stock-manager-setting');
    }

    /**
     * Create empty div. React root from here.
     * @return void
     */
    public function create_setting_page() {
        echo '<div id="woo-admin-stockmanager"></div>';
    }

    /**
     * Create Stock Manager Export (CSV) option to 'Tool' manue.
     * @return void
     */
    public function create_csv_export_page() {
        ?>
            <div class="wrap">
            <h1><?php _e('Stock Manager Export', 'woocommerce-stock-manager') ?></h1>
            <p><?php _e('When you click the button below, this will export all out of stock products with subscribers email.', 'woocommerce-stock-manager') ?></p>
            <form class="alert-export-data" id="alert-export-data" method="post" action="<?php echo admin_url('admin-ajax.php?action=export_subscribers') ?>">
                <input type="hidden" name="export_csv" value="1">
                <input type="submit" class="button-primary" value="<?php _e('Export CSV', 'woocommerce-stock-manager')  ?>">
            </form>
            </div>
        <?php
    }

    /**
     * Register bulk action in 'all product' table.
     * @param mixed $bulk_actions
     * @return mixed
     */
    function register_subscribers_bulk_actions($bulk_actions) {
        $bulk_actions['remove_subscribers'] = __('Remove Subscribers', 'woocommerce-stock-manager');
        return $bulk_actions;
    }

    /**
     * Bulk action handler function.
     * @param mixed $redirect_to
     * @param mixed $doaction
     * @param mixed $post_ids
     * @return mixed
     */
    function subscribers_bulk_action_handler($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'remove_subscribers') {
            return $redirect_to;
        }
        foreach ($post_ids as $post_id) {
            $product_ids = \StockManager\Subscriber::get_related_product(wc_get_product($post_id));
            foreach ($product_ids as $product_id) {
                $emails = \StockManager\Subscriber::get_product_subscribers_email($product_id);
                foreach ($emails as $alert_id => $to) {
                    \StockManager\Subscriber::update_subscriber($alert_id, 'woo_unsubscribed');
                }
                delete_post_meta($product_id, 'no_of_subscribers');
            }
        }
        $redirect_to = add_query_arg('bulk_remove_subscribers', count($post_ids), $redirect_to);
        return $redirect_to;
    }

    /**
     * Set Admin notice in time of bulk action.
     * @return void
     */
    function subscribers_bulk_action_admin_notice() {
        if (!empty($_REQUEST['bulk_remove_subscribers'])) {
            $bulk_remove_count = intval($_REQUEST['bulk_remove_subscribers']);
            printf('<div id="message" class="updated fade"><p>' .
                    _n('Removed subscribers from %s product.', 'Removed subscribers from %s products.', $bulk_remove_count, 'woocommerce-stock-manager'
                    ) . '</p></div>', $bulk_remove_count);
        }
    }

    /**
     * Set style for admin's setting pages.
     * @return void
     */
    public function admin_plugin_page_style() {
        ?>
        <style>
            a.stock-manager-pro-plugin {
                font-weight: 700;
                background: linear-gradient(110deg, rgb(63, 20, 115) 0%, 25%, rgb(175 59 116) 50%, 75%, rgb(219 75 84) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            a.stock-manager-pro-plugin:hover {
                background: #3f1473;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
        <?php
    }

    /**
     * Enqueue JavaScript for admin fronend page and localize script.
     * @return void
     */
    public function enqueue_admin_script() {
        global $Woo_Stock_Manager;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        $columns_subscriber = apply_filters('woo_stock_manager_subscribers_list_headers', array(
            array(
                'name'      =>  __('Date', 'woocommerce-stock-manager'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "date",
            ),
            array(
                'name'      =>  __('Product', 'woocommerce-stock-manager'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "product",
            ),
            array(
                'name'      =>  __('Email', 'woocommerce-stock-manager'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "email",
            ),
            array(
                'name'      =>  __('Registered', 'woocommerce-stock-manager'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "reg_user",
            ),
            array(
                'name'      =>  __('Status', 'woocommerce-stock-manager'),
                'selector'  =>  '',
                'sortable'  =>  false,
                'selector_choice'  => "status",
            )
        ));

        $subscription_page_string     =   array(
            'all'           =>  __('All', 'woocommerce-stock-manager'),
            'subscribe'     =>  __('Subscribe', 'woocommerce-stock-manager'),
            'unsubscribe'   =>  __('Unsubscribe', 'woocommerce-stock-manager'),
            'mail_sent'     =>  __('Mail Sent', 'woocommerce-stock-manager'),
            'search'        =>  __('Search by Email', 'woocommerce-stock-manager'),
            'show_product'  =>  __('Search by Product Name', 'woocommerce-stock-manager'),
            'daterenge'     =>  __('DD-MM-YYYY ~ DD-MM-YYYY', 'woocommerce-stock-manager'),
        );

        $setting_string     =   array(
            'form_dec'              =>  __('Form Description', 'woocommerce-stock-manager'),
            'submit_button_text'    =>  __('Submit Button Text', 'woocommerce-stock-manager'),
            'background'            =>  __('Background', 'woocommerce-stock-manager'),
            'border'                =>  __('Border', 'woocommerce-stock-manager'),
            'hover_background'      =>  __('Hover Background', 'woocommerce-stock-manager'),
            'hover_border'          =>  __('Hover Border', 'woocommerce-stock-manager'),
            'hover_text'            =>  __('Hover Text', 'woocommerce-stock-manager'),
            'font_size'             =>  __('Font Size', 'woocommerce-stock-manager'),
            'border_radius'         =>  __('Border Radius', 'woocommerce-stock-manager'),
            'border_size'           =>  __('Border Size', 'woocommerce-stock-manager'),
        );

        $pro_settings_list = apply_filters('woo_stock_manager_pro_settings_lists', array(
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
        
        if (get_current_screen()->id == 'toplevel_page_woo-stock-manager-setting') {
            wp_enqueue_script( 'woo-stockmanager-script', $Woo_Stock_Manager->plugin_url . 'build/index.js', array( 'wp-element' ), $Woo_Stock_Manager->version, true );
            wp_localize_script( 'woo-stockmanager-script', 'stockManagerAppLocalizer', apply_filters('woo_stock_manager_settings', [
                'apiUrl'                    => home_url('/wp-json'),
                'nonce'                     => wp_create_nonce('wp_rest'),
                'default_alert_text'        => __('Receive in-stock notifications for this product.', 'woocommerce-stock-manager'),
                'default_email_place'       => __('Enter your email', 'woocommerce-stock-manager'),
                'default_alert_button'      => __('Notify me', 'woocommerce-stock-manager'),
                'subscriber_list'           => $Woo_Stock_Manager->plugin_url . 'assets/images/subscriber-list.jpg',
                'pro_active'                => apply_filters('woo_stock_manager_pro_active', 'free'),
                'columns_subscriber'        => $columns_subscriber,
                'subscription_page_string'  => $subscription_page_string,
                'download_csv'              => __('Download CSV', 'woocommerce-stock-manager'),
                'pro_settings_list'         => $pro_settings_list,
                'pro_coupon_code'           => __('UPGRADE10', 'woocommerce-stock-manager'),
                'pro_coupon_text'           => __('Don\'t miss out! Enjoy 10% off on our pro features.', 'woocommerce-stock-manager'),
                'pro_url'                   => esc_url(WOO_STOCK_MANAGER_PRO_SHOP_URL),
                'setting_string'            => $setting_string,
                'banner_show'               => get_option('woocommerce_stock_manager_pro_banner_hide') ? false : true,
                'default_massages_fields'   => $woo_admin_massages_fields,
                'default_massages'          => \StockManager\Utill::get_form_settings_array()
              ]));
            wp_enqueue_style('woo_stockmanager_style', $Woo_Stock_Manager->plugin_url . 'build/index.css');
            wp_enqueue_style('woo_admin_rsuite_css', $Woo_Stock_Manager->plugin_url . 'assets/admin/css/rsuite-default' . '.min' . '.css', array(), $Woo_Stock_Manager->version);
        }
        wp_enqueue_style('stock_manager_product_admin_css', $Woo_Stock_Manager->plugin_url . 'assets/admin/css/admin'. $suffix .'.css');
    }

    /**
     * Custom column addition
     */
    function set_custom_column_header($columns) {
        return array_merge($columns, array('product_subscriber' => __('Interested Person(s)', 'woocommerce-stock-manager')));
    }

    /**
     * Manage custom column for Stock Manager
     */
    function display_subscriber_count_in_custom_column($column_name, $post_id) {
        if($column_name == 'product_subscriber') {
            $no_of_subscriber = get_post_meta($post_id, 'no_of_subscribers', true);
            echo '<div class="product-subscribtion-column">' . ((isset($no_of_subscriber) && $no_of_subscriber > 0) ? $no_of_subscriber : 0) . '</div>';
        }
    }

    /**
     * Stock Manager news on Product edit page (simple)
     */
    function display_product_subscriber_count_in_metabox($product) {
        global $post;

        if(\StockManager\Subscriber::is_product_outofstock($post->ID)){
            $no_of_subscriber = get_post_meta($post->ID, 'no_of_subscribers', true);
            ?>
            <p class="form-field _stock_field">
                <label class=""><?php _e('Number of Interested Person(s)', 'woocommerce-stock-manager'); ?></label>
                <span class="no_subscriber"><?php echo ((isset($no_of_subscriber) && $no_of_subscriber > 0) ? $no_of_subscriber : 0); ?></span>
            </p>
            <?php
        }
    }

    /**
     * Stock Manager news on Product edit page (variable)
     */
    function display_product_subscriber_count_in_variation_metabox($loop, $variation_data, $variation) {
        if (\StockManager\Subscriber::is_product_outofstock($variation->ID, 'variation')) {
            $product_subscriber = get_post_meta($variation->ID, 'no_of_subscribers', true);
            ?>
            <p class="form-row form-row-full interested_person">
                <label class="stock_label"><?php _e('Number of Interested Person(s) : ', 'woocommerce-stock-manager'); ?></label>
            <div class="variation_no_subscriber"><?php echo ((isset($product_subscriber) && $product_subscriber > 0) ? $product_subscriber : 0); ?></div>
            </p>
            <?php
        }
    }
}