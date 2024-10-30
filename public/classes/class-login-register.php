<?php

/**
 * Login Register class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Login_Register {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Redirect url for login success.
	 *
	 * @global $post;
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @return string $login_redirect_url
	 */
    public static function ctlggi_login_success_redirect_url() 
	{
		global $post; 
		$current_page_id = get_the_ID();
		
		// default
		//$login_redirect_url = '';
		$login_redirect_url = admin_url('index.php'); // default,wp-admin/index.php
		
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		$checkout_page_id = $ctlggi_cart_options['checkout_page']; // checkout page id
		// make page redirect
		$redirect_page_id = $ctlggi_cart_options['login_redirect_page']; // redirect page id
		// redirect to page if exist
		if ( $redirect_page_id != '0' && ! empty($redirect_page_id)  ) {
			// FIX: do not do custom login redirection on the checkout page when customer logs in
			// should be redirected to the current page not to any other pages.
			if ( isset($_GET['page'] ) && $_GET['page'] == "checkout" ) {
				$login_redirect_url = $_SERVER['REQUEST_URI']; // default, current page
			} elseif ( $checkout_page_id != '0' && ! empty($checkout_page_id)  ) {
				// if we are on the checkout page
				if ( $checkout_page_id == $current_page_id ) {
					$login_redirect_url = $_SERVER['REQUEST_URI']; // default, current page
				} else {
					// get page link by post id
					$page_link = get_permalink( $redirect_page_id );
					$login_redirect_url = $page_link;
				}
			} elseif ( $checkout_page_id == '0'  ) {
				// get page link by post id
				$page_link = get_permalink( $redirect_page_id );
				$login_redirect_url = $page_link;
			}
		} else {
			//$login_redirect_url = $_SERVER['REQUEST_URI']; // default, current page
			$login_redirect_url = admin_url('index.php'); // default,wp-admin/index.php
		}
		return $login_redirect_url;
	}

	/**
	 * Print error messages.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $errors
	 * @return html $output
	 */
	public static function ctlggi_print_error_message( $errors ) 
	{
		$output = '';
		
		if ( ! empty( $errors ) ) {
			
			$alert_success = 'alert-success'; // css
			$alert_info    = 'alert-info';    // css
			$alert_danger  = 'alert-danger';  // css
			
			// output each error / request
		    $output .= '<div class="cw-form-msgs">';
				$output .= '<div id="ctlggi_error_id_' . esc_attr( $errors['error_id'] ) . '" class="form-messages ' . esc_attr( $alert_danger ) . '">';
				$output .= '&nbsp; ' . esc_attr( $errors['error_message'] ); 
				$output .= '</div>';
		    $output .= '</div>';
			return $output;	
		} else {
			return;
		}
	}

	/**
	 * Print success messages.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $success
	 * @return html $output
	 */
	public static function ctlggi_print_success_message( $success ) 
	{
		$output = '';
		
		if ( ! empty( $success ) ) {
			
			$alert_success = 'alert-success'; // css
			$alert_info    = 'alert-info';    // css
			$alert_danger  = 'alert-danger';  // css
			
			// output each error / request
		    $output .= '<div class="cw-form-msgs">';
				$output .= '<div id="ctlggi_success_id_' . esc_attr( $success['success_id'] ) . '" class="form-messages ' . esc_attr( $alert_success ) . '">';
				$output .= '&nbsp; ' . esc_attr( $success['success_message'] ); 
				$output .= '</div>';
		    $output .= '</div>';
			return $output;	
		} else {
			return;
		}
	}

	/**
	 * Login form required fields.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @return array $required_fields
	 */
	public static function ctlggi_login_form_required_fields() {
		
		$required_fields = array(
			'ctlggi_username' => array(
				'error_id' => 'username_required',
				'error_message' => __( 'Username is required.', 'cataloggi' )
			),
			'ctlggi_password' => array(
				'error_id' => 'password_required',
				'error_message' => __( 'Password is required.', 'cataloggi' )
			)
		);
		
        return apply_filters( 'ctlggi_login_form_required_fields', $required_fields );

	}

	/**
	 * Login form validation.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @return array $validate_fields
	 */
	public static function ctlggi_login_form_validate() {
		
		$validate_fields = array(
			'incorrect_password' => array(
				'error_id' => 'incorrect_password',
				'error_message' => __( 'Incorrect password entered.', 'cataloggi' )
			),
			'username_not_exist' => array(
				'error_id' => 'username_not_exist',
				'error_message' => __( 'The username you entered does not exist.', 'cataloggi' )
			)
		);
		
        return apply_filters( 'ctlggi_login_form_validate', $validate_fields );

	}

	/**
	 * Login the user.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param int $userid
	 * @param string $username
	 * @param int $remember
	 * @return void
	 */
	public static function ctlggi_login_user( $userid, $username, $remember) {
		// security
		if ( $userid < 1 )
		return;
		//$remember = false;
		$secure   = '';
		wp_set_auth_cookie( $userid, $remember, $secure);
		wp_set_current_user($userid, $username);	
	}
	
	/**
	 * Ajax Login form process.
	 * 
	 * @since 1.0.0
	 * @return object
	 */
    public function ctlggi_login_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-login-form-nonce'], 'ctlggi_login_form_nonce') )
	    {
			// defaults
			$message = '';
			
			// learn: http://code.tutsplus.com/articles/data-sanitization-and-validation-with-wordpress--wp-25536
			$username   = sanitize_text_field( $postdata['ctlggi_username'] );
			//$password   = esc_attr( $postdata['ctlggi_password'] );
			$password   = sanitize_text_field( $postdata['ctlggi_password'] ); // do not esc_attr
			
			$remember   = isset( $postdata[ 'ctlggi_remember' ] ) ? sanitize_text_field( $postdata[ 'ctlggi_remember' ] ) : '0'; // checkbox
			
			// check username and password at login
			// source: https://developer.wordpress.org/reference/functions/wp_check_password/#source-code
			$user = get_user_by( 'login', $username );
	
			if(!isset($username) || $username == '') 
			{
				// username - required
				$required = CTLGGI_Login_Register::ctlggi_login_form_required_fields();
				$errors   = $required['ctlggi_username']; // array
				$print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
				// return json
				echo json_encode(array('loggedin'=>false, 'message'=>$print ));
				
			}
			elseif(!isset($password) || $password == '') 
			{
				// password - required
				$required = CTLGGI_Login_Register::ctlggi_login_form_required_fields();
				$errors   = $required['ctlggi_password']; // array
				$print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );

				// return json
				echo json_encode(array('loggedin'=>false, 'message'=>$print ));
				
			} else {
				
			   if ( $user ) 
			   {
					
				  if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) 
				  {	
				     $userid = $user->ID;
					 
					 // ##### success, log user in
                     CTLGGI_Login_Register::ctlggi_login_user( $userid, $username, $remember);
					 
                     // Ajax Redirect - return json - if logged in true, redirect
					 //$loader_img = '<img src="' . CTLGGI_PLUGIN_URL . 'assets/images/spinner.gif" width="128" height="15" alt="loading..." />';
					 $loader_img = '<p>' . __('Login successful, redirecting...') . '</p>';
					 echo json_encode( array('loggedin'=>true, 'message'=>$loader_img) ); // improve it
					 
				  } else {
					  
					 // password - validate
					 $validate = CTLGGI_Login_Register::ctlggi_login_form_validate();
					 $errors   = $validate['incorrect_password']; // array
					 $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );

                     // return json
					 echo json_encode(array('loggedin'=>false, 'message'=>$print ));
					 
				  }
				 
				} else {
					
					// username - validate
				    $validate = CTLGGI_Login_Register::ctlggi_login_form_validate();
					$errors   = $validate['username_not_exist']; // array
					$print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );

                    // return json
					echo json_encode(array('loggedin'=>false, 'message'=>$print ));
				}
			}
		}

	    #### important! #############
	    exit; // don't forget to exit!
			
    }

	/**
	 * Register form fields validation.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @return array $validate_fields
	 */
	public static function ctlggi_register_fields_validate() {
		
		$validate_fields = array(
			'username_already_taken' => array(
				'error_id' => 'username_already_taken',
				'error_message' => __( 'Username already taken.', 'cataloggi' )
			),
			'invalid_username' => array(
				'error_id' => 'invalid_username',
				'error_message' => __( 'Invalid username.', 'cataloggi' )
			),
			'email_already_taken' => array(
				'error_id' => 'email_already_taken',
				'error_message' => __( 'Email address already taken. Please login or enter a different email address.', 'cataloggi' )
			),
			'invalid_email' => array(
				'error_id' => 'invalid_email',
				'error_message' => __( 'Invalid email.', 'cataloggi' )
			),
			'password_required' => array(
				'error_id' => 'password_required',
				'error_message' => __( 'Please enter a password.', 'cataloggi' )
			),
			'passwords_not_match' => array(
				'error_id' => 'passwords_not_match',
				'error_message' => __( 'Passwords do not match.', 'cataloggi' )
			)
		);
		
        return apply_filters( 'ctlggi_register_fields_validate', $validate_fields );

	}

	/**
	 * User data validation.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param array $userdata
	 * @return html $print
	 */
	public static function ctlggi_validate_user_data( $userdata ) 
	{ 
		// defaults
		$print = '';
		
		if( ! empty( $userdata ) ) {
			
			if( username_exists( $userdata['ctlggi_username'] ) ) {
				
				// username - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['username_already_taken']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif( ! validate_username( $userdata['ctlggi_username'] ) ) {
				
				// username - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['invalid_username']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif( email_exists( $userdata['ctlggi_user_email'] ) ) {
				
				// email - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['email_already_taken']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif( empty( $userdata['ctlggi_user_email'] ) || ! is_email( $userdata['ctlggi_user_email'] ) ) {
				
				// email - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['invalid_email']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif( empty( $userdata['ctlggi_user_pass'] ) ) {
				$validation[] = __('Please enter a password.', 'cataloggi');
				
				// password - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['password_required']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			} elseif( ( ! empty( $userdata['ctlggi_user_pass'] ) && empty( $userdata['ctlggi_user_pass_again'] ) ) || ( $userdata['ctlggi_user_pass'] != $userdata['ctlggi_user_pass_again'] ) ) {
				
				// password - validate
				$validate = CTLGGI_Login_Register::ctlggi_register_fields_validate();
				$errors   = $validate['passwords_not_match']; // array
				return $print    = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				
			}
		
		}
		
		do_action( 'ctlggi_validate_user_data' ); // <- extensible	
		
		// validation
		if( empty( $print ) ) {
           return; // valid
		}
		
	}
	
	/**
	 * Ajax register form process.
	 * 
	 * @since 1.0.0
	 * @return object
	 */
    public function ctlggi_register_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-register-form-nonce'], 'ctlggi_register_form_nonce') )
	    {
			// defaults
			$message = '';
			
			// learn: http://code.tutsplus.com/articles/data-sanitization-and-validation-with-wordpress--wp-25536
			$username       = sanitize_text_field( $postdata['ctlggi_username'] );
			$email          = sanitize_email( $postdata['ctlggi_user_email'] );
			$password       = sanitize_text_field( $postdata['ctlggi_user_pass'] );
			$password_again = sanitize_text_field( $postdata['ctlggi_user_pass_again'] );
			
			$role           = sanitize_text_field( $postdata['ctlggi_user_role'] );
			
			// validate userdata
			$userdata = array(
				'ctlggi_username'        => $username,
				'ctlggi_user_email'      => trim($email),
				'ctlggi_user_pass'       => $password,
				'ctlggi_user_pass_again' => $password_again
			);
			
			// validate create an account fields
			$errors = CTLGGI_Login_Register::ctlggi_validate_user_data( $userdata );
			
			// validation
			if ( ! empty( $errors ) ) {
			    echo json_encode(array('success'=>false, 'message'=>$errors )); // return json
			}
			else {	
				// success, register user
				$userdata = array(
					'user_login'      => $username,
					'user_pass'       => $password,
					'user_email'      => $email,
					'first_name'      => '',
					'last_name'       => '',
					'user_registered' => date( 'Y-m-d H:i:s' ),
					'role'            => $role //'cataloggi_customer' // role is set in class-cwctlg-shortcodes.php
				);
				// register new user
				$userid = CTLGGI_Login_Register::ctlggi_register_user( $userdata ); // return userid
				
				// check if we get the user id
				if( ! empty( $userid ) ) {
					$userid = $userid;
				}

				$success = array(
					'success_id'      => 'successfully_registered',
					'success_message' => __( "You have been successfully registered. Now you can login. Thank you! ", "cataloggi" )
				);
				$print    = CTLGGI_Login_Register::ctlggi_print_success_message( $success ) ;
				echo json_encode(array('success'=>true, 'message'=>$print ));
				
			}
			
		}
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}

	/**
	 * Register user and send notification emails.
	 *
	 * @since   1.0.0
	 * @access public static
	 * @param   array $userdata
	 * @return  string $userid
	 */
	public static function ctlggi_register_user( $userdata ) 
	{ 
		if ( empty( $userdata ) )
			return;
			
		do_action( 'ctlggi_register_user_before' ); // <- extensible
			
		$user_args = apply_filters( 'ctlggi_register_user_args', array(
			'user_login'      => $userdata['user_login'] ? sanitize_text_field( $userdata['user_login'] ) : '',
			'user_pass'       => $userdata['user_pass']  ? sanitize_text_field( $userdata['user_pass'] )  : '',
			'user_email'      => $userdata['user_email'] ? sanitize_email( $userdata['user_email'] ) : '',
			'first_name'      => $userdata['first_name'] ? sanitize_text_field( $userdata['first_name'] ) : '',
			'last_name'       => $userdata['last_name']  ? sanitize_text_field( $userdata['last_name'] )  : '',
			'user_registered' => date( 'Y-m-d H:i:s' ),
			'role'            => $userdata['role']  ? sanitize_text_field( $userdata['role'] )  : 'subscriber' // if no role specified use default : subscriber
		), $userdata );
		
		// Insert user
		$userid = wp_insert_user( $user_args );
			
		// On success.
		if ( ! is_wp_error( $userid ) ) {
			
			do_action( 'ctlggi_register_user_after' ); // <- extensible
			
			// send registration details to user
			CTLGGI_Login_Register::ctlggi_send_user_registration_mail( $userdata );
			
			// send notification email to admin
			CTLGGI_Login_Register::ctlggi_send_admin_notification_mail( $userdata );
			
			return $userid;
		}
			
	}
	
	/**
	 * Validate forgot password form.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return array $validate_fields
	 */
	public static function ctlggi_forgot_pw_form_validate() {
		
		$validate_fields = array(
			'user_login_empty' => array(
				'error_id' => 'user_login_empty',
				'error_message' => __( 'Enter a username or e-mail address.', 'cataloggi' )
			),
			'no_user_based_on_email' => array(
				'error_id' => 'no_user_based_on_email',
				'error_message' => __( 'There is no user registered with that email address.', 'cataloggi' )
			),
			'invalid_username_or_email' => array(
				'error_id' => 'invalid_username_or_email',
				'error_message' => __( 'Invalid username or email.', 'cataloggi' )
			),
			'email_sending_error' => array(
				'error_id' => 'email_sending_error',
				'error_message' => __( 'Email sending error.', 'cataloggi' )
			),
		);
		
        return apply_filters( 'ctlggi_forgot_pw_form_validate', $validate_fields );

	}
	
	/**
	 * Forgot password form process.
	 *
	 * @since   1.0.0
	 * @return  object
	 */
    public function ctlggi_forgot_pw_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-forgot-pw-form-nonce'], 'ctlggi_forgot_pw_form_nonce') )
	    {
			$user_login    = sanitize_text_field( $postdata['ctlggi_user_login'] );
			
			// defaults
			$user_data = '';
			$errors    = '';
			if( empty( $user_login ) ) {
				// error: field empty
				$required = CTLGGI_Login_Register::ctlggi_forgot_pw_form_validate();
				$errors   = $required['user_login_empty']; // array	
			} 
			elseif ( strpos( $user_login, '@' ) ) {
				$user_data = get_user_by( 'email', trim( $user_login ) );
				if ( empty( $user_data ) ) {
					// error: no user found
					$required = CTLGGI_Login_Register::ctlggi_forgot_pw_form_validate();
					$errors   = $required['no_user_based_on_email']; // array	
				}
			} else {
				$login = trim($user_login);
				$user_data = get_user_by('login', $login);
			}
			
			if ( ! empty($errors) ) {
				// display errors
				$print = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
				// return json
				echo json_encode(array('success'=>false, 'message'=>$print ));
			} else {
				if ( ! $user_data ) {
					// error: invalid username or email
					$required = CTLGGI_Login_Register::ctlggi_forgot_pw_form_validate();
					$errors   = $required['invalid_username_or_email']; // array
					// display errors
					$print = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
					// return json
					echo json_encode(array('success'=>false, 'message'=>$print ));
				} else {
					// ok
					// generate a new password
					//$random_password = wp_generate_password( 12, false );
					
					$username = $user_data->data->user_login;
                    $user_email = $user_data->data->user_email;
					$user_id = $user_data->data->ID;
					
					$user_info  = get_userdata($user_id); // get user by ID
					$first_name = $user_info->first_name;
					$last_name  = $user_info->last_name;
					
					// get password reset key
					$pw_reset_key = get_password_reset_key( $user_data );
					
					/*
					// update user pw
					$update_user = wp_update_user( array (
							'ID' => $user_id, 
							'user_pass' => $random_password
						)
					);
					*/
						
					$userdata = array(
						'user_login'      => $username,
						'user_email'      => $user_email,
						'first_name'      => $first_name,
						'last_name'       => $last_name,
						'pw_reset_key'    => $pw_reset_key,
					);
					
					// SUCCESS,  send email
					$mail_errors = CTLGGI_Login_Register::ctlggi_send_passord_reset_mail( $userdata );
					
					// will return true only when $mail_errors = true
					if ( $mail_errors === true ) {
					
						// error: email sending error
						$required = CTLGGI_Login_Register::ctlggi_forgot_pw_form_validate();
						$errors   = $required['email_sending_error']; // array
						// display errors
						$print = CTLGGI_Login_Register::ctlggi_print_error_message( $errors );
						// return json
						echo json_encode(array('success'=>false, 'message'=>$print ));
					
					} else {
						
						$success = array(
							'success_id'      => 'pw_reset_conf_email_sent',
							'success_message' => __( "Check your e-mail for the confirmation link. Thank you! ", "cataloggi" )
						);
						$print    = CTLGGI_Login_Register::ctlggi_print_success_message( $success ) ;
						echo json_encode(array('success'=>true, 'message'=>$print ));						

					}
					
				}
			}
			
		}
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}

	/**
	 * Send notification email on successful registration.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param array $userdata
	 * @return void
	 */
	public static function ctlggi_send_user_registration_mail( $userdata ) 
	{ 
	    if ( empty( $userdata ) )
	    return;
		
		$user_name   = sanitize_text_field( $userdata['user_login'] ); // username
		$user_pass   = sanitize_text_field( $userdata['user_pass'] );
		$user_email  = sanitize_email( $userdata['user_email'] );
		$first_name  = sanitize_text_field( $userdata['first_name'] );
		$last_name   = sanitize_text_field( $userdata['last_name'] );
		
		if ( !empty( $first_name ) ) {
			$dear_name = $first_name;
		} else {
			$dear_name = $user_name;
		}
		
		// get site name
		// source: https://developer.wordpress.org/reference/functions/get_bloginfo/
		$get_blog_name = get_bloginfo('name');
		if ( trim($get_blog_name) != false ) {
			$blog_name = trim( get_bloginfo('name') );
		} else {
			$blog_name = 'WordPress';
		}
		
		$admin_email = trim( get_bloginfo('admin_email') );
		
	    $subject = $blog_name . " " . __( "Registration Details", "cataloggi" );
		
		$emailbody = "\n";
		$emailbody .= __( "Dear ", "cataloggi" ) . " " . esc_attr( $dear_name ) . " \n\n";
		$emailbody .= __( "Thank you for your registration!", "cataloggi" ) . " \n\n";
		
		$emailbody .= __( "Your Registration Details", "cataloggi" ) . " \n";
		$emailbody .= __( "Username: ", "cataloggi" ) . " " . esc_attr( $user_name ) . " \n";
		$emailbody .= __( "Email: ", "cataloggi" ) . " " . esc_attr( $user_email ) . " \n";
		$emailbody .= __( "Password: ", "cataloggi" ) . " " . esc_attr( $user_pass ) . " \n\n";
		
		$emailbody .= __( "Please visit our site to login. ", "cataloggi" ) . " \n\n";
		
		$emailbody .= home_url() . "" . " \n\n"; // /wp-login.php
		
		$emailbody .= __( "With Kind Regards,  ", "cataloggi" ) . " " . esc_attr( $blog_name ) .  " \n\n";
		
		$emailbody = stripslashes_deep( nl2br($emailbody) );
		
		$sender = "From: ". esc_attr( $blog_name ) ." <". esc_attr( $admin_email ).">" . "\r\n";
		
		// write the email content
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
	
		$to_email = $user_email;
		
		// send email
		//CTLGGI_Emailer::ctlggi_emailer_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => '',
			'from_email'   => '',	
			'reply_to'     => $admin_email,
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
	 * Send notification email to admin  on successful registration.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param array $userdata
	 * @return void
	 */
	public static function ctlggi_send_admin_notification_mail( $userdata ) 
	{ 
	    if ( empty( $userdata ) )
	    return;
		
		$user_name  = sanitize_text_field( $userdata['user_login'] ); // username
		$user_email = sanitize_email( $userdata['user_email'] );
		
		// get site title
		// source: https://developer.wordpress.org/reference/functions/get_bloginfo/
		if ( !empty( get_bloginfo('name') ) ) {
			$blog_name = get_bloginfo('name');
		} else {
			$blog_name = 'WordPress';
		}
		
		$admin_email = get_bloginfo('admin_email');
	
	    $subject = esc_attr( $blog_name ) . " " . __( "New User Registration", "cataloggi" );
		
		$emailbody = "\n";
		$emailbody .= __( "New user registration on your site ", "cataloggi" ) . " " . esc_attr( $blog_name ) . " \n\n";
		$emailbody .= __( "Username: ", "cataloggi" ) . " " . esc_attr( $user_name ) . " \n";
		$emailbody .= __( "Email: ", "cataloggi" ) . " " . esc_attr( $user_email ) . " \n\n";
		
		$emailbody = stripslashes_deep( nl2br($emailbody) );
		
		$sender = "From: ". esc_attr( $blog_name ) ." <". esc_attr( $admin_email ) .">" . "\r\n";
		
		// write the email content
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
	
		$to_email = $admin_email;
		
		// send email
		//CTLGGI_Emailer::ctlggi_emailer_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => '',
			'from_email'   => '',		
			'reply_to'     => $admin_email,
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
	 * Send password reset email.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param array $userdata
	 * @return void
	 */
	public static function ctlggi_send_passord_reset_mail( $userdata ) 
	{ 
	    if ( empty( $userdata ) )
	    return;
		
		$user_name    = sanitize_text_field( $userdata['user_login'] ); // username
		$user_email   = sanitize_email( $userdata['user_email'] );
		$first_name   = sanitize_text_field( $userdata['first_name'] );
		$last_name    = sanitize_text_field( $userdata['last_name'] );
		$pw_reset_key = sanitize_text_field( $userdata['pw_reset_key'] );
		
		if ( !empty( $first_name ) ) {
			$dear_name = $first_name;
		} else {
			$dear_name = $user_name;
		}
		
		// get site title
		// To Do: Check if is Multisite
		if ( !empty( get_bloginfo('name') ) ) {
			$blog_name = get_bloginfo('name');
		} else {
			$blog_name = 'WordPress';
		}
		
		$admin_email = get_bloginfo('admin_email');
		
	    $subject = esc_attr( $blog_name ) . " " . __( "Password reset request", "cataloggi" );
		
		$emailbody = "\n";
		$emailbody .= __( "Dear ", "cataloggi" ) . " " . esc_attr( $dear_name ) . " \n\n";

		$emailbody .= __( "Someone has requested a password reset for the following account: ", "cataloggi" ) . " " . esc_url( network_home_url( '/' ) ) . " \n\n";
		
		$emailbody .= sprintf(__('Username: %s'), esc_attr(  $user_name ) ) . " \n\n";
		
		$emailbody .= __( "If this was a mistake, just ignore this email and nothing will happen.", "cataloggi" ) . " \n\n";
		
		$emailbody .= __( "To reset your password, visit the following address: ", "cataloggi" ) . " \n\n";
		
		$emailbody .= esc_url( network_site_url("wp-login.php?action=rp&key=$pw_reset_key&login=" . rawurlencode($user_name), "login") ) . " \n\n";
		
		$emailbody .= __( "With Kind Regards,  ", "cataloggi" ) . " " . esc_attr( $blog_name ) .  " \n\n";
		
		$emailbody = stripslashes_deep( nl2br($emailbody) );
		
		$sender = "From: ". esc_attr( $blog_name )." <". esc_attr( $admin_email ).">" . "\r\n";
		
		// write the email content
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=UTF-8\r\n";
		$header .= $sender . "\r\n";
	
		$to_email = $user_email;
		
		// send email
		//$mail_errors = CTLGGI_Emailer::ctlggi_emailer_send_email( $to, $subject, $emailbody, $header );
		
		$emailer = array(
			'from_name'    => '',
			'from_email'   => '',	
			'reply_to'     => $admin_email,
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
		
		return $mail_errors;
		
	}
	
	
}

?>