=== Header Image Uploader  ===

Contributor: Imran Ahmed Khan  
Tags: post, posts, uploader, meta box, custom meta box, image uploader, image, images   
Requires at least: 3.0  
Tested up to: 4.6  
Stable tag: 1.0
License: GPLv2 or later  


Simple WordPress plugin adds image uploader meta box.

== Description ==

A simple WP plugin that adds a meta box in your post admin to allow upload the header image for your post.   

= Installation =

1. Download the Header Image Uploader Plugin.
2. In your WordPress Administration, go to Plugins > Add New > Upload, and select the plugin ZIP file.
3. Activate the plugin.
7. Done! The image uploader meta box will appear on add/edit post.

Printing the image output is very simple. Just use this code in your template file.

<code>
  <?php /*$image = get_post_meta(get_the_ID(), '$key', true); */
			           $id = get_post_meta($post->ID, 'imhy', true);
					   $image = wp_get_attachment_image_src($id, 'full-size');
                      ?>
                      <img src="<?php echo $image[0]; ?>" />
</code>



= Support =

Contact the plugin author: <a href="http://www.imhysoft.com/contact-us/">Imran Ahmed Khan</a>

= Developer =

Are you a developer want to contribute to develop this plugin? Go to <a href="http://www.imhysoft.com/contact-us/">Contact Us</a>.

== Installation ==

1. Download the Header Image Uploader Plugin.
2. In your WordPress Administration, go to Plugins > Add New > Upload, and select the plugin ZIP file.
3. Activate the plugin.
4. The image uploader meta box will appears on add/edit post.

== Screenshots ==

1. Uploader field
2. WordPress media uploader
3. Image uploaded
4. Image selected

== Changelog ==

= 1.0 =
* Initial release
