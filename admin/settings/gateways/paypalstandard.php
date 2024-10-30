

<br class="clear">

<div id="tab_container">

<form method="post" action="" id="ctlggi-paypal-standard-options-form">

<input type="hidden" name="ctlggi-paypal-standard-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_paypalstandard_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2><?php _e('PayPal Standard', 'cataloggi'); ?></h2>
        <tbody>
<tr>
    <th scope="row"><?php _e('Enable/Disable', 'cataloggi'); ?></th>
    <td>
        <input type="checkbox" value="1" id="ctlggi_paypalstandard_enabled" name="ctlggi_paypalstandard_enabled" <?php echo ($ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_enabled'] == '1') ? 'checked' : '' ?>>
        <p class="description"><?php _e('Enable PayPal Standard as a payment method.', 'cataloggi'); ?></p>
    </td>
</tr>
<tr>
<tr>
    <th scope="row"><?php _e('Billing Details', 'cataloggi'); ?></th>
    <td>
        <input type="checkbox" value="1" id="ctlggi_paypalstandard_show_billing_details" name="ctlggi_paypalstandard_show_billing_details" <?php echo ($ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_show_billing_details'] == '1') ? 'checked' : '' ?>>
        <p class="description"><?php _e('Display billing details fields on the checkout page.', 'cataloggi'); ?></p>
    </td>
</tr>
<tr>
    <th scope="row"><?php _e('Title', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_title'] ); ?>" name="ctlggi_paypalstandard_title" id="ctlggi_paypalstandard_title" class="regular-text">
    </td>
</tr>
<tr valign="top">
    <th scope="row">
        <label for="ctlggi_paypalstandard_description"><?php _e('Description', 'cataloggi'); ?></label>
    </th>
    <td>
        <textarea class="widefat" rows="4" cols="90" name="ctlggi_paypalstandard_description" id="ctlggi_paypalstandard_description"><?php echo esc_attr(  $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_description'] ); ?></textarea>
        <p class="description"><?php _e('Description will be shown on the checkout page.', 'cataloggi'); ?></p>
    </td>
</tr>
<tr>
    <th scope="row"><?php _e('PayPal Email', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_email'] ); ?>" name="ctlggi_paypalstandard_email" id="ctlggi_paypalstandard_email" class="regular-text">
        <p class="description"><?php _e('Your PayPal email address for payments.', 'cataloggi'); ?></p>
    </td>
</tr>
<tr>
    <th scope="row"><?php _e('PayPal Page Style', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_page_style'] ); ?>" name="ctlggi_paypalstandard_page_style" id="ctlggi_paypalstandard_page_style" class="regular-text">
        <p class="description"><?php _e('Enter the name of the page style you wish to use or leave blank for default.', 'cataloggi'); ?></p>
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Live Api Username', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_live_api_username'] ) ); ?>" name="ctlggi_paypalstandard_live_api_username" id="ctlggi_paypalstandard_live_api_username" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Live Api Password', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_live_api_password'] ) ); ?>" name="ctlggi_paypalstandard_live_api_password" id="ctlggi_paypalstandard_live_api_password" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Live Api Signature', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_live_api_signature'] ) ); ?>" name="ctlggi_paypalstandard_live_api_signature" id="ctlggi_paypalstandard_live_api_signature" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Test Api Username', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_test_api_username'] ) ); ?>" name="ctlggi_paypalstandard_test_api_username" id="ctlggi_paypalstandard_test_api_username" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Test Api Password', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_test_api_password'] ) ); ?>" name="ctlggi_paypalstandard_test_api_password" id="ctlggi_paypalstandard_test_api_password" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Test Api Signature', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_test_api_signature'] ) ); ?>" name="ctlggi_paypalstandard_test_api_signature" id="ctlggi_paypalstandard_test_api_signature" class="regular-text">
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Account Mode', 'cataloggi'); ?></th>
    <td>
        <p>
        <input type="radio" name="ctlggi_paypalstandard_account_mode" id="modeTest" value="test" <?php echo ($ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_account_mode'] == 'test') ? 'checked' : '' ?>>
        <label for="modeTest"><?php _e("Test", 'cataloggi'); ?></label>
        </p>
        <p>
        <input type="radio" name="ctlggi_paypalstandard_account_mode" id="modeLive" value="live" <?php echo ($ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_account_mode'] == 'live') ? 'checked' : '' ?>>
        <label for="modeLive"><?php _e("Live", 'cataloggi'); ?></label>
        </p>
    </td>
</tr>

        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->