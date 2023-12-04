<?php
class WOO_Product_Stock_Alert_Ajax {

	public function __construct() {
		
		// Save customer email in database
		add_action('wp_ajax_alert_ajax', array(&$this, 'stock_alert_function'));
		add_action('wp_ajax_nopriv_alert_ajax', array(&$this, 'stock_alert_function'));
		// Delete unsubscribed users
		add_action('wp_ajax_unsubscribe_button', array($this, 'unsubscribe_users'));
		add_action('wp_ajax_nopriv_unsubscribe_button', array($this, 'unsubscribe_users'));
		// Export data
		add_action('wp_ajax_export_subscribers', array($this, 'export_stock_alert_data'));
		//add fields for variation product shortcode
		add_action('wp_ajax_nopriv_get_variation_box_ajax', array( $this, 'get_variation_box_ajax'));
		add_action('wp_ajax_get_variation_box_ajax', array($this, 'get_variation_box_ajax'));
		//recaptcha version-3 validate
		add_action('wp_ajax_recaptcha_validate_ajax', array($this, 'recaptcha_validate_ajax'));
		add_action('wp_ajax_nopriv_recaptcha_validate_ajax', array($this, 'recaptcha_validate_ajax'));
	}

	function recaptcha_validate_ajax() {
        $recaptcha_secret = isset($_POST['captcha_secret']) ? $_POST['captcha_secret'] : '';
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_response = isset($_POST['captcha_response']) ? $_POST['captcha_response'] : '';

        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);
        if (!$recaptcha->success || $recaptcha->score < 0.5) {
            echo 0;
        } else {
        	echo 1;
        }
        die();
	}
	
	function export_stock_alert_data() {
		$headers_str = '';
		$headers_arr = $stock_alert_export_datas = $subscribers_list = array();
		$file_name = 'list_subscribers.csv';
		
		// Set page headers to force download of CSV
		header("Content-type: text/x-csv");
		header("Content-Disposition: File Transfar");
		header("Content-Disposition: attachment;filename={$file_name}");
		
		// Set CSV headers
		$headers = array(
			'product_id',
			'product_name',
			'product_sku',
			'product_type',
			'subscribers'
		);

		
		foreach ($headers as $header) { 
			$headers_arr[] = '"' . $header . '"';
		}
		$headers_str = implode(',', $headers_arr);
		$get_subscribed_user = get_product_subscribers_array();

		if (isset($get_subscribed_user) && !empty($get_subscribed_user)) {
			foreach ($get_subscribed_user as $product_id => $subscribers) {
				foreach ($subscribers as $subscriber){
					$product = wc_get_product($product_id);
					$stock_alert_export_datas[] = [
						'"'.$product_id.'"',
						'"'.$product->get_name().'"',
						'"'.$product->get_sku().'"',
						'"'.$product->get_type().'"',
						'"'.$subscriber.'"'
					];
				}
			}
		}
		
		echo $headers_str;
		if (isset($stock_alert_export_datas) && !empty($stock_alert_export_datas)) {
			foreach ($stock_alert_export_datas as $stock_alert_export_data) {
				echo "\r\n";
				echo implode(",", $stock_alert_export_data);
			}
		}
		exit();
	}

	function unsubscribe_users() {
		$customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
		$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : '';
		$variation_id = isset($_POST['var_id']) ? (int)$_POST['var_id'] : 0;
		$current_subscriber = array();
		$success = 'false';
		if ($product_id && !empty($product_id) && !empty($customer_email)) {
			$product = wc_get_product($product_id);
			if ($product && $product->is_type( 'variable' ) && $variation_id > 0) {
				$success = customer_stock_alert_unsubscribe($variation_id, $customer_email);
			} else {
				$success = customer_stock_alert_unsubscribe($product_id, $customer_email);
			}
		}
		echo $success;
		die();
	}
	
	function stock_alert_function() {
		$customer_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
		$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : '';
		$variation_id = isset($_POST['variation_id']) ? (int)$_POST['variation_id'] : 0;
		$status = '';
		if ($product_id && !empty($product_id) && !empty($customer_email)) {
			$product = wc_get_product($product_id);
			if ($product && $product->is_type( 'variable' ) && $variation_id > 0) {
				$status = customer_stock_alert_insert($variation_id, $customer_email);
			} else {
				$status = customer_stock_alert_insert($product_id, $customer_email);
			}
		}
		echo $status;
		die();
	}

	function get_variation_box_ajax(){
		global $WOO_Product_Stock_Alert;
		$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : '';
		$child_id = isset($_POST['variation_id']) ? (int)$_POST['variation_id'] : '';
		$product = wc_get_product( $product_id );
		$display_stock_alert_form = false;
		
		if ($child_id && !empty($child_id)) {
			$child_obj = new WC_Product_Variation($child_id);
			$stock_quantity = $child_obj->get_stock_quantity();
			$managing_stock = $child_obj->managing_stock();
			$is_in_stock = $child_obj->is_in_stock();
			$is_on_backorder = $child_obj->is_on_backorder( 1 );

			if (!$is_in_stock) {
					$display_stock_alert_form = true;
			} elseif ($managing_stock && $is_on_backorder && get_woo_product_alert_plugin_settings('is_enable_backorders')) {
					$display_stock_alert_form = true;
			} elseif ($managing_stock) {
				if (get_option('woocommerce_notify_no_stock_amount')) {
					if ($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount') && get_woo_product_alert_plugin_settings('is_enable_backorders')) {
						$display_stock_alert_form = true;
					}
				}
			}

			if ($display_stock_alert_form) {
				echo $WOO_Product_Stock_Alert->frontend->html_subscribe_form($product, $child_obj);
			}
		}	
		die();
	}
}