<?php
/**
 * The Compressor
 *
 * @package Slimage
 * @since 1.0
 */

namespace Slimage;

class Compressor {

	public function __construct() {
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'compress' ], 10, 2 );
	}

	public function compress( $metadata, $attachment_id ) {
		$enabled = Helper::option( 'slimage_enable_compression' );
		if ( isset( $enabled ) && ! empty( $enabled ) && ( Helper::serverSupports( 'jpegoptim' ) || Helper::serverSupports( 'optipng' ) ) ) {
			$override   = get_post_meta( $attachment_id, 'slimage_override', true );
			$q_override = get_post_meta( $attachment_id, 'slimage_quality', true );
			$e_override = get_post_meta( $attachment_id, 'slimage_extras', true );
			$jl         = ( $override === '1' && $q_override ) ? $q_override : Helper::option( 'slimage_jpegoptim_level' );
			$ol         = ( $override === '1' && $q_override ) ? $q_override : Helper::option( 'slimage_optipng_level' );
			$je         = ( $override === '1' && $e_override ) ? $e_override : Helper::option( 'slimage_jpegoptim_extras' );
			$oe         = ( $override === '1' && $e_override ) ? $e_override : Helper::option( 'slimage_optipng_extras' );
			// Get the full image path.
			$original_path = realpath( get_attached_file( $attachment_id, true ) );
			// Extract the full image filename.
			$original_name = wp_basename( $original_path );
			$sizes         = $metadata['sizes'];

			if ( $sizes ) {
				foreach ( $sizes as $size => $meta ) {
					// Replace the filename of the full image with that of the thumbnail.
					$thumb_path = str_replace( $original_name, $meta['file'], $original_path );
					$format     = $meta['mime-type'];

					if ( $format === 'image/jpeg' && Helper::serverSupports( 'jpegoptim' ) ) {
						$command = 'jpegoptim --max=' . $jl . ' ' . $je . ' ' . $thumb_path;
					} elseif ( in_array( $format, [
							'image/png',
							'image/bmp',
							'image/gif',
							'image/tiff'
						] ) && Helper::serverSupports( 'optipng' ) ) {
						$command = 'optipng -o' . $ol . ' ' . $oe . ' ' . $thumb_path;
					} else {
						$command = null;
					}
					if ( $command ) {
						shell_exec( $command . ' && chmod -R 755 ' . $thumb_path );
					}
				}
			}
		}

		return $metadata;
	}

}
