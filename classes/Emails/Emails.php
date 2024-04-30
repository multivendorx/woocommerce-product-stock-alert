<?php

namespace StockManager\Emails;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'Emails' ) ) :

/**
 * Email for stock manager
 *
 * An email will be sent to the customer when their subscribed product is available.
 *
 * @class 		WC_Email_Stock_Manager
 * @version		1.3.0
 * @author 		WC Marketplace
 * @extends 	\WC_Email
 */
class Emails extends \WC_Email {
	
	public $product;
	public $recipient = '';

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		$this->id 			= 'stock_manager';
		$this->title 			= __( 'Alert Subscriber', 'woocommerce-stock-manager' );
		$this->description	= __( 'Alert customer when their subscribed product becomes in stock', 'woocommerce-stock-manager' );
		$this->template_html 	= 'emails/Email.php';
		$this->template_plain = 'emails/plain/Email.php';
		$this->template_base  = SM()->plugin_path . 'templates/';
		
		// Call parent constuctor
		parent::__construct();
	} 

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $recipient, $product ) {

		$this->customer_email = $recipient;
		$this->recipient = $recipient;
		$this->product = $product;

		if ( apply_filters( 'product_backin_stock_send_admin', false ) ) {
			$this->recipient .= ', ' . get_option( 'admin_email' );
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
		return apply_filters( 'woocommerce_email_subject_stock_manager', __( 'Your Subscribed product on {site_title} is available now', 'woocommerce-stock-manager' ), $this->object );
	} 

	/**
	 * Get email heading.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_heading() {
		return apply_filters( 'woocommerce_email_heading_stock_manager', __( 'Welcome to {site_title} ', 'woocommerce-stock-manager' ), $this->object );
	} 

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, [ 
			'email_heading' => $this->get_heading(), 
			'product' 		=> $this->product, 
			'customer_email'=> $this->customer_email, 
			'sent_to_admin' => false, 
			'plain_text' 	=> false, 
			'email' 		=> $this, 
		], '', $this->template_base );
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
		wc_get_template( $this->template_plain, [ 
			'email_heading' => $this->get_heading(), 
			'product' 		=> $this->product, 
			'customer_email'=> $this->customer_email, 
			'sent_to_admin' => false, 
			'plain_text' 	=> true
		], '', $this->template_base );
		return ob_get_clean();
	} 	
} 
endif;