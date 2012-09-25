=== Random Related Posts Based on Category ===
Contributors: tristarweb
Donate link: www.tristarwebdesign.co.uk
Tags: related, category, random, posts
Requires at least: 3
Tested up to: 3
Stable tag: 1.0.2

This plugin allows you to list any number of related posts from the same category as the current post. You can also randomise these results.

== Description ==

This plugin allows you to list any number of related posts from the same category as the current post. You can also randomise these results. It is very lightweight, at only 3KB.

There are a number of options available:

* Choose the number of Posts to show
* Choose whether it displays these posts randomly, or in order specified by you.
* Choose whether to display a title, what tags to use, and what text should be displayed (e.g. H3)
* Choose a class for the main UL

To display the random related posts in its simplest form, simply place the following code in your single.php file - `<?php relatedPosts(); ?>`.

To edit the options availble, use the following codes:

`<?php relatedPosts(
	$ppp = 4, 
	$rand = true, 
	$rPinctitle = true, 
	$rPtitletag = 'h3', 
	$rPtitle = 'Related Posts', 
	$rPulclass = 'related_posts',
	$orderby = 'title', 
	$order = 'ASC'
); ?>`

Where:

* **$ppp** = Number of posts to display (Any integer allowed)
* **$rand** = sets the display to random (true/false)
* **$rPinctitle** = Displays the title (true/false)
* **$rPtitletag** = The type of tag to wrap the title in (Default h3, but it can be any valid HTML tag)
* **$rPtitle** = The title to be displayed (This can be any text value)
* **$rPulclass** = This sets the class of the ul (This can be any text value)
* **$rPexcerpt** = Displays the post excerpt (true/false)
* **$orderby** = This allows you to order the posts by certain parameters (title/author/date/modified/menu_order/parent/ID/none/comment_count) **If you want to choose the order of your posts, be sure to set $rand to false**
* **$order** = This can either be Ascending or Descending (ASC/DESC)


== Installation ==

1. Upload the `random_related_posts_by_cat` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Place `<?php relatedPosts(); ?>` in your templates - please see the description for more options.

== Changelog ==

= 1.0.2 =
* Added ability to choose the order that the related posts are displayed in.

= 1.0.1 =
* Added ability to choose whether to display the excerpt.

= 1.0 =
* First Release

== Upgrade Notice ==

= 1.0.2 =
* Added ability to choose the order that the related posts are displayed in.

= 1.0.1 =
* Added ability to choose whether to display the excerpt.

= 1.0 =
* First Release.
