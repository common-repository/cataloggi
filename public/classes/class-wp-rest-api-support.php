<?php

/**
 * WordPress Rest API support class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Wp_Rest_Api_Support {

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
	 * Add REST API support to an already registered post type.
	 * Rest API support for "cataloggi" CPT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_cataloggi_rest_api_support() {
		global $wp_post_types;
	  
		//be sure to set this to the name of your post type!
		$post_type_name = 'cataloggi'; // CPT
		if( isset( $wp_post_types[ $post_type_name ] ) ) {
			$wp_post_types[$post_type_name]->show_in_rest = true;
			$wp_post_types[$post_type_name]->rest_base = $post_type_name;
			$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
		}
		//return $wp_post_types;
	}
	
	/**
	 * Add REST API support to an already registered post type.
	 * Rest API support for "cataloggi_orders" CPT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_cataloggi_orders_rest_api_support() {
		global $wp_post_types;
	  
		//be sure to set this to the name of your post type!
		$post_type_name = 'cataloggi_orders'; // CPT
		if( isset( $wp_post_types[ $post_type_name ] ) ) {
			$wp_post_types[$post_type_name]->show_in_rest = true;
			$wp_post_types[$post_type_name]->rest_base = $post_type_name;
			$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
		}
		//return $wp_post_types;
	}
	
	/**
	 * Add REST API support to an already registered taxonomy.
	 * Rest API support for "cataloggicat" Taxonomy
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_cataloggicat_rest_api_support() {
		global $wp_taxonomies;
	  
		//be sure to set this to the name of your taxonomy!
		$taxonomy_name = 'cataloggicat'; // taxonomy
	  
		if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
			$wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
			$wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
			$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
		}
		//return $wp_taxonomies;
	}
	
	
}

?>