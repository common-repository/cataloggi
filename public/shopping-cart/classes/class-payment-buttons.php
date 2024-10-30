<?php

/**
 * Shopping Cart - Payment_Buttons
 *
 * @package     cataloggi
 * @subpackage  public/shopping-cart/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Payment_Buttons {

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
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Payment buttons.
	 *
	 * @to-do This Method is not in use. Just an example.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @return array $payment_buttons
	 */
    public static function ctlggi_payment_buttons() 
	{
		
		$payment_buttons = array(
			'add_to_cart' => array(
				'label_one'  => __( ' - Add To Cart', 'cataloggi' ), 
				'label_two'  => __( ' + View Cart', 'cataloggi' ),
				'color_one'  => '#ec7a5c',
				'color_two'  => '#ec7a5c',
				'page'       => 'cart', 
			),
			'buy_now' => array(
				'label_one'  => __( ' - Buy Now', 'cataloggi' ),
				'label_two'  => __( ' + Checkout', 'cataloggi' ),
				'color_one'  => '#ec7a5c',
				'color_two'  => '#ec7a5c',
				'page'       => 'checkout', 
			),
		);
	
		return apply_filters( 'ctlggi_payment_buttons', $payment_buttons ); // <- extensible

	}
	
	/**
	 * Display "download free" button if applicable.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @return string $output
	 */
    public static function ctlggi_output_free_download_button($free_download) 
	{
		if ( empty( $free_download ) )
		    return;
			
		$post_id = $free_download['item_id'];
		$size    = $free_download['size'];
		
		if ( empty( $post_id ) )
		    return;
		
		// defaults
		$output = '';
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
						
						$item_custom_download_url  = get_post_meta( $post_id, '_ctlggi_item_custom_download_url', true );
						$item_download_from_url  = get_post_meta( $post_id, '_ctlggi_item_download_from_url', true );
						
						if ( $item_custom_download_url == '1' && !empty($item_download_from_url) ) {
							
							$download_link = $item_download_from_url;
							
							$output .= '<div class="ctlggi-download-free-button" id="ctlggi-download-free-button">';
							
								$output .= '<a href="' . esc_url( $download_link ) . '" target="_blank">';
								$output .= '<button type="button" class="btn-cataloggi btn-cataloggi-' . $size . ' btn-cataloggi-green" >';
								$output .= esc_attr('+ Free Download', 'cataloggi');
								$output .= '</button>';
								$output .= '</a>';
							
							$output .= '</div>';
							
							return $output;
							
						} else {
							// check if file name and url not empty
							if ( !empty($item_file_name) && !empty($item_file_url) ) {
								// show download button
								
								// downloadable products create download url
								$secret_data = array(
									'post_id'    => intval( $post_id )
								);
								
								// convert array to json
								$secret_data_json = json_encode( $secret_data );
								$secret_data_json_enc = CTLGGI_Helper::ctlggi_base64url_encode($data=$secret_data_json);
								// e.g. http://cataloggi.com/ctlggi-file-dw-api/?action=freedownload&dwfile=42423432
								$download_link = home_url() . '/ctlggi-file-dw-api/?action=freedownload&dwfile=' . $secret_data_json_enc;
								
								$output .= '<div class="ctlggi-download-free-button" id="ctlggi-download-free-button">';
								
									$output .= '<a href="' . esc_url( $download_link ) . '">';
									$output .= '<button type="button" class="btn-cataloggi btn-cataloggi-' . $size . ' btn-cataloggi-green" >';
									$output .= esc_attr('+ Free Download', 'cataloggi');
									$output .= '</button>';
									$output .= '</a>';
								
								$output .= '</div>';
								
								return $output;
							}
						}
						
					}
				}
			}
		}
		
	}
	
	/**
	 * Display payment buttons. e.g. add to cart, buy now
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $button_atts
	 * @return string $output
	 */
    public static function ctlggi_output_payment_button($button_atts) 
	{
		if ( empty( $button_atts ) )
		return;

		// button atts
		$post_id           = sanitize_text_field( $button_atts['id'] ); // id is the post_id
		$size              = sanitize_text_field( $button_atts['size'] ); // button, select, quantity size
		$type              = sanitize_text_field( $button_atts['type'] ); // buynow or cart
		$gateway           = sanitize_text_field( $button_atts['gateway'] ); // e.g. stripe or paypalstandard
		$custom_item       = sanitize_text_field( $button_atts['custom_item'] ); // if it is a custom item: yes or no
		$custom_item_name  = sanitize_text_field( $button_atts['custom_item_name'] ); // only for custom buy now buttons
		$custom_item_price = sanitize_text_field( $button_atts['custom_item_price'] ); // only for custom buy now buttons
		$custom_field      = sanitize_text_field( $button_atts['custom_field'] );
		$grouped_products  = sanitize_text_field( $button_atts['grouped_products'] ); // use this for grouped products (e.g. store IDs, product names etc.)
		$quantity          = sanitize_text_field( $button_atts['quantity'] ); // display the quantity field, yes or no
		$guest_payment     = sanitize_text_field( $button_atts['guest_payment'] ); // guest payment, yes or no
		$label_one         = sanitize_text_field( $button_atts['label_one'] );
		$label_two         = sanitize_text_field( $button_atts['label_two'] );
		$color_one         = sanitize_text_field( $button_atts['color_one'] );
		$color_two         = sanitize_text_field( $button_atts['color_two'] );
		
		$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug();
		$cataloggiurl = home_url() . '/' . $rewrite_slug . '/';
		
		// Meta Boxes, Retrieve an existing value from the database.
		$item_price                      = get_post_meta( $post_id, '_ctlggi_item_price', true );
		$item_downloadable               = get_post_meta( $post_id, '_ctlggi_item_downloadable', true );
		$subscription_enabled            = get_post_meta( $post_id, '_ctlggi_subsc_enabled', true );
		$software_licensing_enabled      = get_post_meta( $post_id, '_enable_software_licensing', true ); 
		$item_show_free_download_button  = get_post_meta( $post_id, '_ctlggi_item_show_free_download_button', true );
		
		// Set default values.
		if( empty( $item_price ) ) $item_price = '0';
		if( empty( $item_downloadable ) ) $item_downloadable = '';
		if( empty( $subscription_enabled ) ) $subscription_enabled = '';
		if( empty( $software_licensing_enabled ) ) $software_licensing_enabled = '';
		if( empty( $item_show_free_download_button ) ) $item_show_free_download_button = '0';
		
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		//currency symbol
		$catalog_currency = $ctlggi_currency_options['catalog_currency'];
		$currencysymbol   = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $catalog_currency );
		
		// get site title
		if ( !empty( get_bloginfo('name') ) ) {
			$blog_title = get_bloginfo('name');
		} else {
			$blog_title = 'WordPress';
		}
		
		// check if user logged in
		if ( is_user_logged_in() ) {
			$user_logged_in = '1';
		} else {
			$user_logged_in = '0';
		}
		
		
		$free_download = array(
		  'item_id'            => sanitize_text_field( $post_id ),
		  'size'               => sanitize_text_field( $size ),
		);
		// display free download button
		$free_download_button = CTLGGI_Payment_Buttons::ctlggi_output_free_download_button($free_download);
		if ( ! empty($free_download_button) && $item_show_free_download_button == '1' ) {
			return $free_download_button;
		} else {
			// software licensing also using that option (class-public.php)
			$button_data = array(
				'post_id'           => intval( $post_id ),
				'item_price'        => sanitize_text_field( $item_price ),
				'item_payment_type' => 'normal' // normal or subscription
			);
			
			$button_data = CTLGGI_Payment_Buttons::ctlggi_default_price_payment_buttons( $button_data );
			
			$item_price  = sanitize_text_field( $button_data['item_price'] );
	
			// item sale  price
			$item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span
			$item_sale_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price); 
			
			// cookies
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			$cart_totals_cookie_name = CTLGGI_Cookies::ctlggi_cart_totals_cookie_name();
			
			// check if default payment gateway supports buy now buttons
			$defaultgateway = '';  // def
			$gateway_buy_now_allowed = '';  // def
			$display_buy_now_button = '';  // def
			if ( isset($type) && $type == 'buynow' ) {
				$ctlggi_payment_gateway_options = get_option('ctlggi_payment_gateway_options');
				if ( ! empty($gateway) ) {
					$defaultgateway   = $gateway; // e.g. stripe
					// payment gateways array
					$payment_gateways = CTLGGI_Payment_Gateways::ctlggi_payment_gateways();
					$payment_gateways = json_encode( $payment_gateways ); // convert array to json
					$gateways_obj     = json_decode( $payment_gateways ); // Translate into an object
					$gateways_array   = json_decode( $payment_gateways, true ); // Translate into an array
					
					// check if default gateway buy now allowed
					$gateway_buy_now = ''; // def
					if ( ! empty($gateways_array[$defaultgateway]) ) {
						$def_gateway_data = $gateways_array[$defaultgateway];
						if ( ! empty($def_gateway_data['buy_now']) ) {
							$gateway_buy_now = $def_gateway_data['buy_now'];
							if ( $gateway_buy_now == '1' ) {
								$gateway_buy_now_allowed = '1';
							}
						}
					}
				}
				
				// display express checkout button
				// Note: the button will be shown only if the default gateway express checkout is enabled
				if ( $gateway_buy_now_allowed == '1' ) {
					$display_buy_now_button = '1';
				}
				
			}
			
			// check if it is a custom item
			if ( isset($custom_item) && $custom_item == 'yes' ) {
				// it is a custom itemn, create custom id
				$min=10000;
				$max=999999;
				$rand_num =  mt_rand($min,$max);
				$randnum  = '00' . $rand_num;
				
				$post_id = $randnum; // item id
				
				if ( empty($custom_item_name) ) {
					// error: custom buy now buttons should have a custom item name
					$item_name = __( 'Undefined Item Name', 'cataloggi' );
				} else {
					$item_name = $custom_item_name;
				}
				
				// item sale  price
				$item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$custom_item_price); // return span
				
				if ( empty($custom_item_price) ) {
					// error: custom buy now buttons should have a custom item price
					$item_sale_price_hidden = 0;
				} else {
					$item_sale_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$custom_item_price); 
				}
			} else {
				// if "Private:" set on item name, remove
				$item_name = str_replace("Private:", "", get_the_title( $post_id ));
			}
			
			// display buy now button only if gatway support it and it is not a subscription payment
			if ( $display_buy_now_button == '1' && $subscription_enabled != '1' ) {
		
				// array example buy now button
				$button_arr = array(
				  'item_id'            => sanitize_text_field( $post_id ),
				  'size'               => sanitize_text_field( $size ),
				  'type'               => sanitize_text_field( $type ),
				  'custom_item'        => sanitize_text_field( $custom_item ),
				  'item_name'          => sanitize_text_field( $item_name ),
				  'item_price'         => sanitize_text_field( $item_sale_price_hidden ),
				  'item_currency'      => $catalog_currency,
				  'guest_payment'      => $guest_payment,
				  'custom_field'       => $custom_field,
				  'grouped_products'   => $grouped_products,
				  'item_downloadable'  => $item_downloadable,
				  'subscription'       => $subscription_enabled,
				  'software_licensing' => $software_licensing_enabled
				);
				
				// Display Object
				CTLGGI_Developer_Mode::display_object( $object=$button_arr );
				
				// display buy now button 
				$output = '';
				$output .= '<div id="ctlggi_buy_now_button_' . esc_attr( $post_id ) . '" class="ctlggi-payment-buttons-wrapper">';
				
				$output .= '<form class="ctlggi-buy-now-form-class" action="" method="post" data-id="' . $post_id . '" id="ctlggi_buy_now_form_' . $post_id . '">';
				$output .= '<div class="ctlggi-loading-img"></div>'; // display jquery loader img
				$output .= '<div class="display-buy-now-form-response-msg"></div>'; // display jquery response
				$output .= '<input type="hidden" name="ctlggi-global-nonce-for-payment-forms" value="' . wp_create_nonce('ctlggi_global_nonce_for_payment_forms') . '"/>';	
				$output .= '<input type="hidden" name="ctlggi_is_user_logged_in" id="ctlggi_is_user_logged_in" value="' . esc_attr( $user_logged_in ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_id" id="ctlggi_item_id" value="' . esc_attr( $post_id ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_price" id="ctlggi_item_price" class="ctlggi_item_price_class" value="' . esc_attr( $item_sale_price_hidden ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_name" id="ctlggi_item_name" value="' . esc_attr( $item_name ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_downloadable" id="ctlggi_item_downloadable" value="' . esc_attr( $item_downloadable ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_currency" id="ctlggi_item_currency" value="' . esc_attr( $catalog_currency ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_subscription" id="ctlggi_subscription" value="' . esc_attr( $subscription_enabled ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_software_licensing" id="ctlggi_software_licensing" value="' . esc_attr( $software_licensing_enabled ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_default_gateway" id="ctlggi_default_gateway" value="' . esc_attr( $defaultgateway ) . '"/>'; 
				$output .= '<input type="hidden" name="ctlggi_guest_payment" id="ctlggi_guest_payment" value="' . esc_attr( $guest_payment ) . '"/>'; 
				$output .= '<input type="hidden" name="ctlggi_custom_field" id="ctlggi_custom_field" value="' . esc_attr( $custom_field ) . '"/>'; 
				$output .= '<input type="hidden" name="ctlggi_grouped_products" id="ctlggi_grouped_products" value="' . esc_attr( $grouped_products ) . '"/>'; 
					
				$output .= '<div class="ctlggi-payment-button-1" id="ctlggi-payment-button-1">';
					
					// if quantity yes
					if ( $quantity == 'yes' ) {
						$output .= '<div class="ctlggi-item-quantity-input-' . $size . '">';
						$output .= '<input type="number"  max="" min="1" value="1" id="ctlggi_item_quantity" name="ctlggi_item_quantity">';
						$output .= '</div>';
					} else {
						$output .= '<input type="hidden" max="" min="1" value="1" id="ctlggi_item_quantity" name="ctlggi_item_quantity"/>';
					}
					
					$output .= '<div class="ctlggi-payment-button-1-submit"> ';
					$output .= '<button type="submit" id="' . $post_id . '" class="ctlggi-buy-now-form-submit btn-cataloggi btn-cataloggi-' . $size . ' btn-cataloggi-no-bg" style="background:' . esc_attr( $color_one ) . ';" >';
					$output .= ' ' . $item_sale_price_public . ' ' . esc_attr( $label_one );
					$output .= '</button>';
					$output .= '</div>';
				
				$output .= '</div>';
				
				$select_field_data = array(
				  'item_id'            => sanitize_text_field( $post_id ),
				  'size'               => sanitize_text_field( $size ),
				);
				
				$output .= CTLGGI_Payment_Buttons::ctlggi_output_price_options_select_field( $select_field_data );
				
				$output .= '</form>';
				
				$output .= '</div>';
			} else {
				
				// array example buy now button
				$button_arr = array(
				  'item_id'            => sanitize_text_field( $post_id ),
				  'size'               => sanitize_text_field( $size ),
				  'type'               => sanitize_text_field( $type ),
				  'custom_item'        => sanitize_text_field( $custom_item ),
				  'item_name'          => sanitize_text_field( $item_name ),
				  'item_price'         => sanitize_text_field( $item_sale_price_hidden ),
				  'item_currency'      => $catalog_currency,
				  'guest_payment'      => $guest_payment,
				  'custom_field'       => $custom_field,
				  'grouped_products'   => $grouped_products,
				  'item_downloadable'  => $item_downloadable,
				  'subscription'       => $subscription_enabled,
				  'software_licensing' => $software_licensing_enabled
				);
				
				// Display Object
				CTLGGI_Developer_Mode::display_object( $object=$button_arr );
				
				// display add to cart button
				$output = '';
				$output .= '<div id="ctlggi_add_to_cart_button_' . esc_attr( $post_id ) . '" class="ctlggi-payment-buttons-wrapper">';
				
				$output .= '<form class="ctlggi-add-to-cart-form-class" action="" method="post" data-id="' . $post_id . '" id="ctlggi_add_to_cart_form_' . $post_id . '" >';
				$output .= '<input type="hidden" name="ctlggi-global-nonce-for-payment-forms" value="' . wp_create_nonce('ctlggi_global_nonce_for_payment_forms') . '"/>';	
				$output .= '<input type="hidden" name="ctlggi_item_id" id="ctlggi_item_id" value="' . esc_attr( $post_id ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_price" id="ctlggi_item_price" class="ctlggi_item_price_class"  value="' . esc_attr( $item_sale_price_hidden ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_name" id="ctlggi_item_name" value="' . esc_attr( $item_name ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_item_downloadable" value="' . esc_attr( $item_downloadable ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_cart_items_cookie_name" class="ctlggi_cart_items_cookie_name_class"  value="' . esc_attr( $cart_items_cookie_name ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_cart_totals_cookie_name" class="ctlggi_cart_totals_cookie_name_class"  value="' . esc_attr( $cart_totals_cookie_name ) . '"/>';
				$output .= '<input type="hidden" name="ctlggi_custom_field" id="ctlggi_custom_field" class="ctlggi_custom_field_class" value="' . esc_attr( $custom_field ) . '"/>';                $output .= '<input type="hidden" name="ctlggi_grouped_products" id="ctlggi_grouped_products" value="' . esc_attr( $grouped_products ) . '"/>'; 
					
				$output .= '<div class="ctlggi-payment-button-1" id="ctlggi-payment-button-1">';
					
					// if quantity yes
					if ( $quantity == 'yes' ) {
						$output .= '<div class="ctlggi-item-quantity-input-' . $size . '">';
						$output .= '<input type="number"  max="" min="1" value="1" id="ctlggi_item_quantity" name="ctlggi_item_quantity">';
						$output .= '</div>';
					} else {
						$output .= '<input type="hidden" max="" min="1" value="1" id="ctlggi_item_quantity" name="ctlggi_item_quantity"/>';
					}
					
					$output .= '<div class="ctlggi-payment-button-1-submit"> ';
					$output .= '<button type="submit" id="' . $post_id . '" class="ctlggi-add-to-cart-form-submit btn-cataloggi btn-cataloggi-' . $size . ' btn-cataloggi-no-bg" style="background:' . esc_attr( $color_one ) . ';" >';
					$output .= ' ' . $item_sale_price_public . ' ' . esc_attr( $label_one );
					$output .= '</button>';
					$output .= '</div>';
				
				$output .= '</div>';
				
				$output .= '<div class="ctlggi-payment-button-2" id="ctlggi-payment-button-2">';
				    
					$page = 'cart';
					$output .= '<a href="' . esc_url( $cataloggiurl . '?page=' . $page ) . '' . '">';
					$output .= '<button type="button" class="btn-cataloggi btn-cataloggi-' . $size . ' btn-cataloggi-no-bg" style="background:' . esc_attr( $color_two ) . ';" >';
					$output .= esc_attr( $label_two );
					$output .= '</button>';
					$output .= '</a>';
				
				$output .= '</div>';
					
				$select_field_data = array(
				  'item_id'            => sanitize_text_field( $post_id ),
				  'size'               => sanitize_text_field( $size ),
				);
				
				$output .= CTLGGI_Payment_Buttons::ctlggi_output_price_options_select_field( $select_field_data );
				
				$output .= '</form>';
				
				$output .= '</div>';
			
			}
			return $output;
		} 
	}
	
	/**
	 * Set default price for payment buttons.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  array $button_data
	 * @return array $button_data
	 */
	public static function ctlggi_default_price_payment_buttons( $button_data ) 
	{	
		 $post_id           = intval( $button_data['post_id'] );
		 $item_price        = sanitize_text_field( $button_data['item_price'] );
		 $item_payment_type = $button_data['item_payment_type'];
		 
		 $enable_price_options   = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
		 
		 // if price options enabled
		 if ( ! empty( $enable_price_options ) && $enable_price_options == '1' ) {
			 
			$price_default_option = get_post_meta( $post_id, '_ctlggi_price_default_option', true );
			$price_options = get_post_meta( $post_id, '_ctlggi_price_options', true ); // json
			
			$price_options = json_decode($price_options, true);// convert into array
			
			if ( ! empty( $price_options ) && ! empty( $price_default_option ) ) {
				foreach( $price_options as $key ) {
					// get default
					if( $key['option_id'] == $price_default_option ) {
						$price_option_id = $key['option_id'];
						$item_price = esc_attr__( $key['option_price'] );
					}
				}
			} else {
				// if price options enabled but no option added display 0
				$item_price = '0';
			}
			
		 }
		 
		 $button_data = array(
			'post_id'           => intval( $post_id ),
			'item_price'        => sanitize_text_field( $item_price ),
			'item_payment_type' => $item_payment_type, // normal or subscription
		 );
		
		 return $button_data;
	 
	}
	
	/**
	 * Output select field for payment buttons.
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @return string $output
	 */
	public static function ctlggi_output_price_options_select_field( $select_field_data ) 
	{	
	
		if ( empty( $select_field_data ) )
		    return;
			
		$post_id = $select_field_data['item_id'];
		$size    = $select_field_data['size'];
	
		$enable_price_options  = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
		$item_downloadable     = get_post_meta( $post_id, '_ctlggi_item_downloadable', true );
		
		// if price options enabled
		if ( ! empty( $enable_price_options) && $enable_price_options == '1' ) {
		  
		  $output = '';
		  
		  $price_default_option = get_post_meta( $post_id, '_ctlggi_price_default_option', true );
		  
		  if ( ! empty( $price_default_option ) ) {
			  
			  $price_options = get_post_meta( $post_id, '_ctlggi_price_options', true ); // json  
			  
			  if ( ! empty( $price_options ) && $price_options != 'null' ) {
				  
				$price_options = json_decode($price_options, true);// convert into array	
				
				$output .= '<div id="' . $post_id . '" class="ctlggi-price-options ctlggi-price-options-' . $size . '">';
				$output .= '<label>';
				$output .= '<select id="ctlggi_price_options_select_' . $post_id . '" name="ctlggi_price_options" >';
				  
				$selected = ''; // default
				foreach( $price_options as $key ) {
					  
					$option_id   = intval( $key['option_id'] );
					$option_name = sanitize_text_field( $key['option_name'] );
					$item_price  = sanitize_text_field( $key['option_price'] );
					
					// item data defaults
					$item_data = array(
					  'item_id'           => intval( $post_id ),
					  'item_price'        => sanitize_text_field( $item_price ),
					  'item_name'         => sanitize_text_field( $option_name ),
					  'item_quantity'     => '',
					  'item_downloadable' => $item_downloadable,
					  'price_option_id'   => $option_id,
					  'item_total'        => '',
					  'item_payment_type' => 'normal' // normal or subscription
					);
					$item_data = json_encode($item_data);
					// subscriptions and recurring payments use that
					$item_data = apply_filters( 'ctlggi_price_options_select_field_filter', $item_data );
					// payment button extend item data with subsc data
					$item_data = json_decode($item_data, true); // convert to array
		
					// item data
					$item_id              = isset( $item_data['item_id'] ) ? sanitize_text_field( $item_data['item_id'] ) : '';
					$item_price           = isset( $item_data['item_price'] ) ? sanitize_text_field( $item_data['item_price'] ) : '';
					$item_name            = isset( $item_data['item_name'] ) ? sanitize_text_field( $item_data['item_name'] ) : '';
					$item_quantity        = isset( $item_data['item_quantity'] ) ? sanitize_text_field( $item_data['item_quantity'] ) : '';
					$item_downloadable    = isset( $item_data['item_downloadable'] ) ? sanitize_text_field( $item_data['item_downloadable'] ) : '';
					$price_option_id      = isset( $item_data['price_option_id'] ) ? sanitize_text_field( $item_data['price_option_id'] ) : '';
					$item_total           = isset( $item_data['item_total'] ) ? sanitize_text_field( $item_data['item_total'] ) : '';
					$item_payment_type    = isset( $item_data['item_payment_type'] ) ? sanitize_text_field( $item_data['item_payment_type'] ) : '';
					// subsc data
					$subsc_recurring      = isset( $item_data['subsc_recurring'] ) ? sanitize_text_field( $item_data['subsc_recurring'] ) : '';
					$subsc_interval       = isset( $item_data['subsc_interval'] ) ? sanitize_text_field( $item_data['subsc_interval'] ) : '';
					$subsc_interval_count = isset( $item_data['subsc_interval_count'] ) ? sanitize_text_field( $item_data['subsc_interval_count'] ) : '';
					$subsc_times          = isset( $item_data['subsc_times'] ) ? sanitize_text_field( $item_data['subsc_times'] ) : '';
					$subsc_signupfee      = isset( $item_data['subsc_signupfee'] ) ? sanitize_text_field( $item_data['subsc_signupfee'] ) : '';
					$subsc_trial          = isset( $item_data['subsc_trial'] ) ? sanitize_text_field( $item_data['subsc_trial'] ) : '';
					
					$item_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span (HTML)
					$item_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price); // return string
		
					$itemdata = json_encode($item_data);
					// make sure it is a subscriptions and display details
					if ( isset($item_payment_type) && $item_payment_type == 'subscription' ) {
						$display_subsc_details_public = apply_filters( 'ctlggi_price_options_select_field_display_subsc_data', $itemdata );
					} else {
						$display_subsc_details_public = '';
					}
					
					// get default
					if( $option_id == $price_default_option ) {
						$selected = 'selected="selected"';
						$output .= '<option ' . esc_attr( $selected ) . ' ctlggi-data-item-id="' . esc_attr__( $post_id ) . '"  
						ctlggi-data-item-payment-type="' . esc_attr__( $item_payment_type ) . '" 
						ctlggi-data-option-name="' . esc_attr__( $option_name ) . '" ctlggi-data-sale-price-hidden="' 
						. esc_attr__( $item_price_hidden ) . '" value="' . esc_attr__( $option_id ) 
						. '">' . esc_attr__( $option_name ) . ' ' . $item_price_public . $display_subsc_details_public . ' </option>';
					}
					// exclude selected
					if( $option_id != $price_default_option ) {
						$output .= '<option ctlggi-data-item-id="' . esc_attr__( $post_id ) . '" 
						ctlggi-data-item-payment-type="' . esc_attr__( $item_payment_type ) . '" 
						ctlggi-data-option-name="' . esc_attr__( $option_name ) . '" ctlggi-data-sale-price-hidden="' 
						. esc_attr__( $item_price_hidden ) . '" value="' . esc_attr__( $option_id ) . '">' 
						. esc_attr__( $option_name ) . ' ' . $item_price_public . $display_subsc_details_public . ' </option>';
					}
				
				}
				  
				  $output .= '</select>';
				  $output .= '</label>';
				  $output .= '</div>';
				  
				  return $output;
			  
			  } else {
				return;   
			  }
		  
		  }
		
		} else {
		return; 
		}
				
	}

	/**
	 * Product view page when payment button clicked process Ajax.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function ctlggi_payment_buttons_process() 
	{
		// defaults
		$item_id           = '';
		$item_price        = '';
		$item_name         = '';
		$item_quantity     = '';
		$item_downloadable = '';
		$item_payment_type = '';
		$price_option_id   = '';
		
		// get form data
		$formData = $_POST['formData'];
		// parse string
		parse_str($formData, $postdata);
		
	    // verify nonce
	    if ( wp_verify_nonce( $postdata['ctlggi-global-nonce-for-payment-forms'], 'ctlggi_global_nonce_for_payment_forms') )
	    {	

			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			
			$item_id            = isset( $postdata['ctlggi_item_id'] ) ? sanitize_text_field( $postdata['ctlggi_item_id'] ) : '';
			$item_price         = isset( $postdata['ctlggi_item_price'] ) ? sanitize_text_field( $postdata['ctlggi_item_price'] ) : '';
			$item_name          = isset( $postdata['ctlggi_item_name'] ) ? sanitize_text_field( $postdata['ctlggi_item_name'] ) : '';
			$item_quantity      = isset( $postdata['ctlggi_item_quantity'] ) ? sanitize_text_field( $postdata['ctlggi_item_quantity'] ) : '';
			$item_downloadable  = isset( $postdata['ctlggi_item_downloadable'] ) ? sanitize_text_field( $postdata['ctlggi_item_downloadable'] ) : '';
			$price_option_id    = isset( $postdata['ctlggi_price_options'] ) ? sanitize_text_field( $postdata['ctlggi_price_options'] ) : ''; // select field
			$custom_field       = isset( $postdata['ctlggi_custom_field'] ) ? sanitize_text_field( $postdata['ctlggi_custom_field'] ) : ''; 
			$grouped_products   = isset( $postdata['ctlggi_grouped_products'] ) ? sanitize_text_field( $postdata['ctlggi_grouped_products'] ) : '';
			
			// if "Private:" set on item name, remove
			$item_name = str_replace("Private:", "", $item_name);
			
			if( ! empty( $price_option_id ) ) {
				$price_option_id = $price_option_id;
				// get price option name
				$price_options = get_post_meta( $item_id, '_ctlggi_price_options', true ); // json  
				if ( ! empty( $price_options ) && $price_options != 'null' ) {	  
					$price_options = json_decode($price_options, true);// convert into array	
					$price_option_name = isset( $price_options[$price_option_id]['option_name'] ) ? sanitize_text_field( $price_options[$price_option_id]['option_name'] ) : ''; 
				}	
				
			} else {
				$price_option_id   = ''; 
				$price_option_name = ''; 
			}
			
			$item_total = $item_price * $item_quantity;
			$item_total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_total); // format
			
			// initialize empty cart items array
			$cart_items=array();
			
			
			// item data defaults
			$item_data = array(
			  'item_id'           => sanitize_text_field( $item_id ),
			  'item_price'        => sanitize_text_field( $item_price ),
			  'item_name'         => sanitize_text_field( $item_name ),
			  'item_quantity'     => $item_quantity,
			  'item_downloadable' => $item_downloadable,
			  'price_option_id'   => $price_option_id,
			  'price_option_name' => $price_option_name,
			  'item_total'        => $item_total,
			  'item_payment_type' => 'normal', // normal or subscription
			  'custom_field'      => $custom_field,
			  'grouped_products'  => $grouped_products
			);
			$item_data = json_encode($item_data);
			// subscriptions and recurring payments use that
			$item_data = apply_filters( 'ctlggi_payment_buttons_process_payment_filter', $item_data );
			$item_data = json_decode($item_data, true); // convert to array
			
			// extended item data
			$item = $item_data;
			
			$id = intval( $item_data['item_id'] );
			
			// add new item on array
			//$cart_items[$id]=$item;
			$cart_items[]=$item;
			
			// cookie name
			$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
			// check if cookie exist
			if( CTLGGI_Cookies::ctlggi_is_cookie_exist( $name=$cart_items_cookie_name ) === true ) {	
				// read the cookie
				$cookie = CTLGGI_Cookies::ctlggi_get_cookie($name=$cart_items_cookie_name, $default = '');
			} else {
				$cookie = '';
			}
			
			if ( ! empty($cookie) ) {
				$arr_cart_items = json_decode($cookie, true); // convert to array
				$obj_cart_items = json_decode($cookie); // convert to object
			} else {
				$arr_cart_items = array();
			}
			 
			// check if the item is in the array, if it is, do not add
			if(array_key_exists($id, $arr_cart_items)){
				// redirect to product list and tell the user it was already added to the cart
			}
			else {
				
			}
				
				$total = '0';
				// if cart has contents
				if(count($arr_cart_items)>0){
					
					foreach($arr_cart_items as $key => $value){	
					// add old item to array, it will prevent duplicate keys
					//$cart_items[$key]=$value;
					$cart_items[]=$value;  
						
						if ($value['item_price'] != '0') {
						  // item total, item price x quantity
						  $itemtotal = $value['item_price'] * $value['item_quantity'];
						  // price in total
						  $total = $total + $itemtotal;
						  
							// if subsc trial not empty
							if ( isset($value['subsc_trial']) && $value['subsc_trial'] != '' && $value['subsc_trial'] != '0' ) {
								$total = $total - $itemtotal;
							}
							
							// if subsc signupfee is not empty add to item total
							if ( isset($value['subsc_signupfee']) && $value['subsc_signupfee'] != '' && $value['subsc_signupfee'] != '0' ) {
								$total = $total + $value['subsc_signupfee'];
							}
						  
						}
						
					}
					// current item  price * quantity
					$curritem = $item_price * $item_quantity;
					// price in total + current item  price
					$total = $total + $curritem;
					
					// if subsc trial not empty
					if ( isset($item['subsc_trial']) && $item['subsc_trial'] != '' && $item['subsc_trial'] != '0' ) {
						$total = $total - $curritem;
					}
					
					// if subsc signupfee is not empty add to item total
					if ( isset($item['subsc_signupfee']) && $item['subsc_signupfee'] != '' && $item['subsc_signupfee'] != '0' ) {
						$total = $total + $item['subsc_signupfee'];
					}
					
					
				} else {
					// if cart empty add single item price
					// current item  price * quantity
					$curritem = $item_price * $item_quantity;
					$total    = $curritem;
					
					// if subsc trial not empty
					if ( isset($item['subsc_trial']) && $item['subsc_trial'] != '' && $item['subsc_trial'] != '0' ) {
						$total = $total - $curritem;
					}
					
					// if subsc signupfee is not empty add to item total
					if ( isset($item['subsc_signupfee']) && $item['subsc_signupfee'] != '' && $item['subsc_signupfee'] != '0' ) {
						$total = $total + $item['subsc_signupfee'];
					}
					
				}
				
				// save totals in cookie
				$total = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total);
				CTLGGI_Cart::ctlggi_cart_totals($subtotal=$total, $total);

				// cookie name
				$cart_items_cookie_name  = CTLGGI_Cookies::ctlggi_cart_items_cookie_name();
		
				// put item to cookie
				$value = json_encode($cart_items, true); // convert to array
				// set cookie, expires in 1 day
				$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$cart_items_cookie_name, $value, $expiry = 86400, $path = '/', $domain, $secure = false, $httponly = false );
			
	
        } 
		
	    #### important! #############
	    exit; // don't forget to exit!

    }
  
	/**
	 * Display Item Sale price on items listing and item view pages.
	 * Options Display values: default, first
	 *
	 * @since  1.0.0
	 * @access public static
	 * @param  int $post_id
	 * @param  string $item_price
	 * @param  string $display
	 * @return html $item_sale_price_public
	 */
    public static function ctlggi_display_item_sale_price_public($post_id, $item_price, $display='default') 
	{
		if ( empty( $post_id ) )
		return;
		
		$item_sale_price_public = ''; // default
		$enable_price_options   = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
		 // if price options enabled
		 if ( ! empty( $enable_price_options ) && $enable_price_options == '1' ) {
	
			$price_default_option = get_post_meta( $post_id, '_ctlggi_price_default_option', true );
			$price_options = get_post_meta( $post_id, '_ctlggi_price_options', true ); // json
			
			$price_options = json_decode($price_options, true);// convert into array
			/*
			echo '<pre>';
			print_r($price_options);
			echo '</pre>';
			*/
			if ( ! empty( $price_options ) && ! empty( $price_default_option ) ) {
				  foreach( $price_options as $key ) {
					  
					  if ( $display == 'default' ) {
						  // get default
						  if( $key['option_id'] == $price_default_option ) {
							  $option_price_default = $key['option_price'];
							  $item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$option_price_default);
						  }
					  } elseif ( $display == 'first' ) {
						  // get first element of the array
						  if ($key === reset($price_options)) {
							  $option_price_default = $key['option_price'];
							  $item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$option_price_default); 
						  } 
					  } else {
						  // get default
						  if( $key['option_id'] == $price_default_option ) {
							  $option_price_default = $key['option_price'];
							  $item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$option_price_default);
						  }
					  }
					  
					  // get last element of the array
					  if ($key === end($price_options)) {
						 
					  }
	
				  }
				  //$price_label = 'From';
				  
			} else {
				// if price options enabled but no option added display 0
				$amount = '0';
				$item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount);
			}
	
		} else {
				// item sale price
				$item_sale_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price);
		}
		
		return $item_sale_price_public;
		
	}

	/**
	 * Display payment button on product listing and product view pages.
	 *
	 * @since  1.0.0
	 * @param  array $payment_button
	 * @return void
	 */
    public static function shopping_cart_payment_buttons( $payment_button ) {
		
		if ( empty( $payment_button ) )
		return;
		
		$ctlggi_cart_options     = get_option('ctlggi_cart_options');
		$enable_shopping_cart    = isset( $ctlggi_cart_options['enable_shopping_cart'] ) ? sanitize_text_field( $ctlggi_cart_options['enable_shopping_cart'] ) : '';
		
		if ( $enable_shopping_cart != '1' )
		return;
		
		$payment_button = array(
		  'post_id'            => sanitize_text_field( $payment_button['post_id'] ),
		  'size'               => sanitize_text_field( $payment_button['size'] ),
		);
		
		$post_id = $payment_button['post_id'];
		$size    = $payment_button['size'];
		
		if ( empty( $post_id ) )
		return;
		
		$enable_quantity_field = get_post_meta( $post_id, '_ctlggi_enable_quantity_field', true ); 
		if ( $enable_quantity_field == '1' ) {
			$quantity = 'yes';
		} else {
			$quantity = 'no';
		}
		
		$subscription_enabled       = get_post_meta( $post_id, '_ctlggi_subsc_enabled', true );
		
		$buy_now_button = get_post_meta( $post_id, '_ctlggi_buy_now_button', true );
		if ( $buy_now_button == '1' && $subscription_enabled != '1' ) {
			$buy_now_guest_payment = get_post_meta( $post_id, '_ctlggi_buy_now_guest_payment', true );
			if ( $buy_now_guest_payment == '1' ) {
				$guest_payment = 'yes';
			} else {
				$guest_payment = 'no';
			}
			
			$defaultgateway = ''; // def
			$ctlggi_payment_gateway_options = get_option('ctlggi_payment_gateway_options');
			if ( ! empty($ctlggi_payment_gateway_options['default_payment_gateway']) ) {
				$defaultgateway   = $ctlggi_payment_gateway_options['default_payment_gateway']; // e.g. stripe
			}
			
			// buy now button
			echo do_shortcode('[ctlggi_payment_button id="' . esc_attr( $post_id ) . '" size="' . esc_attr( $size ) . '" type="buynow" gateway="' . esc_attr( $defaultgateway ) . '" custom_item="no" quantity="' . $quantity . '" guest_payment="' . $guest_payment . '" label_one=" - Buy Now" color_one="#28a0e5" label_two=" + Checkout" color_two="#28a0e5"]');
		} else {
			//  Add to Cart Button
			echo do_shortcode('[ctlggi_payment_button id="' . esc_attr( $post_id ) . '" size="' . esc_attr( $size ) . '" type="cart" custom_item="no" quantity="' . $quantity . '" guest_payment="no" label_one=" - Add To Cart" color_one="#ec7a5c" label_two=" + View Cart" color_two="#ec7a5c"]'); 
		}
		
	}

	
}

?>