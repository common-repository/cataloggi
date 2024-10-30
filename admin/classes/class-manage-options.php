<?php

/**
 * Manage options on new version release. For WP repository auto updates.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Manage_Options {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;
	
	const VERSION = '1.0.1';

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
	 * Default options list. Defined in CTLGGI_Activator class.
	 *
	 * @since    1.0.0
	 * @return   array $options
	 */
	public static function ctlggi_get_options() 
	{
		$options = array(
			'save_settings_options'            => 'ctlggi_save_settings_options', 
			'general_options'                  => 'ctlggi_general_options',
			'currency_options'                 => 'ctlggi_currency_options',
			'cart_options'                     => 'ctlggi_cart_options',
			'payment_gateway_options'          => 'ctlggi_payment_gateway_options',
			'gateway_bacs_options'             => 'ctlggi_gateway_bacs_options',
			'gateway_paypalstandard_options'   => 'ctlggi_gateway_paypalstandard_options',
			'template_options'                 => 'ctlggi_template_options',
			'template_system_options'          => 'ctlggi_template_system_options',
			'order_email_settings_options'     => 'ctlggi_email_settings_options',
			'order_receipts_options'           => 'ctlggi_order_receipts_options',
			'payment_requests_options'         => 'ctlggi_payment_requests_options',
			'order_notifications_options'      => 'ctlggi_order_notifications_options',
			'misc_google_analytics_options'    => 'ctlggi_misc_google_analytics_options'
		);
		return $options;
	}

	/**
	 * Check if option exist if not create.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_create_option_if_not_exist() {
		
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
			
		require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-activator.php';
		$options = CTLGGI_Manage_Options::ctlggi_get_options();
		// check if option exist
		foreach ( $options as $option ) {
			if ( ! get_option($option) ) {
				// if option NOT exist then create
				CTLGGI_Activator::$option(); // create option with args
			}
		}
	}
	
	/**
	 * check if option version exist, if not update all options.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_add_version_to_option_if_not_exist() {
		
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		
		$options = CTLGGI_Manage_Options::ctlggi_get_options();
		foreach ( $options as $option ) {
			if ( get_option($option) ) {
				$get_option = get_option($option);
				// if no version
				if ( empty( $get_option['version'] ) ) {
					$add_args = array(
						'version'  => '1.0.0'
					);
					// add new args at the beginning
					$new_args = array_merge( $add_args, $get_option  );
					update_option($option, $new_args);
				}
			}
		}
	}
	
	
}

?>