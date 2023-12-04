<?php
/**
 * Stock Alert Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WOO_Product_Stock_Alert;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( "Hi there. A customer has subscribed a product on your shop. Product details are shown below for your reference:", 'woocommerce-product-stock-alert' ) ); ?></p>
<?php
$product_data = woo_stock_product_data($product_id);
$is_prices_including_tax = get_option( 'woocommerce_prices_include_tax' );
?>
<h3><?php esc_html_e( 'Product Details', 'woocommerce-product-stock-alert' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-product-stock-alert' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-product-stock-alert' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $product_data['name'] ); ?>
			
			</th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;">
				<?php 
					echo wp_kses_post( $product_data['price']); 
					echo ( isset( $is_prices_including_tax ) && ($is_prices_including_tax != "yes" )) ? WC()->countries->ex_tax_or_vat() : WC()->countries->inc_tax_or_vat(); 
				?>
			</th>
		</tr>
	</tbody>
</table>

<p style="margin-top: 15px !important;"><?php printf( __( "Following is the product link : ", 'woocommerce-product-stock-alert' ) ); ?><a href="<?php echo esc_url($product_data['link']); ?>"><?php echo esc_html(wp_strip_all_tags($product_data['name'])); ?></a></p>

<h3><?php esc_html_e( 'Customer Details', 'woocommerce-product-stock-alert' ); ?></h3>
<p>
	<strong><?php esc_html_e( 'Email', 'woocommerce-product-stock-alert' ); ?> : </strong>
	<a target="_blank" href="mailto:<?php echo $customer_email; ?>"><?php echo esc_html($customer_email); ?></a>
</p>

<?php do_action( 'woocommerce_email_footer' );
