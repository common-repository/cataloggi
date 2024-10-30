<?php

/**
 * Shopping Cart - Cart
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Cart {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Calculate order total. 
	 * Charge for subsc sign up fees.
	 * If it is a free trial then remove the item total from the totals.
	 *
	 * @since 1.0.0
	 * @param array $order_items
	 * @return array $order_total
	 */
    public static function ctlggi_cart_calculate_order_total( $order_items ) 
	{
		$total = '0';
		// if cart has contents
		if(count( $order_items ) > 0 ) {
			
			foreach( $order_items as $key => $value ){	
				
				if ( $value['item_price'] != '0' && $value['item_price'] != '' ) {
					// item total, item price x quantity
					$itemtotal = $value['item_price'] * $value['item_quantity'];
				    // price in total
				    $total = $total + $itemtotal;
					
					if ( $value['item_payment_type'] == 'subscription' ) { // normal or subscription
				  
						// for free trial we do not charge
						if ( isset($value['subsc_trial']) && $value['subsc_trial'] != '' && $value['subsc_trial'] != '0' ) {
							$total = $total - $itemtotal;
						}
						
						// if there is a sign-up fee then add to total
						if ( isset($value['subsc_signupfee']) && $value['subsc_signupfee'] != '' && $value['subsc_signupfee'] != '0' ) {
							$total = $total + $value['subsc_signupfee'];
						}
					
					}
				  
				}
				
			}
		}
		// order totals
		$order_total = array(
			  'subtotal'  => sanitize_text_field( $total ),
			  'total'     => sanitize_text_field( $total )
		);
		return $order_total;
	}
	
	/**
	 * When remove from cart button clicked process Ajax.
	 *
	 * @since  1.0.0
	 * @return string $total
	 */
    public function ctlggi_remove_from_cart_form_process() 
	{
		// default
		$item_id    = '';
		
		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		    
			/*
			echo '<pre>';
			print_r( $formData );
			echo '</pre>';
			*/
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-remove-from-cart-form-nonce'], 'ctlggi_remove_from_cart_form_nonce') )
	    {	
			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
		    
			$item_id   = sanitize_text_field( $postdata['ctlggi_item_id'] );
			$array_key = sanitize_text_field( $postdata['ctlggi_item_arr_key'] ); // remove item by the item array key
			
			// cookie name
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			// check if cookie exist
			if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) {	
				// read the cookie
				$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');
			} else {
				$cookie = '';
			}
			
			$arr_cart_items = json_decode($cookie, true); // convert to array
			$obj_cart_items = json_decode($cookie); // convert to object
			 
			// remove the item from the array
			//unset($arr_cart_items[$item_id]);
			unset($arr_cart_items[$array_key]);
			
			/*
			echo '<pre>';
			print_r( $arr_cart_items );
			echo '</pre>';
			*/
			
			// cookie name
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			// delete cookie
			$del_cookie = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_items_cookie_name, $path = '/', $domain, $remove_from_global = false);
			
			$total = '0';
			// if array has contents
			if(count($arr_cart_items)>0) {
			 
				// enter new value
				$value = json_encode($arr_cart_items, true);
				// set cookie, expires in 1 day
				$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$cart_items_cookie_name, $value, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
				
				// CALCULATE TOTALS
				$order_total = CTLGGI_Cart::ctlggi_cart_calculate_order_total( $order_items=$arr_cart_items );
				$subtotal    = $order_total['subtotal'];
				$total       = $order_total['total'];
				
				$subtotal    = CTLGGI_Amount::ctlggi_amount_hidden($amount=$subtotal);
				$total       = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total);
				
				// save totals in cookie
				CTLGGI_Cart::ctlggi_cart_totals($subtotal, $total);
				
			
			} else {
				
				// get domain name
				$domain = CTLGGI_Helper::ctlggi_site_domain_name();
				// cookie name
				$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
				// delete cookie
				$del_cookie = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_totals_cookie_name, $path = '/', $domain, $remove_from_global = false);
				
			}
		}
		
		$total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // format
		// return json
		//echo json_encode(array('cart_total'=>$total ));
		echo $total;
		
		
	    #### important! #############
	    exit; // don't forget to exit!
		
	}
	
	/**
	 * When update cart button clicked process Ajax.
	 *
	 * @since  1.0.0
	 * @return string $total
	 */
    public function ctlggi_update_cart_process() 
	{
		// get domain name
		$domain = CTLGGI_Helper::ctlggi_site_domain_name();
		
		// get form data
		$formData = $_POST['formData'];
		$formData = stripslashes($formData);
		
		$obj_cart_items = json_decode($formData);
		$arr_cart_items = json_decode($formData, true);
		
		/*
		echo '<pre>';
		print_r( $arr_cart_items );
		echo '</pre>';
		*/
		
		// convert data to array
		// initialize empty cart items array
		$cart_items=array();
		// defaults
        $total      = '0';
		$item_total = '0';
		// if cart has contents
		if(count($obj_cart_items)>0){
			foreach($obj_cart_items as $key => $value){
				
				// calculate item totals
				if ($value->item_price != '0') {
					  // item total, item price x quantity
					  $item_total = $value->item_price *  $value->item_quantity;
					  $item_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_total); // format
					  
				}
				
				// add new item on array
				$item = array(
					  'item_id'            => sanitize_text_field( $value->item_id ),
					  'item_price'         => sanitize_text_field( $value->item_price ),
					  'item_name'          => sanitize_text_field( $value->item_name ),
					  'item_quantity'      => intval( $value->item_quantity ),
					  'item_downloadable'  => intval( $value->item_downloadable ),
					  'price_option_id'    => intval( $value->price_option_id ),
					  'price_option_name'  => sanitize_text_field( $value->price_option_name ),
					  'item_total'         => sanitize_text_field( $item_total ),
					  'item_payment_type'  => sanitize_text_field( $value->item_payment_type ),
					  'custom_field'       => sanitize_text_field( $value->custom_field ),
					  'grouped_products'   => sanitize_text_field( $value->grouped_products )
				);
				
				// manage subscription
				if ( isset($value->item_payment_type) && $value->item_payment_type == 'subscription' ) {
					$subsc_data = array(
						'subsc_recurring'         => $value->subsc_recurring,
						'subsc_interval'          => $value->subsc_interval,
						'subsc_interval_count'    => $value->subsc_interval_count,
						'subsc_times'             => $value->subsc_times,
						'subsc_signupfee'         => $value->subsc_signupfee,
						'subsc_trial'             => $value->subsc_trial
					); 
					$item = array_merge( $item, $subsc_data );
				}
				
				$id = intval( $item['item_id'] );
				
				// !!!!! update array keys to item_id
				// add new item on array
				//$cart_items[$id]=$item;
				$cart_items[]=$item;
				
			}
		}
		
		// CALCULATE TOTALS
		$order_total = CTLGGI_Cart::ctlggi_cart_calculate_order_total( $order_items=$arr_cart_items );
		$subtotal    = $order_total['subtotal'];
		$total       = $order_total['total'];
		
		$subtotal    = CTLGGI_Amount::ctlggi_amount_hidden($amount=$subtotal);
		$total       = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total);
		
		// save totals in cookie
		CTLGGI_Cart::ctlggi_cart_totals($subtotal, $total);
		
		// cookie name
		$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
		
		// delete cookie
		$del_cookie = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_items_cookie_name, $path = '/', $domain, $remove_from_global = false);
		
		// save items into the cookie
		$value = json_encode($cart_items, true); // convert to array
		// set cookie, expires in 1 day
        $set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$cart_items_cookie_name, $value, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
		
		
		// return json
		//echo json_encode(array('cart_total'=>$total ));
		echo $total;

	    #### important! #############
	    exit; // don't forget to exit!

    }
	
	/**
	 * Manage Cart Totals.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  float $subtotal
	 * @param  float $total
	 * @return void
	 */
    public static function ctlggi_cart_totals($subtotal, $total) 
	{
			
		if ( empty($subtotal) )
		    $subtotal = '0';
			
		if ( empty($total) )
		    $total = '0';
			
			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			
			$totals = array(
				  'subtotal'  => sanitize_text_field( $subtotal ),
				  'total'     => sanitize_text_field( $total )
			);

			// cookie name
			$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
			// delete cookie
			$del_cookie = CTLGGI_Cookies::ctlggi_delete_cookie($name=$cart_totals_cookie_name, $path = '/', $domain, $remove_from_global = false);
			
			// put item to cookie
			$value = json_encode($totals, true); // convert to array
			// set cookie, expires in 1 day
			$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$cart_totals_cookie_name, $value, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
		
	}
	
	
}

?>