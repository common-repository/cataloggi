
<?php 

// get options
$ctlggi_general_options = get_option('ctlggi_general_options'); 

$categories_drop_down_nav  = isset( $ctlggi_general_options['categories_drop_down_nav'] ) ? sanitize_text_field( $ctlggi_general_options['categories_drop_down_nav'] ) : '';

if ( $categories_drop_down_nav == '1' ) {
	
	/** The taxonomy we want to parse */
	$taxonomy = "cataloggicat";
	/** Get all taxonomy terms */
	$terms = get_terms( array(
		'taxonomy' => $taxonomy,
		'parent' => 0,
		'hide_empty' => true, // if term category empty 
		'order' => $ctlggi_general_options['parent_menu_order'], // 'ASC'
		'orderby' => $ctlggi_general_options['parent_menu_order_by'], // modified | title | name | ID | rand
	) );
	/** Get terms that have children */
	$hierarchy = _get_term_hierarchy($taxonomy);
	
	/*
	echo '<pre>';
	print_r($terms);
	echo '</pre>';
	*/

?>

<div class="cataloggi-row">
  <div class="cataloggi-col-6">
    <!-- cw-form start -->
    <div class="cw-form">
    <select class="ctlggi_categories_drop_down_class" name="ctlggi_categories_drop_down" id="ctlggi_categories_drop_down">
        <option selected="selected" value=""><?php _e('Categories ...', 'cataloggi'); ?></option>
		<?php
            /** Loop through every term */
            foreach($terms as $term) {
                /** Skip term if it has children */
                if($term->parent) {
                    continue;
                }
                echo '<option value="' . esc_url( get_term_link( $term ) ) . '">' . esc_attr( $term->name ) . ' </option>';
                /** If the term has children... */
                if($hierarchy[$term->term_id]) {
                    /** ...display them */
                    foreach($hierarchy[$term->term_id] as $child) {
                        /** Get the term object by its ID */
                        $child = get_term($child, "cataloggicat");
                        echo '<option value="' . esc_url( get_term_link( $child ) ) . '"> - ' . esc_attr( $child->name ) . ' </option>';
                    }
                }
            }
        ?>
    </select>
    </div>
    <!-- cw-form end -->
  </div>

</div>

<?php 
wp_reset_query();
}
?>
