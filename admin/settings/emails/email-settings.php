
<?php 

	// get options
	$email_options = get_option('ctlggi_email_settings_options');
	
	$emails_logo           = isset( $email_options['emails_logo'] ) ? sanitize_text_field( $email_options['emails_logo'] ) : '';

	$enable_smtp           = isset( $email_options['enable_smtp'] ) ? sanitize_text_field( $email_options['enable_smtp'] ) : '';
	$smtp_host             = isset( $email_options['smtp_host'] ) ? sanitize_text_field( $email_options['smtp_host'] ) : '';
	$smtp_auth             = isset( $email_options['smtp_auth'] ) ? sanitize_text_field( $email_options['smtp_auth'] ) : '';
	$smtp_username         = isset( $email_options['smtp_username'] ) ? sanitize_text_field( $email_options['smtp_username'] ) : '';
	$smtp_password         = isset( $email_options['smtp_password'] ) ? sanitize_text_field( $email_options['smtp_password'] ) : '';
	$type_of_encryption    = isset( $email_options['type_of_encryption'] ) ? sanitize_text_field( $email_options['type_of_encryption'] ) : '';
	$smtp_port             = isset( $email_options['smtp_port'] ) ? sanitize_text_field( $email_options['smtp_port'] ) : '';
	$from_email            = isset( $email_options['from_email'] ) ? sanitize_text_field( $email_options['from_email'] ) : '';
	$from_name             = isset( $email_options['from_name'] ) ? sanitize_text_field( $email_options['from_name'] ) : '';

?>

<br class="clear">

<div id="tab_container">

<form method="post" action="" id="ctlggi-email-settings-options-form">

<input type="hidden" name="ctlggi-email-settings-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_email_settings_options_form_nonce'); ?>"/>

<table class="form-table">
    <h2><?php _e('Email Settings', 'cataloggi'); ?></h2>
    <tbody>
<tr>
    <th scope="row"><?php _e('Emails Logo', 'cataloggi'); ?></th>
    <td>
        <input type="text" value="<?php echo stripslashes_deep( $emails_logo ); ?>" name="ctlggi_emails_logo" id="upload_image" class="regular-text">
        <input type="button" value="Select Image" class="button" id="upload_image_button">
         <p class="description"><?php _e('Please select logo image. The logo image displayed on HTML emails only.', 'cataloggi'); ?></p>
    </td>
</tr>
    <tr valign="top">
        <th scope="row"><label for="from_email"><?php _e('From Email Address', 'cataloggi');?></label></th>
        <td><input name="from_email" type="text" id="from_email" value="<?php echo $from_email; ?>" class="regular-text code">
            <p class="description"><?php _e("The default email address which will be used as the From Address to Cataloggi's mail function.", 'cataloggi');?></p></td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><label for="from_name"><?php _e('From Name', 'cataloggi');?></label></th>
        <td><input name="from_name" type="text" id="from_name" value="<?php echo $from_name; ?>" class="regular-text code">
            <p class="description"><?php _e("The default name which will be used as the From Name to Cataloggi's mail function.", 'cataloggi');?></p></td>
    </tr>
    </tbody>
</table>

<table class="form-table">
    <h2><?php _e('SMTP', 'cataloggi'); ?></h2>
    <tbody>
    <tr valign="top">
        <th scope="row"><?php _e('Enable SMTP', 'cataloggi'); ?></th>
        <td>
            <input type="checkbox" value="1" name="enable_smtp" id="enable_smtp" <?php echo ($enable_smtp == '1') ? 'checked' : '' ?>>
            <p class="description"><?php _e('Enable SMTP when sending an emails.', 'cataloggi'); ?></p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><label for="smtp_host"><?php _e('SMTP Host', 'cataloggi');?></label></th>
        <td><input name="smtp_host" type="text" id="smtp_host" value="<?php echo $smtp_host; ?>" class="regular-text code">
            <p class="description"><?php _e('The SMTP server which will be used to send email. For example: smtp.gmail.com', 'cataloggi');?></p></td>
    </tr>
    
    <tr>
    <th scope="row"><label for="smtp_auth"><?php _e('SMTP Authentication', 'cataloggi');?></label></th>
    <td>
        <select name="smtp_auth" id="smtp_auth">
            <option value="true" <?php echo selected( $smtp_auth, 'true', false );?>><?php _e('True', 'cataloggi');?></option>
            <option value="false" <?php echo selected( $smtp_auth, 'false', false );?>><?php _e('False', 'cataloggi');?></option>
        </select>
        <p class="description"><?php _e('Whether to use SMTP Authentication when sending an email (recommended: True).', 'cataloggi');?></p>
    </td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><label for="smtp_username"><?php _e('SMTP Username', 'cataloggi');?></label></th>
        <td><input name="smtp_username" type="text" id="smtp_username" value="<?php echo $smtp_username; ?>" class="regular-text code">
            <p class="description"><?php _e('Your SMTP Username.', 'cataloggi');?></p></td>
    </tr>
    
    <tr valign="top">
        <th scope="row"><label for="smtp_password"><?php _e('SMTP Password', 'cataloggi');?></label></th>
        <td><input name="smtp_password" type="text" id="smtp_password" value="<?php echo $smtp_password; ?>" class="regular-text code">
            <p class="description"><?php _e('Your SMTP Password.', 'cataloggi');?></p></td>
    </tr>
    
    <tr>
    <th scope="row"><label for="type_of_encryption"><?php _e('Type of Encryption', 'cataloggi');?></label></th>
    <td>
        <select name="type_of_encryption" id="type_of_encryption">
            <option value="tls" <?php echo selected( $type_of_encryption, 'tls', false );?>><?php _e('TLS', 'cataloggi');?></option>
            <option value="ssl" <?php echo selected( $type_of_encryption, 'ssl', false );?>><?php _e('SSL', 'cataloggi');?></option>
            <option value="none" <?php echo selected( $type_of_encryption, 'none', false );?>><?php _e('No Encryption', 'cataloggi');?></option>
        </select>
        <p class="description"><?php _e('The encryption which will be used when sending an email (recommended: TLS).', 'cataloggi');?></p>
    </td>
    </tr>                   
    
    <tr valign="top">
        <th scope="row"><label for="smtp_port"><?php _e('SMTP Port', 'cataloggi');?></label></th>
        <td><input name="smtp_port" type="text" id="smtp_port" value="<?php echo $smtp_port; ?>" class="regular-text code">
            <p class="description"><?php _e('The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.', 'cataloggi');?></p></td>
    </tr>                                      
    
    </tbody>
</table>
    
    <?php //submit_button(); ?>
    <p class="submit"><input type="submit" name="ctlggi_email_settings_update" id="ctlggi_email_settings_update" class="button button-primary" value="<?php _e('Save Changes', 'cataloggi')?>"></p>
</form>

</div><!--/ #tab_container-->