<?php

/**
 * Shop Payment Gateway None, if payment totals is 0
 *
 * @package     cataloggi
 * @subpackage  Public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Gateway_None {

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
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name    The name of the plugin.
	 * @param      string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Get order data and save in transient.
	 * Uses: do_action ctlggi_gateway_$gateway 
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param object $order_data
	 * @return void
	 */
    public static function ctlggi_order_data_for_gateway_none( $order_data ) 
	{
		$orderdata   = json_decode($order_data, true); // convert to array	
		$order_data = array(
			'order_id'               => $orderdata['order_id'], // order id
			'created_date'           => $orderdata['created_date'],
			'order_date'             => $orderdata['order_date'],
			'order_status'           => $orderdata['order_status'], // from db
			'order_plugin_version'   => $orderdata['order_plugin_version'],
			'form_type'              => $orderdata['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $orderdata['order_access_token'], // from db
			'order_cus_user_id'      => $orderdata['order_cus_user_id'],
			'order_gateway'          => $orderdata['order_gateway'],
			'order_currency'         => $orderdata['order_currency'], // e.g. usd
			'order_key'              => $orderdata['order_key'],
			'order_transaction_id'   => $orderdata['order_transaction_id'], 
			'order_notes'            => $orderdata['order_notes'],
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'], // array
			'order_post_data'        => $orderdata['order_post_data'], // array
		);
		
		// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
		// encode to json and save in transient
		$orderdata = json_encode($order_data);
		delete_transient( 'ctlggi_order_data_transient' );
		set_transient( 'ctlggi_order_data_transient', $orderdata, 7200 ); // for ... seconds
	}

	/**
	 * Process BACS payment Ajax.
	 *
	 * @since 1.0.0
	 * @return object
	 */
    public function ctlggi_process_none_payment() 
	{	
		if ( get_transient( 'ctlggi_order_data_transient' ) ) {
			$orderdata = get_transient( 'ctlggi_order_data_transient' ); // json encoded
		}
		
		if ( empty( $orderdata ) )
		return;
		
		// BACS
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);

		// VALIDATE ORDER FORM
	    $validate_order_form = CTLGGI_Validate_Order_Form::ctlggi_order_processing_validate_order_form($orderdata);
		
		if ( $validate_order_form == 'ok' ) {
			
			$order_data = json_decode($orderdata, true); // convert to array
			
			// check form type
			if ( isset( $order_data['form_type'] ) && $order_data['form_type'] == 'paymentsform' ) {
				$this->ctlggi_none_payments_form_process( $orderdata );
			} elseif ( isset( $order_data['form_type'] ) && $order_data['form_type'] == 'checkoutform' ) {
				$this->ctlggi_none_checkout_form_process( $orderdata );
			} else {
				return;
			}
			
		} else {
			// output error message
			//echo $validate_order_form;
			$print = $validate_order_form;
			echo json_encode(array('checkoutsuccess'=>false, 'message'=>$print ));
		}

		
	    #### important! #############
	    exit; // don't forget to exit!
	}
	
	/**
	 * Payments form.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_none_payments_form_process( $orderdata ) 
	{	
		if ( empty( $orderdata ) )
		return;
		
		$order_data = json_decode($orderdata, true); // convert to array
		
		$order_id   = isset( $order_data['order_id'] ) ? sanitize_text_field( $order_data['order_id'] ) : '';
		$token      = isset( $order_data['order_access_token'] ) ? sanitize_text_field( $order_data['order_access_token'] ) : '';
		
		if ( ! empty($order_id) ) {
			
			// if payment already completed do not process payment
			if ( isset( $order_data['order_status'] ) && $order_data['order_status'] == 'completed' ) {
				$error_id = 'cannot_process_payment_already_completed';
				$error_message = __('The order cannot be processed as the payment already completed.', 'cataloggi');
				$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id=$order_id, $orderdata, $order_status='completed', $error_id, $error_message );
				echo json_encode(array('checkoutsuccess'=>false, 'message'=>$print ));
			} else {
			
				$first_name = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
				$last_name  = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
				$email      = isset( $order_data['order_user_data']['email'] ) ? sanitize_email( $order_data['order_user_data']['email'] ) : '';
				$phone      = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
				$company    = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';
				
				$country    = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
				$city       = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
				$state      = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
				$addr_1     = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
				$addr_2     = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
				$zip        = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
				
				$order_gateway  = isset( $order_data['order_gateway'] ) ? sanitize_text_field( $order_data['order_gateway'] ) : '';
				$order_key      = isset( $order_data['order_key'] ) ? sanitize_text_field( $order_data['order_key'] ) : '';
				
				// Update the meta field in the database.
				update_post_meta( $order_id, '_first_name', $first_name );
				update_post_meta( $order_id, '_last_name', $last_name );
				update_post_meta( $order_id, '_email', $email );
				update_post_meta( $order_id, '_phone', $phone );
				update_post_meta( $order_id, '_company', $company );
				update_post_meta( $order_id, '_billing_addr_1', $addr_1 );
				update_post_meta( $order_id, '_billing_addr_2', $addr_2 );
				update_post_meta( $order_id, '_billing_country', $country );
				update_post_meta( $order_id, '_billing_state', $state );
				update_post_meta( $order_id, '_billing_city', $city );
				update_post_meta( $order_id, '_billing_zip', $zip );
				
				update_post_meta( $order_id, '_order_gateway', $order_gateway );
				update_post_meta( $order_id, '_order_key', $order_key );
				
				$transaction_id_none = '';
				// important, if payment status = completed we will send the downloadable file(s) urls in the order receipt email
				$order_status = 'pending_payment'; // for BACS should be pending_payment
				
				// update the above fields upon successful payment
				update_post_meta( $order_id, '_order_transaction_id', $transaction_id_none );
				update_post_meta( $order_id, '_order_status', $order_status ); // updating order status
	
				$orderdata = array(
					'order_id'               => $order_data['order_id'], // order id
					'created_date'           => $order_data['created_date'],
					'order_date'             => $order_data['order_date'],
					'order_status'           => $order_data['order_status'], // from db
					'order_plugin_version'   => $order_data['order_plugin_version'],
					'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
					'order_access_token'     => $order_data['order_access_token'], // from db
					'order_cus_user_id'      => $order_data['order_cus_user_id'],
					'order_gateway'          => $order_data['order_gateway'],
					'order_currency'         => $order_data['order_currency'], // e.g. usd
					'order_key'              => $order_data['order_key'],
					'order_transaction_id'   => $order_data['order_transaction_id'],
					'order_notes'            => $order_data['order_notes'],
					'order_user_data'        => $order_data['order_user_data'], // array
					'order_billing'          => $order_data['order_billing'], // array
					'order_total'            => $order_data['order_total'], // array
					'order_items'            => $order_data['order_items'], // array
					'order_post_data'        => $order_data['order_post_data'] // array
				);
				
				$orderdata = json_encode($orderdata); // encode to json before send
				
				// test
				//$orderdata_test = json_encode($orderdata);
				//delete_transient( 'ctlggi_order_data_transient' );
				//set_transient( 'ctlggi_order_data_transient_test', $orderdata, 14400 ); // for ... seconds
				
				// update database, send email, redirect to success page upon successful payment
				$print = CTLGGI_Process_Order::ctlggi_order_processing_success( $post_id=$order_id, $orderdata, $order_status ); 
				
				// delete transients
				delete_transient( 'ctlggi_order_data_transient' );
	
				echo json_encode(array('checkoutsuccess'=>true, 'message'=>$print ));
			
			}
		
		}
		
	}

	/**
	 * Checkout form.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_none_checkout_form_process( $orderdata ) 
	{	
		if ( empty( $orderdata ) )
		return;
		
		$order_data = json_decode($orderdata, true); // convert to array
		
		// default
		$post_id = '';
		
		// user data	
		$first_name = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
		$last_name  = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
		$email      = isset( $order_data['order_user_data']['email'] ) ? sanitize_email( $order_data['order_user_data']['email'] ) : '';
		
		$username   = isset( $order_data['order_post_data']['ctlggi_username'] ) ? sanitize_text_field( $order_data['order_post_data']['ctlggi_username'] ) : '';
		$password   = isset( $order_data['order_post_data']['ctlggi_user_pass'] ) ? sanitize_text_field( $order_data['order_post_data']['ctlggi_user_pass'] ) : '';
		
		// user data
		$userdata = array(
			'user_login'      => $username,
			'user_pass'       => $password,
			'user_email'      => $email,
			'first_name'      => $first_name,
			'last_name'       => $last_name,
			'user_registered' => date( 'Y-m-d H:i:s' ),
			'role'            => 'subscriber' 
		);
		
		// manage user (register)
		$userid = CTLGGI_Process_Order::ctlggi_order_processing_manage_user($userdata);

		$orderdata = array(
			'order_id'               => $order_data['order_id'], // order id
			'created_date'           => $order_data['created_date'],
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_data['order_status'], 
			'order_plugin_version'   => $order_data['order_plugin_version'],
			'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_data['order_access_token'], 
			'order_cus_user_id'      => $userid, // add user id 
			'order_gateway'          => $order_data['order_gateway'],
			'order_currency'         => $order_data['order_currency'], // e.g. usd
			'order_key'              => $order_data['order_key'],
			'order_transaction_id'   => $order_data['order_transaction_id'], 
			'order_notes'            => $order_data['order_notes'],
			'order_user_data'        => $order_data['order_user_data'], // array
			'order_billing'          => $order_data['order_billing'], // array
			'order_total'            => $order_data['order_total'], // array
			'order_items'            => $order_data['order_items'], // array
			'order_post_data'        => $order_data['order_post_data'] // array
		);
		
		$orderdata = json_encode($orderdata); // encode to json before send
		
		// insert order data
		$post_id = CTLGGI_Process_Order::ctlggi_insert_order_data( $orderdata );// returns post id
		
		if ( empty( $post_id ) ) {
			// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold' 
			$order_status = 'failed';
			
			$error_id = 'insert_order_data_failed';
			$error_message = __('Failed to insert order data.', 'cataloggi');
			
			// error log saved in the cataloggi-uploads/_ctlggi-error-log.txt file.
			$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id, $orderdata, $order_status, $error_id, $error_message ); // output error message
			
			echo json_encode(array('checkoutsuccess'=>false, 'message'=>$print ));
			
		} else {
			// success
			// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
			
			$transaction_id_none = '';
			// important, if payment status = completed we will send the downloadable file(s) urls in the order receipt email
			$order_status = 'completed'; // for BACS should be pending_payment
			
			// update the above fields upon successful payment
			update_post_meta( $post_id, '_order_transaction_id', $transaction_id_none );
			update_post_meta( $post_id, '_order_status', $order_status ); // updating order status
			
			// update database, send email, redirect to success page upon successful payment
			$print = CTLGGI_Process_Order::ctlggi_order_processing_success( $post_id, $orderdata, $order_status ); 
			
			// delete transients
			delete_transient( 'ctlggi_order_data_transient' );

			echo json_encode(array('checkoutsuccess'=>true, 'message'=>$print ));
			
		}
		
	}
	
	
}

?>