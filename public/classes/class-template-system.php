<?php

/**
 * Amount Format and Calculation and Currency
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Template_System {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;
	
	private $default_template;

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
        $this->ctlggi_default_template();
	}
	
	/**
	 * Get the default template.
	 *
	 * @since   1.0.0
	 * @return  void 
	 */
	public function ctlggi_default_template(){
		if ( get_option('ctlggi_template_system_options') ) {
			$ctlggi_template_system_options = get_option('ctlggi_template_system_options');
			$default_template = $ctlggi_template_system_options['default_template'];	
		} else {
			$default_template = 'default';
		}
		$this->default_template = $default_template;
	}
	
	/**
	 * HOME PAGE - route archive- template.
	 *
	 * @since   1.0.0
	 * @param   void $template
	 * @return  void $template
	 */
	public function ctlggi_load_archive_template($template){
	  if(is_post_type_archive('cataloggi')){
		$theme_files = array('archive-cataloggi.php'); //origin
		$exists_in_theme = locate_template($theme_files, false);
		if($exists_in_theme == ''){
		  $template = CTLGGI_PLUGIN_DIR . 'public/templates/' . $this->default_template . '/archive-cataloggi.php'; // cataloggi catalog home page
		}
	  }
	  return $template;
	}	
	
	/**
	 * Single product view - route single - template.
	 *
	 * @global $post
	 * @global $wp_query
	 *
	 * @since   1.0.0
	 * @param   void $single_template
	 * @return  void $single_template
	 */
	public function ctlggi_load_single_template($single_template){
	  global $post, $wp_query;

	  $found = locate_template('single-cataloggi.php', false); // Custom Template
	  //$found = locate_template('single.php');
	  if($post->post_type == 'cataloggi' && $found == ''){ 
		  
		$single_template = CTLGGI_PLUGIN_DIR . 'public/templates/' . $this->default_template . '/single-cataloggi.php'; // cataloggi catalog single product view page-lm-cart.php
	  }
	  return $single_template;
	}
	
	/**
	 * Search Results - template.
	 *
	 * @global $post
	 * @global $wp_query
	 *
	 * @since   1.0.0
	 * @param   void $template_search
	 * @return  void $template_search
	 */
	public function ctlggi_template_chooser_search($template_search)   
	{    
	  global $post, $wp_query;
      
	  $post_type = get_query_var('post_type');  

	  if( $wp_query->is_search && $post_type == 'cataloggi' )   
	  {
		  
		$found = locate_template('search-cataloggi.php', false);
		
		if ( $found != '' ) {
			// load custom template search page
			$template_search = get_template_part( 'search', 'cataloggi' ); // page : search-cataloggi.php
			//exit();
		} else {
			// load default
			$template_search = CTLGGI_PLUGIN_DIR . 'public/templates/' . $this->default_template . '/search-cataloggi.php';
		}
		
	  } 
	  
	  return $template_search;   
	}
	
	/**
	 * Category - taxonomy - cataloggicat.
	 *
	 * @since   1.0.0
	 * @param   void $template
	 * @return  void $template
	 */
	public function ctlggi_load_taxonomy_template($template) 
	{	
		if ( is_tax('cataloggicat') ) 
		{
			$template = 'taxonomy-cataloggicat.php'; // Category - Custom Template
			
			if (file_exists ( get_template_directory() . '/' . $template) ) 
			{
				$template = get_template_directory() . '/' . $template;
			} else {
				$template = CTLGGI_PLUGIN_DIR . 'public/templates/' . $this->default_template . '/' . $template; // cataloggi catalog products list 
			}
		} 
		return $template;
	}
	
	
	
}

?>