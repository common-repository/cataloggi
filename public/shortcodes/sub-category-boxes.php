<?php 
	
do_action( 'ctlggi_subcategories_before' ); // <- extensible	

// get options
$ctlggi_general_options = get_option('ctlggi_general_options');

	// Here is how you would query the term slug
	if( is_tax() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$slug = $term->slug;
		$termmain = $term->name;
	}

// source: https://codex.wordpress.org/Function_Reference/get_term_children
// Used to get an array of children taxonomies
$term_id = $term->term_id;
$taxonomy_name = 'cataloggicat';
$termchildren = get_term_children( $term_id, $taxonomy_name );

$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';

// Display Main Categories on the catalog homepage and sub category boxes on the catalog pages 
if ( $ctlggi_general_options['display_category_boxes'] == '1' ) {

if ( ! empty( $termchildren ) && ! is_wp_error( $termchildren ) ) {

?>
<div class="cataloggi-items">
  <ul class="cataloggi-item-box-grid columns-3">
<?php

foreach ( $termchildren as $child ) {
   $term = get_term_by( 'id', $child, $taxonomy_name );
   
	// retrieving the values on a custom taxonomy
	// get the term id
	$t_id = $term->term_id;
	$term_meta = get_option( "ctlggi_cataloggicat_taxonomy_" . $t_id );
	//$custom_term_meta = $term_meta['custom_term_meta'];
	$cataloggi_thumb_img = $term_meta['cataloggi_thumb_img'];
	
	if ( $cataloggi_thumb_img == '' ) {
		$thumbimg =  plugins_url() . '/' . $this->plugin_name . '/public/assets/images/no-image.jpg';
	} else {
		$thumbimg = $cataloggi_thumb_img;
	}
	
	$termname = $term->name;
	// shorten term name
	//$termname = CTLGGI_Public::ctlggi_shorten_text($termname, $limit='8');
   
?>

    <li>
    
        <div class="thumb-container">
            <a href="<?php echo esc_url( get_term_link( $child, $taxonomy_name ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all posts under %s', 'cataloggi' ), $term->name ) ); ?>">
            <img alt="<?php echo esc_attr( sprintf( __( 'View all posts under %s', 'cataloggi' ), $term->name ) ); ?>" src="<?php echo esc_url( $thumbimg ); ?>" class="cataloggicat-thumb-img">
            </a>
        </div>
        
        <div class="category-title">
            <a href="<?php echo esc_url( get_term_link( $child, $taxonomy_name ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all posts under %s', 'cataloggi' ), $term->name ) ); ?>">
            <?php echo esc_attr( $termname ); ?>
            </a>
        </div>
        
    </li>
            
<?php

}

?>
  </ul>
</div><!--/ cataloggi-items -->
<?php 

echo '<hr class="cataloggi-hr">';

}

// clean up after the query and pagination
wp_reset_postdata(); 

} // if end Display Categories

?>