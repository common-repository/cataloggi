<?php 

 $custom_query = new WP_Query( apply_filters( 'ctlggi_products_list', $query_args ) ) ; // <- extensible

 // Display Object
 //CTLGGI_Developer_Mode::display_object( $object=$custom_query );
	
 if( $custom_query->have_posts() ) {
/*		
	if ($termmain == '') {
		$termmain = __( 'All Items', 'cataloggi' );
	} else {
		$termmain = $termmain;
	}
*/	

    do_action( 'ctlggi_produsts_list_before' ); // <- extensible

	// cookie name
	$items_view_cookie_name  = CTLGGI_Cookies::ctlggi_items_view_cookie_name();
		
	// check if cookie exist
	if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$items_view_cookie_name ) === true )
	{		
		// read the cookie
		$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$items_view_cookie_name, $default = '');
		// Items View: Normal, Large or List View
		$itemsview = $cookie;
	
	} else {
	    // default 
	    // $itemsview = 'cataloggi-item-box-grid columns-3';
		// get option
		$itemsview = $ctlggi_general_options['default_items_view'];
		// switch
		$itemsview = CTLGGI_Admin::ctlggi_default_items_view_switch( $itemsview );
	}
	
	?>
<div class="cataloggi-items">
  <ul id="set-cataloggi-grid-view" class="<?php echo esc_attr( $itemsview ); ?>"> <!-- grid view: cataloggi-item-box-grid columns-3 or list view: cataloggi-item-box-list-view -->
	<?php 
	
    while ($custom_query->have_posts()) : $custom_query->the_post(); 
	
	$post_id = get_the_ID();
	
	$hide_product_on_catalog_home = ''; // def
	// check only If it's the custom post type archive page or website(home) || is_home()
	if( is_post_type_archive('cataloggi') )
	{
		// display product on the catalog home page, if value is 1 hide the product
		$hide_product_on_catalog_home  = get_post_meta( get_the_ID(), '_ctlggi_hide_product_on_catalog_home', true );
		if( empty( $hide_product_on_catalog_home ) ) $hide_product_on_catalog_home = '';
	}
	
	if ( $hide_product_on_catalog_home != '1' ) {
	
	// Meta Boxes, Retrieve an existing value from the database.
	$item_regular_price = get_post_meta( get_the_ID(), '_ctlggi_item_regular_price', true );
	$item_price = get_post_meta( get_the_ID(), '_ctlggi_item_price', true );
	$item_currency = get_post_meta( get_the_ID(), '_ctlggi_item_currency', true );
	$item_short_desc = get_post_meta( get_the_ID(), '_ctlggi_item_short_desc', true );
	$product_thumb_image   = get_post_meta( get_the_ID(), '_ctlggi_product_thumb_image', true );
	
	// Set default values.
	if( empty( $item_regular_price ) ) $item_regular_price = '';
	if( empty( $item_price ) ) $item_price = '0';
	if( empty( $item_currency ) ) $item_currency = '';
	if( empty( $item_short_desc ) ) $item_short_desc = '';
	if( empty( $product_thumb_image ) ) $product_thumb_image = '';

	?>

    <li>
    
	<?php 
    $display_item_regular_price_span = ''; // default
	if ( $item_regular_price != '0' && $item_regular_price != '' ) {
		// regular price
		$item_regular_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_regular_price); // return span
		
		// display item price
		if ( $ctlggi_general_options['display_item_price'] == '1' ) {
			$display_item_regular_price_span = '<span class="cataloggi-item-regular-price">' . $item_regular_price_public . '</span>';
		}
	}
	
	// item sale  price
    //$item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price);
    
	// default
	$display_item_sale_price_public_span = '';
    // display item price only if it's enabled in settings
    if ( $ctlggi_general_options['display_item_price'] == '1' ) {
        //$display_item_sale_price_public = $item_sale_price_public;
		$display_item_sale_price_public = CTLGGI_Payment_Buttons::ctlggi_display_item_sale_price_public($post_id, $item_price, $display='first'); // return span
		//$price_label = __( 'From', 'cataloggi' );
		$price_label = '';
		
		$enable_price_options = get_post_meta( get_the_ID(), '_ctlggi_enable_price_options', true );
		// display free text
		if ( $enable_price_options != '1' && $item_price == '0' ) {
			$display_item_sale_price_public_span = '<span class="cataloggi-item-price-free">' . __( 'Free', 'cataloggi' ) . '</span>';
		} else {
			$display_item_sale_price_public_span = '<span class="cataloggi-item-price">' . esc_attr( $price_label ) . ' ' . $display_item_sale_price_public . '</span>';
		}
		
    } else {
        $display_item_sale_price_public = '';
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
        if ( !empty( $product_thumb_image ) ) {
			$thumb_img = '<img src="' . esc_url( $product_thumb_image ) . '"/>';
			?>
			<a href="<?php echo esc_url( the_permalink() ); ?>" alt="<?php echo esc_attr( the_title_attribute() ); ?>">
			<?php echo $thumb_img; ?>
            </a>
			<?php
		}
        // check if has product featured image
        elseif ( has_post_thumbnail() ) {
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
		echo esc_attr( the_title_attribute() );
		?>
        </a>
        </div>
        
        <div class="cataloggi-item-price-holder ">
        <?php echo $display_item_regular_price_span; ?>
		<?php echo $display_item_sale_price_public_span; ?> 
        </div>
        
        <?php 
		$ctlggi_cart_options     = get_option('ctlggi_cart_options');
		$enable_shopping_cart    = isset( $ctlggi_cart_options['enable_shopping_cart'] ) ? sanitize_text_field( $ctlggi_cart_options['enable_shopping_cart'] ) : '';
		$display_payment_button  = isset( $ctlggi_cart_options['display_payment_button'] ) ? sanitize_text_field( $ctlggi_cart_options['display_payment_button'] ) : '';
		
		if ( $enable_shopping_cart == '1' && $display_payment_button == '1' ) {
		?>
            <div class="payment-button cataloggi-text-align-left cataloggi-margin-bottom-5">
            <?php
            $payment_button = array(
              'post_id'            => sanitize_text_field( $post_id ),
              'size'               => sanitize_text_field( 'small' ),
            );
            CTLGGI_Payment_Buttons::shopping_cart_payment_buttons( $payment_button ); // output payment button
            ?>
            </div>
        <?php 
		}
		?>
        
        <div class="item-short-desc ">
        <?php echo esc_attr( $display_item_short_desc ); ?>
        </div>
        
    </li>
 
<?php
	} // end if
    endwhile;
?>
  </ul>
</div><!--/ cataloggi-items -->
<?php 
	
	do_action( 'ctlggi_produsts_list_after' ); // <- extensible
	
	// pagination
	require_once CTLGGI_PLUGIN_DIR . 'public/pages/includes/pagination.php';
	if (function_exists("ctlggi_pagination_function")) {
	  echo '<div class="cataloggi-pagination-holder">';
	  ctlggi_pagination_function($custom_query->max_num_pages);
	  echo '</div>';
	}
	
	
 }

// clean up after the query and pagination
wp_reset_postdata(); 

?>