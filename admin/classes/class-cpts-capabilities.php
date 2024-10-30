<?php

/**
 * Custom Post Types Capabilities class.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_CPTS_Capabilities {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties. class-custom-post-types.php
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
	 * Manage CPT items capabilities. CTP: cataloggi
	 * 
	 * @uses get_role()
	 * @uses add_cap()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_manage_items_capabilities() {  
		// gets the role to add capabilities to
		$admin       = get_role( 'administrator' );
		$editor      = get_role( 'editor' );
		$author      = get_role( 'author' );
		$contributor = get_role( 'contributor' );
		$subscriber  = get_role( 'subscriber' );
		
		// cataloggi catalog custom roles
		$cataloggi_customer    = get_role( 'cataloggi_customer' );
		$cataloggi_subscriber  = get_role( 'cataloggi_subscriber' );
		
		
		// Important!!! capability: 'read' should exist, if not add
		// e.g. $subscriber->add_cap( 'read' );
		
		// replicate all the remapped capabilites from the custom post type
		$caps = array(
			'read_item', // all		  
			'edit_items', // author
			'publish_items', // author
			'edit_published_items', // author	
			'delete_published_items', // author
			'upload_files', // admin, editor, author	
			'edit_others_items', // only admin and editor
			'delete_others_items', // only admin and editor
			'read_private_items', // only admin
			'edit_private_items', // only admin
			'delete_private_items', // only admin
			'delete_items', // admin, editor, author, contributor		
		);
		// give all the capabilities to the administrator
		foreach ($caps as $cap) {
			$admin->add_cap( $cap );
		}

		#### ADD CAPS - EDITOR #### 
		// DEFAULTS: read_item, edit_items, publish_items, edit_published_items, delete_published_items, upload_files
		//$editor->add_cap( 'read_item' ); // default cap
		
		// Allows access to Administration Panel options:
		// Posts 
		// Posts > Add New 
		// Comments 
		// Comments > Awaiting Moderation 
		$editor->add_cap( 'edit_items' );
		
		$editor->add_cap( 'publish_items' ); // can publish items
		$editor->add_cap( 'edit_published_items' ); // can edit his published items
		$editor->add_cap( 'delete_published_items' ); // can delete his published items
		$editor->add_cap( 'upload_files' ); // allows to upload files
		// EDITOR: edit_others_items, delete_others_items
		$editor->add_cap( 'edit_others_items' ); // can edit others published items
		$editor->add_cap( 'delete_others_items' ); // can delete others published items
		
		// ADD CAPS - author
		//$author->add_cap( 'read_item' ); // default cap
		
		// ADD CAPS - contributor
		//$contributor->add_cap( 'read_item' ); // default cap
		
		// ADD CAPS - subscriber
		//$subscriber->add_cap( 'read_item' ); // default cap
		
		
		#### LIME ROLES ####
		
		// ADD CAPS - cataloggi_customer
		//$cataloggi_customer->add_cap( 'read_item' ); // default cap
		
		// ADD CAPS - cataloggi_subscriber
		//$cataloggi_subscriber->add_cap( 'read_item' ); // default cap
		
		// use remove_cap for remove caps
		
	}

	/**
	 * Manage CPT orders capabilities. CTP: cataloggi_orders
	 * 
	 * @uses get_role()
	 * @uses add_cap()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_manage_orders_capabilities() {
		// gets the role to add capabilities to
		$admin       = get_role( 'administrator' );
		$editor      = get_role( 'editor' );
		$author      = get_role( 'author' );
		$contributor = get_role( 'contributor' );
		$subscriber  = get_role( 'subscriber' );
		
		// cataloggi catalog custom roles
		$cataloggi_customer    = get_role( 'cataloggi_customer' );
		$cataloggi_subscriber  = get_role( 'cataloggi_subscriber' );
		
		// Important!!! capability: 'read' should exist, if not add
		
		// replicate all the remapped capabilites from the custom post type lesson
		$caps = array(
			'read_order', // all		  
			'edit_orders', // author
			'publish_orders', // author
			'edit_published_orders', // author	
			'delete_published_orders', // author
			'upload_files', // admin, editor, author	
			'edit_others_orders', // only admin and editor
			'delete_others_orders', // only admin and editor
			'read_private_orders', // only admin
			'edit_private_orders', // only admin
			'delete_private_orders', // only admin
			'delete_orders', // admin, editor, author, contributor	
		);
		// give all the capabilities to the administrator
		foreach ($caps as $cap) {
			$admin->add_cap( $cap );
		}
		
		// ADD CAPS - editor
		//$editor->add_cap( 'read_order' ); // default cap
		
		
		// ADD CAPS - author
		//$author->add_cap( 'read_order' ); // default cap
		
		// ADD CAPS - contributor
		//$contributor->add_cap( 'read_order' ); // default cap
		
		// ADD CAPS - subscriber
		//$subscriber->add_cap( 'read_order' ); // default cap
		
		
		#### LIME ROLES ####
		
		// ADD CAPS - cataloggi_customer
		//$cataloggi_customer->add_cap( 'read_order' ); // default cap
		
		// ADD CAPS - cataloggi_subscriber
		//$cataloggi_subscriber->add_cap( 'read_order' ); // default cap
		
		// use remove_cap for remove caps

	}
	
	/**
	 * Manage CPT: cataloggi,  Taxonomy "cataloggicat" capabilities.
	 * 
	 * @uses get_role()
	 * @uses add_cap()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_manage_cataloggicat_taxonomy_capabilities() {
		// gets the role to add capabilities to
		$admin       = get_role( 'administrator' );
		$editor      = get_role( 'editor' );
		$author      = get_role( 'author' );
		$contributor = get_role( 'contributor' );
		$subscriber  = get_role( 'subscriber' );
		
		// cataloggi catalog custom roles
		$cataloggi_customer    = get_role( 'cataloggi_customer' );
		$cataloggi_subscriber  = get_role( 'cataloggi_subscriber' );
		
		// Important!!! capability: 'read' should exist, if not add
		
		// capabilites CPT taxonomy "cataloggicat"
		$caps = array(
			'manage_cataloggicat', // displays the taxonomy in the admin navigation
			'edit_cataloggicat', // only admin and editor
			'delete_cataloggicat', // only admin and editor
			'assign_cataloggicat',
		);
		
		// give all the capabilities to the administrator
		foreach ($caps as $cap) {
			$admin->add_cap( $cap );
		}
		
		#### ADD CAPS - EDITOR #### 
        $editor->add_cap( 'assign_cataloggicat' ); // allow user to select category
		$editor->add_cap( 'manage_cataloggicat' ); 
		$editor->add_cap( 'edit_cataloggicat' ); 
		$editor->add_cap( 'delete_cataloggicat' ); 

	}
	

	
}

?>