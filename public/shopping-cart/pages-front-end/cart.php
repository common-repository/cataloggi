<div class="cataloggi-wrapper">	

<div class="cataloggi-row">

<div class="cataloggi-col-8">

<?php 
/*
	echo '<div class="breadcrumbs">';
	
	// catalog home
	$cataloggihome = home_url() . '/cataloggi/';
	echo '<a href="' . esc_url( $cataloggihome ) . '" alt="' . esc_attr( __( 'Home', 'cataloggi' ) ) .'">' . __( 'Home', 'cataloggi' ) .'</a> > ';
	// cart
	echo 'Cart';
	
	echo '</div>';
*/
?>

<?php 
// get page is only for custom cart
if ( isset($_GET['page'] ) ) {
	//echo 'action';
if($_GET['page'] == "cart")
{ 

echo do_shortcode('[ctlggi_cart]'); 

}
}
?>

</div><!--/ col -->

<div class="cataloggi-col-4">	

<div class="cataloggi-sidebar">

<?php echo do_shortcode('[ctlggi_cart_totals]'); ?>

</div><!--/ cataloggi-sidebar -->

</div><!--/ col -->

</div><!--/ row -->

</div><!--/ cataloggi-wrapper -->