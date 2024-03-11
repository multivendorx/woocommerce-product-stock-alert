<?php

namespace StockManager;

class AdminSettingTab {
    static function get() {
        return [
            'general' => [
                'tablabel'      => __('General', 'woocommerce-stock-manager'),
                'description'   => __('Configure basic product manager settings. ', 'woocommerce-stock-manager'),
                'icon'          => 'icon-general',
                'apiurl'        => 'save-stockmanager',
                'module'        => [
                    [
                        'key'       => 'is_double_optin',
                        'type'      => 'checkbox',
                        'class'     => 'woo-toggle-checkbox',
                        'label'     => __("Subscriber Double Opt-in", 'woocommerce-stock-manager'),
                        'options'   => [
                            [
                                'key'   => "is_double_optin",
                                'label' => apply_filters('allow_store_inventory_double_optin', 'Upgrade to <a href="' . WOO_STOCK_MANAGER_PRO_SHOP_URL . '" target="_blank"><span class="pro-strong">Pro</span></a> to enable Double Opt-in flow for subscription confirmation.', 'woocommerce-stock-manager'),
                                'value' => "is_double_optin"
                            ]
                        ]
                    ],
                    [
                        'key'       => 'double_opt_in_success',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Default: Kindly check your inbox to confirm the subscription.', 'woocommerce-stock-manager-pro'),
                        'label'     => __('Double Opt-In Success Message', 'woocommerce-stock-manager-pro'),
                        'depend_checkbox' => 'is_double_optin',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_enable_backorders',
                        'type'      => 'checkbox',
                        'label'     => __("Allow Subscriptions with Active Backorders", 'woocommerce-stock-manager'),
                        'class'     => 'woo-toggle-checkbox',
                        'options'   => [
                            [
                                'key'   => "is_enable_backorders",
                                'label' => __('Enabling this setting allows users to subscribe to out-of-stock products, even when the backorder option is enabled.', 'woocommerce-stock-manager'),
                                'value' => "is_enable_backorders"
                            ]
                        ]
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_enable_no_interest',
                        'type'      => 'checkbox',
                        'label'     => __("Display Subscriber Count for Out of Stock Items", 'woocommerce-stock-manager'),
                        'class'     => 'woo-toggle-checkbox',
                        'options'   => [
                            [
                                'key'   => "is_enable_no_interest",
                                'label' => __('Enabling this setting shows the subscriber count on the single product page.', 'woocommerce-stock-manager'),
                                'value' => "is_enable_no_interest"
                            ]
                        ]
                    ],
                    [
                        'key'       => 'shown_interest_text',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'label'     => __('Subscriber Count Notification Message', 'woocommerce-stock-manager'),
                        'desc'      => __('Personalize the notification text to let users know about the quantity of subscribers for out-of-stock item. Note: Use %no_of_subscribed% as number of interest/subscribed persons.', 'woocommerce-stock-manager'),
                        'depend_checkbox' => 'is_enable_no_interest',
                    ],
                    [
                        'key'       => 'separator_content',
                        'type'      => 'section',
                        'label'     => "",
                    ],
                    [
                        'key'       => 'is_recaptcha_enable',
                        'type'      => 'checkbox',
                        'label'     => __("Enable  reCAPTCHA", 'woocommerce-stock-manager-pro'),
                        'class'     => 'woo-toggle-checkbox',
                        'options'   => [
                            [
                                'key'   => "is_recaptcha_enable",
                                'label' => apply_filters('allow_store_inventory_recaptcha','Upgrade to <a href="' . WOO_STOCK_MANAGER_PRO_SHOP_URL . '" target="_blank"><span class="pro-strong">Pro</span></a> for unlocking reCAPTCHA for out-of-stock form subscriptions.'),
                                'value' => "is_recaptcha_enable"
                            ]
                        ]
                    ],
                    [
                        'key'       => 'v3_site_key',
                        'type'      => 'text',
                        'label'     => __('Site Key', 'woocommerce-stock-manager-pro'),
                        'depend_checkbox' => 'is_recaptcha_enable',
                    ],
                    [
                        'key'       => 'v3_secret_key',
                        'type'      => 'text',
                        'label'     => __('Secret Key', 'woocommerce-stock-manager-pro'),
                        'depend_checkbox' => 'is_recaptcha_enable',
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
                        'desc'      => __('Set the email address to receive notifications when a user subscribes to an out-of-stock product. You can add multiple comma-separated emails.<br/> Default: The admin\'s email is set as the receiver. Exclude the admin\'s email from the list to exclude admin from receiving these notifications.', 'woocommerce-stock-manager'),
                        'label'     => __('Recipient Email for New Subscriber', 'woocommerce-stock-manager'),
                    ],
                ]
            ],
            'form_customization' => [
                'tablabel'      => __('Form Customization', 'woocommerce-stock-manager'),
                'description'   => __('Configure form settings.', 'woocommerce-stock-manager'),
                'icon'          => 'icon-form-customization',
                'apiurl'        => 'save-stockmanager',
                'module'        => [
                    [
                        'key'       => 'separator_content',
                        'type'      => 'heading',
                        'label'     => __('no_label', 'woocommerce-stock-manager'),
                        'blocktext' => __("Text Customization", 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'email_placeholder_text',
                        'type'          => 'text',
                        'label'         => __('Email Field Placeholder', 'woocommerce-stock-manager'),
                        'desc'          => __('It will represent email field placeholder text.', 'woocommerce-stock-manager'),
                        'placeholder'   => __('Enter your email', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'alert_text',
                        'type'          => 'textarea',
                        'class'         => 'woo-setting-wpeditor-class',
                        'desc'          => __('Descriptive text guiding users on the purpose of providing their email address above the email entry field.', 'woocommerce-stock-manager'),
                        'placeholder'   => __('Receive in-stock notifications for this product.', 'woocommerce-stock-manager'),
                        'label'         => __('Subscription Purpose Description', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'button_text',
                        'type'          => 'text',
                        'label'         => __('Subscribe Button', 'woocommerce-stock-manager'),
                        'desc'          => __('Modify the subscribe button text. By default we display Notify Me.', 'woocommerce-stock-manager'),
                        'placeholder'   => __('Notify Me', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'unsubscribe_button_text',
                        'type'          => 'text',
                        'label'         => __('Unsubscribe Button', 'woocommerce-stock-manager'),
                        'desc'          => __('Modify the un-subscribe button text. By default we display Unsubscribe.', 'woocommerce-stock-manager'),
                        'placeholder'   => __('Unsubscribe', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'separator_content',
                        'type'          => 'heading',
                        'label'         => __('no_label', 'woocommerce-stock-manager'),
                        'blocktext'     => __("Alert Box Customizer", 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'           => 'custom_example_form',
                        'type'          => 'example_form',
                        'class'         => 'woo-setting-own-class',
                        'label'         => __('Sample Form', 'woocommerce-stock-manager')
                    ],
                    [
                        'key'           => 'button_color_section',
                        'type'          => 'form_customize_table',
                        'label'         => __('Customization Settings', 'woocommerce-stock-manager'),
                    ],
                ]
            ],
            'form_submission' => [
                'tablabel'      => __('Submission Messages', 'woocommerce-stock-manager'),
                'description'   => __('Customize message that appears after user submits the form.', 'woocommerce-stock-manager'),
                'icon'          => 'icon-form-submission',
                'apiurl'        => 'save-stockmanager',
                'module'        => [
                    [
                        'key'       => 'alert_success',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
						// Translators: This message display dynamic product title and emails.
                        'desc'      => __('Tip: Utilize %product_title% for dynamic product titles and %customer_email% for personalized customer email addresses in your messages.', 'woocommerce-stock-manager'),
                        'label'     => __('Successful Form Submission', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'alert_email_exist',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
						// Translators: This message display dynamic product title and emails.
                        'desc'      => __('Tip: Enhance personalization by incorporating %product_title% for dynamic product titles and %customer_email% for individual customer emails.', 'woocommerce-stock-manager'),
                        'label'     => __('Repeated Subscription Alert', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'valid_email',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Personalize the message shown to users when they try to subscribe with an invalid email address.', 'woocommerce-stock-manager'),
                        'label'     => __('Email Validation Error', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'alert_unsubscribe_message',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Modify the text that confirms user that they have successful unsubscribe.', 'woocommerce-stock-manager'),
                        'label'     => __('Unsubscribe Confirmation', 'woocommerce-stock-manager'),
                    ],
                ]
            ],
            'email' => [
                'tablabel'      => __('Email Blocker', 'woocommerce-stock-manager'),
                'apiurl'        => 'save-stockmanager',
                'description'   => __('Ban Email Control Center.', 'woocommerce-stock-manager'),
                'icon'          => 'icon-email-setting',
                'module'        => [
                    [
                        'key'       => 'ban_email_domains',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Specify email domains that are restricted from subscribing. You can add multiple commma seperated emails.', 'woocommerce-stock-manager'),
                        'label'     => __('Blocked Email Domains', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'ban_email_domain_text',
                        'type'      => 'textarea',
                        'label'     => __('Blocked Domain Alert Message', 'woocommerce-stock-manager'),
                        'desc'      => __(' Create an alert message for users attempting to subscribe from blocked domains.', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'ban_email_addresses',
                        'type'      => 'textarea',
                        'class'     => 'woo-setting-wpeditor-class',
                        'desc'      => __('Specify email addresses that are restricted from subscribing. You can add multiple commma seperated emails.', 'woocommerce-stock-manager'),
                        'label'     => __('Blocked Email Addresses', 'woocommerce-stock-manager'),
                    ],
                    [
                        'key'       => 'ban_email_address_text',
                        'type'      => 'textarea',
                        'label'     => __('Blocked Email Alert Message', 'woocommerce-stock-manager'),
                        'desc'      => __('Create an alert message for users attempting to subscribe from blocked Email ID.', 'woocommerce-stock-manager'),
                    ],
                ]
            ],
            'mailchimp' => [
                'tablabel'      => __('Mailchimp Integration', 'woocommerce-stock-manager'),
                'description'   => __('Configure mailChimp settings. ', 'woocommerce-stock-manager'),
                'apiurl'        => 'save-stockmanager',
                'icon'          => 'icon-mailchimp-setting',
                'module'        => [
                    [
                        'key'       => 'is_mailchimp_enable',
                        'type'      => 'checkbox',
                        'class'     => 'woo-toggle-checkbox',
                        'label'     => __("Enable Mailchimp", 'woocommerce-stock-manager'),
                        'options'   => [
                            [
                                'key'   => "is_mailchimp_enable",
                                'label' => __('Enable this to activate Mailchimp.', 'woocommerce-stock-manager'),
                                'value' => "is_mailchimp_enable"
                            ]
                        ]
                    ],
                    [
                        'key'       => 'mailchimp_api',
                        'type'      => 'text_api',
                        'label'     => __('Mailchimp API', 'woocommerce-stock-manager'),
                        'desc'      => '',
                        'depend_checkbox' => 'is_mailchimp_enable',
                    ],
                    [
                        'key'       => 'get_mailchimp_list_button',
                        'type'      => 'button',
                        'label'     => 'no_label',
                        'api_link'  => 'stockmanager/v1/get-mailchimp-list',
                        'depend_checkbox' => 'is_mailchimp_enable',
                    ],
                    [
                        'key'       => 'selected_mailchimp_list',
                        'type'      => 'mailchimp_select',
                        'label'     => __('Mailchimp List', 'woocommerce-stock-manager'),
                        'desc'      => __('Select a Mailchimp list.', 'woocommerce-stock-manager'),
                        'options'   => [],
                        'depend_checkbox' => 'is_mailchimp_enable',
                    ],
                ]
            ],
        ];
    }
}
