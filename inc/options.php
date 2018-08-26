<?php
/**
 * Slimage Options page
 *
 * @package Slimage
 * @since 1.0
 */

/**
 * Add all sections, fields and settings during admin_init
 */

function slimage_settings_api_init() {
	// Add the section to media settings so we can add our fields to it
	add_settings_section(
		'slimage_setting_section',
		__( 'Compress images with Slimage', 'slimage' ),
		'slimage_setting_section_callback_function',
		'media'
	);

	// Add the field with the names and function to use for our new settings, put it in our new section
	add_settings_field(
		'slimage_enable_compression',
		__( 'Enable Slimage', 'slimage' ),
		'slimage_setting_enable_compression',
		'media',
		'slimage_setting_section',
		[
			'class' => 'slimage-check'
		]
	);
	add_settings_field(
		'slimage_jpegoptim_level',
		__( 'Quality of JPEG images (0-100)', 'slimage' ),
		'slimage_setting_jpegoptim_compression_level',
		'media',
		'slimage_setting_section',
		[
			'class' => 'slimage-setting'
		]
	);

	add_settings_field(
		'slimage_jpegoptim_extras',
		__( 'Extra arguments for jpegoptim', 'slimage' ),
		'slimage_setting_jpegoptim_extras',
		'media',
		'slimage_setting_section',
		[
			'class' => 'slimage-setting'
		]
	);

	add_settings_field(
		'slimage_optipng_level',
		__( 'Compression level for PNGs (1-7)', 'slimage' ),
		'slimage_setting_optipng_compression_level',
		'media',
		'slimage_setting_section',
		[
			'class' => 'slimage-setting'
		]
	);

	add_settings_field(
		'slimage_optipng_extras',
		__( 'Extra arguments for optipng', 'slimage' ),
		'slimage_setting_optipng_extras',
		'media',
		'slimage_setting_section',
		[
			'class' => 'slimage-setting'
		]
	);

	// Register our setting so that $_POST handling is done for us and
	// our callback function just has to echo the <input>
	register_setting( 'media', 'slimage_enable_compression' );
	register_setting( 'media', 'slimage_jpegoptim_level' );
	register_setting( 'media', 'slimage_jpegoptim_extras' );
	register_setting( 'media', 'slimage_optipng_level' );
	register_setting( 'media', 'slimage_optipng_extras' );

} // slimage_settings_api_init()

add_action( 'admin_init', 'slimage_settings_api_init' );

/**
 * Settings section callback function
 */
function slimage_setting_section_callback_function() {
	echo '<p class="slimage-description">' . __( 'Slimage uses <a href="https://github.com/tjko/jpegoptim" target="_blank">jpegoptim</a> and <a href="http://optipng.sourceforge.net/" target="_blank">optipng</a> to compress your images during upload. It will compress the thumbnails that WordPress generates but will leave the original file intact, so that you can safely restore your changes if necessary. To compress already uploaded images, you have to regenerate their thumbnails using the <code>wp media regenerate</code> command of the amazing <a href="https://wp-cli.org/" target="_blank">WP-CLI</a>. If you prefer to do it with a plugin, <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/" target="_blank">Force Regenerate Thumbnails</a> and <a href="https://wordpress.org/plugins/ajax-thumbnail-rebuild/" target="_blank">AJAX Thumbnail Rebuild</a> have been tested and confirmed to play nice with Slimage.', 'slimage' ) . '</p>';
}

/**
 * Error messages
 */
function slimage_error( $error ) {
	$output = __( '<strong>jpegoptim</strong> and <strong>optipng</strong> are not installed on your server. You need to install them in order to be able to use this plugin.', 'slimage' );
	if ( $error === 'shell_exec' ) {
		$output = __( 'This plugin uses PHP <code>shell_exec()</code> function which is by default enabled by most hosting companies. In your case, thought, <code>shell_exec()</code> is disabled. Please contact your hosting company to make sure that <code>shell_exec()</code> is enabled in your account, in order to be able to use this plugin. ', 'slimage' );
	}
	if ( $error === 'jpegoptim' ) {
		$output = __( '<strong>jpegoptim</strong> is not installed on your server. You need to install it in order to be able to compress JPEG images and activate this setting.', 'slimage' );
	} elseif ( $error === 'optipng' ) {
		$output = __( '<strong>optipng</strong> is not installed on your server. You need to install it in order to be able to compress PNG images and activate this setting.', 'slimage' );
	}

	return $output;
}

/**
 * Callback functions for the settings
 */
function slimage_setting_enable_compression() {
	if ( ! slimage_is_function_enabled( 'shell_exec' ) ) {
		$disabled = 'disabled';
		$label    = slimage_error( 'shell_exec' );
		$value    = '0';
		$checked  = '';
	} elseif ( slimage_program_exists( 'jpegoptim' ) || slimage_program_exists( 'optipng' ) ) {
		$disabled = '';
		$label    = __( 'Enable compression on thumbnails during the image upload.', 'slimage' );
		$value    = '1';
		$checked  = checked( 1, get_option( 'slimage_enable_compression' ), false );
	} else {
		$disabled = 'disabled';
		$label    = slimage_error( false );
		$value    = '0';
		$checked  = '';
	}
	echo '<input 
				name="slimage_enable_compression" 
				id="slimage_enable_compression" 
				type="checkbox" 
				value="' . $value . '" 
				' . $disabled . '
				class="checkbox" ' . $checked . ' /> 
				<label for="slimage_enable_compression">' . $label . '</label>';
}


function slimage_setting_jpegoptim_compression_level() {
	if ( slimage_program_exists( 'jpegoptim' ) ) {
		$disabled = '';
		$label    = __( 'Setting this value too low will affect the quality of the image (default is 90).', 'slimage' );
	} else {
		$disabled = 'disabled';
		$label    = slimage_error( 'jpegoptim' );
	}
	$val = slimage_option( 'slimage_jpegoptim_level' );
	echo '<input 
	            name="slimage_jpegoptim_level" 
	            id="slimage_jpegoptim_level" 
	            type="number" 
	            value="' . $val . '"
	            ' . $disabled . '
	            min="0"
	            max="100" 
	            class="number" />
	            <label for="slimage_jpegoptim_level">' . $label . '</label>';
}

function slimage_setting_optipng_compression_level() {
	if ( slimage_program_exists( 'optipng' ) ) {
		$disabled = '';
		$label    = __( 'A higher value will result in higher compression but will be slower (default is 2).', 'slimage' );
	} else {
		$disabled = 'disabled';
		$label    = slimage_error( 'optipng' );
	}
	$val = slimage_option( 'slimage_optipng_level' );
	echo '<input 
	            name="slimage_optipng_level" 
	            id="slimage_optipng_level" 
	            type="number" 
	            value="' . $val . '"
	            ' . $disabled . '
	            min="1"
	            max="7" 
	            class="number" />
	            <label for="slimage_optipng_level">' . $label . '</label>';
}

function slimage_setting_jpegoptim_extras() {
	if ( slimage_program_exists( 'jpegoptim' ) ) {
		$disabled = '';
		$label    = __( 'Extra arguments for jpegoptim. [<a href="http://www.kokkonen.net/tjko/src/man/jpegoptim.txt" target="_blank">available options</a>]', 'slimage' );
	} else {
		$disabled = 'disabled';
		$label    = slimage_error( 'jpegoptim' );
	}
	$val = slimage_option( 'slimage_jpegoptim_extras' );
	echo '<input 
	            name="slimage_jpegoptim_extras" 
	            id="slimage_jpegoptim_extras" 
	            type="text" 
	            value="' . $val . '"
	            ' . $disabled . '
	            class="text" />
	            <label for="slimage_jpegoptim_extras">' . $label . '</label>';
}

function slimage_setting_optipng_extras() {
	if ( slimage_program_exists( 'optipng' ) ) {
		$disabled = '';
		$label    = __( 'Extra arguments for optipng. [<a href="http://optipng.sourceforge.net/optipng-0.7.7.man.pdf" target="_blank">available options</a>]', 'slimage' );
	} else {
		$disabled = 'disabled';
		$label    = slimage_error( 'optipng' );
	}
	$val = slimage_option( 'slimage_optipng_extras' );
	echo '<input 
	            name="slimage_optipng_extras" 
	            id="slimage_optipng_extras" 
	            type="text" 
	            value="' . $val . '"
	            ' . $disabled . '
	            class="text" />
	            <label for="slimage_optipng_extras">' . $label . '</label>';
}