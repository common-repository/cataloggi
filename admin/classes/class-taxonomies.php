<?php

/**
 * Taxonomies for Cataloggi.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Taxonomies {

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
	 * Custom slug for cataloggi categories taxonomy.
	 * 
	 * @since 1.0.0
	 * @return string $rewrite_slug
	 */
	public static function ctlggi_categories_tax_rewrite_slug()
	{
		// get options
		$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
		if ( !empty($ctlggi_save_settings_options['ctlggi_categories_tax_rewrite_slug']) ) {
			$rewrite_slug = $ctlggi_save_settings_options['ctlggi_categories_tax_rewrite_slug'];
		} else {
			$rewrite_slug = 'cataloggi-category'; // default slug
		}
		return $rewrite_slug;
	}

	/**
	 * Lime category taxonomy.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_cataloggicat_taxonomy() {
	 
		$labels = apply_filters( 'ctlggi_cataloggicat_taxonomy_labels', array(
			'name'              => __( 'Cataloggi Categories', 'cataloggi' ),
			'singular_name'     => __( 'Category', 'cataloggi' ),
			'search_items'      => __( 'Search Categories', 'cataloggi' ),
			'all_items'         => __( 'All Categories', 'cataloggi' ),
			'edit_item'         => __( 'Edit Category', 'cataloggi' ),
			'update_item'       => __( 'Update Category', 'cataloggi' ),
			'add_new_item'      => __( 'Add New Category', 'cataloggi' ),
			'new_item_name'     => __( 'New Item Category', 'cataloggi' ),
			'menu_name'         => __( 'Categories', 'cataloggi' ),
		) );
		
		$capabilities_cataloggicat = array(
			'manage_terms' => 'manage_cataloggicat',
			'edit_terms'   => 'edit_cataloggicat',
			'delete_terms' => 'delete_cataloggicat',
			'assign_terms' => 'assign_cataloggicat',
		);
		
		$rewrite_slug = CTLGGI_Taxonomies::ctlggi_categories_tax_rewrite_slug();
	 
		$args = apply_filters( 'ctlggi_cataloggicat_taxonomy_args', array(
			'labels'                => $labels,
			'public'                => true,
			'publicly_queryable'    => true,
			'query_var'             => true,
			'rewrite'               => array( 'slug' => $rewrite_slug ), // can be cataloggi-category, categories or anything.
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'show_in_nav_menus'     => true,
			'show_tagcloud'         => true,
			'capabilities'          => $capabilities_cataloggicat
			//'show_in_rest'          => true, // rest api
			//'rest_base'             => 'cataloggi-oategories', // rest api
			//'rest_controller_class' => 'WP_REST_Posts_Controller' // rest api
		) );
	 
		register_taxonomy( 'cataloggicat', 'cataloggi', $args );
		
		// Add new taxonomy, TAGS
		$labels_tags = array(
			'name' => _x( 'Tags', 'taxonomy general name' ),
			'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Tags' ),
			'popular_items' => __( 'Popular Tags' ),
			'all_items' => __( 'All Tags' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Tag' ), 
			'update_item' => __( 'Update Tag' ),
			'add_new_item' => __( 'Add New Tag' ),
			'new_item_name' => __( 'New Tag Name' ),
			'separate_items_with_commas' => __( 'Separate tags with commas' ),
			'add_or_remove_items' => __( 'Add or remove tags' ),
			'choose_from_most_used' => __( 'Choose from the most used tags' ),
			'menu_name' => __( 'Tags' ),
		); 
		
		register_taxonomy('cataloggitag','cataloggi',array(
			'hierarchical' => false,
			'labels' => $labels_tags,
			'show_ui' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'tag' ),
		));
		
	}
	

	
	
}

?>