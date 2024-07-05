<?php
/**
 * Stock Manager Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo esc_html( $email_heading ) . "\n\n";

echo sprintf( esc_html__( "Hi there. A customer has subscribed to a product on your shop. Product details are shown below for your reference:", 'woocommerce-stock-manager' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo "\n Product Name : " . esc_html( $product->get_name() ) ;

echo "\n\n Product link : " . esc_html( $product->get_permalink() ) ;

echo "\n\n\n****************************************************\n\n";

echo "\n\n Customer Details : ".esc_html( $customer_email );

echo "\n\n\n****************************************************\n\n";


echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );

