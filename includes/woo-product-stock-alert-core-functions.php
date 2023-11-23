<?php
if (!function_exists('woocommerce_inactive_notice')) {
    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sProduct Stock Manager & Notifier for WooCommerce is inactive.%s The %sWooCommerce plugin%s must be active for the Product Stock Manager & Notifier for WooCommerce to work. Please %sinstall & activate WooCommerce%s', 'woocommerce-product-stock-alert'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }
}

if (!function_exists('get_woo_product_alert_plugin_settings')) {
    function get_woo_product_alert_plugin_settings($key = '', $default = false) {
        $woo_plugin_settings = array();
        $all_options = apply_filters('woo_stock_alert_all_admin_options', array(
            'woo_stock_alert_form_customization_tab_settings',
            'woo_stock_alert_form_submission_tab_settings',
            'woo_stock_alert_general_tab_settings',
            )
        );
        
        foreach ($all_options as $option_name) {
            if (is_array(get_option($option_name, array()))) {
                $woo_plugin_settings = array_merge($woo_plugin_settings, get_option($option_name, array()));
            }
        }
        if (empty($key)) {
            return $default;
        }
        if (!isset($woo_plugin_settings[$key]) || empty($woo_plugin_settings[$key])) {
            return $default;
        }
        return $woo_plugin_settings[$key];
    }
}

if (!function_exists('get_woo_default_massages')) {
    function get_woo_default_massages() {
        $default_massages = array(
            'email_placeholder_text'    => __('Enter your email', 'woocommerce-product-stock-alert'),
            'alert_text'                => __('Receive in-stock notifications for this.', 'woocommerce-product-stock-alert'),
            'button_text'               => __('Notify me', 'woocommerce-product-stock-alert'),
            'unsubscribe_button_text'   => __('Unsubscribe', 'woocommerce-product-stock-alert'),
            'alert_success'             => __('Thank you for expressing interest in %product_title%. We will notify you via email once it is back in stock.', 'woocommerce-product-stock-alert'),
            'alert_email_exist'         => __('%customer_email% is already registered for %product_title%. Please attempt a different email address.', 'woocommerce-product-stock-alert'),
            'valid_email'               => __('Please enter a valid email ID and try again.', 'woocommerce-product-stock-alert'),
            'alert_unsubscribe_message' => __('%customer_email% is successfully unregistered.', 'woocommerce-product-stock-alert'),
            'shown_interest_text'       => __('Product in demand: %no_of_subscribed% waiting.', 'woocommerce-product-stock-alert'),
            'ban_email_domain_text'     => __('This email domain is ban in our site, kindly use another email domain.', 'woocommerce-product-stock-alert'),
            'ban_email_address_text'    => __('This email address is ban in our site, kindly use another email address.', 'woocommerce-product-stock-alert'),
            'double_opt_in_success'     => __('Kindly check your inbox to confirm the subscription.', 'woocommerce-product-stock-alert'),
        );

        return $default_massages;
    }
}

if (!function_exists('get_woo_form_settings_array')) {
    function get_woo_form_settings_array() {
        $default_massages = get_woo_default_massages();
        
        $settings = array(
            'email_placeholder_text' => get_woo_product_alert_plugin_settings('email_placeholder_text', $default_massages['email_placeholder_text']),
            'alert_text' => get_woo_product_alert_plugin_settings('alert_text', $default_massages['alert_text']),
            'alert_text_color' => get_woo_product_alert_plugin_settings('alert_text_color', ''),
            'button_text' => get_woo_product_alert_plugin_settings('button_text', $default_massages['button_text']),
            'unsubscribe_button_text' => get_woo_product_alert_plugin_settings('unsubscribe_button_text', $default_massages['unsubscribe_button_text']),
            'button_background_color' => get_woo_product_alert_plugin_settings('button_background_color', ''),
            'button_border_color' => get_woo_product_alert_plugin_settings('button_border_color', ''),
            'button_text_color' => get_woo_product_alert_plugin_settings('button_text_color', ''),
            'button_background_color_onhover' => get_woo_product_alert_plugin_settings('button_background_color_onhover', ''),
            'button_text_color_onhover' => get_woo_product_alert_plugin_settings('button_text_color_onhover', ''),
            'button_border_color_onhover' => get_woo_product_alert_plugin_settings('button_border_color_onhover', ''),
            'alert_success' => get_woo_product_alert_plugin_settings('alert_success', $default_massages['alert_success']),
            'alert_email_exist' => get_woo_product_alert_plugin_settings('alert_email_exist', $default_massages['alert_email_exist']),
            'valid_email' => get_woo_product_alert_plugin_settings('valid_email', $default_massages['valid_email']),
            'ban_email_domain_text' => get_woo_product_alert_plugin_settings('ban_email_domain_text', $default_massages['ban_email_domain_text']),
            'ban_email_address_text' => get_woo_product_alert_plugin_settings('ban_email_address_text', $default_massages['ban_email_domain_text']),
            'double_opt_in_success' => get_woo_product_alert_plugin_settings('double_opt_in_success', $default_massages['double_opt_in_success']),
            'alert_unsubscribe_message' => get_woo_product_alert_plugin_settings('alert_unsubscribe_message', $default_massages['alert_unsubscribe_message']),
            'shown_interest_text' => get_woo_product_alert_plugin_settings('shown_interest_text', $default_massages['shown_interest_text']),
            'button_font_size' => get_woo_product_alert_plugin_settings('button_font_size', ''),
            'button_border_size' => get_woo_product_alert_plugin_settings('button_border_size', ''),
            'button_border_redious' => get_woo_product_alert_plugin_settings('button_border_radious', ''),
        );
        return $settings;
    }
}

if (!function_exists('get_woo_product_alert_old_plugin_settings')) {
    function get_woo_product_alert_old_plugin_settings($key = '', $default = false) {
        $woo_old_plugin_settings = array();
        $woo_old_plugin_settings = get_option('dc_woo_product_stock_alert_general_settings_name');

        if (empty($key)) {
            return $default;
        }
        if (!isset($woo_old_plugin_settings[$key]) || empty($woo_old_plugin_settings[$key])) {
            return $default;
        }
        return $woo_old_plugin_settings[$key];
    }
}

if (!function_exists('save_woo_product_alert_settings')) {
    function save_woo_product_alert_settings($key, $option_val) {
        update_option( $key, $option_val );
    }
}

if (!function_exists('update_subscriber')) {
    function update_subscriber($stockalert_id, $status) {
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
    function update_product_subscriber_count($product_id ) {
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
        $addtional_email = '';
        
        if (get_woo_product_alert_plugin_settings('additional_alert_email')) {
            $addtional_email = get_woo_product_alert_plugin_settings('additional_alert_email');
        }

        if (function_exists('get_mvx_product_vendors')) {
            $vendor = get_mvx_product_vendors($product_id);
            if ($vendor && apply_filters( 'woo_product_stock_alert_add_vendor', true)) {
                $addtional_email .= ','. sanitize_email($vendor->user_data->user_email);  
            }
        }
        //admin email or vendor email
        if (!empty($addtional_email))
        $admin_mail->trigger($addtional_email, $product_id, $customer_email);

        //customer email
        $cust_mail->trigger($customer_email, $product_id);
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

    function customer_stock_alert_insert($product_id, $customer_email) {
        if (empty($product_id) && empty($customer_email)) return;
        $do_complete_additional_task = apply_filters( 'woo_product_stock_alert_do_complete_additional_task', false );
        $is_accept_email_address = apply_filters( 'woo_stock_alert_is_accept_email_address', false );
        
        if (is_already_subscribed($customer_email, $product_id)) {
            return $status = '/*?%already_registered%?*/';
        } else if ($do_complete_additional_task) {
            return $status = apply_filters( 'woo_product_stock_alert_new_subscriber_added', $status, $customer_email, $product_id );
        } else if ($is_accept_email_address) {
            return $status = apply_filters( 'woo_product_stock_alert_accept_email', $status, $customer_email, $product_id );
        } else {
            insert_subscriber($customer_email, $product_id);
            insert_subscriber_email_trigger($product_id, $customer_email);
            return true;
        }
    }
}

if (!function_exists('customer_stock_alert_unsubscribe')) {
    function customer_stock_alert_unsubscribe($product_id, $customer_email) {
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

if (!function_exists('woo_is_product_outofstock')) {
    function woo_is_product_outofstock($product_id, $type = '') {
        $is_outof_stock = false;
        if (!$product_id) return $is_outof_stock;
        
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

        $is_enable_backorders = get_woo_product_alert_plugin_settings('is_enable_backorders');
        if ($manage_stock) {
            if ($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount')) {
                $is_outof_stock = true;
            } elseif ($stock_quantity <= 0) {
                $is_outof_stock = true;
            }
        } else {
            if ($stock_status == 'onbackorder' && $is_enable_backorders) {
                $is_outof_stock = true;
            } elseif ($stock_status == 'outofstock') {
                $is_outof_stock = true;
            }
        }
        return $is_outof_stock;
    }
}

if(!function_exists('is_activate_double_opt_in')) {
    function is_activate_double_opt_in() {
        $woo_plugin_settings = array();
        $woo_plugin_settings = get_option('woo_stock_alert_general_tab_settings', array());
        if (!isset($woo_plugin_settings['is_double_optin']) || empty($woo_plugin_settings['is_double_optin'])) {
            return false;
        }
        return $woo_plugin_settings['is_double_optin'];
    }
}

if(!function_exists('woo_stock_product_data')) {
    function woo_stock_product_data($product_id) {
        $product_data = array();
        $parent_product_id = wp_get_post_parent_id($product_id);
        if( $parent_product_id ) {
            $product_obj = wc_get_product( $parent_product_id );
            $parent_id = $parent_product_id ? $parent_product_id : 0;
            $product_data['link'] = $product_obj->get_permalink();
            $product_data['name'] = $product_obj && $product_obj->get_formatted_name() ? $product_obj->get_formatted_name() : '';
            $product_data['price'] = $product_obj && $product_obj->get_price_html() ? $product_obj->get_price_html() : '';
        } else {
            $product_obj = wc_get_product( $product_id );
            $product_data['link'] = $product_obj->get_permalink();
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
    function get_product_subscribers_array($args = array()) {
        $all_product_ids = $get_subscribed_user = array();
        $default_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $args = wp_parse_args($args, $default_args);
        $products = get_posts($args);
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

if (!function_exists('woo_stockalert_admin_tabs')) {
    function woo_stockalert_admin_tabs(){
        $stock_alert_settings_page_endpoint = apply_filters('woo_stockalert_endpoint_fields_before_value', array(
            'general' => array(
                'tablabel'        => __('General', 'woocommerce-product-stock-alert'),
                'apiurl'          => 'save_stockalert',
                'description'     => __('Configure basic product alert settings. ', 'woocommerce-product-stock-alert'),
                'icon'            => 'icon-general',
                'submenu'         => 'settings',
                'modulename'      => [
                    [
                        'key'       => 'is_double_optin',
                        'label'     => __("Subscriber Double Opt-in", 'woocommerce-product-stock-alert'),
                        'class'     => 'woo-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options' => array(
                            array(
                                'key'   => "is_double_optin",
                                'label' => apply_filters('allow_store_inventory_double_optin', __('Upgrade to <a href="' . WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL . '" target="_blank"><span class="pro-strong">Pro</span></a> to enable Double Opt-in flow for subscription confirmation.', 'woocommerce-product-stock-alert')),
                                'value' => "is_double_optin"
                            ),
                        ),
                        'props'     => array(
                            'pro_inactive'  => apply_filters('is_stock_alert_pro_inactive', true)
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'double_opt_in_success',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Default: Kindly check your inbox to confirm the subscription.', 'woocommerce-product-stock-alert-pro'),
                        'label'     => __('Double Opt-In Success Message', 'woocommerce-product-stock-alert-pro'),
                        'depend_checkbox'   => 'is_double_optin',
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_enable_backorders',
                        'label'     => __("Allow Subscriptions with Active Backorders", 'woocommerce-product-stock-alert'),
                        'class'     => 'woo-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "is_enable_backorders",
                                'label' => __('Enabling this setting allows users to subscribe to out-of-stock products, even when the backorder option is enabled.', 'woocommerce-product-stock-alert'),
                                'value' => "is_enable_backorders"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_enable_no_interest',
                        'label'     => __("Display Subscriber Count for Out of Stock Items", 'woocommerce-product-stock-alert'),
                        'class'     => 'woo-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "is_enable_no_interest",
                                'label' => __('Enabling this setting shows the subscriber count on the single product page.', 'woocommerce-product-stock-alert'),
                                'value' => "is_enable_no_interest"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'shown_interest_text',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'depend_checkbox'   => 'is_enable_no_interest',
                        'label'     => __('Subscriber Count Notification Message', 'woocommerce-product-stock-alert'),
                        'desc'      => __('Personalize the notification text to let users know about the quantity of subscribers for out-of-stock item. Note: Use %no_of_subscribed% as number of interest/subscribed persons.', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_recaptcha_enable',
                        'label'     => __("Enable  reCAPTCHA", 'woocommerce-product-stock-alert-pro'),
                        'class'     => 'woo-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "is_recaptcha_enable",
                                'label' => apply_filters('allow_store_inventory_recaptcha', __('Upgrade to <a href="' . WOO_PRODUCT_STOCK_ALERT_PRO_SHOP_URL . '" target="_blank"><span class="pro-strong">Pro</span></a> for unlocking reCAPTCHA for out-of-stock form subscriptions.', 'woocommerce-product-stock-alert-pro')),
                                'value' => "is_recaptcha_enable"
                            ),
                        ),
                        'props'     => array(
                            'pro_inactive'  => apply_filters('is_stock_alert_pro_inactive', true)
                        ),
                        'database_value' => array(),
                    ],
                    [ 
                        'key'       => 'v3_site_key',
                        'type'      => 'text',
                        'depend_checkbox'    => 'is_recaptcha_enable',
                        'label'     => __('Site Key', 'woocommerce-product-stock-alert-pro'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'v3_secret_key',
                        'type'      => 'text',
                        'depend_checkbox'    => 'is_recaptcha_enable',
                        'label'     => __('Secret Key', 'woocommerce-product-stock-alert-pro'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'additional_alert_email',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Set the email address to receive notifications when a user subscribes to an out-of-stock product. You can add multiple comma-separated emails.<br/> Default: The admin\'s email is set as the receiver. Exclude the admin\'s email from the list to exclude admin from receiving these notifications.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Recipient Email for New Subscriber', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                ]
            ),
            'form_customization' => array(
                'tablabel'        => __('Form Customization', 'woocommerce-product-stock-alert'),
                'apiurl'          => 'save_stockalert',
                'description'     => __('Configure form settings.', 'woocommerce-product-stock-alert'),
                'icon'            => 'icon-form-customization',
                'submenu'         => 'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'separator_content',
                        'type'      => 'heading',
                        'label'     => __('no_label', 'woocommerce-product-stock-alert'),
                        'blocktext' => __("Text Customization", 'woocommerce-product-stock-alert'),
                    ],
                    [
                        'key'       => 'email_placeholder_text',
                        'type'      => 'text',
                        'label'     => __('Email Field Placeholder', 'woocommerce-product-stock-alert'),
                        'desc'      => __('It will represent email field placeholder text.', 'woocommerce-product-stock-alert'),
                        'placeholder'   => __('Enter your email', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_text',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Descriptive text guiding users on the purpose of providing their email address above the email entry field.', 'woocommerce-product-stock-alert'),
                        'placeholder'   => __('Receive in-stock notifications for this product.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Subscription Purpose Description', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'button_text',
                        'type'      => 'text',
                        'label'     => __('Subscribe Button', 'woocommerce-product-stock-alert'),
                        'desc'      => __('Modify the subscribe button text. By default we display Notify Me.', 'woocommerce-product-stock-alert'),
                        'placeholder'   => __('Notify Me', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'unsubscribe_button_text',
                        'type'      => 'text',
                        'label'     => __('Unsubscribe Button', 'woocommerce-product-stock-alert'),
                        'desc'      => __('Modify the un-subscribe button text. By default we display Unsubscribe.', 'woocommerce-product-stock-alert'),
                        'placeholder'   => __('Unsubscribe', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'heading',
                        'label'     => __('no_label', 'woocommerce-product-stock-alert'),
                        'blocktext' => __("Alert Box Customizer", 'woocommerce-product-stock-alert'),
                    ],
                    [
                        'key'       => 'custom_example_form',
                        'type'      => 'example_form',
                        'class'     => 'woo-setting-own-class',
                        'label'     => __('Sample Form', 'woocommerce-product-stock-alert')
                    ],
                    [
                        'key'       => 'button_color_section',
                        'type'      => 'form_customize_table',
                        'label'     => __('Customization Settings', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                ]
            ),
            'form_submission' => array(
                'tablabel'        => __('Submission Messages', 'woocommerce-product-stock-alert'),
                'apiurl'          => 'save_stockalert',
                'description'     => __('Customize message that appears after user submits the form.', 'woocommerce-product-stock-alert'),
                'icon'            => 'icon-form-submission',
                'submenu'         => 'settings',
                'modulename'      => [
                    [
                        'key'       => 'alert_success',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Tip: Utilize %product_title% for dynamic product titles and %customer_email% for personalized customer email addresses in your messages.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Successful Form Submission', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_email_exist',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Tip: Enhance personalization by incorporating %product_title% for dynamic product titles and %customer_email% for individual customer emails.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Repeated Subscription Alert', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'valid_email',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Personalize the message shown to users when they try to subscribe with an invalid email address.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Email Validation Error', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'alert_unsubscribe_message',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Modify the text that confirms user that they have successful unsubscribe.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Unsubscribe Confirmation', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                ]
            ),
            'email' => array(
                'tablabel'        => __('Email Blocker', 'woocommerce-product-stock-alert'),
                'apiurl'          => 'save_stockalert',
                'description'     => __('Ban Email Control Center.', 'woocommerce-product-stock-alert'),
                'icon'            => 'icon-email-setting',
                'submenu'         => 'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'ban_email_domains',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Specify email domains that are restricted from subscribing. You can add multiple commma seperated emails.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Blocked Email Domains', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_domain_text',
                        'type'      => 'textarea',
                        'label'     => __('Blocked Domain Alert Message', 'woocommerce-product-stock-alert'),
                        'desc'      => __(' Create an alert message for users attempting to subscribe from blocked domains.', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_addresses',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Specify email addresses that are restricted from subscribing. You can add multiple commma seperated emails.', 'woocommerce-product-stock-alert'),
                        'label'     => __('Blocked Email Addresses', 'woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'ban_email_address_text',
                        'type'      => 'textarea',
                        'label'     => __('Blocked Email Alert Message', 'woocommerce-product-stock-alert'),
                        'desc'      => __('Create an alert message for users attempting to subscribe from blocked Email ID.','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                ]
            ),
            'mailchimp' => array(
                'tablabel'        => __('Mailchimp Integration', 'woocommerce-product-stock-alert'),
                'apiurl'          => 'save_stockalert',
                'description'     => __('Configure mailChimp settings. ', 'woocommerce-product-stock-alert'),
                'icon'            => 'icon-mailchimp-setting',
                'submenu'         => 'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'is_mailchimp_enable',
                        'label'     => __( "Enable Mailchimp", 'woocommerce-product-stock-alert' ),
                        'class'     => 'woo-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
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
                        'label'     => __('Mailchimp API', 'woocommerce-product-stock-alert'),
                        'desc'      => __('','woocommerce-product-stock-alert'),
                        'database_value' => '',
                    ],
                    [
                        'key'       => 'get_mailchimp_list_button',
                        'depend_checkbox'   => 'is_mailchimp_enable',
                        'label'     => 'no_label',
                        'type'      => 'button',
                        'api_link'  => 'woo_stockalert_pro/v1/get_mailchimp_list',
                        'database_value' => array(),
                    ],
                    [
                        'key'       => 'selected_mailchimp_list',
                        'type'      => 'mailchimp_select',
                        'depend_checkbox'   => 'is_mailchimp_enable',
                        'label'     => __('Mailchimp List', 'woocommerce-product-stock-alert'),
                        'desc'      => __('Select a Mailchimp list.', 'woocommerce-product-stock-alert'),
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
                        $change_settings_key = str_replace("-", "_", $settings_key);
                        $option_name = 'woo_stock_alert_'.$change_settings_key.'_tab_settings';
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

        $woo_stock_alert_backend_tab_list = apply_filters('woo_stock_alert_tab_list', array(
            'stock_alert-settings' => $stock_alert_settings_page_endpoint,
        ));
        
        return $woo_stock_alert_backend_tab_list;
    }
}
