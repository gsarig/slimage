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

$options = slimage_option();
foreach ( $options as $option => $value ) {
	delete_option( $option );
}
