<?php 
$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
$display_grid_buttons  = isset( $ctlggi_save_settings_options['ctlggi_display_grid_buttons'] ) ? sanitize_text_field( $ctlggi_save_settings_options['ctlggi_display_grid_buttons'] ) : '';
if ( $display_grid_buttons == '1' ) {
?>
 <div class="cataloggi-grid-buttons">
 <a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-white cataloggi-float-right active" cataloggi-grid-data-id="list" href="/" onclick="return false;"> <?php _e( 'List', 'cataloggi' ); ?> </a>
 <a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-white cataloggi-float-right active" cataloggi-grid-data-id="large" style="margin-right:4px;" href="/" onclick="return false;"> <?php _e( 'Large', 'cataloggi' ); ?> </a>
 <a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-white cataloggi-float-right" cataloggi-grid-data-id="normal" style="margin-right:4px;" href="/" onclick="return false;"> <?php _e( 'Normal', 'cataloggi' ); ?> </a>
 </div>
<?php 
}
?>