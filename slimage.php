<?php
/**
 * Plugin Name: Slimage
 * Plugin URI: https://wordpress.org/plugins/slimage/
 * Description: A WordPress plugin that uses jpegoptim and optipng to compress images during upload, allowing you to override the compression level and quality on a per-image basis.
 * Version: 1.0.3
 * Author: Giorgos Sarigiannidis
 * Author URI: https://www.gsarigiannidis.gr
 * License: GPL3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SLIMAGE_VERSION', '1.0.3' );

// Localize the plugin.
add_action( 'init', 'slimage_load_textdomain' );
function slimage_load_textdomain() {
	load_plugin_textdomain( 'slimage', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

// Add settings link on plugin page.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'slimage_settings_links' );
function slimage_settings_links( $links ) {
	$links[] = '<a href="' . admin_url( 'options-media.php' ) . '">' . __( 'Settings', 'slimage' ) . '</a>';

	return $links;
}

// Autoload Classes.
include_once dirname( __FILE__ ) . '/includes/autoloader.php';

// Display the plugin's options at the backend.
new \Slimage\Options();

// Add the fields of a single attachment.
new \Slimage\Fields();

// Run the compressor.
new \Slimage\Compressor();
