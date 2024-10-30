<div class="cataloggi-single-item">	

<div class="cataloggi-shopping-cart-title"><?php _e( 'Shopping Cart', 'cataloggi' ); ?></div>

        <!-- jQuery message 
        <div class="show-update-button-return-data"></div>-->

<?php 

// defaults
$total = '0';
$price_in_total = '0';

$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';

  // cookie name
  $cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
  
  // check if cookie exist
  if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_totals_cookie_name ) === true ) 
  {	
			
	// read the cookie
	$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_totals_cookie_name, $default = '');
	
	$arr_cart_totals = json_decode($cookie, true); // convert to array
	$obj_cart_totals = json_decode($cookie); // convert to object

	// Display Object
	CTLGGI_Developer_Mode::display_object( $object=$obj_cart_totals );
	
	// if cart has contents
	if(count($obj_cart_totals)>0)
	{
		$subtotal = $obj_cart_totals->subtotal;
		$total    = $obj_cart_totals->total;
	}
  }
	  
  // cookie name
  $cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();

  // check if cookie exist
  if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) 
  {
	// read the cookie
	$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');

	$arr_cart_items = json_decode($cookie, true); // convert to array
	$obj_cart_items = json_decode($cookie); // convert to object

	// Display Object
	CTLGGI_Developer_Mode::display_object( $object=$obj_cart_items );
	
	// if cart has contents
	if(count($obj_cart_items)>0)
	{	
	
	// get options
	$ctlggi_general_options = get_option('ctlggi_general_options');
	
	// get options
	$ctlggi_currency_options = get_option('ctlggi_currency_options');
	
	$thousand_separator     = sanitize_text_field( stripslashes( $ctlggi_currency_options['thousand_separator'] ) );
			
?>	

<input type="hidden" class="input-ctlggi-thousand-separator" name="ctlggi-thousand-separator" value="<?php echo esc_attr( $thousand_separator ); ?>"/>

<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="ctlggi-cart-table">

<thead>
  <tr>
    <th class="cataloggi-uppercase" colspan="2"><?php _e( 'Product', 'cataloggi' ); ?></th>
    <th class="cataloggi-uppercase"><?php _e( 'Price', 'cataloggi' ); ?></th>
    <th class="cataloggi-uppercase"><?php _e( 'Quantity', 'cataloggi' ); ?></th>
    <th class="cataloggi-uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
    <th><?php _e( 'Refresh', 'cataloggi' ); ?></th>
    <th><?php _e( 'Delete', 'cataloggi' ); ?></th>
    </tr>
</thead>

<tbody>				
<?php 			
		
	  foreach($obj_cart_items as $key => $value)
	  {
			/*
			echo '<pre>';
			print_r( $value );
			echo '</pre>';
            */
			
			$custom_field      = isset( $value->custom_field ) ? sanitize_text_field( $value->custom_field ) : '';
			$grouped_products  = isset( $value->grouped_products ) ? sanitize_text_field( $value->grouped_products ) : '';
			
			// subsc data
			$subsc_recurring      = isset( $value->subsc_recurring ) ? sanitize_text_field( $value->subsc_recurring ) : '';
			$subsc_interval       = isset( $value->subsc_interval ) ? sanitize_text_field( $value->subsc_interval ) : '';
			$subsc_interval_count = isset( $value->subsc_interval_count ) ? sanitize_text_field( $value->subsc_interval_count ) : '';
			$subsc_times          = isset( $value->subsc_times ) ? sanitize_text_field( $value->subsc_times ) : '';
			$subsc_signupfee      = isset( $value->subsc_signupfee ) ? sanitize_text_field( $value->subsc_signupfee ) : '';
			$subsc_trial          = isset( $value->subsc_trial ) ? sanitize_text_field( $value->subsc_trial ) : '';
			
			$item_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$value->item_price); // return span (HTML)
			$item_price        = CTLGGI_Amount::ctlggi_amount_hidden($amount=$value->item_price); // return string
			
			// Quantity Field
			$enable_quantity_field = get_post_meta( $value->item_id, '_ctlggi_enable_quantity_field', true ); 
			if( empty( $enable_quantity_field ) ) $enable_quantity_field = '0';
			
			// defaults
			$display_price_option = '';
			$price_option_id      = '';	
			$price_option_name    = '';	
			if ( ! empty($value->price_option_id) && ! empty($value->price_option_name) ) {
				$price_option_id      = $value->price_option_id;
				$price_option_name    = $value->price_option_name;
				$display_price_option = '<span style="display:block;">' . esc_attr( $value->price_option_name ) . '</span>';
			}
			

?>	
  <tr class="ctlggi-cart-item">
    <td><div class="cataloggi-cart-thumb-img">
	<?php 
	// source: https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/
	echo get_the_post_thumbnail( $value->item_id, 'thumbnail' ); // item id is the page ID 
	//echo get_the_post_thumbnail( $value->item_id, array( 200, 150) ); // Other resolutions
	?>
    </div></td>
    <td data-title="<?php _e( 'Product', 'cataloggi' ); ?>">
    <input type="hidden" class="input-ctlggi-price-option-id" name="ctlggi-price-option-id" value="<?php echo esc_attr( $price_option_id ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-price-option-name" name="ctlggi-price-option-name" value="<?php echo esc_attr( $price_option_name ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-item-name" name="ctlggi-item-name" value="<?php echo esc_attr( $value->item_name ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-item-downloadable" name="ctlggi-item-downloadable" value="<?php echo esc_attr( $value->item_downloadable ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-item-payment-type" name="ctlggi-item-payment-type" value="<?php echo esc_attr( $value->item_payment_type ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-custom-field" name="ctlggi-custom-field" value="<?php echo esc_attr( $custom_field ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-grouped-products" name="ctlggi-grouped-products" value="<?php echo esc_attr( $grouped_products ); ?>"/> <!-- for jQuery -->
    
    <input type="hidden" class="input-ctlggi-subsc-recurring" name="ctlggi-subsc-recurring" value="<?php echo esc_attr( $subsc_recurring ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-subsc-interval" name="ctlggi-subsc-interval" value="<?php echo esc_attr( $subsc_interval ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-subsc-interval-count" name="ctlggi-subsc-interval-count" value="<?php echo esc_attr( $subsc_interval_count ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-subsc-times" name="ctlggi-subsc-times" value="<?php echo esc_attr( $subsc_times ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-subsc-signupfee" name="ctlggi-subsc-signupfee" value="<?php echo esc_attr( $subsc_signupfee ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-subsc-trial" name="ctlggi-subsc-trial" value="<?php echo esc_attr( $subsc_trial ); ?>"/> <!-- for jQuery -->
    
	<?php echo esc_attr( $value->item_name ); ?>
    <?php echo $display_price_option; // span ?>
    </td>
    <td data-title="<?php _e( 'Price', 'cataloggi' ); ?>"> 
    <input type="hidden" class="input-ctlggi-item-id" name="ctlggi-item-id" value="<?php echo esc_attr( $value->item_id ); ?>"/> <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-item-price" name="ctlggi-item-price" value="<?php echo esc_attr( $item_price ); ?>"/> <!-- for jQuery -->
	<?php echo $item_price_public; // span ?>
    </td>
    <td data-title="<?php _e( 'Quantity', 'cataloggi' ); ?>">
    <div class="cataloggi-item-quantity">
     <?php if ( $enable_quantity_field == '1' ) { ?>
         <input class="input-ctlggi-item-quantity" type="number" max="" min="1" value="<?php echo esc_attr( $value->item_quantity ); ?>" name="ctlggi-item-quantity" >
     <?php } 
	       else { 
		   // if no quantity enabled set value to 1
		   ?>
           <input type="hidden" class="input-ctlggi-item-quantity" value="1" name="ctlggi-item-quantity" >
           <?php
		   echo esc_attr( $value->item_quantity ); 
		   } 
	 ?>
    </div>
    </td>
    <td data-title="<?php _e( 'Total', 'cataloggi' ); ?>"> 
	<?php 
	// calculate total for single item
	$single_item_total = $item_price * $value->item_quantity;
	
    $item_price_public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$single_item_total); // return span (HTML)
    $item_price_total        = CTLGGI_Amount::ctlggi_amount_hidden($amount=$single_item_total); // return string
    
	// make sure it is a subscription before display the data
    if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
		$itemdata = json_encode($value); // value from foreach
		$display_subsc_details_public = apply_filters( 'ctlggi_cart_display_subsc_data', $itemdata );
	} else {
		$display_subsc_details_public = '';
	}
	
	?>
    <input type="hidden" class="input-ctlggi-single-item-total" name="ctlggi-single-item-total" value="<?php echo esc_attr( $item_price_total ); ?>"/> <!-- for jQuery -->
    <span class="html-ctlggi-single-item-total"><?php echo $item_price_public_total; ?></span>
    <?php echo $display_subsc_details_public; ?>
    </td>
    <td>
    <a class="ctlggi-update-cart btn-cataloggi btn-cataloggi-xs btn-cataloggi-orange" id="ctlggi-update-cart" href="/" onclick="return false;"> <i class="glyphicon glyphicon-refresh"></i> </a>
    </td>	
    <td> 
    
    <form action="" method="post" id="<?php echo esc_attr( $value->item_id ); ?>" class="ctlggi-remove-from-cart-form" data-item-id="<?php echo esc_attr( $value->item_id ); ?>">
    
    <input type="hidden" name="ctlggi-remove-from-cart-form-nonce" value="<?php echo wp_create_nonce('ctlggi_remove_from_cart_form_nonce'); ?>"/>
    <input type="hidden" name="ctlggi_item_id" value="<?php echo esc_attr( $value->item_id ); ?>"/>
    <input type="hidden" name="ctlggi_item_arr_key" value="<?php echo esc_attr( $key ); ?>"/>
    
    <button class="remove-from-cart-button btn-cataloggi btn-cataloggi-xs btn-cataloggi-orange" name="ctlggi-remove-from-cart-form-submit"  type="submit"> 
    <i class="glyphicon glyphicon-remove"></i>
    </button>
    
    </form>
    </td>
  </tr>				
<?php 
					
	  }
				
?>	
  <tr>
    <td colspan="7"> 
        <!-- jQuery message -->
        <div class="show-return-data"></div>
    </td>
    </tr>
  
</tbody>

</table>

</div>
<!-- table-responsive end -->				
<?php 	
				
    }
	  
  } 
  else 
  {
	//echo '<strong>No products found</strong> in your cart!';
	//echo 'Your cart is currently empty.';
	_e( 'Your cart is currently empty.', 'cataloggi' );
	echo '<a class="btn-cataloggi btn-cataloggi-xs btn-cataloggi-orange cataloggi-float-right" href="' . esc_url( $cataloggiurl ) . '"> ' . __( '< Return to Shop', 'cataloggi' ) . ' </a>';
  }


?>
</div><!--/ cataloggi-single-item -->