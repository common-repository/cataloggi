<?php 

// check if not empty
if ( isset($_GET['s']) && !empty($_GET['s']) ) {
	
  // get options
  $ctlggi_general_options = get_option('ctlggi_general_options'); 
	
	/*
	echo '<pre>';
	print_r($custom_query);
	echo '</pre>';
	*/

	// cookie name
	$items_view_cookie_name  = CTLGGI_Cookies::ctlggi_items_view_cookie_name();
	
	// Items View: Normal, Large or List View 
    // check if cookie exist
    if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$items_view_cookie_name ) === true ) 
    {	
		// read the cookie
		$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$items_view_cookie_name, $default = '');
		$itemsview = $cookie;
	
	} else {
	    // default 
	    // $itemsview = 'cataloggi-item-box-grid columns-3';
		// get option
		$itemsview = $ctlggi_general_options['default_items_view'];
		// switch
		$itemsview = CTLGGI_Admin::ctlggi_default_items_view_switch( $itemsview );
	}

if ( have_posts() ) {
?>
<div class="cataloggi-items">
  <ul id="set-cataloggi-grid-view" class="<?php echo esc_attr( $itemsview ); ?>"> <!-- grid view: cataloggi-item-box-grid columns-3 or list view: cataloggi-item-box-list-view -->
<?php 
while ( have_posts() ) : the_post();

	// Meta Boxes, Retrieve an existing value from the database.
	$item_regular_price = get_post_meta( get_the_ID(), '_ctlggi_item_regular_price', true );
	$item_price = get_post_meta( get_the_ID(), '_ctlggi_item_price', true );
	$item_currency = get_post_meta( get_the_ID(), '_ctlggi_item_currency', true );
	$item_short_desc = get_post_meta( get_the_ID(), '_ctlggi_item_short_desc', true );
	
	// Set default values.
	if( empty( $item_regular_price ) ) $item_regular_price = '';
	if( empty( $item_price ) ) $item_price = '0';
	if( empty( $item_currency ) ) $item_currency = '';
	if( empty( $item_short_desc ) ) $item_short_desc = '';

	?>

    <li>
    
	<?php 
	
	// regular price
    $item_regular_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_regular_price); // return span (HTML)
    
    // display item price
    if ( $ctlggi_general_options['display_item_price'] == '1' ) {
        $display_item_regular_price = $item_regular_price_public;
    } else {
        $display_item_regular_price = '';
    }
	
	// item sale  price
    $item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span (HTML)
    
    // display item price
    if ( $ctlggi_general_options['display_item_price'] == '1' ) {
        $display_item_sale_price = $item_sale_price_public;
    } else {
        $display_item_sale_price = '';
    }
    
    // Display Short Description
    if ( $ctlggi_general_options['display_item_short_desc'] == '1' ) {
        $display_item_short_desc = $item_short_desc; // get meta box data
    } else {
        $display_item_short_desc = '';
    }
    
    // display item thumb image
    if ( $ctlggi_general_options['display_item_thumb_img'] == '1' ) {
    
     echo '<div class="thumb-container">';
     
        // check if has thumb
        if ( has_post_thumbnail() ) {
            // custom image size
			?>
			<a href="<?php echo esc_url( the_permalink() ); ?>" alt="<?php echo esc_attr( the_title_attribute() ); ?>">
			<?php the_post_thumbnail( 'cataloggi-item-thumb' ); ?>
            </a>
			<?php
        } else {
            $src = plugins_url( '/cataloggi/public/assets/images/no-image.jpg');
            $thumbimg = '<img src="' . esc_url( $src ) . '"/>';
			//echo '<a href="' . esc_url( the_permalink() ) . '" alt="' . esc_attr( the_title_attribute() ) . '">';
			?>
			<a href="<?php echo esc_url( the_permalink() ); ?>" alt="<?php echo esc_attr( the_title_attribute() ); ?>"><?php echo $thumbimg; ?></a>
			<?php
			
        }
        
      echo '</div>';

    }
    
    ?>
       
        
        <div class="item-title">
        <a href="<?php echo esc_url( the_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( sprintf( __( '%s', 'cataloggi' ), the_title_attribute() ) ); ?>">
        <?php 
		//echo CTLGGI_Public::ctlggi_shorten_title('...', 7); 
		echo the_title_attribute();
		?>
        </a>
        </div>
        
        <div class="cataloggi-item-price-holder">
        <span class="cataloggi-item-regular-price"><?php echo $display_item_regular_price; ?></span>
		<?php echo $display_item_sale_price; ?> 
        </div>
        
        <div class="item-short-desc">
        <?php echo esc_attr( $display_item_short_desc ); ?>
        </div>
        
    </li>

<?php 
endwhile;

?>
  </ul>
  
</div><!--/ cataloggi-items -->

<?php
} // end if
else {
echo '<div class="cataloggi-no-results-found">' . __( 'No results were found.', 'cataloggi' ) . '</div>';
}


} else {
	echo '<div class="cataloggi-no-results-found">' . __( 'No results were found.', 'cataloggi' ) . '</div>';
}

// clean up after the query and pagination
wp_reset_postdata(); 

?>