<?php

/**
 * Shopping Cart - Payment Gateways class.
 *
 * @package     cataloggi
 * @subpackage  Public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Payment_Gateways {

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
	 * Global. Payment gatewazs.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $gateways
	 */
    public static function ctlggi_payment_gateways() 
	{
		// get options
		$ctlggi_gateway_bacs_options = get_option('ctlggi_gateway_bacs_options');
		
		if ( $ctlggi_gateway_bacs_options['ctlggi_bacs_show_billing_details'] == '1' ) {
			$billing_details = '1';
		} else {
			$billing_details = '0';
		}
		
		// get options
		$ctlggi_gateway_paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
		
		if ( $ctlggi_gateway_paypalstandard_options['ctlggi_paypalstandard_show_billing_details'] == '1' ) {
			$billing_details_paypalstandard = '1';
		} else {
			$billing_details_paypalstandard = '0';
		}
		
		$gateways = array(
			// none is for the payment requests and for none.php FREE payments
			'none' => array(
				'payment_gateway_label' => 'None', // payment gateway label
				'payment_gateway_name'  => 'none', // payment gateway name
				'create_an_account'     => '1', // checkout form show create an account fields
				'credit_card_details'   => '0', // checkout form show credit card details fields
				'billing_details'       => '0', // checkout form show billing details fields
				'buy_now'               => '0' // buy now button
			),
			'bacs' => array(
				'payment_gateway_label' => 'Direct Bank Transfer', // payment gateway label
				'payment_gateway_name'  => 'bacs', // payment gateway name
				'create_an_account'     => '1', // checkout form show create an account fields
				'credit_card_details'   => '0', // checkout form show credit card details fields
				'billing_details'       => $billing_details, // checkout form show billing details fields
				'buy_now'               => '0' // buy now button
			),
			
			'paypalstandard' => array(
				'payment_gateway_label' => __( 'PayPal Standard', 'cataloggi' ), // payment gateway label
				'payment_gateway_name'  => 'paypalstandard', // payment gateway name
				'create_an_account'     => '1', // checkout form show create an account fields
				'credit_card_details'   => '0', // checkout form show credit card details fields
				'billing_details'       => $billing_details_paypalstandard, // checkout form show billing details fields
				'buy_now'               => '1' // buy now button
			),
			
		);
	
		return apply_filters( 'ctlggi_payment_gateways', $gateways ); // <- extensible

	}
	
    public static function ctlggi_selected_gateway( $selected_gateway ) 
	{
		
	}
	
}

?>