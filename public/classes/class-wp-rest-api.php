<?php

/**
 * Manage WordPress Rest API.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Manage_Wp_Rest_Api {

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
	 * Manage WordPress Rest API
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_manage_wp_rest_api() {
		
		$current_version = get_bloginfo('version');
		
		if ( version_compare( $current_version, '4.7', '>=' ) ) {
			$this->ctlggi_rest_api_force_auth_error();
		} else {
			$this->ctlggi_rest_api_disable_via_filters();
		}
		
	}
	
	/**
     * This function gets called if the current version of WordPress is less than 4.7
     * We are able to make use of filters to actually disable the functionality entirely
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_rest_api_disable_via_filters() {
		// Filters for WP-API version 1.x
		add_filter( 'json_enabled', '__return_false' );
		add_filter( 'json_jsonp_enabled', '__return_false' );
	
		// Filters for WP-API version 2.x
		add_filter( 'rest_enabled', '__return_false' );
		add_filter( 'rest_jsonp_enabled', '__return_false' );
	
		// Remove REST API info from head and headers
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
	}
	
	/**
     * This function is called if the current version of WordPress is 4.7 or above
     * Forcibly raise an authentication error to the REST API if the user is not logged in
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_rest_api_force_auth_error() {
		add_filter( 'rest_authentication_errors', array($this, 'ctlggi_rest_api_access_only_if_logged_in'), 10, 1 );
	}
	
	/**
	 * Allow Rest API access only for logged in users.
	 *
	 * @since 1.0.0
	 * @param $access
	 * @return WP_Error
	 */
	public function ctlggi_rest_api_access_only_if_logged_in( $access ) {
		if( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access the REST API.', 'cataloggi' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return $access;	
	}
	
}

?>