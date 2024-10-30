
<?php 

do_action( 'ctlggi_sidebar_categories_nav_before' ); // <- extensible

?>

<div class="cataloggi-navigation">

<?php echo do_shortcode('[ctlggi_categories_nav]'); ?>

</div>

<?php 
do_action( 'ctlggi_sidebar_categories_nav_after' ); // <- extensible
?>