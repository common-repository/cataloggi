<?php

/**
 * Shopping Cart - Single Order class.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Single_Order {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Get Single order data by order key.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param string $order_key
	 * @return object $order_data
	 */
	public static function get_order_data_by_order_key( $order_key ) 
	{
		if ( empty( $order_key ) )
			return; 
        
		$post_id    = ''; // def
		$order_data = ''; // def
		$error_id   = ''; // def
		
		global $wpdb;
		$postmeta = $wpdb->prefix . 'postmeta'; // table, do not forget about tables prefix 
		$sql  = "
				SELECT *
				FROM $postmeta
				WHERE meta_key = '_order_key' AND  meta_value = '$order_key' LIMIT 1
				";	
		$get_results = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		if ( $get_results ) {
			// return array
			$post_id = $get_results['0']['post_id'];
		}
		
		if ( !empty( $post_id ) ) {
			// get order data
			$order_id = $post_id;
			$order_data = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
			// json object
			return $order_data;
			
		} else {
			// error: no order found
			$error_id = 'get_order_data_by_order_key_post_id_not_found';
			$error_message = __('Get order data by order key: post ID not found.', 'cataloggi');
		}
		
		if ( ! empty($error_id) ) {
			 // save error in the error log
			 CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
		}
			
	}
	
	/**
	 * Get Single order data by order id.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int $order_id
	 * @return object $order_data
	 */
	public static function ctlggi_get_single_order_data_by_order_id( $order_id ) 
	{
		if ( empty( $order_id ) )
			return;  
			
		$orderdata = ''; // def	
		$item_data_ext = array(); // def	
		// create new items array match to checkout cookies array
		$ordered_items = CTLGGI_DB_Order_Items::ctlggi_select_order_items( $order_id );
		if ( !empty($ordered_items) ) {
			foreach($ordered_items as $item )
			{
				$order_item_id     = $item['order_item_id'];
				$order_item_name   = $item['order_item_name'];
				$payment_type      = $item['payment_type']; // normal or subscription
				$order_item_type   = $item['order_item_type'];
				$price_option_id   = $item['price_option_id'];
				$price_option_name = $item['price_option_name'];
				
				if ( $order_item_type == 'downloadable' ) {
					$item_downloadable = '1';
				} else {
					$item_downloadable = '';
				}
				
				$item_meta = CTLGGI_DB_Order_Items::ctlggi_select_order_item_meta( $order_item_id );
				if ( !empty($item_meta) ) {
					foreach($item_meta as $key => $value )
					{
						// item ID 
						if ( $value['meta_key'] == '_item_id' ) {
							$item_id = $value['meta_value'];
						}
						// item price
						if ( $value['meta_key'] == '_item_price' ) {
							$item_price = $value['meta_value'];
						}
						// item quantity
						if ( $value['meta_key'] == '_item_quantity' ) {
							$item_quantity = $value['meta_value'];
						}
						// item total
						if ( $value['meta_key'] == '_item_total' ) {
							$item_total = $value['meta_value'];
						}
						
					}
					
					//echo $item_id . '<br>';
					$item_data = array (
						'item_id'            => $item_id,
						'item_price'         => $item_price,
						'item_name'          => $order_item_name,
						'item_quantity'      => $item_quantity,
						'item_downloadable'  => $item_downloadable,
						'price_option_id'    => $price_option_id,
						'price_option_name'  => $price_option_name,
						'item_total'         => $item_total,
						'item_payment_type'  => $payment_type,
						'order_item_id'      => $order_item_id // this is for to check if it is an existing order
					);
					
					// extend items array with subscription data
					// subscriptions and recurring payments use that
					$item_data = json_encode($item_data);
					$item_data = apply_filters( 'ctlggi_get_single_order_data_by_order_id_filter', $item_data );
					$item_data = json_decode($item_data, true); // convert to array
					//$item_data_ext[$item_id] = $item_data;
					$item_data_ext[] = $item_data;
				}
				
			}
		}
		
		$order_data = CTLGGI_Single_Order::ctlggi_get_single_order_data( $order_id ); // json obj
		$order_data = json_decode($order_data, true); // convert to array
		
		// order data for gateway
		$orderdata = array(
			'order_id'               => $order_data['order_id'], // order id
			'created_date'           => $order_data['created_date'],
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_data['order_status'],
			'order_plugin_version'   => $order_data['order_plugin_version'],
			'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_data['order_access_token'],
			'order_cus_user_id'      => $order_data['order_cus_user_id'],
			'order_gateway'          => $order_data['order_gateway'],
			'order_currency'         => $order_data['order_currency'],
			'order_key'              => $order_data['order_key'],
			'order_transaction_id'   => $order_data['order_transaction_id'], 
			'order_notes'            => $order_data['order_notes'],
			'order_user_data'        => $order_data['order_user_data'], // array
			'order_billing'          => $order_data['order_billing'], // array
			'order_total'            => $order_data['order_total'], // array
			'order_items'            => $item_data_ext // array
		);
		
		$orderdata = json_encode($orderdata); // encode to json before send

		return $orderdata; // json object
			
	}
	
	/**
	 * Single order data for payment request form. Get the single order data by order id and access token.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int $order_id
	 * @return object $order_data
	 */
	public static function ctlggi_single_order_data_for_payments_form( $order_id, $token ) 
	{
		if ( empty( $order_id ) && empty( $token ) )
			return;  
			
		$orderdata = ''; // def	
		
		$order_access_token = get_post_meta( $order_id, '_ctlggi_order_access_token', true );
		if ( ! empty($order_access_token) ) {
			if ( $order_access_token == $token ) {
				$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
			} else {
				return;
			}
		}

		return $orderdata; // json object
	}
	
	/**
	 * Get the order data.
	 * Use this method for sending the email receipt from admin orders.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int $order_id
	 * @return object $order_data
	 */
	public static function ctlggi_get_single_order_data_only_admin( $order_id ) 
	{
		if ( empty( $order_id ) )
			return;  
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
			return;
			
		$order_data = CTLGGI_Single_Order::ctlggi_get_single_order_data( $order_id );	
		
		return $order_data; // json object
	}
	
	/**
	 * Get single order data.
	 *
	 * @since 1.0.0
	 * @access private static - always private!!!!
	 * @param int $order_id
	 * @return object $order_data
	 */
    private static function ctlggi_get_single_order_data( $order_id ) 
	{
		if ( empty( $order_id ) )
			return;  
		
		// get postmeta order data
		$created_date          = get_post_meta( $order_id, '_created_date', true );
		$order_date            = get_post_meta( $order_id, '_order_date', true );
		$cus_user_id           = get_post_meta( $order_id, '_ctlggi_order_cus_user_id', true );
		$order_currency        = get_post_meta( $order_id, '_order_currency', true );
		$order_subtotal        = get_post_meta( $order_id, '_order_subtotal', true );
		$order_total           = get_post_meta( $order_id, '_order_total', true );
		$order_status          = get_post_meta( $order_id, '_order_status', true );
		$order_plugin_version  = get_post_meta( $order_id, '_order_plugin_version', true );
		$form_type             = get_post_meta( $order_id, '_ctlggi_form_type', true );
		$order_gateway         = get_post_meta( $order_id, '_order_gateway', true );
		$order_key             = get_post_meta( $order_id, '_order_key', true ); 
		$order_transaction_id  = get_post_meta( $order_id, '_order_transaction_id', true );
		$order_notes           = get_post_meta( $order_id, '_order_notes', true );
		$order_access_token    = get_post_meta( $order_id, '_ctlggi_order_access_token', true );
			
		$first_name            = get_post_meta( $order_id, '_first_name', true );
		$last_name             = get_post_meta( $order_id, '_last_name', true );
		$email                 = get_post_meta( $order_id, '_email', true );
		$phone                 = get_post_meta( $order_id, '_phone', true );
		$company               = get_post_meta( $order_id, '_company', true );
		$billing_addr_1        = get_post_meta( $order_id, '_billing_addr_1', true );
		$billing_addr_2        = get_post_meta( $order_id, '_billing_addr_2', true );
		$billing_country       = get_post_meta( $order_id, '_billing_country', true );
		$billing_state         = get_post_meta( $order_id, '_billing_state', true );
		$billing_city          = get_post_meta( $order_id, '_billing_city', true );
		$billing_zip           = get_post_meta( $order_id, '_billing_zip', true ); 
		
		// Set default values.
		if( empty( $created_date ) ) $created_date = '';
		if( empty( $order_date ) ) $order_date = '';
		if( empty( $cus_user_id ) ) $cus_user_id = '';
		if( empty( $order_currency ) ) $order_currency = '';
		if( empty( $order_subtotal ) ) $order_subtotal = '';
		if( empty( $order_total ) ) $order_total = '';
		if( empty( $order_status ) ) $order_status = '';
		if( empty( $order_plugin_version ) ) $order_plugin_version = '';
		if( empty( $form_type ) ) $form_type = '';
		if( empty( $order_gateway ) ) $order_gateway = '';
		if( empty( $order_key ) ) $order_key = '';
		if( empty( $order_transaction_id ) ) $order_transaction_id = '';
		if( empty( $order_notes ) ) $order_notes = '';
		if( empty( $order_access_token ) ) $order_access_token = '';
		
		if( empty( $first_name ) ) $first_name = '';
		if( empty( $last_name ) ) $last_name = '';
		if( empty( $email ) ) $email = '';
		if( empty( $phone ) ) $phone = '';
		if( empty( $company ) ) $company = '';
		if( empty( $billing_addr_1 ) ) $billing_addr_1 = '';
		if( empty( $billing_addr_2 ) ) $billing_addr_2 = '';
		if( empty( $billing_country ) ) $billing_country = '';
		if( empty( $billing_state ) ) $billing_state = '';
		if( empty( $billing_city ) ) $billing_city = '';
		if( empty( $billing_zip ) ) $billing_zip = '';
		
		$order_user_data = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'email'      => $email,
			'phone'      => $phone,
			'company'    => $company
		);
		
		$order_billing_data = array(
			'billing_country'       => $billing_country,
			'billing_city'          => $billing_city,
			'billing_state'         => $billing_state,
			'billing_addr_1'        => $billing_addr_1,
			'billing_addr_2'        => $billing_addr_2,
			'billing_zip'           => $billing_zip
		);
		
		$order_total_data = array(
			'subtotal' => $order_subtotal,
			'total'  => $order_total
		);
		
		// ORDER ITEMS
		$ordered_items = CTLGGI_DB_Order_Items::ctlggi_select_order_items( $order_id );
		if ( !empty($ordered_items) ) {
			$items_data = array();
			//$order_items = ''; // default
			$order_items = array(); // default, edited for PHP 7
			foreach($ordered_items as $item )
			{
				$order_item_id     = $item['order_item_id'];
				$order_item_name   = $item['order_item_name'];
				$payment_type      = $item['payment_type']; // normal or subscription
				$order_item_type   = $item['order_item_type'];
				$price_option_id   = $item['price_option_id'];
				$price_option_name = $item['price_option_name'];
				
				// ORDER ITEM METAS
				$item_meta = CTLGGI_DB_Order_Items::ctlggi_select_order_item_meta( $order_item_id );
				
				$order_items[] = array(
				//$order_items[$order_item_id] = array(
					'order_item_id'     => $order_item_id,
					'order_item_name'   => $order_item_name,
					'payment_type'      => $payment_type,
					'order_item_type'   => $order_item_type,
					'price_option_id'   => $price_option_id, 
					'price_option_name' => $price_option_name, 
					'order_item_meta'   => $item_meta // array
				);
		
			}	
		} else {
			$order_items = array();
		}
		
		$order_data = array(
			'order_id'               => $order_id, // order id
			'created_date'           => $created_date,
			'order_date'             => $order_date,
			'order_status'           => $order_status,
			'order_plugin_version'   => $order_plugin_version,
			'form_type'              => $form_type, // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_access_token,
			'order_cus_user_id'      => $cus_user_id,
			'order_gateway'          => $order_gateway,
			'order_currency'         => $order_currency,
			'order_key'              => $order_key, // generated from user email and current date
			'order_transaction_id'   => $order_transaction_id, 
			'order_notes'            => $order_notes,
			'order_user_data'        => $order_user_data, // array
			'order_billing'          => $order_billing_data, // array
			'order_total'            => $order_total_data, // array
			'order_items'            => $order_items // array
		);
		
		$order_data = json_encode($order_data);
		
		return $order_data; // json
	}
	
	/**
	 * Get single order item meta data.
	 *
	 * @to-do This method is not in use, just an example.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param object $order_data
	 * @return void
	 */
    public static function ctlggi_get_single_order_item_meta_data( $order_data ) 
	{
		if ( empty( $order_data ) )
			return;  
			
		$items = $order_data['order_items'];
		
		/*
		echo '<pre>';
		print_r($items);
		echo '</pre>';
		*/
		
		foreach($items as $item => $value )
		{
			//echo $item . ' ' . $value . '<br>';
			$item_metas = $value['order_item_meta'];
			foreach($item_metas as $item_meta )
			{
				echo $item_meta['meta_key'] . ' ' . $item_meta['meta_value'] . '<br>';
			}
		}
			
	}
	

	
}

?>