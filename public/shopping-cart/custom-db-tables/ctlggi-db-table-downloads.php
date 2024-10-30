<?php

/**
 * Items Custom Data Table Downloads : ctlggi_order_downloads
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_DB_Order_Downloads {

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
	 * Select all Downloads.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $downloads
	 */
	public static function ctlggi_select_all_order_downloads() {
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
		return;
		
	    // $post_id is the order_id
	    global $wpdb;
		  
	    $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		
		$sql  = "
				SELECT id, order_id, item_id, user_id, user_email, order_key, download_limit, order_date, download_expiry_date, download_count  
				FROM $ctlggi_order_downloads
				";
		return $downloads = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
	}

	/**
	 * Delete single Download.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $order_id
	 * @param  int $item_id
	 * @return void
	 */
	public static function ctlggi_delete_single_download( $order_id, $item_id )
	{
		if ( empty( $order_id ) && empty( $item_id ) )
		return;
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
		return;
		
		global $wpdb;
		
		$ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		$wpdb->delete( $ctlggi_order_downloads, array( 'order_id' => $order_id, 'item_id' => $item_id ) );
		
	}

	/**
	 * Insert single Download.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $order_id
	 * @param  int $item_id
	 * @return int last insert ID
	 */
	public static function ctlggi_insert_single_download( $order_id, $item_id )
	{
		if ( empty( $order_id ) && empty( $item_id ) )
		return;
		
		// only admin allowed
		if ( ! current_user_can( 'manage_options' ) )
		return;
		
		$order_id = intval( $order_id );
		$item_id  = intval( $item_id );
		
		// get order data
		$order_date            = get_post_meta( $order_id, '_order_date', true );
		$user_id               = get_post_meta( $order_id, '_ctlggi_order_cus_user_id', true );
		$order_key             = get_post_meta( $order_id, '_order_key', true ); // license key
		$user_email            = get_post_meta( $order_id, '_email', true );
		
		// get item meta
		$download_limit        = get_post_meta( $item_id, '_ctlggi_item_download_limit', true );
		$item_download_expiry  = get_post_meta( $item_id, '_ctlggi_item_download_expiry', true ); // return int
		
		if ( empty($download_limit) ) {
			$download_limit = '';
		} else {
			$download_limit = $download_limit;
		}
		
		// if empty never expires
		if ( empty($item_download_expiry) ) {
			$download_expiry_date = ''; // <- never expires, 0000-00-00
		} else {
			// order date + item download expiry 
			$add_days = strtotime(date("Y-m-d", strtotime($order_date)) . "+" . $item_download_expiry . " days");
			$download_expiry_date = date('Y-m-d', $add_days);
		}
		
		// insert into "ctlggi_order_downloads"
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_downloads';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'order_id'             => intval( $order_id ), 
				'item_id'              => intval( $item_id ), 
				'user_id'              => intval( $user_id ), 
				'user_email'           => sanitize_email( $user_email ), 
				'order_key'            => sanitize_text_field( $order_key ), 
				'download_limit'       => sanitize_text_field( $download_limit ), 
				'download_expiry_date' => sanitize_text_field( $download_expiry_date ), 
				'download_count'       => '0', 
				'order_date'           => sanitize_text_field( $order_date ),
			) 
		);
		
		return $wpdb->insert_id;
		
		
	}
	
	/**
	 * Select single Download.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $order_id
	 * @param  int $item_id
	 * @return array $download
	 */
	public static function ctlggi_select_single_download( $order_id, $item_id ) {
		
	  if ( empty( $order_id ) && empty( $item_id ) )
	  return;
      
	  // Note: allow for public as order receipt shortcode using this method
	  // only admin allowed
	  //if ( ! current_user_can( 'manage_options' ) )
	  //return;
	  
	  $order_id = intval( $order_id );
	  $item_id  = intval( $item_id );
		
	  // $post_id is the order_id
	  global $wpdb;
		  
	    $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		
		$sql  = "
				SELECT id, order_id, item_id, user_id, user_email, order_key, download_limit, order_date, download_expiry_date, download_count  
				FROM $ctlggi_order_downloads
				WHERE order_id = $order_id and item_id = $item_id
				";
		return $download = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
	}
	
	/**
	 * Select Downloads by order ID.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $order_id
	 * @return array $downloads
	 */
	public static function ctlggi_select_downloads( $order_id ) {
		
	  if ( empty( $order_id ) )
	  return;
	  
	  // only admin allowed
	  if ( ! current_user_can( 'manage_options' ) )
	  return;
	  
	  $order_id = intval( $order_id );
		
	  // $post_id is the order_id
	  global $wpdb;
		  
	    $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		
		$sql  = "
				SELECT id, order_id, item_id, user_id, user_email, order_key, download_limit, order_date, download_expiry_date, download_count  
				FROM $ctlggi_order_downloads
				WHERE order_id = $order_id
				";
		return $downloads = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
	}
	
	/**
	 * Select Downloads by user ID.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $user_id
	 * @return array $downloads
	 */
	public static function ctlggi_select_downloads_by_user_id( $user_id ) {
		
	  if ( empty( $user_id ) )
	  return;
	  
	  $user_id = intval( $user_id );
		
	  // $post_id is the order_id
	  global $wpdb;
		  
	    $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		
		$sql  = "
				SELECT id, order_id, item_id, user_id, user_email, order_key, download_limit, order_date, download_expiry_date, download_count  
				FROM $ctlggi_order_downloads
				WHERE user_id = $user_id
				";
		return $downloads = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
	}

	/**
	 * Update Download by ID.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $id
	 * @param  int $download_limit
	 * @param  string $download_expiry_date
	 * @param  int $download_count
	 * @return void
	 */
	public static function ctlggi_update_download_by_id( $id, $download_limit, $download_expiry_date, $download_count ) {
		
	  if ( empty( $id ) && empty( $download_limit ) && empty( $download_expiry_date ) && empty( $download_count ) )
	  return;
		
	  // $post_id is the order_id
	  global $wpdb;
	  
	  $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
	  
		$result = $wpdb->update(
			$ctlggi_order_downloads, 
			array( 
				'download_limit'       => $download_limit,
				'download_expiry_date' => $download_expiry_date,
				'download_count'       => $download_count
			), 
			array(
				"id" => $id
			) 
		);
		
		return $result;
		
	}
	
	
}

?>