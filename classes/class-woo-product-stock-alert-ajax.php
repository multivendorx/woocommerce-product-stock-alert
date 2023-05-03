<?php
class WOO_Product_Stock_Alert_Ajax {

	public function __construct() {
		
		// Save customer email in database
		add_action( 'wp_ajax_alert_ajax', array(&$this, 'stock_alert_function') );
		add_action( 'wp_ajax_nopriv_alert_ajax', array(&$this, 'stock_alert_function') );

		// Delete unsubscribed users
		add_action( 'wp_ajax_unsubscribe_button', array($this, 'unsubscribe_users') );
		add_action( 'wp_ajax_nopriv_unsubscribe_button', array($this, 'unsubscribe_users') );
		// Export data
		add_action( 'wp_ajax_export_subscribers', array($this, 'export_stock_alert_data') );

		//add fields for variation product shortcode
		add_action( 'wp_ajax_nopriv_get_variation_box_ajax', array( $this, 'get_variation_box_ajax') );
		add_action('wp_ajax_get_variation_box_ajax', array( $this, 'get_variation_box_ajax') );
	}
	
	function export_stock_alert_data() {
		$headers_str = '';
		$headers_arr = $all_products = $all_products = $get_subscribed_user = $stock_alert_export_datas = array();
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

		
		foreach($headers as $header) { 
			$headers_arr[] = '"' . $header . '"';
		}
		$headers_str = implode(',', $headers_arr);
		
		$all_products = get_posts(
			array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'numberposts' => -1
			)
		);
		
		if( !empty($all_products) && is_array($all_products) ) {
			foreach( $all_products as $products_each ) {
				$child_ids = $product_obj = array();
				$product_obj = wc_get_product( $products_each->ID );
				if( $product_obj->is_type('variable') ) {
					if( $product_obj->has_child() ) {
						$child_ids = $product_obj->get_children();
						if( isset($child_ids) && !empty($child_ids) ) {
							foreach( $child_ids as $child_id ) {
								$all_product_ids[] = $child_id;
							}
						}
					}
				} else {
					$all_product_ids[] = $products_each->ID;
				}
			}
		}
		
		if( !empty($all_product_ids) && is_array($all_product_ids) ) {
			foreach( $all_product_ids as $all_product_id ) {
				$_product_subscriber = get_post_meta($all_product_id, '_product_subscriber', true);
				if ( $_product_subscriber && !empty($_product_subscriber) ) {
					$get_subscribed_user[$all_product_id] = get_post_meta( $all_product_id, '_product_subscriber', true );
				}
			}
		}
		
		if( isset( $get_subscribed_user ) && !empty( $get_subscribed_user ) ) {
			foreach( $get_subscribed_user as $pro_id => $subscribers ) {
				$product = wc_get_product($pro_id);
				$subscribers_string = implode( ",", $subscribers );
				$subscribers_lists = explode( ",", $subscribers_string );
				foreach($subscribers_lists as $subscribers_list){
					$stock_alert_export_datas[] = array(
						'"'.$pro_id.'"',
						'"'.$product->get_name().'"',
						'"'.$product->get_sku().'"',
						'"'.$product->get_type().'"',
						'"'.$subscribers_list.'"'
					);
				}
			}
		}
		
		echo $headers_str;
		if( isset($stock_alert_export_datas) && !empty($stock_alert_export_datas) ) {
			foreach( $stock_alert_export_datas as $stock_alert_export_data ) {
				echo "\r\n";
				echo implode(",", $stock_alert_export_data);
			}
		}
		
		exit();
	}

	function unsubscribe_users() {

		$customer_email = sanitize_email($_POST['customer_email']);
		$product_id = (int)$_POST['product_id'];
		$variation_id = (int)$_POST['var_id'];
		$current_subscriber = array();
		$success = 'false';

		$product = wc_get_product($product_id);
    	if ($product && $product->is_type( 'variable' ) && $variation_id > 0) {
    		$success = customer_stock_alert_unsubscribe($variation_id, $customer_email);
    	} else {
    		$success = customer_stock_alert_unsubscribe($product_id, $customer_email);
    	}

		echo $success;

		die();
	}
	
	function stock_alert_function() {
		$customer_email = sanitize_email($_POST['email']);
		$product_id = (int)$_POST['product_id'];
		$variation_id = (int)$_POST['variation_id'];
		$status = '';
    	$product = wc_get_product($product_id);
    	if ($product && $product->is_type( 'variable' ) && $variation_id > 0) {
    		$status = customer_stock_alert_insert($variation_id, $customer_email);
    	} else {
    		$status = customer_stock_alert_insert($product_id, $customer_email);
    	}
    	echo $status;
		die();
	}

	function get_variation_box_ajax(){
		global $WOO_Product_Stock_Alert;
		$product_id = (int)$_POST['product_id'];
		$child_id = (int)$_POST['variation_id'];
		$product = wc_get_product( $product_id );
		$display_stock_alert_form = false;
		
		if( $child_id && !empty($child_id) ) {
			$child_obj = new WC_Product_Variation($child_id);
			$stock_quantity = $child_obj->get_stock_quantity();
			$managing_stock = $child_obj->managing_stock();
			$is_in_stock = $child_obj->is_in_stock();
			$is_on_backorder = $child_obj->is_on_backorder( 1 );

			if ( ! $is_in_stock ) {
					$display_stock_alert_form = true;
			} elseif ( $managing_stock && $is_on_backorder && get_mvx_product_alert_plugin_settings('is_enable_backorders') ) {
					$display_stock_alert_form = true;
			} elseif ( $managing_stock ) {
				if(get_option('woocommerce_notify_no_stock_amount')){
					if($stock_quantity <= (int) get_option('woocommerce_notify_no_stock_amount') && get_mvx_product_alert_plugin_settings('is_enable_backorders')){
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