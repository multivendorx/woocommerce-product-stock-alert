<?php

class WOO_Product_Stock_Alert_Export {
	
	public function __construct() {
		
		$this->export_stock_alert_data();
	}
	
	function export_stock_alert_data() {
		global $WOO_Product_Stock_Alert;
	
		?>
			<div class="wrap">
				<h1><?php _e('WC Stock Alert Export', 'woocommerce-product-stock-alert') ?></h1>
				<p><?php _e('When you click the button below, this will export all out of stock products with subscribers email.', 'woocommerce-product-stock-alert') ?></p>
				<button class="wc_stock_alert_export_data button-primary"><?php _e('Export CSV', 'woocommerce-product-stock-alert') ?></button>
			</div>
		<?php
	}
}

?>
