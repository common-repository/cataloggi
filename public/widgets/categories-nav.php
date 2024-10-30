<?php

/**
 * Categories Navigation widget class.
 *
 * @package     cataloggi
 * @subpackage  Public/widgets
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
add_action( 'widgets_init', function(){
   register_widget( 'CTLGGI_Categories_Nav_Widget' );
});
 
class CTLGGI_Categories_Nav_Widget extends WP_Widget  {

	/**
	 * Sets up the widgets name etc.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		
		$widget_ops = array( 
			'classname'   => 'CTLGGI_Categories_Nav_Widget',
			'description' => __( 'Display the product categories on your sidebar.', 'cataloggi' )
		);
		
		parent::__construct( 'CTLGGI_Categories_Nav_Widget', 'Cataloggi - Product Categories', $widget_ops ); 

	}
	
	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {  

		// If we're not on a single post, bail.
		/*
		if ( ! is_single() || get_post_type() != 'cataloggi' ) {
			return;
		}
		*/
		
		// check if custom post type exist
		if ( post_type_exists('cataloggi') == false ) {
		  // echo 'CPT NOT exist';
		   return;
		}
	
		//store the options in variables
        $categories_nav_title = $instance['ctlggi_categories_nav_title'];
		
		// This function takes an associative array and returns its keys as variables. 
		// It enables us to echo out $before_widget instead of $args['before_widget'], thus simplifying our code a little.
		extract( $args ); 
		 
		echo $before_widget;
		
		if ( $categories_nav_title ) {
			echo $before_title . $categories_nav_title . $after_title;
		}
							 
		// nav menu
		echo '<div class="cataloggi-categories-nav-widget">';
		echo do_shortcode('[ctlggi_categories_nav]');
		echo '</div>';
		
		echo $after_widget;
		 
	}

	/**
	 * Outputs the options form on admin.
	 *
	 * @since 1.0.0
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		//Check if option1 exists, if its null, set default
		if ( isset( $instance[ 'ctlggi_categories_nav_title' ] ) ) {
			$categories_nav_title = $instance[ 'ctlggi_categories_nav_title' ];
		}
		else {
			$categories_nav_title = __('Categories', 'cataloggi');
		}
		
		?>	
		<p>
		<label for="<?php echo $this->get_field_id( 'ctlggi_categories_nav_title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'ctlggi_categories_nav_title' ); ?>" name="<?php echo $this->get_field_name( 'ctlggi_categories_nav_title' ); ?>" type="text" value="<?php echo esc_attr( $categories_nav_title ); ?>" />
		</p>
		<?php 
		
	}

	/**
	 * Processing widget options on save.
	 *
	 * @since 1.0.0
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['ctlggi_categories_nav_title'] = ( ! empty( $new_instance['ctlggi_categories_nav_title'] ) ) ? strip_tags( $new_instance['ctlggi_categories_nav_title'] ) : '';
		return $instance;
	}

	
	
}

?>