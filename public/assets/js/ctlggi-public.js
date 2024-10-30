(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	// reload content without refresh
	function reload_content_without_refresh() {     
		//alert("Boom!");
		$('body').load(window.location.href,'body');
		return false;
	};
	
	 

})( jQuery );


function htmlLoaded() {
    // Some functions work only after window load 
	  jQuery.ajax({
		type:"POST",
		//dataType: 'json',
		url: ajaxurl, // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		data: {action: 'load_css_after_page_load', formData:'ok'},
			success:function(data){
				// execute function after .. sec
				//setTimeout(reload_content_without_refresh, 500);
				
				setTimeout(function()
				{
					//alert(data);
					alert('Bingo!');
				}, 
				1000);
				
			}
	  });
}

//jQuery(window).load(htmlLoaded); // called on page load

jQuery(document).ready(function() { 

htmlLoaded();

});

/*
jQuery(document).ready(function() { 
  $(window).on('load', function () {
     //insert all your ajax callback code here. 
     //Which will run only after page is fully loaded in background.
	 //alert('Bingo!');
	 
	  jQuery.ajax({
		type:"POST",
		//dataType: 'json',
		url: ajaxurl, // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		data: {action: 'load_css_after_page_load', formData:'ok'},
			success:function(data){
				// execute function after .. sec
				//setTimeout(reload_content_without_refresh, 500);
				
				setTimeout(function()
				{
					//alert(data);
					alert('Bingo!');
				}, 
				1000);
				
			}
	  });
	 
  });
});
*/


