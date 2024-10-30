<div class="cataloggi-single-item">	

<?php 

  // defaults
  $total = '0';
  $price_in_total = '0';
  // current user defaults
  $username    = '';
  $useremail   = '';
  $firstname   = '';
  $lastname    = '';
  $displayname = '';
  $userid      = '';
  
  $create_an_account   = '';
  $credit_card_details = '';
  $billing_details     = '';
  
  $show_hide_checkout_forms = '1';
  
  // PAYPAL
  if ( isset($_REQUEST['page'] ) && $_REQUEST['page'] == "checkout" ) {
	  
	    if ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "processing" ) {
			$show_hide_checkout_forms = '0'; // hide forms
			// checkout form success message
			$success_id = 'checkout_success';
			$success_message = __('Thank you for your purchase!', 'cataloggi');
			echo $success = CTLGGI_Validate::ctlggi_success_msg( $success_id, $success_message );
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "completed" ) {
			$show_hide_checkout_forms = '0'; // hide forms
			// checkout form success message
			$success_id = 'checkout_success';
			$success_message = __('Thank you for your purchase!', 'cataloggi');
			echo $success = CTLGGI_Validate::ctlggi_success_msg( $success_id, $success_message );
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "pending" ) {
			$show_hide_checkout_forms = '0'; // hide forms
			// checkout form success message
			$error_id = 'checkout_pending_payment';
			$error_message = __('The payment is pending.', 'cataloggi');
			echo $errors = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message );
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "failed" ) {
			$show_hide_checkout_forms = '0'; // hide forms
			// checkout form success message
			$error_id = 'checkout_transaction_failed';
			$error_message = __('Transaction failed.', 'cataloggi');
			echo $errors = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message );
		}
  }
  
  if ( $show_hide_checkout_forms == '1' ) {
	  
  // cookie name
  $cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
  
  // check if cookie exist
  if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_totals_cookie_name ) === true ) 
  {	
			
	// read the cookie
	$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_totals_cookie_name, $default = '');
	$cart_totals = $cookie;
	
	$arr_cart_totals = json_decode($cart_totals, true); // convert to array
	$obj_cart_totals = json_decode($cart_totals); // convert to object
	
	// Display Object
	CTLGGI_Developer_Mode::display_object( $object=$obj_cart_totals );
	
	// if cart has contents
	if(count($obj_cart_totals)>0)
	{
		$subtotal = $obj_cart_totals->subtotal;
		$total    = $obj_cart_totals->total;
	}
  }
	
  // cookie name
  $cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();

  // check if cookie exist
  if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) 
  {
	// read the cookie
	$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');
	$cart_items = $cookie;

	$arr_cart_items = json_decode($cart_items, true); // convert to array
	$obj_cart_items = json_decode($cart_items); // convert to object
	
	// Display Object
	CTLGGI_Developer_Mode::display_object( $object=$obj_cart_items );
	
	// if cart has contents
	if(count($obj_cart_items)>0)
	{	
	  
	  foreach($obj_cart_items as $key => $value)
	  {
		//echo $value->item_id . '<br>'; 
		// Get the meta for all items
		$meta = get_post_meta( $value->item_id );
		//$item_payment_type    = isset( $value->item_payment_type ) ? sanitize_text_field( $value->item_payment_type ) : '';	
		
		/*
		echo '<pre>';
		print_r( $meta );
		echo '</pre>';
		*/
		
	  }
	  
	}
	
	
?>

<!-- cw-form start -->
<div class="cw-form cw-form-maxwidth">

<?php 
$loggedin = '1';
// check if user logged in, if not show login form
if ( ! is_user_logged_in() ) {
$loggedin = '0';
?>

<div id="ctlggi-returning-customer-div" class="cw-title cataloggi-uppercase"><?php _e('Returning customer?', 'cataloggi'); ?> <a id="ctlggi-toggle-login-form" href="/" onclick="return false;"><?php _e('Click here to login', 'cataloggi'); ?></a></div>

<div id="ctlggi-show-hide-login-form"><!-- jQuery -->
<?php echo do_shortcode('[ctlggi_login_form]'); // login form ?>
</div>

<?php 
} // end is_user_logged_in
else {
	
  // if logged in get current user data
  $current_user = wp_get_current_user();
  
  /*
  echo '<pre>';
  print_r( $current_user );
  echo '</pre>';
  */
  
  $username    = $current_user->user_login;
  $useremail   = $current_user->user_email;
  $firstname   = $current_user->user_firstname;
  $lastname    = $current_user->user_lastname;
  $displayname = $current_user->display_name;
  $userid      = $current_user->ID;
  
}

$found = false;
// if cart has contents
if(count($obj_cart_items)>0){	
	// check if item_payment_type value "subscription" exist
	foreach($obj_cart_items as $key => $data) {
		if ($data->item_payment_type == 'subscription' ) {
			$found = true;
			break; // no need to loop anymore, as we have found the item => exit the loop
		}
	}
}
// if item_payment_type is NOT a subscription and total is 0 then set gateway to none
// FREE Payments none.php 
if ( $total == '0'  && $found === false ) {
	$defaultgateway      = 'none'; // gateway 
	$create_an_account   = '1';  // return 0 or 1
	$credit_card_details = '0';  // return 0 or 1
	$billing_details     = '0';  // return 0 or 1
} else {

?>

<div id="ctlggi-payment-methods-holder">

<hr class="cataloggi-hr">

<div class="cw-title cataloggi-uppercase"><?php _e('Payment Method', 'cataloggi'); ?></div>

<div class="r-row cataloggi-margin-bottom-15">

<?php 

	// get options
	$ctlggi_payment_gateway_options = get_option('ctlggi_payment_gateway_options');
	$defaultgateway = $ctlggi_payment_gateway_options['default_payment_gateway']; // option
	
    $payment_gateways = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();
	$json = json_encode( $payment_gateways ); // convert array to json

	$gateways_obj   = json_decode( $json ); // Translate into an object
	$gateways_array = json_decode( $json, true ); // Translate into an array
	
	// if has contents
	if(count($gateways_obj)>0)
	{	
	
	  echo '<div class="c-col c-col-12">';
	  
	  foreach( $gateways_obj as $gateway => $value )
	  {
		 
		 $checked = '';
		 if ( $defaultgateway == $gateway ) {
			 
			$checked = 'checked="checked"';
			
			$create_an_account   = $value->create_an_account;   // return 0 or 1
			$credit_card_details = $value->credit_card_details; // return 0 or 1
			$billing_details     = $value->billing_details;     // return 0 or 1
			
		 }
		 
		// check if option exist
		if( get_option('ctlggi_gateway_' . $gateway . '_options') ){
		   // get options for gateways
		   $gateways_options = get_option('ctlggi_gateway_' . $gateway . '_options');
		   // check if gateway enabled
		   if ( $gateways_options['ctlggi_' . $gateway . '_enabled'] == '1' ) {
			   
			  echo '<label style="width: auto; margin-right:18px;">';
			  echo '<input type="radio" class="ctlggi_payment_gateway_radio" name="ctlggi_payment_gateway_radio" id="' . esc_attr( $gateway ) . '" value="' . esc_attr(  $gateway ) . '" ' . esc_attr( $checked ) . ' >';
			  echo '<span class="lbl padding-8">' . esc_attr( $gateways_options['ctlggi_' . $gateway . '_title'] ) . '</span>';
			  echo '</label>';
			  
			  echo '<input type="hidden" name="' . esc_attr( $gateway ) . '_create_an_account" id="' . esc_attr( $gateway ) . '_create_an_account" value="' . esc_attr(  $value->create_an_account ) . '"/>';
			  echo '<input type="hidden" name="' . esc_attr( $gateway ) . '_credit_card_details" id="' . esc_attr( $gateway ) . '_credit_card_details" value="' . esc_attr( $value->credit_card_details ) . '"/>';
			  echo '<input type="hidden" name="' . esc_attr( $gateway ) . '_billing_details" id="' . esc_attr( $gateway ) . '_billing_details" value="' . esc_attr( $value->billing_details ) . '"/>';

		   }
		}
		  
	  }
	  
	  
	  // gateway description
	  foreach( $gateways_obj as $gateway_desc => $value_desc )
	  { 
		// check if option exist
		if( get_option('ctlggi_gateway_' . $gateway_desc . '_options') ){
		   // get options for gateways
		   $gateways_options = get_option('ctlggi_gateway_' . $gateway_desc . '_options');
		   // check if gateway enabled
		   if ( $gateways_options['ctlggi_' . $gateway_desc . '_enabled'] == '1' ) {
			  
			  // get gateway option description
			  $description = $gateways_options['ctlggi_' . $gateway_desc . '_description'];
			  
			  if ( $defaultgateway == $gateway_desc ) {
			  // output gateway description
			  echo '<div id="default_gateway_description" class="cataloggi-checkout-gateway-description">' . esc_attr( stripslashes_deep( $description ) ) . '</div>';
			  }
			  
			  echo '<div style="display:none;" id="' . esc_attr( $gateway_desc ) . '_gateway_description" class="cataloggi-checkout-gateway-description">' . esc_attr( stripslashes_deep( $description ) ) . '</div>';

		   }
		}
		  
	  }
	 
	  
	  echo '</div>';
	  
	}

	/*
	echo '<pre>';
	print_r( $gateways_array );
	echo '</pre>';
	*/

?>

</div>
</div> <!--/ ctlggi-payment-methods-holder -->

<?php 
} // end FREE Payments none.php 
?>


<form id="ctlggi-checkout-form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="ctlggi-global-nonce-for-payment-forms" value="<?php echo wp_create_nonce('ctlggi_global_nonce_for_payment_forms'); ?>"/>
<input type="hidden" name="ctlggi_form_type" id="ctlggi_form_type" value="checkoutform"/>
<?php 
// for third party gateways - current gateway
echo '<input type="hidden" class="ctlggi-current-gateway" name="ctlggi_current_gateway" id="ctlggi_current_gateway" value="' . esc_attr( $defaultgateway ) . '"/>';
// default gatway ( db option)
echo '<input type="hidden" class="ctlggi_default_gateway_class" name="ctlggi_default_gateway" id="ctlggi_default_gateway" value="' . esc_attr( $defaultgateway ) . '"/>';
?>

<div class="ctlggi-personal-details-fields">

<div class="textlabel-forms-bold cataloggi-uppercase"><?php _e('Personal Details', 'cataloggi'); ?></div>
<fieldset>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'First Name', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_first_name" name="ctlggi_first_name" value="<?php echo esc_attr( $firstname ); ?>" required >
    </div>
  </div>
  
  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Last Name', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_last_name" name="ctlggi_last_name" value="<?php echo esc_attr( $lastname ); ?>" required >
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-7">
    <label for="textinput"><?php _e( 'E-mail', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-envelope"></i>
      <input id="ctlggi_user_email" name="ctlggi_user_email" type="email" value="<?php echo esc_attr( $useremail ); ?>" required >
    </div>
  </div>
  
  <div class="c-col c-col-5">
    <label for="textinput"><?php _e( 'Phone', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-earphone"></i>
      <input type="text" id="ctlggi_phone" name="ctlggi_phone" value="">
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Company', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-book"></i>
      <input type="text" id="ctlggi_company" name="ctlggi_company" value="">
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-personal-details-fields -->


<?php 
// check if user logged in, if not show create an account fields
if ( ! is_user_logged_in() ) {
?>

<div class="ctlggi-create-an-account-fields">

<div class="textlabel-forms-bold cataloggi-uppercase"><?php _e('Create an Account', 'cataloggi'); ?></div>

<fieldset>

<div class="r-row">

  <div class="c-col c-col-8">
    <label for="textinput"><?php _e( 'Username', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_username" name="ctlggi_username" value="" required >
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Password', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-lock"></i>
      <input id="ctlggi_user_pass" name="ctlggi_user_pass" type="password" autocomplete="off" value="" required >
    </div>
  </div>
  
  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Password Again', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-lock"></i>
      <input id="ctlggi_user_pass_again" name="ctlggi_user_pass_again" type="password" autocomplete="off" value="" required >
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-create-an-account-fields -->

<?php 
} // end is_user_logged_in

if ( '1' != $credit_card_details ) {
	$card_details_fields = 'style="display:none;"';
	$card_disabled = 'disabled="disabled"';
} else {
	$card_disabled = '';
	$card_details_fields = '';
}
?>

<div class="ctlggi-credit-card-details-fields" <?php echo $card_details_fields; ?> >

<div class="textlabel-forms-bold cataloggi-uppercase"><?php _e('Credit Card Details', 'cataloggi'); ?></div>
<fieldset>

<div class="r-row">

  <div class="c-col c-col-7">
    <label for="textinput"><?php _e('Card Number', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-calendar"></i>
      <input id="ctlggi_card_number" class="ctlggi-card-number" type="text" pattern="[0-9]{13,16}" size="20" autocomplete="off" value="" required <?php echo $card_disabled; ?> />
    </div>
  </div>
  
  <div class="c-col c-col-5">
    <label for="textinput"><?php _e('Card Expiry Date', 'cataloggi'); ?> </label>
    <div class="c-col-no-padding c-col-5">
    <div class="no-icon">
          <select id="ctlggi_card_expiry_month" class="ctlggi-card-expiry-month" required <?php echo $card_disabled; ?> >
            <option value="" selected="selected">&nbsp;</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
      </div>
    </div>
    <div class="c-col-no-padding c-col-7"> 
    <div class="no-icon">
      <select id="ctlggi_card_expiry_year" class="ctlggi-card-expiry-year" required <?php echo $card_disabled; ?> >
          <option selected="selected" value="">&nbsp;</option>
            <?php 
			$year = date("Y");
			echo "<option value='" . esc_attr( $year ) . "'>" . esc_attr( $year ) . "</option>";
            $yearslist = $year;
            $x5=1;
            $value5 = $yearslist;
            while($x5<=35)
              {
               $value5 = $value5 + 1;
              echo "<option value='" . esc_attr( $value5 ) . "'>" . esc_attr( $value5 ) . "</option>";
              $x5++;
              } 
            ?>            
      </select>
    </div>
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-8">
    <label for="textinput"><?php _e( 'Name on the Card', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-lock"></i>
      <input id="ctlggi_name_on_the_card"  class="ctlggi-name-on-the-card" type="text" autocomplete="off" value="" required <?php echo $card_disabled; ?> >
    </div>
  </div>
  
  <div class="c-col c-col-4">
    <label for="textinput"><?php _e( 'CVC/CVV', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-indent-left"></i>
      <input id="ctlggi_card_cvc" class="ctlggi-card-cvc" type="password" autocomplete="off" maxlength="6" pattern="[0-9]{3,4}" value="" required <?php echo $card_disabled; ?> />
    </div>
  </div>
  
</div>

<div class="r-row">
  
  <div class="c-col c-col-6">
  <div class="no-icon">
  <img class="cards-imgs" src="<?php echo esc_url( plugins_url( '/cataloggi/assets/images/credit-cards.png') ); ?>" alt="Secure Payments" />
  </div>
  <div class="ssl-license-text"><?php _e('SSL encrypted secure payments.', 'cataloggi'); ?></div>
  </div>
  
  <div class="c-col c-col-6">
  
  </div>

</div>

</fieldset>

</div> <!--/ ctlggi-credit-card-details-fields -->

<?php 

if ( '1' != $billing_details ) {
	$billing_details_fields = 'style="display:none;"';
	$billing_disabled = 'disabled="disabled"';
} else {
	$billing_disabled = '';
	$billing_details_fields = '';
}
?>
<div class="ctlggi-billing-details-fields" <?php echo $billing_details_fields; ?> >

<div class="textlabel-forms-bold cataloggi-uppercase"><?php _e('Billing Details', 'cataloggi'); ?></div>

<fieldset>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e('Country', 'cataloggi'); ?> </label>
    <div class="no-icon">
          <select id="ctlggi_billing_country" name="ctlggi_billing_country" required <?php echo $billing_disabled; ?> >
            <option value="" selected="selected"><?php _e('Please Select ...', 'cataloggi'); ?></option>     
<?php 

	  // Countries
	  $countries = CTLGGI_Countries::ctlggi_country_list(); // function
	  foreach($countries as $countrycode => $country)
	  {
		  
		$states = CTLGGI_Countries::ctlggi_get_states( $countrycode ); // function
		
		// if returns empty country do not have states
		if (empty($states)) {
			$dropdisplay = '0';
		} else {
			$dropdisplay = '1';
		}
		
		echo '<option data-billing-country-code="' . esc_attr( $countrycode ) . '" data-billing-state-drop-display="' . esc_attr( $dropdisplay ) . '" value="' . esc_attr( $countrycode ) . '">' . esc_attr( $country ) . '</option>';  

	  }
	 
?>
            
          </select>
    </div>
  </div>
  
  <div class="c-col c-col-6">
   
   <div id="ctlggi-billing-country-state-field">
    <label for="textinput"><?php _e('State', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-tree-deciduous"></i>
      <input type="text" id="ctlggi_billing_state" name="ctlggi_billing_state" value="" <?php echo $billing_disabled; ?> > <!-- required -->
    </div> 
   </div>
   
  <div id="ctlggi-billing-country-state-dropdown" style="display:none;" > <!-- style="display:none;" -->
    <label for="textinput"><?php _e('State', 'cataloggi'); ?> </label>
    <div class="no-icon">
    
<?php 

	echo '<select id="ctlggi_billing_state" name="ctlggi_billing_state" ' . $billing_disabled . ' >'; // required
	echo '<option value="" selected="selected">' . __('Please Select ...', 'cataloggi') . '</option>';
			
	  // Countries
	  $getcountries = CTLGGI_Countries::ctlggi_country_list(); // function
	  foreach($getcountries as $getcountrycode => $getcountry)
	  {
		
		if ( $getcountrycode ) {
			
			$getstates = CTLGGI_Countries::ctlggi_get_states( $getcountrycode ); // function
			
			  foreach($getstates as $key => $value)
			  {
				 // convert accented chars to html
				$output = htmlentities($value, 0, "UTF-8");
				if ($output == "") {
					$output = htmlentities(utf8_encode($value), 0, "UTF-8");
				}
				//$trusted_value = CTLGGI_Countries::ctlggi_replace_accents($value);
				echo '<option id="ctlggi_billing_states_' . esc_attr( $getcountrycode ) . '" value="' . esc_attr($output) . '">' . esc_attr($output) . '</option>'; 
			  }
			  
		} 
		

	  }
	  
	echo '</select>';
	  

?>
     
    </div>
  
   </div> 
    
     
  </div>

</div> 

<div class="r-row">

  <div class="c-col c-col-4">
    <label for="textinput"><?php _e('City', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-tree-deciduous"></i>
      <input type="text" id="ctlggi_billing_city" name="ctlggi_billing_city" autocomplete="off" value="" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
  <div class="c-col c-col-8">
    <label for="textinput"><?php _e('Street Addr. 1', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-equalizer"></i>
      <input type="text" id="ctlggi_billing_addr_1" name="ctlggi_billing_addr_1" value="" autocomplete="off" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-8">
    <label for="textinput"><?php _e('Street Addr. 2', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-equalizer"></i>
      <input type="text" id="ctlggi_billing_addr_2" name="ctlggi_billing_addr_2" value="" autocomplete="off" <?php echo $billing_disabled; ?> >
    </div>
  </div>

  <div class="c-col c-col-4">
    <label for="textinput"><?php _e('Postcode/Zip', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-link"></i>
      <input type="text" id="ctlggi_billing_zip" name="ctlggi_billing_zip" value="" autocomplete="off" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-billing-details-fields -->

<?php 
$checkout_notes_field  = isset( $ctlggi_payment_gateway_options['checkout_notes_field'] ) ? sanitize_text_field( $ctlggi_payment_gateway_options['checkout_notes_field'] ) : '';
if ( $checkout_notes_field == '1' ) {
?>
<div class="ctlggi-order-extra-notes-fields">

<div class="textlabel-forms-bold cataloggi-uppercase"><?php _e('Notes', 'cataloggi'); ?></div>

<fieldset>

<div class="r-row">

  <div class="c-col c-col-12">
    <label for="textinput"><?php _e( 'Anything you feel we need to know.', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-comment"></i>
       <textarea id="cus_extra_notes" name="cus_extra_notes" spellcheck="true" rows="2" placeholder=""></textarea>
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-extra-order-notes-fields -->
<?php 
}
?>

<div class="cw-footer">
  <div class="formsubmit">
    <div class="r-row">
      <div class="c-col c-col-6"> 
         &nbsp;<div class="ctlggi-loading-img"></div>
         </div>
       <div class="c-col c-col-6"> 
        <button style="width:100%;" class="btn-cataloggi btn-cataloggi-md btn-cataloggi-orange cataloggi-margin-top-bottom-15" type="submit" id="ctlggi-checkout-form-submit" name="ctlggi-checkout-form-submit">
          <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php _e('Place Order', 'cataloggi'); ?>
        </button>
      </div>
    </div>
  </div>
</div>

</form>

<?php 
// get options
$ctlggi_cart_options = get_option('ctlggi_cart_options');

$page_id = $ctlggi_cart_options['terms_page'];
// check if value not 0 
if ( $page_id != '0' || ! empty($page_id) ) {
	// get page link by post id
	$page_link  = get_permalink( $page_id );
	$page_title = get_the_title( $page_id );
?>

<div id="ctlggi-terms-link" class="footer-extra-data margin-top-10">
<?php _e('By clicking the "Place Order" button, you agree with our ', 'cataloggi'); ?> 
<a href="<?php echo esc_url( $page_link ); ?>"><?php echo esc_attr( $page_title ); ?></a>
</div>

<?php 
}
?>

</div>
<!-- cw-form end -->

<!-- jQuery payment gateway(s) messages -->
<div class="ctlggi-payment-gateway-messages"></div> 

<!-- jQuery -->
<div class="show-checkout-form-return-data"></div>

<?php 	
	
  }
  } // show_hide_checkout_forms

?>

</div><!--/ cataloggi-single-item -->