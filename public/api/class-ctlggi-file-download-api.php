<?php

/**
 * File Downloader API
 *
 * @package     CTLGGI
 * @subpackage  public/api
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_File_Download_Api {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;
	
	// endpoint
	const ENDPOINT = 'ctlggi-file-dw-api';
	
	// query action keys
	const KEY_DOWNLOAD      = 'download';
	const KEY_FREEDOWNLOAD  = 'freedownload';
	const KEY_SLMDOWNLOAD   = 'slmdownload';
	
	private $error_log_id = '';
    private $result = '';
	private $message = '';
	
	private $action;
	private $dwfile;

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
	 * Registering a new rewrite endpoint for accessing the API.
	 *
	 * @access public
	 * @param array $rewrite_rules WordPress Rewrite Rules
	 */
	public function endpoint( $rewrite_rules ) {
		add_rewrite_endpoint( self::ENDPOINT, EP_ALL );
	}

	/**
	 * Registering query vars for API access.
	 *
	 * @access public
	 * @param array $vars Query vars
	 * @return string[] $vars New query vars
	 */
	public function query_vars_filter( $vars ) {
        
		// example: http://cataloggi.com/ctlggi-file-dw-api/?action=freedownload&dwfile=42423432
		// required keys
		$vars[] = 'action'; // values: download, freedownload
		$vars[] = 'dwfile'; // file data

		return $vars;
	}
	
	/**
	 * Listens for the API and then processes the API requests.
	 *
	 * @global $wp_query
	 *
	 * @access public
	 * @global $wp_query
	 * @return void
	 */
	public function api_listener() {

		global $wp_query;
		
		// If our endpoint isn't hit, just return
		if ( ! isset( $wp_query->query_vars[self::ENDPOINT] ) ) {
			return;
		}
		
		// CHECK IF ALL REQUIRED KEYS PROVIDED
		if ( ! isset( $wp_query->query_vars['action'] ) ) {
			$this->error_log_id  = 'file_download_API_no_ation_KEY';
			$this->result        = 'error';
			$this->message       = __('No action KEY provided.', 'cataloggi');
		} elseif ( ! isset( $wp_query->query_vars['dwfile'] ) ) {
			$this->error_log_id  = 'file_download_API_no_dwfile_KEY';
			$this->result        = 'error';
			$this->message       = __('No dwfile KEY provided.', 'cataloggi');
		} 
		
		if ( empty( $this->result ) ) {
			
			// required values
			$action   = $wp_query->query_vars['action'];
			$dwfile   = $wp_query->query_vars['dwfile'];
			
			// CHECK IF REQUIRED KEYS VALUES ARE EMPTY
			if ( empty( $action ) ) {
				$this->error_log_id  = 'file_download_API_action_value_empty';
				$this->result        = 'error';
				$this->message       = __('Action value is empty.', 'cataloggi');
			} elseif ( empty( $dwfile ) ) {
				$this->error_log_id  = 'file_download_API_dwfile_value_empty';
				$this->result        = 'error';
				$this->message       = __('Download file value is empty.', 'cataloggi');
			}
			
			if ( empty( $this->result ) ) 
			{
				// set properties
				$this->action  = $action;
				$this->dwfile  = $dwfile;
				
				if ( $this->action == self::KEY_DOWNLOAD ) {
					// process download Premium products
					$this->process_download();
				} elseif ( $this->action == self::KEY_FREEDOWNLOAD ) {
					// process FREE download
					$this->process_free_download();	
				} elseif ( $this->action == self::KEY_SLMDOWNLOAD ) {
					// process software licensing plugin download
					$this->process_slm_download();	
				} else {
					$this->error_log_id  = 'file_download_API_invalid_action_key_provided';
					$this->result        = 'error';
					$this->message       = __('Invalid action key provided.', 'cataloggi');
				}
			}

		}
		// output error message
		$this->output_api_response();
	}
	
	/**
	 * Process Download.
	 *
	 * @access private
	 * @return void
	 */
    private function process_download() {
		
		$dw_file_data = CTLGGI_Helper::ctlggi_base64url_decode($data=$this->dwfile); // decode file name
		
		$dw_file_data_obj = json_decode( $dw_file_data ); // Translate into an object
		$dw_file_data_arr = json_decode( $dw_file_data, true ); // Translate into an array
		
		$post_id   = $dw_file_data_arr['post_id'];
		$order_id  = $dw_file_data_arr['order_id'];
		$order_key = $dw_file_data_arr['order_key'];
		
		// probably no post id, order id, order key set
	    if ( empty( $post_id ) && empty( $order_id ) && empty( $order_key ) )
	    return;
		
		$order_key_meta = get_post_meta( $order_id, '_order_key', true );
		
		// check order keys, return if order keys do not match 
		if ( $order_key_meta != $order_key )
		return;
		
		if ( ! empty( $post_id ) && ! empty( $order_id) ) {
		  
			$order_status = get_post_meta( $order_id, '_order_status', true );
			// send download file(s) data only if order status = completed
			if ( $order_status == 'completed' ) {
			
				// select from postmeta where post_id = $post_id
				//Show the first value of the specified key inside a loop
				$item_file_url = get_post_meta( $post_id, '_ctlggi_item_file_url', true );
				// Check if has a value
				if ( ! empty( $item_file_url ) ) {
				
					// get base name
					$fileName = basename($item_file_url); // basename() has a bug when processes Asian characters like Chinese. Maybe use pathinfo() !!!!
					$filePath = $this->media_upload_dir();
					
					$download_file = $filePath . $fileName;
				   
					if ( !function_exists( 'wp_check_filetype' ) ) { 
					require_once ABSPATH . WPINC . '/functions.php'; 
					} 
				  
					// Optional. Key is the file extension with value as the mime type. 
					$mimes = null; 
					
					// check allowed mimes
					//$mimes = $this->allowed_mime_types();
					
					// NOTICE! Understand what this does before running. 
					// https://codex.wordpress.org/Function_Reference/wp_check_filetype
					$fileMime = wp_check_filetype($download_file, $mimes); // File name or path and mime types
					$file_mime_ext  = $fileMime['ext']; // e.g. zip
					$file_mime_type = $fileMime['type']; // e.g. application/zip
					
					/*
					$allowed_mime_types = $this->allowed_mime_types();
					// check if mime exist, but not necesarry for downloadable products
					foreach ($allowed_mime_types as $key => $value) { 
					 if ( $key == $file_mime_ext) {
						 echo $key . ' ' . $value;
					 }
					}
					*/
					  
					if ( ! empty($file_mime_type) ) {
					  
						$current_date = date('Y-m-d H:i:s');
						
						// get download_expiry_date from "ctlggi_order_downloads" db
						// select data by order_id and item_id 
						$download = $this->order_downloads_by_ids( $order_id, $item_id=$post_id );
						
						/*
						echo '<pre>';
						print_r($download);
						echo '</pre>';
						exit;
						*/
						
						if ( !empty( $download ) ) {
						  $download_limit       = $download[0]['download_limit'];
						  $order_date           = $download[0]['order_date'];
						  $download_expiry_date = $download[0]['download_expiry_date'];
						  $download_count       = $download[0]['download_count'];
						}
						
						/*
						echo '<pre>';
						print_r($download_limit);
						echo '</pre>';
						exit;
						*/
						
						// if empty or 0 unlimited downloads
						if ( ! empty($download_limit) || $download_limit != 0 ) {
							// check download limit
							if ( $download_limit <= $download_count ) {
								$this->error_log_id  = 'file_download_API_download_Limit_exceeded';
								$this->result        = 'error';
								$this->message       = __('Download Limit Exceeded.', 'cataloggi');
							}
						}
						
						// check if download_expiry_date = 0000-00-00, if 0000-00-00 never expires
						if ( $download_expiry_date != '0000-00-00' ) {
							// check expiry date
							if (  $download_expiry_date < $current_date ) {
								$this->error_log_id  = 'file_download_API_download_access_expired';
								$this->result        = 'error';
								$this->message       = __('Download Access Expired.', 'cataloggi');
							}
						}
						
						// PROCESS DOWNLOAD
						if ( $this->result != 'error' ) {
						    
							$download_count = $download_count + 1; 
							// update download count
							$this->order_downloads_update_download_count( $order_id, $item_id=$post_id, $download_count );

							// download file
							$this->execute_headers( $filePath, $fileName, $file_mime_type );
						  
						}
					
					} else {
						$this->error_log_id  = 'file_download_API_mime_type_not_found';
						$this->result        = 'error';
						$this->message       = __('Mime type not found.', 'cataloggi');	
					}
					   
				} else {
					$this->error_log_id  = 'file_download_API_no_file_found';
					$this->result        = 'error';
					$this->message       = __('No File Found.', 'cataloggi');	
				}
				
			} else {
				$this->error_log_id  = 'file_download_API_invalid_request_order_status_not_completed';
				$this->result        = 'error';
				$this->message       = __('Invalid request.', 'cataloggi');
			}
			
		} else {
			$this->error_log_id  = 'file_download_API_invalid_request_post_id_or_order_id_not_match';
			$this->result        = 'error';
			$this->message       = __('Invalid request.', 'cataloggi');
		}
		
	}

	/**
	 * Get Order downloads by ids.
	 *
	 * @global $wpdb
	 *
	 * @access private
	 * @param int $order_id
	 * @param int $item_id
	 * @return array $download
	 */
	private function order_downloads_by_ids( $order_id, $item_id ) {
		
	  if ( empty( $order_id ) && empty( $item_id ) )
	  return;
		
	  // $post_id is the order_id
	  global $wpdb;
		  
	    $ctlggi_order_downloads = $wpdb->prefix . 'ctlggi_order_downloads'; // table, do not forget about tables prefix
		
		$sql  = "
				SELECT id, order_id, item_id, user_id, user_email, order_key, download_limit, order_date, download_expiry_date, download_count  
				FROM $ctlggi_order_downloads
				WHERE order_id = $order_id and item_id = $item_id
				";
		return $download = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
	}

	/**
	 * Order downloads update download count.
	 *
	 * @global $wpdb
	 *
	 * @access private
	 * @param int $order_id
	 * @param int $item_id
	 * @param int $download_count
	 * @return void
	 */
	private function order_downloads_update_download_count( $order_id, $item_id, $download_count ) {
		
	    if ( empty( $order_id ) && empty( $item_id ) )
	    return;
	  
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_downloads';
		
		$wpdb->update( 
			$table_name, 
			array( 
				'download_count' => $download_count, 
			),
			array( 
				'order_id'       => $order_id, 
				'item_id'        => $item_id, 
			) 
		);
		
	}
	
	/**
	 * Process FREE Download.
	 *
	 * @access private
	 * @return void
	 */
    private function process_free_download() {
		
		$dw_file_data = CTLGGI_Helper::ctlggi_base64url_decode($data=$this->dwfile); // decode file name
		
		$dw_file_data_obj = json_decode( $dw_file_data ); // Translate into an object
		$dw_file_data_arr = json_decode( $dw_file_data, true ); // Translate into an array
		
		$post_id   = $dw_file_data_arr['post_id'];
		
		/*
		echo '<pre>';
		print_r($dw_file_data);
		echo '</pre>';
		exit;
		*/
		
		// probably no post id
	    if ( empty( $post_id ) )
	    return;
		
		// check post type
		if ( get_post_type( $post_id ) == 'cataloggi' ) {
			// show download button for free items
			$item_price = get_post_meta( $post_id, '_ctlggi_item_price', true );
			$item_sale_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price);
			
			// if price is 0 or no price set
			if ( $item_sale_price_hidden == '0' ) {
				$enable_price_options = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
				// check if price option not enabled
				if ( $enable_price_options != '1' ) {
					$item_downloadable  = get_post_meta( $post_id, '_ctlggi_item_downloadable', true ); // checkbox
					// check if item downloadable
					if ( $item_downloadable == '1' ) {
						
						$item_file_name  = get_post_meta( $post_id, '_ctlggi_item_file_name', true );
						$item_file_url   = get_post_meta( $post_id, '_ctlggi_item_file_url', true );
						// check if file name and url not empty
						if ( ! empty($item_file_name) && ! empty($item_file_url) ) {
						  
							// get base name
							$fileName = basename($item_file_url); // basename() has a bug when processes Asian characters like Chinese. Maybe use pathinfo() !!!!
							$filePath = $this->media_upload_dir();
							
							$download_file = $filePath . $fileName; // full path
							   
							  if ( !function_exists( 'wp_check_filetype' ) ) { 
								require_once ABSPATH . WPINC . '/functions.php'; 
							  } 
							  
							  // Optional. Key is the file extension with value as the mime type. 
							  $mimes = null; 
							  
							  // check allowed mimes
							  //$mimes = $this->allowed_mime_types();
							  
							  // NOTICE! Understand what this does before running. 
							  // https://codex.wordpress.org/Function_Reference/wp_check_filetype
							  $fileMime = wp_check_filetype($download_file, $mimes); // File name or path and mime types
							  $file_mime_ext  = $fileMime['ext']; // e.g. zip
							  $file_mime_type = $fileMime['type']; // e.g. application/zip
							  
							  if ( !empty($file_mime_type) ) {
								  
								  //  update item download count
								  $download_count  = get_post_meta( $post_id, '_ctlggi_item_download_count', true );
								  $download_count = $download_count + 1; 
								  update_post_meta( $post_id, '_ctlggi_item_download_count', $download_count );
								  
								  // download file
								  $this->execute_headers( $filePath, $fileName, $file_mime_type );
							  }
						  
						} else {
							$this->error_log_id  = 'file_download_API_free_download_no_file_found';
							$this->result        = 'error';
							$this->message       = __('No File Found.', 'cataloggi');
						}
						
					}
				}
			}
		}
		
	}
	
	/**
	 * Process Software Licensing Downloads
	 * do not check download_expiry_date, download_limit and do not update download_limit
	 * check mime type and allow only zip files
	 *
	 * @access private
	 * @return void
	 */
    private function process_slm_download() {
		
		$dw_file_data = CTLGGI_Helper::ctlggi_base64url_decode($data=$this->dwfile); // decode file name
		
		$dw_file_data_obj = json_decode( $dw_file_data ); // Translate into an object
		$dw_file_data_arr = json_decode( $dw_file_data, true ); // Translate into an array
		
		/*
		echo '<pre>';
		print_r($dw_file_data_arr);
		echo '</pre>';
		exit;
		*/
		
		if ( ! empty( $dw_file_data_arr ) )
		{
			// probably no post id, order id, order key set
			if ( empty( $dw_file_data_arr['post_id'] ) && empty( $dw_file_data_arr['order_id'] ) && empty( $dw_file_data_arr['order_key'] ) )
			    return;
				
			$post_id   = $dw_file_data_arr['post_id']; // item id
			$order_id  = $dw_file_data_arr['order_id'];
			$order_key = $dw_file_data_arr['order_key'];
			
			$currentdate = date("Y-m-d H:i:s");
			
			$order_key_meta = get_post_meta( $order_id, '_order_key', true );
			
			// check order keys, return if order keys do not match 
			if ( $order_key_meta != $order_key )
			    return;
			
			if ( ! empty( $post_id ) && ! empty( $order_id) ) {
			  
				$order_status = get_post_meta( $order_id, '_order_status', true );
				// send download file(s) data only if order status = completed
				if ( $order_status == 'completed' ) {
				
					// get the license data
					$license_data = $this->get_license_data($order_id, $item_id=$post_id);
					
					if ( ! empty($license_data) ) {
						
						//check if license expired
						if ( $license_data['license_expires'] < $currentdate ) {
							// license expired
							$expiry_date = $license_data['license_expires'];
							$expiry_date  = date('M-d, Y',strtotime($expiry_date)); // format expired date
							$this->error_log_id  = 'file_download_API_failed_license_expired';
							$this->result        = 'error';
							$this->message       = __('Unable to process lisense expired on ', 'cataloggi') . ' ' . $expiry_date;	
						}
						//check if license disabled
						elseif ( $license_data['disabled_license'] == 1 ) {
							$this->error_log_id  = 'file_download_API_failed_license_disabled';
							$this->result        = 'error';
							$this->message       = __('Unable to process license disabled by the provider.', 'cataloggi');
						}

						$item_file_url = get_post_meta( $post_id, '_ctlggi_item_file_url', true );
						// Check if has a value
						if ( ! empty( $item_file_url ) ) {
						
							// get base name
							$fileName = basename($item_file_url); // basename() has a bug when processes Asian characters like Chinese. Maybe use pathinfo() !!!!
							$filePath = $this->media_upload_dir();
							
							$download_file = $filePath . $fileName;
						   
							if ( !function_exists( 'wp_check_filetype' ) ) { 
							require_once ABSPATH . WPINC . '/functions.php'; 
							} 
						  
							// Optional. Key is the file extension with value as the mime type. 
							$mimes = null; 
							
							// check allowed mimes
							//$mimes = $this->allowed_mime_types();
							
							// NOTICE! Understand what this does before running. 
							// https://codex.wordpress.org/Function_Reference/wp_check_filetype
							$fileMime = wp_check_filetype($download_file, $mimes); // File name or path and mime types
							$file_mime_ext  = $fileMime['ext']; // e.g. zip
							$file_mime_type = $fileMime['type']; // e.g. application/zip
							
							/*
							$allowed_mime_types = $this->allowed_mime_types();
							// check if mime exist, but not necesarry for downloadable products
							foreach ($allowed_mime_types as $key => $value) { 
							 if ( $key == $file_mime_ext) {
								 echo $key . ' ' . $value;
							 }
							}
							*/
							  
							if ( ! empty($file_mime_type) ) 
							{
								if ( $file_mime_type != 'application/zip' ) {
									$this->error_log_id  = 'file_download_API_wrong_mime_type_only_zip_allowed';
									$this->result        = 'error';
									$this->message       = __('Wrong mime type only zip files allowed.', 'cataloggi');	
								}
								
								if ( $this->result != 'error' ) {
									###### SUCCESS PROCESS DOWNLOAD ########
									$this->execute_headers( $filePath, $fileName, $file_mime_type );
								}
							
							} else {
								$this->error_log_id  = 'file_download_API_mime_type_not_found';
								$this->result        = 'error';
								$this->message       = __('Mime type not found.', 'cataloggi');	
							}
							   
						} else {
							$this->error_log_id  = 'file_download_API_no_file_found';
							$this->result        = 'error';
							$this->message       = __('No File Found.', 'cataloggi');	
						}
					} else {
						$this->error_log_id  = 'file_download_API_cannot_find_license';
						$this->result        = 'error';
						$this->message       = __('Cannot find license.', 'cataloggi');	
					}
					
				} else {
					$this->error_log_id  = 'file_download_API_invalid_request_order_status_not_completed';
					$this->result        = 'error';
					$this->message       = __('Invalid request.', 'cataloggi');
				}
				
			} else {
				$this->error_log_id  = 'file_download_API_invalid_request_post_id_or_order_id_not_match';
				$this->result        = 'error';
				$this->message       = __('Invalid request.', 'cataloggi');
			}
		}
	}
	
	/**
	 * Software Licensing, Get license data by order and item id
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 * @param int $item_id
	 * @return array $license_data 
	 */
	private function get_license_data($order_id, $item_id) {
		
		if ( empty( $order_id ) && empty( $item_id ) )
		return;
		
		global $wpdb;
		
		// get licenses by '_ctlggi_lic_order_id'
		$postmeta = $wpdb->prefix . 'postmeta'; // table, do not forget about tables prefix
		$sql = "
			SELECT post_id, meta_key, meta_value
			FROM $postmeta 
			WHERE meta_value='$order_id' AND meta_key='_ctlggi_lic_order_id'
		";
		$getresults = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A or use OBJECT
		
		if ( !empty($getresults) ) {
			// defaults
			$license_key       = '';
			$license_act_limit = '';
			$license_expires   = '';
			foreach($getresults as $getresult )
			{
				$lic_post_id = $getresult['post_id']; // license post id
				$license_item_id = get_post_meta( $lic_post_id, '_ctlggi_lic_item_id', true );
				
				if ( $item_id == $license_item_id) {
					// get the license data
					$license_key       = get_post_meta( $lic_post_id, '_ctlggi_lic_license_key', true );
					$license_act_limit = get_post_meta( $lic_post_id, '_ctlggi_lic_license_act_limit', true );
					$license_expires   = get_post_meta( $lic_post_id, '_ctlggi_lic_license_expires', true );
					$disabled_license  = get_post_meta( $lic_post_id, '_ctlggi_lic_disabled_license', true );
					
					if ( $license_act_limit == '' ) {
						// unlimited activations
						$license_act_limit = __( 'Unlimited', 'cataloggi' );
					}
					
					// create array
					$license_data = array(	
						'license_key'       => $license_key,
						'activation_limit'  => $license_act_limit,
						'license_expires'   => $license_expires,
						'disabled_license'  => $disabled_license
					);
					return $license_data;
				}
			}
		} else {
			return;
		}
		
	}
	
	/**
	 * Custom Media Upload Dir for Cataloggi files.
	 *
	 * @access private
	 * @return string $upload_dir_path
	 */
	private function media_upload_dir() {

		$upload_dir = wp_upload_dir(); // wp upload dir ARRAY
		
		//print_r( $upload_dir );
		#### important! use the basedir ### 
		$upload_basedir = $upload_dir['basedir']; // !!!!! important, use basedir

		// custom folder for uploads
		$upload_subdir = 'cataloggi-uploads'; // sub dir
		$upload_dir_path = $upload_basedir . '/' . $upload_subdir . '/';
		return $upload_dir_path;
	}

	/**
	 * Process File Download.
	 *
	 * @access private
	 * @param string $filePath
	 * @param string $fileName
	 * @param string $file_mime_type
	 * @return void
	 */
    private function execute_headers( $filePath, $fileName, $file_mime_type ) {
		
		if ( ! empty($filePath) && ! empty($fileName) ) {
			
			$download_file = $filePath . $fileName;
			
			// required for IE & Safari
			if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
			
			if ( empty($file_mime_type) ) {
				$file_mime_type = 'application/force-download'; // application/octet-stream or application/force-download, application/x-rar-compressed
			}
			
			if ( file_exists( $download_file ) ) {
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false); // ?
				header('Content-Type: ' . $file_mime_type);
				header('Content-Disposition: attachment; filename=' . $fileName); // can be any filename
				header('Content-Transfer-Encoding: binary');
				header('Content-Description: File Transfer');
				header('Content-Length: ' . @filesize($filePath . $fileName));
				header('Last-Modified: ' . date("Y-m-d H:i:s.",@filemtime($fileName) ) ); // ?
				//header('Connection: close');
				//readfile($filePath . $fileName);
				$this->read_file_chunked($download_file, $retbytes=true);
			} else {
				$this->error_log_id  = 'file_download_API_execute_headers_file_not_exist';
				$this->result        = 'error';
				$this->message       = __('File not exist.', 'cataloggi');
			}
		}

		exit;

    }
	
	/**
	 * Read a file and display its content chunk by chunk to address the large file download.
	 * 
	 * @param string $filename Required
	 * @param bool $retbytes Default: true
	 */
	private function read_file_chunked($download_file, $retbytes=true){

		$chunksize = 1*(1024*1024);
		$buffer = '';
		$cnt = 0;
		$handle = fopen($download_file, 'rb');

		if ($handle === false) {
			return false;
		}

		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();

			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}

		$status = fclose($handle);

		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}

		return $status;
	}

	/**
	 * Generate File Name included with random number.
	 *
	 * @access private
	 * @param string $fileName
	 * @return string
	 */
	private function set_rand_num($fileName) {
		    
		$randNum = rand (1, 9999999);
		$newFileName = $randNum . "_" . $fileName;
		
		return $newFileName;
	
	}
	
	/**
	 * Output API response.
	 *
	 * @access public
	 * @return object $api_response
	 */
    public function output_api_response() {

		if ( ! empty( $this->result ) ) 
		{
			// response values
			$action            = ! empty( $this->action ) ? $this->action : '';
			$error_log_id      = ! empty( $this->error_log_id ) ? $this->error_log_id : ''; // save in the error log 
			$result            = ! empty( $this->result ) ? $this->result : '';
			$message           = ! empty( $this->message ) ? $this->message : '';
			$dwfile            = ! empty( $this->dwfile ) ? $this->dwfile : '';
			
			// default
			$api_response = array();
			
			// errors
	        if ( $this->result == 'error' ) 
			{ 
				// api response array
				$api_response = array(
					'action'            => $action,
					'error_log_id'      => $error_log_id,
					'result'            => $result,
					'message'           => $message
				);
				
				//header('Content-Type: application/json');
				//$api_response = json_encode($api_response, JSON_PRETTY_PRINT); // pretty formatted json
				
				$api_response = json_encode($api_response);
				//print serialize( $api_response );
				echo $api_response; // do not serialize!!!				
			}
			exit;
		}
	   
    }
	
	/**
	 * Allowed Mime Types for File Downloads.
	 *
	 * @access private
	 * @return array $mime_types
	 */
	private function allowed_mime_types() {
		
        $mime_types = array(

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',

            // archives
            'zip' => 'application/zip',
			'rar' => 'application/rar',
            //'rar' => 'application/x-rar-compressed',
			
            // adobe
            'pdf' => 'application/pdf',
			
        );
		
		return $mime_types;
		
	}
	
	/**
	 * WP Allowed Mime Types.
	 *
	 * @access public
	 * @return array $mime_types
	 */
	public static function wp_mime_types() {
		
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
		
		return $mime_types;
		
	}
	
	
}

?>