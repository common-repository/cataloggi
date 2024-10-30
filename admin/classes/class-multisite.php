<?php

/**
 * Multisite, fire actions on new site activation.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Multisite {

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
	 * Checks to see if a plugin is "Network Active" on a multi-site installation of WordPress.
	 *
	 * @since        1.0.0
     * @return       int   $network_active
	 */
	public function ctlggi_is_plugin_network_active()
	{   
	    $network_active = '0'; // default
		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		$plugin_base_name = plugin_basename(CTLGGI_PLUGIN_FILE); // e.g. plugin-directory/plugin-file.php
		
		if ( is_plugin_active_for_network( $plugin_base_name ) ) {
			// Plugin is activated
			$network_active = '1';
		}
		
		return $network_active;
	}
	
	/**
	 * Action triggered whenever a new blog is created within a multisite network. 
	 *
	 * @global       $wpdb
	 *
	 * @since        1.0.0
	 * @param int    $blog_id Blog ID.
	 * @param int    $user_id User ID.
	 * @param string $domain  Site domain.
	 * @param string $path    Site path.
	 * @param int    $site_id Site ID. Only relevant on multi-network installs.
	 * @param array  $meta    Meta data. Used to set initial site options.
     * @return       void
	 */
	public function ctlggi_multisite_new_site_activation( $blog_id, $user_id, $domain, $path, $site_id, $meta )
	{ 
		global $wpdb;
		
		//echo 'Im here'; die();
		
		if ( empty( $blog_id ) )
			return;
	
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		
		// check if plugin network active
		if ( $this->ctlggi_is_plugin_network_active() != '1' )
			return;
		
		require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-activator.php';
		
		switch_to_blog( $blog_id );
		
		CTLGGI_Activator::ctlggi_order_items_table_install();
		CTLGGI_Activator::ctlggi_order_itemmeta_table_install();
		CTLGGI_Activator::ctlggi_order_downloads_table_install();
		CTLGGI_Activator::ctlggi_install_single_site();
		
		restore_current_blog();
	}
	
	/**
	 * Action triggered whenever a blog is deleted within a multisite network. 
	 *
	 * @global       $wpdb
	 *
	 * @since        1.0.0
	 * @param int    $blog_id Blog ID
	 * @param bool   $drop True if blog's table should be dropped. Default is false.
     * @return       void
	 */
	public function ctlggi_multisite_site_deletion( $blog_id, $drop )
	{ 
		global $wpdb;

		if ( empty( $blog_id ) )
			return;
			
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
			
		// check if plugin network active
		//if ( $this->ctlggi_is_plugin_network_active() != '1' )
			//return;	
			
		require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-uninstall.php';
		
		switch_to_blog( $blog_id );
		//CTLGGI_Uninstall::ctlggi_plugin_uninstaller_delete_options();
		// do something
		restore_current_blog();
			
	}
	
	/**
	 * Deleting the table whenever a blog is deleted.
	 *
	 * @global $wpdb
	 *
	 * @since 1.0.0
	 * @param array $tables
     * @return array $tables
	 */
	public function ctlggi_delete_tables_on_site_deletion( $tables ) {
		global $wpdb;
		
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		
		$tables[] = $wpdb->prefix . 'ctlggi_order_items';
		$tables[] = $wpdb->prefix . 'ctlggi_order_itemmeta';
		$tables[] = $wpdb->prefix . 'ctlggi_order_downloads';
		return $tables;
	}
	
	
}

?>