<?php

/**
 * Shopping Cart - Process Order
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Process_Order {

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
	 * Order processing manage user.
	 *
	 * @since   1.0.0
	 * @access public static
	 * @param   array $userdata
	 * @return  string $userid
	 */
    public static function ctlggi_order_processing_manage_user($userdata) 
	{	
		if ( empty( $userdata ) )
		return;
		
		// default
		$userid = '';
		
		// check if user logged in
		if ( is_user_logged_in() ) {

			// if logged in get current user data
			$current_user = wp_get_current_user();
			
			$username    = $current_user->user_login;
			//$email       = $current_user->user_email;
			//$first_name  = $current_user->user_firstname;
			//$last_name   = $current_user->user_lastname;
			//$displayname = $current_user->display_name;
			$userid      = $current_user->ID;
			
		} else {
			// register user
			if ( !empty($userdata['user_login']) && !empty($userdata['user_email']) && !empty($userdata['user_pass']) ) {
				// user data
				$user_data = array(
					'user_login'      => $userdata['user_login'], // username
					'user_pass'       => $userdata['user_pass'],
					'user_email'      => $userdata['user_email'],
					'first_name'      => $userdata['first_name'],
					'last_name'       => $userdata['last_name'],
					'user_registered' => $userdata['user_registered'], // date
					'role'            => $userdata['role'] // custom role
				);
				// register new user
				$userid = CTLGGI_Login_Register::ctlggi_register_user( $userdata=$user_data ); // return userid
			}
		}
		
		// check if we get the user id
		if( ! empty( $userid ) ) {
			//$userid = $userid;
			CTLGGI_Checkout::ctlggi_checkout_form_process_add_extra_user_meta($userid);
		}
		
		return $userid;
		
	}
	
	/**
	 * Create new order post page.
	 * 
	 * @since 1.0.0
	 * @access protected static
	 * @param int $order_cus_user_id
	 * @return  string $post_id
	 */
    protected static function ctlggi_insert_new_order_post( $order_cus_user_id ) 
	{
		// save into posts (create page)
		$insert_new_order_post = array(
			'post_title'    => 'New Order',
			'post_content'  => '',
			'post_status'   => 'pending_payment', // order payment status
			'post_author'   => intval( $order_cus_user_id ),
			'post_type'     => 'cataloggi_orders' // do not change this
		); 
		// Insert the post into the database. returns the inserted post id 
		$post_id = wp_insert_post( $insert_new_order_post );
		
		// Update post, update inserted post title
		$update_new_order_post = array(
			  'ID'           => intval( $post_id ),
			  'post_title'   => 'order-#' . intval( $post_id ),
			  'post_name'    => 'order-' . intval( $post_id )
		); 
		// Update the post into the database
		wp_update_post( $update_new_order_post );	
		
		return $post_id;
	}

	/**
	 * Insert order data.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param object $orderdata
	 * @return  string $post_id
	 */
    public static function ctlggi_insert_order_data( $orderdata ) 
	{
		// default
		$error_log = '';
		$post_id   = '';
		
		if ( empty( $orderdata ) ) {    
			// record error log
			$error_log = CTLGGI_Error_Log::ctlggi_error_log( 'insert_order_data_order_data_empty', __('Empty order data.', 'cataloggi') );
			return false;
		}
		
		$orderdata = json_decode($orderdata, true); // convert to array
		
		$order_data = array(
			'order_id'               => $orderdata['order_id'], // order id
			'created_date'           => sanitize_text_field( $orderdata['created_date'] ),
			'order_date'             => sanitize_text_field( $orderdata['order_date'] ),
			'order_status'           => sanitize_text_field( $orderdata['order_status'] ), 
			'order_plugin_version'   => sanitize_text_field( $orderdata['order_plugin_version'] ),
			'form_type'              => sanitize_text_field( $orderdata['form_type'] ), // payment form type (paymentsform or checkoutform)
			'order_access_token'     => sanitize_text_field( $orderdata['order_access_token'] ), 
			'order_cus_user_id'      => sanitize_text_field( $orderdata['order_cus_user_id'] ),
			'order_gateway'          => sanitize_text_field( $orderdata['order_gateway'] ),
			'order_currency'         => sanitize_text_field( $orderdata['order_currency'] ), // e.g. usd
			'order_key'              => sanitize_text_field( $orderdata['order_key'] ),
			'order_transaction_id'   => sanitize_text_field( $orderdata['order_transaction_id'] ),
			'order_notes'            => sanitize_text_field( $orderdata['order_notes'] ),
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'], // array
			'order_post_data'        => $orderdata['order_post_data'] // array
		);
		
	    if ( ! wp_verify_nonce( $orderdata['order_post_data']['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {	
		   // record error log
		   $error_log = CTLGGI_Error_Log::ctlggi_error_log( 'insert_order_nonce_failed', __('Nonce verification failed.', 'cataloggi') );
		   return false;
		}
		
		$payment_gateways   = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		$selected_gateway   = $order_data['order_gateway'];
		
		if( ! empty( $selected_gateway ) ) {	
			foreach($payment_gateways as $gateway => $value )
			{  
			   // get the selected gateway
			   if ( $selected_gateway == $gateway ) 
			   {
					$payment_gateway_label = sanitize_text_field( $value['payment_gateway_label'] );
					$payment_gateway_name  = sanitize_text_field( $value['payment_gateway_name'] );
					$create_an_account     = sanitize_text_field( $value['create_an_account'] );
					$credit_card_details   = sanitize_text_field( $value['credit_card_details'] ); // display card details fields
					$billing_details       = sanitize_text_field( $value['billing_details'] );
			   }
			}
		} else {
			// error, no gateway selected
		}
		
		$order_plugin_version = isset( $order_data['order_plugin_version'] ) ? sanitize_text_field( $order_data['order_plugin_version'] ) : '';
		$form_type            = isset( $order_data['form_type'] ) ? sanitize_text_field( $order_data['form_type'] ) : '';
		$order_cus_user_id    = isset( $order_data['order_cus_user_id'] ) ? sanitize_text_field( $order_data['order_cus_user_id'] ) : '';
		$order_gateway        = isset( $order_data['order_gateway'] ) ? sanitize_text_field( $order_data['order_gateway'] ) : '';
		$order_key            = isset( $order_data['order_key'] ) ? sanitize_text_field( $order_data['order_key'] ) : '';
		$order_currency       = isset( $order_data['order_currency'] ) ? sanitize_text_field( $order_data['order_currency'] ) : '';
		$order_date           = isset( $order_data['order_date'] ) ? sanitize_text_field( $order_data['order_date'] ) : '';
		$order_transaction_id = isset( $order_data['order_transaction_id'] ) ? sanitize_text_field( $order_data['order_transaction_id'] ) : '';
		$order_notes          = isset( $order_data['order_notes'] ) ? sanitize_text_field( $order_data['order_notes'] ) : '';
		$order_status         = isset( $order_data['order_status'] ) ? sanitize_text_field( $order_data['order_status'] ) : '';
		
		$cus_extra_notes = isset( $orderdata['order_post_data']['cus_extra_notes'] ) ? wp_kses_post( $orderdata['order_post_data']['cus_extra_notes'] ) : '';

		// creates new post page for order
        $post_id = CTLGGI_Process_Order::ctlggi_insert_new_order_post( $order_cus_user_id );
		
		// personal details (user data)
		$first_name = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
		$last_name  = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
		$email      = isset( $order_data['order_user_data']['email'] ) ? sanitize_email( $order_data['order_user_data']['email'] ) : '';
		$phone      = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
		$company    = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';
		
		// Save Meta to (postmeta) _first_name, _last_name, _email, _phone, _company
		add_post_meta( $post_id, '_first_name', $first_name, true );
		add_post_meta( $post_id, '_last_name', $last_name, true );
		add_post_meta( $post_id, '_email', $email, true );
		add_post_meta( $post_id, '_phone', $phone, true );
		add_post_meta( $post_id, '_company', $company, true );
		
		// Save Meta to (postmeta) 
		// _order_plugin_version, _ctlggi_order_cus_user_id, _order_gateway, _order_key, _order_transaction_id, _order_currency, _order_date, _order_status
		add_post_meta( $post_id, '_order_plugin_version', $order_plugin_version, true );
		add_post_meta( $post_id, '_ctlggi_form_type', $form_type, true );
		add_post_meta( $post_id, '_ctlggi_order_cus_user_id', $order_cus_user_id, true );
		add_post_meta( $post_id, '_order_gateway', $order_gateway, true );
		add_post_meta( $post_id, '_order_key', $order_key, true );
		add_post_meta( $post_id, '_order_currency', $order_currency, true );
		add_post_meta( $post_id, '_order_date', $order_date, true );
		add_post_meta( $post_id, '_created_date', $order_data['created_date'], true );
		add_post_meta( $post_id, '_order_transaction_id', $order_transaction_id, true );
		add_post_meta( $post_id, '_order_notes', $order_notes, true );
		add_post_meta( $post_id, '_order_status', $order_status, true );
		add_post_meta( $post_id, '_ctlggi_cus_extra_notes', $cus_extra_notes, true ); // extra notes textarea on checkout
		
		// MANAGE ACCESS TOKEN
		$order_access_token  = get_post_meta( $post_id, '_ctlggi_order_access_token', true );
		if ( empty($order_access_token) ) {
			// create access token
			$access_token = CTLGGI_Helper::ctlggi_generate_random_string($length='12');
			update_post_meta( $post_id, '_ctlggi_order_access_token', $access_token );
		}
		
		// Save Meta to (postmeta) : order total->subtotal
		$subtotal = $order_data['order_total']['subtotal'];
		// Format Order Total
        $subtotal = CTLGGI_Amount::ctlggi_format_amount($amount=$subtotal);
		// amount to string
		$subtotal = CTLGGI_Amount::ctlggi_format_amount_to_string($subtotal);
		add_post_meta( $post_id, '_order_subtotal', $subtotal, true );
		
		// Save Meta to (postmeta) : order total->total
		$total = $order_data['order_total']['total'];
		// Format Order Total
        $total = CTLGGI_Amount::ctlggi_format_amount($amount=$total);
		// amount to string
		$total = CTLGGI_Amount::ctlggi_format_amount_to_string($total);
		add_post_meta( $post_id, '_order_total', $total, true );
		
		// insert billing details
		if ( $billing_details == '1' && ! empty( $orderdata['order_billing'] ) ) { 
			$billing_country  = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
			$billing_city     = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
			$billing_state    = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
			$billing_addr_1   = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
			$billing_addr_2   = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
			$billing_zip      = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
			
			// Save Meta to (postmeta) : billing details: _billing_addr_1, _billing_addr_1, _billing_country, _billing_state, _billing_city, _billing_zip
			add_post_meta( $post_id, '_billing_country', $billing_country, true );
			add_post_meta( $post_id, '_billing_city', $billing_city, true );
			add_post_meta( $post_id, '_billing_state', $billing_state, true );
			add_post_meta( $post_id, '_billing_addr_1', $billing_addr_1, true );
			add_post_meta( $post_id, '_billing_addr_2', $billing_addr_2, true );
			add_post_meta( $post_id, '_billing_zip', $billing_zip, true );
		}
		
		$order_items = $order_data['order_items'];
		
		$order_id = $post_id; // come from posts
		$order_item_id = ''; // default
		foreach($order_items as $key=>$value)
		{
		  
		  $item_id           = intval( $value['item_id'] ); // item_id is the product id
		  $order_item_name   = sanitize_text_field( $value['item_name'] );
		  $payment_type      = sanitize_text_field( $value['item_payment_type'] );
		  
		  $price_option_id   = intval( $value['price_option_id'] );
		    
			// PRICE OPTION
			$price_option_name = ''; // default
			// notset defined in class-ctlggi-payment-buttons.php
			if ( $price_option_id != '' ) {
				// get data
				$price_options = get_post_meta( $item_id, '_ctlggi_price_options', true ); // json
				$price_options = json_decode($price_options); // convert to object
				$price_option_name = $price_options->$price_option_id->option_name;
			}
		  
		  $item_downloadable = $value['item_downloadable'];
		  if ( $item_downloadable == '1') {
			  $order_item_type = 'downloadable';
			  
			 // if downloadable insert into "ctlggi_order_downloads"  db
			 CTLGGI_Process_Order::ctlggi_process_order_downloads_insert_data( $order_id, $item_id, $user_id=$order_cus_user_id, $user_email=$email, $order_key, $order_date );
			  
		  } else {
			 $order_item_type = 'service'; 
		  }
		  
		  // order items insert data
		  $order_item_id = CTLGGI_Process_Order::ctlggi_process_order_items_insert_data( $order_item_name, $payment_type, $order_item_type, $price_option_id, $price_option_name, $order_id );
		  
		  foreach($value as $meta_key=>$meta_value)
		  {
			  if ( $meta_key == 'item_id' or $meta_key == 'item_price' or $meta_key == 'item_quantity' or $meta_key == 'item_total' ) {
				  // order item meta insert data
				  CTLGGI_Process_Order::ctlggi_process_order_itemmeta_insert_data( $order_item_id, $meta_key, $meta_value );
			  }
		  }
		
		}

      if ( ! empty( $error_log ) ) {
        return false;
	  } else {  
		return $post_id;  
	  }
		
	}

	/**
	 * Insert order items data.
	 *
	 * @global $wpdb
	 *
	 * @since 1.0.0
	 * @access protected static
	 * @param string $order_item_name
	 * @param string $order_item_type
	 * @param int    $price_option_id
	 * @param string $price_option_nam
	 * @param int    $order_id
	 * @return       int last insert ID
	 */
	protected static function ctlggi_process_order_items_insert_data( $order_item_name, $payment_type, $order_item_type, $price_option_id, $price_option_name, $order_id ) 
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_items';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'order_item_name'   => sanitize_text_field( $order_item_name ), 
				'payment_type'      => sanitize_text_field( $payment_type ), // normal or subscription
				'order_item_type'   => sanitize_text_field( $order_item_type ), 
				'price_option_id'   => sanitize_text_field( $price_option_id ),
				'price_option_name' => sanitize_text_field( $price_option_name ),
				'order_id'          => sanitize_text_field( $order_id ),
			) 
		);
		
		return $wpdb->insert_id;
		
	}

	/**
	 * Insert order item meta data.
	 *
	 * @global $wpdb
	 *
	 * @since 1.0.0
	 * @access protected static
	 * @param int    $order_item_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @return       int last insert ID
	 */
	protected static function ctlggi_process_order_itemmeta_insert_data( $order_item_id, $meta_key, $meta_value ) 
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_itemmeta';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'order_item_id' => sanitize_text_field( $order_item_id ), 
				'meta_key'      => sanitize_text_field( '_' . $meta_key ), 
				'meta_value'    => sanitize_text_field( $meta_value ), 
			) 
		);
		
		return $wpdb->insert_id;
		
	}

	/**
	 * Insert order download data.
	 *
	 * @global $wpdb
	 *
	 * @since 1.0.0
	 * @access protected static
	 * @param int     $order_id
	 * @param int     $item_id
	 * @param int     $user_id
	 * @param string  $user_email
	 * @param string  $order_key
	 * @param string  $order_date
	 * @return        int last insert ID
	 */
	protected static function ctlggi_process_order_downloads_insert_data( $order_id, $item_id, $user_id, $user_email, $order_key, $order_date )
	{
		if ( empty( $order_id ) )
		return;
		
		// get download_limit, download_expiry_date by item_id
		$download_limit        = get_post_meta( $item_id, '_ctlggi_item_download_limit', true );
		$item_download_expiry  = get_post_meta( $item_id, '_ctlggi_item_download_expiry', true ); // return int
		
		if ( empty($download_limit) ) {
			$download_limit = '';
		} else {
			$download_limit = $download_limit;
		}
		
		// if empty never expires
		if ( empty($item_download_expiry) ) {
			$download_expiry_date = ''; // <- never expires, 0000-00-00
		} else {
			// order date + item download expiry 
			$add_days = strtotime(date("Y-m-d", strtotime($order_date)) . "+" . $item_download_expiry . " days");
			$download_expiry_date = date('Y-m-d', $add_days);
		}
		
		// insert into "ctlggi_order_downloads"
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_downloads';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'order_id'             => sanitize_text_field( $order_id ),
				'item_id'              => sanitize_text_field( $item_id ),
				'user_id'              => sanitize_text_field( $user_id ),
				'user_email'           => sanitize_email( $user_email ),
				'order_key'            => sanitize_text_field( $order_key ), 
				'download_limit'       => sanitize_text_field( $download_limit ),
				'download_expiry_date' => sanitize_text_field( $download_expiry_date ),
				'download_count'       => sanitize_text_field( '0' ),
				'order_date'           => sanitize_text_field( $order_date )
			) 
		);
		
		return $wpdb->insert_id;
		
		
	}

	/**
	 * Order processing success.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int     $post_id
	 * @param object  $orderdata
	 * @param string  $order_status
	 * @return        html $success - success message
	 */
    public static function ctlggi_order_processing_success( $post_id, $orderdata, $order_status ) 
	{
		if ( ! empty( $post_id ) && ! empty( $orderdata ) && ! empty( $order_status ) ) {
			
            $orderdata = json_decode($orderdata, true); // convert to array
			$currentdate = date("Y-m-d H:i:s");
			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			
			$order_id_cookie_name    = CTLGGI_Cookies::ctlggi_order_id_cookie_name();
			// set cookie, expires in 1 day
			$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$order_id_cookie_name, $value=$post_id, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
			
			$order_key_cookie_name = CTLGGI_Cookies::ctlggi_order_key_cookie_name();
			// set cookie, expires in 1 day
			$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$order_key_cookie_name, $value=$orderdata['order_key'], $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
			
			$order_data = array(
				  'post_id'       => intval( $post_id ),
				  'order_status'  => sanitize_text_field( $order_status ),
				  'order_data'    => $orderdata
			); 
			// encode to json and save in transient
			$order_data = json_encode($order_data);
			
			if ( $order_status == 'completed' ) {	
			
				// update : post status
				$update_order_post = array(
					  'ID'           => intval( $post_id ),
					  'post_status'  => sanitize_text_field( $order_status ) // order payment status
				); 
				// Update the post
				wp_update_post( $update_order_post );
				
				update_post_meta( $post_id, '_order_status', $order_status ); // updating order status
				update_post_meta( $post_id, '_order_transaction_id', $orderdata['order_transaction_id'] );
				update_post_meta( $post_id, '_order_date', $currentdate );
				
				// software licensing using this
				do_action( 'ctlggi_order_processing_success_order_completed', $order_data ); // <- extensible 
			    
			}
			
			$orderdata_trn = json_encode($orderdata);
			set_transient( 'ctlggi_order_processing_success_order_data', $orderdata_trn, 14400 ); // for ... seconds
			
			do_action( 'ctlggi_order_processing_success_after', $order_data ); // <- extensible 
			
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			
			// cart items cookie 
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			// cart totals cookie
			$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
			
			// #### important !!! order data for emails should be placed after (if status completed)
			// SEND EMAIL notification and downloadable files links
			$orderdata = json_encode($orderdata); // encode to json before send
			
			do_action( 'ctlggi_order_processing_success_before_emails', $order_data ); // <- extensible 
			
			CTLGGI_Notification_Emails::ctlggi_order_data_for_emails( $post_id, $orderdata, $order_status );
			
			#### Delete Cookies after Emails sent ###
			// delete cart items cookie 
			$del_cookie_items  = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_items_cookie_name, $path = '/', $domain, $remove_from_global = false);
			// delete cart totals cookie
			$del_cookie_totals = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_totals_cookie_name, $path = '/', $domain, $remove_from_global = false);
			    
		}
		// checkout form success message
		$success_id = 'checkout_success';
		$success_message = __('Thank you for your purchase!', 'cataloggi');
		$success = CTLGGI_Validate::ctlggi_success_msg( $success_id, $success_message );
		return $success;
	}

	/**
	 * Order processing error.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int     $post_id
	 * @param object  $orderdata
	 * @param string  $order_status
	 * @param string  $error_id
	 * @param string  $error_message
	 * @return        html $errors - error message
	 */
    public static function ctlggi_order_processing_error( $post_id, $orderdata, $order_status, $error_id, $error_message ) 
	{
	   if ( ! empty( $orderdata ) ) {
	      $orderdata = json_decode($orderdata, true); // convert to array
	   }
		
	   if ( ! empty( $error_id ) && ! empty( $error_message ) ) {
	     // save error in the error log
	     CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
	   }
	   
		if ( ! empty( $post_id ) && ! empty( $orderdata ) && ! empty( $order_status ) ) {
			// update : post status
			$update_order_post = array(
				  'ID'           => intval( $post_id ),
				  'post_status'  => sanitize_text_field( $order_status ) // order payment status
			); 
			// Update the post
			wp_update_post( $update_order_post );	
			
		}
		
	   $errors = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error msg
	   return $errors;
	   //echo json_encode(array('checkoutsuccess'=>false, 'message'=>$errors )); // return json	
	   
	}

	/**
	 * Order processing success redirect url.
	 *
	 * @since   1.0.0
	 * @access public static
	 * @return string $success_redirect_url
	 */
    public static function ctlggi_order_processing_success_redirect_url() 
	{
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		
		// make page redirect or execute a success message
		$page_id = $ctlggi_cart_options['success_page'];
		// redirect to page if exist
		if ( $page_id != '0' || ! empty($page_id) ) {
			// get page link by post id
			$page_link = get_permalink( $page_id );
			$success_redirect_url = $page_link;
		} else {
			// execute a success message if redirect url 0
			$success_redirect_url = '0';
		}
		return $success_redirect_url;
	}

	/**
	 * Order processing form, form loader image.
	 *
	 * @since   1.0.0
	 * @access public static
	 * @return string $formloaderimg
	 */
    public static function ctlggi_order_processing_form_loader_image() 
	{
		// form loader image
		$formloaderimg = CTLGGI_PLUGIN_URL . 'assets/images/spinner.gif';
		return $formloaderimg;
	}

	/**
	 * Nonce validation failed, save in error log.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param object  $orderdata
	 * @param string  $error_id
	 * @param string  $error_message
	 * @return        void
	 */
    public static function ctlggi_order_processing_nonce_failed( $orderdata, $error_id, $error_message ) 
	{
		
	   if ( ! empty( $error_id ) && ! empty( $error_message ) ) {
	     // save error in the error log
	     CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
	   }
	   // return error: Nonce verification failed!
	   //$errors = CTLGGI_Validate::ctlggi_error_msg( 'order_processing_nonce_failed', __('Nonce verification failed.', 'cataloggi') ); // output error msg
	   //echo json_encode(array('checkoutsuccess'=>false, 'message'=>$errors )); // return json
	}

	
}

?>