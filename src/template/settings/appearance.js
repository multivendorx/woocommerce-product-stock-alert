import { __ } from '@wordpress/i18n';

export default {
    id: 'appearance',
    priority: 1,
    name: __("Appearance", "woocommerce-stock-manager"),
    desc: __("Personalize stock alert form behavior.", "woocommerce-stock-manager"),
    icon: 'font-settings',
    submitUrl: 'save-stockmanager',
    modal: [
        {
            key: "subscribe_form",
            type: "form_customizer",
            label: __("Personalize Layout", "woocommerce-stock-manager")
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "unsubscribe_button_text",
            type: "text",
            label: __('"Unsubscribe" Button Caption ', "woocommerce-stock-manager"),
            desc: __("Modify the un-subscribe button text. By default we display \"Unsubscribe\".", "woocommerce-stock-manager"),
            placeholder: __("Unsubscribe", "woocommerce-stock-manager"),
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_enable_backorders',
            type: 'checkbox',
            label: __("Allow Backorder Subscriptions", "woocommerce-stock-manager"),
            class: 'woo-toggle-checkbox',
            options: [
                {
                    key: "is_enable_backorders",
                    label: __("Enabling this setting allows users to subscribe to out-of-stock products, even when the backorder option is enabled.", "woocommerce-stock-manager"),
                    value: "is_enable_backorders"
                }
            ]
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_enable_no_interest',
            type: 'checkbox',
            label: __("Display subscriber count for out of stock", "woocommerce-stock-manager"),
            class: 'woo-toggle-checkbox',
            options: [
                {
                    key: "is_enable_no_interest",
                    label: __("Enabling this setting shows the subscriber count on the single product page.", "woocommerce-stock-manager"),
                    value: "is_enable_no_interest"
                }
            ]
        },
        {
            key: 'shown_interest_text',
            type: 'textarea',
            class: 'woo-setting-wpeditor-class',
            label: __("Subscriber count notification message", "woocommerce-stock-manager"),
            desc: __("Personalize the notification text to let users know about the quantity of subscribers for out-of-stock item. Note: Use %no_of_subscribed% as number of interest/subscribed persons.", "woocommerce-stock-manager"),
            dependent: {
                key: "is_enable_no_interest",
                set: true
            }
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_double_optin',
            type: 'checkbox',
            class: 'woo-toggle-checkbox',
            label: __("Subscriber double opt-in", "woocommerce-stock-manager"),
            options: [
                {
                    key: "is_double_optin",
                    label: ! appLocalizer.pro_active ? appLocalizer.is_double_optin_free : appLocalizer.is_double_optin_pro ,
                    value: "is_double_optin"
                }
            ],
            proSetting: true,
        },
        {
            key: 'double_opt_in_success',
            type: 'textarea',
            class: 'woo-setting-wpeditor-class',
            desc: __("Default: Kindly check your inbox to confirm the subscription.", "woocommerce-stock-manager-pro"),
            label: __("Double opt-in success message", "woocommerce-stock-manager-pro"),
            dependent: {
                key: "is_double_optin",
                set: true,
            },
            proSetting: true,
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_recaptcha_enable',
            type: 'checkbox',
            label: __("Enable  reCaptcha", "woocommerce-stock-manager-pro"),
            class: 'woo-toggle-checkbox',
            options: [
                {
                    key: "is_recaptcha_enable",
                    label: ! appLocalizer.pro_active ? appLocalizer.is_recaptcha_enable_free : appLocalizer.is_recaptcha_enable_pro ,
                    value: "is_recaptcha_enable"
                }
            ],
            proSetting: true,
        },
        {
            key: 'v3_site_key',
            type: 'text',
            label: __("Site Key", "woocommerce-stock-manager-pro"),
            dependent: {
                key: "is_recaptcha_enable",
                set: true,
            }
        },
        {
            key: 'v3_secret_key',
            type: 'text',
            label: __("Secret Key", "woocommerce-stock-manager-pro"),
            dependent: {
                key: "is_recaptcha_enable",
                set: true,
            }
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'additional_alert_email',
            type: 'textarea',
            class: 'woo-setting-wpeditor-class',
            desc: __("Set the email address to receive notifications when a user subscribes to an out-of-stock product. You can add multiple comma-separated emails.<br/> Default: The admin\'s email is set as the receiver. Exclude the admin\'s email from the list to exclude admin from receiving these notifications.", "woocommerce-stock-manager"),
            label: __("Recipient email for new subscriber", "woocommerce-stock-manager"),
        },
    ]
};
