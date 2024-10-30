<?php 


#### If Pagination not working on sub domains #####
// go to Settings then Reading and in the "Blog pages show at most" field I changed the value from '10' to '6' 

function ctlggi_pagination_function($pages, $range = 4)
{  
     $showitems = ($range * 2)+1;  
	 //$showitems = $range +1; 
 
     global $paged, $wp_query;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         //global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }  
	
	/*
	echo '<pre>';
	print_r($pages);
	echo '</pre>';
    */
	
     if(1 != $pages)
     {
         echo "<div class=\"cataloggi-pagination\"><span>Page ".esc_attr( $paged )." &nbsp; of &nbsp; ". esc_attr( $pages )."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='". esc_url( get_pagenum_link(1) ) ."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='". esc_url( get_pagenum_link($paged - 1) ) ."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".esc_attr( $i )."</span>":"<a href='". esc_url( get_pagenum_link($i) ) ."' class=\"inactive\">".esc_attr( $i )."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"". esc_url( get_pagenum_link($paged + 1) ) ."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='". esc_url( get_pagenum_link($pages) ) ."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}


?>