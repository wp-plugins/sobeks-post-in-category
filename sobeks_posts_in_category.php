<?php
/*
Plugin Name: Sobek`s Posts in Category
Plugin URI: http://wordpress.sobek.pl/sobeks-post-in-category-plugin/
Version: 1.5
Description: You want more control over your sitemap or add a list of posts in a category to your article? Sobek`s Posts in Category plugin can display the contents of categories. Please read <a href="http://wordpress.sobek.pl/sobeks-post-in-category-plugin/">the plugin page</a> for installation and usage.
Author: Lukasz Sobek
Author URI: http://sobek.pl/
*/ 

function sobeks_posts_in_category() {
	//-----------------------------------------------
	// Definitions
	global $wpdb;
	$wp_tp = $wpdb->prefix;
	$number_of_args = func_num_args();
	$list_of_args = func_get_args();
	$order_posts_by = "{$wp_tp}posts.post_title ASC";
	$display_post_count = 0;
	$display_comment_count = 0;
	$cstm_err_msg = '<b style="color:#f00;">Error:</b> Please enter the ID of at least one category';
	//-----------------------------------------------
	// Checking
	// if there are arguments
	if ($number_of_args != 0) {
		// if there are any "display" arguments
		if(!is_bool(array_search('commentsort',$list_of_args))) { $order_posts_by = "{$wp_tp}posts.comment_count DESC"; }
		if(!is_bool(array_search('date',$list_of_args))) { $order_posts_by = "{$wp_tp}posts.post_date DESC"; }
		if(!is_bool(array_search('count',$list_of_args))) { $display_post_count = 1; }
		if(!is_bool(array_search('comments',$list_of_args))) { $display_comment_count = 1; }
		$list_of_args = array_diff($list_of_args, array('commentsort', 'date', 'count', 'comments'));
		$number_of_args = func_num_args($list_of_args);
		// if, after having dealt with the "display arguments", there are any left
		if ($number_of_args != 0) {
			// if there is more than one category
			if($number_of_args != 1) {
				$the_categories_result = "AND ( ";
				for ($i = 0; $i < $number_of_args; $i++) {
					if($i != 0) { $the_categories_result .= " OR "; }
					$the_categories_result .= "{$wp_tp}term_taxonomy.term_id = '" . $list_of_args[$i] . "'";
				}
				$the_categories_result .= " )";
			// if there is one category
			} else { $the_categories_result = "AND {$wp_tp}term_taxonomy.term_id = '" . $list_of_args[0] ."'"; }
		// after having dealt with the "display arguments" there are no arguments left
		} else { echo $cstm_err_msg; exit; }
	// there are no arguments
	} else { echo $cstm_err_msg; exit; }
	//-----------------------------------------------
	// Code to execute after having done the checking
	$posts_in_term = $wpdb->get_results("SELECT {$wp_tp}posts.ID, {$wp_tp}posts.post_date, {$wp_tp}posts.post_title, {$wp_tp}posts.post_status, {$wp_tp}posts.comment_count, {$wp_tp}term_relationships.object_id, {$wp_tp}term_relationships.term_taxonomy_id FROM {$wp_tp}posts, {$wp_tp}term_relationships, {$wp_tp}term_taxonomy WHERE {$wp_tp}posts.ID = {$wp_tp}term_relationships.object_id AND {$wp_tp}term_relationships.term_taxonomy_id = {$wp_tp}term_taxonomy.term_taxonomy_id AND {$wp_tp}term_taxonomy.taxonomy = 'category' AND {$wp_tp}posts.post_status = 'publish' " . $the_categories_result . " AND {$wp_tp}posts.post_date < NOW( ) ORDER BY " . $order_posts_by);
	$posts_in_term = array_values($posts_in_term);
	if ($display_post_count != 0) {
		$number_of_posts = count($posts_in_term);
		if($number_of_posts != 1) {
			$post_count_statement = 'are ' . $number_of_posts . ' posts';
		} else { $post_count_statement = 'is one post'; }
		echo '<li>There ' . $post_count_statement . ' in this category</li>';
	} 
	foreach ($posts_in_term as $posts) {
		echo '<li><a href="' . get_permalink($posts->ID) . '">' . $posts->post_title . '</a>';
		if($display_comment_count != 0){ echo ' (' . $posts->comment_count . ')'; }
		echo '</li>';
	}	
}

?>