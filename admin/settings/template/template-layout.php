

<div id="tab_container">

<form method="post" action="" id="ctlggi-template-options-form">

<input type="hidden" name="ctlggi-template-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_template_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2 class="padding-top-15"><?php _e('Layout', 'cataloggi'); ?></h2>
        <p><?php _e('The following options affect the catalog layout style displayed on the frontend.', 'cataloggi'); ?></p>
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="inner_template_header"><?php _e('Inner Template Header', 'cataloggi'); ?></label>
                </th>
                <td>
                    <textarea class="widefat" rows="7" cols="90" name="inner_template_header"><?php echo esc_textarea( stripslashes( $ctlggi_template_options['inner_template_header'] ) ); ?></textarea>
                    <p class="description"><?php _e('Use this area to customize the theme layout. For example: remove the "container" class then add "width:100%;" into the style tag. This  will change the theme width.', 'cataloggi'); ?></p>
                    <p class="description"><?php _e('by default:', 'cataloggi'); ?>  <?php _e('&lt;div class="container" style="margin-left: auto; margin-right: auto;"&gt;', 'cataloggi'); ?> </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="inner_template_footer"><?php _e('Inner Template Footer', 'cataloggi'); ?></label>
                </th>
                <td>
                    <textarea class="widefat" rows="7" cols="90" name="inner_template_footer"><?php echo esc_textarea( stripslashes( $ctlggi_template_options['inner_template_footer'] ) ); ?></textarea>
                    <p class="description"><?php _e('Use this area to close the open div tags.', 'cataloggi'); ?></p>
                    <p class="description"><?php _e('by default:', 'cataloggi'); ?>  <?php _e('&lt;/div&gt;', 'cataloggi'); ?> </p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->