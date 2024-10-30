<?php 

// get options
$ctlggi_general_options = get_option('ctlggi_general_options');

// default for includes/cataloggi-products.php 
$termmain = '';
 
// source: https://developer.wordpress.org/reference/functions/get_terms/
// Since 4.5.0, taxonomies should be passed via the ‘taxonomy’ argument in the $args array
$terms = get_terms( array(
	'taxonomy' => 'cataloggicat',
	'hide_empty' => true, // if term category empty 
	'parent' => 0, // get only parent terms
	'order' => $ctlggi_general_options['category_order'], // 'ASC'
	'orderby' => $ctlggi_general_options['category_order_by'], // modified | title | name | ID | rand
) );
 
// Prior to 4.5.0, the first parameter of get_terms() was a taxonomy or list of taxonomies
//$terms = get_terms( 'cataloggicat', $args );

/*
echo '<pre>';
print_r($terms);
echo '</pre>';
*/
	
// Display Main Categories on the catalog homepage and sub category boxes on the catalog pages 
if ( $ctlggi_general_options['display_category_boxes'] == '1' ) {
?>

<div class="cataloggi-items">
  <ul class="cataloggi-item-box-grid columns-3">

<?php 
	
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );
		$i = 0;
		
		foreach ( $terms as $term ) 
		{
			$i++;
			
			// retrieving the values on a custom taxonomy
			// get the term id
			$t_id = $term->term_id;
			$term_meta = get_option( "ctlggi_cataloggicat_taxonomy_" . $t_id );
			//$custom_term_meta = $term_meta['custom_term_meta'];
			$cataloggi_thumb_img = $term_meta['cataloggi_thumb_img'];
			
			if ( $cataloggi_thumb_img == '' ) {
				$thumbimg =  plugins_url( '/cataloggi/public/assets/images/no-image.jpg');
			} else {
				$thumbimg = $cataloggi_thumb_img;
			}
			
			$termname = $term->name;
			// shorten term name
			//$termname = CTLGGI_Public::ctlggi_shorten_text($termname, $limit='8');
			
?>

    <li>
    
        <div class="thumb-container">
<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term->name ) ); ?>">
<img alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term->name ) ); ?>" src="<?php echo esc_url( $thumbimg ); ?>" />
</a>
        </div>
        
        <div class="category-title">
<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term->name ) ); ?>">
<?php echo $termname; ?>
</a>
        </div>
        
    </li>
            
<?php
			
		}
		
?>
  </ul>
</div><!--/ cataloggi-items -->
<?php 
		
	}
	
// clean up after the query and pagination
wp_reset_postdata(); 

echo '<hr class="cataloggi-hr">';

} // if end Display Categories

?>