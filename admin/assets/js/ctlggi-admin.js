(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
$(document).ready(function() {
	
	// tooltip, display dialog box on mouse over
	// source: http://www.codechewing.com/library/create-simple-tooltip-jquery/
	$(".ctlggi-tooltip").hover(function(e) {
		
		var titleText = $(this).attr('title');
		//alert(titleText);
		
		$(this)
		  .data('tiptext', titleText)
		  .removeAttr('title');
		
		$('<p class="ctlggi-tooltip-display"></p>')
		.text(titleText)
		.appendTo('body')
		.css('top', (e.pageY - 10) + 'px')
		.css('left', (e.pageX + 20) + 'px')
		.fadeIn('slow');
		
		}, function(){ // Hover off event
		
		$(this).attr('title', $(this).data('tiptext'));
		$('.ctlggi-tooltip-display').remove();
		
		}).mousemove(function(e){ // Mouse move event
		
		$('.ctlggi-tooltip-display')
		  .css('top', (e.pageY - 10) + 'px')
		  .css('left', (e.pageX + 20) + 'px');
	
	});
	
	
	// downloadable product
	$('.item_downloadable_checkbox').on('change', function() {
		if(this.checked) {
		   //Do stuff
		   //alert('checked');
		   $( "#show_downloadable_table" ).show();
		} else {
		   //alert('unchecked');
		   $( "#show_downloadable_table" ).hide();
		}
	});
	
	// display custom download url field
	$('.item_custom_download_url_checkbox').on('change', function() {
		if(this.checked) {
		   //Do stuff
		   //alert('checked');
		   $( "#display_item_download_from_url" ).show();
		} else {
		   //alert('unchecked');
		   $( "#display_item_download_from_url" ).hide();
		}
	});
	
	// display demo url field
	$('.docs_checkbox_class').on('change', function() {
		if(this.checked) {
		   //Do stuff
		   //alert('checked');
		   $( "#display_docs_url" ).show();
		} else {
		   //alert('unchecked');
		   $( "#display_docs_url" ).hide();
		}
	});
	
	// display documentation url field
	$('.demo_url_checkbox_class').on('change', function() {
		if(this.checked) {
		   //Do stuff
		   //alert('checked');
		   $( "#display_demo_url" ).show();
		} else {
		   //alert('unchecked');
		   $( "#display_demo_url" ).hide();
		}
	});
	
	// order view - order items table, display price options select field
	$(".ctlggi-loading-img").hide();
	$('.ctlggi_order_select_item').on('change', function() {
		// empty div before process
		$('.ctlggi_display_price_options_select_field').empty();
		
		var post_id = $("#order_select_item option:selected").val();
		  $(".ctlggi-loading-img").show();
		  // spinner
		  var formloaderimg = ctlggi_admin_js.ctlggi_form_loader_img; 
		  $('.ctlggi-loading-img').html('<br><img src="' + formloaderimg + '" width="128" height="15" alt="loading..." />');
		  $.ajax({
			type:"POST",
			url: ctlggi_admin_js.ctlggi_admin_wp_ajax_url,
			data: {action: 'ctlggi_admin_data_table_price_options_select_field', formData:post_id},
				success:function(response){
					$(".ctlggi-loading-img").hide();
					var price_option_select_field = response;
				    $('.ctlggi_display_price_options_select_field').show().html(price_option_select_field);// show div and insert data into the div
				}
		  });
		  return false;
	});
	
	// orders add new item
	$('.ctlggi-add-new-item').on('click', function(event) {
		//alert('clicked');
		
		// check if any value selected
		if( $('#order_select_item').val() ){
			
			var price_option_check = '';
			var nonce            = $(".input-ctlggi-data-table-form-nonce").val();
			var post_id          = $("#order_select_item option:selected").val(); // selected item
			var price_option_id  = $("#ctlggi_price_option_selector option:selected").val(); // selected price option id
			
			if ( price_option_id ) {
				if ( price_option_id == 0 ) {
					alert( 'Please Select Price Option' );
				} else {
					price_option_check = 'ok';
				}
			} else {
				price_option_check = 'ok';
				price_option_id = '';
			}
			
			if ( price_option_check == 'ok' ) {
				// process
				//alert( 'Item ID: ' + post_id + ' Price Option ID: ' + price_option_id );
				var post_data = 'post_id=' + post_id + '&price_option_id=' + price_option_id + '&nonce=' + nonce;
				$.ajax({
				type:"POST",
				url: ctlggi_admin_js.ctlggi_admin_wp_ajax_url,
				data: {action: 'ctlggi_admin_data_table_insert_new_item', formData:post_data},
					success:function(response){
						var row = response;
						$('#ctlggi-order-items-table tbody').prepend( row );
					}
				});
				return false;
			}
			
		}

	});
	
	// orders add new custom item
	$('.ctlggi-add-new-custom-item').on('click', function(event) {
		//alert('clicked');
		var nonce            = $(".input-ctlggi-data-table-form-nonce").val();
		//alert( 'Item ID: ' + post_id + ' Price Option ID: ' + price_option_id );
		var post_data = 'nonce=' + nonce;
		$.ajax({
		type:"POST",
		url: ctlggi_admin_js.ctlggi_admin_wp_ajax_url,
		data: {action: 'data_table_insert_new_custom_item', formData:post_data},
			success:function(response){
				var row = response;
				$('#ctlggi-order-items-table tbody').prepend( row );
			}
		});
		return false;

	});
	
	// remove item, DOM is ready
	$("#ctlggi-order-items-table").on("click", ".ctlggi-remove-item", function (event) {
		//alert('clicked');
		var remove = $(this); //Store the context of this in a local variable 
		var itemid  = remove.attr('data-item-id');
		// remove closest row
	    remove.closest("tr").remove(); // remove tr
	});
	
	// This will add thousand separators while retaining the decimal part of a given number
	// 2056776401.50 = 2,056,776,401.50
	function ctlggi_format_price(n) {
	  //n = n.toFixed(2) // always two decimal digits
	  n = n.toString()
	  while (true) {
		//var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3') // origin
		var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3');
		if (n == n2) break
		n = n2
	  }
	  return n
	}
	
	// output 1.234.567,89
	function ctlggi_format_price_DE (n) {
		return n
		   //.toFixed(2) // always two decimal digits
		   .replace(".", ",") // replace decimal point character with ,
		   .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") // use . as a separator
	}
	
	// calculate total
	function calculatetotal() {
		
		var sum = 0;
		$(".input-ctlggi-single-item-total").each(function() {      
			sum += +this.value;
		});
		return sum;
	}
	
	// update total
	$('.ctlggi-update-order-total').on('click', function() {
	//$('.ctlggi-update-order-total').click(function() {
		
		var jsonObj = [];
		
		var currSymbol         = $("input.input-ctlggi-curr-data-symbol").val();
		var currPosition       = $("input.input-ctlggi-curr-position").val();	
		var thousandSeparator  = $("input.input-ctlggi-thousand-separator").val();
		//alert(thousandSeparator);
											 
		// jquery loop through each rows
		$("#ctlggi-order-items-table tr.ctlggi-order-items").each(function() {
																	   
			var $this = $(this);
			
			// item data
			var itemid            = $this.find(".input-ctlggi-item-id").val();
			var price             = $this.find(".input-ctlggi-item-price").val();
			var itemname          = $this.find(".input-ctlggi-item-name").val();
			var quantity          = $this.find(".input-ctlggi-item-quantity").val();
			var downloadable      = $this.find(".input-ctlggi-item-downloadable").val();
			var price_option_id   = $this.find(".input-ctlggi-price-option-id").val();
			var price_option_name = $this.find(".input-ctlggi-price-option-name").val();
			var item_payment_type = $this.find(".input-ctlggi-item-payment-type").val();
			
			// subsc data
			var subsc_recurring      = $this.find(".input-ctlggi-subsc-recurring").val();
			var subsc_interval       = $this.find(".input-ctlggi-subsc-interval").val();
			var subsc_interval_count = $this.find(".input-ctlggi-subsc-interval-count").val();
			var subsc_times          = $this.find(".input-ctlggi-subsc-times").val();
			var subsc_signupfee      = $this.find(".input-ctlggi-subsc-signupfee").val();
			var subsc_trial          = $this.find(".input-ctlggi-subsc-trial").val();
			
			
			// create array
			var datatableitem = {};
			datatableitem ["item_id"] = itemid;
			datatableitem ["item_price"] = price;
			datatableitem ["item_name"] = itemname;
			datatableitem ["item_quantity"] = quantity;
			datatableitem ["item_downloadable"] = downloadable;
			datatableitem ["price_option_id"] = price_option_id;
			datatableitem ["price_option_name"] = price_option_name;
			datatableitem ["item_payment_type"] = item_payment_type;
			datatableitem ["subsc_recurring"] = subsc_recurring;
			datatableitem ["subsc_interval"] = subsc_interval;
			datatableitem ["subsc_interval_count"] = subsc_interval_count;
			datatableitem ["subsc_times"] = subsc_times;
			datatableitem ["subsc_signupfee"] = subsc_signupfee;
			datatableitem ["subsc_trial"] = subsc_trial;
			jsonObj.push(datatableitem);
			
			
			// calculate single row item price in total
			var singleitempriceintotal = price * quantity;
			
			var singleitempriceintotalfixed = singleitempriceintotal.toFixed(2); // .toFixed(2) Returns "10.80"
			
			// format price
			if ( thousandSeparator == ',' ) {
				var singleitempriceintotalFormatted = ctlggi_format_price(singleitempriceintotalfixed); // 4,567,354.68
			} else if ( thousandSeparator == '.' ) {
				var singleitempriceintotalFormatted = ctlggi_format_price_DE(singleitempriceintotalfixed); // 4.567.354,68
			}

			// update each single total, span.ctlggi-item-price
			$this.find(".html-ctlggi-single-item-total .ctlggi-item-price").html(singleitempriceintotalFormatted); // public item total
			
			// update each input single total
			$this.find(".input-ctlggi-single-item-total").val(singleitempriceintotalfixed); // hidden item total
			
		});

		// then to get the JSON string
		var jsonString = JSON.stringify(jsonObj);
		//alert(jsonString);

		// check if parent has a child ".ctlggi-currency-symbol" class
		if ($(".html-ctlggi-order-total > .ctlggi-currency-symbol").length > 0) {
            // do nothing
			var currency_symbol_span_app = '';
		} else {
			// append if not exist
			var currency_symbol_span_app = 1;
		}
		
		// check if parent has a child ".ctlggi-item-price" class
		if ($(".html-ctlggi-order-total > .ctlggi-item-price").length > 0) {
            // do nothing
			var item_price_span_app = '';
		} else {
			// append if not exist
			var item_price_span_app = 1;
		}
		// append span symbol and price if not exist
		if ( currPosition == 'Left' ) {
			if ( currency_symbol_span_app == 1 && item_price_span_app == 1 ) {
				$('.html-ctlggi-order-total').append('<span class="ctlggi-currency-symbol"></span>');
				$('.html-ctlggi-order-total').append('<span class="ctlggi-item-price"></span>');
			}
		} else {
			if ( currency_symbol_span_app == 1 && item_price_span_app == 1 ) {
				$('.html-ctlggi-order-total').append('<span class="ctlggi-item-price"></span>');
				$('.html-ctlggi-order-total').append('<span class="ctlggi-currency-symbol"></span>');
			}	
		}
		
		$.ajax({
		type:"POST",
		dataType: 'json',
		url: ctlggi_admin_js.ctlggi_admin_wp_ajax_url,
		data: {action: 'ctlggi_admin_data_table_update_total', formData:jsonString},
			success:function(response){		
				//alert(data);
				//$('.html-ctlggi-order-total .ctlggi-currency-symbol').html(currSymbol); // add curr symbol
				// update total, span.ctlggi-item-price
				$('.html-ctlggi-order-total').html(response.total_public); // public total
				// update input
				$('.input-ctlggi-order-total').val(response.total_hidden); // hidden total
			}
		});
		return false;
		
		
	});
	
    // All Forms Manage Country States
	$('#_billing_country').on('change', function() {	
		
		var countrycode  = $("#_billing_country option:selected").attr("data-billing-country-code");
		var statedropdown  = $("#_billing_country option:selected").attr("data-billing-state-drop-display");
		//alert(countrycode);
		
		if (statedropdown == '1') {
			
			$('#ctlggi-billing-country-state-field').hide();
			$('#ctlggi-billing-country-state-dropdown').show();
			
			$("#ctlggi-billing-country-state-field :input").attr('disabled','disabled');   // This will disable all the inputs inside the div
            
			// manage dropdown
			$('#ctlggi-billing-country-state-dropdown select').removeAttr('disabled'); // enable select
			$('#ctlggi-billing-country-state-dropdown select').prop('selectedIndex',0); // clear selected
			$("#ctlggi-billing-country-state-dropdown select").children('option').hide(); // hide all the options
			$('#ctlggi-billing-country-state-dropdown #ctlggi_billing_states_' + countrycode).show(); // show options only for the selected country
			
			
		} else {
			
			$('#ctlggi-billing-country-state-field').show();
			$('#ctlggi-billing-country-state-dropdown').hide();
			
			$("#ctlggi-billing-country-state-dropdown select").attr('disabled','disabled');   // disable select

			$("#ctlggi-billing-country-state-field :input").removeAttr('disabled'); // This will enable all the inputs inside the div
				
		}

	});
	
	// Order General details - Customer select field
	$('.ctlggi_select_customer_class').on('change', function() {
		
		// get customer email
		var cus_user_email = $("#_ctlggi_order_cus_user_id option:selected").attr("data-cus-user-email");
		//alert(cus_user_email);
		// clear email field
		$("#_email").val('');
		// insert
		$("#_email").val(cus_user_email);
	});
	
	// price options table show hide
	$('.ctlggi_enable_price_options_checkbox').on('change', function() {
		if(this.checked) {
		   //Do stuff
		   //alert('checked');
		   $( "#display_sale_price" ).hide();
		   $( "#ctlggi-price-options-table" ).show();
		} else {
		   //alert('unchecked');
		   $( "#display_sale_price" ).show();
		   $( "#ctlggi-price-options-table" ).hide();
		}
	});
	
	// price options - add new row
    $('.ctlggi-add-price-option').on('click', function(event) {
			
		var currency_symbol  = $("input.input-ctlggi-currency-symbol").val();
		
		var ctlggi_counter  = $("input.input-ctlggi-counter").val();
		var countID = parseInt(ctlggi_counter) + 1; // convert to int
		//alert(countID);
		
		//var rowCount = $('#ctlggi-price-options-table tr.ctlggi-price-option-row').length; // count rows
		//var countID = parseInt(rowCount) + 1; // convert to int
		
		var row = $( '.ctlggi-price-insert-new-row' ).clone(true);
		row.removeClass( 'ctlggi-price-insert-new-row' );
		row.addClass( 'ctlggi-price-option-row' );
		row.insertBefore( '#ctlggi-price-options-table tbody>tr:last' );
		
		row.attr('id', countID); // set data id for each row
		
		$("#" + countID + " span.ctlggi_price_option_id").html(countID);
		$("#" + countID + " input.input-ctlggi-row-id").val( countID );
		$("#" + countID + " input.ctlggi_price_default_radio").val( countID );
		
		// update counter
		$("input.input-ctlggi-counter").val(countID);
		
		return false;
		
	});
	// price options - remove row DOM is ready
	$("#ctlggi-price-options-table").on("click", ".ctlggi-remove-price-option", function (event) {
		$(this).parents('.input-ctlggi-row-id').val(''); // empty id so we do not save in database
		$(this).parents('tr').remove();
		return false;
	});
	
	// Date Picker
	$( ".datepicker" ).datepicker({
		dateFormat : "yy-mm-dd" // yy-mm-dd = Y-m-d
	});
	
	
	// reset download expiry date
	$('.ctlggi_reset_download_expiry_date').on('click', function(event) {	
		var id = $(this).attr('id'); // get the clicked link ID
		//alert(id);
		$("#ctlggi_download_expiry_date_" + id).val('0000-00-00'); // reset value
    });
	
	// Update Download
	$('.ctlggi_update_download_data').on('click', function(event) {
	  
	  // empty before process
	  $('.ctlggi-update-download-form-return-data').empty();
	  
	  var id = $(this).attr('id'); // get the clicked link ID
	  var download_id = $(this).attr('data-download-id'); // get the clicked link download ID
	  //alert(download_id);
	  
	  var download_limit       = $("#ctlggi_download_limit_" + id).val();
	  var download_expiry_date = $("#ctlggi_download_expiry_date_" + id).val();
	  var download_count       = $("#ctlggi_download_count_" + id).val();
	  
	  // create json object
      var download_data_Object = {
              ctlggi_download_id:download_id,
			  ctlggi_download_limit:download_limit,
			  ctlggi_download_expiry_date:download_expiry_date,
			  ctlggi_download_count:download_count
		  };
		  
	  //var download_data_Object_str = JSON.stringify(download_data_Object);
      //alert(download_data_Object_str);
	  
	  $.ajax({
		type:"POST",
		dataType: 'json',
		url: ctlggi_admin_js.ctlggi_admin_wp_ajax_url,
		data: {action: 'ctlggi_update_download_data_form_process', formData:download_data_Object},
			success:function(response){
				
				//alert(JSON.stringify(response));// alert json data
				
				// returns json
				$('#ctlggi-update-download-form-return-data_' + id).show().prepend( response.message );
				// fade out
				$('#ctlggi-update-download-form-return-data_' + id).delay(2000).fadeOut(1600);
				
			}
	  });
	
	return false;
	});
	
	
});

})( jQuery );

// source: http://stackoverflow.com/questions/17668899/how-to-add-the-media-uploader-in-wordpress-plugin
// works only with jQuery not $
jQuery(document).ready(function($){
	
	// file uploader - add new item - meta box 
    var custom_uploader;

    jQuery('#ctlggi_upload_file_button').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose File',
            button: {
                text: 'Choose File'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            console.log(custom_uploader.state().get('selection').toJSON());
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#ctlggi_item_file_url').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });
	
	

});

// source: http://stackoverflow.com/questions/17668899/how-to-add-the-media-uploader-in-wordpress-plugin
jQuery(document).ready(function($){

    var custom_uploader;

    $('#upload_image_button').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            console.log(custom_uploader.state().get('selection').toJSON());
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#upload_image').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });


});


jQuery(document).ready(function($){

    var custom_uploader;

    $('#ctlggi_product_thumb_image_button').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            console.log(custom_uploader.state().get('selection').toJSON());
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#ctlggi_product_thumb_image').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });


});



