
<?php 

// get options
$ctlggi_general_options = get_option('ctlggi_general_options');

// get options
$ctlggi_cart_options = get_option('ctlggi_cart_options');

// if shopping cart enabled
if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) {

do_action( 'ctlggi_sidebar_basket_before' ); // <- extensible 
?>

<div class="cataloggi-boxes">

<div class="cataloggi-shopping-basket-holder">
<?php 
$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';
?>

<?php echo do_shortcode('[ctlggi_basket]'); ?>


</div>

</div>

<?php 
do_action( 'ctlggi_sidebar_basket_after' ); // <- extensible

}
?>