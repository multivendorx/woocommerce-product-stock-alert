<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Email_Stock_Alert' ) ) :

/**
 * Email for stock alert
 *
 * An email will be sent to the customer when their subscribed product is available.
 *
 * @class 		WC_Email_Stock_Alert
 * @version		1.3.0
 * @author 		WC Marketplace
 * @extends 	WC_Email
 */
class WC_Email_Stock_Alert extends WC_Email {
	
	public $product_id;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $WOO_Product_Stock_Alert;
		
		$this->id 				= 'stock_alert';
		$this->title 			= __( 'Alert Subscriber', 'woocommerce-product-stock-alert' );
		$this->description		= __( 'Alert customer when their subscribed product becomes in stock', 'woocommerce-product-stock-alert' );

		$this->template_html 	= 'emails/stock_alert_email.php';
		$this->template_plain 	= 'emails/plain/stock_alert_email.php';

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

		$this->customer_email = $recipient;
		$this->recipient = $recipient;
		$this->product_id = $product_id;

		if (apply_filters('woocommerce_instock_alert_email_goes_to_admin', true)) {
			$this->recipient .= ',' . get_option('admin_email');
		}
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
		return apply_filters( 'woocommerce_email_subject_stock_alert', __( 'Your Subscribed product on {site_title} is available now', 'woocommerce-product-stock-alert'), $this->object );
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
			'customer_email' => $this->customer_email,
			'sent_to_admin' => false,
			'plain_text' => false,
			'email' => $this,
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
			'customer_email' => $this->customer_email,
			'sent_to_admin' => false,
			'plain_text' => true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
	
}

endif;

