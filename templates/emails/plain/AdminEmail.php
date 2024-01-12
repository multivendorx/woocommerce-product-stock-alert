<?php
/**
 * Stock Manager Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. A customer has subscribed a product on your shop. Product details are shown below for your reference:", 'woocommerce-stock-manager' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo "\n Product Name : " . $product->get_name() ;

echo "\n\n Product link : " . $product->get_permalink() ;

echo "\n\n\n****************************************************\n\n";

echo "\n\n Customer Details : ".$customer_email;

echo "\n\n\n****************************************************\n\n";


echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
