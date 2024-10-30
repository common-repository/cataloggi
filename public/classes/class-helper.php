<?php

/**
 * Helper Class For Cataloggi and for Extensions.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Helper {

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
	 * Current date time.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @return string $currentdate
	 */
	public static function currentDate() {
        $currentdate = date("Y-m-d H:i:s");
		return $currentdate;
	}
	
	/**
	 * Remove time from current datetime.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $date
	 * @return string $date
	 */
	public static function removeTimeFromDate( $date ) {
		
		if ( empty( $date ) )
		return;
		
		$date  = date('Y-m-d',strtotime($date)); 
		return $date;
	}
	
	/**
	 * Add time to current date.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $date
	 * @return string $datetime
	 */
	public static function addTimeToDate( $date ) {
		
		if ( empty( $date ) )
		return;
		
		$time = date('H:i:s');
		$format = $date . ' ' . $time;
		
		$datetime  = date('Y-m-d H:i:s',strtotime($format)); 
		return $datetime;
	}

	/**
	 * Format date. d F Y - d M Y - M-d, Y
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $date
	 * @return string $date
	 */
	public static function formatDate( $date ) {
        $fdate  = date('d M Y',strtotime($date));
		return $fdate;
	}

	/**
	 * Format date time. d F Y - d M Y - M-d, Y
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $date
	 * @return string $date
	 */
	public static function formatDateTime( $date ) {
        $fdate  = date('d M Y  - g:i A',strtotime($date));
		return $fdate;
	}

	/**
	 * base64_encode.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $data
	 * @return string $data
	 */
	public static function ctlggi_base64url_encode($data) {
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	/**
	 * base64_decode.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param string $data
	 * @return string $data
	 */
	public static function ctlggi_base64url_decode($data) {
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	} 

	/**
	 * Generate random string.
	 * 
	 * @since 1.0.0
	 * @access public static
	 * @param int $length
	 * @return string $randomString
	 */
	public static function ctlggi_generate_random_string($length)
	{
		// length should be minimum 6 characters long
		if ( empty($length) or $length <= 6 )
		{ 
		   $length = '6';
		}
		
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charsLength = strlen($chars); 
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $chars[rand(0, $charsLength - 1)];
		}
		 return $randomString;
		
	}
	
	/**
	 * AVOID METABOXES DUPLICATES.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param int $post_id
	 * @param string $post_type
	 * @return string $get_post_type
	 */
	public static function ctlggi_check_post_post_type_by_post_id($post_id, $post_type) {
		
		if ( empty( $post_id ) && empty( $post_type ) )
		return false;
		
		$get_post_type = '';
		// get post data by post id
		if ( get_post( $post_id ) ) {
			$get_post = get_post( $post_id );
			$get_post_type = $get_post->post_type;
			// check post type
			if ( $get_post_type == $post_type ) {
				return $get_post_type; 
			} else {
				// post type do not match
				return false;
			}
		} else {
			// no post found
			return false;
		}
	}
	
	/**
	 * Get domain name. e.g. example.com
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return string $domain
	 */
    public static function ctlggi_site_domain_name() 
	{
		// current page url
		//$url = CTLGGI_Helper::ctlggi_get_current_page_url();
		$url = home_url(); // use home_url as it's more safe
		// get domain name
		$domain = CTLGGI_Helper::ctlggi_get_url_parse($url, $part='host');
		
		return $domain;
	}

	/**
	 * Get url parse.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param  string $url
	 * @param  string $part
	 * @return string $parse
	 */
	public static function ctlggi_get_url_parse($url, $part='host') {
		$parse = parse_url($url);
		return $parse[$part];	
	}

	/**
	 * Get current page url.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $pageURL
	 */
	public static function ctlggi_get_current_page_url() 
	{
		 $pageURL = 'http';
		 if ( isset ($_SERVER["HTTPS"] ) == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 /*
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 */
		 $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 return $pageURL;
	}
	

	
	
}

?>