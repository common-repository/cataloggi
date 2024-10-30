<?php

/**
 * SMTP Emailer class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2017, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Emailer_Smtp {
	
    private $from_name;
	private $from_email;
	private $reply_to;
	private $subject;
	private $message;
	private $to_name;
	private $to_email;
	private $mime_version;
	private $content_type;
	private $charset;
	private $cc;
	private $bcc;
	
	private $enable_smtp;
	private $smtp_host;
	private $smtp_auth;
	private $smtp_username;
	private $smtp_password;
	private $type_of_encryption;
	private $smtp_port;
	
	private $phpmailer;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $emailer = null ) {
			
		$from_name      = isset( $emailer['from_name'] ) ? sanitize_text_field( $emailer['from_name'] ) : '';		
		$from_email     = isset( $emailer['from_email'] ) ? sanitize_email( $emailer['from_email'] ) : '';	
		$reply_to       = isset( $emailer['reply_to'] ) ? sanitize_email( $emailer['reply_to'] ) : '';
		$subject        = isset( $emailer['subject'] ) ? sanitize_text_field( $emailer['subject'] ) : '';
		$message        = isset( $emailer['message'] ) ? $emailer['message'] : '';
		$to_name        = isset( $emailer['to_name'] ) ? sanitize_text_field( $emailer['to_name'] ) : '';
		$to_email       = isset( $emailer['to_email'] ) ? sanitize_email( $emailer['to_email'] ) : '';
		$mime_version   = isset( $emailer['mime_version'] ) ? sanitize_text_field( $emailer['mime_version'] ) : '';
		$content_type   = isset( $emailer['content_type'] ) ? sanitize_text_field( $emailer['content_type'] ) : '';
		$charset        = isset( $emailer['charset'] ) ? sanitize_text_field( $emailer['charset'] ) : '';
		
		// if from email and name specified
		if ( ! empty($from_name) ) {
			$this->from_name = $from_name;
		}		
		if ( ! empty($from_email) ) {
			$this->from_email = $from_email;
		}
	
	    $this->reply_to      = $reply_to;
		$this->subject       = $subject;
		$this->message       = $message;
		$this->to_name       = $to_name;
		$this->to_email      = $to_email;
		$this->mime_version  = $mime_version;
		$this->content_type  = $content_type;
		$this->charset       = $charset;
		$this->cc            = $emailer['cc']; // array
		$this->bcc           = $emailer['bcc']; // array
		
		$this->settings();
        $this->send_email();

	}
	
	public function settings() {	
	
		$email_options = get_option('ctlggi_email_settings_options');
		
		$enable_smtp           = isset( $email_options['enable_smtp'] ) ? sanitize_text_field( $email_options['enable_smtp'] ) : '';
		$smtp_host             = isset( $email_options['smtp_host'] ) ? sanitize_text_field( $email_options['smtp_host'] ) : '';
		$smtp_auth             = isset( $email_options['smtp_auth'] ) ? sanitize_text_field( $email_options['smtp_auth'] ) : '';
		$smtp_username         = isset( $email_options['smtp_username'] ) ? sanitize_text_field( $email_options['smtp_username'] ) : '';
		$smtp_password         = isset( $email_options['smtp_password'] ) ? sanitize_text_field( $email_options['smtp_password'] ) : '';
		$type_of_encryption    = isset( $email_options['type_of_encryption'] ) ? sanitize_text_field( $email_options['type_of_encryption'] ) : '';
		$smtp_port             = isset( $email_options['smtp_port'] ) ? sanitize_text_field( $email_options['smtp_port'] ) : '';
		$from_name             = isset( $email_options['from_name'] ) ? sanitize_text_field( $email_options['from_name'] ) : '';
		$from_email            = isset( $email_options['from_email'] ) ? sanitize_text_field( $email_options['from_email'] ) : '';

		$settings = array(
			'enable_smtp'         => $enable_smtp,
			'smtp_host'           => $smtp_host,
			'smtp_auth'           => $smtp_auth,
			'smtp_username'       => $smtp_username,
			'smtp_password'       => $smtp_password,
			'type_of_encryption'  => $type_of_encryption,
			'smtp_port'           => $smtp_port,
			'from_name'           => $from_name,
			'from_email'          => $from_email
			
		);

		$this->enable_smtp        = $enable_smtp;
		$this->smtp_host          = $smtp_host;
		$this->smtp_auth          = $smtp_auth;
		$this->smtp_username      = $smtp_username;
		$this->smtp_password      = $smtp_password;
		$this->type_of_encryption = $type_of_encryption;
		$this->smtp_port          = $smtp_port;
		
		// if from email and name already specified  on construct, do not override
		if ( empty($this->from_name) ) {
			$this->from_name = $from_name;
		}		
		if ( empty($this->from_email) ) {
			$this->from_email = $from_email;
		}
		//$this->from_name          = $from_name;
		//$this->from_email         = $from_email;
		
		return $settings;
	}
	
	public function is_smtp() {
		$is_smtp = ''; // def
		$settings = $this->settings();
		$is_smtp = true;
		if(!isset($settings['smtp_host']) || empty($settings['smtp_host'])){
			$is_smtp = false;
		}
		if(!isset($settings['smtp_auth']) || empty($settings['smtp_auth'])){
			$is_smtp = false;
		}
		if(isset($settings['smtp_auth']) && $settings['smtp_auth'] == "true"){
			if(!isset($settings['smtp_username']) || empty($settings['smtp_username'])){
				$is_smtp = false;
			}
			if(!isset($settings['smtp_password']) || empty($settings['smtp_password'])){
				$is_smtp = false;
			}
		}
		if(!isset($settings['type_of_encryption']) || empty($settings['type_of_encryption'])){
			$is_smtp = false;
		}
		if(!isset($settings['smtp_port']) || empty($settings['smtp_port'])){
			$is_smtp = false;
		}
		if(!isset($settings['from_email']) || empty($settings['from_email'])){
			$is_smtp = false;
		}
		if(!isset($settings['from_name']) || empty($settings['from_name'])){
			$is_smtp = false;
		}
		return $is_smtp;
	}
	
	public function phpmailer_send_mail() {	 
		
		global $phpmailer;
		
		$error_id       = ''; // def
		$mail_error     = ''; // def
		$mail_error_msg = ''; // def
	    
		// (Re)create it, if it's gone missing
		if ( ! ( $phpmailer instanceof PHPMailer ) ) {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			require_once ABSPATH . WPINC . '/class-smtp.php';
			$phpmailer = new PHPMailer( true );
		}
	
		if( $this->type_of_encryption == 'none' ){
			$type_of_encryption = '';  
		} else {
			$type_of_encryption = $this->type_of_encryption;
		}

		// Empty out the values that may be set
		$phpmailer->ClearAllRecipients();
		$phpmailer->ClearAttachments();
		$phpmailer->ClearCustomHeaders();
		$phpmailer->ClearReplyTos();
		
		$phpmailer->isSMTP(); // Tell PHPMailer to use SMTP
		$phpmailer->Host       = $this->smtp_host;
		$phpmailer->SMTPAuth   = $this->smtp_auth; // Force it to use Username and Password to authenticate
		$phpmailer->Username   = $this->smtp_username;
		$phpmailer->Password   = $this->smtp_password;		
		$phpmailer->SMTPSecure = $type_of_encryption; // Choose SSL or TLS, if necessary for your server
		$phpmailer->Port       = $this->smtp_port;
		$phpmailer->FromName   = $this->from_name;
		$phpmailer->From       = $this->from_email;

		// if array not empty
		if ( isset($this->cc) && !empty($this->cc) ) {
			// send copy to multiple addresses
			foreach($this->cc as $key => $value) {
			   $phpmailer->AddCC($value['email'], $value['name']); // send copy to
			}
		}
		
		// add bcc
		
		if ( empty($this->from_email) ) {
			$error_id      = 'smtp_mailer_sender_address_missing';
			$error_message = __('SMTP Mailer sender address missing.', 'cataloggi');
		}
		elseif ( empty($this->to_email) ) {
			$error_id      = 'smtp_mailer_recipient_address_missing';
			$error_message = __('SMTP Mailer recipient address missing.', 'cataloggi');
		} else {
			$isHtml = true;
			$phpmailer->WordWrap = 500;
			$phpmailer->isHTML($isHtml); // Set email format to HTML
			$phpmailer->addAddress( $this->to_email, $this->to_name ); // Add a recipient   
			$phpmailer->addReplyTo( $this->reply_to );
			$phpmailer->Subject = $this->subject;
			$phpmailer->Body    = $this->message;
			
			try {
				$phpmailer->Send();
			} catch ( phpmailerException $e ) {
				$mail_error     = $e->getCode();
				$mail_error_msg = $e->getMessage();
			}
		}
		
		if ( ! empty($error_id) ) {
			// save in error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
		}
		
		return $mail_error_msg;
	}
	
	
	/**
	 * Send SMTP and WP Mails. 
	 *
	 * @access  public static
	 * @since   1.0.0
	 * @return  bool
	 */
	public function send_email() {
		
		$error_id   = ''; // def
		$mail_error = ''; // def
		
		if ( isset($this->enable_smtp) && $this->enable_smtp == '1' ) {
			// check if ok
			if ( $this->is_smtp() ) {
				// send SMTP email
				$mail_error = $this->phpmailer_send_mail();
				if ( ! empty($mail_error) ) {
					$error_id      = 'smtp_phpmailer_email_sending_problem';
					$error_message = $mail_error;
				}
			} else {
				$error_id      = 'smtp_mailer_cannot_send_mails_invalid_or_missing_settings';
				$error_message = __('SMTP Mailer cannot send emails until you enter your credentials in the settings.', 'cataloggi');
			}

		} else {
			// set default from_name
			if ( empty($this->from_name) ) {
				$from_name = get_bloginfo('name');
			} else {
				$from_name = $this->from_name;
			}
			// set default from_email
			if ( empty($this->from_email) ) {
				$from_email = get_bloginfo('admin_email');
			} else {
				$from_email = $this->from_email;
			}
			
			if ( empty($this->mime_version) ) {
				$mime_version = '1.0';
			} else {
				$mime_version = $this->mime_version;
			}
			
			if ( empty($this->content_type) ) {
				$content_type = 'text/html';
			} else {
				$content_type = $this->content_type;
			}
			
			if ( empty($this->charset) ) {
				$charset = 'UTF-8';
			} else {
				$charset = $this->charset;
			}
			
			// if array not empty, send to multiple addresses
			if ( isset($this->cc) && !empty($this->cc) ) {
				// create array for wp mail
				foreach($this->cc as $key => $value) {
					$send_to_emails[] = $value['email'];
				}
				$to = $send_to_emails;
			} else {
				$to = $this->to_email;
			}
			
			
			$subject = $this->subject;
			$message = $this->message;
			
            $from   = "From: " . sanitize_text_field( $from_name ) ." <". sanitize_text_field( $from_email ) . ">" . " \r\n";
			$header = "MIME-Version: " . $mime_version . " \r\n";
			$header .= "Content-Type: " . $content_type . "; charset=" . $charset . " \r\n";
			$header .= $from . " \r\n";
			
			// send WP email
			if( ! wp_mail($to, $subject, $message, $header) ) {
				$error_id      = 'wp_mail_email_sending_problem';
				$error_message = __('WP Mail email sending problem.', 'cataloggi');
			}
		}
		
		if ( ! empty($error_id) ) {
			// save in error log
			CTLGGI_Error_Log::ctlggi_error_log( $error_id, $error_message );
		}
		
		return $error_id;
		
	}
	
	
	
}

?>