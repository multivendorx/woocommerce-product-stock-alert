import { __ } from '@wordpress/i18n';
export default {
    tablabel: __("Mailchimp Integration", "woocommerce-stock-manager"),
    description: __("Configure mailChimp settings. ", "woocommerce-stock-manager"),
    apiurl: "save-stockmanager",
    priority: 5,
    icon: "icon-mailchimp-setting",
    module: [
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
            key: "mailchimp_api",
            type: "text_api",
            label: __("Mailchimp API", "woocommerce-stock-manager"),
            desc: "",
            depend_checkbox: "is_mailchimp_enable",
        },
        {
            key: "get_mailchimp_list_button",
            type: "button",
            label: "no_label",
            api_link: "stockmanager/v1/get-mailchimp-list",
            depend_checkbox: "is_mailchimp_enable",
        },
        {
            key: "selected_mailchimp_list",
            type: "mailchimp_select",
            label: __("Mailchimp List", "woocommerce-stock-manager"),
            desc: __("Select a Mailchimp list.", "woocommerce-stock-manager"),
            options: [],
            depend_checkbox: "is_mailchimp_enable",
        },
    ]
};
