
<?php 
do_action( 'ctlggi_sidebar_search_form_before' ); // <- extensible
?>

<div class="cataloggi-boxes">
<!--<div class="cataloggi-boxes-title"></div>-->

<?php echo do_shortcode('[ctlggi_search]'); ?>

</div>

<?php 
do_action( 'ctlggi_sidebar_search_form_after' ); // <- extensible
?>