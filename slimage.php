<?php
/**
 * Plugin Name: Slimage
 * Plugin URI: https://www.gsarigiannidis.gr
 * Description: A WordPress plugin to compress images during upload, using jpegoptim and optipng.
 * Version: 1.0.0
 * Author: Giorgos Sarigiannidis
 * Author URI: https://www.gsarigiannidis.gr
 * License: GPL3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'slimage_VERSION', '1.0' );

// Localize the plugin.
load_plugin_textdomain( 'slimage', false, basename( dirname( __FILE__ ) ) . '/languages' );

// Enqueue scripts and styles.
add_action( 'admin_enqueue_scripts', 'slimage_scripts' );
function slimage_scripts( $hook ) {
	if ( 'options-media.php' != $hook && 'post.php' != $hook ) {
		return;
	}
	wp_enqueue_style( 'slimage-styles', plugins_url( '/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'slimage-scripts', plugins_url( '/js/script.js', __FILE__ ) );
}


// Helper functions.
require_once dirname( __FILE__ ) . '/inc/helpers.php';

// Display the plugin's options at the backend.
if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/inc/options.php';
}

// Set the custom fields of the single attachments.
require_once dirname( __FILE__ ) . '/inc/fields.php';

// Compress thumbnail sizes.
include_once dirname( __FILE__ ) . '/inc/compressor.php';

add_action( 'wp_head', 'test' );
function test() {
	slash_dump(get_post_meta( 7, 'slimage_quality', true ));
}