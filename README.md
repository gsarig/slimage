Slimage uses <a href="https://github.com/tjko/jpegoptim" target="_blank">jpegoptim</a> and <a href="http://optipng.sourceforge.net/" target="_blank">optipng</a> to compress your images during upload. It will compress the thumbnails that WordPress generates but will leave the original file intact, so that you can safely restore your changes if necessary. 

The plugin will also allow you to manually set the compression level and quality on a per-image basis. That way you have absolute control on how much quality you are willing to sacrifice in order to achieve better performance improvements, which can be handy if you want to pass the Google PageSpeed test.

Using it in conjuction with the <code>wp media regenerate</code> command of <a href="https://wp-cli.org/" target="_blank">WP-CLI</a> or with some thumbnail regeneration plugin, you can bulk optimize your photos and even experiment until you find the combination of quality/size that suits you.

To use the plugin you need to have <code>jpegoptim</code> and <code>optipng</code> installed on your server and make sure that PHP <code>shell_exec()</code> function isn't disabled in your <code>php.ini</code>. 