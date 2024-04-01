import { __ } from '@wordpress/i18n';

export default {
    id: 'general',
    priority: 1,
    name: __("General", "woocommerce-stock-manager"),
    desc: __("Configure basic product manager settings.", "woocommerce-stock-manager"),
    icon: 'icon-general',
    submitUrl: 'save-stockmanager',
    modal: [
        {
            key: 'is_double_optin',
            type: 'checkbox',
            class: 'woo-toggle-checkbox',
            label: __("Subscriber Double Opt-in", "woocommerce-stock-manager"),
            options: [
                {
                    key: "is_double_optin",
                    label: appLocalizer.pro_active == 'free' ? appLocalizer.is_double_optin_free : appLocalizer.is_double_optin_pro ,
                    value: "is_double_optin"
                }
            ]
        },
        {
            key: 'double_opt_in_success',
            type: 'textarea',
            class: 'woo-setting-wpeditor-class',
            desc: __("Default: Kindly check your inbox to confirm the subscription.", "woocommerce-stock-manager-pro"),
            label: __("Double Opt-In Success Message", "woocommerce-stock-manager-pro"),
            depend_checkbox: 'is_double_optin',
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_enable_backorders',
            type: 'checkbox',
            label: __("Allow Subscriptions with Active Backorders", "woocommerce-stock-manager"),
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
            label: __("Display Subscriber Count for Out of Stock Items", "woocommerce-stock-manager"),
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
            label: __("Subscriber Count Notification Message", "woocommerce-stock-manager"),
            desc: __("Personalize the notification text to let users know about the quantity of subscribers for out-of-stock item. Note: Use %no_of_subscribed% as number of interest/subscribed persons.", "woocommerce-stock-manager"),
            depend_checkbox: 'is_enable_no_interest',
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: 'is_recaptcha_enable',
            type: 'checkbox',
            label: __("Enable  reCAPTCHA", "woocommerce-stock-manager-pro"),
            class: 'woo-toggle-checkbox',
            options: [
                {
                    key: "is_recaptcha_enable",
                    label: appLocalizer.pro_active == 'free' ? appLocalizer.is_recaptcha_enable_free : appLocalizer.is_recaptcha_enable_pro ,
                    value: "is_recaptcha_enable"
                }
            ]
        },
        {
            key: 'v3_site_key',
            type: 'text',
            label: __("Site Key", "woocommerce-stock-manager-pro"),
            depend_checkbox: 'is_recaptcha_enable',
        },
        {
            key: 'v3_secret_key',
            type: 'text',
            label: __("Secret Key", "woocommerce-stock-manager-pro"),
            depend_checkbox: 'is_recaptcha_enable',
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
            label: __("Recipient Email for New Subscriber", "woocommerce-stock-manager"),
        },
    ]
};
