<?php 
// defaults
$basketCount = '0';

$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';

	// cookie name
	$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
	// check if cookie exist
	if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) 
	{	
		// read the cookie
		$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');
	
		$arr_cart_items = json_decode($cookie, true); // convert to array
		$obj_cart_items = json_decode($cookie); // convert to object
		
		// if cart has contents
		if(count($arr_cart_items)>0)
		{
			$basketCount = count($arr_cart_items); // count array
		}
  }
?>

<div class="cataloggi-shopping-basket">
<a href="<?php echo esc_url( $cataloggiurl . '?page=cart' ); ?>"> 
<i class="glyphicon glyphicon-shopping-cart basket-icon"></i> &nbsp; 
</a>
<span class="basket-text">
<input type="hidden" class="input-ctlggi-arr-cart-items" name="ctlggi-arr-cart-items" value="<?php echo esc_attr( $basketCount ); ?>"/> <!-- for jQuery -->
<input type="hidden" class="input-ctlggi-basket-items" name="ctlggi-basket-items" value="<?php echo esc_attr( $basketCount ); ?>"/> <!-- for jQuery -->
<span class="cataloggi-shopping-basket-items"><?php echo esc_attr( $basketCount ); ?></span>
 <?php _e( 'item(s) in my ', 'cataloggi' ); ?>  
 <a class="cataloggi-shopping-basket-link" href="<?php echo esc_url( $cataloggiurl . '?page=cart' ); ?>"> <?php _e( 'cart', 'cataloggi' ); ?> </a>
</span>
</div>