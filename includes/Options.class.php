<?php
/**
 * Plugin Options
 *
 * @package Slimage
 * @since 1.0
 */

namespace Slimage;


class Options {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
	}

	/**
	 * Add all sections, fields and settings during admin_init
	 */

	public function init() {
		// Add the section to media settings so we can add our fields to it
		add_settings_section(
			'slimage_setting_section',
			__( 'Compress images with Slimage', 'slimage' ),
			[ $this, 'description' ],
			'media'
		);

		// Add the field with the names and function to use for our new settings, put it in our new section
		add_settings_field(
			'slimage_enable_compression',
			__( 'Enable Slimage', 'slimage' ),
			[ $this, 'enableCompression' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-check',
			]
		);

		add_settings_field(
			'slimage_server_path',
			__( 'Server path for jpegoptim & optipng', 'slimage' ),
			[ $this, 'serverPath' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-server-path' . ( Helper::serverSupports( 'jpegoptim' ) || Helper::serverSupports( 'optipng' ) ? ' hidden' : '' ),
			]
		);

		add_settings_field(
			'slimage_jpegoptim_level',
			__( 'Quality of JPEG images (0-100)', 'slimage' ),
			[ $this, 'jpegoptimLevel' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-setting'
			]
		);

		add_settings_field(
			'slimage_jpegoptim_extras',
			__( 'Extra arguments for jpegoptim', 'slimage' ),
			[ $this, 'jpegoptimExtras' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-setting'
			]
		);

		add_settings_field(
			'slimage_optipng_level',
			__( 'Compression level for PNGs (1-7)', 'slimage' ),
			[ $this, 'optipngLevel' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-setting'
			]
		);

		add_settings_field(
			'slimage_optipng_extras',
			__( 'Extra arguments for optipng', 'slimage' ),
			[ $this, 'optipngExtras' ],
			'media',
			'slimage_setting_section',
			[
				'class' => 'slimage-setting'
			]
		);

		// Register our setting so that $_POST handling is done for us and
		// our callback function just has to echo the <input>
		register_setting( 'media', 'slimage_enable_compression' );
		register_setting( 'media', 'slimage_server_path', [ $this, 'server_path_sanitize' ] );
		register_setting( 'media', 'slimage_jpegoptim_level' );
		register_setting( 'media', 'slimage_jpegoptim_extras' );
		register_setting( 'media', 'slimage_optipng_level' );
		register_setting( 'media', 'slimage_optipng_extras' );

	}

	public function server_path_sanitize( $input ) {
		$output = preg_replace( '/[\/]/', '%', $input );

		return $output;
	}


	public function description() {
		echo '<p class="slimage-description">' . __( 'Slimage uses <a href="https://github.com/tjko/jpegoptim" target="_blank">jpegoptim</a> and <a href="http://optipng.sourceforge.net/" target="_blank">optipng</a> to compress your images during upload. It will compress the thumbnails that WordPress generates but will leave the original file intact, so that you can safely restore your changes if necessary. To compress already uploaded images, you have to regenerate their thumbnails using the <code>wp media regenerate</code> command of the amazing <a href="https://wp-cli.org/" target="_blank">WP-CLI</a>. If you prefer to do it with a plugin, <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/" target="_blank">Force Regenerate Thumbnails</a> and <a href="https://wordpress.org/plugins/ajax-thumbnail-rebuild/" target="_blank">AJAX Thumbnail Rebuild</a> have been tested and confirmed to play nice with Slimage.', 'slimage' ) . '</p>';
	}


	public function error( $error ) {
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

	public function enableCompression() {
		if ( ! Helper::phpSupports( 'shell_exec' ) ) {
			$disabled = 'disabled';
			$label    = self::error( 'shell_exec' );
			$value    = '0';
			$checked  = '';
		} elseif ( Helper::serverSupports( 'jpegoptim' ) || Helper::serverSupports( 'optipng' ) ) {
			$disabled = '';
			$label    = __( 'Enable compression on thumbnails during the image upload.', 'slimage' );
			$value    = '1';
			$checked  = checked( 1, get_option( 'slimage_enable_compression' ), false );
		} else {
			$disabled = 'disabled';
			$label    = self::error( false );
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

	public function serverPath() {
		$label = __( 'If you are certain that jpegoptim and optipng are installed on your server but you still have no access to the plugin\'s options, maybe they are located on a different path (it can happen on shared servers). If this is the case, declare the path here like for example: <code>/home/username/bin/</code>.', 'slimage' );
		$val   = preg_replace( '/%/', '/', Helper::option( 'slimage_server_path' ) );
		echo '<input 
	            name="slimage_server_path" 
	            id="slimage_server_path" 
	            type="text" 
	            value="' . $val . '"
	            class="text" />
	            <label for="slimage_server_path">' . $label . '</label>';
	}

	public function jpegoptimLevel() {
		if ( Helper::serverSupports( 'jpegoptim' ) ) {
			$disabled = '';
			$label    = __( 'Setting this value too low will affect the quality of the image (default is 90).', 'slimage' );
		} else {
			$disabled = 'disabled';
			$label    = self::error( 'jpegoptim' );
		}
		$val = Helper::option( 'slimage_jpegoptim_level' );
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

	public function optipngLevel() {
		if ( Helper::serverSupports( 'optipng' ) ) {
			$disabled = '';
			$label    = __( 'A higher value will result in higher compression but will be slower (default is 2).', 'slimage' );
		} else {
			$disabled = 'disabled';
			$label    = self::error( 'optipng' );
		}
		$val = Helper::option( 'slimage_optipng_level' );
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

	public function jpegoptimExtras() {
		if ( Helper::serverSupports( 'jpegoptim' ) ) {
			$disabled = '';
			$label    = __( 'Extra arguments for jpegoptim. [<a href="http://www.kokkonen.net/tjko/src/man/jpegoptim.txt" target="_blank">available options</a>]', 'slimage' );
		} else {
			$disabled = 'disabled';
			$label    = self::error( 'jpegoptim' );
		}
		$val = Helper::option( 'slimage_jpegoptim_extras' );
		echo '<input 
	            name="slimage_jpegoptim_extras" 
	            id="slimage_jpegoptim_extras" 
	            type="text" 
	            value="' . $val . '"
	            ' . $disabled . '
	            class="text" />
	            <label for="slimage_jpegoptim_extras">' . $label . '</label>';
	}

	public function optipngExtras() {
		if ( Helper::serverSupports( 'optipng' ) ) {
			$disabled = '';
			$label    = __( 'Extra arguments for optipng. [<a href="http://optipng.sourceforge.net/optipng-0.7.7.man.pdf" target="_blank">available options</a>]', 'slimage' );
		} else {
			$disabled = 'disabled';
			$label    = self::error( 'optipng' );
		}
		$val = Helper::option( 'slimage_optipng_extras' );
		echo '<input 
	            name="slimage_optipng_extras" 
	            id="slimage_optipng_extras" 
	            type="text" 
	            value="' . $val . '"
	            ' . $disabled . '
	            class="text" />
	            <label for="slimage_optipng_extras">' . $label . '</label>';
	}

	public function enqueues( $hook ) {
		if ( 'options-media.php' != $hook && 'post.php' != $hook && 'upload.php' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'slimage-styles', plugins_url( '/admin/css/styles.css', dirname( __FILE__ ) ), [], SLIMAGE_VERSION );
		wp_enqueue_script( 'slimage-scripts', plugins_url( '/admin/js/scripts.js', dirname( __FILE__ ) ), [], SLIMAGE_VERSION, true );
	}
}