<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://cataloggi.com
 * @since      1.0.0
 *
 * @package    Cataloggi
 * @subpackage Cataloggi/includes
 */

/**
 * Fired during plugin deactivation.
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
 
class CTLGGI_Deactivator {

	/**
	 * Delete options on plugin deactivation if save settings not == 1
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_plugin_deactivator_delete_options() 
	{
		// check if we need to save the data on plugin deactivation
		// use get_option for multisite instead of get_option
		if ( get_option('ctlggi_save_settings_options') ) {
			// get options
			$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
			$plugin_deactivation_save_settings = $ctlggi_save_settings_options['ctlggi_plugin_deactivation_save_settings'];
			// delete options
			// use delete_option for multisite instead of delete_option
			if ( $plugin_deactivation_save_settings != '1' ) { 
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
			}
		} else {
		   return;
		}
	}
	
	/**
	 * WP Cron clean the scheduler on deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function wp_cron_clean_the_scheduler() {
		
		//first find the next schedule callback time
		$five_seconds = wp_next_scheduled("ctlggi_wp_cron_five_seconds_event");
		//use this function to unschedule it by passing the time and event name
		wp_unschedule_event($five_seconds, "ctlggi_wp_cron_five_seconds_event");
		
		//first find the next schedule callback time
		$daily = wp_next_scheduled("ctlggi_wp_cron_daily_event");
		//use this function to unschedule it by passing the time and event name
		wp_unschedule_event($daily, "ctlggi_wp_cron_daily_event");
		
		//first find the next schedule callback time
		$monthly = wp_next_scheduled("ctlggi_wp_cron_monthly_event");
		//use this function to unschedule it by passing the time and event name
		wp_unschedule_event($monthly, "ctlggi_wp_cron_monthly_event");
		
		wp_clear_scheduled_hook('ctlggi_wp_cron_five_seconds_event');
		wp_clear_scheduled_hook('ctlggi_wp_cron_daily_event');
		wp_clear_scheduled_hook('ctlggi_wp_cron_weekly_event');
		wp_clear_scheduled_hook('ctlggi_wp_cron_monthly_event');
	}
	
	/**
	 * This is how you would flush rewrite rules when a plugin is deactivated
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_flush_rewrite_rules() {
	   // check if custom post types exist
       //if ( post_type_exists('cataloggi') && post_type_exists('cataloggi_orders') ) {
	   if ( post_type_exists('cataloggi') ) {
		   flush_rewrite_rules( false ); // soft flush. Default is true (hard), update rewrite rules
	   }
	}

}

?>
