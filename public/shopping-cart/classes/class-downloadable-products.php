<?php

/**
 * Shopping Cart - Downloadable Products.
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Downloadable_Products {

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
	 * Get Downloadable items.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @return array $downloadable
	 */
	public static function ctlggi_order_get_downloadable_items( $post_id ) {
		
	  // $post_id is the order_id
	  global $wpdb;
		
	  // check post type
	  if ( get_post_type( $post_id ) == 'cataloggi_orders' ) {
		  
	    $ctlggi_order_items = $wpdb->prefix . 'ctlggi_order_items'; // table, do not forget about tables prefix
		$sql  = "
				SELECT order_item_id, order_item_type, order_id 
				FROM $ctlggi_order_items
				WHERE order_id = $post_id and order_item_type = 'downloadable'
				";
		$downloadable_items = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
		// check if query not empty
		if (count($downloadable_items)> 0){
			
		  $downloadable_item_meta      = CTLGGI_Downloadable_Products::ctlggi_order_downloadable_item_meta( $downloadable_items );
		  $postmeta_downloadable_items = CTLGGI_Downloadable_Products::ctlggi_postmeta_downloadable_items( $downloadable_item_meta );
		  $downloadable                = CTLGGI_Downloadable_Products::ctlggi_is_item_downloadable( $postmeta_downloadable_items );
          return $downloadable; // return downloadable items array
			
		} else {
		  return false;
		}
		
	  }	else {
		  return false;
	  }
		
	}
	
	/**
	 * Get Downloadable item meta.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $downloadable_items
	 * @return array $downloadable_item_meta
	 */
	public static function ctlggi_order_downloadable_item_meta( $downloadable_items ) {
		
		global $wpdb;
		$ctlggi_order_itemmeta = $wpdb->prefix . 'ctlggi_order_itemmeta'; // table, do not forget about tables prefix
		// downloadable items list
		if ( $downloadable_items ) {
			$downloadable_item_meta = array(); // create empty array
			// Using foreach loop without key
			foreach($downloadable_items as $item) {
				$order_item_id   = $item['order_item_id'];
			
				$sql  = "
						SELECT order_item_id, meta_key, meta_value
						FROM $ctlggi_order_itemmeta
						WHERE order_item_id = $order_item_id
						";
				// save each result in array		
			    $downloadable_item_meta[] = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A

			}
			// return array
			return $downloadable_item_meta; // return downloadable item meta array
		} else {
		  return false;
	    }
	}
	
	/**
	 * Return downloadable postmeta array.
	 *
	 * @global $wpdb
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $downloadable_item_meta
	 * @return array $downloadable_items
	 */
	public static function ctlggi_postmeta_downloadable_items( $downloadable_item_meta ) {
		
		global $wpdb;
		$postmeta = $wpdb->prefix . 'postmeta'; // table, do not forget about tables prefix
		// downloadable items list
		if ( $downloadable_item_meta ) {
			$downloadable_items = array(); // create empty array
			// Using foreach loop without key
			foreach($downloadable_item_meta as $meta_key => $meta_value) {
			//$item_id = $meta_key[0]['meta_value']; // this is the item_id, don't use fixed array $meta_key[0] as database order can be changed later
				
				foreach($meta_value as $key => $value )
				{
					// check if _item_id exist
					if ( $value['meta_key'] == '_item_id' ) {
						$item_id = $value['meta_value'];
						
						$sql  = "
								SELECT post_id, meta_key, meta_value
								FROM $postmeta
								WHERE post_id = $item_id
								";
						// save each result in array		
						$downloadable_items[] = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
						
					}
				}

			}
			// return array
			return $downloadable_items; // return downloadable items
		} else {
		  return false;
	    }
	}
	
	/**
	 * Check if item is downloadable.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $downloadable_items
	 * @return array $is_item_downloadable - value is 1 if downloadable
	 */
	public static function ctlggi_is_item_downloadable( $downloadable_items ) {
		if ( $downloadable_items ) {
			$is_item_downloadable = array(); // create empty array
			// Using foreach loop without key
			foreach($downloadable_items as $meta_key => $meta_value) {
				
				foreach($meta_value as $key => $value )
				{
					// check if meta_key "ctlggi_item_downloadable" exist and value "1"
					if ( $value['meta_key'] == '_ctlggi_item_downloadable' ) {
						//echo 'Post ID: ' . $value['post_id'] . ' Meta Key: ' . $value['meta_key'] . ' Meta Value: ' . $value['meta_value'] . '<br>';
						// check if value = 1
						if ( $value['meta_value'] == '1' ) { 
						   // save in array	
						   $is_item_downloadable[] = $meta_value; // return checked and valid array data
						}
					}
				}
			}	
			return $is_item_downloadable;
		} else {
		  return false;
	    }
	}
	
	
}

?>