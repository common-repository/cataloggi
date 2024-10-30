
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
echo do_shortcode('[ctlggi_categories_drop_down_nav]');

echo do_shortcode('[ctlggi_search_results]'); 

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