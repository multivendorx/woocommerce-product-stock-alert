<?php

namespace StockManager\Emails;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'SubscriberConfirmationEmail' ) ) :

/**
 * Email for stock manager
 *
 * An confirmation email will be sent to the customer when they subscribe product.
 *
 * @class 		SubscriberConfirmationEmail
 * @version		1.3.0
 * @author 		WC Marketplace
 * @extends 	\WC_Email
 */
class SubscriberConfirmationEmail extends \WC_Email {
	
	public $product;
	public $recipient = '';

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {		
		$this->id 			= 'stock_manager_subscriber_confirmation';
		$this->title 			= __( 'Confirm subscriber', 'woocommerce-stock-manager' );
		$this->description	= __( 'Confirm customer when they subscribe a product', 'woocommerce-stock-manager' );
		$this->template_html 	= 'emails/SubscriberConfirmationEmail.php';
		$this->template_plain = 'emails/plain/SubscriberConfirmationEmail.php';
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
		
		$this->recipient = $recipient;
		$this->product = $product;
		
		if ( !$this->is_enabled() || ! $this->get_recipient() ) {
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
		return apply_filters( 'woocommerce_email_subject_stock_manager', __( 'You have subscribed to a product on {site_title} ', 'woocommerce-stock-manager' ), $this->object );
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
			'customer_email'=> $this->recipient, 
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
			'customer_email'=> $this->recipient, 
			'sent_to_admin' => false, 
			'plain_text' 	=> true
		], '', $this->template_base );
		return ob_get_clean();
	} 
	
} 
endif;