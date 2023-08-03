<?php
if (!function_exists('woocommerce_inactive_notice')) {

    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sWoocommerce Product Stock Alert is inactive.%s The %sWooCommerce plugin%s must be active for the Woocommerce Product Stock Alert to work. Please %sinstall & activate WooCommerce%s', WOO_PRODUCT_STOCK_ALERT_TEXT_DOMAIN), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('get_mvx_product_alert_plugin_settings')) {

    function get_mvx_product_alert_plugin_settings($key = '', $default = false) {
        $mvx_plugin_settings = array();
        $all_options = apply_filters('mvx_woo_stock_alert_all_admin_options', array(
            'mvx_woo_stock_alert_form_customization_tab_settings',
            'mvx_woo_stock_alert_form_submission_tab_settings',
            'mvx_woo_stock_alert_general_tab_settings',
            )
        );
        
        foreach ($all_options as $option_name) { 
            $mvx_plugin_settings = array_merge($mvx_plugin_settings, get_option($option_name, array()));
        }
        if (empty($key)) {
            return $default;
        }
        if (!isset($mvx_plugin_settings[$key]) || empty($mvx_plugin_settings[$key])) {
            return $default;
        }
        return $mvx_plugin_settings[$key];
    }

}

if (!function_exists('delete_mvx_product_alert_plugin_settings')) {

    function delete_mvx_product_alert_plugin_settings($name = '', $tab = '') {
        if (empty($name)) {
            return;
        }
        if (!empty($tab)) {
            $option_name = "mvx_woo_stock_alert_{$tab}_tab_settings";
            $settings = get_option($option_name);
        }
        if ($settings && isset($settings[$name])) {
            unset($settings[$name]);
            update_option($option_name, $settings);
        }
    }
}

if (!function_exists('get_woo_form_settings_array')) {

    function get_woo_form_settings_array() {
        $settings = array(
            'email_placeholder_text' => get_mvx_product_alert_plugin_settings('email_placeholder_text') ? get_mvx_product_alert_plugin_settings('email_placeholder_text') : __('Enter your email', 'woocommerce-product-stock-alert'),
            'alert_text' => get_mvx_product_alert_plugin_settings('alert_text') ? get_mvx_product_alert_plugin_settings('alert_text') : __('Get an alert when the product is in stock:', 'woocommerce-product-stock-alert'),
            'alert_text_color' => get_mvx_product_alert_plugin_settings('alert_text_color')  ? get_mvx_product_alert_plugin_settings('alert_text_color') : '',
            'button_text' => get_mvx_product_alert_plugin_settings('button_text') ? get_mvx_product_alert_plugin_settings('button_text') : __('Get an alert', 'woocommerce-product-stock-alert'),
            'unsubscribe_button_text' => get_mvx_product_alert_plugin_settings('unsubscribe_button_text') ? get_mvx_product_alert_plugin_settings('unsubscribe_button_text') : __('Unsubscribe', 'woocommerce-product-stock-alert'),
            'button_background_color' => get_mvx_product_alert_plugin_settings('button_background_color') ? get_mvx_product_alert_plugin_settings('button_background_color') : '',
            'button_border_color' => get_mvx_product_alert_plugin_settings('button_border_color') ? get_mvx_product_alert_plugin_settings('button_border_color') : '',
            'button_text_color' => get_mvx_product_alert_plugin_settings('button_text_color') ? get_mvx_product_alert_plugin_settings('button_text_color') : '',
            'button_background_color_onhover' => get_mvx_product_alert_plugin_settings('button_background_color_onhover') ? get_mvx_product_alert_plugin_settings('button_background_color_onhover') : '',
            'button_text_color_onhover' => get_mvx_product_alert_plugin_settings('button_text_color_onhover') ? get_mvx_product_alert_plugin_settings('button_text_color_onhover') : '',
            'button_border_color_onhover' => get_mvx_product_alert_plugin_settings('button_border_color_onhover') ? get_mvx_product_alert_plugin_settings('button_border_color_onhover') : '',
            'alert_success' => get_mvx_product_alert_plugin_settings('alert_success') ? get_mvx_product_alert_plugin_settings('alert_success') : '',
            'alert_email_exist' => get_mvx_product_alert_plugin_settings('alert_email_exist') ? get_mvx_product_alert_plugin_settings('alert_email_exist') : '',
            'valid_email' => get_mvx_product_alert_plugin_settings('valid_email') ? get_mvx_product_alert_plugin_settings('valid_email') : '',
            'ban_email_domin' => apply_filters('stock_alert_ban_email_domin_text', ''),
            'ban_email_address' => apply_filters('stock_alert_ban_email_address_text', ''),
            'double_opt_in_success' => apply_filters('stock_alert_double_opt_in_success_text', ''),
            'alert_unsubscribe_message' => get_mvx_product_alert_plugin_settings('alert_unsubscribe_message') ? get_mvx_product_alert_plugin_settings('alert_unsubscribe_message') : '',
            'shown_interest_text' => get_mvx_product_alert_plugin_settings('shown_interest_text') ? get_mvx_product_alert_plugin_settings('shown_interest_text') : __('Already %no_of_subscribed% persons shown interest.', 'woocommerce-product-stock-alert'),
            'button_font_size' => get_mvx_product_alert_plugin_settings('button_font_size') ? get_mvx_product_alert_plugin_settings('button_font_size'). 'px' : '',
        );

        if (empty($settings['alert_success'])) {
            $settings['alert_success'] = __('Thank you for your interest in <b>%product_title%</b>, you will receive an email alert when it becomes available.', 'woocommerce-product-stock-alert');
        }
        if (empty($settings['alert_email_exist'])) {
            $settings['alert_email_exist'] = __('<b>%customer_email%</b> is already registered with <b>%product_title%</b>.', 'woocommerce-product-stock-alert');
        }
        if (empty($settings['valid_email'])) {
            $settings['valid_email'] = __('Please enter a valid email id and try again.', 'woocommerce-product-stock-alert');
        }
        if (empty($settings['alert_unsubscribe_message'])) {
            $settings['alert_unsubscribe_message'] = __('<b>%customer_email%</b> is successfully unregistered.', 'woocommerce-product-stock-alert');
        }
        return $settings;
    }
}

if (!function_exists('get_mvx_product_alert_old_plugin_settings')) {
    function get_mvx_product_alert_old_plugin_settings($key = '', $default = false) {
        $mvx_old_plugin_settings = array();
        $mvx_old_plugin_settings = get_option('dc_woo_product_stock_alert_general_settings_name');

        if (empty($key)) {
            return $default;
        }
        if (!isset($mvx_old_plugin_settings[$key]) || empty($mvx_old_plugin_settings[$key])) {
            return $default;
        }
        return $mvx_old_plugin_settings[$key];
    }
}

if (!function_exists('save_mvx_product_alert_settings')) {
    function save_mvx_product_alert_settings($key, $option_val) {
        update_option( $key, $option_val );
    }
}

if (!function_exists('update_subscriber')) {
    function update_subscriber( $stockalert_id, $status) {
        $args = array(
            'ID' => $stockalert_id,
            'post_type' => 'woostockalert',
            'post_status' => $status,
        );
        $id = wp_update_post($args);
        return $id;
    }
}

if (!function_exists('mvx_convert_select_structure')) {
    function mvx_convert_select_structure($data_fileds = array(), $csv = false, $object = false) {
        $is_csv = $csv ? 'key' : 'value';
        $datafileds_initialize_array = [];
        if ($data_fileds) {
            foreach($data_fileds as $fileds_key => $fileds_value) {
                if ($object) {
                    $datafileds_initialize_array[] = array(
                        'value' => $fileds_value->ID,
                        'label' => $fileds_value->post_title
                    );
                } else {
                    $datafileds_initialize_array[] = array(
                        $is_csv => $csv ? $fileds_value : $fileds_key,
                        'label' => $fileds_value
                    );
                }
            }
        }
        return $datafileds_initialize_array;
    }
}

if (!function_exists('update_product_subscriber_count')) {
    function update_product_subscriber_count( $product_id ) {
        $get_count = get_no_subscribed_persons($product_id, 'woo_subscribed');
        update_post_meta($product_id, 'no_of_subscribers', $get_count);   
    }
}

if (!function_exists('insert_subscriber')) {
    function insert_subscriber($subscriber_email, $product_id) {
        $args = array(
            'post_title' => $subscriber_email,
            'post_type' => 'woostockalert',
            'post_status' => 'woo_subscribed',
        );

        $id = wp_insert_post($args);
        if (!is_wp_error($id)) {
            $default_data = array(
                'wooinstock_product_id' => $product_id,
                'wooinstock_subscriber_email' => $subscriber_email,
            );
            foreach ($default_data as $key => $value) {
                update_post_meta($id, $key, $value);
            }
            update_product_subscriber_count($product_id);
            return $id;
        } else {
            return false;
        }
    }
}

if (!function_exists('insert_subscriber_email_trigger')) {
    function insert_subscriber_email_trigger($product_id, $customer_email) {
        $admin_mail = WC()->mailer()->emails['WC_Admin_Email_Stock_Alert'];
        $cust_mail = WC()->mailer()->emails['WC_Subscriber_Confirmation_Email_Stock_Alert'];
        $admin_email = '';
        if (get_mvx_product_alert_plugin_settings('is_remove_admin_email')) {
            $admin_email = '';
        } else {
            $admin_email = get_option('admin_email');
        }

        if (get_mvx_product_alert_plugin_settings('additional_alert_email')) {
            $admin_email .= ','.get_mvx_product_alert_plugin_settings('additional_alert_email');
        }

        if (function_exists( 'get_mvx_product_vendors' )) {
            $vendor = get_mvx_product_vendors( $product_id );
            if ($vendor && apply_filters( 'mvx_wc_product_stock_alert_add_vendor', true )) {
                $admin_email .= ','. sanitize_email( $vendor->user_data->user_email );  
            }
        }
        //admin email or vendor email
        if( !empty( $admin_email ) )
        $admin_mail->trigger( $admin_email, $product_id, $customer_email );

        //customer email
        $cust_mail->trigger( $customer_email, $product_id );
    }
}

if (!function_exists('is_already_subscribed')) {
    function is_already_subscribed($subscriber_email, $product_id) {
        $args = array(
            'post_type' => 'woostockalert',
            'fields' => 'ids',
            'posts_per_page' => 1,
            'post_status' => 'woo_subscribed',
        );
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => 'wooinstock_product_id',
                'value' => $product_id,
            ),
            array(
                'key' => 'wooinstock_subscriber_email',
                'value' => $subscriber_email,
            ),
        );
        $args['meta_query'] = $meta_query;
        $get_posts = get_posts($args);
        return $get_posts;
    }
}

if (!function_exists('get_no_subscribed_persons')) {
    function get_no_subscribed_persons($product_id, $status = 'any') {
        $args = array(
            'post_type' => 'woostockalert',
            'post_status' => $status,
            'meta_query' => array(
                array(
                    'key' => 'wooinstock_product_id',
                    'value' => array($product_id),
                    'compare' => 'IN',
                )),
            'numberposts' => -1,
        );
        $query = get_posts($args);
        return count($query); 
    }
}

if (!function_exists('get_product_subscribers_email')) {
    function get_product_subscribers_email( $product_id ) {
        $emails = array();
        $args = array(
            'post_type' => 'woostockalert',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => 'woo_subscribed',
            'meta_query'      => array(
                    array(
                        'key'     => 'wooinstock_product_id',
                        'value'   => ( $product_id > '0' || $product_id ) ? $product_id : 'no_data_found',
                        'compare' => '='
                    )
                )
        );
        $subsciber_post = get_posts($args);
        if ($subsciber_post && count($subsciber_post) > 0) {
            foreach ($subsciber_post as $subsciber_id) {
                $emails[$subsciber_id] = get_post_meta( $subsciber_id, 'wooinstock_subscriber_email', true ) ? get_post_meta( $subsciber_id, 'wooinstock_subscriber_email', true ) : '';
            }
        }

        return $emails;
    }
}

if (!function_exists('customer_stock_alert_insert')) {

    function customer_stock_alert_insert( $product_id, $customer_email) {
        if (empty($product_id) && empty($customer_email)) return;
        $do_complete_additional_task = apply_filters( 'mvx_wc_product_stock_alert_do_complete_additional_task', false );
        $is_accept_email_address = apply_filters( 'mvx_stock_alert_is_accept_email_address', false );
        
        if (is_already_subscribed($customer_email, $product_id)) {
            return $status = '/*?%already_registered%?*/';
        } else if ($do_complete_additional_task) {
            return $status = apply_filters( 'mvx_wc_product_stock_alert_new_subscriber_added', $status, $customer_email, $product_id );
        } else if ($is_accept_email_address) {
            return $status = apply_filters( 'mvx_wc_product_stock_alert_accept_email', $status, $customer_email, $product_id );
        } else {
            insert_subscriber($customer_email, $product_id);
            insert_subscriber_email_trigger($product_id, $customer_email);
            return true;
        }
    }
}

if (!function_exists('customer_stock_alert_unsubscribe')) {

    function customer_stock_alert_unsubscribe( $product_id, $customer_email) {
        $unsubscribe_post = is_already_subscribed($customer_email, $product_id);
        if ($unsubscribe_post) {
            foreach($unsubscribe_post as $post){
                update_subscriber($post, 'woo_unsubscribed');
            }
            update_product_subscriber_count($product_id);
            return true;
        }
        return false; 
    }
}

if (!function_exists('mvx_is_product_outofstock')) {

    function mvx_is_product_outofstock($product_id, $type = '') {
        $is_stock = true;
        if ($product_id) {
            if ($type == 'variation') {
                $child_obj = new WC_Product_Variation($product_id);
                $ch_managing_stock = $child_obj->managing_stock();
                $ch_stock_quantity = intval($child_obj->get_stock_quantity());
                $ch_manage_stock = $child_obj->get_manage_stock();
                $ch_stock_status = $child_obj->get_stock_status();
                if ($ch_manage_stock) {
                    if ($child_obj->backorders_allowed() && get_mvx_product_alert_plugin_settings('is_enable_backorders')) {                            
                        $is_stock = false; 
                    } else {
                        if ($ch_stock_quantity > (int) get_option('woocommerce_notify_no_stock_amount')) {
                            $is_stock = false;
                        } else {
                            $is_stock = true;

                        }
                    }
                } else {
                    if( $ch_stock_status == 'onbackorder' && get_mvx_product_alert_plugin_settings('is_enable_backorders') ) {
                        $is_stock = false;
                    } elseif($ch_stock_status == 'instock') {
                        $is_stock = false;
                    }
                }
            } else {
                $product = wc_get_product($product_id);
                $stock_quantity = $product->get_stock_quantity();
                $manage_stock = $product->get_manage_stock();
                $stock_status = $product->get_stock_status();
                if ($manage_stock) {
                    if ($product->backorders_allowed() && get_mvx_product_alert_plugin_settings('is_enable_backorders')) {                            
                        $is_stock = false; 
                    } else {
                        if ($stock_quantity > (int) get_option('woocommerce_notify_no_stock_amount')) {
                            $is_stock = false;
                        } else {
                            $is_stock = true;

                        }
                    }
                } else {
                    if( $stock_status == 'onbackorder' && get_mvx_product_alert_plugin_settings('is_enable_backorders') ) {
                        $is_stock = false;
                    } elseif($stock_status == 'instock') {
                        $is_stock = false;
                    }
                }
            }
        }
        return $is_stock;
    }
}

if(!function_exists('is_activate_double_opt_in')) {
    function is_activate_double_opt_in() {
        $mvx_plugin_settings = array();
        $mvx_plugin_settings = get_option('mvx_woo_stock_alert_general_tab_settings', array());
        if (!isset($mvx_plugin_settings['is_double_optin']) || empty($mvx_plugin_settings['is_double_optin'])) {
            return false;
        }
        return $mvx_plugin_settings['is_double_optin'];
    }
}

/**
 * Write to log file
 */
if (!function_exists('doWooStockAlertLOG')) {

    function doWooStockAlertLOG($str) {
        global $WOO_Product_Stock_Alert;
        $file = $WOO_Product_Stock_Alert->plugin_path . 'log/stock_alert_log.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
            if ($current) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents($file, $current);
        }
    }
}

if (!function_exists('mvx_stockalert_admin_tabs')) {
    function mvx_stockalert_admin_tabs(){
        $stock_alert_settings_page_endpoint = apply_filters('mvx_stockalert_endpoint_fields_before_value', array(
			'general' => array(
				'tablabel'        =>  __('General', 'woocommerce-product-stock-alert'),
				'apiurl'          =>  'save_stockalert',
				'description'     =>  __('Configure basic product alert settings. ', 'woocommerce-product-stock-alert'),
				'icon'            =>  'icon-general',
				'submenu'         =>  'settings',
				'modulename'      =>  [
					[
						'key'    => 'is_enable',
						'label'   => __( "Enable stock alert", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
										'key'=> "is_enable",
										'label'=> __('Enable this to activate stock alert.', 'woocommerce-product-stock-alert' ),
										'value'=> "is_enable"
								),
						),
						'database_value' => array(),
					],
                    [
						'key'    => 'is_enable_backorders',
						'label'   => __( "Enable with backorders", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
										'key'=> "is_enable_backorders",
										'label'=>  __('Enable this to activate backorder with stock alert.', 'woocommerce-product-stock-alert' ),
										'value'=> "is_enable_backorders"
								),
						),
						'database_value' => array(),
					],
                    [
						'key'    => 'is_enable_no_interest',
						'label'   => __( "Enable No. of Interest on Product Page", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
										'key'=> "is_enable_no_interest",
										'label'=>  __('How many person shown interest or subscribed for the product.', 'woocommerce-product-stock-alert' ),
										'value'=> "is_enable_no_interest"
								),
						),
						'database_value' => array(),
					],
                    [
                        'key'       => 'shown_interest_text',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'depend_checkbox'   => 'is_enable_no_interest',
                        'label'     => __( 'Edit Shown Interest text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('Enter the text which you want to display as shown interest text<br>Hint: Use %no_of_subscribed% as number of interest/subscribed persons', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
						'key'    => 'is_double_optin',
						'label'   => __( "Double opt-in", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
                        'props'     => array(
                            'disabled'  => apply_filters('is_stock_alert_pro_inactive', true)
                        ),
						'options' => array(
								array(
										'key'=> "is_double_optin",
										// 'label'=> __('Enable this option to send customers mail to seek permission for storing customer\'s mail id.', 'woocommerce-product-stock-alert' ),
                                        'label'=> apply_filters('allow_store_inventory_double_optin', __('Upgrade to MultiVendorX Pro and enable store inventory module to enable Double Opt-in flow for subscription confirmation.', 'woocommerce-product-stock-alert') ),
										'value'=> "is_double_optin"
								),
						),
						'database_value' => array(),
					],
                    [
						'key'    => 'is_remove_admin_email',
						'label'   => __( "Remove Admin Email", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
										'key'=> "is_remove_admin_email",
										'label'=> __('Remove admin email from stock alert receivers list.', 'woocommerce-product-stock-alert' ),
										'value'=> "is_remove_admin_email"
								),
						),
						'database_value' => array(),
					],
                    [
                        'key'       => 'additional_alert_email',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Enter email address if you want to receive stock alert mail along with admin mail. You can add multiple commma seperated emails. Default: Admin emails.', 'woocommerce-product-stock-alert'),
                        'label'     => __( 'Additional Receivers Emails', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
				]
			),
            'form_customization' => array(
				'tablabel'        =>  __('Form Customization', 'woocommerce-product-stock-alert'),
				'apiurl'          =>  'save_stockalert',
				'description'     =>  __('Configure form settings. ', 'woocommerce-product-stock-alert'),
				'icon'            =>  'icon-form-customization',
				'submenu'         =>  'settings',
				'modulename'      =>  [
                    [
                        'key'       => 'email_placeholder_text',
                        'type'      => 'text',
                        'label'     => __( 'Edit email field placeholder text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent email field placeholder text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_text',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc' => __('Enter the text which you want to display as alert text.','woocommerce-product-stock-alert'),
                        'label'     => __( 'Edit alert text', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_text_color',
                        'type'      => 'color',
                        'label'     => __( 'Choose alert text color', 'woocommerce-product-stock-alert' ),
                        'desc' => __('This lets you choose alert text color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text',
                        'type'      => 'text',
                        'label'     => __( 'Edit subscribe button text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent subscribe button text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'unsubscribe_button_text',
                        'type'      => 'text',
                        'label'     => __( 'Edit unsubscribe button text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent unsubscribe button text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_background_color',
                        'type'      => 'color',
                        'label'     => __( 'Choose button background color', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button background color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_border_color',
                        'type'      => 'color',
                        'label'     => __( 'Choose button border color', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button border color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text_color',
                        'type'      => 'color',
                        'label'     => __( 'Choose button text color', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button text color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_background_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Choose button background color on hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button background color on hover.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_border_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Choose button border color on hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose alert button border color on hover.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Choose button text color on hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose alert button text color on hover.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_font_size',
                        'type'      => 'number',
                        'label'     => __( 'Choose button font size', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button font size.', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
				]
			),
            'form_submission' => array(
				'tablabel'        =>  __('Form Submission', 'woocommerce-product-stock-alert'),
				'apiurl'          =>  'save_stockalert',
				'description'     =>  __('Configure form submission settings. ', 'woocommerce-product-stock-alert'),
				'icon'            =>  'icon-form-submission',
				'submenu'         =>  'settings',
				'modulename'      =>  [
					[
                        'key'       => 'alert_success',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Hint: Use %product_title% as product title and %customer_email% as customer email.<br/> Example: Thank you for your interest in %product_title%, you will receive an email alert when it becomes available.', 'woocommerce-product-stock-alert'),
                        'label'     => __( 'Edit alert text when form submitted successfully', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_email_exist',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __( 'Hint: Use %product_title% as product title and %customer_email% as customer email.<br/> Example: %customer_email% is already registered with %product_title%. Please try again.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit alert text when email is already submitted', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'valid_email',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Hint: Use %product_title% as product title and %customer_email% as customer email.<br/> Example: Please enter a valid email id and try again.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit alert text for valid email check', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_unsubscribe_message',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __( 'Hint: Use %customer_email% as customer email.<br/> Example: %customer_email% is successfully unregistered.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit alert text for successful unsubscribe', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
				]
			),
        ));

        if (!empty($stock_alert_settings_page_endpoint)) {
            foreach ($stock_alert_settings_page_endpoint as $settings_key => $settings_value) {
            	if (isset($settings_value['modulename']) && !empty($settings_value['modulename'])) {
	                foreach ($settings_value['modulename'] as $inter_key => $inter_value) {
	                    $change_settings_key    =   str_replace("-", "_", $settings_key);
	                    $option_name = 'mvx_woo_stock_alert_'.$change_settings_key.'_tab_settings';
	                    $database_value = get_option($option_name) ? get_option($option_name) : array();
	                    if (!empty($database_value)) {
	                        if (isset($inter_value['key']) && array_key_exists($inter_value['key'], $database_value)) {
	                            if (empty($inter_value['database_value'])) {
	                               $stock_alert_settings_page_endpoint[$settings_key]['modulename'][$inter_key]['database_value'] = $database_value[$inter_value['key']];
	                            }
	                        }
	                    }
	                }
	            }
            }
        }

		$mvx_stock_alert_backend_tab_list = apply_filters('mvx_stock_alert_tab_list', array(
			'stock_alert-settings'      => $stock_alert_settings_page_endpoint,
		));
        
		return $mvx_stock_alert_backend_tab_list;
    }
}
