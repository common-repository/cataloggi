<div class="cataloggi-single-item">			

<?php 

// get options
$ctlggi_cart_options = get_option('ctlggi_cart_options');

$ctlggi_general_options = get_option('ctlggi_general_options');
$product_view_featured_image  = isset( $ctlggi_general_options['product_view_featured_image'] ) ? sanitize_text_field( $ctlggi_general_options['product_view_featured_image'] ) : '1';

// the_content() should be inside the loop
if ( have_posts() ) : while ( have_posts() ) : the_post();

	$post_id = get_the_ID();
	$post_info = get_post( $post_id );
	// get options
	$ctlggi_general_options = get_option('ctlggi_general_options');
	
	$item_price = get_post_meta( $post_id, '_ctlggi_item_price', true );
    $item_sku   = get_post_meta( $post_id, '_ctlggi_item_sku', true );
	
	if( empty( $item_price ) ) $item_price = '0';
	if( empty( $item_sku ) ) $item_sku = '';
	
	/*
	echo '<pre>';
	print_r($post_info);
	echo '</pre>';
	
	
	//the_meta();
	$custom_fields  = get_post_meta( $post_id, 'my_custom_fields', false );
	
	
	echo '<pre>';
	print_r($custom_fields);
	echo '</pre>';
    */
	
	// default
	$display_item_sale_price_public_div = '';
    $display_item_sale_price_public = CTLGGI_Payment_Buttons::ctlggi_display_item_sale_price_public($post_id, $item_price, $display='first'); // return span (HTML)

    // display item price only if it's enabled in settings
    if ( $ctlggi_general_options['display_item_price'] == '1' ) {
		$enable_price_options   = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
		
		// display free text
		if ( $enable_price_options != '1' && $item_price == '0' ) {
			//$display_item_sale_price_public_div = '<span class="cataloggi-item-price-free">' . __( 'Free', 'cataloggi' ) . '</span>';
			$display_item_sale_price_public_div = '';
		} else {
			// if price options enabled
			if ( ! empty( $enable_price_options ) && $enable_price_options == '1' ) {
				$price_label = __( 'From', 'cataloggi' );
			} else {
				$price_label = __( 'Price', 'cataloggi' );
			}
				 
			$display_item_sale_price_public_div = '<div class="cataloggi-item-price">' . esc_attr( $price_label ) . ' ' . $display_item_sale_price_public . '</div>';
		}
	}
	
	//get_the_date();
 	?>
    
      <div class="cataloggi-row">
        <div class="cataloggi-col-7">

        <!-- jQuery message -->
        <div class="show-return-data"></div>
        
        <?php do_action( 'ctlggi_single_item_title_before' ); // <- extensible ?>
        	
            <!-- <div class="cataloggi-title"></div> -->
            <h1><?php echo esc_attr( get_the_title( $post_id ) ); ?></h1>
            <?php echo $display_item_sale_price_public_div; ?>        
        </div><!--/ col -->
        
        <div class="cataloggi-col-5">	
        
<!-- shopping cart - add to cart button  -->
<?php 
	// display Add to Cart Button for shopping cart
	if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) {
		
?>
    <div class="ctlggi-text-align-payment-buttons">
        
    <?php
	$payment_button = array(
	  'post_id'            => sanitize_text_field( $post_id ),
	  'size'               => sanitize_text_field( 'normal' ),
	);
    CTLGGI_Payment_Buttons::shopping_cart_payment_buttons( $payment_button ); // output payment button
	?>
        
    </div>
    
    <?php do_action( 'ctlggi_single_item_payment_button_after' ); // <- extensible ?>
		
<?php 
	}
?> 
           
        </div><!--/ col -->
      </div><!--/ row -->
    
    <hr class="cataloggi-hr">
    
    <?php do_action( 'ctlggi_single_item_featured_img_before' ); // <- extensible ?>
    
    <?php 
	if ( $product_view_featured_image == '1' ) {
	?>
    <div class="img-holder">
	<?php 
		// check if has thumb
		if ( has_post_thumbnail() ) {
			// ~~~~~~~~~ source: https://codex.wordpress.org/Post_Thumbnails 
			//echo $thumbimg = the_post_thumbnail( array(600, 550) );
			echo $thumbimg = the_post_thumbnail('full');
		} else {
			$src = plugins_url( '/cataloggi/public/assets/images/no-image.jpg');
			//echo $thumbimg = '<img src="' . $src . '"/>';
		} 
	?>
    </div>
    <?php 
	}
	?>
    
    <?php do_action( 'ctlggi_single_item_featured_img_after' ); // <- extensible ?>
    
    <?php do_action( 'ctlggi_single_item_description_before' ); // <- extensible ?>
    
    <div class="cataloggi-content">
    <div class="cataloggi-description"></div>
	<?php echo the_content(); ?>
    </div>
    
    <?php do_action( 'ctlggi_single_item_description_after' ); // <- extensible ?>
    
    <hr class="cataloggi-hr">
    <?php if ( $item_sku != '' ) { ?> 
      <div class="cataloggi-row">
        <div class="cataloggi-col-6">	
        <?php _e( 'SKU:', 'cataloggi' ); ?> <?php esc_attr_e( $item_sku ); ?> 
        </div><!--/ col -->
      </div><!--/ row -->
    <?php } ?>  
    <br>
    
      <div class="cataloggi-row">
        <div class="cataloggi-col-6">
        &nbsp;	
           <?php 
			// if logged in edit link
	        edit_post_link( __( 'Edit', 'cataloggi' ), '<span class="edit-post">', '</span>' );
		   ?>          
        </div><!--/ col -->
        
        <div class="cataloggi-col-6 cataloggi-back-button">	
        <a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-grey cataloggi-float-right" href="javascript: history.go(-1)"> <?php _e( 'Go Back', 'cataloggi' ); ?> </a>
           
        </div><!--/ col -->
      </div><!--/ row -->

</div><!--/ cataloggi-single-item -->

<?php 
endwhile;
endif;
?>