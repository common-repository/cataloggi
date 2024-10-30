    <!-- search-form -->
    <div class="cataloggi-search-form">
    <form role="search" id="quicksearchform"  method="get" action="<?php echo esc_attr( home_url('/') ); // use home_url ?>">     
    <input type="hidden" name="post_type" value="cataloggi" /> <!-- // hidden should include post_type 'cataloggi' value -->          
    <span class="fieldsgroup">
    <input type="text" name="s" placeholder="Search Products"/>
    <!--<input type="submit" value="Go" id="submit-button" >-->
    </span>
    </form>
    </div>
    <!-- search-form end -->