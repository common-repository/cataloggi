
<?php 
// def
$public_total = '';
$hidden_total = '';

  // get options
  $ctlggi_general_options = get_option('ctlggi_general_options');
  
  // get options
  $ctlggi_currency_options = get_option('ctlggi_currency_options');
  
  $catalog_currency       = $ctlggi_currency_options['catalog_currency']; // default currency
  $currency_data_symbol   = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $currency=$catalog_currency );
  $currency_position      = $ctlggi_currency_options['currency_position']; // position : left or right
  $thousand_separator     = stripslashes( $ctlggi_currency_options['thousand_separator'] );
  
  $order_status      = get_post_meta( get_the_ID(), '_order_status', true );
  if( empty( $order_status ) ) $order_status = '';
?>

<input type="hidden" class="input-ctlggi-data-table-form-nonce" name="ctlggi-data-table-form-nonce" value="<?php echo wp_create_nonce('ctlggi_data_table_form_nonce_action'); ?>"/>
<input type="hidden" class="input-ctlggi-curr-data-symbol" name="ctlggi-curr-data-symbol" value="<?php echo esc_attr( $currency_data_symbol ); ?>"/>
<input type="hidden" class="input-ctlggi-curr-position" name="ctlggi-curr-position" value="<?php echo esc_attr( $currency_position ); ?>"/>
<input type="hidden" class="input-ctlggi-thousand-separator" name="ctlggi-thousand-separator" value="<?php echo esc_attr( $thousand_separator ); ?>"/>

<div class="padding-left-right-15">

<!-- table-responsive start -->
<div class="cw-table-responsive cw-admin-forms"> 

<table id="cwtable">

<tbody>  
  
  <tr>
    <td>
    <p><?php _e("Select Product", 'cataloggi'); ?></p>
    <select style="max-width:680px;" class="selectfield ctlggi_order_select_item" name="order_select_item" id="order_select_item">
        <option selected="selected" value=""></option>
		<?php 
        $args=array(
          'post_type' => 'cataloggi',
			'post_status'      => array(        //(string / array) - use post status. Retrieves posts by Post Status, default value i'publish'.         
									'publish',  // - a published post or page.
									'private',  // - not visible to users who are not logged in.
									),
          'posts_per_page' => -1
        );
        $query = null;
        $query = new WP_Query($args);
        if( $query->have_posts() ) {
          while ($query->have_posts()) : $query->the_post();
		  $post_id = get_the_ID();
        ?>  
         <option value="<?php echo esc_attr( $post_id ); ?>"><?php esc_attr( the_title() ); ?></option>
        <?php  
          endwhile;
        }
        wp_reset_postdata(); 
        ?>   
    </select>
     
     <div class="ctlggi-loading-img"></div> <!-- jquery -->
     <div class="ctlggi_display_price_options_select_field"></div> <!-- jquery -->
        
    
    </td>
    
    <td>
    
    <?php 
	if ( $order_status == 'completed' ) {
	?>
    <p><?php _e( 'If order status is "Completed" products are no longer editable.', 'cataloggi' ); ?></p>
    <?php 
	} else {
	?>
    <a class="ctlggi-add-new-item button" href="/" onclick="return false;"><?php _e( 'Add Product', 'cataloggi' ); ?></a>
    <a class="ctlggi-add-new-custom-item button" href="/" onclick="return false;"><?php _e( 'Add Custom', 'cataloggi' ); ?></a>
    <?php 
	}
	?>
    
    </td>
    
  </tr> 
  
</tbody>

</table>

</div>
<!-- table-responsive end -->



<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="ctlggi-order-items-table">

<thead>
  <tr>
    <th class="uppercase"><?php _e( 'Product Name', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Price', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Quantity', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
    <th></th>
    </tr>
</thead>

<tbody>	


<?php

  if(isset($_GET['post']))
  {
	$order_id = $_GET['post'];
	
		$orderdata     = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
		$orderdata_obj = json_decode($orderdata); // convert to obj
		
		// Display Object
		CTLGGI_Developer_Mode::display_object( $object=$orderdata_obj );
		
		$orderdata     = json_decode($orderdata, true); // convert to array
		
		$order_data = array(
			'order_id'               => $orderdata['order_id'], // order id
			'created_date'           => sanitize_text_field( $orderdata['created_date'] ),
			'order_date'             => sanitize_text_field( $orderdata['order_date'] ),
			'order_status'           => sanitize_text_field( $orderdata['order_status'] ), 
			'order_plugin_version'   => sanitize_text_field( $orderdata['order_plugin_version'] ),
			'form_type'              => sanitize_text_field( $orderdata['form_type'] ), // payment form type (paymentsform or checkoutform)
			'order_access_token'     => sanitize_text_field( $orderdata['order_access_token'] ), 
			'order_cus_user_id'      => intval( $orderdata['order_cus_user_id'] ),
			'order_gateway'          => sanitize_text_field( $orderdata['order_gateway'] ),
			'order_currency'         => sanitize_text_field( $orderdata['order_currency'] ), // e.g. usd
			'order_key'              => sanitize_text_field( $orderdata['order_key'] ),
			'order_transaction_id'   => sanitize_text_field( $orderdata['order_transaction_id'] ),
			'order_notes'            => sanitize_text_field( $orderdata['order_notes'] ),
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'] // array
		);
		
		$order_date            = isset( $order_data['order_date'] ) ? sanitize_text_field( $order_data['order_date'] ) : '';
		$cus_user_id           = isset( $order_data['order_cus_user_id'] ) ? sanitize_text_field( $order_data['order_cus_user_id'] ) : '';
		$order_currency        = isset( $order_data['order_currency'] ) ? sanitize_text_field( $order_data['order_currency'] ) : '';
		$total                 = isset( $order_data['order_total']['total'] ) ? sanitize_text_field( $order_data['order_total']['total'] ) : '';
		$order_status          = isset( $order_data['order_status'] ) ? sanitize_text_field( $order_data['order_status'] ) : '';
		$order_plugin_version  = isset( $order_data['order_plugin_version'] ) ? sanitize_text_field( $order_data['order_plugin_version'] ) : '';
		$order_gateway         = isset( $order_data['order_gateway'] ) ? sanitize_text_field( $order_data['order_gateway'] ) : '';
		$order_key             = isset( $order_data['order_key'] ) ? sanitize_text_field( $order_data['order_key'] ) : '';
		$order_transaction_id  = isset( $order_data['order_transaction_id'] ) ? sanitize_text_field( $order_data['order_transaction_id'] ) : '';
		
		$public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return span (HTML)
		$hidden_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return string
	 
		$order_items = json_encode($order_data['order_items']);
		$order_items = json_decode($order_items); // convert to object 
		
		$item_row = '0';
		foreach($order_items as $key => $value)
		{
			// item data
			$item_id              = isset( $value->item_id ) ? sanitize_text_field( $value->item_id ) : '';
			$item_price           = isset( $value->item_price ) ? sanitize_text_field( $value->item_price ) : '';
			$item_name            = isset( $value->item_name ) ? sanitize_text_field( $value->item_name ) : '';
			$item_quantity        = isset( $value->item_quantity ) ? sanitize_text_field( $value->item_quantity ) : '';
			$item_downloadable    = isset( $value->item_downloadable ) ? sanitize_text_field( $value->item_downloadable ) : '';
			$price_option_id      = isset( $value->price_option_id ) ? sanitize_text_field( $value->price_option_id ) : '';
			$price_option_name    = isset( $value->price_option_name ) ? sanitize_text_field( $value->price_option_name ) : '';
			$item_total           = isset( $value->item_total ) ? sanitize_text_field( $value->item_total ) : '';
			$item_payment_type    = isset( $value->item_payment_type ) ? sanitize_text_field( $value->item_payment_type ) : '';	
			// subsc data
			$subsc_recurring      = isset( $value->subsc_recurring ) ? sanitize_text_field( $value->subsc_recurring ) : '';
			$subsc_interval       = isset( $value->subsc_interval ) ? sanitize_text_field( $value->subsc_interval ) : '';
			$subsc_interval_count = isset( $value->subsc_interval_count ) ? sanitize_text_field( $value->subsc_interval_count ) : '';
			$subsc_times          = isset( $value->subsc_times ) ? sanitize_text_field( $value->subsc_times ) : '';
			$subsc_signupfee      = isset( $value->subsc_signupfee ) ? sanitize_text_field( $value->subsc_signupfee ) : '';
			$subsc_trial          = isset( $value->subsc_trial ) ? sanitize_text_field( $value->subsc_trial ) : '';
			
			$item_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span (HTML)
			$item_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price); // return string
			
			$item_price_public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$item_total); // return span (HTML)
			$item_price_total        = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_total); // return string
			
			// defaults
			$display_price_option = '';
			if ( ! empty($price_option_id) && ! empty($price_option_name) ) {
				$display_price_option = '<span style="display:block;">' . esc_attr( $price_option_name ) . '</span>';
			}
			
			// make sure it is a subscription before display the data
			if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' && isset($value->subsc_recurring) ) {
				$itemdata = json_encode($value); // value from foreach
				// subscription using this
				$display_subsc_details_public = apply_filters( 'ctlggi_admin_view_order_items_filter', $itemdata ); // <- extensible 
			} else {
				$display_subsc_details_public = '';
			}
			
			

?>
  <tr class="ctlggi-order-items">
  
    <!-- for jQuery -->
    <input type="hidden" class="input-ctlggi-item-id" name="ctlggi_item_id[]" value="<?php echo esc_attr( $item_id ); ?>"/>
    <input type="hidden" class="input-ctlggi-item-name" name="ctlggi_item_name[]" value="<?php echo esc_attr( $item_name ); ?>"/>
    <input type="hidden" class="input-ctlggi-item-price" name="ctlggi_item_price[]" value="<?php echo esc_attr( $item_price ); ?>"/>
    <input type="hidden" class="input-ctlggi-single-item-total" name="ctlggi_single_item_total[]" value="<?php echo esc_attr( $item_total ); ?>"/>
    <input type="hidden" class="input-ctlggi-item-downloadable" name="ctlggi_item_downloadable[]" value="<?php echo esc_attr( $item_downloadable ); ?>"/>
    <input type="hidden" class="input-ctlggi-price-option-id" name="ctlggi_price_option_id[]" value="<?php echo esc_attr( $price_option_id ); ?>"/>
    <input type="hidden" class="input-ctlggi-price-option-name" name="ctlggi_price_option_name[]" value="<?php echo esc_attr( $price_option_name ); ?>"/>
    <input type="hidden" class="input-ctlggi-item-payment-type" name="ctlggi_item_payment_type[]" value="<?php echo esc_attr( $item_payment_type ); ?>"/> 

    <input type="hidden" class="input-ctlggi-subsc-recurring" name="ctlggi-subsc-recurring" value="<?php echo esc_attr( $subsc_recurring ); ?>"/> 
    <input type="hidden" class="input-ctlggi-subsc-interval" name="ctlggi-subsc-interval" value="<?php echo esc_attr( $subsc_interval ); ?>"/> 
    <input type="hidden" class="input-ctlggi-subsc-interval-count" name="ctlggi-subsc-interval-count" value="<?php echo esc_attr( $subsc_interval_count ); ?>"/> 
    <input type="hidden" class="input-ctlggi-subsc-times" name="ctlggi-subsc-times" value="<?php echo esc_attr( $subsc_times ); ?>"/> 
    <input type="hidden" class="input-ctlggi-subsc-signupfee" name="ctlggi-subsc-signupfee" value="<?php echo esc_attr( $subsc_signupfee ); ?>"/> 
    <input type="hidden" class="input-ctlggi-subsc-trial" name="ctlggi-subsc-trial" value="<?php echo esc_attr( $subsc_trial ); ?>"/> 
    
    <td data-title="<?php _e( 'Product', 'cataloggi' ); ?>">
	<?php 
	
	echo esc_attr( $item_name ); 
	echo $display_price_option;
	?>
    </td>
    <td data-title="<?php _e( 'Price', 'cataloggi' ); ?>"> 
    <div class="html-ctlggi-single-item-price">
    <?php echo $item_price_public; ?>
    </div>
    </td>
    <td data-title="<?php _e( 'Quantity', 'cataloggi' ); ?>">
    <div class="cataloggi-item-quantity">
     <input class="input-ctlggi-item-quantity" type="number" max="" min="1" value="<?php echo esc_attr( $item_quantity ); ?>" name="ctlggi_item_quantity[]" >
    </div>
    </td>
    <td data-title="<?php _e( 'Total', 'cataloggi' ); ?>"> 
    <span class="html-ctlggi-single-item-total"><?php echo $item_price_public_total; ?></span>
    <?php echo $display_subsc_details_public; // should be under public total ?>
    </td>
    <td> 
    
    <?php 
	if ( $order_status != 'completed' ) {
	?>
  <a id="<?php echo esc_attr( $item_id ); ?>" data-item-id="<?php echo esc_attr( $item_id ); ?>" class="ctlggi-remove-item" href="/" onclick="return false;"><?php _e( 'remove', 'cataloggi' ); ?></a>
    <?php 
	} 
	?>
   
    </td>
  </tr>				

<?php 
	
		}

  }
	
?>

  <tr>
    <td colspan="5">
    <div class="float-right margin-left-right-25"> 
    <input type="hidden" class="input-ctlggi-order-total" name="ctlggi-order-total" value="<?php echo esc_attr( $hidden_total ); ?>"/> <!-- for jQuery -->
    <div class="order-total margin-top-bottom-10"><?php _e( 'Order Total:', 'cataloggi' ); ?> 
	 <span class="html-ctlggi-order-total"><?php echo $public_total; ?></span>
    </div>
    <?php 
	if ( $order_status != 'completed' ) {
	?>
    <a id="ctlggi-update-order-total" class="ctlggi-update-order-total button-primary" href="/" onclick="return false;"><?php _e( 'Calculate Total', 'cataloggi' ); ?></a>
    <?php 
	} 
	?>
    </div>
    </td>
  </tr>
 
</tbody>

</table>

</div>
<!-- table-responsive end -->	



</div><!--/ padding-left-right-15 -->

