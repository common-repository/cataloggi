
<?php 

$access_token_ok = '0'; // default
$order_id  = ''; // default
if ( isset( $_GET['id'] ) && $_GET['id'] != '' ) {

?>

<div class="cataloggi-boxes">

<div class="cataloggi-padding-left-right-15">
<div class="cataloggi-boxes-title font-size-16"><?php _e( 'Order', 'cataloggi' ); ?></div>

<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="cwtable">

<thead>
  <tr>
    <th class="cataloggi-uppercase"><?php _e( 'Product', 'cataloggi' ); ?></th>
    <th class="cataloggi-uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
    </tr>
</thead>

<tbody>
<?php

	   $order_id = $_GET['id'];
	   if ( isset( $_GET['token'] ) && $_GET['token'] != '' ) {
		   $token = $_GET['token'];
	       //echo 'Order ID: ' . $order_id . ' Access Token: ' . $token;
		   // get order by order id and access token
		   $orderdata = CTLGGI_Single_Order::ctlggi_single_order_data_for_payments_form( $order_id, $token );
		   //$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
		   if ( ! empty($orderdata) ) {
			 
			$orderdata_obj = json_decode($orderdata); // convert to obj
			$orderdata     = json_decode($orderdata, true); // convert to array
			
			// Display Object
			//CTLGGI_Developer_Mode::display_object( $object=$orderdata_obj );
			
			$access_token_ok = '1';
		   }
	   }
   
   if ($access_token_ok == '1' ) {	

		// defaults
		$total = '0';
		$subtotal = '0';
		$price_in_total = '0';
  
	   $order_items = $orderdata['order_items'];
	   $order_items = json_encode($order_items);
	   $obj_order_items   = json_decode($order_items); // convert to obj
	   // if cart has contents
	   if(count($obj_order_items)>0)
	   {	
		
		  foreach($obj_order_items as $key => $value)
		  {	
				if ($value->item_price != '0') {
				  //$item_price = CTLGGI_Amount::ctlggi_amount_hidden($amount=$value->item_price);
				}
				
				$display_price_option = ''; // default
				
				// notset defined in class-ctlggi-payment-buttons.php
				if ( $value->price_option_id != '' ) {
				//if ( ! empty($value->price_option_id) ) {
					
					$price_option_id = $value->price_option_id;
					// get data
					$price_options = get_post_meta( $value->item_id, '_ctlggi_price_options', true ); // json
					if ( $price_options != '' ) {
						$price_options = json_decode($price_options); // convert to object
						$price_option_name = $price_options->$price_option_id->option_name;
						
						$display_price_option = '<span style="display:block;">' . esc_attr( $price_option_name ) . '</span>';
					} else {
						$display_price_option = '';
					}
				}
			
?>
  <tr>
    <td><?php echo esc_attr( $value->item_name ); ?>  x <?php echo esc_attr( $value->item_quantity ); ?> <?php echo $display_price_option; // span ?></td>
    <td>
    <?php
	
	// calculate total for single item
	$single_item_total = $value->item_price * $value->item_quantity;
	
    $item_price_public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$single_item_total); // return span (HTML)
    $item_price_total        = CTLGGI_Amount::ctlggi_amount_hidden($amount=$single_item_total); // return string
	
	// make sure it is a subscription before display the data
    if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
		$itemdata = json_encode($value); // value from foreach
		$display_subsc_details_public = apply_filters( 'ctlggi_payments_totals_display_subsc_data', $itemdata );
	} else {
		$display_subsc_details_public = '';
	}
	
	echo $item_price_public_total;
	echo $display_subsc_details_public;
	
	?>
    </td>
  </tr> 

<?php 
		  }
	   }
	}
?>  
   
  <tr>
    <td colspan="2">
	<?php 
	
	$sub_total = isset( $orderdata['order_total']['subtotal'] ) ? sanitize_text_field( $orderdata['order_total']['subtotal'] ) : '';
	$total     = isset( $orderdata['order_total']['total'] ) ? sanitize_text_field( $orderdata['order_total']['total'] ) : '';
	
    $public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return span (HTML)
    $hidden_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return string
	
	?>
    <input type="hidden" class="input-ctlggi-items-price-in-total" name="ctlggi-items-price-in-total" value="<?php echo esc_attr( $hidden_total ); ?>"/> <!-- for jQuery -->
	<span style="font-size:20px; font-weight: normal; color: rgba(0, 0, 0, 0.45);"><?php _e( 'Total', 'cataloggi' ); ?> </span> 
	<span style="font-size:20px; font-weight: normal;"><?php echo $public_total; // span ?></span>
    </td>
  </tr>   
  
</tbody>

</table>


</div>
<!-- table-responsive end -->

</div><!--/ cataloggi-padding-left-right-15 -->

</div><!--/ cataloggi-boxes -->

<?php 
}
?>