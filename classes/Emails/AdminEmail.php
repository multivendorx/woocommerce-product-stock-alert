<?php

namespace StockManager\Emails;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'AdminEmail' ) ) :

/**
 * Email to Admin for stock manager
 *
 * An email will be sent to the admin when customer subscribe an out of stock product.
 *
 * @class 		WC_Admin_Email_Stock_Manager
 * @version		1.3.0
 * @author 		WC Marketplace
 * @extends 	\WC_Email
 */
class AdminEmail extends \WC_Email {
	
	public $product;
	public $customer_email;
	public $recipient = '';

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {		
		$this->id 			= 'stock_manager_admin';
		$this->title 			= __( 'Alert admin', 'woocommerce-stock-manager' );
		$this->description	= __( 'Admin will get an alert when customer subscribe any out of stock product', 'woocommerce-stock-manager' );
		$this->template_html 	= 'emails/AdminEmail.php';
		$this->template_plain = 'emails/plain/AdminEmail.php';
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
	function trigger( $recipient, $product, $customer_email ) {
		
		$this->recipient 		= $recipient;
		$this->product 		= $product;
		$this->customer_email = $customer_email;
		
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
		return apply_filters( 'woocommerce_email_subject_stock_manager', __( 'A Customer has subscribed to a product on {site_title} ', 'woocommerce-stock-manager' ), $this->object );
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
			'sent_to_admin' => true, 
			'plain_text' 	=> false, 
			'email'			=> $this, 
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
			'email_heading'  => $this->get_heading(), 
			'product' 		 => $this->product, 
			'customer_email' => $this->customer_email, 
			'sent_to_admin'  => true, 
			'plain_text'     => true
		 ], '', $this->template_base );
		return ob_get_clean();
	} 
	
} 
endif;