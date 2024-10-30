<?php

/**
 * Shopping Cart - Error Log
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Error_Log {

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
	 * Save in error log.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  string $error_id
	 * @param  string $error_message
	 * @return array $error_log
	 */
    public static function ctlggi_error_log( $error_id, $error_message ) {
		
		if ( empty( $error_id ) || empty( $error_message ) )
		    return;
		
		// create array
		$error_log[] = array(
			'error_log_id'   => sanitize_text_field( $error_id ),
			'error_log_msg'  => sanitize_text_field( $error_message )
		);
		
		// save data to _ctlggi-error-log.txt
		CTLGGI_Error_Log::ctlggi_save_error_log_txt( $error_log );
		
		return $error_log;
	}

	/**
	 * Custom upload dir for Cataloggi files.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return string $uploadpath
	 */
	public static function ctlggi_custom_upload_dir_path() {
		$upload_dir = wp_upload_dir(); // wp upload dir ARRAY
		//print_r( $upload_dir );
		$upload_dir_path = $upload_dir['path'];
		// custom folder for uploads
		$customfolder = 'cataloggi-uploads'; // <- folder created on plugin activation
		// custom media folder dir path
		$uploadpath = $upload_dir_path . '/' . $customfolder;
		return $uploadpath;
	}

	/**
	 * Save error log data in .txt file in "cataloggi-uploads" folder.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $error_log
	 * @return void
	 */
	public static function ctlggi_save_error_log_txt( $error_log ) {
		if ( ! empty( $error_log ) ) { 
			$uploadpath = CTLGGI_Error_Log::ctlggi_custom_upload_dir_path();
			$filepath = $uploadpath . '/_ctlggi-error-log.txt';
			
			$log_date = date( 'Y-m-d H:i:s' );
			
			foreach($error_log as $key => $value )
			{
				//echo $key . '<br>';
				foreach($value as $error_id => $error_msg )
				{
					$error_log = $log_date . ' ' . sanitize_text_field( $error_id ) . ': ' . sanitize_text_field( $error_msg ) . PHP_EOL; // line break
					$error_log_txt = file_put_contents($filepath, $error_log , FILE_APPEND);
					
				}
			}
		}
		
	}

	/**
	 * Get error log .txt file content.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $error_log_txt_content_arr
	 */
    public static function ctlggi_get_error_log_txt_content() {
		$uploadpath = CTLGGI_Error_Log::ctlggi_custom_upload_dir_path();
		$filepath = $uploadpath . '/_ctlggi-error-log.txt';
		if ( file_exists( $filepath ) && wp_is_writable( $uploadpath ) ) {
			//$error_log_txt_content = file_get_contents($filepath);
			$error_log_txt_content_arr = explode("\n", file_get_contents($filepath));
			return $error_log_txt_content_arr;
		} else {
			//file_put_contents($filepath, ''); // empty file
			return;
		}
	}
	
	
}

?>