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
	echo 'Checkout';
	
	echo '</div>';
    */
?>

<?php 
if ( isset($_GET['page'] ) ) {
	if($_GET['page'] == "checkout")
	{
	   echo do_shortcode('[ctlggi_checkout]'); 
	}
}
?>

</div><!--/ col -->

<div class="cataloggi-col-4">	

<div class="cataloggi-sidebar">

<?php echo do_shortcode('[ctlggi_checkout_totals]'); ?>

</div><!--/ cataloggi-sidebar -->

</div><!--/ col -->

</div><!--/ row -->

</div><!--/ cataloggi-wrapper -->