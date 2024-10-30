<!-- Cataloggi -->                  
<?php 
// Cataloggi Archive
$catalog_home_page = '0'; // default
$ctlggi_general_options = get_option('ctlggi_general_options');  
$ctlggi_cart_options    = get_option('ctlggi_cart_options');
if ( isset($_GET['page'] ) && $ctlggi_cart_options['enable_shopping_cart'] == '1' ) {
	// sub pages ()shopping cart	
	if($_GET['page'] == "cart") { 
		$page_id = $ctlggi_cart_options['cart_page'];
		// check if value is not 0
		if ( $page_id != '0' && ! empty($page_id) ) {
			// get page link by post id
			$page_link = get_permalink( $page_id );
			// if exist redirect to page
			wp_redirect( $page_link, 302 );
			exit();
		} else {
			echo do_shortcode('[ctlggi_cart]');
			echo do_shortcode('[ctlggi_cart_totals]');
		} 
	} else if($_GET['page'] == "checkout") { 
		$page_id = $ctlggi_cart_options['checkout_page'];
		// check if value is not 0 
		if ( $page_id != '0' && ! empty($page_id) ) {
			// get page link by post id
			$page_link = get_permalink( $page_id );
			// if exist redirect to page
			wp_redirect( $page_link, 302 );
			exit();
		} else {
			echo do_shortcode('[ctlggi_checkout_totals]');
			echo do_shortcode('[ctlggi_checkout]');
		}
	} 
	else if($_GET['page'] == "payments") { 
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
		echo do_shortcode('[ctlggi_payments_totals]');
		echo do_shortcode('[ctlggi_payments]');
	} 
	else {
		// home page
		$catalog_home_page = '1';
	}
} else {
	// default - home page
	$catalog_home_page = '1';
}
	if ( $catalog_home_page == '1' ) {    
		echo '<div class="cataloggi-row">';
		echo '<div class="cataloggi-col-6">';
		echo do_shortcode('[ctlggi_breadcrumbs]');
		echo '</div>';
		echo '<div class="cataloggi-col-6">';
		echo do_shortcode('[ctlggi_grid_or_list_view]');    
		echo '</div>';
		echo '</div>';
		
		// drop down navigation
		echo do_shortcode('[ctlggi_categories_drop_down_nav]');
		
		echo do_shortcode('[ctlggi_home_category_boxes]');
		echo do_shortcode('[ctlggi_home_products]');
    }
?>
<!--/ Cataloggi -->