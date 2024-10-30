<?php

/**
 * Fired during plugin uninstallation
 *
 * @link       https://cataloggi.com/
 * @since      1.0.0
 *
 * @package    Cataloggi
 * @subpackage Cataloggi/includes
 */

/**
 * Fired during plugin uninstallation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cataloggi
 * @subpackage Cataloggi/includes
 * @author     Attila Abraham
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Uninstall {

	/**
	 * Delete options on plugin uninstallation if save settings not == 1
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_plugin_uninstaller_delete_options() 
	{
		// check if we need to save the data on plugin deactivation
		// use get_option for multisite instead of get_option
		if ( get_option('ctlggi_save_settings_options') ) {
			// get options
			$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
			$plugin_uninstallation_save_settings = $ctlggi_save_settings_options['ctlggi_plugin_uninstall_save_settings'];
			// delete options
			// use delete_option for multisite instead of delete_option
			if ( $plugin_uninstallation_save_settings != '1' ) { 
			   delete_option('ctlggi_general_options');
			   delete_option('ctlggi_currency_options');
			   delete_option('ctlggi_cart_options');
			   delete_option('ctlggi_save_settings_options');
			   delete_option('ctlggi_payment_gateway_options');
			   delete_option('ctlggi_template_options');
			   delete_option('ctlggi_gateway_bacs_options'); 
			   delete_option('ctlggi_email_settings_options');  
			   delete_option('ctlggi_order_receipts_options'); 
			   delete_option('ctlggi_order_notifications_options'); 
			   delete_option('ctlggi_payment_requests_options'); 
			   delete_option('ctlggi_template_system_options'); 
			   
			   // on plugin uninstallation we might delete ALL THE DB tables as well, but for security do not add option for
			   
			}
		} else {
		   return;
		}
	}



}

?>