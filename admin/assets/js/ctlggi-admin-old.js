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
	
	// add new item - meta box
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
	
	// display price options select field
	$('.ctlggi_order_select_item').on('change', function() {
														 
		$( ".ctlggi_display_price_options_select_field" ).hide();
		var itemID = $("#order_select_item option:selected").val();
		//alert(itemID);
		var enable_price_options = $("#order_select_item option:selected").attr("data-enable-price-options");
		// display price option select field
		if ( enable_price_options == '1' ) {
			$( "#display_price_option-" + itemID ).show();
		}
		
	});
	
	// orders add new item
	$('.ctlggi-add-new-item').on('click', function(event) {
		//alert('clicked');
		
		// check if any value selected
		if( $('#order_select_item').val() ){
			
			var itemID               = $("#order_select_item option:selected").val();
			var itemName             = $("#order_select_item option:selected").attr("data-item-name"); 
			var itemPrice            = $("#order_select_item option:selected").attr("data-item-price");
			var itemDownloadable     = $("#order_select_item option:selected").attr("data-item-downloadable");
			var itemQuantity         = '1';
			var currSymbol           = $("#order_select_item option:selected").attr("data-curr-symbol");
			var currPosition         = $("#order_select_item option:selected").attr("data-curr-position");
			//alert(currPosition);
			
			var enable_price_options = $("#order_select_item option:selected").attr("data-enable-price-options");
			
			// if price options enabled for current item check if selected value not empty
			
			var price_option_check = '';
			var price_option_id = '';
			var price_option_name = '';
			var price_option_name_div = '';
			
			if ( enable_price_options == '1' ) {
				// check if option selected
				if( $("#display_price_option-" + itemID + ' #ctlggi_price_option_selector').val() ){
					price_option_check = 'ok';
					// price option, item price
					itemPrice = $("#display_price_option-" + itemID + ' #ctlggi_price_option_selector').val();
					price_option_id = $("#display_price_option-" + itemID + ' #ctlggi_price_option_selector option:selected').attr("data-price-option-id");
					price_option_name = $("#display_price_option-" + itemID + ' #ctlggi_price_option_selector option:selected').attr("data-price-option-name");
					//alert(price_option_name);
					price_option_name_div = '<div class="html-ctlggi-price-option-name">' + price_option_name + '</div>';
					
				} else {
					price_option_check = '';
				}
			} else {
				price_option_check = 'ok';
			}
			
			var itemTotal = itemPrice;
			
			if ( price_option_check == 'ok' ) {
				
				var thousandSeparator  = $("input.input-ctlggi-thousand-separator").val();
				// format price
				if ( thousandSeparator == ',' ) {
					
					// format item price
					var itemPriceFormatted = ctlggi_format_price(itemPrice); // 4,567,354.68
					// format item total
					var itemTotalFormatted = ctlggi_format_price(itemTotal); // 4,567,354.68
					
				} else if ( thousandSeparator == '.' ) {
					
					// format item price
					var itemPriceFormatted = ctlggi_format_price_DE(itemPrice); // 4.567.354,68
					// format item total
					var itemTotalFormatted = ctlggi_format_price_DE(itemTotal); // 4.567.354,68
					
				}
				
				
				if ( currPosition == 'Left' ) {
					var itemPricePublic = currSymbol + itemPriceFormatted;
					var itemTotalPublic = currSymbol + itemTotalFormatted;
				} else {
					var itemPricePublic = itemPriceFormatted + ' ' + currSymbol;
					var itemTotalPublic = itemTotalFormatted + ' ' + currSymbol;
				}
				
				//alert(itemID + ' ' + itemName + ' ' + itemPricePublic + ' ' + itemQuantity + ' ' + itemTotalPublic);
				
				var row = '<tr class="ctlggi-order-items">';
				row += '<input type="hidden" class="input-ctlggi-item-id" name="ctlggi_item_id[]" value="' + itemID + '"/>';
				row += '<input type="hidden" class="input-ctlggi-item-name" name="ctlggi_item_name[]" value="' + itemName + '"/>';
				row += '<input type="hidden" class="input-ctlggi-item-price" name="ctlggi_item_price[]" value="' + itemPrice + '"/>';
				row += '<input type="hidden" class="input-ctlggi-single-item-total" name="ctlggi_single_item_total[]" value="' + itemTotal + '"/>';
				row += '<input type="hidden" class="input-ctlggi-item-downloadable" name="ctlggi_item_downloadable[]" value="' + itemDownloadable + '"/>';
				row += '<input type="hidden" class="input-ctlggi-price-option-id" name="ctlggi_price_option_id[]" value="' + price_option_id + '"/>';
				row += '<input type="hidden" class="input-ctlggi-price-option-name" name="ctlggi_price_option_name[]" value="' + price_option_name + '"/>';
				row += '<td data-title="Product">' + itemName + ' ' + price_option_name_div + '</td>';
				row += '<td data-title="Price"><div class="html-ctlggi-single-item-price">' + itemPricePublic + '</div></td>';
				
				row += '<td data-title="Quantity"><div class="cataloggi-item-quantity">';
				row += '<input class="input-ctlggi-item-quantity" type="number" max="" min="1" value="' + itemQuantity + '" name="ctlggi_item_quantity[]" >';
				row += '</div></td>';
				
				row += '<td data-title="Total"><div class="html-ctlggi-single-item-total">' + itemTotalPublic + '</div></td>';
				
				row += '<td><a id="' + itemID + '" data-item-id="' + itemID + '" class="ctlggi-remove-item" href="/" onclick="return false;">remove</a></td>';
				
				row += '</tr>';
				
				$('#ctlggi-order-items-table tbody').prepend( row );
				
				//alert(itemName);
			}
		}

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
	$('.ctlggi-update-order-total').on('click', function(event) {
														 
		var currSymbol         = $("input.input-ctlggi-curr-data-symbol").val();
		var currPosition       = $("input.input-ctlggi-curr-position").val();	
		var thousandSeparator  = $("input.input-ctlggi-thousand-separator").val();
		//alert(thousandSeparator);
														 
		// jquery loop through each rows
		$("#ctlggi-order-items-table tr.ctlggi-order-items").each(function() {
																	   
			var $this = $(this);
			
			var price        = $this.find("input.input-ctlggi-item-price").val();
			var quantity     = $this.find("input.input-ctlggi-item-quantity").val();
			
			// calculate single row item price in total
			var singleitempriceintotal = price * quantity;
            var singleitempriceintotalfixed = singleitempriceintotal.toFixed(2); // .toFixed(2) Returns "10.80"
			
			// format price
			if ( thousandSeparator == ',' ) {
				var singleitempriceintotalFormatted = ctlggi_format_price(singleitempriceintotalfixed); // 4,567,354.68
			} else if ( thousandSeparator == '.' ) {
				var singleitempriceintotalFormatted = ctlggi_format_price_DE(singleitempriceintotalfixed); // 4.567.354,68
			}
			
			if ( currPosition == 'Left' ) {
				var itemPricePublic = currSymbol + singleitempriceintotalFormatted;
			} else {
				var itemPricePublic = singleitempriceintotalFormatted + ' ' + currSymbol;
			}
			
			// update each single total, span.ctlggi-item-price
			$this.find("div.html-ctlggi-single-item-total").html(itemPricePublic);
			
			// update each input single total
			$this.find("input.input-ctlggi-single-item-total").val(singleitempriceintotalfixed);
			
		});
		
		// calculate total
		var itemstotal = calculatetotal();
		
		var itemstotalfixed = itemstotal.toFixed(2); // .toFixed(2) Returns "10.80"

		// round value
		//var itemstotalrounded = roundPrice(itemstotal, 1).toFixed(2); // Returns "10.80"
		
		// format price
		if ( thousandSeparator == ',' ) {
			var itemstotalFormatted = ctlggi_format_price(itemstotalfixed); // 4,567,354.68
		} else if ( thousandSeparator == '.' ) {
			var itemstotalFormatted = ctlggi_format_price_DE(itemstotalfixed); // 4.567.354,68
		}
		
		
		if ( currPosition == 'Left' ) {
			var itemTotalPublic = currSymbol + itemstotalFormatted;
		} else {
			var itemTotalPublic = itemstotalFormatted + ' ' + currSymbol;
		}
		
		// update total, span.ctlggi-item-price
		$('.html-ctlggi-order-total span').html(itemTotalPublic); //itemstotalFormatted
		
		// remove commas
		//var itemstotalhidden = itemstotalFormatted.replace(/,/g, '');
		
		// update input
		$('input.input-ctlggi-order-total').val(itemstotalfixed);
		
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
// works only with jQuery not $
jQuery(document).ready(function($){
	
	// file uploader - add new item - meta box 
    var custom_uploader;

    jQuery('#ctlggi_upload_emails_logo_button').click(function(e) {

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
            jQuery('#ctlggi_emails_logo').val(attachment.url);
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
