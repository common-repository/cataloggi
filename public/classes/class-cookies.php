<?php

/**
 * Cookies class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Cookies {

	const Session    = null;
	const OneDay     = 86400;
	const SevenDays  = 604800;
	const ThirtyDays = 2592000;
	const SixMonths  = 15811200;
	const OneYear    = 31536000;
	const Lifetime   = -1; // 2030-01-01 00:00:00
	
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
	 * Returns true if there is a cookie with this name.
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @return bool
	 */
	public static function ctlggi_is_cookie_exist($name)
	{
		if ( empty( $name ) )
		    return;
			
	    return isset($_COOKIE[$name]);
	}
	
	 /**
	 * Returns true if there no cookie with this name or it's empty, or 0,
	 * or a few other things. Check http://php.net/empty for a full list.
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @return bool
	 */
	public static function ctlggi_is_cookie_empty($name)
	{
		if ( empty( $name ) )
		    return;
			
	    return empty($_COOKIE[$name]);
	}
  
	 /**
	 * Get the value of the given cookie. If the cookie does not exist the value
	 * of $default will be returned.
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public static function ctlggi_get_cookie($name, $default = '')
	{
		if ( empty( $name ) )
		    return;
			
	     return (isset($_COOKIE[$name]) ? stripslashes($_COOKIE[$name]) : $default);
	}

	 /**
	 * Set a cookie. 
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @param mixed  $value - Can be string, array, object etc.
	 * @param mixed  $expiry
	 * @param string $path
	 * @param string $domain
	 * @param bool   $secure
	 * @param bool   $httponly - Should be false so it will be accessible by JavaScript
	 * @return bool
	 */
	public static function ctlggi_set_cookie($name, $value, $expiry = self::OneYear, $path = '/', $domain = false, $secure = false, $httponly = false )
	{
		if ( empty( $name ) )
			return;
			
		$retval = false;
		if (!headers_sent())
		{
			if ($domain === false)
				$domain = CTLGGI_Helper::ctlggi_site_domain_name();
		
			if ($expiry === -1)
				$expiry = 1893456000; // Lifetime = 2030-01-01 00:00:00
			elseif (is_numeric($expiry))
				$expiry += time();
			else
				$expiry = strtotime($expiry);
			
			$retval = @setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
			if ($retval)
			$_COOKIE[$name] = $value;
		}
		return $retval;
		
	}
  
	 /**
	 * Delete a cookie.
	 *
	 * @since 1.0.0
	 * @param string $name
	 * @param string $path
	 * @param string $domain
	 * @param bool $remove_from_global Set to true to remove this cookie from this request.
	 * @return bool
	 */
	public static function ctlggi_delete_cookie($name, $path = '/', $domain = false, $remove_from_global = false)
	{
		if ( empty( $name ) )
		    return;
			
		$retval = false;
		if (!headers_sent())
		{
			if ($domain === false)
			    $domain = CTLGGI_Helper::ctlggi_site_domain_name();
			    $retval = setcookie($name, '', time() - 50000, $path, $domain);
			
			if ($remove_from_global)
			    unset($_COOKIE[$name]);
		}
		return $retval;
	}

	/**
	 * Create unique prefix for cookies based on the domain name.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $cookie_prefix
	 */
	public static function ctlggi_unique_cookies_prefix() 
	{
		// get domain name
		$domain = CTLGGI_Helper::ctlggi_site_domain_name();
		$domain = trim($domain);
		$cookie_prefix = str_replace('.', '_', $domain);
		/*
		// create transients for cookie names
		// Cart Items
		$cartitems = $cookie_prefix . '_cartitems';
		if( get_transient( $cartitems ) === false )
		{
			// Expired or not found, so create
			set_transient( $cartitems, $cartitems, 6 * HOUR_IN_SECONDS ); // for 6 hours
		}
		// Cart Totals
		$carttotals = $cookie_prefix . '_carttotals';
		if( get_transient( $carttotals ) === false )
		{
			// Expired or not found, so create
			set_transient( $carttotals, $carttotals, 6 * HOUR_IN_SECONDS ); // for 6 hours
		}
		*/
		return $cookie_prefix; // unique cookie prefix
	}
	
	/**
	 * Dynamic Cart items cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $cart_items_cookie_name
	 */
	public static function ctlggi_cart_items_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$cart_items_cookie_name = $cookies_prefix . '_ctlggi_cart_items';
		return $cart_items_cookie_name;
	}
	
	/**
	 * Dynamic Cart totals cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $cart_totals_cookie_name
	 */
	public static function ctlggi_cart_totals_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$cart_totals_cookie_name = $cookies_prefix . '_ctlggi_cart_totals';
		return $cart_totals_cookie_name;
	}
	
	/**
	 * Dynamic Order ID cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $order_id_cookie_name
	 */
	public static function ctlggi_order_id_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$order_id_cookie_name = $cookies_prefix . '_ctlggi_order_id';
		return $order_id_cookie_name;
	}
	
	/**
	 * Dynamic Order Key cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $order_key_cookie_name
	 */
	public static function ctlggi_order_key_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$order_key_cookie_name = $cookies_prefix . '_ctlggi_order_key';
		return $order_key_cookie_name;
	}
	
	/**
	 * Dynamic Items view cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $items_view_cookie_name
	 */
	public static function ctlggi_items_view_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$items_view_cookie_name = $cookies_prefix . '_ctlggi_items_view';
		return $items_view_cookie_name;
	}
	
	/**
	 * Dynamic Is Manual Order cookie name for multisites.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return string $cookie_name
	 */
	public static function ctlggi_is_manual_order_cookie_name() 
	{
		$cookies_prefix = CTLGGI_Cookies::ctlggi_unique_cookies_prefix();
		$cookie_name = $cookies_prefix . '_ctlggi_is_manual_order';
		return $cookie_name;
	}
	
}

?>