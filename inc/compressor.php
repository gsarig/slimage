<?php
/**
 * Compress thumbnail sizes
 *
 * @package Slimage
 * @since 1.0
 */

add_filter( 'wp_generate_attachment_metadata', 'slimage_compressor', 10, 2 );

/**
 * Compress the thumbnails of an attachment.
 *
 * @param $metadata
 * @param $attachment_id
 *
 * @return mixed
 */
function slimage_compressor( $metadata, $attachment_id ) {

	$enabled = slimage_option( 'slimage_enable_compression' );
	if ( isset( $enabled ) && ! empty( $enabled ) && ( slimage_program_exists( 'jpegoptim' ) || slimage_program_exists( 'optipng' ) ) ) {
		$override   = get_post_meta( $attachment_id, 'slimage_override', true );
		$q_override = get_post_meta( $attachment_id, 'slimage_quality', true );
		$e_override = get_post_meta( $attachment_id, 'slimage_extras', true );
		$jl         = ( $override === '1' && $q_override ) ? $q_override : slimage_option( 'slimage_jpegoptim_level' );
		$ol         = ( $override === '1' && $q_override ) ? $q_override : slimage_option( 'slimage_optipng_level' );
		$je         = ( $override === '1' && $e_override ) ? $e_override : slimage_option( 'slimage_jpegoptim_extras' );
		$oe         = ( $override === '1' && $e_override ) ? $e_override : slimage_option( 'slimage_optipng_extras' );
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

				if ( $format === 'image/jpeg' && slimage_program_exists( 'jpegoptim' ) ) {
					$command = 'jpegoptim --max=' . $jl . ' ' . $je . ' ' . $thumb_path;
				} elseif ( in_array( $format, [
						'image/png',
						'image/bmp',
						'image/gif',
						'image/tiff'
					] ) && slimage_program_exists( 'optipng' ) ) {
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