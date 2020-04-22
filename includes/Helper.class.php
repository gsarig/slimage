<?php
/**
 * Helper functions
 *
 * @package Slimage
 * @since 1.0
 */

namespace Slimage;


class Helper {

	/**
	 * Check if a specific PHP function is enabled.
	 *
	 * @param $func
	 *
	 * @return bool
	 */
	public static function phpSupports( $func ) {
		return is_callable( $func ) && false === stripos( ini_get( 'disable_functions' ), $func );
	}


	/**
	 * Check if jpegoptim and optipng are installed on the server.
	 *
	 * @param $name
	 *
	 * @return null|string
	 */
	public static function serverSupports( $name ) {
		$output = null;
		if ( self::phpSupports( 'shell_exec' ) ) {
			$output = shell_exec( self::programPath( $name ) . ' --version' );
		}

		return $output;
	}


	/**
	 * Check if a server path is declared.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public static function programPath( $name ) {
		$output = $name;
		$path   = self::option( 'slimage_server_path' );
		if ( $path ) {
			$output = $path . $name;
		}

		return escapeshellarg( preg_replace( '/%/', '/', self::sanitizePath( $output ) ) );
	}

	/**
	 * Sanitize the path name.
	 *
	 * @param $name
	 *
	 * @return null|string|string[]
	 */
	public static function sanitizePath( $name ) {
		return preg_replace( '/[^a-z0-9\/%]/', '', strtolower( $name ) );
	}

	/**
	 * Get the plugin options and set their defaults.
	 *
	 * @param string $option
	 *
	 * @return array|mixed|null
	 */
	public static function option( $option = 'all' ) {
		$options = [
			'slimage_enable_compression' => get_option( 'slimage_enable_compression' ),
			'slimage_server_path'        => get_option( 'slimage_server_path' ) ? get_option( 'slimage_server_path' ) : '',
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
}