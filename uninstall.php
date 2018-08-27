<?php
/**
 * Uninstall Slimage
 *
 * @package Slimage
 * @since 1.0
 */


// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Remove main options
$options = Slimage\Helper::option();
foreach ( $options as $option => $value ) {
	delete_option( $option );
}

// Remove post_meta
$wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key REGEXP 'slimage_'" );
