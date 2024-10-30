<?php 

	// get options
	$ctlggi_general_options = get_option('ctlggi_general_options'); 
	
    // default for includes/cataloggi-products.php 
	$termmain = '';
		
	// source: https://developer.wordpress.org/reference/functions/get_terms/
	// Since 4.5.0, taxonomies should be passed via the ‘taxonomy’ argument in the $args array
	$parentcategories = get_terms( array(
		'taxonomy' => 'cataloggicat',
		'parent' => 0,
		'hide_empty' => true, // if term category empty 
		'order' => $ctlggi_general_options['parent_menu_order'], // 'ASC'
		'orderby' => $ctlggi_general_options['parent_menu_order_by'], // modified | title | name | ID | rand
	) );
	
	/*
	echo '<pre>';
	print_r($parentcategories);
	echo '</pre>';
	*/

	$no_of_categories = count ( $parentcategories ) ;
 
	if ( $no_of_categories > 0 ) {
		
		echo '<div class="cataloggi-categories-nav">';
 
		foreach ( $parentcategories as $parentcategory ) {
		    
			echo '<ul class="menu"><li><a href="' . esc_url( get_term_link( $parentcategory ) ) . '">' . esc_attr( $parentcategory ->name ) . '</a>';
 
				$parent_id = $parentcategory ->term_id;
				
				$subcategories = get_terms( array(
					'taxonomy'    => 'cataloggicat',
					'child_of'    => $parent_id,
					'hide_empty'  => true, // if term category empty 
					'order'       => $ctlggi_general_options['sub_menu_order'], // 'ASC'
					'orderby'     => $ctlggi_general_options['sub_menu_order_by'], // modified | title | name | ID | rand
				) );
				
				/*
				echo '<pre>';
				print_r($subcategories);
				echo '</pre>';
				*/
            
			foreach ( $subcategories as $subcategory ) { 
                
				// for pages
				$args = array (
					'post_type'=> 'cataloggi',
					'post_per_page'=> -1,
					'nopaging'=> 'true',
					'taxonomy_name'=> $subcategory->name,
					'order' => 'ASC', // 'ASC'
					'orderby' => 'ID', // modified | title | name | ID | rand
				); 
				
				//echo '<ul><li><h3>' . $subcategory->name . '</h3><ul>';
				echo '<ul class="submenu"><li><a href="' . esc_url( get_term_link( $subcategory ) ) . '">' . esc_attr( $subcategory->name ) . '</a><ul>';
				    
					/*
					query_posts ( $args ) ;
 
						while ( have_posts () ) : the_post () ;
 
							?> 
								<li><a href="<?php the_permalink () ; ?>"><?php the_title () ; ?></a></li>
							<?php
 
						endwhile;
					*/
					
					
				echo '</ul></li></ul>' ; 
			
			} 
           
			echo '</li></ul>' ;
 
	   }
			
	   echo '</div>';
			
	}
	
	wp_reset_query () ;

?>