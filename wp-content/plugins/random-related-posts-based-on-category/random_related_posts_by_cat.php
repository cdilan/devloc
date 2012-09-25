<?php
/*
Plugin Name: Random Related Posts Based on Category
Plugin URI: http://www.tristarwebdesign.co.uk
Description: This plugin allows you to list any number of related posts from the same category as the current post. You can also randomise these results.
Version: 1.0.2
Author: Tristar Web Design
Author URI: http://www.tristarwebdesign.co.uk
License: GPL2
*/
?>
<?php
/*  Copyright 2010 James Kemp  (email : webmaster@tristarwebdesign.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php
	function query_random_posts($query) {
		return query_posts($query . '&random=true');
	}
	class RandomPosts {
		function orderby($orderby) {
			if ( get_query_var('random') == 'true' )
				return "RAND()";
			else
				return $orderby;
		}
		function register_query_var($vars) {
			$vars[] = 'random';
			return $vars;
		}
	}

	add_filter( 'posts_orderby', array('RandomPosts', 'orderby') );
	add_filter( 'query_vars', array('RandomPosts', 'register_query_var') );

	function relatedPosts($ppp = 4, $rand = true, $rPinctitle = true, $rPtitletag = 'h3', $rPtitle = 'Related Posts', $rPulclass = 'related_posts', $rPexcerpt = true, $orderby = 'title', $order = 'ASC') {
		if( is_single() ) {
			global $post;
			$categories = get_the_category($post->ID);
			$querycats = array();
			foreach($categories as $category) {
				$querycats[] = $category->cat_ID;
				$i++;
			}
			$querycats = implode(",", $querycats);
			// Query Arguments
			$args = array(
							'post__not_in' => array($post->ID),
							'cat' => $querycats,
							'posts_per_page' => $ppp,
							'random' => $rand,
							'orderby' => $orderby,
							'order' => $order
					);
			//The Query
			query_posts($args);
			//The Loop
			if ( have_posts() ) { 
				if($rPinctitle == true) {
					echo '<'.$rPtitletag.'>'.$rPtitle.'</'.$rPtitletag.'>';
				}
				echo '<ul class="'.$rPulclass.'">';
					$i = 1;
					while ( have_posts() ) : the_post();
						echo '<li>'.the_permalink().' --- '.the_title().'</li>';
						$i++;
					endwhile;
				echo '</ul>';
			}
			wp_reset_query();
		}
	}
?>