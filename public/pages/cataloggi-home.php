
<?php 

$catalog_home_page = '0';

// get options
$ctlggi_general_options = get_option('ctlggi_general_options');

// get options
$ctlggi_cart_options = get_option('ctlggi_cart_options');
	
// if shopping cart option enabled
if ( isset($_GET['page'] ) && $ctlggi_cart_options['enable_shopping_cart'] == '1' ) {
	// sub pages ()shopping cart	
  if($_GET['page'] == "cart") { 
	$page_id = $ctlggi_cart_options['cart_page'];
	// check if value not 0 (0 value defined as default in class-cwctlg-activator.php)
	if ( $page_id != '0' && ! empty($page_id) ) {
		// get page link by post id
		$page_link = get_permalink( $page_id );
		// if exist redirect to page
		wp_redirect( $page_link, 302 );
		exit();
	} else {
		//echo 'Postname ' . $post_name . ' NOT exist.';
		require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/cart.php';
	}
	
  } elseif($_GET['page'] == "checkout") { 
	$page_id = $ctlggi_cart_options['checkout_page'];
	// check if value not 0 (0 value defined as default in class-cwctlg-activator.php)
	if ( $page_id != '0' && ! empty($page_id) ) {
		// get page link by post id
		$page_link = get_permalink( $page_id );
		// if exist redirect to page
		wp_redirect( $page_link, 302 );
		exit();
	} else {
		//echo 'Postname ' . $post_name . ' NOT exist.';
		require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/checkout.php';
	}
	
  }	else if($_GET['page'] == "payments") { 
	    /*
		$page_id = $ctlggi_cart_options['payments_page'];
		// check if value is not 0 
		if ( $page_id != '0' && ! empty($page_id) ) {
			// get page link by post id
			$page_link = get_permalink( $page_id );
			// if exist redirect to page
			wp_redirect( $page_link, 302 );
			exit();
		} else {
			echo do_shortcode('[ctlggi_payments_totals]');
			echo do_shortcode('[ctlggi_payments]');
		}
		*/
        require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/payments.php';
	} else {
	// home page
	$catalog_home_page = '1';
	//require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-home.php';
  }
	
} else {
	// DEFAULT - home page
	$catalog_home_page = '1';
	//require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-home.php';
}

if ( $catalog_home_page == '1' ) {

?>

<div class="cataloggi-wrapper">	

<div class="cataloggi-row">

<div class="cataloggi-col-9">	

<div class="cataloggi-row">
 <div class="cataloggi-col-6">	
 <?php echo do_shortcode('[ctlggi_breadcrumbs]'); ?>
 </div><!--/ col -->

 <div class="cataloggi-col-6">	
 <?php echo do_shortcode('[ctlggi_grid_or_list_view]'); ?>    
 </div><!--/ col --> 

</div><!--/ row -->

<?php

// drop down navigation
//echo do_shortcode('[ctlggi_categories_drop_down_nav]');

do_action( 'ctlggi_main_categories_before' ); // <- extensible

echo do_shortcode('[ctlggi_home_category_boxes]');

do_action( 'ctlggi_main_categories_after' ); // <- extensible

do_action( 'ctlggi_all_items_before' ); // <- extensible
  
echo do_shortcode('[ctlggi_home_products]');
	
?>

</div><!--/ col -->

<div class="cataloggi-col-3">	

<div class="cataloggi-sidebar">	

<?php 
echo do_shortcode('[ctlggi_sidebar_basket]');
echo do_shortcode('[ctlggi_sidebar_search]');
echo do_shortcode('[ctlggi_sidebar_nav]');
?>

</div><!--/ cataloggi-sidebar -->

</div><!--/ col -->

</div><!--/ row -->

</div><!--/ cataloggi-wrapper -->

<?php 
}
?>