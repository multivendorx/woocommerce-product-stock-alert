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

if (!function_exists('get_woo_form_settings_array')) {
    function get_woo_form_settings_array() {
        $default_text = __('Enter your email', 'woocommerce-product-stock-alert');
        $default_alert_text = __('Get an alert when the product is in stock:', 'woocommerce-product-stock-alert');
        $default_button_text = __('Get an alert', 'woocommerce-product-stock-alert');
        $default_unsubscribe_button_text = __('Unsubscribe', 'woocommerce-product-stock-alert');
        $default_alert_success = __('Thank you for your interest in <b>%product_title%</b>, you will receive an email alert when it becomes available.', 'woocommerce-product-stock-alert');
        $default_alert_email_exist = __('<b>%customer_email%</b> is already registered with <b>%product_title%</b>.', 'woocommerce-product-stock-alert');
        $default_valid_email = __('Please enter a valid email id and try again.', 'woocommerce-product-stock-alert');
        $default_alert_unsubscribe_message = __('<b>%customer_email%</b> is successfully unregistered.', 'woocommerce-product-stock-alert');
        $default_shown_interest_text = __('Product in demand: %no_of_subscribed% waiting.', 'woocommerce-product-stock-alert');

        $settings = array(
            'email_placeholder_text' => get_mvx_product_alert_plugin_settings('email_placeholder_text', $default_text),
            'alert_text' => get_mvx_product_alert_plugin_settings('alert_text', $default_alert_text),
            'alert_text_color' => get_mvx_product_alert_plugin_settings('alert_text_color', ''),
            'button_text' => get_mvx_product_alert_plugin_settings('button_text', $default_button_text),
            'unsubscribe_button_text' => get_mvx_product_alert_plugin_settings('unsubscribe_button_text', $default_unsubscribe_button_text),
            'button_background_color' => get_mvx_product_alert_plugin_settings('button_background_color', ''),
            'button_border_color' => get_mvx_product_alert_plugin_settings('button_border_color', ''),
            'button_text_color' => get_mvx_product_alert_plugin_settings('button_text_color', ''),
            'button_background_color_onhover' => get_mvx_product_alert_plugin_settings('button_background_color_onhover', ''),
            'button_text_color_onhover' => get_mvx_product_alert_plugin_settings('button_text_color_onhover', ''),
            'button_border_color_onhover' => get_mvx_product_alert_plugin_settings('button_border_color_onhover', ''),
            'alert_success' => get_mvx_product_alert_plugin_settings('alert_success', $default_alert_success),
            'alert_email_exist' => get_mvx_product_alert_plugin_settings('alert_email_exist', $default_alert_email_exist),
            'valid_email' => get_mvx_product_alert_plugin_settings('valid_email', $default_valid_email),
            'ban_email_domin' => apply_filters('stock_alert_ban_email_domin_text', ''),
            'ban_email_address' => apply_filters('stock_alert_ban_email_address_text', ''),
            'double_opt_in_success' => apply_filters('stock_alert_double_opt_in_success_text', ''),
            'alert_unsubscribe_message' => get_mvx_product_alert_plugin_settings('alert_unsubscribe_message', $default_alert_unsubscribe_message),
            'shown_interest_text' => get_mvx_product_alert_plugin_settings('shown_interest_text', $default_shown_interest_text),
            'button_font_size' => get_mvx_product_alert_plugin_settings('button_font_size', ''),
        );
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
    function get_product_subscribers_email($product_id) {
        $emails = array();
        $args = array(
            'post_type'     => 'woostockalert',
            'fields'        => 'ids',
            'posts_per_page'=> -1,
            'post_status'   => 'woo_subscribed',
            'meta_query'    => array(
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
                $email = get_post_meta($subsciber_id, 'wooinstock_subscriber_email', true);
                $emails[$subsciber_id] = $email ? $email : '';
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

        if (!$product_id) {
            return $is_stock;
        }

        if ($type == 'variation') {
            $child_obj = new WC_Product_Variation($product_id);
            $manage_stock = $child_obj->managing_stock();
            $stock_quantity = intval($child_obj->get_stock_quantity());
            $stock_status = $child_obj->get_stock_status();
        } else {
            $product = wc_get_product($product_id);
            $manage_stock = $product->get_manage_stock();
            $stock_quantity = $product->get_stock_quantity();
            $stock_status = $product->get_stock_status();
        }

        $is_enable_backorders = get_mvx_product_alert_plugin_settings('is_enable_backorders');

        if ($manage_stock) {
            if ($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                $is_stock = false;
            } elseif ($stock_quantity <= 0) {
                $is_stock = false;
            }
        } else {
            if ($stock_status == 'onbackorder' && $is_enable_backorders) {
                $is_stock = false;
            } elseif ($stock_status == 'instock') {
                $is_stock = false;
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

if(!function_exists('woo_stock_product_data')) {
    function woo_stock_product_data($product_id) {
        $product_data = array();
        $parent_product_id = wp_get_post_parent_id($product_id);
        if( $parent_product_id ) {
            $product_obj = wc_get_product( $parent_product_id );
            $parent_id = $parent_product_id ? $parent_product_id : 0;
            $product_data['link'] = admin_url('post.php?post=' . $parent_id . '&action=edit');
            $product_data['name'] = $product_obj && $product_obj->get_formatted_name() ? $product_obj->get_formatted_name() : '';
            $product_data['price'] = $product_obj && $product_obj->get_price_html() ? $product_obj->get_price_html() : '';
        } else {
            $product_obj = wc_get_product( $product_id );
            $product_data['link'] = admin_url('post.php?post=' . $product_id . '&action=edit');
            $product_data['name'] = $product_obj && $product_obj->get_formatted_name() ? $product_obj->get_formatted_name() : '';
            $product_data['price'] = $product_obj && $product_obj->get_price_html() ? $product_obj->get_price_html() : '';
        }
        return apply_filters('woo_stock_alert_product_data', $product_data, $product_id);
    }
}

if (!function_exists('woo_stock_alert_fileds')) {
    function woo_stock_alert_fileds() {
        $stock_alert_fields_array = array();
        $stock_alert_field = $user_email = '';
        $separator = apply_filters('woo_fileds_separator', '<br>');
        $settings_array = get_woo_form_settings_array();
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->data->user_email;
        }
        $placeholder = $settings_array['email_placeholder_text'];
        $alert_fields = apply_filters('woo_stock_alert_fileds_array', array(
            'alert_email' => array(
                'type' => 'text',
                'class'=> 'stock_alert_email woo-fields',
                'value'=> $user_email,
                'placeholder' => $placeholder
            )
        ), $settings_array);
        if ($alert_fields) {
            foreach ($alert_fields as $key => $fvalue) {
                $type = in_array($fvalue['type'], ['recaptcha-v3', 'text', 'number', 'email']) ? esc_attr($fvalue['type']) : 'text';
                $class = isset($fvalue['class']) ? esc_attr($fvalue['class']) : 'stock_alert_' . $key;
                $value = isset($fvalue['value']) ? esc_attr($fvalue['value']) : '';
                $placeholder = isset($fvalue['placeholder']) ? esc_attr($fvalue['placeholder']) : '';
                switch ($fvalue['type']) {
                    case 'recaptcha-v3':
                        $recaptcha_type = isset($fvalue['version']) ? esc_attr($fvalue['version']) : 'v3';
                        $sitekey = isset($fvalue['sitekey']) ? esc_attr($fvalue['sitekey']) : '';
                        $secretkey = isset($fvalue['secretkey']) ? esc_attr($fvalue['secretkey']) : '';

                        $recaptchaScript = '
                        <script>
                            grecaptcha.ready(function () {
                                grecaptcha.execute("' . $sitekey . '").then(function (token) {
                                    var recaptchaResponse = document.getElementById("recaptchav3_response");
                                    recaptchaResponse.value = token;
                                });
                            });
                        </script>';
                        
                        $recaptchaResponseInput = '<input type="hidden" id="recaptchav3_response" name="recaptchav3_response" value="" />';
                        $recaptchaSiteKeyInput = '<input type="hidden" id="recaptchav3_sitekey" name="recaptchav3_sitekey" value="' . esc_html($sitekey) . '" />';
                        $recaptchaSecretKeyInput = '<input type="hidden" id="recaptchav3_secretkey" name="recaptchav3_secretkey" value="' . esc_html($secretkey) . '" />';

                        $stock_alert_fields_array[] = $recaptchaScript . $recaptchaResponseInput . $recaptchaSiteKeyInput . $recaptchaSecretKeyInput;
                        break;
                    default:
                        $stock_alert_fields_array[] = '<input type="' . $type . '" name="' . $key . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '" >';
                        break;
                }
            }
        }
        if ($stock_alert_fields_array) {
            $stock_alert_field = implode($separator, $stock_alert_fields_array);
        }
        return $stock_alert_field;    
    }
}
if (!function_exists('get_product_subscribers_array')) {
    function get_product_subscribers_array() {
        $all_product_ids = $get_subscribed_user = array();

        $products = get_posts(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'numberposts' => -1
        ));
        if ($products) {
            foreach ($products as $product) {
                $product_obj = wc_get_product($product->ID);
                if ($product_obj->is_type('variable') && $product_obj->has_child()) {
                    $child_ids = $product_obj->get_children();
                    $all_product_ids = array_merge($all_product_ids, $child_ids);
                } else {
                    $all_product_ids[] = $product->ID;
                }
            }

            if (!empty($all_product_ids) && is_array($all_product_ids)) {
                foreach ($all_product_ids as $product_id) {
                    $subscribers = get_product_subscribers_email($product_id);
                    if ($subscribers && !empty($subscribers)) {
                        $get_subscribed_user[$product_id] = $subscribers; 
                    }
                }
            }
        }
        return $get_subscribed_user;
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
						'key'    => 'is_enable_backorders',
						'label'   => __( "Allow Subscription for Backorders Product", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
									'key'=> "is_enable_backorders",
									'label'=>  __('Enabling this setting allows users to subscribe to out-of-stock products, even when the backorder option is enabled.', 'woocommerce-product-stock-alert' ),
									'value'=> "is_enable_backorders"
								),
						),
						'database_value' => array(),
					],
                    [
						'key'    => 'is_enable_no_interest',
						'label'   => __( "Display Subscribers Count for Out-of-Stock Products", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
						'options' => array(
								array(
									'key'=> "is_enable_no_interest",
									'label'=>  __('Enabling this setting will display the number of subscribers for products on product page.', 'woocommerce-product-stock-alert' ),
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
                        'label'     => __( 'Subscription Count Notification Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('Enter the text that will inform users about the number of subscribers for this out-of-stock product. Note: Use %no_of_subscribed% as number of interest/subscribed persons.', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
						'key'    => 'is_double_optin',
						'label'   => __( "Double Opt-in", 'woocommerce-product-stock-alert' ),
						'class'     => 'mvx-toggle-checkbox',
						'type'    => 'checkbox',
                        'props'     => array(
                            'disabled'  => apply_filters('is_stock_alert_pro_inactive', true)
                        ),
						'options' => array(
								array(
									'key'=> "is_double_optin",
                                    'label'=> apply_filters('allow_store_inventory_double_optin', __('Upgrade to Pro to enable Double Opt-in flow for subscription confirmation.', 'woocommerce-product-stock-alert') ),
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
                        'label'     => __( 'Edit Email Field Placeholder Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent email field placeholder text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_text',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc' => __('Enter the text which you want to display as alert text.','woocommerce-product-stock-alert'),
                        'label'     => __( 'Edit Alert Text', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text',
                        'type'      => 'text',
                        'label'     => __( 'Edit Subscribe Button Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent subscribe button text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'unsubscribe_button_text',
                        'type'      => 'text',
                        'label'     => __( 'Edit Unsubscribe Button Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('It will represent unsubscribe button text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_font_size',
                        'type'      => 'number',
                        'label'     => __( 'Choose Button Font Size', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button font size.', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       =>  'button_color_section',
                        'type'      =>  'blocktext',
                        'label'     =>  __( 'no_label', 'woocommerce-product-stock-alert' ),
                        'blocktext'      =>  __( "Color Section", 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_text_color',
                        'type'      => 'color',
                        'label'     => __( 'Alert Text', 'woocommerce-product-stock-alert' ),
                        'desc' => __('This lets you choose alert text color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_background_color',
                        'type'      => 'color',
                        'label'     => __( 'Button Background', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button background color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_border_color',
                        'type'      => 'color',
                        'label'     => __( 'Button Border', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button border color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text_color',
                        'type'      => 'color',
                        'label'     => __( 'Button Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button text color.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_background_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Button Background on Hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose button background color on hover.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_border_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Button Border on Hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose alert button border color on hover.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text_color_onhover',
                        'type'      => 'color',
                        'label'     => __( 'Button Text on Hover', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('This lets you choose alert button text color on hover.','woocommerce-product-stock-alert'),
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
                        'label'     => __( 'Edit Alert Text When Form Submitted Successfully', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_email_exist',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __( 'Hint: Use %product_title% as product title and %customer_email% as customer email.<br/> Example: %customer_email% is already registered with %product_title%. Please try again.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit Alert Text When Email is Already Submitted', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'valid_email',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Default: Please enter a valid email id and try again.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit Alert Text For Valid Email Check', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_unsubscribe_message',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __( 'Default: %customer_email% is successfully unregistered.', 'woocommerce-product-stock-alert' ),
                        'label'     => __( 'Edit Alert Text For Successful Unsubscribe', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
				]
			),
            'email' => array(
                'tablabel'        =>  __('Email Settings', 'woocommerce-product-stock-alert'),
                'apiurl'          =>  'save_stockalert',
                'description'     =>  __('Configure email settings.', 'woocommerce-product-stock-alert'),
                'icon'            =>  'icon-email-setting',
                'submenu'         =>  'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'ban_email_domains',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Enter email domains if you want to ban from your site.(Separated by comma)', 'woocommerce-product-stock-alert'),
                        'label'     => __( 'Ban Email Domains', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_domain_text',
                        'type'      => 'textarea',
                        'label'     => __( 'Edit Ban Email Domains Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('Enter the text which you want to display as ban domain text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_addresses',
                        'type'      => 'textarea',
                        'class'     =>  'mvx-setting-wpeditor-class',
                        'desc'      => __('Enter email addresses if you want to ban from your site.(Separated by comma)', 'woocommerce-product-stock-alert'),
                        'label'     => __( 'Ban Email Addresses', 'woocommerce-product-stock-alert' ),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_address_text',
                        'type'      => 'textarea',
                        'label'     => __('Edit Ban Email Address Text', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('Enter the text which you want to display as ban email address text.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                ]
            ),

            'mailchimp' => array(
                'tablabel'        =>  __('Mailchimp Settings', 'woocommerce-product-stock-alert'),
                'apiurl'          =>  'save_stockalert',
                'description'     =>  __('Configure mailChimp settings. ', 'woocommerce-product-stock-alert'),
                'icon'            =>  'icon-mailchimp-setting',
                'submenu'         =>  'settings',
                'modulename'      =>  [
                    [
                        'key'    => 'is_mailchimp_enable',
                        'label'   => __( "Enable Mailchimp", 'woocommerce-product-stock-alert' ),
                        'class'     => 'mvx-toggle-checkbox',
                        'type'    => 'checkbox',
                        'options' => array(
                                array(
                                    'key'=> "is_mailchimp_enable",
                                    'label'=> __('Enable this to activate Mailchimp.', 'woocommerce-product-stock-alert' ),
                                    'value'=> "is_mailchimp_enable"
                                ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'mailchimp_api',
                        'type'      => 'text_api',
                        'depend_checkbox'   => 'is_mailchimp_enable',
                        'label'     => __( 'Mailchimp API', 'woocommerce-product-stock-alert' ),
                        'desc'      => __('','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'get_mailchimp_list_button',
                        'depend_checkbox'   => 'is_mailchimp_enable',
                        'label'     => 'no_label',
                        'type'      => 'button',
                        'api_link'  => 'mvx_stockalert_pro/v1/get_mailchimp_list',
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'selected_mailchimp_list',
                        'type'      => 'mailchimp_select',
                        'depend_checkbox'   => 'is_mailchimp_enable',
                        'label'     => __( 'Mailchimp List', 'woocommerce-product-stock-alert' ),
                        'desc'      => __( 'Select a mailchimp list.', 'woocommerce-product-stock-alert' ),
                        'options' => array(),
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
