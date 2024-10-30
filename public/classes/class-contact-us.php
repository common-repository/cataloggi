<?php

/**
 * Contact Class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Contact {

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
	 * Process the contact form.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public function ctlggi_contact_form_process()
	{

		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-contact-form-nonce'], 'ctlggi_contact_form_nonce') )
	    {
			// sanitize form values
			$firstname      = isset( $postdata['ctlggi_firstname'] ) ? sanitize_text_field( $postdata['ctlggi_firstname'] ) : '';
			$lastname       = isset( $postdata['ctlggi_lastname'] ) ? sanitize_text_field( $postdata['ctlggi_lastname'] ) : '';
			$email          = sanitize_email( $postdata['ctlggi_email'] );
			$telephone      = sanitize_text_field( $postdata['ctlggi_telephone'] );
			$email_subject  = sanitize_text_field( $postdata['ctlggi_subject'] );
			$message        = wp_kses_post( $postdata['ctlggi_message'] );
			
			$full_name      = $firstname . ' ' . $lastname;
			
			// get site name
			// source: https://developer.wordpress.org/reference/functions/get_bloginfo/
			if ( trim( get_bloginfo('name') ) != false ) {
				$blog_name = get_bloginfo('name');
			} else {
				$blog_name = 'WordPress';
			}
			
			// get the blog administrator's email address
			$to = get_bloginfo('admin_email');
			
			$subject = esc_attr( $blog_name ) . " " . __( "Contact Form", "cataloggi" );
			
			$sender = "From: " . esc_attr( $full_name ) . " <" . esc_attr( $email ) . ">" . "\r\n";
			
			$emailbody = "\n";
			$emailbody .= __( "Date: ", "cataloggi" ) . " " . esc_attr( date( 'Y-m-d H:i:s' ) ) . " \n\n";
			$emailbody .= __( "Sent From: ", "cataloggi" ) . " " . esc_attr( $blog_name ) . __( " Contact Form ", "cataloggi" ) . " \n\n";	
			$emailbody .= __( "Firstname: ", "cataloggi" ) . " " . esc_attr( $firstname ) . " " .  __( "Lastname: ", "cataloggi" ) . esc_attr( $lastname ) . " \n\n";
			$emailbody .= __( "Email: ", "cataloggi" ) . " " . esc_attr( $email ) . " " .  __( "Phone: ", "cataloggi" ) . esc_attr( $telephone ) . " \n\n";
			$emailbody .= __( "Subject: ", "cataloggi" ) . " " . esc_attr( $email_subject ) . " \n\n";
			$emailbody .= __( "Message: ", "cataloggi" ) . " \n\n";
			$emailbody .= esc_textarea( $message ) . " \n\n";
			
			$emailbody .= __( "With Kind Regards,  ", "cataloggi" ) . " " . esc_attr( $blog_name ) .  " \n";
			$emailbody .= network_home_url( '/' ) .  " \n\n";
			
			$emailbody = stripslashes_deep( nl2br($emailbody) );
			
			$header = "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: text/html; charset=UTF-8\r\n";
			$header .= "Reply-To: " . $full_name . " <" . $email . ">" . "\r\n";
			$header .= $sender . "\r\n";
			
			$mail_errors = ''; // default
			
			// send email
			//$mail_errors = CTLGGI_Emailer::ctlggi_emailer_send_email( $to, $subject, $emailbody, $header ); 
			
			
			$emailer = array(
				'from_name'    => $blog_name,
				'from_email'   => $to,
				'reply_to'     => $email,	// user: $email	
				'subject'      => $subject,
				'message'      => $emailbody,
				'to_name'      => $blog_name,
				'to_email'     => $to,		
				'mime_version' => '1.0',
				'content_type' => 'text/html',
				'charset'      => 'UTF-8',
				'cc'           => '',
				'bcc'          => ''
			);
			
			$mail_errors = new CTLGGI_Emailer_Smtp( $emailer );
			
			
			// will return true only when $mail_errors = true
			if ( $mail_errors === true ) {
			    
				$error_message = $mail_errors;
				//$error_message = __( "An unexpected error occurred while sending email.", "cataloggi" );
                $print = CTLGGI_Validate::ctlggi_error_msg( $error_id='email_sending_error', $error_message );
				// return json
				echo json_encode(array('success'=>false, 'message'=>$print ));
			
			} else {
				
				$success_message = __( "Thank you! We have received your email.", "cataloggi" );
                $print = CTLGGI_Validate::ctlggi_success_msg( $success_id='email_sent_successfully', $success_message );
				echo json_encode(array('success'=>true, 'message'=>$print ));						

			}
			
		}
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}
	
	
	
}

?>