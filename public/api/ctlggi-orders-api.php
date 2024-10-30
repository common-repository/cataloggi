<?php

/**
 * Orders Api.
 *
 * @to-do This calass is not finished and not in use!!!
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Orders_Api {

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

	// return orders posts array
	public static function ctlggi_orders_list() {

		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
		return;
		
		$allowed_orders = array();
		
		global $wpdb;
		$posts = $wpdb->prefix . 'posts'; // table, do not forget about tables prefix
				
		$sql  = "
				SELECT ID, post_status, post_name
				FROM $posts
				WHERE post_type = 'cataloggi_orders'
				";
        $orders = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
		$statuses = CTLGGI_Custom_Post_Statuses::ctlggi_order_custom_post_statuses(); // available post statuses
		
		foreach($orders as $order )
		{
			$post_status = $order['post_status'];
			
			foreach($statuses as $status => $value )
			{
				// list orders by the allowed custom post statuses
				if ( $post_status == $status) {
				   $allowed_orders[] = $order; // array
				}
			}
			
		}
		
		return $allowed_orders; // return array
		
	}
	
    public static function ctlggi_orders_items_list() {
		
	}
	
	
}

?>