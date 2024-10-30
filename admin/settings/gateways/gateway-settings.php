
<?php 

$checkout_notes_field  = isset( $ctlggi_payment_gateway_options['checkout_notes_field'] ) ? sanitize_text_field( $ctlggi_payment_gateway_options['checkout_notes_field'] ) : '';

?>

<br class="clear">

<div id="tab_container">

<form method="post" action="" id="ctlggi-payment-gateway-options-form">

<input type="hidden" name="ctlggi-payment-gateway-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_payment_gateway_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2><?php _e('Payment Gateway Settings', 'cataloggi'); ?></h2>
        <tbody>
    <tr>
    <th scope="row"><?php _e('Default Gateway', 'cataloggi'); ?></th>
    <td>
        <select id="ctlggi_default_payment_gateway" name="ctlggi_default_payment_gateway">
        
<?php 
    $defaultgateway = $ctlggi_payment_gateway_options['default_payment_gateway'];

    $payment_gateways = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();
	$json = json_encode( $payment_gateways ); // convert array to json

	$gateways_obj   = json_decode( $json ); // Translate into an object
	$gateways_array = json_decode( $json, true ); // Translate into an array
	
	// if has contents
	if(count($gateways_obj)>0)
	{	
	
	  if ( empty($defaultgateway) ) {
		 echo '<option selected="selected" value="">' . __('Please Select Gateway', 'cataloggi') . '</option>';  
	  }
	
	  // Payment Methods
	  foreach( $gateways_obj as $gateway => $value )
	  {
		  
		if ( $defaultgateway == $gateway ) {
		  echo '<option selected="selected" value="' . esc_attr( $gateway ) . '">' . esc_attr( $value->payment_gateway_label ) . '</option>';  
		}
		
		   // check if option exist
		   if( get_option('ctlggi_gateway_' . $gateway . '_options') ){
			   // get options for gateways
			   $gateways_options = get_option('ctlggi_gateway_' . $gateway . '_options');
			   // check if gateway enabled
			   if ( $gateways_options['ctlggi_' . $gateway . '_enabled'] == '1' ) {
				   
                  echo '<option value="' . esc_attr( $gateway ) . '">' . esc_attr( $value->payment_gateway_label ) . '</option>';  
				 
			   }
		   }
		  
		
	  }
	  
	}
?>

        </select>
        <p class="description"><?php _e('Default gateway for checkout.', 'cataloggi'); ?></p>
    </td>
</tr>

            <tr>
                <th scope="row"><?php _e('Notes', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="checkout_notes_field" id="checkout_notes_field" <?php echo ($checkout_notes_field == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the Notes field on the checkout page.', 'cataloggi'); ?></p>
                </td>
            </tr>

        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->