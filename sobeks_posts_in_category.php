<?php
/*
Plugin Name: Sobek`s Posts in Category
Plugin URI: http://wordpress.sobek.pl/sobeks-post-in-category-plugin/
Version: 1.2
Description: List the posts of one or more selected categories. Whether you want more control over your sitemap or add the contents of a category to a post, this plugin can be used to list the contents of one or several categories in alphabetical order. Please read http://wordpress.sobek.pl/sobeks-post-in-category-plugin/ for installation and usage.

v1.2 - adds a sort by title or date option and a name change of the function
v1.1 - features listing several categories with one call 
v1.0 - basic working version
Author: Lukasz Sobek
Author URI: http://sobek.pl/
*/ 

function sobeks_posts_in_category() {

	global $wpdb;
	$number_of_args = func_num_args();
	$list_of_args = func_get_args();

	//-----------------------------------------------
	// checking everything
	//-----------------------------------------------
	
	//-----------------------------------------------
	// if there are arguments
	if ($number_of_args != 0) {
		
		// if the first argument is a string
		if(is_string($list_of_args[0])) {

			$order_posts_input = $list_of_args[0];
			array_splice($list_of_args, 0, 1);
			$number_of_args = $number_of_args - 1;

			// if the string is "date"
			if ($order_posts_input != "title") {

				$order_posts_by = "wp_posts.post_date DESC";

			// if the string is something else
			} else {

				$order_posts_by = "wp_posts.post_title ASC";
			}

		// if the first argument is not a string
		} else {
			$order_posts_by = "wp_posts.post_title ASC";
		}

		// if there is more than one category
		if($number_of_args != 1) {

			$the_categories_result = "AND ( ";
			
			for ($i = 0; $i < $number_of_args; $i++) {
				
				if($i != 0) {
					$the_categories_result .= " OR ";
				}

				$the_categories_result .= "wp_term_taxonomy.term_id = '" . $list_of_args[$i] . "'";
			}

			$the_categories_result .= " )";

		// if there is only one category
		} else {
			$the_categories_result = "AND wp_term_taxonomy.term_id = '" . $list_of_args[0] ."'";
		}
	
	//-----------------------------------------------
	// if there are no arguments
	} else {
		echo '<b style="color:#f00;">Error:</b> Please enter the ID of at least one category' ;
		exit;
	}

	//-----------------------------------------------
	// code to execute after having done the checking
	//-----------------------------------------------
		
	$posts_in_term = $wpdb->get_results("SELECT wp_posts.ID, wp_posts.post_date, wp_posts.post_title, wp_posts.post_status, wp_term_relationships.object_id, wp_term_relationships.term_taxonomy_id FROM wp_posts, wp_term_relationships, wp_term_taxonomy WHERE wp_posts.ID = wp_term_relationships.object_id AND wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_taxonomy.taxonomy = 'category' AND wp_posts.post_status = 'publish' " . $the_categories_result . " AND wp_posts.post_date < NOW( ) ORDER BY " . $order_posts_by);

	$posts_in_term = array_values($posts_in_term);

	foreach ($posts_in_term as $posts) {
	
		echo '<li><a href="' . get_permalink($posts->ID) . '">' . $posts->post_title . '</a></li>';		

	}	

}

?>