<?php

/**
 * Shop Payment Gateway PayPal Standard Buy Now class. 
 *
 * @package     cataloggi
 * @subpackage  Public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_PayPal_Standard_Buy_Now {

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
	 * Listens for PayPal IPN.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function buy_now_paypal_response() {
		// e.g. https://example.com/?ctlggi-listener=paypalres
		if ( isset( $_GET['ctlggi-listener'] ) && $_GET['ctlggi-listener'] == 'paypalbuynowres' ) { 
		
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
	public function buy_now_listen_paypal_ipn() {
		// e.g. https://example.com/?ctlggi-listener=paypalipn
		if ( isset( $_GET['ctlggi-listener'] ) && $_GET['ctlggi-listener'] == 'paypalbuynowipn' ) {
			
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
						
						if ( empty($orderdata['order_user_data']['email']) ) {
							// send order notif email to payer email
							$orderdata['order_user_data']['email'] = $payer_email; // guest payments
							update_post_meta( $order_id, '_email', $orderdata['order_user_data']['email'] ); // guest payments
						}
						
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
	 * Listens for PayPal IPN.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function buy_now_listen_paypal_ipn_example() {
		// e.g. https://example.com/?ctlggi-listener=paypalipn
		if ( isset( $_GET['ctlggi-listener'] ) && $_GET['ctlggi-listener'] == 'paypalbuynowipn' ) {
			
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
				
				$payment_status   = $_POST['payment_status']; // Completed, Pending, and Denied. 
				$order_id         = $_POST['custom']; // order id
				$txn_id           = $_POST['txn_id']; // transaction ID
				$business_email   = $_POST['business'];
				$payment_currency = $_POST['mc_currency'];
				$payment_date     = $_POST['payment_date'];
				$payer_email      = $_POST['payer_email'];
				$invoice          = $_POST['invoice']; // order key
				
				//$orderdata = get_transient( 'ctlggi_paypal_standard_buy_now_order_data_' . $invoice ); // json encoded
				$orderdata = CTLGGI_Single_Order::get_order_data_by_order_key( $order_key=$invoice );
				$orderdata = json_decode($orderdata, true); // convert to array
				
				if ( $invoice == $orderdata['order_key'] ) {
				
					// get options
					$ctlggi_currency_options = get_option('ctlggi_currency_options');
					$catalog_currency        = $ctlggi_currency_options['catalog_currency'];
					
					$current_date = date( 'Y-m-d H:i:s' );
					
					// PayPal statuses: Canceled_Reversal, Completed, Created, Denied, Expired, Failed, Pending, Refunded, Reversed, Processed, Voided
					if ( $payment_status == 'Completed' ) {
						
						// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
						$order_status = 'completed';

						// RECREATE NONCE, required for insert order data
						$new_nonce = wp_create_nonce('ctlggi_global_nonce_for_payment_forms');
						$orderdata['order_post_data']['ctlggi-global-nonce-for-payment-forms'] = $new_nonce;
						
						if ( empty($orderdata['order_user_data']['email']) ) {
							// send order notif email to payer email
							$orderdata['order_user_data']['email'] = $payer_email; // guest payments
						}
						
						$order_data = array(
							'order_id'               => '', // order id
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
							'order_post_data'        => $orderdata['order_post_data'], // array
						);
						$order_data = json_encode($order_data); // json encode before send
						
						// insert order data
						$post_id = CTLGGI_Process_Order::ctlggi_insert_order_data( $orderdata=$order_data );// returns post id
						
						if ( empty( $post_id ) ) {
							// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
							$order_status = 'failed';
							
							$error_id = 'insert_order_data_failed';
							$error_message = __('Failed to insert order data.', 'cataloggi');
							
						} else {
							
							// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
							$order_status = 'completed';
							
							$orderdata = json_decode($orderdata, true); // convert to array
							
							$order_data = array(
								'order_id'               => $post_id, // order id
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
								'order_post_data'        => $orderdata['order_post_data'], // array
							);
							$order_data = json_encode($order_data); // json encode before send
							
							// set transients
							set_transient( 'ctlggi_paypal_standard_buy_now_order_data_' . $invoice, $order_data, 12 * HOUR_IN_SECONDS ); // for ... seconds
							
							// SUCCESS
							$print = CTLGGI_Process_Order::ctlggi_order_processing_success( $post_id, $orderdata=$order_data, $order_status ); 
						}
						
					} elseif ( $payment_status == 'Pending' && isset( $_POST['pending_reason'] ) ) {
						
						$error_id = 'paypal_ipn_pending';
						$pending_reason = strtolower( $_POST['pending_reason'] ); // This variable is set only if payment_status is Pending.
						
						// return pending reasons
						$error_message = CTLGGI_PayPal_Standard::paypal_pending_reason( $pending_reason );
						$error_id = 'paypal_ipn_pending_payment';
						
					} else {
						// FAILED
						$error_id = 'paypal_ipn_transaction_failed';
						$error_message = __( 'PayPal IPN: Transaction failed.', 'cataloggi' );
					}
					
					// store data in transient
					delete_transient( 'ctlggi_paypal_ipn_res' );
					set_transient( 'ctlggi_paypal_ipn_res', $_POST, 14400 );
				
				} else {
					$error_id = 'paypal_ipn_listener_order_keys_not_match';
					$error_message = __('PayPal IPN listener order keys not match.', 'cataloggi');
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
			
			$this->response = $_POST;
			
		} else {
			return;
		}
	}
	
	/**
	 * Buy Now form.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function paypal_standard_buy_now_form_process() 
	{
		// defaults
		$response = '';
		$error_id = '';
		$error_message = '';
		$output_error = '';
		$user_id = '';
		$displayname = '';
		$post_id = '';
		
		// get form data
		$formData   = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
	
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {	
			// dates
			$currentdate = date("Y-m-d H:i:s"); $date = date("Y-m-d"); $time = date("H:i:s");
		
			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			
			$item_id            = isset( $postdata['ctlggi_item_id'] ) ? sanitize_text_field( $postdata['ctlggi_item_id'] ) : '';
			$item_price         = isset( $postdata['ctlggi_item_price'] ) ? sanitize_text_field( $postdata['ctlggi_item_price'] ) : '';
			$item_currency      = isset( $postdata['ctlggi_item_currency'] ) ? sanitize_text_field( $postdata['ctlggi_item_currency'] ) : ''; // e.g. gbp, usd
			$item_name          = isset( $postdata['ctlggi_item_name'] ) ? sanitize_text_field( $postdata['ctlggi_item_name'] ) : '';
			$item_quantity      = isset( $postdata['ctlggi_item_quantity'] ) ? sanitize_text_field( $postdata['ctlggi_item_quantity'] ) : '';
			$item_downloadable  = isset( $postdata['ctlggi_item_downloadable'] ) ? sanitize_text_field( $postdata['ctlggi_item_downloadable'] ) : '';
			$price_option_id    = isset( $postdata['ctlggi_price_options'] ) ? sanitize_text_field( $postdata['ctlggi_price_options'] ) : ''; // select field
			$default_gateway    = isset( $postdata['ctlggi_default_gateway'] ) ? sanitize_text_field( $postdata['ctlggi_default_gateway'] ) : ''; 
			$guest_payment      = isset( $postdata['ctlggi_guest_payment'] ) ? sanitize_text_field( $postdata['ctlggi_guest_payment'] ) : ''; 
			
			// only process if gateway is paypalstandard
			if ( $default_gateway == 'paypalstandard' ) {
				
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
				}
				
				// user data
				$first_name = isset( $first_name ) ? sanitize_text_field( $first_name ) : '';
				$last_name  = isset( $last_name ) ? sanitize_text_field( $last_name ) : '';
				$email      = isset( $email ) ? sanitize_text_field( $email ) : '';
				
				$user_data = array(
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'email'      => $email,
					'phone'      => '',
					'company'    => ''
				);
				
				if ( ! empty($displayname) ) {
					$displayname = trim($displayname);
				} else {
					$displayname = __( 'Guest', 'cataloggi' ); // for guest payments
				}
				
				//$plugin_version = sanitize_text_field( $this->version );
				$plugin_data = get_plugin_data( CTLGGI_PLUGIN_FILE ); // array
				$plugin_version = $plugin_data['Version'];			
				
				// generate order key
				$order_key = CTLGGI_Checkout::ctlggi_generate_order_key();
				
				// if "Private:" set on item name, remove
				$item_name = str_replace("Private:", "", $item_name);
				
				if( ! empty( $price_option_id ) ) {
					$price_option_id = $price_option_id;
					// get price option name
					$price_options = get_post_meta( $item_id, '_ctlggi_price_options', true ); // json  
					if ( ! empty( $price_options ) && $price_options != 'null' ) {	  
						$price_options = json_decode($price_options, true);// convert into array	
						$price_option_name = isset( $price_options[$price_option_id]['option_name'] ) ? sanitize_text_field( $price_options[$price_option_id]['option_name'] ) : ''; 
					}	
					
				} else {
					$price_option_id   = ''; 
					$price_option_name = ''; 
				}
				
				$item_total = $item_price * $item_quantity;
				
				// order totals
				$order_total = array(
					  'subtotal'  => $item_total,
					  'total'     => $item_total
				);
                
				// initialize empty cart items array
			    $order_items=array();
				// item data defaults
				$item_data = array(
				  'item_id'           => intval( $item_id ),
				  'item_price'        => sanitize_text_field( $item_price ),
				  'item_name'         => sanitize_text_field( $item_name ),
				  'item_quantity'     => $item_quantity,
				  'item_downloadable' => $item_downloadable,
				  'price_option_id'   => $price_option_id,
				  'price_option_name' => sanitize_text_field( $price_option_name ),
				  'item_total'        => $item_total,
				  'item_payment_type' => 'normal' // normal or subscription
				);

				$item_data = json_encode($item_data);
				// subscriptions use that
				$item_data = apply_filters( 'ctlggi_paypal_standard_buy_now_process_payment_filter', $item_data );
				$item_data = json_decode($item_data, true); // convert to array
				$order_items[]=$item_data;
				
				$order_status = 'pending_payment';
				$order_data = array(
					'order_id'               => '', // order id
					'created_date'           => $currentdate,
					'order_date'             => $currentdate,
					'order_status'           => $order_status, 
					'order_plugin_version'   => $plugin_version,
					'form_type'              => 'buy_now', // payment form type (paymentsform, checkoutform, buy_now)
					'order_access_token'     => '',
					'order_cus_user_id'      => $user_id, // if any
					'order_gateway'          => $default_gateway,
					'order_currency'         => $item_currency, // e.g. usd
					'order_key'              => $order_key,
					'order_transaction_id'   => '',
					'order_notes'            => '',
					'order_user_data'        => $user_data, // array
					'order_billing'          => '', // array
					'order_total'            => $order_total, // array
					'order_items'            => $order_items, // array
					'order_post_data'        => $postdata // array
				);
				
				// get domain name
				$domain = CTLGGI_Helper::ctlggi_site_domain_name();
				$order_key_cookie_name = CTLGGI_Cookies::ctlggi_order_key_cookie_name();
				// set cookie, expires in 1 day
				$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$order_key_cookie_name, $value=$order_key, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
				
				$orderdata = json_encode($order_data); // encode to json before send
				
				// insert order data
				$post_id = CTLGGI_Process_Order::ctlggi_insert_order_data( $orderdata );// returns post id
				
				if ( empty( $post_id ) ) {
					// ORDER STATUSES: 'completed', 'processing', 'pending_payment', 'failed', 'cancelled', 'refunded', 'on_hold'
					$order_status = 'failed';
					
					$error_id = 'insert_order_data_failed';
					$error_message = __('Failed to insert order data.', 'cataloggi');
					
					// error log saved in the cataloggi-uploads/_ctlggi-error-log.txt file.
					$print = CTLGGI_Process_Order::ctlggi_order_processing_error( $post_id, $orderdata, $order_status, $error_id, $error_message ); // output error message
					
					$response = json_encode(array('checkoutsuccess'=>false, 'message'=>$print ));
					
				} else {
					// PAYPAL PROCESS
					$this->paypal_standard_buy_now_process_payment( $post_id, $orderdata );		
				}
				
			}
		} else {
			// nonce failed
			$error_id = 'paypal_standard_buy_now_nonce_failed';
			$error_message = __('Nonce validation failed!', 'cataloggi');
			$print = CTLGGI_Validate::ctlggi_error_msg( $error_id, $error_message ); // output error msg
			$response = json_encode(array('checkoutsuccess'=>false, 'message'=>$print )); // output error message
		}
		
		// Payment Failed
		if ( ! empty($error_id) ) {
			 // save error in the error log
			 CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
			 echo $response; 
		}

	    #### important! #############
	    exit; // don't forget to exit!	
	}
	
	/**
	 * PayPal Standard Buy Now process payment.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function paypal_standard_buy_now_process_payment( $post_id, $orderdata ) 
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
		
		// delete transients
		//delete_transient( 'ctlggi_order_data_transient' );	
		// recreate transient included with the order ID
		//$orderdata_trns = json_encode($orderdata);
		// set custom transient for each of the orders so multiple purchases can be processed at the same time
		//set_transient( 'ctlggi_paypal_standard_buy_now_order_data_' . $order_data['order_key'], $orderdata_trns, 12 * HOUR_IN_SECONDS ); // for ... seconds
		
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
		
		$listener_url      = add_query_arg( 'ctlggi-listener', 'paypalbuynowipn', home_url( 'index.php' ) );
		$return_url        = add_query_arg( 'ctlggi-listener', 'paypalbuynowres', home_url( 'index.php' ) );  // go back to checkout page or success page if defined
		$cancel_return_url = add_query_arg( 'ctlggi-listener', 'paypalbuynowres', home_url( 'index.php' ) );

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
				
				$price_option_name = isset( $item['price_option_name'] ) ? sanitize_text_field( $item['price_option_name'] ) : '';

				$paypal_args['item_name_' . $i ] = $item['item_name'] . ' ' . $price_option_name;
				$paypal_args['quantity_' . $i ]  = $item['item_quantity'];
				$paypal_args['amount_' . $i ]    = $item['item_price']; // should be item price NOT item total

				$i++;

			}
		}
		
		// subscriptions and recurring payments use that
		$paypal_args = apply_filters( 'ctlggi_paypal_args_before_redirect', $paypal_args, $order_data );

		// Build query
		$paypal_redirect .= http_build_query( $paypal_args );

		// Fix for some sites that encode the entities
		$paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );
		
		echo json_encode(array('checkoutsuccess'=>true, 'redirecturl'=>$paypal_redirect ));
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}

	
}

?>