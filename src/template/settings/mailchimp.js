import { __ } from '@wordpress/i18n';

export default {
    id: "mailchimp",
    priority: 5,
    name: __("Mailchimp Integration", "woocommerce-stock-manager"),
    desc: __("Configure mailChimp settings. ", "woocommerce-stock-manager"),
    icon: "font-mailchimp",
    proDependent: true,
    submitUrl: "save-stockmanager",
    modal: [
        {
            key: "is_mailchimp_enable",
            type: "checkbox",
            class: "woo-toggle-checkbox",
            label: __("Enable Mailchimp", "woocommerce-stock-manager"),
            options: [
                {
                    key: "is_mailchimp_enable",
                    label: __("Enable this to activate Mailchimp.", "woocommerce-stock-manager"),
                    value: "is_mailchimp_enable"
                }
            ]
        },
        {
            // Spacial input field
            key: "mailchimp_api",
            selectKey: 'selected_mailchimp_list',
            optionKey: 'mailchimp_list_options',
            apiLink: "get-mailchimp-list",
            type: "api_connect",
            label: __("Mailchimp API", "woocommerce-stock-manager"),
            desc: "",
            dependent: {
                key: "is_mailchimp_enable",
                set: true,
            }
        },
    ]
};