<?php

/**
 * Items Metaboxes.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Items_Metaboxes {

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
	 * @since      1.0.0
	 * @param      string    $plugin_name    The name of the plugin.
	 * @param      string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Add taxonomy term field.
	 *
	 * @since 1.0.0
	 * @return html $output
	 */
	public function ctlggi_cataloggicat_tax_add_new_meta_field() 
	{
		/*
		// this will add the custom meta field to the add new term page
		$output = '<div class="form-field">';
		$output .= '<label for="term_meta[custom_term_meta]">Example meta field</label>';
		$output .= '<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">';
		$output .= '<p class="description">Enter a value for this field</p>';
		$output .= '</div>';
		*/
		
		// this will add the custom meta field to the add new term page
		$output  = '<div class="form-field">';
		$output .= '<label for="term_meta[cataloggi_thumb_img]">' . __( 'Category Image', 'cataloggi' ) . '</label>';
		$output .= '<input type="text" id="upload_image" name="term_meta[cataloggi_thumb_img]" value="">';
		$output .= '<input id="upload_image_button" class="button" type="button" value="' . __( 'Add Image', 'cataloggi' ) . '" />';
		//$output .= '<p class="description">' . __( 'Select or upload an image.', 'cataloggi' ) . '</p>';
		$output .= '</div>';
		
		echo $output; // <- use echo
	
	}
	
	/**
	 * Edit taxonomy term field.
	 *
	 * @since 1.0.0
	 * @param object $term
	 * @return html $output
	 */
	public function ctlggi_cataloggicat_tax_edit_meta_field($term) 
	{
		// default
		$value = '';
		$value2 = '';
	 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "ctlggi_cataloggicat_taxonomy_$t_id" );
		
		/*
		$value = esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : '';
		
		$output = '<tr class="form-field">';
		$output .= '<th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Example meta field</label></th>';
		$output .= '<td>';
		$output .= '<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="' . $value . '">';
		$output .= '<p class="description">Enter a value for this field</p>';
		$output .= '</td>';
		$output .= '</tr>';
		*/
		
		$value2 = $term_meta['cataloggi_thumb_img'] ? sanitize_text_field( $term_meta['cataloggi_thumb_img'] ) : '';
		
		$output  = '<tr class="form-field">';
		$output .= '<th scope="row" valign="top"><label for="term_meta[cataloggi_thumb_img]">' . __( 'Category Image', 'cataloggi' ) . '</label></th>';
		$output .= '<td>';
		$output .= '<input type="text" name="term_meta[cataloggi_thumb_img]" id="upload_image" value="' . esc_attr( $value2 ) . '">';
		$output .= '<input id="upload_image_button" class="button" type="button" value="' . __( 'Add Image', 'cataloggi' ) . '" />';
		//$output .= '<p class="description">' . __( 'Select or upload an image.', 'cataloggi' ) . '</p>';
		$output .= '</td>';
		$output .= '</tr>';
		
		echo $output; // <- use echo
	
	}
	
	/**
	 * Save extra taxonomy fields, callback function.
	 *
	 * @since 1.0.0
	 * @param int $term_id
	 * @return void
	 */
	public function ctlggi_cataloggicat_save_tax_custom_meta( $term_id ) 
	{
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = intval( $term_id );
			$term_meta = get_option( "ctlggi_cataloggicat_taxonomy_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = sanitize_text_field( $_POST['term_meta'][$key] );
				}
			}
			// Save the option array.
			update_option( "ctlggi_cataloggicat_taxonomy_$t_id", $term_meta );
		}
	}

	/**
	 * Add Metabox item data.
	 * 
	 * @uses add_meta_box()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_add_metabox_item_data() {
		add_meta_box(
			'ctlggi_item_data',
			__( 'Product Data', 'cataloggi' ),
			array( $this, 'ctlggi_render_metabox_item_data' ), 
			'cataloggi', // custom post type
			'normal',
			'high' // tells wordpress where to place the meta box in the context. 'high', 'default' or 'low' 
		);
	}

	/**
	 * Render Metabox for item data.
	 *
	 * @since 1.0.0
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_render_metabox_item_data( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'ctlggi_item_data_nonce_action', 'ctlggi_item_data_nonce' );
		
		// Get the meta for all keys for the current post
		$meta = get_post_meta( $post->ID );
		/*
		echo '<pre>';
		print_r($meta);
		echo '</pre>';
		*/

		// Retrieve an existing value from the database.
		$item_regular_price    = get_post_meta( $post->ID, '_ctlggi_item_regular_price', true );
		$item_price            = get_post_meta( $post->ID, '_ctlggi_item_price', true );
		$enable_quantity_field = get_post_meta( $post->ID, '_ctlggi_enable_quantity_field', true ); 
		$product_thumb_image   = get_post_meta( $post->ID, '_ctlggi_product_thumb_image', true );
		$item_sku              = get_post_meta( $post->ID, '_ctlggi_item_sku', true );
		$hide_product_on_catalog_home  = get_post_meta( $post->ID, '_ctlggi_hide_product_on_catalog_home', true );
		$item_downloadable     = get_post_meta( $post->ID, '_ctlggi_item_downloadable', true ); // checkbox
		
		$item_show_free_download_button  = get_post_meta( $post->ID, '_ctlggi_item_show_free_download_button', true );
		
		$item_custom_download_url  = get_post_meta( $post->ID, '_ctlggi_item_custom_download_url', true );
		$item_download_from_url  = get_post_meta( $post->ID, '_ctlggi_item_download_from_url', true );
		
		$demo_url_checkbox  = get_post_meta( $post->ID, '_ctlggi_demo_url_checkbox', true );
		$demo_url           = get_post_meta( $post->ID, '_ctlggi_demo_url', true );
		
		$docs_checkbox      = get_post_meta( $post->ID, '_ctlggi_docs_checkbox', true );
		$docs_url           = get_post_meta( $post->ID, '_ctlggi_docs_url', true );
		
		$enable_price_options  = get_post_meta( $post->ID, '_ctlggi_enable_price_options', true );
		$price_options         = get_post_meta( $post->ID, '_ctlggi_price_options', true ); // json
		$price_default_option  = get_post_meta( $post->ID, '_ctlggi_price_default_option', true );
		
		// downloadable items
		$item_file_name        = get_post_meta( $post->ID, '_ctlggi_item_file_name', true );
		$item_file_url         = get_post_meta( $post->ID, '_ctlggi_item_file_url', true );
		$item_download_limit   = get_post_meta( $post->ID, '_ctlggi_item_download_limit', true );
		$item_download_expiry  = get_post_meta( $post->ID, '_ctlggi_item_download_expiry', true );
		
		// get options
		$ctlggi_general_options  = get_option('ctlggi_general_options');
		// get options
		$ctlggi_cart_options     = get_option('ctlggi_cart_options');
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		
		//currency symbol
		$currpostfix = $ctlggi_currency_options['catalog_currency'];
		$currencysymbol = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $currpostfix );
		
		// Format Regular Price
		$item_regular_price = CTLGGI_Amount::ctlggi_format_amount($amount=$item_regular_price);
		// Format Sale Price
        $item_price = CTLGGI_Amount::ctlggi_format_amount($amount=$item_price);

		// Set default values.
		if( empty( $item_regular_price ) ) $item_regular_price = '';
		if( empty( $item_price ) ) $item_price = '';
		if( empty( $enable_quantity_field ) ) $enable_quantity_field = '';
		if( empty( $product_thumb_image ) ) $product_thumb_image = '';
		if( empty( $enable_price_options ) ) $enable_price_options = '';
		if( empty( $price_options ) ) $price_options = '';
		if( empty( $price_default_option ) ) $price_default_option = '';
		if( empty( $item_sku ) ) $item_sku = '';
		if( empty( $hide_product_on_catalog_home ) ) $hide_product_on_catalog_home = '';
		if( empty( $item_downloadable ) ) $item_downloadable = '';
		
		if( empty( $item_show_free_download_button ) ) $item_show_free_download_button = '0';
		
		if( empty( $item_custom_download_url ) ) $item_custom_download_url = '';
		if( empty( $item_download_from_url ) ) $item_download_from_url = '';
		
		if( empty( $demo_url_checkbox ) ) $demo_url_checkbox = '';
		if( empty( $demo_url ) ) $demo_url = '';
		if( empty( $docs_checkbox ) ) $docs_checkbox = '';
		if( empty( $docs_url ) ) $docs_url = '';
		
		if( empty( $item_file_name ) ) $item_file_name = '';
		if( empty( $item_file_url ) ) $item_file_url = '';
		if( empty( $item_download_limit ) ) $item_download_limit = '';
		if( empty( $item_download_expiry ) ) $item_download_expiry = '';

		// Table - Item Data
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'metaboxes/items/item-data.php';


	}

	/**
	 * Save Metabox item data.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_save_metabox_item_data( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_action = 'ctlggi_item_data_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $_POST['ctlggi_item_data_nonce'] ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $_POST['ctlggi_item_data_nonce'], $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;
		
		// get options
		$ctlggi_general_options = get_option('ctlggi_general_options');
		
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		
		// DOWNLOADABLE PRODUCTS
		// show fields only if shopping cart enabled on the settings [age
		if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
		{
			// Sanitize user input.
			$item_new_downloadable = isset( $_POST[ 'item_downloadable' ] ) ? sanitize_text_field( $_POST[ 'item_downloadable' ] ) : '0'; // checkbox
			
			// Update the meta field in the database.
			update_post_meta( $post_id, '_ctlggi_item_downloadable', $item_new_downloadable );
			
			// if checkbox checked returns 1
			if ( $item_new_downloadable == '1' ) {
				// update meta data, 
				$item_show_free_download_button   = isset( $_POST[ 'item_show_free_download_button' ] ) ? sanitize_text_field( $_POST[ 'item_show_free_download_button' ] ) : '0'; // checkbox
				
				$item_custom_download_url  = isset( $_POST[ 'item_custom_download_url' ] ) ? sanitize_text_field( $_POST[ 'item_custom_download_url' ] ) : '0';// checkbox
				$item_download_from_url    = isset( $_POST[ 'item_download_from_url' ] ) ? sanitize_text_field( $_POST[ 'item_download_from_url' ] ) : '';
		
		        $demo_url_checkbox  = isset( $_POST[ 'demo_url_checkbox' ] ) ? sanitize_text_field( $_POST[ 'demo_url_checkbox' ] ) : '0';// checkbox
				$demo_url           = isset( $_POST[ 'demo_url' ] ) ? sanitize_text_field( $_POST[ 'demo_url' ] ) : '';
				$docs_checkbox      = isset( $_POST[ 'docs_checkbox' ] ) ? sanitize_text_field( $_POST[ 'docs_checkbox' ] ) : '0';// checkbox
				$docs_url           = isset( $_POST[ 'docs_url' ] ) ? sanitize_text_field( $_POST[ 'docs_url' ] ) : '';
				
				
				$item_new_file_name        = isset( $_POST[ 'item_file_name' ] ) ? sanitize_text_field( $_POST[ 'item_file_name' ] ) : '';
				$item_new_file_url         = isset( $_POST[ 'item_file_url' ] ) ? sanitize_text_field( $_POST[ 'item_file_url' ] ) : '';
				$item_new_download_limit   = isset( $_POST[ 'item_download_limit' ] ) ? sanitize_text_field( $_POST[ 'item_download_limit' ] ) : '';
				$item_new_download_expiry  = isset( $_POST[ 'item_download_expiry' ] ) ? sanitize_text_field( $_POST[ 'item_download_expiry' ] ) : '';
				
				update_post_meta( $post_id, '_ctlggi_item_show_free_download_button', $item_show_free_download_button );
				
				update_post_meta( $post_id, '_ctlggi_item_custom_download_url', $item_custom_download_url );
				update_post_meta( $post_id, '_ctlggi_item_download_from_url', $item_download_from_url );
				
				update_post_meta( $post_id, '_ctlggi_demo_url_checkbox', $demo_url_checkbox );
				update_post_meta( $post_id, '_ctlggi_demo_url', $demo_url );
				update_post_meta( $post_id, '_ctlggi_docs_checkbox', $docs_checkbox );
				update_post_meta( $post_id, '_ctlggi_docs_url', $docs_url );
				
				update_post_meta( $post_id, '_ctlggi_item_file_name', $item_new_file_name );
				update_post_meta( $post_id, '_ctlggi_item_file_url', $item_new_file_url );
				update_post_meta( $post_id, '_ctlggi_item_download_limit', $item_new_download_limit );
				update_post_meta( $post_id, '_ctlggi_item_download_expiry', $item_new_download_expiry );
			}
		}

		// Sanitize user input.
		$item_new_regular_price = isset( $_POST[ 'item_regular_price' ] ) ? sanitize_text_field( $_POST[ 'item_regular_price' ] ) : '';
		$item_new_price         = isset( $_POST[ 'item_price' ] ) ? sanitize_text_field( $_POST[ 'item_price' ] ) : '';
		$enable_quantity_field  = isset( $_POST[ 'enable_quantity_field' ] ) ? sanitize_text_field( $_POST[ 'enable_quantity_field' ] ) : '0'; // checkbox
		$product_thumb_image    = isset( $_POST[ 'product_thumb_image' ] ) ? sanitize_text_field( $_POST[ 'product_thumb_image' ] ) : '';
		$enable_price_options   = isset( $_POST[ 'enable_price_options' ] ) ? sanitize_text_field( $_POST[ 'enable_price_options' ] ) : '';
		$item_new_sku           = isset( $_POST[ 'item_sku' ] ) ? sanitize_text_field( $_POST[ 'item_sku' ] ) : '';
		$hide_product_on_catalog_home   = isset( $_POST[ 'hide_product_on_catalog_home' ] ) ? sanitize_text_field( $_POST[ 'hide_product_on_catalog_home' ] ) : '';
		
		// Set default values.
		if( ! empty( $item_new_regular_price ) ) {
			$item_new_regular_price = CTLGGI_Amount::ctlggi_format_amount_to_string($amount=$item_new_regular_price);
		} else {
			$item_new_regular_price = '0';
		}
		
		// Set default values.
		if( ! empty( $item_new_price ) )  {
			$item_new_price = CTLGGI_Amount::ctlggi_format_amount_to_string($amount=$item_new_price);
		} else {
			$item_new_price = '0';
		}
		
		// Format Regular Price
		//$item_new_regular_price = CTLGGI_Amount::ctlggi_format_amount($amount=$item_new_regular_price);
		// Format Sale Price
        //$item_new_price = CTLGGI_Amount::ctlggi_format_amount($amount=$item_new_price);
		// amount to string
	
		if( $enable_price_options == '1' ) {
			$row_ids          = $_POST[ 'input_ctlggi_row_id' ]; // array
			$option_name      = $_POST[ 'ctlggi_price_option_name' ]; // array
			$option_price     = $_POST[ 'ctlggi_price_option_price' ]; // array	 	
			
			$price_default  = isset( $_POST[ 'ctlggi_price_default' ] ) ? sanitize_text_field( $_POST[ 'ctlggi_price_default' ] ) : ''; // radio
			
			if ( ! empty( $price_default ) ) {
				update_post_meta( $post_id, '_ctlggi_price_default_option', $price_default );
			} else {
				update_post_meta( $post_id, '_ctlggi_price_default_option', $row_ids[0] ); // set first as default if not selected
			}
			
			update_post_meta( $post_id, '_ctlggi_price_options', '' ); // empty meta
			
			if ( ! empty($row_ids) ) {
				
				$index = 0; // default
				foreach($row_ids as $row_id) {
					
				  if ( ! empty( $option_name[$index] ) && ! empty( $option_price[$index] ) ) {
					  
					  $price = sanitize_text_field( $option_price[$index] );
					  
					  //$price = CTLGGI_Amount::ctlggi_format_amount($amount=$price);
					  $price = CTLGGI_Amount::ctlggi_format_amount_to_string($amount=$price);
	
					  $options_list[$row_id] = array(
						  'option_id'        => intval( $row_id ),
						  'option_name'      => sanitize_text_field( $option_name[$index] ),
						  'option_price'     => $price
					  );	
				  
				  }
				  
				  $index++; // should be at the end of the loop
				}
				$price_options = json_encode($options_list); // convert into json
			} else {
				$price_options = '';
			}
		} 
		else {
		    $price_options = ''; // default
		}
		
		// Update the meta field in the database.
		update_post_meta( $post_id, '_ctlggi_item_regular_price', $item_new_regular_price );
		update_post_meta( $post_id, '_ctlggi_item_price', $item_new_price );
		update_post_meta( $post_id, '_ctlggi_enable_quantity_field', $enable_quantity_field );
		update_post_meta( $post_id, '_ctlggi_product_thumb_image', $product_thumb_image );
		update_post_meta( $post_id, '_ctlggi_enable_price_options', $enable_price_options );
		update_post_meta( $post_id, '_ctlggi_price_options', $price_options ); // json
		update_post_meta( $post_id, '_ctlggi_item_sku', $item_new_sku );
		update_post_meta( $post_id, '_ctlggi_hide_product_on_catalog_home', $hide_product_on_catalog_home );
		
		// download count
		$download_count  = get_post_meta( $post_id, '_ctlggi_item_download_count', true );
		if ( empty($download_count) ) {
		    update_post_meta( $post_id, '_ctlggi_item_download_count', '0' );
		}
		
		// remove action
		//remove_action( 'save_post', 'ctlggi_save_metabox_item_data', 13, 2 );

	}

	/**
	 * Add Metabox item short description.
	 * 
	 * @uses add_meta_box()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_add_metabox_item_short_desc() {
		add_meta_box(
			'ctlggi_item_short_desc',
			__( 'Short Description', 'cataloggi' ),
			array( $this, 'ctlggi_render_metabox_item_short_desc' ),
			'cataloggi', // custom post type
			'normal',
			'high' // tells wordpress where to place the meta box in the context. 'high', 'default' or 'low' 
		);
	}

	/**
	 * Render Metabox for item short description.
	 *
	 * @since 1.0.0
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_render_metabox_item_short_desc( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'ctlggi_item_short_desc_nonce_action', 'ctlggi_item_short_desc_nonce' );

		// Retrieve an existing value from the database.
		$item_short_desc = get_post_meta( $post->ID, '_ctlggi_item_short_desc', true );

		// Set default values.
		if( empty( $item_short_desc ) ) $item_short_desc = '';
	
		// Short Description
		echo '	<textarea  style="width:100%; min-width:200px;" id="item_short_desc" name="item_short_desc" class="short_desc_field" rows="3" cols="70">' . esc_textarea( $item_short_desc ) . '</textarea>';
		echo '	<p class="description"> ' . __( 'Enter the Product Short Description or leave the field blank.', 'cataloggi' ) . '</p>';

	}

	/**
	 * Save Metabox item short description.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_save_metabox_item_short_desc( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_action = 'ctlggi_item_short_desc_nonce_action'; 

		// Check if a nonce is set.
		if ( ! isset( $_POST['ctlggi_item_short_desc_nonce'] ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $_POST['ctlggi_item_short_desc_nonce'], $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// Sanitize user input.
		$item_new_short_desc = isset( $_POST[ 'item_short_desc' ] ) ? sanitize_text_field( $_POST[ 'item_short_desc' ] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, '_ctlggi_item_short_desc', $item_new_short_desc );

	}
	
	/**
	 * Add Metabox express checkout button.
	 * 
	 * @uses add_meta_box()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_add_metabox_payment_button() {
		
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		// show only if shopping cart enabled
		if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
		{
			add_meta_box(
				'ctlggi_payment_button',
				__( 'Payment Button', 'cataloggi' ),
				array( $this, 'ctlggi_render_payment_button' ),
				'cataloggi', // custom post type
				'side', // normal or side
				'high' // tells wordpress where to place the meta box in the context. 'high', 'default' or 'low' 
			);
		}
	}
	
	/**
	 * Render Metabox express checkout button.
	 *
	 * @since 1.0.0
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_render_payment_button( $post ) {
		
		// Add nonce for security and authentication.
		wp_nonce_field( 'ctlggi_payment_button_nonce_action', 'ctlggi_payment_button_nonce' );
		
	    $post_id = $post->ID;
		
		$buy_now_button   = get_post_meta( $post_id, '_ctlggi_buy_now_button', true );
		if( $buy_now_button == true ) { $buy_now_checked = 'checked="checked"'; } else { $buy_now_checked = ''; }
		
		$output = '';
		$output .= '<label><input type="checkbox" value="1" ' . $buy_now_checked . ' name="buy_now_button" />';
		$output .= '<strong>' . __( 'Buy Now Button', 'cataloggi' ) . '</strong><span class="ctlggi-tooltip tooltip-info-icon" title="' . __( 'The Buy Now Button will be displayed instead of the add to cart button.', 'cataloggi' ) . '"></span>';
		$output .= '</label>';
		//$output .= '<span class="tooltip-display" id="buy_now"> ' . __( 'The Buy Now Button will be displayed instead of the add to cart button.', 'cataloggi' ) . '</span>';
		$output .= '<p class="description"></p>';
		
		$buy_now_guest_payment   = get_post_meta( $post_id, '_ctlggi_buy_now_guest_payment', true );
		if( $buy_now_guest_payment == true ) { $buy_now_guest_checked = 'checked="checked"'; } else { $buy_now_guest_checked = ''; }
		
		$output .= '<label><input type="checkbox" value="1" ' . $buy_now_guest_checked . ' name="buy_now_guest_payment" />';
		$output .= '<strong>' . __( 'Buy Now Guest Payment', 'cataloggi' ) . '</strong><span class="ctlggi-tooltip tooltip-info-icon" title="' . __( "Allow guest payments if buy now button enabled.", 'cataloggi' ) . '"></span>';
		$output .= '</label>';
		//$output .= '<span class="tooltip-display" id="buy_now_guest"> ' . __( "Check if Buy Now Button guest payment is allowed.", 'cataloggi' ) . '</span>';
		$output .= '<p class="description"></p>';
		
		$payment_button_simple = '[ctlggi_payment_button id="' . $post_id . '"]';
		
		//$output .= '<br>';
		$output .= '<strong>' . __( 'Payment Button Shortcode', 'cataloggi' ) . '</strong>';
		$output .= '<p><strong>' . esc_html( $payment_button_simple ) . '</strong></p>';
		$output .= '<p class="description"> ' . __( 'Use this shortcode to display the Payment Button on your posts or pages.', 'cataloggi' ) . '</p>';
		
		echo $output;

	}
	
	/**
	 * Save Metabox data for the express checkout button.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param object $post
	 * @return void
	 */
	public function ctlggi_save_metabox_payment_button( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_action = 'ctlggi_payment_button_nonce_action'; 

		// Check if a nonce is set.
		if ( ! isset( $_POST['ctlggi_payment_button_nonce'] ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $_POST['ctlggi_payment_button_nonce'], $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// Sanitize user input.
		$buy_now_button = isset( $_POST[ 'buy_now_button' ] ) ? sanitize_text_field( $_POST[ 'buy_now_button' ] ) : ''; 
		
		if( ! empty( $buy_now_button ) )  {
			$buy_now = '1';
		} else {
			$buy_now = '0';
		}
		
		// Sanitize user input.
		$buy_now_guest_payment = isset( $_POST[ 'buy_now_guest_payment' ] ) ? sanitize_text_field( $_POST[ 'buy_now_guest_payment' ] ) : ''; 
		
		if( ! empty( $buy_now_guest_payment ) )  {
			$buy_now_guest = '1';
		} else {
			$buy_now_guest = '0';
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, '_ctlggi_buy_now_button', $buy_now );
		update_post_meta( $post_id, '_ctlggi_buy_now_guest_payment', $buy_now_guest );

	}


	
	
	
}

?>