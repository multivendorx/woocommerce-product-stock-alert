<?php

spl_autoload_register( 'file_autoloader' );

/**
 * @param string $class The fully-qualified class name.
 * @return void
 */
function file_autoloader($class) {
    // replace namespace separators with directory separators in the relative 
    // class name, append with .php
    $class_path = str_replace('\\', '/', $class);
    $class_path = str_replace('StockManager', '', $class_path);
    
    $file =  __DIR__ . '/' . $class_path . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}