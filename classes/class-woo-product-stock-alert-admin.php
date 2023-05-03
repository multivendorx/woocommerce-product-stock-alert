<?php

class WOO_Product_Stock_Alert_Admin {
    public $settings;

    public function __construct() {

        //admin script and style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));
        $this->load_class('settings');
        $this->settings = new WOO_Product_Stock_Alert_Settings();
        add_action('admin_menu', array($this, 'add_export_page'), 100);

        if (get_mvx_product_alert_plugin_settings('is_enable')) {
            // create custom column
            add_action('manage_edit-product_columns', array($this, 'custom_column'));
            // manage stock alert column
            add_action('manage_product_posts_custom_column', array($this, 'manage_custom_column'), 10, 2);
            // manage interest column 
            add_filter('manage_edit-product_sortable_columns', array($this, 'manage_interest_column_sorting'));
            add_filter('request', array($this, 'manage_interest_column_orderby'));

            // show number of subscribers for individual product
            add_action('woocommerce_product_options_inventory_product_data', array($this, 'product_subscriber_details'));
            add_action('woocommerce_product_after_variable_attributes', array($this, 'manage_variation_custom_column'), 10, 3);

            // check product stock status
            add_action('save_post', array($this, 'check_product_stock_status'), 5, 2);

            // bulk action to remove subscribers
            add_filter('bulk_actions-edit-product', array($this, 'register_subscribers_bulk_actions'));
            add_filter('handle_bulk_actions-edit-product', array($this, 'subscribers_bulk_action_handler'), 10, 3);
            add_action('admin_notices', array($this, 'subscribers_bulk_action_admin_notice'));
        }
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
                            delete_post_meta( $child_id, 'no_of_subscribers' );
                            delete_post_meta( $child_id, '_product_subscriber' );
                        }
                    }
                }
			} else {
                delete_post_meta( $post_id, 'no_of_subscribers' );
                delete_post_meta( $post_id, '_product_subscriber' );
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

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WOO_Product_Stock_Alert;
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        if (get_current_screen()->id == 'woocommerce_page_woo-product-stock-alert-setting-admin') {
            wp_enqueue_script( 'mvx-stockalert-script', $WOO_Product_Stock_Alert->plugin_url . 'build/index.js', array( 'wp-element' ), $WOO_Product_Stock_Alert->version, true );
            wp_localize_script( 'mvx-stockalert-script', 'stockalertappLocalizer', apply_filters('stockalert_settings', [
                'apiUrl' => home_url( '/wp-json' ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                //'banner_img' => $WOO_Product_Stock_Alert->plugin_url . 'assets/images/catalog-pro-add-admin-banner.jpg',
              ] ) );
            wp_enqueue_style( 'mvx-stockalert-style', $WOO_Product_Stock_Alert->plugin_url . 'build/index.css' );
        }
        if (get_current_screen()->id == 'tools_page_woo-product-stock-alert-export-admin') {
            wp_enqueue_script('stock_alert_admin_js', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/js/admin'. $suffix .'.js', array('jquery'), $WOO_Product_Stock_Alert->version, true);
            wp_localize_script('stock_alert_admin_js', 'dc_params', array( 'ajaxurl'    => 'admin-ajax.php' ));
        }
        wp_enqueue_style('stock_alert_product_admin_css', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/css/admin'. $suffix .'.css' );
    }

    /**
     * Custom column addition
     */
    function custom_column($columns) {
        global $WOO_Product_Stock_Alert;
        return array_merge($columns, array('product_subscriber' => __('Interested Person(s)', 'woocommerce-product-stock-alert')));
    }

    function manage_interest_column_sorting($columns) {
        $columns['product_subscriber'] = 'product_subscriber';
        return $columns;
    }

    function manage_interest_column_orderby($vars) {

        if (isset($vars['orderby']) && 'product_subscriber' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => 'no_of_subscribers',
                'orderby' => 'meta_value'
            ));
        }

        return $vars;
    }

    /**
     * Add options page
     */
    public function add_export_page() {
        global $WOO_Product_Stock_Alert;

        add_submenu_page(
                'tools.php', __('WC Stock Alert Export', 'woocommerce-product-stock-alert'), __('WC Stock Alert Export', 'woocommerce-product-stock-alert'), 'manage_options', 'woo-product-stock-alert-export-admin', array($this, 'create_woo_product_stock_alert_export')
        );
    }

    function create_woo_product_stock_alert_export() {
        global $WOO_Product_Stock_Alert;
        new WOO_Product_Stock_Alert_Export();
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
                                if (mvx_is_product_outofstock($child_id, 'variation')) {
                                    $no_of_subscriber += get_no_subscribed_persons($child_id);
                                }
                            }
                        }
                        echo $no_of_subscriber;
                    } else {
                        $no_of_subscriber += get_no_subscribed_persons($product_obj->get_id());
                        echo $no_of_subscriber;
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
            if (mvx_is_product_outofstock($post->ID)) {
                $no_of_subscriber = get_no_subscribed_persons($post->ID);
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
        if (mvx_is_product_outofstock($variation->ID, 'variation')) {
            $product_subscriber = get_no_subscribed_persons($variation->ID);
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

    /**
     * Alert on Product Stock Update
     *
     */
    function check_product_stock_status($post_id, $post) {
        if ($post->post_type == 'product') {
            $product_subscriber = array();
            $product_obj = array();
            $product_obj = wc_get_product($post_id);
            if ($product_obj && $product_obj->is_type('variable')) {
                if ($product_obj->has_child()) {
                    $child_ids = $product_obj->get_children();
                    if (isset($child_ids) && !empty($child_ids)) {
                        foreach ($child_ids as $child_id) {
                            $child_obj = new WC_Product_Variation($child_id);
                            $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                            if (isset($product_subscriber) && !empty($product_subscriber)) {
                                $product_availability_stock = $child_obj->get_stock_quantity();
                                $manage_stock = $child_obj->get_manage_stock();
                                $stock_status = $child_obj->get_stock_status();
                                if ($stock_status == 'instock' ) {
                                    if (isset($product_availability_stock) && $manage_stock) {
                                        if ($product_availability_stock > 0) {
                                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                            foreach ($product_subscriber as $to) {
                                                $email->trigger($to, $child_id);
                                            }
                                            delete_post_meta($child_id, '_product_subscriber');
                                            delete_post_meta($child_id, 'no_of_subscribers');
                                        }
                                    } else {
                                        $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                        foreach ($product_subscriber as $to) {
                                            $email->trigger($to, $child_id);
                                        }
                                        delete_post_meta($child_id, '_product_subscriber');
                                        delete_post_meta($child_id, 'no_of_subscribers');
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $product_subscriber = get_post_meta($post_id, '_product_subscriber', true);
                if (isset($product_subscriber) && !empty($product_subscriber)) {
                    $product_availability_stock = $product_obj->get_stock_quantity();
                    $manage_stock = $product_obj->get_manage_stock();
                    $stock_status = $product_obj->get_stock_status();
                    if ($stock_status == 'instock' ) {
                        if (isset($product_availability_stock) && $manage_stock) {
                            if ($product_availability_stock > 0) {
                                $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                foreach ($product_subscriber as $to) {
                                    $email->trigger($to, $post_id);
                                }
                                delete_post_meta($post_id, '_product_subscriber');
                                delete_post_meta($post_id, 'no_of_subscribers');
                            }
                        } else {
                            $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                            foreach ($product_subscriber as $to) {
                                $email->trigger($to, $post_id);
                            }
                            delete_post_meta($post_id, '_product_subscriber');
                            delete_post_meta($post_id, 'no_of_subscribers');
                        }
                    }
                }
            }
        }
    }
}