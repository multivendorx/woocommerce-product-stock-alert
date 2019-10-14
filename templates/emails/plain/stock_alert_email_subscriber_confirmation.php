<?php
/**
 * Stock Alert Email
 *
 * @author 		Dualcube
 * @version   1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WOO_Product_Stock_Alert;

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. You have successfully subscribed a product. We will inform you when the product becomes available. Product details are shown below for your reference:", 'woocommerce-product-stock-alert' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_obj = wc_get_product( $product_id );

if( $product_obj->is_type('variation') ) {
	$parent_id = $product_obj->get_parent_id();
	$product_link = $parent_obj->get_permalink();
	$product_name = $product_obj->get_formatted_name();
	$product_price = $product_obj->get_price_html();
} else {
	$product_link = $product_obj->get_permalink();
	$product_name = $product_obj->get_formatted_name();
	$product_price = $product_obj->get_price_html();
}

echo "\n Product Name : ".$product_name;

if($product_obj->get_type() == 'variation'){
  foreach ($product_obj->get_attributes() as $label => $value) {
    echo "\n".ucfirst(wc_attribute_label($label)).": ".ucfirst($value)."\n";
  }
} 

echo "\n\n Product Price : ".$product_price;

echo "\n\n Product link : ".$product_link;

echo "\n\n\n****************************************************\n\n";

echo "\n\n Your Details : ".$customer_email;

echo "\n\n\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
