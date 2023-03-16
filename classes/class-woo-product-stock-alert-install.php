<?php
/**
 * Start schedule after plugin activation
 *
 */

class WOO_Product_Stock_Alert_Install {
	
	public function __construct() {
		
		if( !get_option('dc_product_stock_alert_activate') ) {
			if( $this->stock_alert_activate() == 'true' ) {
				update_option('dc_product_stock_alert_activate', 1);
			}
		}
		
		if( get_option( 'dc_product_stock_alert_installed' ) ) :
			$this->start_cron_job();
		endif;
	}
	
	/*
	 * This function will start the cron job
	 *
	 */
	function start_cron_job() {
		wp_clear_scheduled_hook('dc_start_stock_alert');
		
		wp_schedule_event( time(), 'hourly', 'dc_start_stock_alert' );
		update_option( 'dc_product_stock_alert_cron_start', 1 );
	}

	function stock_alert_activate() {
		global $WOO_Product_Stock_Alert;
		$stock_alert_settings = array();

		$stock_alert_settings = array(
                'is_enable' => 'is_enable'
            );

		if( !get_option('mvx_woo_stock_alert_general_tab_settings') ) {
			if( update_option( 'mvx_woo_stock_alert_general_tab_settings', $stock_alert_settings ) ) {
				return 'true';
			}
		}
	}
}