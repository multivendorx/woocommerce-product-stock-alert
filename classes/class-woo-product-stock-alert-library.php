<?php
class WOO_Product_Stock_Alert_Library {
  
  public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $jquery_lib_path;
  
  public $jquery_lib_url;

	public function __construct() {
	  global $WOO_Product_Stock_Alert;
	  
	  $this->lib_path = $WOO_Product_Stock_Alert->plugin_path . 'lib/';

    $this->lib_url = $WOO_Product_Stock_Alert->plugin_url . 'lib/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->jquery_lib_path = $this->lib_path . 'jquery/';
    
    $this->jquery_lib_url = $this->lib_url . 'jquery/';
	}
	
}
