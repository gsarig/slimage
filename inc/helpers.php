<?php
/**
 * Helper functions
 *
 * @package Slimage
 * @since 1.0
 *
 */


/**
 * Check if a specific PHP function is enabled.
 *
 * @param $func
 *
 * @return bool
 */
function slimage_is_function_enabled( $func ) {
	return is_callable( $func ) && false === stripos( ini_get( 'disable_functions' ), $func );
}


/**
 * Check if jpegoptim and optipng are installed on the server.
 *
 * @param $name
 *
 * @return null|string
 */
function slimage_program_exists( $name ) {
	$output = null;
	if ( slimage_is_function_enabled( 'shell_exec' ) ) {
		$output = shell_exec( $name . ' --version' );
	}

	return $output;
}

/**
 * Get the plugin options and set their defaults.
 *
 * @param string $option
 *
 * @return array|mixed|null
 */
function slimage_option( $option = 'all' ) {
	$options = [
		'slimage_enable_compression' => get_option( 'slimage_enable_compression' ),
		'slimage_jpegoptim_level'    => get_option( 'slimage_jpegoptim_level' ) ? get_option( 'slimage_jpegoptim_level' ) : '90',
		'slimage_optipng_level'      => get_option( 'slimage_optipng_level' ) ? get_option( 'slimage_optipng_level' ) : '2',
		'slimage_jpegoptim_extras'   => get_option( 'slimage_jpegoptim_extras' ) ? get_option( 'slimage_jpegoptim_extras' ) : '--strip-all --all-progressive',
		'slimage_optipng_extras'     => get_option( 'slimage_optipng_extras' ) ? get_option( 'slimage_optipng_extras' ) : '',
	];

	if ( $option === 'all' ) {
		$output = $options;
	} else {
		$output = $options[ $option ] ? $options[ $option ] : null;
	}

	return $output;
}