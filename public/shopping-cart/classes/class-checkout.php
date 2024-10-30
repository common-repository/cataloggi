<?php

/**
 * Shopping Cart - Checkout class.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Checkout {

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
	 * Generate order key.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return string $order_key
	 */
    public static function ctlggi_generate_order_key() 
	{
		$rand_num = CTLGGI_Helper::ctlggi_generate_random_string($length='18');
		$order_key = 'order_' . $rand_num;
		//$order_key_generate = strtolower( $email . ' ' . date( 'Y-m-d H:i:s' ) . ' ctlggi_order_key' );  // Order key
		//$order_key = CTLGGI_Helper::ctlggi_base64url_encode($order_key_generate);
		return $order_key;
	}
	
	/**
	 * Return checkout cart totals.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $arr_cart_totals
	 */
    public static function ctlggi_checkout_cart_totals() 
	{
		// cookie name
		$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
		
		// check if cookie exist
		if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_totals_cookie_name ) === true ) 
		{	
			// read the cookie
			$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_totals_cookie_name, $default = '');
			$cart_totals = $cookie;
			
			$arr_cart_totals = json_decode($cart_totals, true); // convert to array
			$obj_cart_totals = json_decode($cart_totals); // convert to object
		
			// if has contents
			if(count($obj_cart_totals)>0)
			{
				return $arr_cart_totals;
			}
		} else {
		  return;	
		}
	}
	
	/**
	 * Return checkout cart items.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $arr_cart_items
	 */
    public static function ctlggi_checkout_cart_items() 
	{
		// cookie name
		$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
		
		// check if cookie exist
		if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) 
		{
			// read the cookie
			$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');
			$cart_items = $cookie;

			$arr_cart_items = json_decode($cart_items, true); // convert to array
			$obj_cart_items = json_decode($cart_items); // convert to object
		    
			// if cart has contents
			if(count($obj_cart_items)>0)
			{	
			  return $arr_cart_items;
			}
			
		} else {
		  return;	
		}
	}
	
	/**
	 * Replace roles.
	 *
	 * @to-do This method is not in use for many reasons.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return void
	 */
    public static function ctlggi_checkout_form_process_manage_user_role() 
	{
		// check if user logged in
		if ( is_user_logged_in() ) {
			
			// if logged in get current user data
			$current_user = wp_get_current_user();
			
			$user_id     = $current_user->ID;
			$username    = $current_user->user_login;
			$email       = $current_user->user_email;
			$first_name  = $current_user->user_firstname;
			$last_name   = $current_user->user_lastname;
			$displayname = $current_user->display_name;
			$user_roles  = $current_user->roles; // get roles
			
			$first_user_role = $user_roles['0']; // first role
			
			### IMPORTANT !!! ###
			// Multiple roles are NOT working on WP so use only ONE role
			
			// if role is subscriber or cataloggi_subscriber, replace role  with (cataloggi_customer)
			if ( $first_user_role == 'subscriber' || $first_user_role == 'cataloggi_subscriber' ) {
				// remove role
				//$user->remove_role($first_user_role);
				// add role
				//$user->add_role('cataloggi_customer');
			}
			
			// if user role is NOT administrator, editor, author, contributor
			if ( $first_user_role != 'administrator' && $first_user_role != 'editor' && $first_user_role != 'author' && $first_user_role != 'contributor' ) {
				// remove role
				//$user->remove_role($first_user_role);
				// add role
				//$user->add_role('cataloggi_customer');
			}
			
			if ( ! empty($user_roles) ) {
				foreach( $user_roles as $user_role )
				{
					$db_user_roles[] = $user_role;
				}
			}
			
			/*
			$cataloggi_customer = 'cataloggi_customer'; // role
			// if role 'cataloggi_customer' not in roles, add
			if( ! in_array($cataloggi_customer,$db_user_roles)) {
				
				$user = new WP_User( $user_id );
				
				// Remove role
				//$user->remove_role( 'cataloggi_subscriber' );
		
				// Add role
				//$user->add_role( $cataloggi_customer );
			}
			*/
			
		} else {
			return;
		}

	}
	
	/**
	 * Add "ctlggi_cataloggi_customer" for user meta so we can determine if a member ever bought something.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $userid
	 * @return void
	 */
    public static function ctlggi_checkout_form_process_add_extra_user_meta($userid) 
	{
		if ( empty( $userid ) )
		return;
		// if user purchased any product add ctlggi_cataloggi_customer = 1 so user become into cataloggi customer 
		// update user meta
		update_user_meta( $userid, 'ctlggi_cataloggi_customer', '1' );
	}
	
	/**
	 * Checkout form Ajax process.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_checkout_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		// parse string, convert formdata into array
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {
			// check form type
			if ( isset( $postdata['ctlggi_form_type'] ) && $postdata['ctlggi_form_type'] == 'paymentsform' ) {
				$this->ctlggi_form_type_payments_form( $formData );
			} elseif ( isset( $postdata['ctlggi_form_type'] ) && $postdata['ctlggi_form_type'] == 'checkoutform' ) {
				$this->ctlggi_form_type_checkout_form( $formData );
			} else {
				return;
			}
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
    public function ctlggi_form_type_payments_form( $formData ) 
	{
		if ( empty( $formData ) )
		return;
		
		// parse string, convert formdata into array
		parse_str($formData, $postdata);
		
		$order_id   = isset( $postdata['ctlggi_order_id'] ) ? sanitize_text_field( $postdata['ctlggi_order_id'] ) : '';
		$token      = isset( $postdata['ctlggi_access_token'] ) ? sanitize_text_field( $postdata['ctlggi_access_token'] ) : '';
		
		$payment_gateways    = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		$gateway             = $postdata['ctlggi_default_gateway'] ? sanitize_text_field( $postdata['ctlggi_default_gateway'] ) : '';
		
	    // get order by order id and access token
	    $orderdata = CTLGGI_Single_Order::ctlggi_single_order_data_for_payments_form( $order_id, $token );
		//$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
		
	    if ( ! empty($orderdata) ) {
		
			$orderdata = json_decode($orderdata, true); // convert to array
			
			// personal details
			$first_name          = isset( $postdata['ctlggi_first_name'] ) ? sanitize_text_field( $postdata['ctlggi_first_name'] ) : '';
			$last_name           = isset( $postdata['ctlggi_last_name'] ) ? sanitize_text_field( $postdata['ctlggi_last_name'] ) : '';
			$email               = isset( $postdata['ctlggi_user_email'] ) ? sanitize_text_field( $postdata['ctlggi_user_email'] ) : '';
			$phone               = isset( $postdata['ctlggi_phone'] ) ? sanitize_text_field( $postdata['ctlggi_phone'] ) : '';
			$company             = isset( $postdata['ctlggi_company'] ) ? sanitize_text_field( $postdata['ctlggi_company'] ) : '';
	
			// billing details
			$billing_country     = isset( $postdata['ctlggi_billing_country'] ) ? sanitize_text_field( $postdata['ctlggi_billing_country'] ) : '';
			$billing_city        = isset( $postdata['ctlggi_billing_city'] ) ? sanitize_text_field( $postdata['ctlggi_billing_city'] ) : '';
			$billing_state       = isset( $postdata['ctlggi_billing_state'] ) ? sanitize_text_field( $postdata['ctlggi_billing_state'] ) : '';
			$billing_addr_1      = isset( $postdata['ctlggi_billing_addr_1'] ) ? sanitize_text_field( $postdata['ctlggi_billing_addr_1'] ) : '';
			$billing_addr_2      = isset( $postdata['ctlggi_billing_addr_2'] ) ? sanitize_text_field( $postdata['ctlggi_billing_addr_2'] ) : '';
			$billing_zip         = isset( $postdata['ctlggi_billing_zip'] ) ? sanitize_text_field( $postdata['ctlggi_billing_zip'] ) : '';
	
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
			
			// generate order key
			$order_key  = CTLGGI_Checkout::ctlggi_generate_order_key();
			$date = date( 'Y-m-d H:i:s' );
			
			// order data for gateway
			$order_data = array(
				'order_id'               => $orderdata['order_id'], // order id
				'created_date'           => $orderdata['created_date'],
				'order_date'             => $date, // current date
				'order_status'           => $orderdata['order_status'], // from db
				'order_plugin_version'   => $orderdata['order_plugin_version'],
				'form_type'              => $postdata['ctlggi_form_type'], // payment form type (paymentsform or checkoutform)
				'order_access_token'     => $orderdata['order_access_token'], // from db
				'order_cus_user_id'      => $orderdata['order_cus_user_id'],
				'order_gateway'          => $gateway, // selected gateway
				'order_currency'         => $orderdata['order_currency'], // e.g. usd
				'order_key'              => $order_key, // generated from user email and current date
				'order_transaction_id'   => '', // should be empty before gateway
				'order_notes'            => $orderdata['order_notes'],
				'order_user_data'        => $order_user_data, // array
				'order_billing'          => $order_billing_data, // array
				'order_total'            => $orderdata['order_total'], // array
				'order_items'            => $orderdata['order_items'], // array
				'order_post_data'        => $postdata // array
			);
			
			$order_data = json_encode($order_data); // encode to json before send
			
			// Allow order details to be modified before send to gateway
			$order_data = apply_filters('ctlggi_filter_payments_form_before_gateway',$order_data); // <- extensible
			
			do_action( 'ctlggi_action_payments_form_before_gateway', $order_data ); // <- extensible 
			
			// ### send data to gateway ###
			CTLGGI_Checkout::ctlggi_send_order_data_to_gateway( $gateway, $order_data );
		
		}
		
	}

	/**
	 * Checkout form.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_form_type_checkout_form( $formData ) 
	{
		
		if ( empty( $formData ) )
		return;
		
		// parse string, convert formdata into array
		parse_str($formData, $postdata);
		
		// get options
		$ctlggi_general_options = get_option('ctlggi_general_options');
		
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		$default_currency_opt = $ctlggi_currency_options['catalog_currency'];
		
		$payment_gateways    = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		$gateway             = $postdata['ctlggi_default_gateway'] ? sanitize_text_field( $postdata['ctlggi_default_gateway'] ) : '';

		// personal details
		$first_name          = isset( $postdata['ctlggi_first_name'] ) ? sanitize_text_field( $postdata['ctlggi_first_name'] ) : '';
		$last_name           = isset( $postdata['ctlggi_last_name'] ) ? sanitize_text_field( $postdata['ctlggi_last_name'] ) : '';
		$email               = isset( $postdata['ctlggi_user_email'] ) ? sanitize_text_field( $postdata['ctlggi_user_email'] ) : '';
		$phone               = isset( $postdata['ctlggi_phone'] ) ? sanitize_text_field( $postdata['ctlggi_phone'] ) : '';
		$company             = isset( $postdata['ctlggi_company'] ) ? sanitize_text_field( $postdata['ctlggi_company'] ) : '';

		// billing details
		$billing_country     = isset( $postdata['ctlggi_billing_country'] ) ? sanitize_text_field( $postdata['ctlggi_billing_country'] ) : '';
		$billing_city        = isset( $postdata['ctlggi_billing_city'] ) ? sanitize_text_field( $postdata['ctlggi_billing_city'] ) : '';
		$billing_state       = isset( $postdata['ctlggi_billing_state'] ) ? sanitize_text_field( $postdata['ctlggi_billing_state'] ) : '';
		$billing_addr_1      = isset( $postdata['ctlggi_billing_addr_1'] ) ? sanitize_text_field( $postdata['ctlggi_billing_addr_1'] ) : '';
		$billing_addr_2      = isset( $postdata['ctlggi_billing_addr_2'] ) ? sanitize_text_field( $postdata['ctlggi_billing_addr_2'] ) : '';
		$billing_zip         = isset( $postdata['ctlggi_billing_zip'] ) ? sanitize_text_field( $postdata['ctlggi_billing_zip'] ) : '';

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
			
		$order_total = CTLGGI_Checkout::ctlggi_checkout_cart_totals();
		$order_items = CTLGGI_Checkout::ctlggi_checkout_cart_items();
		
		//$plugin_version = sanitize_text_field( $this->version );
		$plugin_data = get_plugin_data( CTLGGI_PLUGIN_FILE ); // array
		$plugin_version = $plugin_data['Version'];			
		
		// generate order key
		$order_key = CTLGGI_Checkout::ctlggi_generate_order_key();
		
		$date = date( 'Y-m-d H:i:s' );
		
		// order data for gateway
		$order_data = array(
			'order_id'               => '', // order id 
			'created_date'           => $date, // current date
			'order_date'             => $date, // current date
			'order_status'           => '', // should be empty before gateway
			'order_plugin_version'   => $plugin_version,
			'form_type'              => $postdata['ctlggi_form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => '', // will be created on order process
			'order_cus_user_id'      => '', // will be created on order process
			'order_gateway'          => $gateway, // selected gateway
			'order_currency'         => $default_currency_opt, // e.g. usd
			'order_key'              => $order_key, // generated from user email and current date
			'order_transaction_id'   => '', // should be empty before gateway
			'order_notes'            => '',
			'order_user_data'        => $order_user_data, // array
			'order_billing'          => $order_billing_data, // array
			'order_total'            => $order_total, // array
			'order_items'            => $order_items, // array
			'order_post_data'        => $postdata // array
		);
		
		$order_data = json_encode($order_data); // encode to json before send
		
		// Allow order details to be modified before send to gateway
		$order_data = apply_filters('ctlggi_filter_checkout_form_before_gateway',$order_data); // <- extensible
			
		// test
		//set_transient( 'ctlggi_checkout_form_before_gateway_order_data_transient_test', $order_data, 14400 ); // for ... seconds
		
		do_action( 'ctlggi_action_checkout_form_before_gateway', $order_data ); // <- extensible 
		
		// ### send data to gateway ###
		CTLGGI_Checkout::ctlggi_send_order_data_to_gateway( $gateway, $order_data );

	}
	
	/**
	 * Order Data for Gateway.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $gateway
	 * @param  object $order_data
	 * @return void
	 */
	public static function ctlggi_send_order_data_to_gateway( $gateway, $order_data ) {
		// $gateway must match the registered gateway ID
		do_action( 'ctlggi_gateway_' . $gateway, $order_data );
	}

	/**
	 * Checkout form on success message.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $message
	 */
	public static function ctlggi_checkout_form_success() {
		
		$message = array(
			'checkout_success' => array(
				'success_id' => 'checkout_success',
				'success_message' => __( 'Thank you for your purchase!', 'cataloggi' )
			)
		);
		
        return apply_filters( 'ctlggi_checkout_form_success', $message );

	}

	/**
	 * Checkout form billing details required fields.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $required_fields
	 */
	public static function ctlggi_billing_details_required_fields() {
		
		$required_fields = array(
			'ctlggi_billing_country' => array(
				'error_id' => 'billing_country_required',
				'error_message' => __( 'Please select Country.', 'cataloggi' )
			),
			'ctlggi_billing_city' => array(
				'error_id' => 'billing_city_required',
				'error_message' => __( 'City is required.', 'cataloggi' )
			),
			'ctlggi_billing_addr_1' => array(
				'error_id' => 'billing_addr_1_required',
				'error_message' => __( 'Street Addr. 1 is required.', 'cataloggi' )
			),
			'ctlggi_billing_zip' => array(
				'error_id' => 'billing_zip_required',
				'error_message' => __( 'Postcode/Zip is required.', 'cataloggi' )
			)
		);
		
        return apply_filters( 'ctlggi_billing_details_required_fields', $required_fields );

	}

	/**
	 * Checkout form validate billing details fields.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $billing
	 * @return void $print
	 */
	public static function ctlggi_validate_billing_details( $billing ) 
	{ 
		// defaults
		$print = '';
		
		if( ! empty( $billing ) ) {
			
			if ( empty( $billing['ctlggi_billing_country'] ) ) { 
				
				// username - validate
				$required = CTLGGI_Checkout::ctlggi_billing_details_required_fields();
				$errors   = $required['ctlggi_billing_country']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif ( empty( $billing['ctlggi_billing_city'] ) ) { 
				
				// username - validate
				$required = CTLGGI_Checkout::ctlggi_billing_details_required_fields();
				$errors   = $required['ctlggi_billing_city']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif ( empty( $billing['ctlggi_billing_addr_1'] ) ) { 
				
				// username - validate
				$required = CTLGGI_Checkout::ctlggi_billing_details_required_fields();
				$errors   = $required['ctlggi_billing_addr_1']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif ( empty( $billing['ctlggi_billing_zip'] ) ) { 
				
				// username - validate
				$required = CTLGGI_Checkout::ctlggi_billing_details_required_fields();
				$errors   = $required['ctlggi_billing_zip']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			}
			
			do_action( 'ctlggi_validate_billing_details' ); // <- extensible	
			
		}
		
		// validation
		if( empty( $print ) ) {
           return; // valid
		}
		
	}
	
	
}

?>