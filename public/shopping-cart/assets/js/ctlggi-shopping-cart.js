jQuery(document).ready(function($) {
								
		// This will add thousand separators while retaining the decimal part of a given number
		// 2056776401.50 = 2,056,776,401.50
		function ctlggi_format_price(n) {
		  n = n.toString()
		  while (true) {
			var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, '$1,$2$3')
			if (n == n2) break
			n = n2
		  }
		  return n
		};
	
		// output 1.234.567,89
		function ctlggi_format_price_DE (n) {
			return n
			   //.toFixed(2) // always two decimal digits
			   .replace(".", ",") // replace decimal point character with ,
			   .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") // use . as a separator
		};
	
		// thousand separator
		function ctlggi_thousand_separator(s) {
			return (""+s)
				.replace(/(\d+)(\d{3})(\d{3})$/  ,"$1 $2 $3" )
				.replace(/(\d+)(\d{3})$/         ,"$1 $2"    )
				.replace(/(\d+)(\d{3})(\d{3})\./ ,"$1 $2 $3.")
				.replace(/(\d+)(\d{3})\./        ,"$1 $2."   )
			;
		};
	
		// round number
		function round2Fixed(value) {
		  value = +value;
		
		  if (isNaN(value))
			return NaN;
		
		  // Shift
		  value = value.toString().split('e');
		  value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2)));
		
		  // Shift back
		  value = value.toString().split('e');
		  return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
		}
	
		function roundPrice(value, exp) {
		  if (typeof exp === 'undefined' || +exp === 0)
			return Math.round(value);
		
		  value = +value;
		  exp = +exp;
		
		  if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
			return NaN;
		
		  // Shift
		  value = value.toString().split('e');
		  value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
		
		  // Shift back
		  value = value.toString().split('e');
		  return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
		};

		// calculate total
		function calculatetotal() {
			
			var sum = 0;
			jQuery(".input-ctlggi-single-item-total").each(function() {      
				sum += +this.value;
			});
			return sum;
		};
	
	/*
	jQuery(document).ready(function($) {
		// not in use
		function ctlggi_set_cart_cookie() {
			
			if (jQuery.cookie('demo_cookie') ) { jQuery.cookie( 'demo_cookie', null) }
			// save cookie
			jQuery.cookie('demo_cookie', 'hello', {
								   expires : 10,           //expires in 10 days
								   path    : '/',
								   domain  : 'wp-test.codeweby.com',
								   secure  : false          //If set to true the secure attribute of the cookie
								});
			
			  alert('You have set the cookie: '+ jQuery.cookie('demo_cookie'));
		};
	});
	*/
	
		// get cookie
		function ctlggi_get_Cookie(name) {
			var dc = document.cookie;
			var prefix = name + "=";
			var begin = dc.indexOf("; " + prefix);
			if (begin == -1) {
				begin = dc.indexOf(prefix);
				if (begin != 0) return null;
			}
			else
			{
				begin += 2;
				var end = document.cookie.indexOf(";", begin);
				if (end == -1) {
				end = dc.length;
				}
			}
			return unescape(dc.substring(begin + prefix.length, end));
		}; 
		
		// Display the categories drop down navigation top of the products list - DOM ready
		jQuery(".cw-form").on("change", ".ctlggi_categories_drop_down_class", function (event) {
																													   
			var category_link = jQuery(this).val(); // link
			//alert(category_link);
			
			// do redirect
			if ( category_link ){
				document.location.href = category_link;
			}
			
		});
		
		// Payment Buttons Buy Now form, Price Options select field on change - DOM ready
		// Can handle multiple payment buttons on one page
		jQuery(".ctlggi-payment-buttons-wrapper .ctlggi-buy-now-form-class").on("change", ".ctlggi-price-options", function (event) {
																													   
			var id = jQuery(this).attr('id'); // item id
			//alert(id);
			var buy_now_form = '#ctlggi_buy_now_form_' + id; // buy now form	
			var price_options_select = '#ctlggi_price_options_select_' + id; // price options select field

			var sale_price_hidden = jQuery(price_options_select + " :selected").attr("ctlggi-data-sale-price-hidden");
			var option_id  = jQuery(price_options_select + " :selected").val(); // selected option id
			
			// update
			jQuery('#ctlggi_buy_now_button_' + id + ' input.ctlggi_item_price_class').val(sale_price_hidden); // update hidden item price
			jQuery('#ctlggi_buy_now_button_' + id + ' span.ctlggi-item-price').html(sale_price_hidden); // update public item price	
			
		});
	
		// Cart Payment Buttons Price Options select field on change - DOM ready
		jQuery(".ctlggi-payment-buttons-wrapper .ctlggi-add-to-cart-form-class").on("change", ".ctlggi-price-options", function (event) {
																								  
			//var itemid   = jQuery(this).find(":selected").attr("ctlggi-data-item-id");
			var id = jQuery(this).attr('id'); // item id
			//alert(id);
			
			var add_to_cart_form = '#ctlggi_add_to_cart_form_' + id; // buy now form	
			var price_options_select = '#ctlggi_price_options_select_' + id; // price options select field
			
			var sale_price_hidden = jQuery(price_options_select + " :selected").attr("ctlggi-data-sale-price-hidden");
			var option_id  = jQuery(price_options_select + " :selected").val(); // selected option id

			// update
			jQuery('#ctlggi_add_to_cart_button_' + id + ' input.ctlggi_item_price_class').val(sale_price_hidden); // update hidden item price
			jQuery('#ctlggi_add_to_cart_button_' + id + ' span.ctlggi-item-price').html(sale_price_hidden); // update public item price	
			
		});
		
		// Add to Cart Button Process
		// Can handle multiple buttons on one page
		jQuery('.ctlggi-payment-buttons-wrapper .ctlggi-add-to-cart-form-submit').on('click', function(event) {
																								   
			var id = this.id; // submit button id
			var add_to_cart_form = '#ctlggi_add_to_cart_form_' + id; // buy now form
			//alert( 'ID: ' + id + ' Add to Cart ID: ' + add_to_cart_form);
		
			var ctlggi_cart_items_cookie_name = jQuery('.ctlggi_cart_items_cookie_name_class').val();
			//alert(cart_items_cookie_name);
			
			var form_Data = jQuery(add_to_cart_form).serialize();
			//alert(form_Data);
			
			var itemid   = jQuery(add_to_cart_form + " #ctlggi_item_id").val();
			//alert(itemid);
			
			// shopping basket - check if cookie exist
			var cartitems = ctlggi_get_Cookie(ctlggi_cart_items_cookie_name); // get cookie
			
			// get value
			var basket = parseInt( jQuery('.input-ctlggi-basket-items').val() ); // parseInt for sum values
			//alert(basket);
			var basketnew = basket + 1;
			jQuery('.input-ctlggi-basket-items').val( basketnew );
			jQuery('span.cataloggi-shopping-basket-items').html( basketnew );
			
			var cartArray = JSON.parse(cartitems); // json decode
			
			jQuery.ajax({
			type:"POST",
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_payment_buttons_process', formData:form_Data},
				success:function(data){
					
					//jQuery('.show-return-data').show().prepend( data );
					// fade out
					//$('.show-return-data').delay(3000).fadeOut(800);
					
					jQuery('#ctlggi_add_to_cart_button_' + itemid + ' #ctlggi-payment-button-1').hide();
					jQuery('#ctlggi_add_to_cart_button_' + itemid + ' #ctlggi-payment-button-2').show();
				
				}
			});
		
		return false;
		});	
	
		// remove from cart
		jQuery('.ctlggi-remove-from-cart-form').submit(removefromcartSubmit);
		
		function removefromcartSubmit(){
		
		// empty div before process
		jQuery('.show-return-data').empty();
		
		  var form = jQuery(this); //Store the context of this in a local variable 
		
		  var formData = jQuery(this).serialize();
		  //alert(formData);
		
		  jQuery.ajax({
			type:"POST",
			//dataType: 'json',
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_remove_from_cart_form_process', formData:formData},
				success:function(data){
					//alert(itemid);
					// remove closest row
					form.closest("tr").remove();
					// update total
					jQuery('.html-ctlggi-items-price-in-total span.ctlggi-item-price').html(data); // public
					// update input
					jQuery('input.input-ctlggi-items-price-in-total').val(data); // hidden
				}
		  });
		
		return false;
		};
	
		// update cart
		jQuery('.ctlggi-update-cart').click(function() {
	
			var jsonObj = [];
			
			var thousandSeparator  = jQuery("input.input-ctlggi-thousand-separator").val();
			//alert(thousandSeparator);
			
			// jquery loop through each rows
			jQuery("#ctlggi-cart-table tr.ctlggi-cart-item").each(function() {
																		   
				var $this = jQuery(this);
				
				// item data
				var itemid            = $this.find("input.input-ctlggi-item-id").val();
				var price             = $this.find("input.input-ctlggi-item-price").val();
				var itemname          = $this.find("input.input-ctlggi-item-name").val();
				var quantity          = $this.find("input.input-ctlggi-item-quantity").val();
				var downloadable      = $this.find("input.input-ctlggi-item-downloadable").val();
				var price_option_id   = $this.find("input.input-ctlggi-price-option-id").val();
				var price_option_name = $this.find("input.input-ctlggi-price-option-name").val();
				var item_payment_type = $this.find("input.input-ctlggi-item-payment-type").val();
				var custom_field      = $this.find("input.input-ctlggi-custom-field").val();
				var grouped_products  = $this.find("input.input-ctlggi-grouped-products").val();
				
				// subsc data
				var subsc_recurring      = $this.find("input.input-ctlggi-subsc-recurring").val();
				var subsc_interval       = $this.find("input.input-ctlggi-subsc-interval").val();
				var subsc_interval_count = $this.find("input.input-ctlggi-subsc-interval-count").val();
				var subsc_times          = $this.find("input.input-ctlggi-subsc-times").val();
				var subsc_signupfee      = $this.find("input.input-ctlggi-subsc-signupfee").val();
				var subsc_trial          = $this.find("input.input-ctlggi-subsc-trial").val();
				  
				// create array
				var cartitem = {};
				cartitem ["item_id"] = itemid;
				cartitem ["item_price"] = price;
				cartitem ["item_name"] = itemname;
				cartitem ["item_quantity"] = quantity;
				cartitem ["item_downloadable"] = downloadable;
				cartitem ["price_option_id"] = price_option_id;
				cartitem ["price_option_name"] = price_option_name;
				cartitem ["item_payment_type"] = item_payment_type;
				cartitem ["custom_field"] = custom_field;
				cartitem ["grouped_products"] = grouped_products;
				cartitem ["subsc_recurring"] = subsc_recurring;
				cartitem ["subsc_interval"] = subsc_interval;
				cartitem ["subsc_interval_count"] = subsc_interval_count;
				cartitem ["subsc_times"] = subsc_times;
				cartitem ["subsc_signupfee"] = subsc_signupfee;
				cartitem ["subsc_trial"] = subsc_trial;
				jsonObj.push(cartitem);
				
				// calculate single row item price in total
				var singleitempriceintotal = price * quantity;
				
				var singleitempriceintotalfixed = singleitempriceintotal.toFixed(2); // .toFixed(2) Returns "10.80"
				
				// round value
				//var singleitempriceintotalrounded = round2Fixed(singleitempriceintotal);
				//var singleitempriceintotalrounded = roundPrice(singleitempriceintotal, 1).toFixed(2); // Returns "10.80"
				
				// format price
				if ( thousandSeparator == ',' ) {
					var singleitempriceintotalFormatted = ctlggi_format_price(singleitempriceintotalfixed); // 4,567,354.68
				} else if ( thousandSeparator == '.' ) {
					var singleitempriceintotalFormatted = ctlggi_format_price_DE(singleitempriceintotalfixed); // 4.567.354,68
				}
				
				// update each single total
				$this.find("span.html-ctlggi-single-item-total span.ctlggi-item-price").html(singleitempriceintotalFormatted);
				
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
				var itemstotalFormatted = ctlggi_format_price_DE(itemstotalfixed); // 4,567,354.68
			}
			
			// update total
			//jQuery('.html-ctlggi-items-price-in-total span.ctlggi-item-price').html(itemstotalFormatted);
			
			// remove commas
			//var itemstotalhidden = itemstotalFormatted.replace(/,/g, '');
			
			// update input
			jQuery('input.input-ctlggi-items-price-in-total').val(itemstotalfixed);
			
			
			  // then to get the JSON string
			  var jsonString = JSON.stringify(jsonObj);
			  // update cookie
			  jQuery.ajax({
				type:"POST",
				//dataType: 'json',
				url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
				data: {action: 'ctlggi_update_cart_process', formData:jsonString},
					success:function(data){		
					//alert(data.cart_total);
						// show return data
						//jQuery('.show-update-button-return-data').show().prepend( data );
						// update total
						jQuery('.html-ctlggi-items-price-in-total span.ctlggi-item-price').html(data); // data.cart_total
					}
			  });
			  return false;
			
		});
	
		// grid - small or large and list view
		jQuery('.cataloggi-grid-buttons a').on('click', function(event){
			event.preventDefault();
			
			var setview = jQuery(this).attr("cataloggi-grid-data-id");
			//alert(setview);
			
			if ( setview == 'normal' ) {
				var itemsview = 'cataloggi-item-box-grid columns-3'; // three colums view
			} else if ( setview == 'large' ) {
				var itemsview = 'cataloggi-item-box-grid columns-2'; // two colums view
			} else if ( setview == 'list' ) {
				var itemsview = 'cataloggi-item-box-list-view'; // list view
			} else {
				// default
				var itemsview = 'cataloggi-item-box-grid columns-3'; // three colums view
			}
			
			jQuery('#set-cataloggi-grid-view').fadeOut(500, function(){
				
				jQuery("#set-cataloggi-grid-view").attr('class', ''); // clear attr
				jQuery("#set-cataloggi-grid-view").addClass( itemsview ); // add class								
													
				jQuery('#set-cataloggi-grid-view').fadeIn(500);
				
			});
			
			// button set active
			jQuery('.cataloggi-grid-buttons li a').removeClass("btn-cataloggi btn-cataloggi-sm btn-cataloggi-grey cataloggi-float-right active");
			jQuery(this).addClass("btn-cataloggi btn-cataloggi-sm btn-cataloggi-grey cataloggi-float-right active");
			
			  // set cookie
			  jQuery.ajax({
				type:"POST",
				url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
				data: {action: 'ctlggi_items_view_process', itemsview:itemsview},
					success:function(data){		
						// show return data
						//jQuery('.show-items-view-return-data').show().prepend( data );
					}
			  });
			  return false;
	
		});
	
		// Shopping Cart Checkout - payment method on change
		jQuery('.ctlggi_payment_gateway_radio').on('change', function() {
			
			var gateway = this.value;
			//alert(gateway);
			
			// for third party gateways - update current gateway hidden field value
			jQuery('.ctlggi-current-gateway').val(gateway);
			
			// gateway description
			jQuery( "#default_gateway_description" ).hide();
			jQuery( ".cataloggi-checkout-gateway-description" ).hide();
			jQuery( "#" + gateway + "_gateway_description" ).show();
			
			// update gateway hidden field
			jQuery('input.ctlggi_default_gateway_class').val(gateway);
			
			// get fields
			//var personal_details    = jQuery('#' + gateway + '_personal_details').val();    // return 0 or 1 not in use
			var create_an_account   = jQuery('#' + gateway + '_create_an_account').val();   // return 0 or 1
			var credit_card_details = jQuery('#' + gateway + '_credit_card_details').val(); // return 0 or 1
			var billing_details     = jQuery('#' + gateway + '_billing_details').val();     // return 0 or 1
			
			if (credit_card_details == '1') {
				jQuery( ".ctlggi-credit-card-details-fields" ).show();
				// enable credit card details fields
				jQuery(".ctlggi-credit-card-details-fields").find("input,select").removeAttr('disabled'); // disable all inputs and selects in div
			} else {
				// disable credit card details fields
				jQuery(".ctlggi-credit-card-details-fields").find("input,select").attr("disabled", "disabled"); // disable all inputs and selects in div
				jQuery( ".ctlggi-credit-card-details-fields" ).hide();
			}
			
			if (billing_details == '1') {
				jQuery( ".ctlggi-billing-details-fields" ).show();
				// enable billing details fields
				jQuery(".ctlggi-billing-details-fields").find("input,select").removeAttr('disabled'); // disable all inputs and selects in div
			} else {
				// disable billing details fields
				jQuery(".ctlggi-billing-details-fields").find("input,select").attr("disabled", "disabled"); // disable all inputs and selects in div
				jQuery( ".ctlggi-billing-details-fields" ).hide();
			}
			
			
		});
	
		// Shopping Cart Checkout - Login Form toggle
		jQuery( "#ctlggi-show-hide-login-form" ).hide(); // default
		jQuery('#ctlggi-toggle-login-form').on('click', function() {
			  jQuery('#ctlggi-show-hide-login-form').fadeToggle(600); //.slideToggle('slow');
		});
	
		// Shopping Cart Checkout - Login Form Process (json)
		jQuery('#ctlggi-login-form').submit(loginformSubmit);
		
		function loginformSubmit(){
		
		// empty div before process
		jQuery('.show-login-form-return-data').empty();
		
		  var logformData = jQuery(this).serialize();
		  //alert(logformData);
		
		  jQuery.ajax({
			type:"POST",
			dataType: 'json',
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_login_form_process', formData:logformData},
				success:function(data){
					
					// returns json
					jQuery('.show-login-form-return-data').show().prepend( data.message );
					// fade out
					jQuery('.show-login-form-return-data').delay(5000).fadeOut(1600);
					
					// success - redirect (refress)
					if (data.loggedin == true){
						document.location.href = ctlggi_ajax_shopping_cart.ctlggi_login_redirect_url; // specified at wp_localize_script
					}
				
				}
		  });
		
		return false;
		};
	
		// Register Form Process (json)
		jQuery('#ctlggi-register-form').submit(registerformSubmit);
		
		function registerformSubmit(){
		
		// empty div before process
		jQuery('.show-register-form-return-data').empty();
		
		  var regformData = jQuery(this).serialize();
		  //alert(regformData);
		
		  jQuery.ajax({
			type:"POST",
			dataType: 'json',
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_register_form_process', formData:regformData},
				success:function(data){
					
					//alert(data.message);
					// returns json
					jQuery('.show-register-form-return-data').show().prepend( data.message );
					// fade out
					jQuery('.show-register-form-return-data').delay(15000).fadeOut(1600);
					
					/*
					// success - redirect (refress)
					if (data.success == true){
					   document.location.href = ctlggi_ajax_shopping_cart.ctlggi_register_redirect_url; // specified at wp_localize_script
					}
					*/
				}
		  });
		
		return false;
		};
	
		// Forgot PW Form Process (json)
		jQuery('#ctlggi-forgot-pw-form').submit(forgotpwformSubmit);
		
		function forgotpwformSubmit(){
		
		// empty div before process
		jQuery('.show-forgot-pw-form-return-data').empty();
		
		  var forgotpwformData = jQuery(this).serialize();
		  //alert(regformData);
		
		  jQuery.ajax({
			type:"POST",
			dataType: 'json',
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_forgot_pw_form_process', formData:forgotpwformData},
				success:function(data){
					
					// returns json
					jQuery('.show-forgot-pw-form-return-data').show().prepend( data.message );
					// fade out
					jQuery('.show-forgot-pw-form-return-data').delay(15000).fadeOut(1600);
					
				}
		  });
		
		return false;
		};
	
		// Contact Form Process (json)
		jQuery('#ctlggi-contact-form').submit(contactformSubmit);
		
		function contactformSubmit(){
		
		// empty div before process
		jQuery('.show-contact-form-return-data').empty();
		
		  var contactformData = jQuery(this).serialize();
		
		  jQuery.ajax({
			type:"POST",
			dataType: 'json',
			url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
			data: {action: 'ctlggi_contact_form_process', formData:contactformData},
				success:function(data){
					
					// returns json
					jQuery('.show-contact-form-return-data').show().prepend( data.message );
					// fade out
					//jQuery('.show-contact-form-return-data').delay(15000).fadeOut(1600);
					jQuery('#ctlggi-contact-form-holder').hide();
					
				}
		  });
		
		return false;
		};
	
	
		// Shopping Cart - Checkout Form Process (json) - Send Order Data to Gateway
		jQuery(".ctlggi-loading-img").hide();
		jQuery('#ctlggi-checkout-form').submit(checkoutformSubmit);
		function checkoutformSubmit(event){
        event.preventDefault();
			var form$ = jQuery("#ctlggi-checkout-form");
			
			// empty div before process
			jQuery('.show-checkout-form-return-data').empty();
				
			  var checkoutformData = jQuery(this).serialize();
			  //alert(checkoutformData);
				  jQuery.ajax({
					type:"POST",
					dataType: 'json',
					//dataType: "text json",
					url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
					data: {action: 'ctlggi_checkout_form_process', formData:checkoutformData},
						success:function(response){
							
							//alert(response.checkoutformdata.ctlggi_first_name); // output data example
							//alert(JSON.stringify(response));// alert json data
							
							/*
							// output returned data using .each
							var newHTML = [];
							jQuery.each(response.checkoutformdata, function(key,value){
								//alert(key + " / " + value );
								newHTML.push('<span>' + ' - ' + value + '</span><br>');	
							});
							jQuery('.show-checkout-form-return-data').html( newHTML.join("") );
							
							// validate checkout form
							if (response.checkoutformvalid == false){
								
								// show error messages
								// returns json
								jQuery('.show-checkout-form-return-data').show().prepend( response.message ); // response.message
								// fade out
								jQuery('.show-checkout-form-return-data').delay(10000).fadeOut(1600);
								
							} else {
								// process
								// checkout form valid, set hidden field to 1
								jQuery('.ctlggi_checkout_form_valid').val('1');
								//alert('ok');
								//alert(JSON.stringify(response.checkoutformvalid));// alert json data
							}
							*/
						
						}
						
				  });
		
		return false;
		};
		
		// Payment Gateway NONE - Process FREE Payment
		jQuery(".ctlggi-loading-img").hide();
		jQuery('#ctlggi-checkout-form').submit(gatewayNoneSubmit);
		
		function gatewayNoneSubmit(event){
        event.preventDefault();
		var currentgateway = jQuery('.ctlggi-current-gateway').val();
		//alert(currentgateway);
		// load jQuery only if none gateway selected
		if ( currentgateway == 'none' ) { // should be the registered gateway name
			//alert('hi');
			// clear and hide error messages
			jQuery(".ctlggi-payment-gateway-messages").html('');
			jQuery(".ctlggi-payment-gateway-messages").hide();
				
			var checkoutformData = jQuery(this).serialize();
			//alert(checkoutformData);
			
			jQuery(".ctlggi-loading-img").show();
			// spinner
			var formloaderimg = ctlggi_ajax_shopping_cart.ctlggi_form_loader_img; // ajax wp_localize_script
			jQuery('.ctlggi-loading-img').html('<img src="' + formloaderimg + '" width="128" height="15" alt="loading..." />');
			jQuery('#ctlggi-checkout-form-submit').attr('disabled', 'disabled'); // disable submit button after form submit
			// ajax submit after 2 seconds so preloader visible until
			setTimeout(function() 
			{
				jQuery.ajax({
					type:"POST",
					dataType: 'json',
					url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
					data: {action: 'ctlggi_process_none_payment', formData:checkoutformData},
					success:function(response)
					{
						jQuery('#ctlggi-checkout-form-submit').attr("disabled", false); // re-enable the submit button
						
	                    //alert(JSON.stringify(response));// alert json data
						
						jQuery(".ctlggi-loading-img").hide();
						
						if (response.checkoutsuccess == false){
							// show error messages
							// returns json
							jQuery('.ctlggi-payment-gateway-messages').show().prepend( response.message ); // response.message
							// fade out
							//jQuery('.ctlggi-payment-gateway-messages').delay(16000).fadeOut(1600);	
						} else {
							// success
							jQuery('#ctlggi-returning-customer-div').hide(); // returning customer div
							jQuery('#ctlggi-show-hide-login-form').hide(); // hide login form
							jQuery('#ctlggi-payment-methods-holder').hide(); // hide login form
							jQuery('#ctlggi-checkout-form').hide(); // hide checkout form
							jQuery('#ctlggi-terms-link').hide(); // hide terms link
							
							// show thank you message or use redirect
							var redirect = ctlggi_ajax_shopping_cart.ctlggi_success_redirect_url; // specified on public wp_localize_script
							if ( redirect != '0' ) {
								// redirect to specified page
								document.location.href = ctlggi_ajax_shopping_cart.ctlggi_success_redirect_url; // specified on public wp_localize_script
							} else {
								// show success message
								//alert(JSON.stringify(response.message));// alert json data
								jQuery('.ctlggi-payment-gateway-messages').show().prepend( response.message ); // response.message
							}
						}
	
					} // success end
				});
			
			}, 2000);
		
		}
		
		return false;
		};
	
		// Payment Gateway BACS - Process Payment
		jQuery(".ctlggi-loading-img").hide();
		jQuery('#ctlggi-checkout-form').submit(gatewayBacsSubmit);
		
		function gatewayBacsSubmit(event){
        event.preventDefault();
		var currentgateway = jQuery('.ctlggi-current-gateway').val();
		//alert(currentgateway);
		// load jQuery only if bacs gateway selected
		if ( currentgateway == 'bacs' ) { // should be the registered gateway name
			//alert('hi');
			// clear and hide error messages
			jQuery(".ctlggi-payment-gateway-messages").html('');
			jQuery(".ctlggi-payment-gateway-messages").hide();
				
			var checkoutformData = jQuery(this).serialize();
			//alert(checkoutformData);
			
			jQuery(".ctlggi-loading-img").show();
			// spinner
			var formloaderimg = ctlggi_ajax_shopping_cart.ctlggi_form_loader_img; // ajax wp_localize_script
			jQuery('.ctlggi-loading-img').html('<img src="' + formloaderimg + '" width="128" height="15" alt="loading..." />');
			jQuery('#ctlggi-checkout-form-submit').attr('disabled', 'disabled'); // disable submit button after form submit
			// ajax submit after 2 seconds so preloader visible until
			setTimeout(function() 
			{
				jQuery.ajax({
					type:"POST",
					dataType: 'json',
					url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
					data: {action: 'ctlggi_process_bacs_payment', formData:checkoutformData},
					success:function(response)
					{
						jQuery('#ctlggi-checkout-form-submit').attr("disabled", false); // re-enable the submit button
						
	                    //alert(JSON.stringify(response));// alert json data
						
						jQuery(".ctlggi-loading-img").hide();
						
						if (response.checkoutsuccess == false){
							// show error messages
							// returns json
							jQuery('.ctlggi-payment-gateway-messages').show().prepend( response.message ); // response.message
							// fade out
							//jQuery('.ctlggi-payment-gateway-messages').delay(16000).fadeOut(1600);	
						} else {
							// success
							jQuery('#ctlggi-returning-customer-div').hide(); // returning customer div
							jQuery('#ctlggi-show-hide-login-form').hide(); // hide login form
							jQuery('#ctlggi-payment-methods-holder').hide(); // hide login form
							jQuery('#ctlggi-checkout-form').hide(); // hide checkout form
							jQuery('#ctlggi-terms-link').hide(); // hide terms link
							
							// show thank you message or use redirect
							var redirect = ctlggi_ajax_shopping_cart.ctlggi_success_redirect_url; // specified on public wp_localize_script
							if ( redirect != '0' ) {
								// redirect to specified page
								document.location.href = ctlggi_ajax_shopping_cart.ctlggi_success_redirect_url; // specified on public wp_localize_script
							} else {
								// show success message
								//alert(JSON.stringify(response.message));// alert json data
								jQuery('.ctlggi-payment-gateway-messages').show().prepend( response.message ); // response.message
							}
						}
	
					} // success end
				});
			
			}, 2000);
		
		}
		
		return false;
		};
		
		// Payment Gateway PayPal Standard - Process Payment
		jQuery(".ctlggi-loading-img").hide();
		jQuery('#ctlggi-checkout-form').submit(gatewayPayPalStandardSubmit);
		
		function gatewayPayPalStandardSubmit(event){
        event.preventDefault();
		var currentgateway = jQuery('.ctlggi-current-gateway').val();
		//alert(currentgateway);
		// load jQuery only if bacs gateway selected
		if ( currentgateway == 'paypalstandard' ) { // should be the registered gateway name
			//alert('hi');
			// clear and hide error messages
			jQuery(".ctlggi-payment-gateway-messages").html('');
			jQuery(".ctlggi-payment-gateway-messages").hide();
				
			var checkoutformData = jQuery(this).serialize();
			//alert(checkoutformData);
			
			jQuery(".ctlggi-loading-img").show();
			// spinner
			var formloaderimg = ctlggi_ajax_shopping_cart.ctlggi_form_loader_img; // ajax wp_localize_script
			jQuery('.ctlggi-loading-img').html('<img src="' + formloaderimg + '" width="128" height="15" alt="loading..." />');
			jQuery('#ctlggi-checkout-form-submit').attr('disabled', 'disabled'); // disable submit button after form submit
			// ajax submit after 2 seconds so preloader visible until
			setTimeout(function() 
			{
				jQuery.ajax({
					type:"POST",
					dataType: 'json',
					url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
					data: {action: 'ctlggi_process_paypalstandard_payment', formData:checkoutformData},
					success:function(response)
					{
						jQuery('#ctlggi-checkout-form-submit').attr("disabled", false); // re-enable the submit button
						
	                    //alert(JSON.stringify(response));// alert json data
						
						jQuery(".ctlggi-loading-img").hide();
						
						if (response.checkoutsuccess == false){
							// show error messages
							// returns json
							jQuery('.ctlggi-payment-gateway-messages').show().prepend( response.message ); // response.message
							// fade out
							//jQuery('.ctlggi-payment-gateway-messages').delay(16000).fadeOut(1600);	
						} else {
							// success
							jQuery('#ctlggi-returning-customer-div').hide(); // returning customer div
							jQuery('#ctlggi-show-hide-login-form').hide(); // hide login form
							jQuery('#ctlggi-payment-methods-holder').hide(); // hide login form
							jQuery('#ctlggi-checkout-form').hide(); // hide checkout form
							jQuery('#ctlggi-terms-link').hide(); // hide terms link
							
							var paypalredirect = response.redirecturl;
							
							//alert(JSON.stringify(paypalredirect));// alert json data
							
							// redirect to specified page
							document.location.href = paypalredirect;
						}
	
					} // success function end
				});
			
			}, 2000);
		
		}
		
		return false;
		};
		
		// Payment Gateway PayPal Standard - Buy Now Button Process Payment
		// Can handle multiple payment buttons on one page
		jQuery('.ctlggi-payment-buttons-wrapper .ctlggi-buy-now-form-submit').on('click', function(event) {
																								   
			var id = this.id; // submit button id
			var buy_now_form = '#ctlggi_buy_now_form_' + id; // buy now form
			//alert( 'ID: ' + id + ' Buy Now Form ID: ' + buy_now_form);
			
			jQuery(buy_now_form + " .ctlggi-loading-img").hide();
		
			var default_gateway = jQuery(buy_now_form + ' #ctlggi_default_gateway').val();
			//alert(default_gateway);
			// load jQuery only if gateway is paypalstandard
			if ( default_gateway == 'paypalstandard' ) { // should be the registered gateway name 
			
			jQuery(buy_now_form + " .display-buy-now-form-response-msg").html('').hide();
				
				var form_Data = jQuery(buy_now_form).serialize();
				//alert(form_Data);
				var item_id            = jQuery(buy_now_form + " #ctlggi_item_id").val();
				var item_price         = jQuery(buy_now_form + " #ctlggi_item_price").val();
				var item_name          = jQuery(buy_now_form + " #ctlggi_item_name").val();
				var item_downloadable  = jQuery(buy_now_form + " #ctlggi_item_downloadable").val();
				var item_currency      = jQuery(buy_now_form + " #ctlggi_item_currency").val();
				var subscription       = jQuery(buy_now_form + " #ctlggi_subscription").val(); // check if subsc enabled
				var software_licensing = jQuery(buy_now_form + " #ctlggi_software_licensing").val(); // check if licensing enabled
				var item_quantity      = jQuery(buy_now_form + " #ctlggi_item_quantity").val();
				var guest_payment      = jQuery(buy_now_form + " #ctlggi_guest_payment").val(); // yes or no
			
				// check if price options select field exist
				if (jQuery( buy_now_form + ' .ctlggi-price-options' ) && jQuery( buy_now_form + ' .ctlggi-price-options' ).length ) {
				 var option_name  = jQuery(buy_now_form + " .ctlggi-price-options :selected").attr("ctlggi-data-option-name"); // selected option name
				 // selected option item payment type (normal or subscription)
				 var item_payment_type    = jQuery(buy_now_form + " .ctlggi-price-options :selected").attr("ctlggi-data-item-payment-type"); 
				 var item_name_and_option = item_name + ' ' + option_name;
				} else {
				 var item_payment_type    = 'normal';
				 var item_name_and_option = item_name;
				}
				
				var item_total = item_price * item_quantity;
				//var item_total_in_cents = item_total * 100;
				
				var process_payment = '1'; // def
				// login required
				var is_user_logged_in = jQuery(buy_now_form + " #ctlggi_is_user_logged_in").val(); // 1 or 0
				if ( guest_payment != 'yes' && is_user_logged_in != '1' ) {
				  process_payment = '0';
				  var error_msg = '<span class="cw-form-txt-msgs">';
				  error_msg += '<span class="alert-txt-danger">Please login to complete your purchase.</span>';
				  error_msg += '</span>';
				  jQuery(buy_now_form + ' .display-buy-now-form-response-msg').show().prepend( error_msg );
				  jQuery(buy_now_form + ' .display-buy-now-form-response-msg').delay(12000).fadeOut(1600);
				} 
				
				// Stripe express checkout buy now button cannot handle subscription payments.
				if ( subscription == '1' ) {
				  process_payment = '0';
				  var error_msg = '<span class="cw-form-txt-msgs">';
				  error_msg += '<span class="alert-txt-danger">PayPal Standard buy now button do not support subscription payments.</span>';
				  error_msg += '</span>';
				  jQuery(buy_now_form + ' .display-buy-now-form-response-msg').show().prepend( error_msg );
				  jQuery(buy_now_form + ' .display-buy-now-form-response-msg').delay(12000).fadeOut(1600);
				} 
			
				if ( process_payment == '1' ) {
					jQuery(buy_now_form + " .ctlggi-loading-img").show();
					// spinner
					var formloaderimg = ctlggi_ajax_shopping_cart.ctlggi_form_loader_img; // ajax wp_localize_script
					jQuery(buy_now_form + ' .ctlggi-loading-img').html('<img src="' + formloaderimg + '" width="128" height="15" alt="loading..." />');
					jQuery(buy_now_form).attr('disabled', 'disabled'); // disable submit button after form submit
					// ajax submit after 2 seconds so preloader visible until
					setTimeout(function() 
					{
					    
						jQuery.ajax({
							type:"POST",
							dataType: 'json',
							url: ctlggi_ajax_shopping_cart.ctlggi_wp_ajax_url,
							data: {action: 'paypal_standard_buy_now_form_process', formData:form_Data},
							success: function(response) {
								
								jQuery(buy_now_form).attr("disabled", false); // re-enable the submit button
								
								//alert(JSON.stringify(response));// alert json data
								
								jQuery(buy_now_form + " .ctlggi-loading-img").hide();
								
								if (response.checkoutsuccess == false){
									// show error messages
									// returns json
									jQuery(buy_now_form + ' .display-buy-now-form-response-msg').show().prepend( response.message ); // response.message
									// fade out
									//jQuery('.ctlggi-payment-gateway-messages').delay(16000).fadeOut(1600);	
								} else {
									// success
									var paypalredirect = response.redirecturl;
									//alert(JSON.stringify(paypalredirect));// alert json data
									
									// redirect to specified page
									document.location.href = paypalredirect;
								}
							},
							error: function(e){
							  // Do something with the error
							}
						});
						
					
					}, 2000);
				}
			
			}
		
		return false;
		});	
	
		// All Forms Manage Country States
		jQuery('#ctlggi_billing_country').on('change', function() {	
			
			var countrycode  = jQuery("#ctlggi_billing_country option:selected").attr("data-billing-country-code");
			var statedropdown  = jQuery("#ctlggi_billing_country option:selected").attr("data-billing-state-drop-display");
			//alert(countrycode);
			
			if (statedropdown == '1') {
				
				jQuery('#ctlggi-billing-country-state-field').hide();
				jQuery('#ctlggi-billing-country-state-dropdown').show();
				
				jQuery("#ctlggi-billing-country-state-field :input").attr('disabled','disabled');   // This will disable all the inputs inside the div
				
				// manage dropdown
				jQuery('#ctlggi-billing-country-state-dropdown select').removeAttr('disabled'); // enable select
				jQuery('#ctlggi-billing-country-state-dropdown select').prop('selectedIndex',0); // clear selected
				jQuery("#ctlggi-billing-country-state-dropdown select").children('option').hide(); // hide all the options
				jQuery('#ctlggi-billing-country-state-dropdown #ctlggi_billing_states_' + countrycode).show(); // show options only for the selected country
				
				
			} else {
				
				jQuery('#ctlggi-billing-country-state-field').show();
				jQuery('#ctlggi-billing-country-state-dropdown').hide();
				
				jQuery("#ctlggi-billing-country-state-dropdown select").attr('disabled','disabled');   // disable select
	
				jQuery("#ctlggi-billing-country-state-field :input").removeAttr('disabled'); // This will enable all the inputs inside the div
					
			}
	
		});
		
		// Payments form fix for billing state
		jQuery('.ctlggi_payment_gateway_radio').on('change', function() {
			
			var gateway = this.value;
			//alert(gateway);
			var is_paymentsform = jQuery('#ctlggi-checkout-form #ctlggi_form_type').val();
			if (is_paymentsform == 'paymentsform') {
				//alert(is_paymentsform);
				var billing_details     = jQuery('#' + gateway + '_billing_details').val();     // return 0 or 1
				if (billing_details == '1') {
					var state_field_imput = jQuery( "#ctlggi-billing-country-state-field .ctlggi_billing_state_input_class" ).val();
					if ( state_field_imput.trim() != false ) {
						//alert(state_field_imput);
						// disable ctlggi_billing_state select
						jQuery("#ctlggi-billing-country-state-dropdown select").attr('disabled','disabled');   // disable select
						jQuery("#ctlggi-billing-country-state-field :input").removeAttr('disabled'); // This will enable all the inputs inside the div
					}
				} else {
					// enable ctlggi_billing_state select
					jQuery('#ctlggi-billing-country-state-dropdown select').removeAttr('disabled'); // enable select
					jQuery("#ctlggi-billing-country-state-field :input").attr('disabled','disabled');   // This will disable all the inputs inside the div
				}
			}
			
		});
		
	
		// login, register, forgot pw forms
		jQuery('.cataloggi-log-reg-buttons a').on('click', function(event){
			event.preventDefault();
			
			var form_type = jQuery(this).attr("cataloggi-form-type");
			
			if ( form_type == 'login' ) {
				jQuery('.cataloggi-display-login-form').fadeIn(500);
				jQuery('.cataloggi-display-register-form').hide();
				jQuery('.cataloggi-display-forgot-pw-form').hide();
			} else if ( form_type == 'register' ) {
				jQuery('.cataloggi-display-login-form').hide();
				jQuery('.cataloggi-display-forgot-pw-form').hide();			
				jQuery('.cataloggi-display-register-form').fadeIn(500);
			} else if ( form_type == 'forgot_pw' ) {
				jQuery('.cataloggi-display-login-form').hide();
				jQuery('.cataloggi-display-register-form').hide();			
				jQuery('.cataloggi-display-forgot-pw-form').fadeIn(500);
			} else {
				// default
				jQuery('.cataloggi-display-login-form').show();
				jQuery('.cataloggi-display-register-form').hide();
				jQuery('.cataloggi-display-forgot-pw-form').hide();
			}
	
		});
		
		
});
	

