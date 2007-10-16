<?php
/*
Plugin Name: Sobek`s Posts in Category
Plugin URI: http://wordpress.sobek.pl/sobeks-post-in-category-plugin/
Version: 1.1
Description: List the posts of one or more selected categories. Whether you want more control over your sitemap or add the contents of a category to a post, this plugin can be used to list the contents of one or several categories in alphabetical order. Please read http://wordpress.sobek.pl/sobeks-post-in-category-plugin/ for installation and usage.

- v1.1 - features listing several categories with one call 
- v1.0 - basic working version
Author: Lukasz Sobek
Author URI: http://sobek.pl/
*/ 

function s_posts_in_category( $catID ) {

	global $wpdb;
	$number_of_cats = func_num_args();
	$list_of_cats = func_get_args();

	if($number_of_cats == 0) {

		echo 'Error: Please enter the ID of at least one category' ;
		exit;

	} elseif($number_of_cats == 1)	{

		$the_categories_result = "AND wp_term_taxonomy.term_id = '$catID'";

	} else {

		$the_categories_result = "AND ( ";

		for ($i = 0; $i < $number_of_cats; $i++) {

			if($i != 0) { $the_categories_result .= " OR "; }

			$the_categories_result .= "wp_term_taxonomy.term_id = '" . $list_of_cats[$i] . "'";
		}

		$the_categories_result .= " )";
	}

	$posts_in_term = $wpdb->get_results("
	SELECT wp_posts.ID, wp_posts.post_date, wp_posts.post_title, wp_posts.post_status, wp_term_relationships.object_id, wp_term_relationships.term_taxonomy_id FROM wp_posts, wp_term_relationships, wp_term_taxonomy
	WHERE wp_posts.ID = wp_term_relationships.object_id
	AND wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
	AND wp_term_taxonomy.taxonomy = 'category'
	AND wp_posts.post_status = 'publish'"
	. $the_categories_result .
	" AND wp_posts.post_date < NOW( )
	ORDER BY wp_posts.post_title ASC
	");

	$posts_in_term = array_values($posts_in_term);

	foreach ($posts_in_term as $posts) {
		echo '<li><a href="' . get_permalink($posts->ID) . '">' . $posts->post_title . '</a></li>';		
	}	
	
}

?>