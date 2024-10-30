<?php

/**
 * Shop Payment Gateway PayPal Standard class. 
 *
 * @package     cataloggi
 * @subpackage  Public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_PayPal_Standard {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;
	
	private $response;
	
    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';
	
    /** HTTPS Protocol */
    const HTTPS_PROTOCOL = 'https://';
	
    /** PayPal URL */
    const PAYPAL_URI = 'www.paypal.com/cgi-bin/webscr';
    /** PayPal Sandbox URL */
    const PAYPAL_SANDBOX_URI = 'www.sandbox.paypal.com/cgi-bin/webscr';

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
	 * Listens for PayPal IPN.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_paypal_response() {
		// e.g. https://example.com/?ctlggi-listener=paypalres
		if ( isset( $_GET['ctlggi-listener'] ) && $_GET['ctlggi-listener'] == 'paypalres' ) { 
		
			// defaults
			$error_id = '';
			$error_message = '';
			
			$res = get_transient( 'ctlggi_paypal_ipn_res' ); // POST Data
			$invoice = $res['invoice'];  // order key
			
			/*
			if ( empty($res) ) {
				// error: empty_paypal_ipn_res
				$error_id = 'paypal_ipn_response_empty';
				$error_message = __('PayPal IPN empty response.', 'cataloggi');
			}
			*/

			$payment_status   = $res['payment_status']; // Completed, Pending, and Denied. 
			$order_id         = $res['custom']; // order id
			
			$orderdata = get_transient( 'ctlggi_order_data_transient' ); // json encoded
			$orderdata = json_decode($orderdata, true);
			// get option
			$ctlggi_cart_options = get_option('ctlggi_cart_options');
			
			// clear cookies
			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			// cookie name
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			// cookie name
			$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
			// delete cookie
			$del_cookie_items  = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_items_cookie_name, $path = '/', $domain, $remove_from_global = false);
			// delete cookie
			$del_cookie_totals = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_totals_cookie_name, $path = '/', $domain, $remove_from_global = false);
			
			/*
			echo '<pre>';
			print_r( $res );
			echo '</pre>';
			
			echo '<pre>';
			print_r( $orderdata );
			echo '</pre>';
			
			exit; 
			*/
			
			// manage form type, payment form type (paymentsform or checkoutform)
			if ( $orderdata['form_type'] == 'checkoutform' ) {
				$page = 'checkout';
			} elseif ( $orderdata['form_type'] == 'paymentsform' ) {
				$page = 'payments';
			} else {
				$page = 'checkout'; // default
			}
			
			if ( ! count($res)) {
				// Missing Response Data, might be beacause of PayPal's delayed response
				$redirect_status = 'processing';
			} elseif ( $payment_status == 'Completed' ) {
				$redirect_status = 'completed';
			} elseif ( $payment_status == 'Pending' ) {
				$redirect_status = 'pending';
			} else {
				$redirect_status = 'failed';
			}
			
			if ( ! empty($redirect_status) ) {
				$page_link = ''; // def
				$path = '?page=' . $page . '&paypal=' . $redirect_status;
				$page_id = $ctlggi_cart_options['success_page'];
				// check if value not 0 (0 value defined as default in class-cwctlg-activator.php)
				if ( $page_id != '0' && ! empty($page_id) ) {
					$page_link = get_permalink( $page_id );
				} else {
					$page_id = $ctlggi_cart_options['checkout_page'];
					if ( $page_id != '0' && ! empty($page_id) ) {
						$page_link = get_permalink( $page_id );
					} else {
						$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
						$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';
						$page_link = $cataloggiurl;
					}
				}
				if ( ! empty($page_link) ) {
					wp_redirect( $page_link . $path, 302 );
					exit();
				}
			}
			
			if ( ! empty($error_id) ) {
				 // save error in the error log
				 CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			}

		} else {
			return;
		}
		
	}
	
	/**
	 * Listens for PayPal IPN.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_listen_paypal_ipn() {
		// e.g. https://example.com/?ctlggi-listener=paypalipn
		if ( isset( $_GET['ctlggi-listener'] ) && $_GET['ctlggi-listener'] == 'paypalipn' ) {
			
			// defaults
			$error_id = '';
			$error_message = '';
			
			// get options
			$paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
			$account_mode           = trim( $paypalstandard_options['ctlggi_paypalstandard_account_mode'] ); // test or live
			$paypalstandard_email   = $paypalstandard_options['ctlggi_paypalstandard_email'];
			
			require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/payment-gateways/paypal/PaypalIPN.php';
			
			$ipn = new CTLGGI_PaypalIPN();
			// Use the sandbox endpoint during testing.
			if ( $account_mode == 'test' ) {
			    $ipn->useSandbox();
			}
			// Use the PHP certs.
			$use_php_certs = '1';
			if ( $use_php_certs == '1' ) {
			    $ipn->usePHPCerts();
			}
			$verified = $ipn->verifyIPN();
			if ($verified) {
				
				/*
				 * Process IPN
				 * A list of variables is available here:
				 * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
				 * $_POST = super global
				 */
				
				if ( ! count($_POST)) {
					
					// Missing POST Data, might be beacause of PayPal's delayed response
					$order_status = 'processing';
					// update the above fields upon successful payment
					update_post_meta( $order_id, '_order_status', $order_status ); // updating order status
					$error_id = 'paypal_ipn_missing_post_data';
					$error_message = __( 'PayPal IPN: Missing POST Data, might be beacause of PayPal delayed response.', 'cataloggi' );
					$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id=$order_id, $orderdata, $order_status, $error_id, $error_message );
					
				} else {
					
					// store data in transient
					delete_transient( 'ctlggi_paypal_ipn_res' );
					set_transient( 'ctlggi_paypal_ipn_res', $_POST, 14400 );
					
					$this->response = $_POST;
					
					$payment_status   = $_POST['payment_status']; // Completed, Pending, and Denied. 
					$order_id_paypal  = $_POST['custom']; // order id
					$txn_id           = $_POST['txn_id']; // transaction ID
					$business_email   = $_POST['business'];
					$payment_currency = $_POST['mc_currency'];
					$payment_date     = $_POST['payment_date'];
					$payer_email      = $_POST['payer_email'];
					$invoice          = $_POST['invoice']; // order key
					
					//$orderdata = get_transient( 'ctlggi_order_data_transient' ); // json encoded
					$orderdata = CTLGGI_Single_Order::get_order_data_by_order_key( $order_key=$invoice );
					$orderdata = json_decode($orderdata, true); // convert to array
					$order_id  = isset( $orderdata['order_id'] ) ? sanitize_text_field( $orderdata['order_id'] ) : '';

					if ( empty($order_id) ) {
						$error_id = 'paypal_ipn_listener_order_id_missing';
						$error_message = __('PayPal IPN listener order ID missing.', 'cataloggi');
					} elseif ( $order_id_paypal != $order_id ) {
						$error_id = 'paypal_ipn_listener_order_ids_not_match';
						$error_message = __('PayPal IPN listener order IDs not match.', 'cataloggi');
					} elseif ( $invoice != $orderdata['order_key'] ) {
						$error_id = 'paypal_ipn_listener_order_keys_not_match';
						$error_message = __('PayPal IPN listener order keys not match.', 'cataloggi');
					} else {
						// get options
						$ctlggi_currency_options = get_option('ctlggi_currency_options');
						$catalog_currency        = $ctlggi_currency_options['catalog_currency'];
						
						$current_date = date( 'Y-m-d H:i:s' );
						
						// source: https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNandPDTVariables/
						// PayPal statuses: Canceled_Reversal, Completed, Created, Denied, Expired, Failed, Pending, Refunded, Reversed, Processed, Voided
	
						if ( $payment_status == 'Completed' ) {
							// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
							$order_status = 'completed';
		
							$order_data = array(
								'order_id'               => $order_id, // order id
								'created_date'           => $orderdata['created_date'],
								'order_date'             => $current_date, // now
								'order_status'           => $order_status, 
								'order_plugin_version'   => $orderdata['order_plugin_version'],
								'form_type'              => $orderdata['form_type'], // payment form type (paymentsform or checkoutform)
								'order_access_token'     => $orderdata['order_access_token'], // from db
								'order_cus_user_id'      => $orderdata['order_cus_user_id'],
								'order_gateway'          => $orderdata['order_gateway'],
								'order_currency'         => $orderdata['order_currency'], // e.g. usd
								'order_key'              => $orderdata['order_key'],
								'order_transaction_id'   => $txn_id,
								'order_notes'            => $orderdata['order_notes'],
								'order_user_data'        => $orderdata['order_user_data'], // array
								'order_billing'          => $orderdata['order_billing'], // array
								'order_total'            => $orderdata['order_total'], // array
								'order_items'            => $orderdata['order_items'], // array
								'order_post_data'        => '', // array (should be empty)
							);
							$order_data = json_encode($order_data); // json encode before send
							
							// SUCCESS
							$print = CTLGGI_Process_Order::ctlggi_order_processing_success( $post_id=$order_id, $orderdata=$order_data, $order_status ); 
							
						} elseif ( $payment_status == 'Pending' && isset( $_POST['pending_reason'] ) ) {
							
							$error_id = 'paypal_ipn_pending';
							$pending_reason = strtolower( $_POST['pending_reason'] ); // This variable is set only if payment_status is Pending.
							
							// return pending reasons
							$error_message = CTLGGI_PayPal_Standard::paypal_pending_reason( $pending_reason );
							
							if( ! empty( $error_message ) ) {
								// save note 
								update_post_meta( $order_id, '_order_notes', $error_message );
							}
							
							// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
							$order_status = 'pending_payment';
							// update the above fields upon successful payment
							update_post_meta( $order_id, '_order_transaction_id', $txn_id );
							update_post_meta( $order_id, '_order_status', $order_status ); // updating order status
							
							$error_id = 'paypal_ipn_pending_payment';
							$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id=$order_id, $orderdata, $order_status, $error_id, $error_message );
							
						} elseif ( $payment_status == 'Failed' ) {
							
							// FAILED
							// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
							$order_status = 'failed';
							// update the above fields upon successful payment
							update_post_meta( $order_id, '_order_transaction_id', $txn_id );
							update_post_meta( $order_id, '_order_status', $order_status ); // updating order status
							$error_id = 'paypal_ipn_transaction_failed';
							$error_message = __( 'PayPal IPN: Transaction failed.', 'cataloggi' );
							$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id=$order_id, $orderdata, $order_status, $error_id, $error_message );
						}
					
					}
				}
				
				// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
				header("HTTP/1.1 200 OK");
				
			} else {
				// error: empty_paypal_ipn_res
				$error_id = 'paypal_ipn_listener_invalid_response_not_verified';
				$error_message = __('PayPal IPN listener invalid response NOT verified.', 'cataloggi');
			}
			
			if ( ! empty($error_id) ) {
				 // save error in the error log
				 CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			}
			
		} else {
			return;
		}
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
    public static function ctlggi_order_data_for_gateway_paypalstandard( $order_data ) 
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
		set_transient( 'ctlggi_order_data_transient', $orderdata, 12 * HOUR_IN_SECONDS ); // for ... seconds
	}

	/**
	 * Process PayPal Standard payment Ajax.
	 *
	 * @since 1.0.0
	 * @return object
	 */
    public function ctlggi_process_paypalstandard_payment() 
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
				$this->ctlggi_paypalstandard_payments_form_process( $orderdata );
			} elseif ( isset( $order_data['form_type'] ) && $order_data['form_type'] == 'checkoutform' ) {
				$this->ctlggi_paypalstandard_checkout_form_process( $orderdata );
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
    public function ctlggi_paypalstandard_payments_form_process( $orderdata ) 
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
				
				// recreate array
				$order_user_data = array(
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'email'      => $email,
					'phone'      => $phone,
					'company'    => $company
				);
				
				update_post_meta( $order_id, '_billing_addr_1', $addr_1 );
				update_post_meta( $order_id, '_billing_addr_2', $addr_2 );
				update_post_meta( $order_id, '_billing_country', $country );
				update_post_meta( $order_id, '_billing_state', $state );
				update_post_meta( $order_id, '_billing_city', $city );
				update_post_meta( $order_id, '_billing_zip', $zip );
				
				// recreate array
				$order_billing_data = array(
					'billing_country'       => $country,
					'billing_city'          => $city,
					'billing_state'         => $state,
					'billing_addr_1'        => $addr_1,
					'billing_addr_2'        => $addr_2,
					'billing_zip'           => $zip
				);
				
				update_post_meta( $order_id, '_order_gateway', $order_gateway );
				update_post_meta( $order_id, '_order_key', $order_key );
				
				$transaction_id_paypalstandard = '';
				// important, if payment status = completed we will send the downloadable file(s) urls in the order receipt email
				$order_status = 'pending_payment'; // should be pending_payment
				
				// update the above fields upon successful payment
				update_post_meta( $order_id, '_order_transaction_id', $transaction_id_paypalstandard );
				update_post_meta( $order_id, '_order_status', $order_status ); // updating order status
	
				$orderdata = array(
					'order_id'               => $order_data['order_id'], // order id
					'created_date'           => $order_data['created_date'],
					'order_date'             => $order_data['order_date'],
					'order_status'           => $order_status, // from db
					'order_plugin_version'   => $order_data['order_plugin_version'],
					'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
					'order_access_token'     => $order_data['order_access_token'], // from db
					'order_cus_user_id'      => $order_data['order_cus_user_id'],
					'order_gateway'          => $order_gateway,
					'order_currency'         => $order_data['order_currency'], // e.g. usd
					'order_key'              => $order_key,
					'order_transaction_id'   => $order_data['order_transaction_id'],
					'order_notes'            => $order_data['order_notes'],
					'order_user_data'        => $order_user_data, // array
					'order_billing'          => $order_billing_data, // array
					'order_total'            => $order_data['order_total'], // array
					'order_items'            => $order_data['order_items'], // array
					'order_post_data'        => $order_data['order_post_data'] // array
				);
				
				$orderdata = json_encode($orderdata); // encode to json before send
				
				// test
				$orderdata_test = json_encode($orderdata);
				delete_transient( 'ctlggi_order_data_transient_test' );
				set_transient( 'ctlggi_order_data_transient_test', $orderdata, 14400 ); // for ... seconds
				
				// PAYPAL PROCESS
				$this->ctlggi_paypalstandard_process_payment( $post_id=$order_id, $orderdata );
				
				// delete transients
				//delete_transient( 'ctlggi_order_data_transient' );
			
			}
		
		}
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}

	/**
	 * Checkout form.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_paypalstandard_checkout_form_process( $orderdata ) 
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
			'role'            => 'subscriber' // subscriber, cataloggi_customer etc.
		);
		
		// manage user (register)
		$userid = CTLGGI_Process_Order::ctlggi_order_processing_manage_user($userdata);
		
		$order_status = 'pending_payment'; // should be pending_payment

		$orderdata = array(
			'order_id'               => $order_data['order_id'], // order id
			'created_date'           => $order_data['created_date'],
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_status, 
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
            // PAYPAL PROCESS
			$this->ctlggi_paypalstandard_process_payment( $post_id, $orderdata );	
		}
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}

	/**
	 * PayPal redirect url.
	 *
	 * @since  1.0.0
	 * @return string
	 */
    public static function ctlggi_paypal_redirect_url() 
	{
		// get options
		$paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
		$account_mode = trim( $paypalstandard_options['ctlggi_paypalstandard_account_mode'] ); // test or live
		// Check the account mode
        if ( $account_mode == 'test' ) {
			// Test mode
            return self::HTTPS_PROTOCOL . self::PAYPAL_SANDBOX_URI;
        } elseif ( $account_mode == 'live' ) {
			// Live mode
            return self::HTTPS_PROTOCOL . self::PAYPAL_URI;
        } else {
			throw new Exception("PayPal redirect Url Invalid account mode.");
		}		
	}
	
	/**
	 * PayPal page style.
	 *
	 * @since  1.0.0
	 * @return string $page_style
	 */
    public static function ctlggi_paypal_page_style() 
	{
		// get options
		$paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
		$page_style = $paypalstandard_options['ctlggi_paypalstandard_page_style'];
		if ( ! empty($page_style) ) {
			$page_style = trim( $page_style );
		} else {
			$page_style = '';
		}
		return apply_filters( 'ctlggi_paypal_page_style', $page_style );
	}
	
	/**
	 * PayPal Standard process payment.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_paypalstandard_process_payment( $post_id, $orderdata ) 
	{	
		if ( empty( $post_id ) && empty( $orderdata ) )
		return;
		
		$order_data = json_decode($orderdata, true); // convert to array
		
		$current_date = date( 'Y-m-d H:i:s' );
		
		$orderdata = array(
			'order_id'               => $post_id, // add order id
			'created_date'           => $order_data['created_date'],
			'order_date'             => $order_data['order_date'],
			'order_status'           => $order_data['order_status'], 
			'order_plugin_version'   => $order_data['order_plugin_version'],
			'form_type'              => $order_data['form_type'], // payment form type (paymentsform or checkoutform)
			'order_access_token'     => $order_data['order_access_token'], 
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
		
		// get domain name
		$domain = CTLGGI_Helper::ctlggi_site_domain_name();

		$order_id_cookie_name    = CTLGGI_Cookies::ctlggi_order_id_cookie_name();
		// set cookie, expires in 1 day
		$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$order_id_cookie_name, $value=$post_id, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
		
		$order_key_cookie_name = CTLGGI_Cookies::ctlggi_order_key_cookie_name();
		// set cookie, expires in 1 day
		$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$order_key_cookie_name, $value=$order_data['order_key'], $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
		
		// delete transients
		delete_transient( 'ctlggi_order_data_transient' );	
		// recreate transient included with the order ID
		$orderdata_trns = json_encode($orderdata);
		set_transient( 'ctlggi_order_data_transient', $orderdata_trns, 12 * HOUR_IN_SECONDS ); // for ... seconds
		
		$order_plugin_version = isset( $order_data['order_plugin_version'] ) ? sanitize_text_field( $order_data['order_plugin_version'] ) : '';
		$order_cus_user_id    = isset( $order_data['order_cus_user_id'] ) ? sanitize_text_field( $order_data['order_cus_user_id'] ) : '';
		$order_gateway        = isset( $order_data['order_gateway'] ) ? sanitize_text_field( $order_data['order_gateway'] ) : '';
		$order_key            = isset( $order_data['order_key'] ) ? sanitize_text_field( $order_data['order_key'] ) : '';
		$order_currency       = isset( $order_data['order_currency'] ) ? sanitize_text_field( $order_data['order_currency'] ) : '';
		$created_date         = isset( $order_data['created_date'] ) ? sanitize_text_field( $order_data['created_date'] ) : '';
		$order_date           = isset( $order_data['order_date'] ) ? sanitize_text_field( $order_data['order_date'] ) : '';
		$order_transaction_id = isset( $order_data['order_transaction_id'] ) ? sanitize_text_field( $order_data['order_transaction_id'] ) : '';
		$order_status         = isset( $order_data['order_status'] ) ? sanitize_text_field( $order_data['order_status'] ) : '';
		
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
		
		// get options
		$paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
		$show_billing_details   = $paypalstandard_options['ctlggi_paypalstandard_show_billing_details'];
		
		// Get the PayPal redirect uri
		//$paypal_redirect = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		//$paypal_redirect = trailingslashit( $paypal_redirect ) . '?';
		$paypal_redirect = trailingslashit( CTLGGI_PayPal_Standard::ctlggi_paypal_redirect_url() ) . '?';
		
		$page_style = CTLGGI_PayPal_Standard::ctlggi_paypal_page_style();
		
		$listener_url      = add_query_arg( 'ctlggi-listener', 'paypalipn', home_url( 'index.php' ) );
		$return_url        = add_query_arg( 'ctlggi-listener', 'paypalres', home_url( 'index.php' ) );  // go back to checkout page or success page if defined
		$cancel_return_url = add_query_arg( 'ctlggi-listener', 'paypalres', home_url( 'index.php' ) );

		// Setup PayPal arguments
		$paypal_args = array(
			'business'      => $paypalstandard_options['ctlggi_paypalstandard_email'],
			'email'         => $email, // customer
			'first_name'    => $first_name, // customer
			'last_name'     => $last_name, // customer
			'invoice'       => $order_key,
			'no_shipping'   => '1',
			'shipping'      => '0',
			'no_note'       => '1',
			'currency_code' => strtoupper($order_currency),
			'charset'       => get_bloginfo( 'charset' ),
			'custom'        => $post_id, // order id
			'rm'            => '2',
			'return'        => $return_url, 
			'cancel_return' => $cancel_return_url,
			'notify_url'    => $listener_url,
			'page_style'    => $page_style,
			'cbt'           => get_bloginfo( 'name' ),
			'bn'            => 'Cataloggi_SP'
		);
		
		
		if ( $show_billing_details == '1' && !empty($order_data['order_billing']) ) {
			$paypal_args['address1'] = $addr_1;
			$paypal_args['address2'] = $addr_2;
			$paypal_args['city']     = $city;
			$paypal_args['country']  = $country;
		}
		
		$paypal_extra_args = array(
			'cmd'      => '_cart',
			'tax_cart' => '0',
			'upload'   => '1'
		);

		$paypal_args = array_merge( $paypal_extra_args, $paypal_args );
		
		$i = 1;
		if( is_array( $order_data['order_items'] ) && ! empty( $order_data['order_items'] ) ) {
			foreach ( $order_data['order_items'] as $item ) {

				$paypal_args['item_name_' . $i ] = $item['item_name'];
				$paypal_args['quantity_' . $i ]  = $item['item_quantity'];
				$paypal_args['amount_' . $i ]    = $item['item_price']; // should be item price NOT item total

				$i++;

			}
		}
		
		// subscriptions and recurring payments use that
		//$paypal_args = apply_filters( 'ctlggi_paypal_args_before_redirect', $paypal_args, $order_data );
		$paypal_args = apply_filters( 'ctlggi_paypal_args_before_redirect', $paypal_args );

		// Build query
		$paypal_redirect .= http_build_query( $paypal_args );

		// Fix for some sites that encode the entities
		$paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );
		
		// test
		$paypal_args_test = json_encode($paypal_args);
		set_transient( 'ctlggi_paypal_data_transient_test', $paypal_args_test, 14400 ); // for ... seconds
		
		echo json_encode(array('checkoutsuccess'=>true, 'redirecturl'=>$paypal_redirect ));
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}
	
	/**
	 * PayPal pending reason.
	 *
	 * @since  1.0.0
	 * @return string $page_style
	 */
    public static function paypal_pending_reason( $pending_reason ) 
	{
		if ( empty( $pending_reason ) )
		return;
		
		$error_message = ''; // def
		
		if ( $pending_reason == 'address' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because your customer did not include a confirmed shipping address.', 'cataloggi' );
		} elseif ( $pending_reason == 'authorization' ) {
			$error_message =  __( 'PayPal IPN Pending Reason: You set the payment action to Authorization and have not yet captured funds.', 'cataloggi' );
		} elseif ( $pending_reason == 'delayed_disbursement' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The transaction has been approved and is currently awaiting funding from the bank.', 'cataloggi' );
		} elseif ( $pending_reason == 'echeck' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because it was made by an eCheck that has not yet cleared.', 'cataloggi' );
		} elseif ( $pending_reason == 'intl' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your account.', 'cataloggi' );
		} elseif ( $pending_reason == 'multi_currency' ) {
			$error_message = __( 'PayPal IPN Pending Reason: You do not have a balance in the currency sent, and you do not have your profiles Payment Receiving Preferences option set to automatically convert and accept this payment. As a result, you must manually accept or deny this payment.', 'cataloggi' );
		} elseif ( $pending_reason == 'order' ) {
			$error_message = __( 'PayPal IPN Pending Reason: You set the payment action to Order and have not yet captured funds.', 'cataloggi' );
		} elseif ( $pending_reason == 'paymentreview' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending while it is reviewed by PayPal for risk.', 'cataloggi' );
		} elseif ( $pending_reason == 'regulatory_review' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because PayPal is reviewing it for compliance with government regulations.', 'cataloggi' );
		} elseif ( $pending_reason == 'unilateral' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because it was made to an email address that is not yet registered or confirmed.', 'cataloggi' );
		} elseif ( $pending_reason == 'upgrade' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status before you can receive the funds.', 'cataloggi' );
		} elseif ( $pending_reason == 'verify' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.', 'cataloggi' );
		} elseif ( $pending_reason == 'other' ) {
			$error_message = __( 'PayPal IPN Pending Reason: The payment is pending for a reason other than those listed above. For more information, contact PayPal Customer Service.', 'cataloggi' );
		} else {
			$error_message = '';
		}
		
		return $error_message;
		
	}
	
	
}

?>