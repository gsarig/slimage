<?php
/**
 * Created by PhpStorm.
 * User: George
 * Date: 8/26/2018
 * Time: 17:46
 */

/**
 * Adding a custom field to Attachment Edit Fields
 *
 * @param  array $form_fields
 * @param  WP_POST $post
 *
 * @return array
 */
function slimage_add_media_custom_field( $form_fields, $post ) {

	$format = get_post_mime_type( $post->ID );
	if ( ( 'image/jpeg' === $format && slimage_program_exists( 'jpegoptim' ) ) ||
	     ( in_array( $format, [
			     'image/png',
			     'image/bmp',
			     'image/gif',
			     'image/tiff'
		     ] ) && slimage_program_exists( 'optipng' ) )
	) {

		if ( 'image/jpeg' === $format ) {
			$min             = '0';
			$max             = '100';
			$title           = __( 'Image quality', 'slimage' );
			$quality_label   = __( 'Override the default image quality (0-100).', 'slimage' );
			$extras_label    = __( 'Override the default extra parameters of jpegoptim.', 'slimage' );
			$quality_default = 'slimage_jpegoptim_level';
			$extras_default  = 'slimage_jpegoptim_extras';
		} else {
			$min             = '1';
			$max             = '7';
			$title           = __( 'Compression level', 'slimage' );
			$quality_label   = __( 'Override the default compression level (1-7).', 'slimage' );
			$extras_label    = __( 'Override the default extra parameters of optipng.', 'slimage' );
			$quality_default = 'slimage_optipng_level';
			$extras_default  = 'slimage_optipng_extras';
		}

		// Override
		$override_value                           = get_post_meta( $post->ID, 'slimage_override', true );
		$set_override_value                       = $override_value ? $override_value : 0;
		$checked                                  = $override_value ? 'checked="checked"' : '';
		$override_label                           = __( 'Override the default compression settings for this particular image.', 'slimage' );
		$form_fields['slimage_override']['label'] = __( 'Override quality', 'slimage' );
		$form_fields['slimage_override']['input'] = 'html';
		$form_fields['slimage_override']['html']  = '
		<input type="checkbox" value="' . $override_value . '" ' . $checked . ' id="attachments[' . $post->ID . '][slimage_override_handler]" />
		<input 
			type="number" hidden
			value="' . $set_override_value . '"
            min="0"
	        max="1" 
            name="attachments[' . $post->ID . '][slimage_override]"
            id="attachments[' . $post->ID . '][slimage_override]" />
            <label for="attachments[' . $post->ID . '][slimage_override_handler]">' . $override_label . '</label>';


		// Quality
		$quality_value                           = get_post_meta( $post->ID, 'slimage_quality', true );
		$set_quality_value                       = $quality_value ? $quality_value : slimage_option( $quality_default );
		$form_fields['slimage_quality']['label'] = $title;
		$form_fields['slimage_quality']['input'] = 'html';
		$form_fields['slimage_quality']['html']  = '
		<input 
			type="number" 
			value="' . $set_quality_value . '"
            min="' . $min . '"
	        max="' . $max . '" 
            name="attachments[' . $post->ID . '][slimage_quality]"
            id="attachments[' . $post->ID . '][slimage_quality]" />
            <label for="attachments[' . $post->ID . '][slimage_quality]">' . $quality_label . '</label>';

		// Extras
		$extras_value                           = get_post_meta( $post->ID, 'slimage_extras', true );
		$set_extras_value                       = $extras_value ? $extras_value : slimage_option( $extras_default );
		$form_fields['slimage_extras']['label'] = __( 'Extra parameters', 'slimage' );
		$form_fields['slimage_extras']['input'] = 'html';
		$form_fields['slimage_extras']['html']  = '
		<input 
			type="text" 
			value="' . $set_extras_value . '"
            name="attachments[' . $post->ID . '][slimage_extras]"
            id="attachments[' . $post->ID . '][slimage_extras]" /><br>
            <label for="attachments[' . $post->ID . '][slimage_extras]">' . $extras_label . '</label>
            <div class="warning"><p>In order for your changes to take effect, the image\'s thumbnails need to be regenerated. You can do that either with the <code>wp media regenerate</code> command of <a href="https://wp-cli.org/" target="_blank">WP-CLI</a> or with a third-party plugin like <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/" target="_blank">Force Regenerate Thumbnails</a> or <a href="https://wordpress.org/plugins/ajax-thumbnail-rebuild/" target="_blank">AJAX Thumbnail Rebuild</a>.</p></div>';
	}

	return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'slimage_add_media_custom_field', null, 2 );


/**
 * Save the data
 *
 * @param $post
 * @param $attachment
 *
 * @return mixed
 */
function slimage_save_attachment( $post, $attachment ) {
	if ( isset( $attachment['slimage_override'] ) ) {
		update_post_meta( $post['ID'], 'slimage_override', $attachment['slimage_override'] );
	}
	if ( isset( $attachment['slimage_quality'] ) ) {
		update_post_meta( $post['ID'], 'slimage_quality', $attachment['slimage_quality'] );
	}
	if ( isset( $attachment['slimage_extras'] ) ) {
		update_post_meta( $post['ID'], 'slimage_extras', $attachment['slimage_extras'] );
	}

	return $post;
}

add_filter( 'attachment_fields_to_save', 'slimage_save_attachment', null, 2 );