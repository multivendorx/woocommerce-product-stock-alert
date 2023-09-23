<?php
/**
 * Stock Alert Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WOO_Product_Stock_Alert;

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. You have successfully subscribed a product. We will inform you when the product becomes available. Product details are shown below for your reference:", 'woocommerce-product-stock-alert' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_data = woo_stock_product_data($product_id);

echo "\n Product Name : " . $product_data['name'];

echo "\n\n Product Price : " . $product_data['price'];

echo "\n\n Product link : " . $product_data['link']; 

echo "\n\n\n****************************************************\n\n";

echo "\n\n Your Details : ".$customer_email;

echo "\n\n\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
