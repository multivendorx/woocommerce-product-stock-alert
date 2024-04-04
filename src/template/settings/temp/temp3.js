import { __ } from '@wordpress/i18n';

export default {
    id: "temp3",
    priority: 6,
    name: __("TEMP3", "woocommerce-stock-manager"),
    desc: __("Ban Email Control Center.", "woocommerce-stock-manager"),
    icon: "font-mail",
    proDependent: true,
    submitUrl: "save-stockmanager",
    modal: [
        {
            key: "ban_email_domains",
            type: "textarea",
            class: "woo-setting-wpeditor-class",
            desc: __("Specify email domains that are restricted from subscribing. You can add multiple commma seperated emails.", "woocommerce-stock-manager"),
            label: __("Blocked Email Domains", "woocommerce-stock-manager"),
        },
        {
            key: "ban_email_domain_text",
            type: "textarea",
            label: __("Blocked Domain Alert Message", "woocommerce-stock-manager"),
            desc: __(" Create an alert message for users attempting to subscribe from blocked domains.", "woocommerce-stock-manager"),
        },
        {
            key: "ban_email_addresses",
            type: "textarea",
            class: "woo-setting-wpeditor-class",
            desc: __("Specify email addresses that are restricted from subscribing. You can add multiple commma seperated emails.", "woocommerce-stock-manager"),
            label: __("Blocked Email Addresses", "woocommerce-stock-manager"),
        },
        {
            key: "ban_email_address_text",
            type: "textarea",
            label: __("Blocked Email Alert Message", "woocommerce-stock-manager"),
            desc: __("Create an alert message for users attempting to subscribe from blocked Email ID.", "woocommerce-stock-manager"),
        },
    ]
};