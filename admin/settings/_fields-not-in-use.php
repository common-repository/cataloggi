            <tr>
                <th scope="row"><?php _e('Thousands Separator', 'cataloggi'); ?></th>
                <td>
                    <input class="small-text" type="text" value="<?php echo $ctlggi_currency_options['thousands_separator']; ?>" name="thousands_separator" id="thousands_separator"> 
                    <p class="description"><?php _e('The symbol (usually , or .) to separate thousands. by default: ,', 'cataloggi'); ?> </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Decimal Separator', 'cataloggi'); ?></th>
                <td>
                    <input class="small-text" type="text" value="<?php echo $ctlggi_currency_options['decimal_separator']; ?>" name="decimal_separator" id="decimal_separator"> 
                    <p class="description"><?php _e('The symbol (usually , or .) to separate thousands. by default: . ', 'cataloggi'); ?> </p>
                </td>
            </tr>