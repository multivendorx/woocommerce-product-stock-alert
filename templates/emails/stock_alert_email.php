<?php
/**
 * Stock Alert Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WOO_Product_Stock_Alert;

do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( "Hi there. You have subscribed a product. Your subscribed product is available now. Product details are shown below for your reference:", 'woocommerce-product-stock-alert' ) );

$product_obj = wc_get_product( $product_id );

if( $product_obj->is_type('variation') ) {
	$parent_id = $product_obj->get_parent_id();
	$parent_obj = wc_get_product( $parent_id );
	$product_link = $parent_obj->get_permalink();
	$product_name = $product_obj->get_formatted_name();
	$product_price = $product_obj->get_price_html();
} else {
	$product_link = $product_obj->get_permalink();
	$product_name = $product_obj->get_formatted_name();
	$product_price = $product_obj->get_price_html();
}
$is_prices_including_tax = get_option( 'woocommerce_prices_include_tax' );
?>
<h3><?php _e( 'Product Details', 'woocommerce-product-stock-alert' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce-product-stock-alert' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce-product-stock-alert' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( $product_obj->get_name(), 'woocommerce-product-stock-alert' ); ?>
			<?php if($product_obj->get_type() == 'variation'){
              foreach ($product_obj->get_attributes() as $label => $value) {
                echo "<br>".ucfirst(wc_attribute_label($label)).": <strong>".ucfirst($value)."</strong>";
              }
            } ?></th>
           <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( $product_price, 'woocommerce-product-stock-alert' ); echo ( isset( $is_prices_including_tax ) && ($is_prices_including_tax != "yes" )) ? WC()->countries->ex_tax_or_vat() : WC()->countries->inc_tax_or_vat(); ?></th>
		</tr>
	</tbody>
</table>

<p style="margin-top: 15px !important;"><?php printf( __( "Following is the product link : ", 'woocommerce-product-stock-alert' ) ); ?><a href="<?php echo $product_link; ?>"><?php echo $product_name; ?></a></p>

<h3><?php _e( 'Your Details', 'woocommerce-product-stock-alert' ); ?></h3>
<p>
	<strong><?php _e( 'Email', 'woocommerce-product-stock-alert' ); ?> : </strong>
	<a target="_blank" href="mailto:<?php echo $customer_email; ?>"><?php echo $customer_email; ?></a>
</p>

</p>
<?php do_action( 'woocommerce_email_footer' ); ?>
