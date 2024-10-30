                    <!-- Cataloggi -->
                    <div class="cataloggi-row">
                       <div class="cataloggi-col-6">	
                         <?php echo do_shortcode('[ctlggi_breadcrumbs]'); ?>
                       </div>
                       <div class="cataloggi-col-6">	
                         <?php echo do_shortcode('[ctlggi_grid_or_list_view]'); ?>    
                       </div>
                    </div>
                    <?php  
					  // drop down navigation
					  echo do_shortcode('[ctlggi_categories_drop_down_nav]');
                      echo do_shortcode('[ctlggi_sub_category_boxes]');
                      echo do_shortcode('[ctlggi_products]');
                    ?>
                    <!--/ Cataloggi -->