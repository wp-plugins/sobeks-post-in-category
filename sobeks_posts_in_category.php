<?php
/*
Plugin Name: Sobek`s Posts in Category
Plugin URI: http://wordpress.sobek.pl/sobeks-post-in-category-plugin/
Version: 1.0
Description: List the posts of a specified category. Whether you want more control over your sitemap or add the contents of a category to a post, this plugin can be used to list the contents of categories in alphabetical order. Simply enter &lt;ul&gt;&lt;?php posts_in_category(Number of category); ?&gt;&lt;/ul&gt; in the code of the page where you wish the list to be displayed. Based on pre 2.3 version from <a href="http://watershedstudio.com/portfolio/software/wp-category-posts.html">Brian Groce</a>.
Author: Lukasz Sobek
Author URI: http://sobek.pl/
*/ 

function s_posts_in_category( $catID ) {

	global $wpdb;
	
	$posts_in_term = $wpdb->get_results("
	SELECT wp_posts.ID, wp_posts.post_date, wp_posts.post_title, wp_posts.post_status,
	wp_term_relationships.object_id, wp_term_relationships.term_taxonomy_id
	FROM wp_posts, wp_term_relationships, wp_term_taxonomy
	WHERE wp_posts.ID = wp_term_relationships.object_id
	AND wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
	AND wp_term_taxonomy.taxonomy = 'category'
	AND wp_posts.post_status = 'publish'
	AND wp_term_taxonomy.term_id = '$catID'
	AND wp_posts.post_date < NOW( )
	ORDER BY wp_posts.post_title ASC
	");

	$posts_in_term = array_values($posts_in_term);

	foreach ($posts_in_term as $posts) {
		echo '<li><a href="' . get_permalink($posts->ID) . '">' . $posts->post_title . '</a></li>';		
	}	
	
}

?>