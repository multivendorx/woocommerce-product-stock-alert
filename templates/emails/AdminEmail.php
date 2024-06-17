<?php
/**
 * Stock Manager Email
 *
 * @author 	  WC Marketplace
 * @version   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


<p><?php printf( esc_html__( "Hi there. A customer has subscribed to a product on your shop. Product details are shown below for your reference:", 'woocommerce-stock-manager' ) ); ?></p>
<?php
$is_prices_including_tax = get_option( 'woocommerce_prices_include_tax' );
?>
<h3><?php esc_html_e( 'Product Details', 'woocommerce-stock-manager' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-stock-manager' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-stock-manager' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $product->get_name() ); ?>
			
			</th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;">
				<?php 
					echo wp_kses_post( wc_price( wc_get_price_to_display( $product ) ) ); 
					echo ( isset( $is_prices_including_tax ) && ( $is_prices_including_tax != "yes" ) ) ? WC()->countries->ex_tax_or_vat() : WC()->countries->inc_tax_or_vat(); 
				?>
			</th>
		</tr>
	</tbody>
</table>

<p style="margin-top: 15px !important;"><?php printf( esc_html__( "Following is the product link : ", 'woocommerce-stock-manager' ) ); ?><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( wp_strip_all_tags( $product->get_name() ) ); ?></a></p>

<h3><?php esc_html_e( 'Customer Details', 'woocommerce-stock-manager' ); ?></h3>
<p>
	<strong><?php esc_html_e( 'Email', 'woocommerce-stock-manager' ); ?> : </strong>
	<a target="_blank" href="mailto:<?php echo esc_html( $customer_email ); ?>"><?php echo esc_html( $customer_email ); ?></a>
</p>

<?php do_action( 'woocommerce_email_footer' );
