
<?php 

  $show_hide_checkout_forms = '1';
  
  // PAYPAL
  if ( isset($_REQUEST['page'] ) && $_REQUEST['page'] == "checkout" ) {
	  
	    if ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "processing" ) {
			$show_hide_checkout_forms = '0'; // hide forms
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "completed" ) {
			$show_hide_checkout_forms = '0'; // hide forms
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "pending" ) {
			$show_hide_checkout_forms = '0'; // hide forms
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "failed" ) {
			$show_hide_checkout_forms = '0'; // hide forms
		}
  }
  
  if ( $show_hide_checkout_forms == '1' ) {

?>

<div class="cataloggi-boxes">

<div class="cataloggi-padding-left-right-15">
<div class="cataloggi-boxes-title font-size-16"><?php _e( 'Order', 'cataloggi' ); ?></div>

<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="cwtable">

<thead>
  <tr>
    <th class="cataloggi-uppercase"><?php _e( 'Product', 'cataloggi' ); ?></th>
    <th class="cataloggi-uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
    </tr>
</thead>

<tbody>
<?php

  // defaults
  $total = '0';
  $subtotal = '0';
  $price_in_total = '0';
  
  // cookie name
  $cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
  
  // check if cookie exist
  if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_totals_cookie_name ) === true ) 
  {	
			
	// read the cookie
	$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_totals_cookie_name, $default = '');
	$cart_totals = $cookie;
	
	$arr_cart_totals = json_decode($cart_totals, true); // convert to array
	$obj_cart_totals = json_decode($cart_totals); // convert to object
	
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
	//CTLGGI_Developer_Mode::display_object( $object=$obj_cart_items );
	
	// if cart has contents
	if(count($obj_cart_items)>0)
	{	
	
	  foreach($obj_cart_items as $key=>$value)
	  {
			// Display Object
	        //CTLGGI_Developer_Mode::display_object( $object=$value );
			
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
  <tr>
    <td>
	<?php echo esc_attr( $value->item_name ); ?>  x <?php echo esc_attr( $value->item_quantity ); ?> 
	<?php echo $display_price_option; // span ?>
    </td>
    <td>
    <?php
	
	// calculate total for single item
	$single_item_total = $value->item_price * $value->item_quantity;
	
    $item_price_public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$single_item_total); // return span (HTML)
    $item_price_total        = CTLGGI_Amount::ctlggi_amount_hidden($amount=$single_item_total); // return string

	// make sure it is a subscription before display the data
    if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
		$itemdata = json_encode($value); // value from foreach
		$display_subsc_details_public = apply_filters( 'ctlggi_checkout_totals_display_subsc_data', $itemdata );
	} else {
		$display_subsc_details_public = '';
	}

	echo $item_price_public_total;
	echo $display_subsc_details_public;
	
	?>
    </td>
  </tr> 

<?php 
	  }
	}
  }
?>  
   
  <tr>
    <td colspan="2">
	<?php 
	
    $public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return span (HTML)
    $hidden_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return string
	
	?>
    <input type="hidden" class="input-ctlggi-items-price-in-total" name="ctlggi-items-price-in-total" value="<?php echo esc_attr( $hidden_total ); ?>"/> <!-- for jQuery -->
	<span style="font-size:20px; font-weight: normal; color: rgba(0, 0, 0, 0.45);"><?php _e( 'Total', 'cataloggi' ); ?> </span> 
	<span style="font-size:20px; font-weight: normal;"><?php echo $public_total; // span ?></span>
    </td>
  </tr>   
  
</tbody>

</table>


</div>
<!-- table-responsive end -->

</div><!--/ cataloggi-padding-left-right-15 -->

</div><!--/ cataloggi-boxes -->

<?php 

  }

?>