<?php

/**
 * Manage Downloads class.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Admin_Manage_Downloads {

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
	 * Update download data, process Ajax.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_update_download_data_form_process() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		$id                   = intval( $formData['ctlggi_download_id'] );
		$download_limit       = intval( $formData['ctlggi_download_limit'] );
		$download_expiry_date = sanitize_text_field( $formData['ctlggi_download_expiry_date'] );
		$download_count       = intval( $formData['ctlggi_download_count'] );
		
		if ( $download_limit == 0 ) {
			$download_limit = '';
		}
		
		// update database
		$result = CTLGGI_DB_Order_Downloads::ctlggi_update_download_by_id( $id, $download_limit, $download_expiry_date, $download_count );
        
		$print = __(' saved ', 'cataloggi');
		// return json
		echo json_encode(array('result'=>true, 'message'=>$print  ));
		
	    exit; // don't forget to exit!		
		
	}
	
	
}

?>