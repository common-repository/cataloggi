<?php

/**
 * Shopping Cart - Notification Emails class.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Notification_Emails {

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
	 * Html email sending function, read template file.
	 *
	 * @since  1.0.0
	 * @access protected static
	 * @param  string $FileName
	 * @return void $str
	 */
	protected static function readTemplateFile($FileName) 
	{	  
		$fp = fopen($FileName,"r") or exit("Unable to open File ".$FileName);
		$str = "";
		while(!feof($fp)) {
			$str .= fread($fp,1024);
		}	
		return $str;	
	}
	
	/**
	 * Resend order receipt form admin orders view.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @param  object $orderdata
	 * @return void
	 */
	public static function ctlggi_resend_order_receipt_from_admin( $post_id, $orderdata ) 
	{
		if ( empty( $post_id ) )
		    return; 
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
			return;
			
		$orderdata   = json_decode($orderdata, true); // convert to array
		//$order_data_obj = json_decode($order_data); // convert to object
		
		$order_data = array(
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'], // array
			'order_plugin_version'   => $orderdata['order_plugin_version'],
			'order_cus_user_id'      => $orderdata['order_cus_user_id'],
			'order_gateway'          => $orderdata['order_gateway'],
			'order_key'              => $orderdata['order_key'],
			'order_currency'         => $orderdata['order_currency'],
			'order_date'             => $orderdata['order_date'],
			'order_transaction_id'   => $orderdata['order_transaction_id'],
			'order_status'           => $orderdata['order_status']
		);
		
		$order_data = json_encode($order_data); // encode to json before send
		
		// software licensing using this
		do_action( 'ctlggi_resend_order_receipt_from_admin_before', $post_id, $order_data ); // <- extensible 
		
		// send order receipt
		CTLGGI_Notification_Emails::ctlggi_send_order_receipt( $post_id, $order_data );
		
	}
	
	/**
	 * Resend Payment Pequest form admin orders view.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @param  object $orderdata
	 * @return void
	 */
	public static function ctlggi_resend_payment_request_from_admin( $post_id, $orderdata ) 
	{
		if ( empty( $post_id ) )
		    return; 
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
			return;
			
		$orderdata   = json_decode($orderdata, true); // convert to array
		//$order_data_obj = json_decode($order_data); // convert to object
		
		$order_data = array(
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'], // array
			'order_plugin_version'   => $orderdata['order_plugin_version'],
			'order_cus_user_id'      => $orderdata['order_cus_user_id'],
			'order_gateway'          => $orderdata['order_gateway'],
			'order_key'              => $orderdata['order_key'],
			'order_currency'         => $orderdata['order_currency'],
			'order_date'             => $orderdata['order_date'],
			'order_transaction_id'   => $orderdata['order_transaction_id'],
			'order_access_token'     => $orderdata['order_access_token'],
			'order_status'           => $orderdata['order_status']
		);
		
		$order_data = json_encode($order_data); // encode to json before send 
		
		// software licensing using this
		// to-do set up on software licensing
		do_action( 'ctlggi_resend_payment_request_from_admin_before', $post_id, $order_data ); // <- extensible 
		
		// send payment request
		CTLGGI_Notification_Emails::ctlggi_send_payment_request( $post_id, $order_data );
		
	}
	
	/**
	 * Send Payment Pequest to customer.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @param  object $order_data
	 * @return void
	 */
	private static function ctlggi_send_payment_request( $post_id, $order_data ) {
	    
	    if ( empty( $post_id ) )
	    return;
		
		$order_data  = json_decode($order_data, true); // convert to array
		
		// order data
		$user_email  = $order_data['order_user_data']['email'];
		
	    $user_email  = strtolower( $user_email );
	  
		// get options
		$payment_requests_options = get_option('ctlggi_payment_requests_options');
		
		$from_name      = isset( $payment_requests_options['from_name'] ) ? sanitize_text_field( $payment_requests_options['from_name'] ) : '';		
		$from_email     = isset( $payment_requests_options['from_email'] ) ? sanitize_email( $payment_requests_options['from_email'] ) : '';	
		
		$subject       = $payment_requests_options['subject'];
		$email_content = $payment_requests_options['email_content'];
		
		$order_data = json_encode($order_data); // encode to json before send
		$emailbody = CTLGGI_Notification_Emails::ctlggi_orders_email_template( $post_id, $order_data, $email_content );
        
		$sender = "From: ". sanitize_text_field( $from_name ) ." <". sanitize_text_field( $from_email ) .">" . "\r\n";
		
		// write the email content
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
	
		$to_email = sanitize_email( $user_email );
		
		// send email
		//CTLGGI_Notification_Emails::ctlggi_orders_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => $from_name,
			'from_email'   => $from_email,	
			'reply_to'     => $from_email,
			'subject'      => $subject,
			'message'      => $emailbody,
			'to_name'      => '',
			'to_email'     => $to_email,		
			'mime_version' => '1.0',
			'content_type' => 'text/html',
			'charset'      => 'UTF-8',
			'cc'           => '',
			'bcc'          => ''
		);
		
		$mail_errors = new CTLGGI_Emailer_Smtp( $emailer );
	
	}
	
	/**
	 * Send Notification Emails Upon any Sales. Data From : CTLGGI_Process_Order Class
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @param  object $orderdata
	 * @param  string $order_status
	 * @return void
	 */
	public static function ctlggi_order_data_for_emails( $post_id, $orderdata, $order_status ) 
	{
		if ( empty( $post_id ) )
		return;
		
		$order_transaction_id    = get_post_meta( $post_id, '_order_transaction_id', true );
		if( empty( $order_transaction_id ) ) $order_transaction_id = '';
		
		$orderdata   = json_decode($orderdata, true); // convert to array
		
		$order_data = array(
			'order_post_data'        => $orderdata['order_post_data'], // array
			'order_user_data'        => $orderdata['order_user_data'], // array
			'order_billing'          => $orderdata['order_billing'], // array
			'order_total'            => $orderdata['order_total'], // array
			'order_items'            => $orderdata['order_items'], // array
			'order_plugin_version'   => $orderdata['order_plugin_version'],
			'order_cus_user_id'      => $orderdata['order_cus_user_id'],
			'order_gateway'          => $orderdata['order_gateway'],
			'order_key'              => $orderdata['order_key'],
			'order_currency'         => $orderdata['order_currency'],
			'order_date'             => $orderdata['order_date'],
			'order_transaction_id'   => $order_transaction_id,
			'order_status'           => $order_status
		);
		
		$order_data = json_encode($order_data); // encode to json before send
		
		// send order receipt
		CTLGGI_Notification_Emails::ctlggi_send_order_receipt( $post_id, $order_data );
		// send order sale notification
		CTLGGI_Notification_Emails::ctlggi_send_order_sale_notification( $post_id, $order_data );
		
	}

	/**
	 * Process email sending.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  string $to
	 * @param  string $subject
	 * @param  string $emailbody
	 * @param  string $header
	 * @return bool $email_errors
	 */
	public static function ctlggi_orders_send_email( $to, $subject, $emailbody, $header ) {
		$email_errors = false;
		// send the email using wp_mail()
		if( !wp_mail($to, $subject, $emailbody, $header) ) {
			$send_email_errors = true;
		}
		return $email_errors;
	}

	/**
	 * Send Order Receipt to customer.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @param  object $order_data
	 * @return void
	 */
	private static function ctlggi_send_order_receipt( $post_id, $order_data ) {
	    
	    if ( empty( $post_id ) )
	    return;
		
		$order_data  = json_decode($order_data, true); // convert to array
		
		// order data
		$user_email  = $order_data['order_user_data']['email'];
		
	    $user_email  = strtolower( $user_email );
	  
		// get options
		$order_receipts_options = get_option('ctlggi_order_receipts_options');
		
		$from_name      = isset( $order_receipts_options['from_name'] ) ? sanitize_text_field( $order_receipts_options['from_name'] ) : '';		
		$from_email     = isset( $order_receipts_options['from_email'] ) ? sanitize_email( $order_receipts_options['from_email'] ) : '';	
		$subject       = $order_receipts_options['subject'];
		$email_content = $order_receipts_options['email_content'];
		
		$order_data = json_encode($order_data); // encode to json before send
		$emailbody = CTLGGI_Notification_Emails::ctlggi_orders_email_template( $post_id, $order_data, $email_content );
        
		$sender = "From: ". sanitize_text_field( $from_name ) ." <". sanitize_text_field( $from_email ) .">" . "\r\n";
		
		// write the email content
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
	
		$to_email = sanitize_email( $user_email );
		
		// send email
		//CTLGGI_Notification_Emails::ctlggi_orders_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => $from_name,
			'from_email'   => $from_email,	
			'reply_to'     => $from_email,
			'subject'      => $subject,
			'message'      => $emailbody,
			'to_name'      => '',
			'to_email'     => $to_email,		
			'mime_version' => '1.0',
			'content_type' => 'text/html',
			'charset'      => 'UTF-8',
			'cc'           => '',
			'bcc'          => ''
		);
		
		$mail_errors = new CTLGGI_Emailer_Smtp( $emailer );
	
	}

	/**
	 * Send Order Notification email to admin.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @param  object $order_data
	 * @return void
	 */
	private static function ctlggi_send_order_sale_notification( $post_id, $order_data ) {
	  
	    if ( empty( $post_id ) )
	    return;
		
		$order_data   = json_decode($order_data, true); // convert to array
	  
		// get options
		$order_notifications_options = get_option('ctlggi_order_notifications_options');
		
		$notifications_enabled   = $order_notifications_options['notifications_enabled'];
		$send_to                 = $order_notifications_options['send_to']; // array
		$subject                 = $order_notifications_options['subject'];
		$email_content           = $order_notifications_options['email_content'];
		
	    if ( $notifications_enabled != '1' )
	    return;
		
		$order_data = json_encode($order_data); // encode to json before send
		$emailbody = CTLGGI_Notification_Emails::ctlggi_orders_email_template( $post_id, $order_data, $email_content );
        
		// get options
		$order_receipts_options = get_option('ctlggi_order_receipts_options');
		
		$from_name      = isset( $order_receipts_options['from_name'] ) ? sanitize_text_field( $order_receipts_options['from_name'] ) : '';		
		$from_email     = isset( $order_receipts_options['from_email'] ) ? sanitize_email( $order_receipts_options['from_email'] ) : '';	
		
		$sender = "From: ". sanitize_text_field( $from_name ) ." <". sanitize_text_field( $from_email ) .">" . "\r\n";
		
		// write the email content
		$header = '';
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
		
		if ( !empty($send_to) ) {
			$send_to = str_replace(" ","",$send_to); // replace white spaces
			$send_to_array = explode(',', $send_to); // create array, explode by comma
			$to = $send_to_array;
			// cc for phpmailer
			foreach($send_to_array as $email) {
				$cc[] = array(
					'email'  => $email,
					'name'   => ''
				);
			}
			//unset($cc[0]); // unset the first email from array
			$cc_first_email = $send_to_array['0'];
		} else {
			$cc = '';
		}
		
		$site_name = get_bloginfo('name');
		$to_admin  = get_bloginfo('admin_email');
		
		// send email
		//CTLGGI_Notification_Emails::ctlggi_orders_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => $from_name,
			'from_email'   => $from_email,		
			'reply_to'     => $from_email,
			'subject'      => $subject,
			'message'      => $emailbody,
			'to_name'      => $site_name,
			'to_email'     => $to_admin,		
			'mime_version' => '1.0',
			'content_type' => 'text/html',
			'charset'      => 'UTF-8',
			'cc'           => $cc,
			'bcc'          => ''
		);
		
		$mail_errors = new CTLGGI_Emailer_Smtp( $emailer );
	
	}

	/**
	 * Orders email template.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @param  object $order_data
	 * @param  string $email_content
	 * @return string $emailBody
	 */
	private static function ctlggi_orders_email_template( $post_id, $order_data, $email_content ) {
		
	    if ( empty( $post_id ) || empty( $order_data ) || empty( $email_content ) )
	    return;
		
		$order_data   = json_decode($order_data, true); // convert to array
		
		$order_date      = sanitize_text_field( $order_data['order_date'] );
		$order_status    = sanitize_text_field( $order_data['order_status'] );
		$transaction_id  = isset( $order_data['order_transaction_id'] ) ? sanitize_text_field( $order_data['order_transaction_id'] ) : '';
        // it is for payment requests
		$access_token    = isset( $order_data['order_access_token'] ) ? sanitize_text_field( $order_data['order_access_token'] ) : '';
		$order_gateway   = sanitize_text_field( $order_data['order_gateway'] );
		
		$order_subtotal  = sanitize_text_field( $order_data['order_total']['subtotal'] );
		$order_total     = sanitize_text_field( $order_data['order_total']['total'] );
		$order_currency  = sanitize_text_field( $order_data['order_currency'] );
		$first_name = isset( $order_data['order_user_data']['first_name'] ) ? sanitize_text_field( $order_data['order_user_data']['first_name'] ) : '';
		$last_name  = isset( $order_data['order_user_data']['last_name'] ) ? sanitize_text_field( $order_data['order_user_data']['last_name'] ) : '';
		$email      = isset( $order_data['order_user_data']['email'] ) ? sanitize_email( $order_data['order_user_data']['email'] ) : '';
		$phone      = isset( $order_data['order_user_data']['phone'] ) ? sanitize_text_field( $order_data['order_user_data']['phone'] ) : '';
		$company    = isset( $order_data['order_user_data']['company'] ) ? sanitize_text_field( $order_data['order_user_data']['company'] ) : '';
		
		$billing_country  = isset( $order_data['order_billing']['billing_country'] ) ? sanitize_text_field( $order_data['order_billing']['billing_country'] ) : '';
		$billing_city     = isset( $order_data['order_billing']['billing_city'] ) ? sanitize_text_field( $order_data['order_billing']['billing_city'] ) : '';
		$billing_state    = isset( $order_data['order_billing']['billing_state'] ) ? sanitize_text_field( $order_data['order_billing']['billing_state'] ) : '';
		$billing_addr_1   = isset( $order_data['order_billing']['billing_addr_1'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_1'] ) : '';
		$billing_addr_2   = isset( $order_data['order_billing']['billing_addr_2'] ) ? sanitize_text_field( $order_data['order_billing']['billing_addr_2'] ) : '';
		$billing_zip      = isset( $order_data['order_billing']['billing_zip'] ) ? sanitize_text_field( $order_data['order_billing']['billing_zip'] ) : '';
		
		// extra notes textarea on checkout
		$cus_extra_notes = isset( $order_data['order_post_data']['cus_extra_notes'] ) ? wp_kses_post( $order_data['order_post_data']['cus_extra_notes'] ) : '';
		$cus_extra_notes = wp_strip_all_tags( stripslashes( $cus_extra_notes ) );
		
		$order_key       = sanitize_text_field( $order_data['order_key'] );
		
		if ( ! empty($billing_country) ) {
		    // Countries
		    $countries    = CTLGGI_Countries::ctlggi_country_list(); // function
		    $country_name = $countries[$billing_country]; // get country name
		} else {
			$country_name = '';
		}
		
        // gateway options data
		$payment_gateway_data = CTLGGI_Notification_Emails::ctlggi_email_gateway_options_data($order_gateway);
		$payment_gateway_data = wp_kses_post( $payment_gateway_data );
		
		$email = strtolower( $email );
			
		// get options
		$email_settings_options = get_option('ctlggi_email_settings_options');
		
		$emails_logo = sanitize_text_field( $email_settings_options['emails_logo'] );
		if ( !empty($emails_logo) ) {
		   $logo_image = '<img class="responsive-image" style="margin-top:10px; height: 50px;" border="0" src="' . esc_url( $emails_logo ) . '" />';
		} else {
		  $logo_image = '';	
		}
		
		// get options
		$order_receipts_options = get_option('ctlggi_order_receipts_options');
		
		$fromname      = sanitize_text_field( $order_receipts_options['from_name'] );
		$fromemail     = sanitize_text_field( $order_receipts_options['from_email'] );
		
		$items = CTLGGI_Notification_Emails::ctlggi_email_ordered_items($post_id);
		
		$order_total = CTLGGI_Amount::ctlggi_format_amount($amount=$order_total);
		
		// get the currency symbol
		$order_currency_symbol = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $currency=$order_currency );
		$order_total = CTLGGI_Amount::ctlggi_orders_currency_symbol_position($amount=$order_total, $order_currency_symbol); 
		
		$order_status_name = ''; // default
		$order_statuses    = CTLGGI_Custom_Post_Statuses::ctlggi_order_custom_post_statuses();
		$order_status_name = $order_statuses[$order_status]; // order status value
		
		if ( empty($first_name) ) {
			$first_name = __('Customer', 'cataloggi'); // guest payments
		}
		// replace
		$email_content = str_replace("[user_first_name]",esc_attr( $first_name ),$email_content); // user first name
		$email_content = str_replace("[user_last_name]",esc_attr( $last_name ),$email_content); // user last name
		$email_content = str_replace("[user_email]",esc_attr( $email ),$email_content); // user email
		$email_content = str_replace("[user_phone]",esc_attr( $phone ),$email_content); // user phone
		$email_content = str_replace("[user_company]",esc_attr( $company ),$email_content); // user company
		
		$email_content = str_replace("[billing_country]",esc_attr( $country_name ),$email_content);
		$email_content = str_replace("[billing_city]",esc_attr( $billing_city ),$email_content);
		$email_content = str_replace("[billing_state]",esc_attr( $billing_state ),$email_content);
		$email_content = str_replace("[billing_addr_1]",esc_attr( $billing_addr_1 ),$email_content);
		$email_content = str_replace("[billing_addr_2]",esc_attr( $billing_addr_2 ),$email_content);
		$email_content = str_replace("[billing_zip]",esc_attr( $billing_zip ),$email_content);	
		
		if ( !empty($cus_extra_notes) ) {
		    $email_content = str_replace("[customer_extra_notes]",esc_attr( $cus_extra_notes ),$email_content);	// customer extra notes
		}
		
		$email_content = str_replace("[items]",$items,$email_content); // LIST, purchased items list
		$email_content = str_replace("[order_total]",$order_total,$email_content); // SPAN, ordered items total
		
		$email_content = str_replace("[order_status]",esc_attr( $order_status_name ),$email_content);
		
		$email_content = str_replace("[from_name]",esc_attr( $fromname ),$email_content); // email sent, from name
		$email_content = str_replace("[from_email]",esc_attr( $fromemail ),$email_content); // email sent, from email

		$email_content = str_replace("[order_id]",esc_attr( $post_id ),$email_content); // order id
		
		$email_content = str_replace("[transaction_id]",esc_attr( $transaction_id ),$email_content); // transaction id
		$email_content = str_replace("[order_key]",esc_attr( $order_key ),$email_content); // order key is the license key for digital goods
		
		$order_date_f = CTLGGI_Helper::formatDate( $date=$order_date ); // for datetime: formatDateTime or date: formatDate
		$email_content = str_replace("[order_date]",esc_attr( $order_date_f ),$email_content); // order date
		
		$email_content = str_replace("[payment_gateway]",esc_attr( $order_gateway ),$email_content);
		
		$email_content = str_replace("[payment_gateway_data]",$payment_gateway_data,$email_content); // gateway title (name), gateway notes, bank account details (only for BACS)
		
		$current_site_url = home_url();
		$email_content = str_replace("[current_site_url]",esc_attr( $current_site_url ),$email_content);
		
		$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
		$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';
		
		// CREATE PAYMENT LINK
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		$page_id = $ctlggi_cart_options['payments_page'];
		// check if value is not 0 
		if ( $page_id != '0' && ! empty($page_id) ) {
			$base_url = get_permalink( $page_id ); // e.g. https://example.com/payments/
			$payment_request_link = $base_url . '?page=payments&id=' . $post_id . '&token=' . $access_token; // post_id is the order id
		} else {
			// no page found
			$payment_request_link = $cataloggiurl . '?page=payments&id=' . $post_id . '&token=' . $access_token; // post_id is the order id
		}
		
		$payment_request_button = '<a style="padding-left:12px; padding-right:12px; padding-top:6px; padding-bottom:6px;margin:0px;background-color:#28A3D7;border-radius:4px; text-decoration:none; color:#fff; font-size:8pt; font-weight:bold;" href="' . esc_attr( $payment_request_link ) . '">' . __('Pay Now', 'cataloggi') . '</a>';

		// payment request tags
		$email_content = str_replace("[payment_request_link]",esc_attr( $payment_request_link ),$email_content);
		$email_content = str_replace("[payment_request_button]",$payment_request_button,$email_content);
		
		// software licensing using this
		$email_content = apply_filters( 'ctlggi_orders_email_template_extend_template_tags', $email_content ); // <- extensible 
		
		// should be placed after all $email_content
		$email_content = str_replace("\n\n\n\n",'\n\n',$email_content); // fix
		$email_content = stripslashes_deep( nl2br($email_content) );

		# read themplate file
		$themeUrl = CTLGGI_PLUGIN_DIR . 'public/html-emails/default/emails-default-template.php';
		$emailBody = CTLGGI_Notification_Emails::readTemplateFile( $themeUrl );
		
		# theme replace
		$emailBody = str_replace("[logo_image]",$logo_image,$emailBody); // logo
		$emailBody = str_replace("[email_content]",$email_content,$emailBody);
		
		return $emailBody; // HTML email template
		
	}

	/**
	 * Orders payment gateway data.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  array $order_gateway
	 * @return string $output
	 */
	private static function ctlggi_email_gateway_options_data($order_gateway) {
		
		$output = ''; // default
		
		$payment_gateways   = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();// gateways
		
		if( ! empty( $order_gateway ) ) {	
			foreach($payment_gateways as $gateway => $value )
			{  
			   // get the selected gateway
			   if ( $order_gateway == $gateway ) 
			   {
					$payment_gateway_label = sanitize_text_field( $value['payment_gateway_label'] );
					$payment_gateway_name  = sanitize_text_field( $value['payment_gateway_name'] );
			   }
			}
		} else {
			// error, no gateway selected
			$payment_gateway_label = '';
			$payment_gateway_name  = '';
		}
		
		// check if option exist
		if( get_option('ctlggi_gateway_' . $order_gateway . '_options') ){
			
			$gateway_options = get_option('ctlggi_gateway_' . $order_gateway . '_options');
			
			// if gateway title exist
			if ( !empty($gateway_options['ctlggi_' . $order_gateway . '_title']) ) {
				$gateway_title = $gateway_options['ctlggi_' . $order_gateway . '_title'];
				$output .= '<strong>' . __( 'Payment Gateway: ', 'cataloggi' ) . '</strong>' . esc_attr( $payment_gateway_label ) . "\n\n";
			}
			
			// if gateway notes exist
			if ( !empty($gateway_options['ctlggi_' . $order_gateway . '_notes']) ) {
				$gateway_notes = $gateway_options['ctlggi_' . $order_gateway . '_notes'];
				$output .= '<strong>' . __( 'Notes: ', 'cataloggi' ) . '</strong>' . wp_strip_all_tags( $gateway_notes ) . "\n\n";
			}
			
			// if gateway bank account details exist
			if ( !empty($gateway_options['ctlggi_' . $order_gateway . '_bank_account_details']) ) {
				$gateway_bank_account_details = $gateway_options['ctlggi_' . $order_gateway . '_bank_account_details'];
				$output .= '<strong>' . __( 'Bank Account Details: ', 'cataloggi' ) . '</strong> ' . " \n";
				$output .= wp_strip_all_tags( $gateway_bank_account_details ) . " \n"; // wp_strip_all_tags() removes HTML tags
			}
			
			do_action( 'ctlggi_email_gateway_options_data' ); // <- extensible
			
			return $output;
			
		} else {
			return;
		}
		
	}

	/**
	 * Ordered items.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @return string $output
	 */
	private static function ctlggi_email_ordered_items($post_id) {
		
		if ( empty( $post_id ) )
		return;
		
		$output = ''; // default
		
		$order_id = $post_id;
		
		$orderdata = CTLGGI_Single_Order::ctlggi_get_single_order_data_by_order_id( $order_id );
		$orderdata = json_decode($orderdata, true); // convert to array
		
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

			$download_data = ''; // default
			$site_home_url = home_url();
			// send download file(s) data only if order status = completed
			if ( $order_status == 'completed' ) {

				// check again if file is downloadable
				if ( $item_downloadable == '1' ) {
					// get file data, downloadable items
					$item_file_name        = get_post_meta( $item_id, '_ctlggi_item_file_name', true );
					$item_file_url         = get_post_meta( $item_id, '_ctlggi_item_file_url', true );
					$item_download_limit   = get_post_meta( $item_id, '_ctlggi_item_download_limit', true );
					$item_download_expiry  = get_post_meta( $item_id, '_ctlggi_item_download_expiry', true );
					
					// download expiry date
					$current_date = date('Y-m-d H:i:s');
					// order data : order date
					$order_date = get_post_meta( $order_id, '_order_date', true );
					// order date + item download expiry 
					$add_days = strtotime(date("Y-m-d H:i:s", strtotime($order_date)) . "+" . $item_download_expiry . " days");
					$download_expiry_date = date('Y-m-d H:i:s', $add_days);
					
					if ( $item_download_limit == '' ) {
						// unlimited downloads
						$item_download_limit = __( 'Unlimited', 'cataloggi' );
					}
					
					$check_expiry_date = strtotime($download_expiry_date);
					if ( $check_expiry_date == '0' ) {
						$download_expiry_date = __( 'Never Expires', 'cataloggi' );
					} else {
						$download_expiry_date = CTLGGI_Helper::formatDate( $date=$download_expiry_date ); // format date
					}
					
					// downloadable products create download url
					$secret_data = array(
						'post_id'    => intval( $item_id ), // db posts, item id is the post id !!!
						'order_id'   => intval( $order_id ), // db posts
						'order_key'  => sanitize_text_field( $order_key )
					);
					
					// convert array to json
					$secret_data_json = json_encode( $secret_data );
					$secret_data_json_enc = CTLGGI_Helper::ctlggi_base64url_encode($data=$secret_data_json);	
					
					//$download_link = home_url() . '/ctlggi-file-dw-api/?action=download&dwfile=' . $secret_data_json_enc;
					$file_link = '<a href="' . esc_url( $site_home_url . '/ctlggi-file-dw-api/?action=download&dwfile=' . $secret_data_json_enc ) 
					. '">' . esc_attr( $item_file_name ) . '</a>';
					
					$file_download_urls[] = $file_link; // save in array for later usage
					
					// download file data
					$download_data = ""; // " \n"
					$download_data .= __( 'Download Limit: ', 'cataloggi' ) . esc_attr( $item_download_limit ) . " \n";
					$download_data .= __( 'Download Expiry Date: ', 'cataloggi' ) . esc_attr( $download_expiry_date ) . " \n";	
					$download_data .= __( 'Download File: ', 'cataloggi' ) . $file_link . " \n";
					
				}
			
			}
			
			// Order Item Data
			$output .= '<strong>' . $item_name . '</strong>' . " \n";
			// price option ame
			if ( ! empty ($price_option_name) && $price_option_name != 'null' )  {
				$output .= '' . $price_option_name . '' . " \n";
			}
			
			// make sure it is a subscription before display the data
			if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
				$itemdata = json_encode($value); // value from foreach
				// subscription using this
				$display_subsc_details_public = apply_filters( 'ctlggi_notification_emails_ordered_items_filter', $itemdata ); // <- extensible 
			} else {
				$display_subsc_details_public = '';
			}
			
			$output .=  $download_data; 
			
			// Order Item Meta Data
			//$output .= __( 'Item ID: ', 'cataloggi' ) . intval( $item_id ) . " \n";
			$output .= __( 'Price: ', 'cataloggi' ) . sanitize_text_field( $item_price_public ) . " \n";
			$output .= __( 'Quantity: ', 'cataloggi' ) . intval( $item_quantity ) . " \n";
			$output .= __( 'Total: ', 'cataloggi' ) . sanitize_text_field( $item_price_public_total ) . $display_subsc_details_public . " \n";
			$output .= " \n";
		}
		
		return $output;
	
	}

	/**
	 * Downloadable items.
	 *
	 * @to-do This Method is not in use.
	 *
	 * @since  1.0.0
	 * @access private static
	 * @param  int $post_id
	 * @return NONE
	 */
	private function ctlggi_email_downloadable_items($post_id) {
	   // downloadable products ARRAY
	   $downloadable_items = CTLGGI_Downloadable_Products::ctlggi_order_get_downloadable_items( $post_id );
	   
	   $file_download_urls = array(); // create empty array
	   $site_home_url = home_url();
	   $items_order_id = $post_id;
	   
	   $order_key = get_post_meta( $post_id, '_order_key', true ); 
	   
	    // if there are downloadable products insert download link into the email invoice
		if ( $downloadable_items ) {
			// Using foreach loop without key
			foreach($downloadable_items as $meta_key => $meta_value) {
				
				foreach($meta_value as $key => $value )
				{
				   //echo 'Post ID: ' . $value['post_id'] . ' Meta Key: ' . $value['meta_key'] . ' Meta Value: ' . $value['meta_value'] . '<br>';
				   
				   // get file data based on the file name
				   if ( $value['meta_key'] == '_ctlggi_item_file_name' ) {
					   
						$item_file_name = $value['meta_value'];
						
						$item_post_id = $value['post_id'];
						
						$secret_data = array(
							'post_id'    => intval( $item_post_id ),
							'order_id'   => intval( $items_order_id ),
							'order_key'  => sanitize_text_field( $order_key )
						);
						
						// convert array to json
						$secret_data_json = json_encode( $secret_data );
						$secret_data_json_enc = CTLGGI_Helper::ctlggi_base64url_encode($data=$secret_data_json);	
						
						//$download_link = home_url() . '/ctlggi-file-dw-api/?action=download&dwfile=' . $secret_data_json_enc;
						$file_link = '<a href="' . esc_url( $site_home_url . '/ctlggi-file-dw-api/?action=download&dwfile=' . $secret_data_json_enc ) . '">' . esc_attr(  $item_file_name ) . '</a>';
						
						$file_download_urls[] = $file_link;
				   }
				   
				   // get meta: file url
				   if ( $value['meta_key'] == '_ctlggi_item_file_url' ) {
						//echo 'File URL: ' . $item_file_url = $value['meta_value'] . '<br>';
				   }
				   
				   /*
				   // get meta: Download Limit
				   if ( $value['meta_key'] == '_ctlggi_item_download_limit' ) {
						echo 'Download Limit: ' . $item_download_limit = $value['meta_value'] . '<br>';
				   }
				   
				   // get meta: Download Expiry
				   if ( $value['meta_key'] == '_ctlggi_item_download_expiry' ) {
						echo 'Download Expiry: ' . $item_download_expiry = $value['meta_value'] . '<br>';
				   }
				   */
				   
				}

			}	
		}
		
		if ( !empty($file_download_urls) ) {
			$email_content = 'Please click on the following link(s) to download your files. <br><br>';
			$email_content .= '[xdownloadsx]';
		} else {
			// remove download lines
		}
		
	}
	
}

?>