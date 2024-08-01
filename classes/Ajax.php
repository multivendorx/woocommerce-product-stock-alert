<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Ajax {

	public function __construct() {
		// Save customer email in database
		add_action( 'wp_ajax_alert_ajax', [ &$this, 'subscribe_users' ] );
		add_action( 'wp_ajax_nopriv_alert_ajax', [ &$this, 'subscribe_users' ] );
		// Delete unsubscribed users
		add_action( 'wp_ajax_unsubscribe_button', [ $this, 'unsubscribe_users' ] );
		add_action( 'wp_ajax_nopriv_unsubscribe_button', [ $this, 'unsubscribe_users' ] );
		// Export data
		add_action( 'wp_ajax_export_subscribers', [ $this, 'export_CSV_data' ] );
		//add fields for variation product shortcode
		add_action( 'wp_ajax_nopriv_get_variation_box_ajax', [ $this, 'get_variation_box_ajax' ] );
		add_action( 'wp_ajax_get_variation_box_ajax', [ $this, 'get_variation_box_ajax' ] );
		//recaptcha version-3 validate
		add_action( 'wp_ajax_recaptcha_validate_ajax', [ $this, 'recaptcha_validate_ajax' ] );
		add_action( 'wp_ajax_nopriv_recaptcha_validate_ajax', [ $this, 'recaptcha_validate_ajax' ] );
	} 

	/**
	 * This funtion check recaptcha validation.
	 * @return never
	 */
	function recaptcha_validate_ajax() {
		if ( ! check_ajax_referer( 'stock-manager-security-nonce', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		} 
        $recaptcha_secret = isset( $_POST[ 'captcha_secret' ] ) ? sanitize_text_field( $_POST[ 'captcha_secret' ] ) : '';
        $recaptcha_response = isset( $_POST[ 'captcha_response' ] ) ? sanitize_text_field( $_POST[ 'captcha_response' ] ) : '';
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

        $recaptcha =  wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response );
        
        if ( !$recaptcha->success || $recaptcha->score < 0.5 ) {
            echo 0;
        } else {
        	echo 1;
        } 
        die();
	}
	
	/**
	 * Preaper data for CSV. CSV contain all stockmanager subscribtion details.
	 * @return never
	 */
	function export_CSV_data( $argument = [] ) {
		$get_subscribed_user = [];

		// Merge the arguments with default arguments.
		if ( ! is_array( $argument ) ) $argument = [];
		$argument = array_merge( [ 'limit' => -1, 'return' => 'ids' ], $argument );

        $products = wc_get_products( $argument );

		foreach( $products as $product ) {
			$product_ids = Subscriber::get_related_product( $product );

            foreach ( $product_ids as $product_id ) {
				$subscribers = Subscriber::get_product_subscribers_email( $product_id );
                if ( $subscribers && !empty( $subscribers ) ) {
					$get_subscribed_user[ $product_id ] = $subscribers;
                } 
            } 
        }

		$csv_header_string = '';
		$csv_headers_array = $csv_body_arrays = $subscribers_list = [];
		$file_name = 'list_subscribers.csv';
		
		// Set page headers to force download of CSV
		header( "Content-type: text/x-csv" );
		header( "Content-Disposition: File Transfar" );
		header( "Content-Disposition: attachment;filename= {$file_name} " );
		
		// Set CSV headers
		$csv_headings = [ 
			'product_id', 
			'product_name', 
			'product_sku', 
			'product_type', 
			'subscribers'
		];
		
		foreach ( $csv_headings as $heading ) { 
			$csv_headers_array[] = $heading;
		} 
		$csv_header_string = implode( ', ', $csv_headers_array );

		if ( isset( $get_subscribed_user ) && !empty( $get_subscribed_user ) ) {
			foreach ( $get_subscribed_user as $product_id => $subscribers ) {
				foreach ( $subscribers as $subscriber ) {
					$product = wc_get_product( $product_id );
					$csv_body_arrays[] = [ 
						 $product_id , 
						 $product->get_name() , 
						 $product->get_sku() , 
						 $product->get_type() , 
						 $subscriber 
					 ];
				} 
			} 
		} 
		
		echo $csv_header_string;
		if ( isset( $csv_body_arrays ) && !empty( $csv_body_arrays ) ) {
			foreach ( $csv_body_arrays as $csv_body_array ) {
				echo "\r\n";
				echo implode( ", ", $csv_body_array );
			} 
		} 
		exit();
	} 

	/**
	 * Unsubscribe a user through ajax call.
	 * @return never
	 */
	function unsubscribe_users() {
		if ( ! check_ajax_referer( 'stock-manager-security-nonce', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		}
		
		$customer_email = isset( $_POST[ 'customer_email' ] ) ? sanitize_email( $_POST[ 'customer_email' ] ) : '';
		$product_id = isset( $_POST[ 'product_id' ] ) ? absint( $_POST[ 'product_id' ] ) : '';
		$variation_id = isset( $_POST[ 'var_id' ] ) ? absint( $_POST[ 'var_id' ] ) : 0;

		$success = false;

		if ( $product_id && !empty( $product_id ) && !empty( $customer_email ) ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( 'variable' ) && $variation_id > 0 ) {
				$success = Subscriber::remove_subscriber( $variation_id, $customer_email );
			} else {
				$success = Subscriber::remove_subscriber( $product_id, $customer_email );
			} 
		} 
		echo esc_html( $success );
		die();
	} 
	
	/**
	 * Subscribe a user through ajax call.
	 * @return never
	 */
	function subscribe_users() {
		if ( ! check_ajax_referer( 'stock-manager-security-nonce', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		}

		$customer_email = isset( $_POST[ 'email' ] ) ? sanitize_email( $_POST[ 'email' ] ) : '';
		$product_id 	= isset( $_POST[ 'product_id' ] ) ? absint( $_POST[ 'product_id' ] ) : '';
		$variation_id 	= isset( $_POST[ 'variation_id' ] ) ? absint( $_POST[ 'variation_id' ] ) : 0;
		$status 		= '';

		/**
		 * Action hook before subscription
		 * @var string $customer_email
		 * @var int $product_id
		 * @var int $variation_id
		 */
		do_action( 'stock_manager_before_subscribe', $customer_email, $product_id, $variation_id );

		if ( $product_id && !empty( $product_id ) && !empty( $customer_email ) ) {
			$product_id = ( $variation_id && $variation_id > 0 ) ? $variation_id : $product_id;
			$do_complete_additional_task = apply_filters( 'stock_manager_do_complete_additional_task', false );
        	$is_accept_email_address = apply_filters( 'stock_manager_is_accept_email_address', false );

			if ( Subscriber::is_already_subscribed( $customer_email, $product_id ) ) {
				$status = '/*?%already_registered%?*/';
			} else if ( $do_complete_additional_task ) {
				$status = apply_filters( 'stock_manager_new_subscriber_added', true, $customer_email, $product_id );
			} else if ( $is_accept_email_address ) {
				$status = apply_filters( 'stock_manager_accept_email', true, $customer_email, $product_id );
			} else {
				Subscriber::insert_subscriber( $customer_email, $product_id );
				Subscriber::insert_subscriber_email_trigger( wc_get_product( $product_id ), $customer_email );
				$status = true;
			} 
		}

		echo esc_html( $status );
		die();
	}

	/**
	 * Get the subscription form for variation product through ajax call.
	 * @return never
	 */
	function get_variation_box_ajax() {
		if ( ! check_ajax_referer( 'stock-manager-security-nonce', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		} 
		$product_id = isset( $_POST[ 'product_id' ] ) ? absint( $_POST[ 'product_id' ] ) : '';
		$child_id = isset( $_POST[ 'variation_id' ] ) ? absint( $_POST[ 'variation_id' ] ) : '';
		$product = wc_get_product( $product_id );
		$child_obj = null;
		if ( $child_id && !empty( $child_id ) ) {
			$child_obj = new \WC_Product_Variation( $child_id );
		} 
		echo SM()->frontend->get_subscribe_form( $product, $child_obj );
		die();
	} 
} 