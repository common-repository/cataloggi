<?php

/**
 * Admin Item Data class.
 *
 * @package     cataloggi
 * @subpackage  public/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_ADMIN_Order_Items_Data_Table {

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
	 * Display price options select field. Ajax
	 *
	 * @since  1.0.0
	 * @return html $output
	 */
    public function ctlggi_admin_data_table_price_options_select_field() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		$output = ''; // def
		
		$post_id = isset( $formData ) ? sanitize_text_field( $formData ) : '';
		
		if ( empty( $post_id ) )
		return;
		
		$enable_price_options  = get_post_meta( $post_id, '_ctlggi_enable_price_options', true ); 
		
		if( $enable_price_options == '1' ) {
			
			$item_name = get_the_title( $post_id );
			$item_downloadable = get_post_meta( $post_id, '_ctlggi_item_downloadable', true );
		  
			$price_options         = get_post_meta( $post_id, '_ctlggi_price_options', true ); // json
			$price_options         = json_decode($price_options, true); // convert into array  
			
			if ( ! empty ($price_options) && $price_options != 'null' )  {
				$output .= '<div id="display_price_option-' . $post_id . '" class="ctlggi_price_options_select_field">';
				$output .= '<select name="ctlggi_price_option_selector" id="ctlggi_price_option_selector" class="ctlggi_price_option_selector_class">';
				$output .= '<option selected="selected" value="0">' . __( 'Select Price Option', 'cataloggi' ) . '</option>'; // value is 0 for jquery
				foreach( $price_options as $key ) {	
					$price_option_id   = $key['option_id'];
					$price_option_name = sanitize_text_field( $key['option_name'] );
					$item_price  = sanitize_text_field( $key['option_price'] );
					
					// item data defaults
					$item_data = array(
					  'item_id'           => intval( $post_id ),
					  'item_price'        => sanitize_text_field( $item_price ),
					  'item_name'         => sanitize_text_field( $price_option_name ),
					  'item_quantity'     => '',
					  'item_downloadable' => $item_downloadable,
					  'price_option_id'   => $price_option_id,
					  'price_option_name' => $price_option_name,
					  'item_total'        => '',
					  'item_payment_type' => 'normal' // normal or subscription
					);
					
					$item_data = json_encode($item_data);
					// subscriptions and recurring payments use that
					$item_data = apply_filters( 'ctlggi_admin_data_table_price_options_select_field_filter', $item_data );
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
						$display_subsc_details_public = apply_filters( 'ctlggi_admin_data_table_price_options_select_field_display_subsc_data', $itemdata );
					} else {
						$display_subsc_details_public = '';
					}
					
					$item_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span (HTML)
					$item_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price); // return string
					$output .= '<option value="' . esc_attr__( $price_option_id ) . '">';
					$output .= esc_attr__( $price_option_name );
					$output .= ' ' . esc_attr__( strip_tags($item_price_public) ) . $display_subsc_details_public; // remove HTML from public item price
					$output .= '</option>';	 
				}
				$output .= '</select>';
				$output .= '</div>';
			}
		
		}
		
		echo $output;
		
        exit; // don't forget to exit!	
		
	}

	/**
	 * Data table insert new item. Ajax
	 *
	 * @since  1.0.0
	 * @return html $output
	 */
    public function ctlggi_admin_data_table_insert_new_item() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		// parse string
		parse_str($formData, $postdata);
		
		// Add nonce for security and authentication.
		$nonce_action = 'ctlggi_data_table_form_nonce_action';
        /*
		// Check if a nonce is set.
		if ( ! isset( $postdata['nonce'] ) )
			return;
		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $postdata['nonce'], $nonce_action ) )
			return;
		*/
		
		// def
		$item_name             = '';
		$item_price            = '0';
		$item_total            = '0';
		$item_downloadable     = '';
		$price_option_id       = '';
		$price_option_name     = '';
		$display_price_option  = '';
		
		$output = ''; // def
		
		$post_id = isset( $postdata['post_id'] ) ? sanitize_text_field( $postdata['post_id'] ) : '';
		
		if ( empty( $post_id ) )
		return;
		
		$price_option_id  = isset( $postdata['price_option_id'] ) ? sanitize_text_field( $postdata['price_option_id'] ) : '';
		
		// get data
		$enable_price_options  = get_post_meta( $post_id, '_ctlggi_enable_price_options', true );
		
		if( $enable_price_options == '1' && $price_option_id != '0'  && $price_option_id != '' ) {
		    $price_options  = get_post_meta( $post_id, '_ctlggi_price_options', true ); // json
		    $price_options  = json_decode($price_options, true); // convert into array   
		  
			foreach( $price_options as $key ) {
				// get default
				if( $key['option_id'] == $price_option_id ) {
					$price_option_id   = $key['option_id'];
					$price_option_name = $key['option_name'];
					$item_price        = esc_attr__( $key['option_price'] );
				}
			}
		  
		} else {
		  // get post meta _ctlggi_item_price
		  $item_price = get_post_meta( $post_id, '_ctlggi_item_price', true );
		}
		
		$item_name = get_the_title( $post_id );
		$item_downloadable = get_post_meta( $post_id, '_ctlggi_item_downloadable', true );
		
		// item data defaults
		$item_data = array(
		  'item_id'           => intval( $post_id ),
		  'item_price'        => sanitize_text_field( $item_price ),
		  'item_name'         => sanitize_text_field( $item_name ),
		  'item_quantity'     => '',
		  'item_downloadable' => $item_downloadable,
		  'price_option_id'   => $price_option_id,
		  'price_option_name' => $price_option_name,
		  'item_total'        => '',
		  'item_payment_type' => 'normal' // normal or subscription
		);
		
		$item_data = json_encode($item_data);
		// subscriptions and recurring payments use that
		$item_data = apply_filters( 'ctlggi_admin_data_table_insert_new_item_filter', $item_data );
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
			$display_subsc_details_public = apply_filters( 'ctlggi_admin_data_table_insert_new_item_display_subsc_data', $itemdata );
		} else {
			$display_subsc_details_public = '';
		}
		
		if ( ! empty($price_option_id) && ! empty($price_option_name) ) {
			$display_price_option = '<span style="display:block;">' . esc_attr( $price_option_name ) . '</span>';
		}
		
		$output .= '<tr class="ctlggi-order-items">';
		$output .= '<input type="hidden" class="input-ctlggi-item-id" name="ctlggi_item_id[]" value="' . $post_id . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-name" name="ctlggi_item_name[]" value="' . $item_name . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-price" name="ctlggi_item_price[]" value="' . $item_price . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-single-item-total" name="ctlggi_single_item_total[]" value="' . $item_price . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-downloadable" name="ctlggi_item_downloadable[]" value="' . $item_downloadable . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-price-option-id" name="ctlggi_price_option_id[]" value="' . $price_option_id . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-price-option-name" name="ctlggi_price_option_name[]" value="' . $price_option_name . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-payment-type" name="ctlggi_item_payment_type[]" value="' . $item_payment_type . '"/>'; 
	
		$output .= '<input type="hidden" class="input-ctlggi-subsc-recurring" name="ctlggi-subsc-recurring" value="' . $subsc_recurring . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-subsc-interval" name="ctlggi-subsc-interval" value="' . $subsc_interval . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-subsc-interval-count" name="ctlggi-subsc-interval-count" value="' . $subsc_interval_count . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-subsc-times" name="ctlggi-subsc-times" value="' . $subsc_times . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-subsc-signupfee" name="ctlggi-subsc-signupfee" value="' . $subsc_signupfee . '"/>'; 
		$output .= '<input type="hidden" class="input-ctlggi-subsc-trial" name="ctlggi-subsc-trial" value="' . $subsc_trial . '"/>'; 
		
		$output .= '<td data-title="Product">' . $item_name . ' ' . $display_price_option . '</td>';
		$output .= '<td data-title="Price"><div class="html-ctlggi-single-item-price">' . $item_price_public . '</div></td>';
		
		$output .= '<td data-title="Quantity"><div class="cataloggi-item-quantity">';
		$output .= '<input class="input-ctlggi-item-quantity" type="number" max="" min="1" value="' . '1' . '" name="ctlggi_item_quantity[]" >';
		$output .= '</div></td>';
		
		$output .= '<td data-title="Total"><span class="html-ctlggi-single-item-total">' . $item_price_public . '</span> ' . $display_subsc_details_public . ' </td>';
		
		$output .= '<td><a id="' . $post_id . '" data-item-id="' . $post_id . '" class="ctlggi-remove-item" href="/" onclick="return false;">remove</a></td>';
		
		$output .= '</tr>';

		echo $output;
		
        exit; // don't forget to exit!	
		
	}
	
	/**
	 * Data table insert new custom item. Ajax
	 *
	 * @since  1.0.0
	 * @return html $output
	 */
    public function data_table_insert_new_custom_item() 
	{
		// get form data
		$formData = $_POST['formData'];
		
		if ( empty( $formData ) )
		return;
		
		// parse string
		parse_str($formData, $postdata);
		
		// Add nonce for security and authentication.
		$nonce_action = 'ctlggi_data_table_form_nonce_action';
		
		// def
		$item_name             = '';
		$item_price            = '0';
		$item_total            = '0';
		$item_downloadable     = '';
		$price_option_id       = '';
		$price_option_name     = '';
		$display_price_option  = '';
		
		$output = ''; // def
		
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		
		//currency symbol
		$currpostfix = $ctlggi_currency_options['catalog_currency'];
		$currencysymbol = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $currpostfix );
		
		// create custom item id
		$random_string = CTLGGI_Helper::ctlggi_generate_random_string($length=8);
		$item_id_custom = 'custom_' . $random_string;
		
		// random item id for custom items
		$min=10000;
		$max=999999;
		$rand_num =  mt_rand($min,$max);
		$randnum  = '00' . $rand_num;
		
		// item data defaults
		$item_data = array(
		  'item_id'           => $randnum,
		  'item_price'        => '0',
		  'item_name'         => '',
		  'item_quantity'     => '',
		  'item_downloadable' => '0',
		  'price_option_id'   => '',
		  'price_option_name' => '',
		  'item_total'        => '0',
		  'item_payment_type' => 'normal' // normal or subscription
		);
		
		// item data
		$item_id              = isset( $item_data['item_id'] ) ? sanitize_text_field( $item_data['item_id'] ) : '';
		$item_price           = isset( $item_data['item_price'] ) ? sanitize_text_field( $item_data['item_price'] ) : '';
		$item_name            = isset( $item_data['item_name'] ) ? sanitize_text_field( $item_data['item_name'] ) : '';
		$item_quantity        = isset( $item_data['item_quantity'] ) ? sanitize_text_field( $item_data['item_quantity'] ) : '';
		$item_downloadable    = isset( $item_data['item_downloadable'] ) ? sanitize_text_field( $item_data['item_downloadable'] ) : '';
		$price_option_id      = isset( $item_data['price_option_id'] ) ? sanitize_text_field( $item_data['price_option_id'] ) : '';
		$item_total           = isset( $item_data['item_total'] ) ? sanitize_text_field( $item_data['item_total'] ) : '';
		$item_payment_type    = isset( $item_data['item_payment_type'] ) ? sanitize_text_field( $item_data['item_payment_type'] ) : '';
		
		$item_price_public = CTLGGI_Amount::ctlggi_amount_public($amount=$item_price); // return span (HTML)
		$item_price_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$item_price); // return string
		
		$output .= '<tr class="ctlggi-order-items">';
		$output .= '<input type="hidden" class="input-ctlggi-item-id" name="ctlggi_item_id[]" value="' . $item_id . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-single-item-total" name="ctlggi_single_item_total[]" value="' . $item_price . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-downloadable" name="ctlggi_item_downloadable[]" value="' . $item_downloadable . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-price-option-id" name="ctlggi_price_option_id[]" value="' . $price_option_id . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-price-option-name" name="ctlggi_price_option_name[]" value="' . $price_option_name . '"/>';
		$output .= '<input type="hidden" class="input-ctlggi-item-payment-type" name="ctlggi_item_payment_type[]" value="' . $item_payment_type . '"/>'; 
		
		$output .= '<td data-title="Product"><input type="text" class="input-ctlggi-item-name" name="ctlggi_item_name[]" value="' . $item_name . '"/></td>';
		$output .= '<td data-title="Price">' . esc_attr( $currencysymbol ) . '<input type="text" class="input-ctlggi-item-price" name="ctlggi_item_price[]" value="' . $item_price_hidden . '"/></td>';
		
		$output .= '<td data-title="Quantity"><div class="cataloggi-item-quantity">';
		$output .= '<input class="input-ctlggi-item-quantity" type="number" max="" min="1" value="' . '1' . '" name="ctlggi_item_quantity[]" >';
		$output .= '</div></td>';
		
		$output .= '<td data-title="Total"><span class="html-ctlggi-single-item-total">' . $item_price_public . '</span> </td>';
		
		$output .= '<td><a id="' . $item_id . '" data-item-id="' . $item_id . '" class="ctlggi-remove-item" href="/" onclick="return false;">remove</a></td>';
		
		$output .= '</tr>';

		echo $output;
		
        exit; // don't forget to exit!	
		
	}
	
	/**
	 * Update total. Ajax
	 *
	 * @since  1.0.0
	 * @return string $total
	 */
    public function ctlggi_admin_data_table_update_total() 
	{
		// get form data
		$formData = $_POST['formData'];
		$formData = stripslashes($formData);
		
		if ( empty( $formData ) )
		return;
		
		$obj_data_table_items = json_decode($formData);
		$arr_data_table_items = json_decode($formData, true);
		
		// defaults
        $total      = '0';
		$item_total = '0';
		
		//print_r( $arr_data_table_items );
		
		// if cart has contents
		if(count( $arr_data_table_items ) > 0 ) {
			
			foreach( $arr_data_table_items as $key => $value ){	
				
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
		
		$total_public = CTLGGI_Amount::ctlggi_amount_public($amount=$total); // return string
		$total_hidden = CTLGGI_Amount::ctlggi_amount_hidden($amount=$total); // return string
		echo json_encode(array('total_hidden'=>$total_hidden, 'total_public'=>$total_public )); // return json	
		
        exit; // don't forget to exit!	
		
	}
	
	
}

?>