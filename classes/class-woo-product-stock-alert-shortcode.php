<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class WOO_Product_Stock_Alert_Shortcode {

	public function __construct() {
		// Product Stock Alert Form Shortcode
		add_shortcode( 'display_stock_alert_form', array($this, 'display_stock_alert_form') );
	}

	function display_stock_alert_form($attr) {
		global $WOO_Product_Stock_Alert;

		$this->load_class('display-stock-alert-form');
		return $this->shortcode_wrapper(array('WOO_Product_Stock_Alert_Display_Form', 'output'), $attr);
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode Class Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	
	public function load_class($class_name = '') {
		global $WOO_Product_Stock_Alert;
		if ('' != $class_name && '' != $WOO_Product_Stock_Alert->token) {
			require_once ('shortcode/class-' . esc_attr($WOO_Product_Stock_Alert->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}
}

?>