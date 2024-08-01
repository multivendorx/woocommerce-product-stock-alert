<?php
/**
 * Stock Manager Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo esc_html( $email_heading ) . "\n\n";

echo sprintf( esc_html__( "Hi there. You have successfully subscribed to a product. We will inform you when the product becomes available. Product details are shown below for your reference:", 'woocommerce-stock-manager' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo "\n Product Name : " . esc_html( $product->get_name() ) ;

echo "\n\n Product Price : " . esc_html( wc_price( wc_get_price_to_display( $product ) ) ) ;

echo "\n\n Product link : " . esc_html( $product->get_permalink() ) ; 

echo "\n\n\n****************************************************\n\n";

echo "\n\n Your Details : ".esc_html( $customer_email );

echo "\n\n\n****************************************************\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
