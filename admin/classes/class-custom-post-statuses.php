<?php

/**
 * Admin Custom Post Statuses for Orders Custom Post Type.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Custom_Post_Statuses {

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
	 * Orders Custom Post Statuses
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return array $statuses
	 */
	public static function ctlggi_order_custom_post_statuses(){
		$statuses = array(
			'completed'       => 'Completed',
			'processing'      => 'Processing',
			'pending_payment' => 'Pending Payment',
			'failed'          => 'Failed',
			'cancelled'       => 'Cancelled',
			'refunded'        => 'Refunded',
			'on_hold'         => 'On Hold'
		);
		return $statuses;
	}

	/**
	 * Orders Custom Post Status: completed
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_completed(){
		 register_post_status( 'completed', array(
			  'label'                     => _x( 'Completed', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: processing
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_processing(){
		 register_post_status( 'processing', array(
			  'label'                     => _x( 'Processing', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: pending_payment
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_pending(){
		 register_post_status( 'pending_payment', array(
			  'label'                     => _x( 'Pending Payment', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: failed
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_failed(){
		 register_post_status( 'failed', array(
			  'label'                     => _x( 'Failed', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: cancelled
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_cancelled(){
		 register_post_status( 'cancelled', array(
			  'label'                     => _x( 'Cancelled', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: refunded
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_refunded(){
		 register_post_status( 'refunded', array(
			  'label'                     => _x( 'Refunded', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>' )
		 ) );
	}

	/**
	 * Orders Custom Post Status: on_hold
	 *
	 * @uses register_post_status()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_order_custom_post_status_on_hold(){
		 register_post_status( 'on_hold', array(
			  'label'                     => _x( 'On Hold', 'cataloggi' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>' )
		 ) );
	}
	
	
}

?>