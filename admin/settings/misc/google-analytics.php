
<?php 

$google_analytics_tracking_id  = isset( $ctlggi_google_analytics_options['google_analytics_tracking_id'] ) ? sanitize_text_field( $ctlggi_google_analytics_options['google_analytics_tracking_id'] ) : '';

?>

<div id="tab_container">

<form method="post" action="" id="ctlggi-misc-google-analytics-options-form">

<input type="hidden" name="ctlggi-misc-google-analytics-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_misc_google_analytics_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2 class="padding-top-15"><?php _e('Google Analytics', 'cataloggi'); ?></h2>
        <p><?php _e('Easily connects your Google Analytics account with your WordPress site.', 'cataloggi'); ?></p>
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="google_analytics_tracking_id"><?php _e('Google Analytics Tracking ID', 'cataloggi'); ?></label>
                </th>
                <td>
                    <input name="google_analytics_tracking_id" type="text" id="google_analytics_tracking_id" value="<?php echo esc_attr( stripslashes_deep( $google_analytics_tracking_id ) ); ?>" class="regular-text code">
                    <p class="description"><?php _e('Enter your Google Analytics tracking ID.', 'cataloggi'); ?></p>
                    <p class="description">
					<?php _e('You can get your tracking ID from: ', 'cataloggi'); ?>
                    <a href="https://www.google.com/analytics/" target="_blank"><?php _e('Google Analytics', 'cataloggi'); ?></a>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->