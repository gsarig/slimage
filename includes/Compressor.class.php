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
			$has_override     = get_post_meta( $attachment_id, 'slimage_override', true );
			$quality_override = get_post_meta( $attachment_id, 'slimage_quality', true );
			$extras_override  = get_post_meta( $attachment_id, 'slimage_extras', true );
			$jpegoptim_level  = ( $has_override === '1' && $quality_override ) ? $quality_override : Helper::option( 'slimage_jpegoptim_level' );
			$jpegoptim_extras = ( $has_override === '1' && $extras_override ) ? $extras_override : Helper::option( 'slimage_jpegoptim_extras' );
			$optipng_level    = ( $has_override === '1' && $quality_override ) ? $quality_override : Helper::option( 'slimage_optipng_level' );
			$optipng_extras   = ( $has_override === '1' && $extras_override ) ? $extras_override : Helper::option( 'slimage_optipng_extras' );
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
						$command = Helper::programPath( 'jpegoptim' ) . ' --max=' . $jpegoptim_level . ' ' . $jpegoptim_extras . ' ' . $thumb_path;
					} elseif ( in_array( $format, [
							'image/png',
							'image/bmp',
							'image/gif',
							'image/tiff'
						] ) && Helper::serverSupports( 'optipng' ) ) {
						$command = Helper::programPath( 'optipng' ) . ' -o' . $optipng_level . ' ' . $optipng_extras . ' ' . $thumb_path;
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
