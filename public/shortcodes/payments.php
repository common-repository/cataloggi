<div class="cataloggi-single-item">	

<?php 

  // PAYPAL
  if ( isset($_GET['page'] ) && $_GET['page'] == "payments" ) {
	    if ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "processing" ) {
			$show_hide_checkout_forms = '0'; // hide forms
			// checkout form success message
			$success_id = 'checkout_success';
			$success_message = __('Thank you for your purchase!', 'cataloggi');
			echo $success = CTLGGI_Validate::ctlggi_success_msg( $success_id, $success_message );
		} elseif ( isset($_REQUEST['paypal'] ) && $_REQUEST['paypal'] == "completed" ) {
			$show_hide_payments_forms = '0'; // hide forms
			// payments form success message
			$success_id = 'payments_success';
			$success_message = __('Thank you for your purchase!', 'cataloggi');
			echo $success = CTLGGI_Validate::ctlggi_success_msg( $success_id, $success_message );
		} elseif ( isset($_GET['paypal'] ) && $_GET['paypal'] == "pending" ) {
			$show_hide_payments_forms = '0'; // hide forms
			// payments form success message
			$error_id = 'payments_pending_payment';
			$error_message = __('The payment is pending.', 'cataloggi');
			echo $errors = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message );
		} elseif ( isset($_GET['paypal'] ) && $_GET['paypal'] == "failed" ) {
			$show_hide_payments_forms = '0'; // hide forms
			// payments form success message
			$error_id = 'payments_transaction_failed';
			$error_message = __('Transaction failed.', 'cataloggi');
			echo $errors = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message );
		}
  }


   $access_page_ok = '0'; // default
   $order_id  = ''; // default
   if ( isset( $_GET['id'] ) && $_GET['id'] != '' ) {
	   $order_id = $_GET['id'];
	   // check order status, if completed then restrict access
	   $order_status      = get_post_meta( $order_id, '_order_status', true );
	   if ( !empty($order_status) && $order_status != 'completed' ) {
		   if ( isset( $_GET['token'] ) && $_GET['token'] != '' ) {
			   $token = $_GET['token'];
			   //echo 'Order ID: ' . $order_id . ' Access Token: ' . $token;
			   // get order by order id and access token
			   $orderdata = CTLGGI_Single_Order::ctlggi_single_order_data_for_payments_form( $order_id, $token );
			   //$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
			   if ( ! empty($orderdata) ) {
				
				$orderdata_obj = json_decode($orderdata); // convert to obj
				$orderdata     = json_decode($orderdata, true); // convert to array
				
				// Display Object
			    CTLGGI_Developer_Mode::display_object( $object=$orderdata_obj );
					
				$first_name = isset( $orderdata['order_user_data']['first_name'] ) ? sanitize_text_field( $orderdata['order_user_data']['first_name'] ) : '';
				$last_name  = isset( $orderdata['order_user_data']['last_name'] ) ? sanitize_text_field( $orderdata['order_user_data']['last_name'] ) : '';
				$email      = isset( $orderdata['order_user_data']['email'] ) ? sanitize_email( $orderdata['order_user_data']['email'] ) : '';
				$phone      = isset( $orderdata['order_user_data']['phone'] ) ? sanitize_text_field( $orderdata['order_user_data']['phone'] ) : '';
				$company    = isset( $orderdata['order_user_data']['company'] ) ? sanitize_text_field( $orderdata['order_user_data']['company'] ) : '';
				
				$country    = isset( $orderdata['order_billing']['billing_country'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_country'] ) : '';
				$city       = isset( $orderdata['order_billing']['billing_city'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_city'] ) : '';
				$state      = isset( $orderdata['order_billing']['billing_state'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_state'] ) : '';
				$addr_1     = isset( $orderdata['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_addr_1'] ) : '';
				$addr_2     = isset( $orderdata['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_addr_2'] ) : '';
				$zip        = isset( $orderdata['order_billing']['billing_zip'] ) ? sanitize_text_field( $orderdata['order_billing']['billing_zip'] ) : '';
				
				$access_page_ok = '1';
			   }
		   }
	   }
   }

if ($access_page_ok == '1' ) {	
	
?>

<!-- cw-form start -->
<div class="cw-form cw-form-maxwidth">

<div id="ctlggi-payment-methods-holder">

<hr class="cataloggi-hr">

<div class="cw-title cataloggi-uppercase"><?php _e('Select Payment Method', 'cataloggi'); ?></div>

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

<form id="ctlggi-checkout-form" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="ctlggi-global-nonce-for-payment-forms" value="<?php echo wp_create_nonce('ctlggi_global_nonce_for_payment_forms'); ?>"/>
<input type="hidden" name="ctlggi_form_type" id="ctlggi_form_type" value="paymentsform"/>
<input type="hidden" name="ctlggi_order_id" value="<?php echo esc_attr( $order_id ); ?>"/>
<input type="hidden" name="ctlggi_access_token" value="<?php echo esc_attr( $token ); ?>"/>
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
      <input type="text" id="ctlggi_first_name" name="ctlggi_first_name" value="<?php echo esc_attr( $first_name ); ?>" required >
    </div>
  </div>
  
  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Last Name', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_last_name" name="ctlggi_last_name" value="<?php echo esc_attr( $last_name ); ?>" required >
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-7">
    <label for="textinput"><?php _e( 'E-mail', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-envelope"></i>
      <input id="ctlggi_user_email" name="ctlggi_user_email" type="email" value="<?php echo esc_attr( $email ); ?>" required >
    </div>
  </div>
  
  <div class="c-col c-col-5">
    <label for="textinput"><?php _e( 'Phone', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-earphone"></i>
      <input type="text" id="ctlggi_phone" name="ctlggi_phone" value="<?php echo esc_attr( $phone ); ?>">
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Company', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-book"></i>
      <input type="text" id="ctlggi_company" name="ctlggi_company" value="<?php echo esc_attr( $company ); ?>">
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-personal-details-fields -->

<?php 

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
// def
$disable_state_select = '';

if ( '1' != $billing_details ) {
	$billing_details_fields = 'style="display:none;"';
	$billing_disabled = 'disabled="disabled"';
} else {
	$billing_disabled = '';
	$billing_details_fields = '';
	
	// fix if state not emapty
	if ( ! empty($state) ) {
		$disable_state_select = 'disabled="disabled"';
	} else {
		$disable_state_select = '';
	}	
	
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
<?php 
	// Countries
	$countries = CTLGGI_Countries::ctlggi_country_list(); // function
	if ( empty($country) ) {
	   echo '<option selected="selected" value="">' . __('Please Select ...', 'cataloggi') . '</option>';
	} else {
	  $country_name = $countries[$country]; // get country name
	  echo '<option selected="selected" value="' . esc_attr( $country ) . '">' . esc_attr( $country_name ) . '</option>';
	}
	  
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
      <input type="text" id="ctlggi_billing_state" name="ctlggi_billing_state" class="ctlggi_billing_state_input_class" value="<?php echo esc_attr( $state ); ?>" <?php echo $billing_disabled; ?> > <!-- required -->
    </div> 
   </div>
   
  <div id="ctlggi-billing-country-state-dropdown" style="display:none;" > <!-- style="display:none;" -->
    <label for="textinput"><?php _e('State', 'cataloggi'); ?> </label>
    <div class="no-icon">
    
<?php 

	echo '<select id="ctlggi_billing_state" name="ctlggi_billing_state" ' . $billing_disabled . ' ' . $disable_state_select . ' >'; // required
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
      <input type="text" id="ctlggi_billing_city" name="ctlggi_billing_city" autocomplete="off" value="<?php echo esc_attr( $city ); ?>" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
  <div class="c-col c-col-8">
    <label for="textinput"><?php _e('Street Addr. 1', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-equalizer"></i>
      <input type="text" id="ctlggi_billing_addr_1" name="ctlggi_billing_addr_1" value="<?php echo esc_attr( $addr_1 ); ?>" autocomplete="off" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-8">
    <label for="textinput"><?php _e('Street Addr. 2', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-equalizer"></i>
      <input type="text" id="ctlggi_billing_addr_2" name="ctlggi_billing_addr_2" value="<?php echo esc_attr( $addr_2 ); ?>" autocomplete="off" <?php echo $billing_disabled; ?> >
    </div>
  </div>

  <div class="c-col c-col-4">
    <label for="textinput"><?php _e('Postcode/Zip', 'cataloggi'); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-link"></i>
      <input type="text" id="ctlggi_billing_zip" name="ctlggi_billing_zip" value="<?php echo esc_attr( $zip ); ?>" autocomplete="off" required <?php echo $billing_disabled; ?> >
    </div>
  </div>
  
</div>

</fieldset>

</div> <!--/ ctlggi-billing-details-fields -->

<div class="cw-footer">
  <div class="formsubmit">
    <div class="r-row">
      <div class="c-col c-col-6"> 
         &nbsp;<div class="ctlggi-loading-img"></div>
         </div>
       <div class="c-col c-col-6"> 
        <button style="width:100%;" class="btn-cataloggi btn-cataloggi-md btn-cataloggi-orange cataloggi-margin-top-bottom-15" type="submit" id="ctlggi-checkout-form-submit" name="ctlggi-checkout-form-submit">
          <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php _e('Complete Payment', 'cataloggi'); ?>
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
<?php _e('By clicking the "Complete Payment" button, you agree with our ', 'cataloggi'); ?> 
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
	
  } else {
	  _e('This payment is already completed. Please close this window.', 'cataloggi');
  }

?>

</div><!--/ cataloggi-single-item -->