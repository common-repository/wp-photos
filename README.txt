Follow these steps to set up WP Photos:

1) Put everything (except this README.txt file) from the zip file into your blog directory.

http://www.example.com/blog/  <-- everything in there


Now you'll have:

http://www.example.com/blog/wp-photos/
 - this is where the photos will go

http://www.example.com/blog/wp-photo.php
 - this shows the full size photo

http://www.example.com/blog/wp-photos.php
 - the page used for entering/editing data

http://www.example.com/blog/wp-photos.config.php
 - your settings go in here

http://www.example.com/blog/wp-photos.hack.php
 - the functions/objects used in the hack

http://www.example.com/blog/wp-photos.set-up.php
 -  this will create your wp_photos table in your database


2) Open up the wp-photos.config.php file and change the settings as needed.

3) Point your browser at: http://www.example.com/blog/wp-photos.set-up.php to create the wp-photos table in your database. You can delete this file when you're done.

4) Add this to edit.php before the <br /> tag and after the Edit/Delete links at line 275:

	<?php include_once('../wp-photos.hack.php'); wpphotos_link(); ?>

5) Add the following into your my-hacks.php (or index.php) file:

- this goes at the top somewhere before the rest of these tags

	<?php require_once('wp-photos.hack.php'); ?>

- this goes just in front of <?php the_content(); ?>
	
	<?php
	if ((!isset($c) || $c != 1) && (empty($post->password) || 
	    $HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] == $post->post_password)) {
		$photo = get_preview_photo($id);
		if (!empty($photo->thumb)) {
			print($photo->preview_thumbnail());
		}
	}
	?>

- this goes before the comments

	<?php
	if (isset($c) && $c == 1 && (empty($post->password) || 
	    $HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] == $post->post_password)) {
		$photos = get_post_photos($id);
		$photos = get_thumbnail_grid($photos);
		print($photos);
	}
	?>

That's it, you're all set up now.




Ok, now how to use it:

Add a folder into the wp-photos directory and upload the photos you want to include with your post:

http://www.example.com/blog/wp-content/wp-photos/new_folder/

The photos should be here:

http://www.example.com/blog/wp-content/wp-photos/new_folder/photo_1.jpg
http://www.example.com/blog/wp-content/wp-photos/new_folder/photo_2.jpg
http://www.example.com/blog/wp-content/wp-photos/new_folder/photo_3.jpg

and the thumbnail images need to be put into a folder named "t" in the same folder like so:

http://www.example.com/blog/wp-content/wp-photos/new_folder/t/photo_1.jpg
http://www.example.com/blog/wp-content/wp-photos/new_folder/t/photo_2.jpg
http://www.example.com/blog/wp-content/wp-photos/new_folder/t/photo_3.jpg

Then click the Add link next to a post in the admin interface to open the WP Photos window. Then click the (Use ->) link next to the folder you want to use. Add your captions, select the one you want as your preview photo and hit save!