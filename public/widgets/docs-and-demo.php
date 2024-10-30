<?php

/**
 * Docs and Demo widget class.
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
   register_widget( 'CTLGGI_Docs_and_Demo' );
});
 
class CTLGGI_Docs_and_Demo extends WP_Widget  {

	/**
	 * Sets up the widgets name etc.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		
		$widget_ops = array( 
			'classname'   => 'CTLGGI_Docs_and_Demo',
			'description' => __( 'Display the product Docs and Demo widget on your sidebar.', 'cataloggi' )
		);
		
		parent::__construct( 'CTLGGI_Docs_and_Demo', 'Cataloggi - Docs and Demo Widget', $widget_ops ); 

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
		if ( ! is_single() || get_post_type() != 'cataloggi' ) {
			return;
		}
		
		// check if custom post type exist
		if ( post_type_exists('cataloggi') == false ) {
		  // echo 'CPT NOT exist';
		   return;
		}
		
		$post_id = get_the_ID();
		
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		
		// if shopping cart enabled
		if ( $ctlggi_cart_options['enable_shopping_cart'] != '1' ) 
		    return;
	    
		$title = '';
		//store the options in variables
        $title = $instance['ctlggi_docs_and_demo_title'];
		
		if ( ! empty($title) ) {
			$title = $title;
		}
		
		// This function takes an associative array and returns its keys as variables. 
		// It enables us to echo out $before_widget instead of $args['before_widget'], thus simplifying our code a little.
		extract( $args ); 
		 
		echo $before_widget;
		
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
							 
		// Extension Details shortcode
		echo do_shortcode('[ctlggi_docs_and_demo]'); 
		
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
		if ( isset( $instance[ 'ctlggi_docs_and_demo_title' ] ) ) {
			$title = $instance[ 'ctlggi_docs_and_demo_title' ];
		}
		else {
			$title = __('Documentation', 'cataloggi');
			//$title = '';
		}
		
		?>	
		<p>
		<label for="<?php echo $this->get_field_id( 'ctlggi_docs_and_demo_title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'ctlggi_docs_and_demo_title' ); ?>" name="<?php echo $this->get_field_name( 'ctlggi_docs_and_demo_title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
		$instance['ctlggi_docs_and_demo_title'] = ( ! empty( $new_instance['ctlggi_docs_and_demo_title'] ) ) ? strip_tags( $new_instance['ctlggi_docs_and_demo_title'] ) : '';
		return $instance;
	}

	
	
}

?>