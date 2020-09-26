<?php

class WOO_Product_Stock_Alert_Admin {

    private $dc_plugin_settings;
    public $settings;

    public function __construct() {
        // Get plugin settings
        $this->dc_plugin_settings = get_dc_plugin_settings();

        //admin script and style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'));

        add_action('woo_product_stock_alert_dualcube_admin_footer', array($this, 'dualcube_admin_footer_for_woo_product_stock_alert'));

        $this->load_class('settings');
        $this->settings = new WOO_Product_Stock_Alert_Settings();

        add_action('admin_menu', array($this, 'add_export_page'), 100);

        if (isset($this->dc_plugin_settings) && !empty($this->dc_plugin_settings)) {
            if (isset($this->dc_plugin_settings['is_enable']) && $this->dc_plugin_settings['is_enable'] == 'Enable') {
                // create custom column
                add_action('manage_edit-product_columns', array($this, 'custom_column'));

                // manage stock alert column
                add_action('manage_product_posts_custom_column', array($this, 'manage_custom_column'), 10, 2);
                // manage interest column sortable
                add_filter('manage_edit-product_sortable_columns', array($this, 'manage_interest_column_sorting'));
                add_filter('request', array($this, 'manage_interest_column_orderby'));

                // show number of subscribers for individual product
                add_action('woocommerce_product_options_stock_fields', array($this, 'product_subscriber_details'));
                add_action('woocommerce_product_after_variable_attributes', array($this, 'manage_variation_custom_column'), 10, 3);

                // check product stock status
                add_action('save_post', array($this, 'check_product_stock_status'), 5, 2);
                // bulk action to remove subscribers
                add_filter('bulk_actions-edit-product', array($this, 'register_subscribers_bulk_actions'));
                add_filter('handle_bulk_actions-edit-product', array($this, 'subscribers_bulk_action_handler'), 10, 3);
                add_action('admin_notices', array($this, 'subscribers_bulk_action_admin_notice'));
            }
        }
    }

    function load_class($class_name = '') {
        global $WOO_Product_Stock_Alert;
        if ('' != $class_name) {
            require_once ($WOO_Product_Stock_Alert->plugin_path . '/admin/class-' . esc_attr($WOO_Product_Stock_Alert->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    function dualcube_admin_footer_for_woo_product_stock_alert() {
        global $WOO_Product_Stock_Alert;
        ?>
        <div style="clear: both"></div>
        <div id="dc_admin_footer">
            <?php _e('Powered by', 'woocommerce-product-stock-alert'); ?> <a href="http://wc-marketplace.com" target="_blank"><img src="<?php echo $WOO_Product_Stock_Alert->plugin_url . '/assets/images/dualcube.png'; ?>"></a><?php _e('WC Marketplace', 'woocommerce-product-stock-alert'); ?> &copy; <?php echo date('Y'); ?>
        </div>
        <?php
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
            if($product && $product->get_parent_id() != 0 && get_post_meta( $post_id, '_product_subscriber', true )){
                delete_post_meta( $product->get_parent_id(), 'no_of_subscribers' );
                delete_post_meta( $post_id, '_product_subscriber' );
            } if($product && $product->is_type('variable')) {
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
        $screen = get_current_screen();

        $WOO_Product_Stock_Alert->library->load_qtip_lib();
        $WOO_Product_Stock_Alert->library->load_colorpicker_lib();
        wp_enqueue_script('stock_alert_admin_js', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $WOO_Product_Stock_Alert->version, true);
        wp_enqueue_style('stock_alert_admin_css', $WOO_Product_Stock_Alert->plugin_url . 'assets/admin/css/admin.css', array(), $WOO_Product_Stock_Alert->version);
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
        $dc_settings = get_dc_plugin_settings();
        $no_of_subscriber = 0;
        $product_subscriber = array();
        $index = 0;
        $child_ids = $product_obj = array();
        switch ($column_name) {
            case 'product_subscriber' :

                $product_obj = wc_get_product($post_id);
                if (!$product_obj->is_type('grouped')) {
                    if ($product_obj->is_type('variable')) {
                        $child_ids = $product_obj->get_children();
                        if (isset($child_ids) && !empty($child_ids)) {
                            foreach ($child_ids as $child_id) {
                                $child_obj = new WC_Product_Variation($child_id);
                                $managing_stock = $child_obj->managing_stock();
                                $product_availability_stock = intval($child_obj->get_stock_quantity());
                                $manage_stock = $child_obj->get_manage_stock();
                                $stock_status = $child_obj->get_stock_status();

                                if (isset($product_availability_stock) && $manage_stock) {
                                    if ($managing_stock && $product_availability_stock <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                                        if ($child_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                            if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                                $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                                if (!empty($product_subscriber)) {
                                                    $index = 1;
                                                    $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                                }
                                            }
                                        } else {
                                            $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                            if (!empty($product_subscriber)) {
                                                $index = 1;
                                                $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                            }
                                        }
                                    } elseif ($product_availability_stock <= 0) {
                                        if ($child_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                            if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                                                $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                                if (!empty($product_subscriber)) {
                                                    $index = 1;
                                                    $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                                }
                                            }
                                        } else {
                                            $product_subscriber = get_post_meta($child_id, '_product_subscriber', true);
                                            if (!empty($product_subscriber)) {
                                                $index = 1;
                                                $no_of_subscriber = $no_of_subscriber + count($product_subscriber);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($index == 1) {
                            if ($no_of_subscriber > 0) {
                                echo '<span class="stock_column">' . $no_of_subscriber . '</span>';
                            } else {
                                echo '<span class="stock_column_zero">0</span>';
                            }
                        }
                    } else {
                        $managing_stock = $product_obj->managing_stock();
                        $stock_quantity = $product_obj->get_stock_quantity();
                        $manage_stock = $product_obj->get_manage_stock();
                        $stock_status = $product_obj->get_stock_status();

                        if (isset($stock_quantity) && $manage_stock) {
                            if ($managing_stock && $stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                                if ($product_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                    if ($stock_status == 'outofstock' || $stock_quantity <= 0) {
                                        $product_subscriber = get_post_meta($product_obj->get_id(), '_product_subscriber', true);
                                        if ($product_subscriber) {
                                            $no_of_subscriber = count($product_subscriber);
                                            echo '<span class="stock_column">' . $no_of_subscriber . '</span>';
                                        } else {
                                            echo '<span class="stock_column_zero">0</span>';
                                        }
                                    }
                                } else {
                                    $product_subscriber = get_post_meta($product_obj->get_id(), '_product_subscriber', true);
                                    if ($product_subscriber) {
                                        $no_of_subscriber = count($product_subscriber);
                                        echo '<span class="stock_column">' . $no_of_subscriber . '</span>';
                                    } else {
                                        echo '<span class="stock_column_zero">0</span>';
                                    }
                                }
                            } elseif ($stock_quantity <= 0) {
                                if ($product_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                                    if ($stock_status == 'outofstock' || $stock_quantity <= 0) {
                                        $product_subscriber = get_post_meta($product_obj->get_id(), '_product_subscriber', true);
                                        if ($product_subscriber) {
                                            $no_of_subscriber = count($product_subscriber);
                                            echo '<span class="stock_column">' . $no_of_subscriber . '</span>';
                                        } else {
                                            echo '<span class="stock_column_zero">0</span>';
                                        }
                                    }
                                } else {
                                    $product_subscriber = get_post_meta($product_obj->get_id(), '_product_subscriber', true);
                                    if ($product_subscriber) {
                                        $no_of_subscriber = count($product_subscriber);
                                        echo '<span class="stock_column">' . $no_of_subscriber . '</span>';
                                    } else {
                                        echo '<span class="stock_column_zero">0</span>';
                                    }
                                }
                            }
                        }
                    }
                }
        }
    }

    /**
     * Stock Alert news on Product edit page (simple)
     */
    function product_subscriber_details() {
        global $post, $WOO_Product_Stock_Alert;
        $dc_settings = get_dc_plugin_settings();
        $no_of_subscriber = 0;
        $flag = false;
        $product_obj = wc_get_product($post->ID);
        if (!$product_obj->is_type('variable')) {
            $product_availability_stock = intval($product_obj->get_stock_quantity());
            $manage_stock = $product_obj->get_manage_stock();
            $managing_stock = $product_obj->managing_stock();
            $stock_status = $product_obj->get_stock_status();

            if (isset($product_availability_stock) && $manage_stock) {
                if ($managing_stock && $product_availability_stock <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                    if ($product_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                        if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                            $flag = true;
                        }
                    } else {
                        $flag = true;
                    }
                } elseif ($product_availability_stock <= 0) {
                    if ($product_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                        if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                            $flag = true;
                        }
                    } else {
                        $flag = true;
                    }
                }

                if ($flag) {
                    $product_subscriber = get_post_meta($post->ID, '_product_subscriber', true);
                    if (!empty($product_subscriber)) {
                        $no_of_subscriber = count($product_subscriber);
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
    }

    /**
     * Stock Alert news on Product edit page (variable)
     */
    function manage_variation_custom_column($loop, $variation_data, $variation) {
        global $WOO_Product_Stock_Alert;
        $dc_settings = get_dc_plugin_settings();
        $flag = false;
        $variation_id = $variation->ID;
        $variation_obj = new WC_Product_Variation($variation_id);
        $product_availability_stock = intval($variation_obj->get_stock_quantity());
        $manage_stock = $variation_obj->get_manage_stock();
        $managing_stock = $variation_obj->managing_stock();
        $stock_status = $variation_obj->get_stock_status();

        if (isset($product_availability_stock) && $manage_stock) {
            if ($managing_stock && $product_availability_stock <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                if ($variation_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                    if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }
            } elseif ($product_availability_stock <= 0) {
                if ($variation_obj->backorders_allowed() && isset($dc_settings['is_enable_backorders']) && $dc_settings['is_enable_backorders'] == 'Enable') {
                    if ($stock_status == 'outofstock' || $product_availability_stock <= 0) {
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }
            }

            if ($flag) {
                $product_subscriber = get_post_meta($variation_id, '_product_subscriber', true);
                if (!empty($product_subscriber)) {
                    ?>
                    <p class="form-row form-row-full interested_person">
                        <label class="stock_label"><?php echo _e('Number of Interested Person(s) : ', 'woocommerce-product-stock-alert'); ?></label>
                    <div class="variation_no_subscriber"><?php echo count($product_subscriber); ?></div>
                    </p>
                    <?php
                }
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
                                if (isset($product_availability_stock) && $manage_stock) {
                                    if ($product_availability_stock > 0) {
                                        $email = WC()->mailer()->emails['WC_Email_Stock_Alert'];
                                        foreach ($product_subscriber as $to) {
                                            $email->trigger($to, $child_id);
                                        }
                                        delete_post_meta($child_id, '_product_subscriber');
                                        $parent_product_subscribers = get_post_meta($child_obj->get_parent_id(), 'no_of_subscribers', true);
                                        update_post_meta($child_obj->get_parent_id(), 'no_of_subscribers', $parent_product_subscribers - count($product_subscriber));
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
                    if (isset($product_availability_stock) && $manage_stock) {
                        if ($product_availability_stock > 0) {
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
