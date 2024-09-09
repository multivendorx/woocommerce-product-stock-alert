import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

registerBlockType('stock-manager-form-block', {
    title: 'Stock Manager Form',
    description: 'A block to display the Stock Manager form.',
    category: 'widgets',
    icon: 'clipboard',
    supports: {
        html: false,
    },

    edit: () => {
        return (
            <div {...useBlockProps()}>
                <p>Stock Manager Form Block</p>
            </div>
        );
    },

    save: () => {
        return (
            <div {...useBlockProps.save()}>
                <p>[display_stock_manager_form]</p>
            </div>
        );
    },
});
