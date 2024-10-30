<?php 
	
    /* Taxonomy Breadcrumb */ 
	echo '<div class="cataloggi-breadcrumbs">';
	
	$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
	$rewrite_slug_category = CTLGGI_Taxonomies::ctlggi_categories_tax_rewrite_slug();
	$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';
	//echo '<a href="' . esc_url( $cataloggiurl ) . '" alt="' . _e( 'Home', 'cataloggi' ) .'">' . _e( 'Home', 'cataloggi' ) .'</a> / ';
	
	?>
	<a href="<?php echo esc_url( $cataloggiurl ); ?>" alt="<?php _e( 'Home', 'cataloggi' ); ?>"><?php _e( 'Home', 'cataloggi' ); ?></a>  
	<?php 
	
	// Get the current term
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	
    if(!empty($term)) {
		
		/*
		echo '<pre>';
		print_r($term);
		echo '</pre>';
		*/
	
		// Create a list of all the term's parents
		$parent = $term->parent;
		while ($parent):
		$parents[] = $parent;
		$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
		$parent = $new_parent->parent;
		endwhile;
		if(!empty($parents)):
		$parents = array_reverse($parents);
		// For each parent, create a breadcrumb item
		foreach ($parents as $parent):
		$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
		$url = get_bloginfo('url').'/'.$rewrite_slug_category.'/'.$item->slug;
		echo ' > <a href="'. esc_url( $url ) .'">'. esc_attr( $item->name ) .'</a> ';
		endforeach;
		endif;
		// Display the current term in the breadcrumb
		echo ' > '. esc_attr( $term->name ) .'';
	
	} else {
		
		/*
		if ( get_the_title() ) {
			echo ' > '.get_the_title().'';
		}
		*/
		
		
		/*
	   // single product view
		$postid = get_the_ID();
		// source: https://developer.wordpress.org/reference/functions/get_post/
		$post_info = get_post( $postid );
		//$author = $post_info->post_author;
		$post_type = $post_info->post_type;
		
		// Get post type taxonomies.
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		
        // Get the terms related to post.
        $terms = get_the_terms( $postid, 'cataloggicat' );
		
		//Returns All Term Items for "my_taxonomy"
        $term_list = wp_get_post_terms($postid, 'cataloggicat', array("fields" => "all"));
		
		echo '<pre>';
		print_r($term_list);
		echo '</pre>';
		*/
		
		/*
		
		if ( $post_info->post_type = 'cataloggi') {
			// get custom post type by post id
			$categories = get_the_terms( $postid, 'cataloggicat' );
			$firstCategory = $categories[0]->name; 
			$caturl = home_url() . '/' . $categories[0]->taxonomy . '/' . $categories[0]->slug . '/';
			
			
			echo '<pre>';
			print_r($categories);
			echo '</pre>';
			
			
			// category
			echo ' > <a href="' . esc_url( $caturl ) . '" alt="' . esc_attr( $firstCategory ) . '">' . $firstCategory . '</a> > ';
			// page
			echo get_the_title();
		}
	    */
	}
	
	echo '</div>';
	
?>