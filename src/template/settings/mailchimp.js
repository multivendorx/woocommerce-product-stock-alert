import { __ } from '@wordpress/i18n';

export default {
    id: "mailchimp",
    priority: 5,
    name: __("Mailchimp Integration", "woocommerce-stock-manager"),
    desc: __("Integrate Mailchimp for email marketing.", "woocommerce-stock-manager"),
    icon: "font-mailchimp",
    proDependent: true,
    submitUrl: "save-stockmanager",
    modal: [
        {
            key: "is_mailchimp_enable",
            type: "checkbox",
            class: "woo-toggle-checkbox",
            label: __("Enable Mailchimp", "woocommerce-stock-manager"),
            desc: __("Get your MailChimp API from your MailChimp <a href='https://us20.admin.mailchimp.com/account/api/manage/#create'>account</a>. For further help, please check this doc.", "woocommerce-stock-manager"),
            options: [
                {
                    key: "is_mailchimp_enable",
                    value: "is_mailchimp_enable"
                }
            ],
            proSetting: true,
        },
        {
            // Spacial input field
            key: "mailchimp_api",
            selectKey: 'selected_mailchimp_list',
            optionKey: 'mailchimp_list_options',
            apiLink: "get-mailchimp-list",
            type: "api_connect",
            label: __("Mailchimp API", "woocommerce-stock-manager"),
            dependent: {
                key: "is_mailchimp_enable",
                set: true,
            },
            proSetting: true,
        },
    ]
};