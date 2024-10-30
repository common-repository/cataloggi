<?php

/**
 * Shopping Cart - Payments.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Payments {

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
	 * Checkout form Ajax process.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_payments_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		// parse string, convert formdata into array
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-payments-form-nonce'], 'ctlggi_payments_form_nonce') )
	    {
			
		}
		
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
	
	
}

?>