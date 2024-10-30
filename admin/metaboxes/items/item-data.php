
<?php

    // show fields only if shopping cart enabled on the settings page
    if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
    {  
        $display_enable_quantity_field = '';
		$display_enable_price_options_field = '';
	} else {
		$display_enable_quantity_field = 'style="display:none;"';
		$display_enable_price_options_field = 'style="display:none;"';
	}

    if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
    { 
		// PRICE OPTIONS
		// if price options enabled
		if ( $enable_price_options == '1' ) {
			$display_sale_price = 'style="display:none;"';
			$display_price_options_table = '';
		} else {
			$display_sale_price = '';
			$display_price_options_table = 'style="display:none;"';
		}
	} else {
		$display_sale_price = '';
		$display_price_options_table = 'style="display:none;"';
	}
?>

<table class="form-table"> 

    <tr>
    <th><label for="item_regular_price" class="item_regular_price_label"><?php  _e( 'Regular Price', 'cataloggi' ); ?></label></th>
    <td>
    <?php echo esc_attr( $currencysymbol ); ?>	<input type="text" id="item_regular_price" name="item_regular_price" class="item_regular_price_field" placeholder="<?php echo  esc_attr__( '0.00', 'cataloggi' ); ?>" value="<?php echo  esc_attr__( $item_regular_price ); ?>">
    <p class="description"><?php  _e( 'Enter the product regular price or leave the field blank.', 'cataloggi' ); ?></p>
    </td>
    </tr>

    <tr id="display_sale_price" <?php echo $display_sale_price; ?>>
    <th><label for="item_price" class="item_price_label"><?php  _e( 'Sale Price', 'cataloggi' ); ?></label></th>
    <td>
    <?php echo esc_attr( $currencysymbol ); ?>	<input type="text" id="item_price" name="item_price" class="item_price_field" placeholder="<?php echo esc_attr__( '0.00', 'cataloggi' ); ?>" value="<?php echo esc_attr__( $item_price ); ?>">
    <p class="description"><?php  _e( 'Enter the product sale price or leave the field blank.', 'cataloggi' ); ?></p>
    </td>
    </tr>
    
    <tr id="display_enable_quantity_field" <?php echo $display_enable_quantity_field; ?> >
    <th><label for="enable_quantity_field" class="enable_quantity_field_label"><?php  _e( 'Quantity', 'cataloggi' ); ?></label></th>
    <td>
<?php if( $enable_quantity_field == true ) { $quantity_field_checked = 'checked="checked"'; } else { $quantity_field_checked = ''; } ?>
  <input type="checkbox" id="enable_quantity_field" name="enable_quantity_field" class="ctlggi_enable_quantity_field_checkbox" value="1"<?php echo $quantity_field_checked; ?>>
    <p class="description"> <?php  _e( 'Display the quantity field next to the payment button and on the cart page. ', 'cataloggi' ); ?></p>
    </td>
    </tr>
    
        <tr>
        <th><label for="product_thumb_image" class="product_thumb_image_label"><?php  _e( 'Product Thumb Image', 'cataloggi' ); ?></label></th>
        <td>
      <input style="min-width: 60%;" type="text" id="ctlggi_product_thumb_image" name="product_thumb_image" class="product_thumb_image_field" value="<?php echo esc_attr__( $product_thumb_image ); ?>" placeholder="product thumb image">
      <input type="button" value="Select" class="button" id="ctlggi_product_thumb_image_button">
        <p class="description"> <?php  _e( 'The Product Thumb Image is used on the product listing pages. Recommended image size is 800 x 600 pixels.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
    <?php 
		// make it extensible (sashimi plugin using this)
		do_action( 'ctlggi_admin_product_view_page_after_quantity_field', $post ); // <- extensible	
	?>
    
    <tr <?php echo $display_enable_price_options_field; ?>>
    <th><label for="enable_price_options" class="enable_price_options_label"><?php  _e( 'Price Options', 'cataloggi' ); ?></label></th>
    <td>
<?php if( $enable_price_options == true ) { $price_options_checked = 'checked="checked"'; } else { $price_options_checked = ''; } ?>
  <input type="checkbox" id="enable_price_options" name="enable_price_options" class="ctlggi_enable_price_options_checkbox" value="1"<?php echo $price_options_checked; ?>>
    <p class="description"> <?php  _e( 'Enable price options. ', 'cataloggi' ); ?></p>
    </td>
    </tr>
    
</table>

<!-- table-responsive start -->
<div class="cw-table-responsive" id="ctlggi-price-options-table" <?php echo $display_price_options_table; ?>>

<table class="ctlggi-price-options-table" >
<!-- for jQuery -->
<input type="hidden" class="input-ctlggi-currency-symbol" name="ctlggi_currency_symbol" value="<?php echo esc_attr( $currencysymbol ); ?>"/>
<thead>
  <tr>
    <th id="th-price-option-id"><?php _e( 'ID', 'cataloggi' ); ?></th>
    <th id="th-price-option-name"><?php _e( 'Option Name', 'cataloggi' ); ?></th>
    <th id="th-price-option-price"><?php _e( 'Sale Price', 'cataloggi' ); ?></th>
    <th id="th-price-option-default"><?php _e( 'Default', 'cataloggi' ); ?></th>
    <th id="th-price-option-actions"></th>
    </tr>
</thead>

<tbody>	

<?php 

if ( ! empty ($price_options) && $price_options != 'null' )  {

$price_options = json_decode($price_options, true);// convert back to array

// get last key of the array, this will be the counter number
end($price_options);
$lastKey = key($price_options);

$count = count( $price_options );

/*
print '<pre>';
print_r( $price_options );
print '</pre>';
*/

?>
<input type="hidden" class="input-ctlggi-counter" name="ctlggi_counter" value="<?php echo esc_attr__( $lastKey ); ?>"/>
<?php 

foreach( $price_options as $key ) {

$price = $key['option_price'];
$price = CTLGGI_Amount::ctlggi_format_amount($amount=$price);

?>
  
  <tr class="ctlggi-price-option-row">
  <input type="hidden" class="input-ctlggi-row-id" name="input_ctlggi_row_id[]" value="<?php echo esc_attr__( $key['option_id'] ); ?>"/>
    <td> 
    <span class="ctlggi_price_option_id" ><?php echo esc_attr__( $key['option_id'] ); ?></span>
    </td>   
    <td> 
    <input type="text" name="ctlggi_price_option_name[]" class="ctlggi_price_option_name_input" placeholder="Option Name" value="<?php echo  esc_attr__( $key['option_name'] ); ?>">
    </td>
    <td>
	<?php echo esc_attr__( $currencysymbol ); ?> <input type="text" name="ctlggi_price_option_price[]" class="ctlggi_price_option_price_input" placeholder="0.00" value="<?php echo  esc_attr__( $price ); ?>">
    </td>
    <td> 
    <?php if( $key['option_id'] == $price_default_option ) { $checked = 'checked="checked"'; } else { $checked = ''; } ?>
    <input type="radio" value="<?php echo esc_attr__( $key['option_id'] ); ?>" <?php echo $checked; ?> name="ctlggi_price_default" class="ctlggi_price_default_radio">
    </td>   
    <td> 
    <a class="ctlggi-remove-price-option" href="/" onclick="return false;"><?php _e( 'remove', 'cataloggi' ); ?></a>
    </td>   
  </tr>	
  
<?php 
}

} else {
	
?>

 <input type="hidden" class="input-ctlggi-counter" name="ctlggi_counter" value="1"/>

  <tr id="1" class="ctlggi-price-option-row">
  <input type="hidden" class="input-ctlggi-row-id" name="input_ctlggi_row_id[]" value="1"/>
    <td> 
    <span class="ctlggi_price_option_id" >1</span>
    </td> 
    <td> 
    <input type="text" name="ctlggi_price_option_name[]" class="ctlggi_price_option_name_input" placeholder="Option Name" value="">
    </td>
    <td>
	<?php echo esc_attr__( $currencysymbol ); ?> <input type="text" name="ctlggi_price_option_price[]" class="ctlggi_price_option_price_input" placeholder="0.00" value="">
    </td>
    <td> 
    <input type="radio" value="1" name="ctlggi_price_default" class="ctlggi_price_default_radio">
    </td>   
    <td> 
    <a class="ctlggi-remove-price-option" href="/" onclick="return false;"><?php _e( 'remove', 'cataloggi' ); ?></a>
    </td>   
  </tr>	
<?php 
}
?>
  
  <!-- hidden row for jQuery -->
  <tr class="ctlggi-price-insert-new-row">
  <input type="hidden" class="input-ctlggi-row-id" name="input_ctlggi_row_id[]" value="1"/>
    <td> 
    <span class="ctlggi_price_option_id" ></span>
    </td> 
    <td> 
    <input type="text" name="ctlggi_price_option_name[]" class="ctlggi_price_option_name_input" placeholder="Option Name" value="">
    </td>
    <td>
	<?php echo esc_attr__( $currencysymbol ); ?> <input type="text" name="ctlggi_price_option_price[]" class="ctlggi_price_option_price_input" placeholder="0.00" value="">
    </td>
    <td> 
    <input type="radio" value="1" name="ctlggi_price_default" class="ctlggi_price_default_radio">
    </td>   
    <td> 
    <a class="ctlggi-remove-price-option" href="/" onclick="return false;"><?php _e( 'remove', 'cataloggi' ); ?></a>
    </td>   
  </tr>	

</tbody>

</table>

<p style="float: none; clear:both;" class="submit">
  <a style="margin: 12px 0 4px;" class="button ctlggi-add-price-option"><?php _e( 'Add Price Option', 'cataloggi' ); ?></a>
</p>

</div>
<!-- table-responsive end -->	

<table class="form-table">    

    <tr>
    <th><label for="item_sku" class="item_sku_label"><?php  _e( 'SKU', 'cataloggi' ); ?></label></th>
    <td>
  <input type="text" id="item_sku" name="item_sku" class="item_sku_field" value="<?php echo esc_attr__( $item_sku ); ?>">
    <p class="description"> <?php  _e( 'Stock Keeping Unit.', 'cataloggi' ); ?></p>
    </td>
    </tr>
    
        <tr>
        <th><label for="hide_product_on_catalog_home_checkbox" class="hide_product_on_catalog_home_label"><?php  _e( 'Product on Catalog Home', 'cataloggi' ); ?></label></th>
        <td>
      <?php if( $hide_product_on_catalog_home == '1' ) { $prod_on_catalog_home = 'checked="checked"'; } else { $prod_on_catalog_home = ''; } ?>
      <input type="checkbox" id="hide_product_on_catalog_home" name="hide_product_on_catalog_home" class="hide_product_on_catalog_home_class" value="1" <?php echo $prod_on_catalog_home; ?>>
        <p class="description"> <?php  _e( 'If checked product will be hidden on the catalog home page but will be shown on the category listings.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
	<?php
    // DOWNLOADABLE PRODUCTS
    // show fields only if shopping cart enabled on the settings page
    if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
    {
	?>
    <tr>
    <th><label for="item_downloadable" class="item_downloadable_label"><?php  _e( 'Downloadable Product', 'cataloggi' ); ?></label></th>
    <td>
  <?php if( $item_downloadable == '1' ) { $downloadable = 'checked="checked"'; } else { $downloadable = ''; } ?>
  <input type="checkbox" id="item_downloadable" name="item_downloadable" class="item_downloadable_checkbox" value="1" <?php echo $downloadable; ?>>
    <p class="description"> <?php  _e( 'Enable downloadable product.', 'cataloggi' ); ?></p>
    </td>
    </tr>
	<?php
    }
    ?>
</table>
	<?php
    // DOWNLOADABLE PRODUCTS
    // show fields only if shopping cart enabled on the settings page
    if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
    {  
        // show if it is a downloadable product
        if ( $item_downloadable == '1' ) 
        {	
           $display_downloadable_table = '';
        } else {
           $display_downloadable_table = 'style="display:none;"';
        }
	} else {
           $display_downloadable_table = 'style="display:none;"';
        }
	// Table - Downloadable Items
    ?>
    <table class="form-table" id="show_downloadable_table" <?php echo $display_downloadable_table; ?> >

        <tr>
        <th><label for="item_show_free_download_button" class="item_show_free_download_button_label"><?php  _e( 'Free Download Button', 'cataloggi' ); ?></label></th>
        <td>
      <?php if( $item_show_free_download_button == '1' ) { $show_free_download_button = 'checked="checked"'; } else { $show_free_download_button = ''; } ?>
      <input type="checkbox" id="item_show_free_download_button" name="item_show_free_download_button" class="item_show_free_download_button_checkbox" value="1" <?php echo $show_free_download_button; ?>>
        <p class="description"> <?php  _e( 'Display the Free Download Button if sale price is empty or 0.', 'cataloggi' ); ?></p>
        </td>
        </tr>

        <tr>
        <th><label for="item_custom_download_url" class="item_custom_download_url_label"><?php  _e( 'Custom Download Url', 'cataloggi' ); ?></label></th>
        <td>
      <?php if( $item_custom_download_url == '1' ) { $custom_dw_url = 'checked="checked"'; } else { $custom_dw_url = ''; } ?>
      <input type="checkbox" id="item_custom_download_url" name="item_custom_download_url" class="item_custom_download_url_checkbox" value="1" <?php echo $custom_dw_url; ?>>
        <p class="description"> <?php  _e( 'Enable custom download Url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
            
        <tr id="display_item_download_from_url" <?php if( $item_custom_download_url != '1' ) { echo 'style="display:none;"'; } ?>>
        <th><label for="item_download_from_url" class="item_download_from_url_label"><?php  _e( 'External Download Url', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" style="min-width: 60%;" id="item_download_from_url" name="item_download_from_url" class="item_download_from_url_field" value="<?php echo esc_attr__( $item_download_from_url ); ?>">
        <p class="description"> <?php  _e( 'Product External Download Url. Active only if the free download button enabled.', 'cataloggi' ); ?></p>
        </td>
        </tr>
        
        <tr>
        <th><label for="demo_url_checkbox" class="demo_url_checkbox_label"><?php  _e( 'Demo', 'cataloggi' ); ?></label></th>
        <td>
      <?php if( $demo_url_checkbox == '1' ) { $demo = 'checked="checked"'; } else { $demo = ''; } ?>
      <input type="checkbox" id="demo_url_checkbox" name="demo_url_checkbox" class="demo_url_checkbox_class" value="1" <?php echo $demo; ?>>
        <p class="description"> <?php  _e( 'Enable product demo Url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
            
        <tr id="display_demo_url" <?php if( $demo_url_checkbox != '1' ) { echo 'style="display:none;"'; } ?>>
        <th><label for="demo_url" class="demo_url_label"><?php  _e( 'Product Demo Url', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" style="min-width: 60%;" id="demo_url" name="demo_url" class="demo_url_field" value="<?php echo esc_attr__( $demo_url ); ?>">
        <p class="description"> <?php  _e( 'Enter the product demo Url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
        
        <tr>
        <th><label for="docs_checkbox" class="docs_checkbox_label"><?php  _e( 'Documentation', 'cataloggi' ); ?></label></th>
        <td>
      <?php if( $docs_checkbox == '1' ) { $demo = 'checked="checked"'; } else { $demo = ''; } ?>
      <input type="checkbox" id="docs_checkbox" name="docs_checkbox" class="docs_checkbox_class" value="1" <?php echo $demo; ?>>
        <p class="description"> <?php  _e( 'Enable product documentation Url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
            
        <tr id="display_docs_url" <?php if( $docs_checkbox != '1' ) { echo 'style="display:none;"'; } ?>>
        <th><label for="docs_url" class="docs_url_label"><?php  _e( 'Product Documentation Url', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" style="min-width: 60%;" id="docs_url" name="docs_url" class="docs_url_field" value="<?php echo esc_attr__( $docs_url ); ?>">
        <p class="description"> <?php  _e( 'Enter the product documentation Url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
        <th><label for="item_file_name" class="item_file_name_label"><?php  _e( 'Downloadable File', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" id="item_file_name" name="item_file_name" class="item_file_name_field" value="<?php echo esc_attr__( $item_file_name ); ?>" placeholder="file name">
      &nbsp;
      <input style="min-width: 60%;" type="text" id="ctlggi_item_file_url" name="item_file_url" class="item_file_url_field" value="<?php echo esc_attr__( $item_file_url ); ?>" placeholder="file url">
      <input type="button" value="Select File" class="button" id="ctlggi_upload_file_button">
        <p class="description"> <?php  _e( 'Downloadable file name and url.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
        <th><label for="item_download_limit" class="item_download_limit_label"><?php  _e( 'Download Limit', 'cataloggi' ); ?></label></th>
        <td>
      <input type="number" id="item_download_limit" name="item_download_limit" class="item_download_limit_field" value="<?php echo esc_attr__( $item_download_limit ); ?>">
        <p class="description"> <?php  _e( 'Leave blank for unlimited re-downloads.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
        <th><label for="item_download_expiry" class="item_download_expiry_label"><?php  _e( 'Download Expiry', 'cataloggi' ); ?></label></th>
        <td>
      <input type="number" id="item_download_expiry" name="item_download_expiry" class="item_download_expiry_field" value="<?php echo esc_attr__( $item_download_expiry ); ?>">
        <p class="description"> <?php  _e( 'Enter the number of days before the download link expires, or leave blank so it is never expires.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
    </table>
    
    
    
    
    
    
	<?php   
    
    // TANGIBLE ITEMS
    // show fields only if shopping cart tangible items enabled (1) on the plugin activator page
    if ( $ctlggi_general_options['enable_tangible_items'] == 'do_not_enable' ) 
    {
        
    // Table - Tangible Items
    ?>
    <table class="form-table">	
        
        <tr>
        <th><label for="item_shipping" class="item_shipping_label"><?php  _e( 'Shipping', 'cataloggi' ); ?></label></th>
        <td>
      <?php  //if( $item_downloadable == true ) { $downloadable = 'checked="checked"'; } else { $downloadable = ''; } ?>
      <input type="checkbox" id="item_shipping" name="item_shipping" class="item_shipping_field" value="1">
        <p class="description"> <?php  _e( 'Tick if product shipping required.', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
        <th><label for="item_weight" class="item_weight_label"><?php  _e( 'Item Weight', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" id="item_weight" name="item_weight" class="item_weight_field" value="">
        <p class="description"> <?php  _e( 'Item weight is in (kg).', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
        <th><label for="item_dimensions" class="item_weight_label"><?php  _e( 'Item Dimensions', 'cataloggi' ); ?></label></th>
        <td>
      <input type="text" id="item_lenght" name="item_lenght" class="item_lenght_field" value="" placeholder="lenght">
      <input type="text" id="item_width" name="item_width" class="item_width_field" value="" placeholder="width">
      <input type="text" id="item_height" name="item_height" class="item_height_field" value="" placeholder="height">
        <p class="description"> <?php  _e( 'Item dimensions is in (cm).', 'cataloggi' ); ?></p>
        </td>
        </tr>
    
        <tr>
            <th><label for="item_shipping_options" class="item_shipping_options_label"><?php  _e( 'Shipping Options', 'cataloggi' ); ?></label></th>
            <td>
                <select id="item_shipping_options" name="item_shipping_options" class="item_shipping_options_field">
                <option value="local_pickup"> <?php  _e( 'Local Pickup', 'cataloggi' ); ?></option>
                <option value="free_shipping"> <?php  _e( 'Free Shipping', 'cataloggi' ); ?></option>
                <option value="local_delivery"> <?php  _e( 'Local Delivery', 'cataloggi' ); ?></option>
                <option value="flat_rate"> <?php  _e( 'Flat Rate', 'cataloggi' ); ?></option>
                <option value="international_flat_rate"> <?php  _e( 'International Flat Rate', 'cataloggi' ); ?></option>
                </select>
        <p class="description"> <?php  _e( 'Please select shipping option.', 'cataloggi' ); ?></p>
            </td>
        </tr>
    
    </table>
	<?php    
    }
    ?>