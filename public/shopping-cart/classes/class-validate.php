<?php

/**
 * Shopping Cart - Validate
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Validate {

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
	 * Manage validation messages.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param array $validation
	 * @param string $type
	 * @return html $output
	 */
    public static function ctlggi_output_validation_message($validation='', $type='success') 
	{
		$output = '';
		
	    if ( $validation != '') {
		
			if ($type == 'success') {
				$type = 'alert-success'; // css
			} elseif ($type == 'info') {
				$type = 'alert-info'; // css
			} elseif ($type == 'error') {
				$type = 'alert-danger'; // css
			}
			
			// display validation error messages
			if( $validation != '' ) {
				$output .= '<div class="cw-form-msgs">';
				foreach ($validation as $validate ) {
				  $output .= '<div id="ctlggi_validation_id_' . esc_attr( $validate['validation_id'] ) . '" class="form-messages ' . esc_attr( $type ) . '">';
				  $output .= '&nbsp; ' . esc_attr( $validate['validation_msg'] ); 
				  $output .= '</div>';
				}
				$output .= '</div>';
			}
			return $output;	
		
		} else {
			return false;
		}
	}
	
	/**
	 * Output the error messages.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $error_id
	 * @param string $error_message
	 * @return html $output
	 */
    public static function ctlggi_error_msg( $error_id, $error_message ) {
		
		$output = '';
		
		// create array
		$validation[] = array(
			'validation_id'   => sanitize_text_field( $error_id ),
			'validation_msg'  => sanitize_text_field( $error_message )
		);
		
		$output = CTLGGI_Validate::ctlggi_output_validation_message($validation, $type='error');
		return $output;
	}
	
	/**
	 * Output the success messages.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $success_id
	 * @param string $success_message
	 * @return html $output
	 */
    public static function ctlggi_success_msg( $success_id, $success_message ) {
		
		$output = '';
		
		// create array
		$validation[] = array(
			'validation_id'   => sanitize_text_field( $success_id ),
			'validation_msg'  => sanitize_text_field( $success_message )
		);
		
		$output = CTLGGI_Validate::ctlggi_output_validation_message($validation, $type='success');
		return $output;
	}
	
	
}

?>