<?php

/**
 * Emailer class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Emailer {

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
	 * Processing the emails. 
	 *
	 * To-Do: create emailer class, SMTP example: cw-notify-me/class/eMailer.php
	 *
	 * @access  public static
	 * @since   1.0.0
	 * @return  bool
	 */
	public static function ctlggi_emailer_send_email( $to, $subject, $emailbody, $header ) {
		
	    if ( empty( $to ) || empty( $subject ) || empty( $emailbody ) )
	        return;
		
		$mail_errors = false;
		// send the email using wp_mail()
		if( ! wp_mail($to, $subject, $emailbody, $header) ) {
			$mail_errors = true;
		}
		
		return $mail_errors;
	}
	
	
	
}

?>