=== Slimage ===
Contributors: gsarig
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WLR4KUNK7NZJE
Tags: compress, image, optimization, optimize, optipng, jpegoptim, lossy, lossless
Requires at least: 4.8
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later

A WordPress plugin that uses jpegoptim and optipng to compress images during upload, allowing you to override the compression level and quality on a per-image basis. It will compress the thumbnails that WordPress generates but will leave the original file intact, so that you can safely restore your changes if necessary. In order to use Slimage, you need to have jpegoptim and optipng installed on your server. 

== Description ==

Slimage uses <a href="https://github.com/tjko/jpegoptim" target="_blank">jpegoptim</a> and <a href="http://optipng.sourceforge.net/" target="_blank">optipng</a> to compress your images during upload. It will compress the thumbnails that WordPress generates but will leave the original file intact, so that you can safely restore your changes if necessary. 

The plugin will also allow you to manually set the compression level and quality on a per-image basis. That way you have absolute control on how much quality you are willing to sacrifice in order to achieve better performance improvements, which can be handy if you want to pass the Google PageSpeed test.

Using it in conjuction with the <code>wp media regenerate</code> command of <a href="https://wp-cli.org/" target="_blank">WP-CLI</a> or with some thumbnail regeneration plugin, you can bulk optimize your photos and even experiment until you find the combination of quality/size that suits you.

To use the plugin you need to have <code>jpegoptim</code> and <code>optipng</code> installed on your server and make sure that PHP <code>shell_exec()</code> function isn't disabled in your <code>php.ini</code>. 

= Features =

* Compress an image during upload.
* You can set a default level of compression for all newly uploaded images.
* You can override that compression level on a per-image basis.
* You can use all the available options of jpegoptim and optipng. 
* Combining it with some image regeneration tool, you can bulk compress your existing images.
* The plugin leaves the original image intact, so that any change that you make can be reversible. 

== Installation ==

1. Install jpegoptim and optipng on your server.
2. Make sure that shell_exec() is active on your php.ini (it usually is).
3. Upload the Slimage plugin to your WordPress plugins directory and activate it. 
4. Go to Settings / Media and check the "Enable Slimage" under "Compress images with Slimage". If jpegoptim and optipng are installed on a custom path on your server, set that path under "Server path for jpegoptim & optipng". 

== Frequently Asked Questions ==

= Can I bulk regenerate already uploaded images? =

Yes. After you set your desired compression level on the plugin settings and save your changes, you can use the <code>wp media regenerate</code> command of the amazing <a href="https://wp-cli.org/" target="_blank">WP-CLI</a> to regenerate the thumbnails. If you don't feel comfortable with the command line and prefer to do it with a plugin, <a href="https://wordpress.org/plugins/force-regenerate-thumbnails/" target="_blank">Force Regenerate Thumbnails</a> and <a href="https://wordpress.org/plugins/ajax-thumbnail-rebuild/" target="_blank">AJAX Thumbnail Rebuild</a> have been tested and confirmed to play nice with Slimage.

= Can I set different compression level for a specific image? =

Yes. On each image edit page there is an option called "Override quality" which allows you to set your specific settings for that particular image. After changing your image settings, you still need to regenerate its thumbnails in order to run the compression. 

= Can I use this plugin if I don't have jpegoptim or optipng installed on my server? =

If neither jpegoptim nor optipng exist on your server, then the plugin won't work. It won't break your site, but it will not do anything (except from showing a related warning message on it's settings page). If only one of the tools exists, then it will use it to process the specific type of images only (JPEGs for jpegoptim and PNGs for optipng).

= Can I use this plugin if <code>shell_exec()</code> isn't enabled on my php.ini? = 

No. The plugin relies on the PHP <code>shell_exec()</code> function which is by default enabled by most hosting companies. If, in your case, it is disabled, you will not be able to use it. Again, this will not break your site.

= What extra arguments can I set? =

The default extra arguments should be fine for most cases. If you really want to play around with it, though, there are various additional options for both <a href="http://www.kokkonen.net/tjko/src/man/jpegoptim.txt" target="_blank">jpegoptim</a> and <a href="http://optipng.sourceforge.net/optipng-0.7.7.man.pdf" target="_blank">optipng</a>.

= Can I pass the Google Pagespeed test with that plugin? =

Yes. But you might need to make some compromise on the quality of your photos. For JPEGs, setting the quality level to 50 or 60 is enough to pass the test on most cases. Depending on the complexity of the image, though, on some cases the quality loss might be visible to the naked eye so you might need to experiment with the level of quality. 

== Screenshots ==
1. The main settings of the plugin, under Settings / Media.
2. The option to override quality for a specific image under the image's edit page. 
3. A sample of a few compression levels. The first image keeps the default WordPress compression and no Slimage compression applied (65.33KB). The rest of the samples are compressed with 60% (40.26KB), 50% (35.5KB), 40% (33.03KB), 30% (26.25KB), 20% (20.64KB) and 10% (13.3KB). 

== Changelog ==

= 1.0 =
* Initial release