<?php 
$order_id = $_REQUEST['view-order'];
		
$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
$orderdata_obj = json_decode($orderdata); // convert to obj
$orderdata = json_decode($orderdata, true); // convert to array
// Display Object
CTLGGI_Developer_Mode::display_object( $object=$orderdata_obj );

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
	
	$first_name            = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
	$last_name             = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
	$email                 = isset( $order_data['order_user_data']['email'] ) ? sanitize_email( $order_data['order_user_data']['email'] ) : '';
	$phone                 = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
	$company               = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';
	
	$billing_country       = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
	$billing_city          = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
	$billing_state         = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
	$billing_addr_1        = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
	$billing_addr_2        = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
	$billing_zip           = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
	
	if ( ! empty($billing_country) ) {
		// Countries
		$countries    = CTLGGI_Countries::ctlggi_country_list(); // function
		$country_name = $countries[$billing_country]; // get country name
	} else {
		$country_name = '';
	}
	
	// get site title
	$blog_title = ''; // default
	if ( !empty( get_bloginfo('name') ) ) {
		$blog_title = get_bloginfo('name');
	}
		
?>
        
        <div class="cataloggi-boxes">
        
        <div class="cataloggi-boxes-title font-size-16"><?php _e( 'Order', 'cataloggi' ); ?> #<?php echo esc_attr( $order_id ); ?> <?php _e( 'Details', 'cataloggi' ); ?></div>
        
<!-- table-responsive start -->
<div class="cw-table-responsive">

<table id="ctlggi-order-items-table">

<thead>
  <tr>
    <th class="uppercase"><?php _e( 'Order Details', 'cataloggi' ); ?></th>
    <th class="uppercase"></th>
    </tr>
</thead>

<tbody>	

  <tr class="ctlggi-order-items">
    <td>
	<?php 
		// order date
		if ( ! empty($order_date) ) {
			$order_date_f = CTLGGI_Helper::formatDateTime( $date=$order_date );
			echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Order Date: ', 'cataloggi' ) . '</span> ' . esc_attr( $order_date_f ) . '</span>';
		}
		// order id
		if ( ! empty($order_id) ) {
			echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Order ID: ', 'cataloggi' ) . '</span> ' . esc_attr( $order_id ) . '</span>';
		}
		// order key
		if ( ! empty($order_key) ) {
			echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Order Key: ', 'cataloggi' ) . '</span> ' . esc_attr( $order_key ) . '</span>';
		}
	?>
    </td>
    <td>
	<?php 
		// Transaction ID
		if ( ! empty($order_transaction_id) ) {
			echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Transaction ID: ', 'cataloggi' ) . '</span> ' . esc_attr( $order_transaction_id ) . '</span>';
		}
		// Order Gateway
		if ( ! empty($order_gateway) ) {
			$payment_gateways = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();
			//$payment_gateway_label = $payment_gateways[$order_gateway]['payment_gateway_label'];
			$payment_gateway_label = ! empty( $payment_gateways[$order_gateway]['payment_gateway_label'] ) ? $payment_gateways[$order_gateway]['payment_gateway_label'] : '';
			echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Payment Method: ', 'cataloggi' ) . '</span> ' . esc_attr( $payment_gateway_label ) . '</span>';
		}
		// order status
		if ( ! empty($order_status) ) {
			$statuses = CTLGGI_Custom_Post_Statuses::ctlggi_order_custom_post_statuses();
			if ( ! empty( $statuses[$order_status] ) ) {
				$status = $statuses[$order_status];
				echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Order Status: ', 'cataloggi' ) . '</span> ' . esc_attr( $status ) . '</span>';
			} else {
				echo '<span class="cataloggi-display-block"><span class="cataloggi-strong">' . __( 'Order Status: ', 'cataloggi' ) . '</span> ' . '' . '</span>';
			}
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
    <th class="uppercase"><?php _e( 'Product', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Price', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Quantity', 'cataloggi' ); ?></th>
    <th class="uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
    </tr>
</thead>

<tbody>	

<?php
  
  if( ! empty($order_id) )
  {
  $order_items = json_encode($order_data['order_items']);
  $order_items = json_decode($order_items); // convert to object 
  
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
		
		// defaults
		$display_price_option = '';
		if ( ! empty($price_option_id) && ! empty($price_option_name) ) {
			$display_price_option = '<span style="display:block;">' . esc_attr( $price_option_name ) . '</span>';
		}

?>
  <tr class="ctlggi-order-items">
    <td data-title="<?php _e( 'Product', 'cataloggi' ); ?>">
	<?php 
	
	$download_data = ''; // default
	$site_home_url = home_url();
	// send download file(s) data only if order status = completed
	if ( $order_status == 'completed' ) {
		
		// check again if file is downloadable
		if ( $item_downloadable == '1' ) {
			
			// get file data, downloadable items
			$item_file_name        = get_post_meta( $item_id, '_ctlggi_item_file_name', true );
			$item_file_url         = get_post_meta( $item_id, '_ctlggi_item_file_url', true );
			//$item_download_limit   = get_post_meta( $item_id, '_ctlggi_item_download_limit', true );
			//$item_download_expiry  = get_post_meta( $item_id, '_ctlggi_item_download_expiry', true );
			
			// GET DOWNLOAD
			$download = CTLGGI_DB_Order_Downloads::ctlggi_select_single_download( $order_id, $item_id );
			
			/*
			echo '<pre>';
			print_r($download);
			echo '</pre>';
			*/
			
			if ( !empty( $download ) ) {
			  $download_limit       = $download[0]['download_limit'];
			  $download_order_date  = $download[0]['order_date'];
			  $download_expiry_date = $download[0]['download_expiry_date'];
			  $download_count       = $download[0]['download_count'];
			}
			
			/*
			// download expiry date
			$current_date = date('Y-m-d H:i:s');
			// order data : order date
			$order_date = get_post_meta( $order_id, '_order_date', true );
			// order date + item download expiry 
			$add_days = strtotime(date("Y-m-d H:i:s", strtotime($order_date)) . "+" . $item_download_expiry . " days");
			$download_expiry_date = date('Y-m-d H:i:s', $add_days);
			*/
			
			if ( $download_limit == '' ) {
				// unlimited downloads
				$download_limit = __( 'Unlimited', 'cataloggi' );
			} else {
				$download_limit = $download_limit;
			}
			
			if ( $download_expiry_date == '0000-00-00' ) {
				$download_expiry_date = __( 'Never Expires', 'cataloggi' );
			} else {
				$download_expiry_date = CTLGGI_Helper::formatDate( $date=$download_expiry_date );
			}
			
			// downloadable products create download url
			$secret_data = array(
				'post_id'    => $item_id, // db posts, item id is the post id !!!
				'order_id'   => $order_id, // db posts
				'order_key'  => $order_key
			);
			
			// convert array to json
			$secret_data_json = json_encode( $secret_data );
			$secret_data_json_enc = CTLGGI_Helper::ctlggi_base64url_encode($data=$secret_data_json);	
			
			$file_link = '<a href="' . esc_url( $site_home_url . '/ctlggi-file-dw-api/?action=download&dwfile=' . $secret_data_json_enc ) . '">' . esc_attr( $item_file_name ) . '</a>';
			
			$file_download_urls[] = $file_link; // save in array for later usage
			
			// downloadable file link
			$download_data = "";
			$download_data .= '<span style="display:block;">' . __( 'Download File: ', 'cataloggi' ) . $file_link . '</span>';
			$download_data .= '<span style="display:block;">' . __( 'Download Limit: ', 'cataloggi' ) . esc_attr( $download_limit ) . '</span>';
			$download_data .= '<span style="display:block;">' . __( 'Download Count: ', 'cataloggi' ) . esc_attr( $download_count ) . '</span>';
			$download_data .= '<span style="display:block;">' . __( 'Download Expiry Date: ', 'cataloggi' ) . esc_attr( $download_expiry_date ) . '</span>';
			
			echo '<span class="cataloggi-strong">' . esc_attr( $item_name ) . '</span>';
			echo $display_price_option;
			echo $download_data;
			
		} else {
		echo '<span class="cataloggi-strong">' . esc_attr( $item_name ) . '</span>'; 
		echo $display_price_option;
		}
	
	} else {
		echo '<span class="cataloggi-strong">' . esc_attr( $item_name ) . '</span>';
		echo $display_price_option;
	}
	
	?>
    </td>
    <td data-title="<?php _e( 'Price', 'cataloggi' ); ?>"> 
    <div class="html-ctlggi-single-item-price">
    <?php echo $item_price_public; ?>
    </div>
    </td>
    <td data-title="<?php _e( 'Quantity', 'cataloggi' ); ?>">
    <div class="cataloggi-item-quantity">
     <?php echo $item_quantity; ?>
    </div>
    </td>
    <td data-title="<?php _e( 'Total', 'cataloggi' ); ?>"> 
    <div class="html-ctlggi-single-item-total">
	<?php 
	echo $item_total_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_total); // return span (HTML) 
	// make sure it is a subscription before display the data
	$display_subsc_details_public = '';
    if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
		$itemdata = json_encode($value); // value from foreach
		echo $display_subsc_details_public = apply_filters( 'ctlggi_order_history_view_display_subsc_data', $itemdata );
	}
	?>
    </div>
    </td>
  </tr>			

<?php 
	
	}

  }
    
    $public_total = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return span (HTML)
    $hidden_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return string
	
?>

  <tr>
    <td colspan="4">
    <div class="cataloggi-float-right cataloggi-margin-left-right-25"> 
    <div class="html-ctlggi-order-total order-total cataloggi-margin-top-bottom-5 font-size-14"><?php _e( 'Order Total:', 'cataloggi' ); ?> 
	<?php echo $public_total; ?>
    </div>
    </div>
    </td>
  </tr>
 
</tbody>

</table>

</div>
<!-- table-responsive end -->	

<a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-grey cataloggi-float-right" href="javascript: history.go(-1)"> <?php _e( 'Go Back', 'cataloggi' ); ?> </a>

		
        </div><!--/ cataloggi-boxes -->	