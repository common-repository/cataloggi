

<?php 

$deactivation_save_settings  = isset( $ctlggi_save_settings_options['ctlggi_plugin_deactivation_save_settings'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_plugin_deactivation_save_settings'] ) : '';

$uninstall_save_settings  = isset( $ctlggi_save_settings_options['ctlggi_plugin_uninstall_save_settings'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_plugin_uninstall_save_settings'] ) : '';

$rewrite_slug_cataloggi  = isset( $ctlggi_save_settings_options['ctlggi_cataloggi_cpt_rewrite_slug'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_cataloggi_cpt_rewrite_slug'] ) : '';

$rewrite_slug_cataloggi_cat  = isset( $ctlggi_save_settings_options['ctlggi_categories_tax_rewrite_slug'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_categories_tax_rewrite_slug'] ) : '';

$cataloggi_home  = isset( $ctlggi_save_settings_options['ctlggi_redirect_wp_home_to_cataloggi'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_redirect_wp_home_to_cataloggi'] ) : '';

$display_grid_buttons  = isset( $ctlggi_save_settings_options['ctlggi_display_grid_buttons'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_display_grid_buttons'] ) : '';

?>

<div id="tab_container">
    
<form method="post" action="" id="ctlggi-save-settings-options-form">

<input type="hidden" name="ctlggi-save-settings-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_save_settings_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2 class="padding-top-15"><?php _e('Settings', 'cataloggi'); ?></h2>
        <tbody>
            <tr>
                <th scope="row">
				<?php _e("Rewrite Slug Catalog", "cataloggi"); ?>
                <span class="ctlggi-tooltip tooltip-info-icon" title="<?php _e( "E.g. add mycatalog so Cataloggi will be visible at https://yourwebsite.com/mycatalog", 'cataloggi' ); ?>"></span>
                </th>
                <td>
                    <input type="text" value="<?php echo $rewrite_slug_cataloggi; ?>" name="ctlggi_cataloggi_cpt_rewrite_slug" id="ctlggi_cataloggi_cpt_rewrite_slug" class="regular-text" >
                  <p class="description"><?php _e("Rewrite slug for Cataloggi's pages.", "cataloggi"); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
				<?php _e("Rewrite Slug Categories", "cataloggi"); ?>
                <span class="ctlggi-tooltip tooltip-info-icon" title="<?php _e( "E.g. add mycategories so Cataloggi's categories will be visible at https://yourwebsite.com/mycategories", 'cataloggi' ); ?>"></span>
                </th>
                <td>
                    <input type="text" value="<?php echo $rewrite_slug_cataloggi_cat; ?>" name="ctlggi_categories_tax_rewrite_slug" id="ctlggi_categories_tax_rewrite_slug" class="regular-text" >
                     <p class="description"><?php _e("Rewrite slug for Cataloggi's category pages.", "cataloggi"); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Display the Grid Buttons', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="ctlggi_display_grid_buttons" id="ctlggi_display_grid_buttons" <?php echo ($display_grid_buttons == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the Grid Buttons (Normal, Large and List View)', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cataloggi Home Page', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="ctlggi_redirect_wp_home_to_cataloggi" id="ctlggi_redirect_wp_home_to_cataloggi" <?php echo ($cataloggi_home == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display Cataloggi as your Home page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Plugin Deactivator', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="ctlggi_plugin_deactivation_save_settings" id="ctlggi_plugin_deactivation_save_settings" <?php echo ($deactivation_save_settings == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Save site settings on plugin deactivation.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Plugin Uninstaller', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="ctlggi_plugin_uninstall_save_settings" id="ctlggi_plugin_uninstall_save_settings" <?php echo ($uninstall_save_settings == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Save site settings on plugin uninstallation.', 'cataloggi'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>

    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->