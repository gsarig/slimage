<?php
/**
 * Uninstall Slimage
 *
 * @package Slimage
 * @since 1.0
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

// Remove options
$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name REGEXP 'slimage_'" );

// Remove post_meta
$wpdb->query( "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key REGEXP 'slimage_'" );
