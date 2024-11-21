import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import axios from 'axios'; // Import axios

registerBlockType('woocommerce-stock-manager/stock-notification-block', {
    title: __('Stock Notification Block', 'woocommerce-stock-manager'),
    description: __('This block can be connected to WooCommerce Out-of-Stock and Backorder products to provide a stock notification form for users.', 'woocommerce-stock-manager'),
    category: 'woocommerce',
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
        const [formHtml, setFormHtml] = useState(__('Loading form...', 'woocommerce-stock-manager'));

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

        // Fetch the rendered form from the REST API
        useEffect(() => {
            if (productId) {
                axios.get(`${stockNotificationForm.apiUrl}/${stockNotificationForm.restUrl}/render-form?product_id=${productId}`)
                    .then((response) => {
                        setFormHtml(response.data.html || __('Failed to load form.', 'woocommerce-stock-manager'));
                    });
            } else {
                setFormHtml(__('No product selected.', 'woocommerce-stock-manager'));
            }
        }, [productId]);

        return (
            <div {...blockProps}>
                <div dangerouslySetInnerHTML={{ __html: formHtml }} />
            </div>
        );
    },

    save: () => {
        // Save function remains empty since rendering is handled by the PHP render callback
        return null;
    },
});
