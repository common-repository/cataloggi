<?php

/**
 * Shopping Cart - Validate Order Form class.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

class CTLGGI_Validate_Order_Form {

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
	 * Order form validate before process.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param object $orderdata
	 * @return  string $results ok on success or error for jQuery we use the 'ok' value.
	 */
    public static function ctlggi_order_processing_validate_order_form($orderdata) 
	{
		if ( empty( $orderdata ) )
		return;
		
		$results = ''; // default
		
		$order_data   = json_decode($orderdata, true); // convert to array
		//$order_data_obj = json_decode($orderdata); // convert to object
		
		// check form type
		if ( isset( $order_data['form_type'] ) && $order_data['form_type'] == 'paymentsform' ) {
			$results = CTLGGI_Validate_Order_Form::ctlggi_validate_payments_form( $orderdata );
		} elseif ( isset( $order_data['form_type'] ) && $order_data['form_type'] == 'checkoutform' ) {
			$results = CTLGGI_Validate_Order_Form::ctlggi_validate_checkout_form( $orderdata );
		}
		
		return $results;
		
	}
	
	/**
	 * Validate payments form.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param object $orderdata
	 * @return  string ok on success or error $output_error - for jQuery we use the 'ok' value.
	 */
    public static function ctlggi_validate_payments_form($orderdata) 
	{
		if ( empty( $orderdata ) )
		return;
		
		$order_data   = json_decode($orderdata, true); // convert to array
		
		// defaults
		$error_id = '';
		$error_message = '';
		$output_error = '';
		
		$orderdata = array(
			'order_id'               => $order_data['order_id'], // order id
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_data['order_status'], // from db
			'order_plugin_version'   => $order_data['order_plugin_version'],
			'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_data['order_access_token'], // from db
			'order_cus_user_id'      => $order_data['order_cus_user_id'],
			'order_gateway'          => $order_data['order_gateway'],
			'order_currency'         => $order_data['order_currency'], // e.g. usd
			'order_key'              => $order_data['order_key'],
			'order_transaction_id'   => $order_data['order_transaction_id'], // array
			'order_user_data'        => $order_data['order_user_data'], // array
			'order_billing'          => $order_data['order_billing'], // array
			'order_total'            => $order_data['order_total'], // array
			'order_items'            => $order_data['order_items'], // array
			'order_post_data'        => $order_data['order_post_data'] // array
		);
		
		// defaults
		$payment_gateway_label = '';
		$payment_gateway_name  = '';
		$create_an_account     = '';
		$credit_card_details   = '';
		$billing_details       = '';
		
		// gateway data
		$payment_gateways   = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		$selected_gateway   = $order_data['order_gateway'];
		if( ! empty( $selected_gateway ) ) {	
			foreach($payment_gateways as $gateway => $value )
			{  
			   // get the selected gateway
			   if ( $selected_gateway == $gateway ) 
			   {
					$payment_gateway_label = $value['payment_gateway_label'];
					$payment_gateway_name  = $value['payment_gateway_name'];
					$create_an_account     = $value['create_an_account'];
					$credit_card_details   = $value['credit_card_details'];
					$billing_details       = $value['billing_details'];
			   }
			}
		}
		
		// user data
		$first_name     = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
		$last_name      = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
		$email          = isset( $order_data['order_user_data']['email'] ) ? sanitize_text_field( $order_data['order_user_data']['email'] ) : '';
		$phone          = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
		$company        = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';

		// billing details
		$billing_country     = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
		$billing_city        = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
		$billing_state       = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
		$billing_addr_1      = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
		$billing_addr_2      = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
		$billing_zip         = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
		
		if ( $billing_details == '1' ) {
			$billing = array(
				'ctlggi_billing_country'  => $billing_country,
				'ctlggi_billing_city'     => $billing_city,
				'ctlggi_billing_state'    => $billing_state,
				'ctlggi_billing_addr_1'   => $billing_addr_1,
				'ctlggi_billing_addr_2'   => $billing_addr_2,
				'ctlggi_billing_zip'      => $billing_zip
			);
			// validate billing details fields
			$billing_errors = CTLGGI_Checkout::ctlggi_validate_billing_details( $billing );
		} else {
			$billing_errors = '';
		}
		
		// FORM VALIDATION
		// validate nonce
	    if ( ! wp_verify_nonce( $order_data['order_post_data']['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {	
			$error_id = 'nonce_verification_failed';
			$error_message = __('Nonce verification failed.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			$output_error = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error
			return $output_error;
			
		} elseif ( empty( $selected_gateway ) ) {
			// validate payment gateway	
			// error, no gateway selected
			$error_id = 'no_gateway_selected';
			$error_message = __('No gateway selected.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			$output_error = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error
			return $output_error;

		} elseif ( ! empty( $billing_errors ) ) {
			// validate billing details				
			$error_id = 'order_form_billing_details_error';
			$error_message = __('Order form billing details error.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			
			$output_error = $billing_errors; // output error
			return $output_error;
		} else {
			return 'ok'; // should use 'ok' as defined in jQuery
		}
		
	}

	/**
	 * Validate checkout form.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param object $orderdata
	 * @return  string ok on success or error $output_error - for jQuery we use the 'ok' value.
	 */
    public static function ctlggi_validate_checkout_form($orderdata) 
	{
		if ( empty( $orderdata ) )
		return;
		
		$order_data   = json_decode($orderdata, true); // convert to array
		
		/*
		echo '<pre>';
		print_r( $order_data );
		echo '</pre>';
		exit;
		*/
		
		// defaults
		$error_id = '';
		$error_message = '';
		$output_error = '';
		
		$orderdata = array(
			'order_id'               => $order_data['order_id'], // order id
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_data['order_status'], // from db
			'order_plugin_version'   => $order_data['order_plugin_version'],
			'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_data['order_access_token'], // from db
			'order_cus_user_id'      => $order_data['order_cus_user_id'],
			'order_gateway'          => $order_data['order_gateway'],
			'order_currency'         => $order_data['order_currency'], // e.g. usd
			'order_key'              => $order_data['order_key'],
			'order_transaction_id'   => $order_data['order_transaction_id'], // array
			'order_user_data'        => $order_data['order_user_data'], // array
			'order_billing'          => $order_data['order_billing'], // array
			'order_total'            => $order_data['order_total'], // array
			'order_items'            => $order_data['order_items'], // array
			'order_post_data'        => $order_data['order_post_data'] // array
		);
		
		// defaults
		$payment_gateway_label = '';
		$payment_gateway_name  = '';
		$create_an_account     = '';
		$credit_card_details   = '';
		$billing_details       = '';
		
		// gateway data
		$payment_gateways   = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		$selected_gateway   = $order_data['order_gateway'];
		if( ! empty( $selected_gateway ) ) {	
			foreach($payment_gateways as $gateway => $value )
			{  
			   // get the selected gateway
			   if ( $selected_gateway == $gateway ) 
			   {
					$payment_gateway_label = $value['payment_gateway_label'];
					$payment_gateway_name  = $value['payment_gateway_name'];
					$create_an_account     = $value['create_an_account'];
					$credit_card_details   = $value['credit_card_details'];
					$billing_details       = $value['billing_details'];
			   }
			}
		}
		
		// user data
		$first_name     = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
		$last_name      = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
		$email          = isset( $order_data['order_user_data']['email'] ) ? sanitize_text_field( $order_data['order_user_data']['email'] ) : '';
		$phone          = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
		$company        = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';
        $username       = isset( $order_data['order_post_data']['ctlggi_username'] ) ? sanitize_text_field( $order_data['order_post_data']['ctlggi_username'] ) : '';
		$password       = isset( $order_data['order_post_data']['ctlggi_user_pass'] ) ? sanitize_text_field( $order_data['order_post_data']['ctlggi_user_pass'] ) : '';
		$password_again = isset( $order_data['order_post_data']['ctlggi_user_pass_again'] ) ? sanitize_text_field( $order_data['order_post_data']['ctlggi_user_pass_again'] ) : '';

		// billing details
		$billing_country     = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
		$billing_city        = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
		$billing_state       = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
		$billing_addr_1      = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
		$billing_addr_2      = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
		$billing_zip         = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
		
		// check if user logged in
		if ( ! is_user_logged_in() ) {
			// validate userdata
			$userdata = array(
				'ctlggi_username'        => $username,
				'ctlggi_user_pass'       => $password,
				'ctlggi_user_pass_again' => $password_again,
				'ctlggi_user_email'      => $email
			);
			// validate create an account fields
			$register_errors = CTLGGI_Login_Register::ctlggi_validate_user_data( $userdata );
		} else {
			$register_errors = '';
		}
		
		if ( $billing_details == '1' ) {
			$billing = array(
				'ctlggi_billing_country'  => $billing_country,
				'ctlggi_billing_city'     => $billing_city,
				'ctlggi_billing_state'    => $billing_state,
				'ctlggi_billing_addr_1'   => $billing_addr_1,
				'ctlggi_billing_addr_2'   => $billing_addr_2,
				'ctlggi_billing_zip'      => $billing_zip
			);
			// validate billing details fields
			$billing_errors = CTLGGI_Checkout::ctlggi_validate_billing_details( $billing );
		} else {
			$billing_errors = '';
		}
		
		// FORM VALIDATION
		// validate nonce
	    if ( ! wp_verify_nonce( $order_data['order_post_data']['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {	
			$error_id = 'nonce_verification_failed';
			$error_message = __('Nonce verification failed.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			$output_error = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error
			return $output_error;
			
		} elseif ( empty( $selected_gateway ) ) {
			// validate payment gateway	
			// error, no gateway selected
			$error_id = 'no_gateway_selected';
			$error_message = __('No gateway selected.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			$output_error = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error
			return $output_error;

		} elseif ( ! empty( $register_errors ) ) {
			// validate registration details if user NOT logged in		
			$error_id = 'order_form_user_registration_error';
			$error_message = __('Order form user registration error.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			
			$output_error = $register_errors; // output error
			return $output_error;
			
		} elseif ( ! empty( $billing_errors ) ) {
			// validate billing details				
			$error_id = 'order_form_billing_details_error';
			$error_message = __('Order form billing details error.', 'cataloggi');
			// save error in the error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			
			$output_error = $billing_errors; // output error
			return $output_error;
		} else {
			return 'ok'; // should use 'ok' as defined in jQuery
		}
		
		/*
		// return error message
		if( ! empty( $error_id ) ) {
			return $output_error;
		} else {
			// valid
			
			//return true;
			return $first_name;
		}
		*/
		
	}
	
	
}

?>