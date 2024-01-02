<?php
if (!defined('ABSPATH')) exit;

class Woo_Product_Stock_Alert_Restapi {
    function __construct() {
        if (current_user_can('manage_options')) {
            add_action('rest_api_init', array($this, 'stock_alert_rest_routes_react_module'));
        }
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function stock_alert_rest_routes_react_module() {
        register_rest_route('woo_stockalert/v1', '/fetch_admin_tabs', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_fetch_admin_tabs'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/save_stockalert', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'woo_stockalert_save_stockalert'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/get_button_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_get_button_data'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
        register_rest_route('woo_stockalert/v1', '/close_banner', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'woo_stockalert_close_banner'),
            'permission_callback' => array($this, 'stockalert_permission'),
        ] );
    }

    public function stockalert_permission() {
        return true;
    }

    public function woo_stockalert_close_banner() {
        update_option('woocommerce_stock_alert_pro_banner_hide', true);
        return rest_ensure_response(false);
    }
    
    
    public function woo_stockalert_get_button_data() {
        $form_customization_tab_settings = get_option('woo_stock_alert_form_customization_tab_settings');
        $button_data = [
            'alert_text'                        => $form_customization_tab_settings['alert_text'],
            'button_text'                       => $form_customization_tab_settings['button_text'],
            'unsubscribe_button_text'           => $form_customization_tab_settings['unsubscribe_button_text'],
            'alert_text_color'                  => $form_customization_tab_settings['alert_text_color'],
            'button_background_color'           => $form_customization_tab_settings['button_background_color'],
            'button_border_color'               => $form_customization_tab_settings['button_border_color'],
            'button_text_color'                 => $form_customization_tab_settings['button_text_color'],
            'button_background_color_onhover'   => $form_customization_tab_settings['button_background_color_onhover'],
            'button_text_color_onhover'         => $form_customization_tab_settings['button_text_color_onhover'],
            'button_border_color_onhover'       => $form_customization_tab_settings['button_border_color_onhover'],
            'button_font_size'                  => $form_customization_tab_settings['button_font_size'],
            'button_border_size'                => $form_customization_tab_settings['button_border_size'],
            'button_border_redious'             => $form_customization_tab_settings['button_border_redious'],
        ];
        return rest_ensure_response($button_data);
    }
    
    public function woo_stockalert_save_stockalert($request) {
        $all_details = [];
        $modulename = $request->get_param('modulename');
        $modulename = str_replace("-", "_", $modulename);
        $optionname = 'woo_stock_alert_'.$modulename.'_tab_settings';
        $get_managements_data = $request->get_param( 'model' );
        update_option($optionname, $get_managements_data);
        do_action('woo_stock_alert_settings_after_save', $modulename, $get_managements_data);
        $all_details['error'] = __('Settings Saved', 'woocommerce-product-stock-alert');
        return $all_details;
    }
    
    public function woo_stockalert_fetch_admin_tabs() {
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
        
        return rest_ensure_response($woo_stock_alert_backend_tab_list);
    }
}