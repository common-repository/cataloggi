<?php

/**
 * WordPress Rest API Endpoints class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Wp_Rest_Api_Endpoints {

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
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 */
	public function ctlggi_prefix_get_endpoint_phrase() {
		// rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
		$data = 'Hello World, this is the WordPress REST API';
		
			// if logged in get current user data
			$current_user = wp_get_current_user();
			
			$username    = $current_user->user_login;
			//$email       = $current_user->user_email;
			//$first_name  = $current_user->user_firstname;
			//$last_name   = $current_user->user_lastname;
			$displayname = $current_user->display_name;
			$userid      = $current_user->ID;
			
			if ( !empty($displayname) ) {
				$displayname = $displayname;
			} else {
				$displayname = 'Cannot get data.';
			}
			
		
        // Return all of our comment response data.
        return rest_ensure_response( $data );
	}
	 
	/**
	 * This function is where we register our routes for our example endpoint.
	 */
	public function ctlggi_prefix_register_example_routes() {
		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		// e.g. https://codeweby.com/wp-json/hello-world/v1/phrase
		register_rest_route( 'hello-world/v1', '/phrase', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			//'methods'  => WP_REST_Server::READABLE,
			'methods'  => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array($this, 'ctlggi_prefix_get_endpoint_phrase')
		) );
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_items_rest_endpoint( $request ) {
		
		// if logged in get current user data
		$current_user = wp_get_current_user();
		
		$username    = $current_user->user_login;
		//$email       = $current_user->user_email;
		//$first_name  = $current_user->user_firstname;
		//$last_name   = $current_user->user_lastname;
		$displayname = $current_user->display_name;
		$userid      = $current_user->ID;
		
		if ( !empty($displayname) ) {
			$displayname = $displayname;
		} else {
			$displayname = 'Cannot get data.';
		}
		
$user_meta=get_userdata('1');

$user_roles=$user_meta->roles; //array of roles the user is part of.
		
		$home_url = home_url();
		
		return new WP_REST_Response( $user_meta, 200 );
		
		//return rest_ensure_response( $displayname );
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_items_rest_register_routes() {
		// https://codeweby.com/wp-json/cataloggi/v1/items
		
		$version = '1';
		$namespace = 'cataloggi/v' . $version;
		$base = 'items';
		
		register_rest_route( $namespace, '/' . $base, array(
		  array(
			'methods'             => WP_REST_Server::READABLE,
			//'methods' => 'POST',
			'callback'            => array($this, 'ctlggi_items_rest_endpoint'),
			//'permission_callback' => array( $this, 'permissions_check' ),
			'args'                => array()
          ) 
		));
	}

	/**
	* Check if a given request has access to get items
	*
	* @param WP_REST_Request $request Full data about the request.
	* @return WP_Error|bool
	*/
	public function permissions_check( $request ) {
		//return true; <--use to make readable by all
		return current_user_can( 'read' );
	}
	
	
}

?>