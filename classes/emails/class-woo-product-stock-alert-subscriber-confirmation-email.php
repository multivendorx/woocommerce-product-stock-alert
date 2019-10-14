<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Subscriber_Confirmation_Email_Stock_Alert' ) ) :

/**
 * Email for stock alert
 *
 * An confirmation email will be sent to the customer when they subscribe product.
 *
 * @class 		WC_Subscriber_Confirmation_Email_Stock_Alert
 * @version		1.3.0
 * @author 		Dualcube
 * @extends 	WC_Email
 */
class WC_Subscriber_Confirmation_Email_Stock_Alert extends WC_Email {
	
	public $product_id;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $WOO_Product_Stock_Alert;
		
		$this->id 				= 'stock_alert_subscriber_confirmation';
		$this->title 			= __( 'Confirm subscriber', 'woocommerce-product-stock-alert' );
		$this->description		= __( 'Confirm customer when they subscribe a product', 'woocommerce-product-stock-alert' );

		$this->template_html 	= 'emails/stock_alert_email_subscriber_confirmation.php';
		$this->template_plain 	= 'emails/plain/stock_alert_email_subscriber_confirmation.php';

		$this->template_base = $WOO_Product_Stock_Alert->plugin_path . 'templates/';
		
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $recipient, $product_id ) {
		
		$this->recipient = $recipient;
		$this->product_id = $product_id;
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}
			
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get email subject.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_subject() {
		return apply_filters( 'woocommerce_email_subject_stock_alert', __( 'You have subscribed a product on {site_title}', 'woocommerce-product-stock-alert'), $this->object );
	}

	/**
	 * Get email heading.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_heading() {
		return apply_filters( 'woocommerce_email_heading_stock_alert', __( 'Welcome to {site_title}', 'woocommerce-product-stock-alert'), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'product_id' => $this->product_id,
			'customer_email' => $this->recipient,
			'sent_to_admin' => false,
			'plain_text' => false
		), '', $this->template_base);
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'product_id' => $this->product_id,
			'customer_email' => $this->recipient,
			'sent_to_admin' => false,
			'plain_text' => true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
	
}

endif;

return new WC_Subscriber_Confirmation_Email_Stock_Alert();
