<?php

/**
 * Manage custom tables on new version release. For WP repository auto updates.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Manage_Custom_Tables {

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
	 * Manage the order items table. E.g. If new field added etc.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public static function ctlggi_manage_order_items_table() 
	{
		$current_version = '1.0.1';
		$installed_ver = get_option( 'ctlggi_order_items_db_version' );
		
		if ( $installed_ver != $current_version ) {
			
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'ctlggi_order_items'; // do not forget about tables prefix 
		    //table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();
			$sql =
			"CREATE TABLE {$table_name} (
			order_item_id bigint(20) NOT NULL AUTO_INCREMENT,
			order_item_name longtext NOT NULL,
			payment_type varchar(255) NOT NULL,
			order_item_type varchar(255) NOT NULL,
			price_option_id bigint(20) NOT NULL,
			price_option_name varchar(255) NOT NULL,
			order_id bigint(20) NOT NULL,
			PRIMARY KEY (order_item_id)
			) $charset_collate;";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			
			update_option( 'ctlggi_order_items_db_version', $current_version ); // save version in options
		}
	}
	
	
}

?>