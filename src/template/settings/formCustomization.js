import { __ } from '@wordpress/i18n';
export default {
    id: 'form_customization',
    priority: 2,
    name: __("Form Customization", "woocommerce-stock-manager"),
    desc: __("Adjust the appearance and layout of stock alert form.", "woocommerce-stock-manager"),
    icon: "font-file-submission",
    submitUrl: "save-stockmanager",
    modal: [
        {
            key: "separator_content",
            type: "heading",
            label: __("no_label", "woocommerce-stock-manager"),
            blocktext: __("Text Customization", "woocommerce-stock-manager"),
        },
        {
            key: "unsubscribe_button_text",
            type: "text",
            label: __("Unsubscribe button", "woocommerce-stock-manager"),
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
            key: "subscribe_form",
            type: "form_customizer",
            label: __("Customize form", "woocommerce-stock-manager")
        }
    ]
};
