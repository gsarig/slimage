<?php
/**
 * Autoload Classes
 *
 * @package Slimage
 * @since 1.0
 */

try {
	spl_autoload_register( function ( $class ) {

		// project-specific namespace prefix
		$prefix = 'Slimage';

		// base directory for the namespace prefix
		$base_dir = plugin_dir_path( __DIR__ ) . 'classes/';

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// no, move to the next registered autoloader
			return;
		}

		// get the relative class name
		$relative_class = substr( $class, $len );

		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.class.php';

		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require $file;
		}
	} );
} catch ( Exception $e ) {
}