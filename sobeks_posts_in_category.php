<?php
/*
Plugin Name: Sobek`s Posts in Category
Plugin URI: http://wordpress.sobek.pl/sobeks-post-in-category-plugin/
Version: 1.3
Description: List the posts of one or more selected categories. Whether you want more control over your sitemap or add the contents of a category to a post, this plugin can be used to list the contents of one or several categories in alphabetical order. Please read http://wordpress.sobek.pl/sobeks-post-in-category-plugin/ for installation and usage.
Author: Lukasz Sobek
Author URI: http://sobek.pl/
*/ 

function sobeks_posts_in_category() {

	//-----------------------------------------------
	// Definitions
	global $wpdb;
	$number_of_args = func_num_args();
	$list_of_args = func_get_args();
	$order_posts_by = "wp_posts.post_title ASC";
	$display_post_count = 0;
	$cstm_err_msg = '<b style="color:#f00;">Error:</b> Please enter the ID of at least one category';

	//-----------------------------------------------
	// Checking

	// if there are arguments
	if ($number_of_args != 0) {
		
		// check if there are any "display" arguments
		for ($i = 0; $i < $number_of_args ; $i++) {
				
			if(!is_string($list_of_args[$i])) {
			} else {
			if($list_of_args[$i] == "date") { $order_posts_by = "wp_posts.post_date DESC"; }
			if($list_of_args[$i] == "count") { $display_post_count = 1; }
			array_splice($list_of_args, $i, 1);
			$number_of_args = $number_of_args - 1;
			}

		}

		// check if, after having dealt with the "display arguments", there are any left
		if ($number_of_args != 0) {

			// if there is more than one category
			if($number_of_args != 1) {

				$the_categories_result = "AND ( ";
			
				for ($i = 0; $i < $number_of_args; $i++) {
				
					if($i != 0) { $the_categories_result .= " OR "; }

					$the_categories_result .= "wp_term_taxonomy.term_id = '" . $list_of_args[$i] . "'";
				}

				$the_categories_result .= " )";

			// if there is one category
			} else {
				$the_categories_result = "AND wp_term_taxonomy.term_id = '" . $list_of_args[0] ."'";
			}
		
		// after having dealt with the "display arguments" there are no arguments left
		} else { echo $cstm_err_msg; exit; }
	
	// there are no arguments
	} else { echo $cstm_err_msg; exit; }

	//-----------------------------------------------
	// Code to execute after having done the checking
		
	$posts_in_term = $wpdb->get_results("SELECT wp_posts.ID, wp_posts.post_date, wp_posts.post_title, wp_posts.post_status, wp_term_relationships.object_id, wp_term_relationships.term_taxonomy_id FROM wp_posts, wp_term_relationships, wp_term_taxonomy WHERE wp_posts.ID = wp_term_relationships.object_id AND wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_taxonomy.taxonomy = 'category' AND wp_posts.post_status = 'publish' " . $the_categories_result . " AND wp_posts.post_date < NOW( ) ORDER BY " . $order_posts_by);

	$posts_in_term = array_values($posts_in_term);

	if ($display_post_count != 0) {
		$number_of_posts = count($posts_in_term);
		echo '<li>There are ' . $number_of_posts . ' post(s) in this category</li>';
	}

	foreach ($posts_in_term as $posts) {
	
		echo '<li><a href="' . get_permalink($posts->ID) . '">' . $posts->post_title . '</a></li>';		

	}	

}

?>