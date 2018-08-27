<?php
/**
 * Enqueue scripts and styles
 *
 * @package Slimage
 * @since 1.0
 */

add_action( 'admin_enqueue_scripts', 'slimage_scripts' );
function slimage_scripts( $hook ) {
	if ( 'options-media.php' != $hook && 'post.php' != $hook ) {
		return;
	}
	wp_enqueue_style( 'slimage-styles', plugins_url( '/css/styles.css', dirname( __FILE__ ) ), [], SLIMAGE_VERSION );
	wp_enqueue_script( 'slimage-scripts', plugins_url( '/js/scripts.js', dirname( __FILE__ ) ), [], SLIMAGE_VERSION, true );
}