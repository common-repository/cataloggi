<?php

/**
 * The plugin bootstrap file
 *
 * The plugin uses the WordPress Plugin Boilerplate: 
 * A foundation for WordPress Plugin Development that aims to provide a clear and consistent guide for building your plugins.
 * Source: https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Cataloggi
 * @author            Codeweby - Attila Abraham
 * @copyright         Copyright (c) Cataloggi - Attila Abraham
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Cataloggi
 * Plugin URI:        https://codeweby.com/products/cataloggi-ecommerce-product-catalog/
 * Description:       Cataloggi is an easy to use responsive eCommerce Product Catalog that is free to use for anyone.
 * Version:           1.4.0
 * Author:            Codeweby
 * Author URI:        https://codeweby.com/products/cataloggi-ecommerce-product-catalog/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cataloggi
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin path
define( 'CTLGGI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// Plugin URL
define( 'CTLGGI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// Plugin FILE
define( 'CTLGGI_PLUGIN_FILE', __FILE__ );
// Developer Mode
define( 'CTLGGI_DEVELOPER_MODE', 'OFF' ); // values OFF or ON 
// CSS
define( 'CTLGGI_CSS_MODE', 'css' ); // css or css-min
// JS
define( 'CTLGGI_JS_MODE', 'js-min' ); // js or js-min

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ctlggi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ctlggi() {
		
	$plugin = new Cataloggi();
	$plugin->run();

}
run_ctlggi();

/**
 * The code that runs during plugin activation.
 * Callback function for `register_activation_hook()`.
 *
 * @since    1.0.0
 */
function activate_ctlggi( $network_wide ) {
	
	global $wpdb;

    if ( ! current_user_can( 'activate_plugins' ) )
        return;
	
	require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-activator.php';
	
	// These functions call remains here since it needs to happen globally.
	CTLGGI_Activator::ctlggi_order_items_table_install();
	CTLGGI_Activator::ctlggi_order_itemmeta_table_install();
	CTLGGI_Activator::ctlggi_order_downloads_table_install();
	
	// Check if the plugin is being network-activated or not.
	if ( $network_wide ) {
		// Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
		if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
		  $site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
		} else {
		  $site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;" );
		}
		
		// Install the plugin for all these sites.
		foreach ( $site_ids as $site_id ) {
		  switch_to_blog( $site_id );
		  CTLGGI_Activator::ctlggi_install_single_site();
		  restore_current_blog();
		}
	} else {
	    CTLGGI_Activator::ctlggi_install_single_site();
	}
	
} 

/**
 * The code that runs during plugin deactivation.
 *
 * @since    1.0.0
 */
function deactivate_ctlggi( $network_wide ) {
	
	global $wpdb;
	
    if ( ! current_user_can( 'activate_plugins' ) )
        return;
	
	require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-deactivator.php';
	
	// Check if the plugin is being network-activated or not.
	if ( $network_wide ) {
		// Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
		if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
		  $site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
		} else {
		  $site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;" );
		}
		
		// Deactivate the plugin for all these sites.
		foreach ( $site_ids as $site_id ) {
		  switch_to_blog( $site_id );
		  CTLGGI_Deactivator::ctlggi_plugin_deactivator_delete_options();
		  CTLGGI_Deactivator::wp_cron_clean_the_scheduler();
		  CTLGGI_Deactivator::ctlggi_flush_rewrite_rules();
		  restore_current_blog();
		}
	} else {
	    CTLGGI_Deactivator::ctlggi_plugin_deactivator_delete_options();
		CTLGGI_Deactivator::wp_cron_clean_the_scheduler();
		CTLGGI_Deactivator::ctlggi_flush_rewrite_rules();
	}
	
}

/**
 * The code that runs during plugin uninstallation.
 *
 * @since    1.0.0
 */
function uninstall_ctlggi() {
	
	global $wpdb;
	
    if ( ! current_user_can( 'activate_plugins' ) )
        return;
		
	//this check makes sure that this file is called manually.
	if (!defined("WP_UNINSTALL_PLUGIN"))
		return;
	
	require_once CTLGGI_PLUGIN_DIR . 'includes/class-ctlggi-uninstall.php';
	
	// Check if we are on a Multisite or not.
	if ( is_multisite() ) {
		// Retrieve all site IDs from all networks (WordPress >= 4.6 provides easy to use functions for that).
		if ( function_exists( 'get_sites' ) ) {
		  $site_ids = get_sites( array( 'fields' => 'ids' ) );
		} else {
		  $site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
		}
		
		// Uninstall the plugin for all these sites.
		foreach ( $site_ids as $site_id ) {
		  switch_to_blog( $site_id );
		  CTLGGI_Uninstall::ctlggi_plugin_uninstaller_delete_options();
		  restore_current_blog();
		}
	} else {
	    CTLGGI_Uninstall::ctlggi_plugin_uninstaller_delete_options();
	}
	
}

register_activation_hook( __FILE__, 'activate_ctlggi' );
register_deactivation_hook( __FILE__, 'deactivate_ctlggi' );
register_uninstall_hook ( __FILE__, 'uninstall_ctlggi' );

?>
