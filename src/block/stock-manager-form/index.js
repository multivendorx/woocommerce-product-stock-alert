import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';  

registerBlockType('stock-manager-form-block', {
    title: __('Stock Manager Form', 'woocommerce-stock-manager'),
    description: __('A block to display the Stock Manager form.', 'woocommerce-stock-manager'),
    category: 'widgets',
    icon: 'clipboard',
    supports: {
        html: false,
    },

    edit: () => {
        return (
            <div {...useBlockProps()}>
                <p>{ __('Stock Manager Form Block', 'woocommerce-stock-manager') }</p>
            </div>
        );
    },

    save: () => {
        return (
            <div {...useBlockProps.save()}>
                <p>{ __('[display_stock_manager_form]', 'woocommerce-stock-manager') }</p>
            </div>
        );
    },
});
