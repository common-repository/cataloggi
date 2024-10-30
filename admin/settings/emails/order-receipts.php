
<?php 

	// get options
	$order_receipts_options = get_option('ctlggi_order_receipts_options');

?>

<br class="clear">

<div id="tab_container">

<form method="post" action="" id="ctlggi-order-receipts-options-form">

<input type="hidden" name="ctlggi-order-receipts-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_order_receipts_options_form_nonce'); ?>"/>

<table class="form-table">
    <h2><?php _e('Order Receipts', 'cataloggi'); ?></h2>
    <p class="description"><?php _e('Buyer will receive an order receipt after purchase.', 'cataloggi'); ?></p>
    <tbody>
<tr>
    <th scope="row"><?php _e('"From" Name', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $order_receipts_options['from_name'] ) ); ?>" name="ctlggi_from_name" id="ctlggi_from_name" class="regular-text">
         <p class="description"><?php _e('Order receipt sent from name. E.g. your name or business name.', 'cataloggi'); ?></p>
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('"From" Email', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $order_receipts_options['from_email'] ) ); ?>" name="ctlggi_from_email" id="ctlggi_from_email" class="regular-text">
         <p class="description"><?php _e('Order receipt sent from email. Your email address.', 'cataloggi'); ?></p>
    </td>
</tr>

<tr>
    <th scope="row"><?php _e('Subject', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo esc_attr( stripslashes_deep( $order_receipts_options['subject'] ) ); ?>" name="ctlggi_subject" id="ctlggi_subject" class="regular-text">
         <p class="description"><?php _e('Email subject of the order receipt.', 'cataloggi'); ?></p>
    </td>
</tr>

    </tbody>
</table>

<table id="ctlggi_order_receipt_email_content_table" class="form-table" style="width:100%;" >

<tr>

    <td>
<label for="ctlggi_email_content"><strong><?php _e('Email Content', 'cataloggi'); ?></strong></label>
<?php 
	// <textarea  style="width:100%;" id="product_desc_section" name="product_desc_section" class="product_desc_section_textarea" rows="24" cols="70"></textarea>
	// add wysiwyg editor in Wordpress meta box
	// source: https://codex.wordpress.org/Function_Reference/wp_editor	
	// only low case [a-z], no hyphens and underscores
	$editor_id = 'ctlggi_order_receipt_email_content_editor';
	$content = stripslashes_deep( $order_receipts_options['email_content'] );
	// 'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), // note that spaces in this list seem to cause an issue
	wp_editor( $content, $editor_id, array(
		'wpautop'       => true, // remove <br> and <p> tags if set to true
		'media_buttons' => false,
		'textarea_name' => 'ctlggi_email_content',
		'textarea_rows' => 30,
		'teeny'         => true
	) );
?>
        
        <br>
        
        <p class="description"> <?php _e('The following template tags are available:', 'cataloggi'); ?> </p>
        
        <br>
        
        <p class="description"> <?php _e('[user_first_name]  - user (buyer) first name', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[user_last_name]   - user (buyer) last name', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[user_email]       - user (buyer) email', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[user_company]     - user (buyer) company or business name', 'cataloggi'); ?> </p>
        
        <br>
        
        <p class="description"> <?php _e('[billing_country]  - billing country', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[billing_city]     - billing city', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[billing_state]    - billing state', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[billing_addr_1]   - billing address line 1', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[billing_addr_2]   - billing address line 2', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[billing_zip]      - billing zip', 'cataloggi'); ?> </p>
        
        <br>
        
        <p class="description"> <?php _e('[items]            - order items list', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[order_total]      - order total', 'cataloggi'); ?> </p>
        
        <br>
        
        <p class="description"> <?php _e('[order_status]     - the status of the order E.g. Completed, Failed etc.', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[order_id]         - order ID', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[transaction_id]   - transaction id', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[order_key]        - order key', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[order_date]       - order date', 'cataloggi'); ?> </p>
        
        <br>
        
        <p class="description"> <?php _e('[payment_gateway]       - gateway base name', 'cataloggi'); ?> </p>
        <p class="description"> <?php _e('[payment_gateway_data]  - gateway title (name), gateway notes, bank account details (only for Direct Bank Transfer)', 'cataloggi'); ?> </p>

    </td>
</tr>

</table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->