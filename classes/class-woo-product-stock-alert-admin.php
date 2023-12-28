<?php

class WOO_Product_Stock_Alert_Admin {
    public $settings;

    public function __construct() {
        //admin script and style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));
        $this->load_class('settings');
        $this->settings = new WOO_Product_Stock_Alert_Settings();

        // create custom column
        add_action('manage_edit-product_columns', array($this, 'custom_column'));
        // manage stock alert column
        add_action('manage_product_posts_custom_column', array($this, 'manage_custom_column'), 10, 2);

        // show number of subscribers for individual product
        add_action('woocommerce_product_options_inventory_product_data', array($this, 'product_subscriber_details'));
        add_action('woocommerce_product_after_variable_attributes', array($this, 'manage_variation_custom_column'), 10, 3);

        // bulk action to remove subscribers
        add_filter('bulk_actions-edit-product', array($this, 'register_subscribers_bulk_actions'));
        add_filter('handle_bulk_actions-edit-product', array($this, 'subscribers_bulk_action_handler'), 10, 3);
        add_action('admin_notices', array($this, 'subscribers_bulk_action_admin_notice'));
        add_action('admin_print_styles-plugins.php', array( $this, 'admin_plugin_page_style'));
    }

    function load_class($class_name = '') {
        global $WOO_Product_Stock_Alert;
        if ('' != $class_name) {
            require_once ($WOO_Product_Stock_Alert->plugin_path . '/admin/class-' . esc_attr($WOO_Product_Stock_Alert->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

    function register_subscribers_bulk_actions($bulk_actions) {
        $bulk_actions['remove_subscribers'] = __('Remove Subscribers', 'woocommerce-product-stock-alert');
        return $bulk_actions;
    }

    function subscribers_bulk_action_handler($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'remove_subscribers') {
            return $redirect_to;
        }
        foreach ($post_ids as $post_id) {
            $product = wc_get_product($post_id);
            if($product && $product->is_type('variable')) {
                if ($product->has_child()) {
                    $child_ids = $product->get_children();
                    if (isset($child_ids) && !empty($child_ids)) {
                        foreach ($child_ids as $child_id) {
                            $subscribers_email = get_product_subscribers_email($child_id);
                            if ($subscribers_email && !empty($subscribers_email)) {
                                foreach ($subscribers_email as $alert_id => $to) {
                                    update_subscriber($alert_id, 'woo_unsubscribed');
                                }
                                delete_post_meta($child_id, 'no_of_subscribers');
                            }
                        }
                    }
                }
			} else {
                $subscribers_email = get_product_subscribers_email($post_id);
                if ($subscribers_email && !empty($subscribers_email)) {
                    foreach ($subscribers_email as $alert_id => $to) {
                        update_subscriber($alert_id, 'woo_unsubscribed');
                    }
                    delete_post_meta($post_id, 'no_of_subscribers');
                }
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
        global $WOO_Product_Stock_Alert;
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
            wp_enqueue_script( 'woo-stockalert-script', $WOO_Product_Stock_Alert->plugin_url . 'build/index.js', array( 'wp-element' ), $WOO_Product_Stock_Alert->version, true );
            wp_localize_script( 'woo-stockalert-script', 'stockalertappLocalizer', apply_filters('stockalert_settings', [
                'apiUrl'                    => home_url('/wp-json'),
                'nonce'                     => wp_create_nonce('wp_rest'),
                'default_alert_text'        => __('Receive in-stock notifications for this product.', 'woocommerce-product-stock-alert'),
                'default_email_place'       => __('Enter your email', 'woocommerce-product-stock-alert'),
                'default_alert_button'      => __('Notify me', 'woocommerce-product-stock-alert'),
                'banner_img'                => $WOO_Product_Stock_Alert->plugin_url . 'assets/images/stock-alert-pro-banner.jpg',
                'subscriber_list'           => $WOO_Product_Stock_Alert->plugin_url . 'assets/images/subscriber-list.jpg',
                'pro_active'                => apply_filters('woo_stock_alert_pro_active', 'free'),
                'columns_subscriber'        => $columns_subscriber,
                'subscription_page_string'  => $subscription_page_string,
                'download_csv'              => __('Download CSV', 'woocommerce-product-stock-alert'),
                'pro_settings_list'         => $pro_settings_list,
                'pro_coupon_code'           => __('UPGRADE10', 'woocommerce-product-stock-alert'),
                'pro_coupon_text'           => __('Don\'t miss out! Enjoy 10% off on our pro features.', 'woocommerce-product-stock-alert'),
                'pro_url'                   => esc_url(WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL),
                'setting_string'            => $setting_string,
                'banner_show'               => $this->woo_stockalert_is_banner_close(),
                'default_massages_fields'   => $woo_admin_massages_fields,
                'default_massages'          => get_woo_default_massages(),
              ]));
            wp_enqueue_style('woo-stockalert-style', $WOO_Product_Stock_Alert->plugin_url . 'build/index.css');
            wp_enqueue_style('woo_admin_rsuite_css', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/css/rsuite-default' . '.min' . '.css', array(), $WOO_Product_Stock_Alert->version);
        }
        
        wp_enqueue_style('stock_alert_product_admin_css', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/css/admin'. $suffix .'.css');
    }

    /**
     * Custom column addition
     */
    function custom_column($columns) {
        global $WOO_Product_Stock_Alert;
        return array_merge($columns, array('product_subscriber' => __('Interested Person(s)', 'woocommerce-product-stock-alert')));
    }

    /**
     * Manage custom column for Stock Alert
     */
    function manage_custom_column($column_name, $post_id) {
        $no_of_subscriber = 0;
        $product_subscriber = $child_ids = $product_obj = array();
        switch ($column_name) {
            case 'product_subscriber' :
                $product_obj = wc_get_product($post_id);
                if (!$product_obj->is_type('grouped')) {
                    if ($product_obj->is_type('variable')) {
                        $child_ids = $product_obj->get_children();
                        if (isset($child_ids) && !empty($child_ids)) {
                            foreach ($child_ids as $child_id) {
                                if (woo_is_product_outofstock($child_id, 'variation')) {
                                    $no_of_subscriber += get_no_subscribed_persons($child_id, 'woo_subscribed');
                                }
                            }
                        }
                        echo '<div class="product-subscribtion-column">' . $no_of_subscriber . '</div>';
                    } else {
                        if (woo_is_product_outofstock($product_obj->get_id())) {
                            $no_of_subscriber = get_no_subscribed_persons($product_obj->get_id(), 'woo_subscribed');
                        }
                        echo '<div class="product-subscribtion-column">' . $no_of_subscriber . '</div>';
                    }
                }
        }
    }

    /**
     * Stock Alert news on Product edit page (simple)
     */
    function product_subscriber_details() {
        global $post, $WOO_Product_Stock_Alert;
        $no_of_subscriber = 0;
        $product_obj = wc_get_product($post->ID);
        if (!$product_obj->is_type('variable')) {
            if (woo_is_product_outofstock($post->ID)) {
                $no_of_subscriber = get_no_subscribed_persons($post->ID, 'woo_subscribed');
                if (!empty($no_of_subscriber) && $no_of_subscriber > 0) {
                    ?>
                    <p class="form-field _stock_field">
                        <label class=""><?php _e('Number of Interested Person(s)', 'woocommerce-product-stock-alert'); ?></label>
                        <span class="no_subscriber"><?php echo $no_of_subscriber; ?></span>
                    </p>
                    <?php
                }
            }
        }
    }

    /**
     * Stock Alert news on Product edit page (variable)
     */
    function manage_variation_custom_column($loop, $variation_data, $variation) {
        global $WOO_Product_Stock_Alert;
        if (woo_is_product_outofstock($variation->ID, 'variation')) {
            $product_subscriber = get_no_subscribed_persons($variation->ID, 'woo_subscribed');
            if (!empty($product_subscriber) && $product_subscriber >0) {
                ?>
                <p class="form-row form-row-full interested_person">
                    <label class="stock_label"><?php echo _e('Number of Interested Person(s) : ', 'woocommerce-product-stock-alert'); ?></label>
                <div class="variation_no_subscriber"><?php echo $product_subscriber; ?></div>
                </p>
                <?php
            }
        }
    }

    public function woo_stockalert_is_banner_close() {
        if (get_option('woocommerce_stock_alert_pro_banner_hide')) {
            return false;
        } else {
            return true;
        }
    }
}