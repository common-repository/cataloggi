<?php

/**
 * Developer mode class. 
 * Turn Cataloggi into a dveloper mode if CTLGGI_DEVELOPER_MODE is set to ON.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Developer_Mode {

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
	 * Checks if developer mode is ON or OFF.
	 *
	 * @since        1.0.0
     * @return       string $developer_mode
	 */
	public static function is_developer_mode() { 
	   $developer_mode = 'OFF';
	   if ( CTLGGI_DEVELOPER_MODE == 'ON' ) {
		  $developer_mode = 'ON'; 
	   }
	   return $developer_mode;
	}
	
	/**
	 * Display the given object.
	 *
	 * @since        1.0.0
     * @return       string $developer_mode
	 */
	public static function display_object( $object ) { 
	
		// only admin
		if ( ! current_user_can( 'activate_plugins' ) )
		    return;
	
	    $developer_mode = CTLGGI_Developer_Mode::is_developer_mode();
		if ( !empty( $object ) && $developer_mode == 'ON' ) {
			echo '<pre>';
			print_r( $object );
			echo '</pre>';
		} else {
			return;
		}
	}
	
	
}

?>