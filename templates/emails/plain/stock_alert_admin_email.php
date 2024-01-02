<?php
/**
 * Stock Alert Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woo_Product_Stock_Alert;

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. A customer has subscribed a product on your shop. Product details are shown below for your reference:", 'woocommerce-product-stock-alert' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_data = woo_stock_product_data($product_id);

echo "\n Product Name : " . $product_data['name'];

echo "\n\n Product link : " . $product_data['link'];

echo "\n\n\n****************************************************\n\n";

echo "\n\n Customer Details : ".$customer_email;

echo "\n\n\n****************************************************\n\n";


echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
