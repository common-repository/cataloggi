
<div id="tab_container">

	<?php 
	    
		/*
		echo '<pre>';
		print_r($ctlggi_currency_options);
		echo '</pre>';
		*/
    
    ?>
    
<form method="post" action="" id="ctlggi-currency-options-form">

<input type="hidden" name="ctlggi-currency-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_currency_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2 class="padding-top-15"><?php _e('Currency Options', 'cataloggi'); ?></h2>
        <p><?php _e('The following options affect how the currency displayed on the catalog.', 'cataloggi'); ?></p>
        <tbody>
            <tr>
                <th scope="row"><?php _e('Currency', 'cataloggi'); ?></th>
                <td>
                    <select class="small-text" name="catalog_currency" id="catalog_currency">
                        <option selected="selected" value="<?php echo esc_attr( $ctlggi_currency_options['catalog_currency'] ); ?>"><?php echo esc_attr( $ctlggi_currency_options['catalog_currency_name'] ); ?> (<?php echo esc_attr( strtoupper( $ctlggi_currency_options['catalog_currency'] ) ); ?>)</option>
                                <?php
                                foreach ( CTLGGI_Amount::ctlggi_available_currencies() as $currency_key => $currency_obj ) {
                                    $option = '<option value="' . $currency_key . '">';
                                    $option .= esc_attr( $currency_obj['name'] ) . ' (' . esc_attr( $currency_obj['code'] ) . ')';
                                    $option .= '</option>';
                                    echo $option;
                                }
                                ?>
                            </select>
                            <p class="description"><?php _e('Default currency.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Currency Position', 'cataloggi'); ?></th>
                <td>
                    <select class="small-text" name="currency_position" id="currency_position">
                        <option selected="selected" value="<?php echo esc_attr( $ctlggi_currency_options['currency_position'] ); ?>"><?php echo esc_attr( $ctlggi_currency_options['currency_position'] ); ?></option>
                        <option value="<?php echo esc_attr( 'Left' ); ?>"><?php _e('Left', 'cataloggi'); ?></option>
                        <option value="<?php echo esc_attr( 'Right' ); ?>"><?php _e('Right', 'cataloggi'); ?></option>
                    </select>
                    <p class="description"><?php _e('Options: Left ($50.00) or Right (50.00$)', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Thousand Separator', 'cataloggi'); ?></th>
                <td>
                    <select class="small-text" name="thousand_separator" id="thousand_separator">
                        <option selected="selected" value="<?php echo esc_attr( $ctlggi_currency_options['thousand_separator'] ); ?>"><?php echo esc_attr( $ctlggi_currency_options['thousand_separator'] ); ?></option>
                        <option value="<?php echo esc_attr( ',' ); ?>"><?php echo esc_attr( ',' ); ?></option>
                        <option value="<?php echo esc_attr( '.' ); ?>"><?php echo esc_attr( '.' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Decimal Separator', 'cataloggi'); ?></th>
                <td>
                    <select class="small-text" name="decimal_separator" id="decimal_separator">
                        <option selected="selected" value="<?php echo esc_attr( $ctlggi_currency_options['decimal_separator'] ); ?>"><?php echo esc_attr( $ctlggi_currency_options['decimal_separator'] ); ?></option>    
                        <option value="<?php echo esc_attr( '.' ); ?>"><?php echo esc_attr( '.' ); ?></option>
                        <option value="<?php echo esc_attr( ',' ); ?>"><?php echo esc_attr( ',' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Number of Decimals', 'cataloggi'); ?></th>
                <td>
                    <input class="small-text" type="number" value="<?php echo esc_attr( $ctlggi_currency_options['number_of_decimals'] ); ?>" name="number_of_decimals" id="number_of_decimals" >
                </td>
            </tr>
        </tbody>
    </table>
    
    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->