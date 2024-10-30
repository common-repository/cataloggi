<div class="cataloggi-boxes">

<?php 

  // defaults
  $total    = '0';
  $subtotal = '0';
  
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
		//$total    = '0'; // test
	}
  }
?>

<div class="cataloggi-padding-left-right-15">
<div class="cataloggi-boxes-title font-size-16"><?php _e( 'Cart Totals', 'cataloggi' ); ?></div>

<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="cwtable">

<tbody>

  <tr>
    <td class="cataloggi-uppercase font-size-20"><?php _e( 'Total', 'cataloggi' ); ?></td>
    <td class="font-size-20">
	<?php 
	
    $public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return span
    $hidden_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return span
	
	?>
    <input type="hidden" class="input-ctlggi-items-price-in-total" name="ctlggi-items-price-in-total" value="<?php echo $hidden_total; ?>"/> <!-- for jQuery -->
    <div class="html-ctlggi-items-price-in-total"><?php echo $public_total; ?></div>
    </td>
  </tr>   
  
</tbody>

</table>

    <div class="cataloggi-margin-top-15">
    <a class="ctlggi-update-cart btn-cataloggi btn-cataloggi-md btn-cataloggi-silver cataloggi-width-90" id="ctlggi-update-cart" href="<?php echo esc_url( '/' ); ?>" onclick="return false;"> <?php _e( 'Update Cart', 'cataloggi' ); ?> </a>
    </div>
    
    <div>
    <a class="btn-cataloggi btn-cataloggi-md btn-cataloggi-orange cataloggi-width-90" id="ctlggi-proceed-to-checkout"  href="<?php echo esc_url( $cataloggiurl . '?page=checkout' ); ?>"> <?php _e( 'Proceed to Checkout', 'cataloggi' ); ?> </a>
    </div>
    
    <div>
    <a class="btn-cataloggi btn-cataloggi-md btn-cataloggi-silver cataloggi-width-90" id="ctlggi-continue-shopping"  href="<?php echo esc_url( $cataloggiurl ); ?>"> <?php _e( 'Continue Shopping', 'cataloggi' ); ?> </a>
    </div>


</div>
<!-- table-responsive end -->

</div><!--/ cataloggi-padding-left-right-15 -->

</div><!--/ cataloggi-boxes -->