<?php

namespace StockManager;
defined( 'ABSPATH' ) || exit;

class Utill {
    public static function log( $str ) {
        $file = SM( ) -> plugin_path . 'log/woo-stock-manager.log';
        if ( file_exists( $file ) ) {
            // Open the file to get existing content
            $str = var_export( $str, true );
            $current = wp_remote_get( $file );
            if ( $current ) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents( $file, $current );
        }
    }
} 