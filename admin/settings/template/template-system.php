

<br class="clear">

<div id="tab_container">

<form method="post" action="" id="ctlggi-template-system-options-form">

<input type="hidden" name="ctlggi-template-system-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_template_system_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2><?php _e('Template Settings', 'cataloggi'); ?></h2>
        <p><?php _e('Set up your custom template for Cataloggi.', 'cataloggi'); ?></p>
        <tbody>
    <tr>
    <th scope="row"><?php _e('Default Template', 'cataloggi'); ?></th>
    <td>
        <select id="ctlggi_default_template" name="ctlggi_default_template">
        
<?php 
     $default_template = $ctlggi_template_system_options['default_template'];

	 echo '<option selected="selected" value="' . esc_attr( $default_template ) . '">' . esc_attr( $default_template ) . '</option>';  

	 $themesPath     = CTLGGI_PLUGIN_DIR . 'public/templates/';
	 $directories    = glob($themesPath . '*' , GLOB_ONLYDIR); // get sub dirs

	  // Templates
	  foreach( $directories as $directory => $value )
	  {
		$theme_dir = basename($value);
		echo '<option value="' . esc_attr( $theme_dir ) . '">' . esc_attr( $theme_dir ) . '</option>';  
	  }
	 
?>

        </select>
        <p class="description"><?php _e('Default template for Cataloggi.', 'cataloggi'); ?></p>
    </td>
</tr>

        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->