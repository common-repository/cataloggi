<?php 

// get options
$ctlggi_general_options = get_option('ctlggi_general_options');
$display_cat_boxes  = isset( $ctlggi_general_options['display_cat_boxes'] ) ? sanitize_text_field( $ctlggi_general_options['display_cat_boxes'] ) : '0';

// default for includes/cataloggi-products.php 
$termmain = '';

$queried_object = get_queried_object();
$term_id   = $queried_object->term_id;
$term_name = $queried_object->name;

/*
echo '<pre>';
print_r($queried_object); // $queried_object
echo '</pre>';
exit;
*/
	
// Display Main Categories on the catalog homepage and sub category boxes on the catalog pages 
if ( $display_cat_boxes == '1' ) {
?>

<div class="cataloggi-items">
  <ul class="cataloggi-item-box-grid columns-3">

<?php 
	
	$term_meta = get_option( "ctlggi_cataloggicat_taxonomy_" . $term_id );
	//$custom_term_meta = $term_meta['custom_term_meta'];
	$cataloggi_thumb_img = $term_meta['cataloggi_thumb_img'];
	
	if ( $cataloggi_thumb_img == '' ) {
		$thumbimg =  plugins_url( '/cataloggi/public/assets/images/no-image.jpg');
	} else {
		$thumbimg = $cataloggi_thumb_img;
	}
			
?>

    <li>
    
        <div class="thumb-container">
<a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term_name ) ); ?>">
<img alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term_name ) ); ?>" src="<?php echo esc_url( $thumbimg ); ?>" />
</a>
        </div>
        
        <div class="category-title">
<a href="<?php echo esc_url( get_term_link( $term_id ) ); ?>" alt="<?php echo esc_attr( sprintf( __( 'View all post filed under %s', 'cataloggi' ), $term_name ) ); ?>">
<?php echo $term_name; ?>
</a>
        </div>
        
    </li>
            
  </ul>
</div><!--/ cataloggi-items -->
<?php 

echo '<hr class="cataloggi-hr">';

} // if end Display Categories

?>