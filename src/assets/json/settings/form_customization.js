import { __ } from '@wordpress/i18n';

export default {
    tablabel: __("Form Customization", "woocommerce-stock-manager"),
    description: __("Configure form settings.", "woocommerce-stock-manager"),
    icon: "icon-form-customization",
    priority: 2,
    apiurl: "save-stockmanager",
    module: [
        {
            key: "separator_content",
            type: "heading",
            label: __("no_label", "woocommerce-stock-manager"),
            blocktext: __("Text Customization", "woocommerce-stock-manager"),
        },
        {
            key: "email_placeholder_text",
            type: "text",
            label: __("Email Field Placeholder", "woocommerce-stock-manager"),
            desc: __("It will represent email field placeholder text.", "woocommerce-stock-manager"),
            placeholder: __("Enter your email", "woocommerce-stock-manager"),
        },
        {
            key: "alert_text",
            type: "textarea",
            class: "woo-setting-wpeditor-class",
            desc: __("Descriptive text guiding users on the purpose of providing their email address above the email entry field.", "woocommerce-stock-manager"),
            placeholder: __("Receive in-stock notifications for this product.", "woocommerce-stock-manager"),
            label: __("Subscription Purpose Description", "woocommerce-stock-manager"),
        },
        {
            key: "button_text",
            type: "text",
            label: __("Subscribe Button", "woocommerce-stock-manager"),
            desc: __("Modify the subscribe button text. By default we display Notify Me.", "woocommerce-stock-manager"),
            placeholder: __("Notify Me", "woocommerce-stock-manager"),
        },
        {
            key: "unsubscribe_button_text",
            type: "text",
            label: __("Unsubscribe Button", "woocommerce-stock-manager"),
            desc: __("Modify the un-subscribe button text. By default we display Unsubscribe.", "woocommerce-stock-manager"),
            placeholder: __("Unsubscribe", "woocommerce-stock-manager"),
        },
        {
            key: "separator_content",
            type: "heading",
            label: __("no_label", "woocommerce-stock-manager"),
            blocktext: __("Alert Box Customizer", "woocommerce-stock-manager"),
        },
        {
            key: "custom_example_form",
            type: "example_form",
            class: "woo-setting-own-class",
            label: __("Sample Form", "woocommerce-stock-manager")
        },
        {
            key: "button_color_section",
            type: "form_customize_table",
            label: __("Customization Settings", "woocommerce-stock-manager"),
        },
    ]
};
