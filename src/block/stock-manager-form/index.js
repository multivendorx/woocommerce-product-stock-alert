import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

registerBlockType('woocommerce-stock-manager/stock-manager-form', {
    title: __('Stock Manager Form', 'woocommerce-stock-manager'),
    description: __('A block to display the Stock Manager form.', 'woocommerce-stock-manager'),
    category: 'widgets',
    icon: 'clipboard',
    supports: {
        html: false,
    },
    attributes: {
        productId: {
            type: 'number',
            default: null,
        },
    },

    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        // Select the product ID from the WooCommerce Single Product Block
        const productId = useSelect((select) => {
            const blocks = select('core/block-editor').getBlocks();
            const singleProductBlock = blocks.find(
                (block) => block.name === 'woocommerce/single-product'
            );
            return singleProductBlock?.attributes?.productId || null;
        }, []);

        // Update the product ID attribute if it changes
        useEffect(() => {
            if (productId && productId !== attributes.productId) {
                setAttributes({ productId });
            }
        }, [productId]);

        return (
            <div {...blockProps}>
                <p>{__('Stock Manager Form Block', 'woocommerce-stock-manager')}</p>
            </div>
        );
    },

    save: ({ attributes }) => {
        const blockProps = useBlockProps.save();

        // Render the shortcode dynamically with the saved productId
        return (
            <div {...blockProps}>
                {attributes.productId ? (
                    <p>[display_stock_manager_form product_id="{attributes.productId}"]</p>
                ) : (
                    <p>[display_stock_manager_form]</p>
                )}
            </div>
        );
    },
});
