<?php

spl_autoload_register( 'file_autoloader' );

/**
 * @param string $class The fully-qualified class name.
 * @return void
 */
function file_autoloader($class) {
    // replace namespace separators with directory separators in the relative 
    // class name, append with .php
    $class_path = preg_replace('/StockManager/', '', $class, 1);
    $file = plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . $class_path . '.php';
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}