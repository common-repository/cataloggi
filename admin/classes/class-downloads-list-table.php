<?php

/**
 * Downloads List Table class.
 *
 * @package     cataloggi
 * @subpackage  Admin/
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class CTLGGI_Downloads_List_Table extends WP_List_Table {
	
	/**
	 * Downloads Data
	 *
	 * @since 1.0.0
	 * @return array $downloads
	 */
	public function ctlggi_lt_get_downloadable_items() {
		// default
		$downloads            = '';
		$download_limit       = '';
		$download_expiry_date = '';
		$download_count       = '';
		$author               = '';
		// only admin
		if ( current_user_can( 'manage_options' ) ) {
		    $downloadable_items = CTLGGI_DB_Order_Items::ctlggi_select_all_order_downloadable_items(); // only admin 
		} 
		// this is for vendors
		elseif ( ! current_user_can( 'manage_options' ) && is_user_logged_in() ) {
			// if logged in get current user data
			$current_user = wp_get_current_user();
			$user_id      = $current_user->ID;
			// get user downloads
		    //$downloads    = CTLGGI_DB_Order_Downloads::ctlggi_select_downloads_by_user_id( $user_id ); // only logged in user
			$downloadable_items = '';
		}
		
		if ( ! empty($downloadable_items) ) {
			
            // create the columns array_reverse ASC order
			foreach ( array_reverse($downloadable_items) as $downloadable_item ) 
			{
				$order_item_id   = $downloadable_item['order_item_id'];
				$order_id        = $downloadable_item['order_id'];
				$order_item_name = $downloadable_item['order_item_name'];
				$order_post_url  = admin_url( 'post.php?post=' . $order_id ) . '&action=edit';
				$view_order      = '<a href="' . esc_url( $order_post_url ) . '"> #' . esc_attr( $order_id ) . '</a>';
				
				// get item ID
				$item_metas = CTLGGI_DB_Order_Items::ctlggi_select_order_item_meta( $order_item_id=$downloadable_item['order_item_id'] );
				foreach ( $item_metas as $item_meta ) 
				{
					// get the item id
					if ( $item_meta['meta_key'] == '_item_id' ) {
						$item_id = $item_meta['meta_value'];
					}
				}
				
				$item_post_url  = admin_url( 'post.php?post=' . $item_id ) . '&action=edit';
				$view_item      = '<a href="' . esc_url( $item_post_url ) . '">' . esc_attr( trim($order_item_name) ) . '</a>';
				
				// get download by order_id and item_id
				$get_download = CTLGGI_DB_Order_Downloads::ctlggi_select_single_download( $order_id, $item_id );
				$download_id    = $get_download[0]['id'];
				$download_limit = $get_download[0]['download_limit'];
				$download_expiry_date = $get_download[0]['download_expiry_date'];
				
				// get the author of the posted item (product) from the posts db
				$item      = get_post($item_id);
				$author_id = $item->post_author;
				
				// get user by
				$author_obj   = get_user_by('id', $author_id);
				$display_name = $author_obj->display_name;
				
				$download_limit_field = '<input style="width:68px;" class="inputfield" id="ctlggi_download_limit_' . $order_item_id . '" name="ctlggi_download_limit" type="number" value="' . esc_attr( $download_limit ) . '">';
				
				$never_expires = ' <a title="' . __('reset to never expires', 'cataloggi') . '" class="ctlggi_reset_download_expiry_date" id="' . $order_item_id . '" href="' . esc_url( '/' ) . '" onclick="return false;">' . esc_attr__('x', 'cataloggi') . '</a>';
				
				$download_expiry_date_field = '<input style="width:90px;" class="datepicker inputfield ctlggi_download_expiry_date" id="ctlggi_download_expiry_date_' . $order_item_id . '" name="ctlggi_download_expiry_date" type="text" value="' . esc_attr( $download_expiry_date ) . '">' . $never_expires;
				
                $download_count_field = '<input style="width:68px;" class="inputfield" id="ctlggi_download_count_' . $order_item_id . '" name="ctlggi_download_count" type="number" value="' . esc_attr( $get_download[0]['download_count'] ) . '">';
				
				$update_return_msg = '<span style="color: #3c763d;" class="ctlggi-update-download-form-return-data" id="ctlggi-update-download-form-return-data_' . $order_item_id . '"></span>';
				
                $update_button = ' <a title="' . __('update', 'cataloggi') . '" class="ctlggi_update_download_data" id="' . $order_item_id . '" href="' . esc_url( '/' ) . '" onclick="return false;" data-download-id="' . $download_id . '">' . esc_attr__('update', 'cataloggi') . '</a> ' . $update_return_msg;
				
				$downloads[] = array(
				   'order_item_name'       => $view_item,
				   'order_id'              => $view_order,
				   'download_limit'        => $download_limit_field,
				   'download_expiry_date'  => $download_expiry_date_field,
				   'download_count'        => $download_count_field,
				   'author'                => esc_attr( $display_name ),
				   'action'                => $update_button
				);
				
			}
		}
		
		return $downloads;
	}
	
	public function get_columns(){
	  $columns = array(
		'order_item_name'       => __('Item', 'cataloggi'),
		'order_id'              => __('Order ID', 'cataloggi'),
		'download_limit'        => __('Download Limit', 'cataloggi'),
		'download_expiry_date'  => __('Download Expiry Date', 'cataloggi'),
		'download_count'        => __('Download Count', 'cataloggi'),
		'author'                => __('Author', 'cataloggi'),
		'action'                => __('Action', 'cataloggi'),
	  );
	  return $columns;
	}
	
	public function prepare_items() {
	  
	  $data = $this->ctlggi_lt_get_downloadable_items();
	  
	  $columns  = $this->get_columns();
	  $hidden   = array();
	  $sortable = array();
	  //$sortable = $this->get_sortable_columns(); // use for shorting columns
	  $this->_column_headers = array($columns, $hidden, $sortable);
	  
	  $per_page = 12;
	  $current_page = $this->get_pagenum();
	  $total_items = count($data);
	  
	  if ( ! empty($data) ) {
		  // pagination, only ncessary because we are using array, from direct db simply use SQL's LIMIT
		  $found_data = array_slice($data,(($current_page-1)*$per_page),$per_page);
	  } else {
		  $found_data = ''; 
	  }
		/*
		echo '<pre>';
		print_r( $found_data );
		echo '</pre>';	
		exit;
		*/
	  
	  $this->set_pagination_args( array(
		'total_items' => $total_items,  //WE have to calculate the total number of items
		'per_page'    => $per_page      //WE have to determine how many items to show on a page
	  ) );
	  
	  $this->items = $found_data;
	}
	
	public static function ctlggi_find_string_in_array($arr, $string) {
		return array_filter($arr, function($value) use ($string) {
			return strpos($value, $string) != false;
		});
	}
	
	public function search_items( $string ) {
		
	  if ( empty( $string ) )
		return;
		
	  $array = $this->ctlggi_lt_get_downloadable_items();
      
	  // search array specified column if has the search string
	  // then list the found arrays 
	  $found_data = array();
      foreach ($array as $key => $value) {
          $item_names = $value['order_item_name'];
		  // check if string exist in each
			if (strpos($item_names, $string) != false) {
				$found_data[] = $array[$key];
			}
	  }
	  
	    /*
		echo '<pre>';
		print_r( $found_data );
		echo '</pre>';	
		exit;
		*/
	  
	  $columns  = $this->get_columns();
	  $hidden   = array();
	  $sortable = array();
	  //$sortable = $this->get_sortable_columns(); // use for shorting columns
	  $this->_column_headers = array($columns, $hidden, $sortable);
	  
	  $per_page = 20;
	  $current_page = $this->get_pagenum();
	  $total_items = count($found_data);
	  
      // pagination, only ncessary because we are using array, from direct db simply use SQL's LIMIT
      $found_data = array_slice($found_data,(($current_page-1)*$per_page),$per_page);
	  
	  $this->set_pagination_args( array(
		'total_items' => $total_items,  //WE have to calculate the total number of items
		'per_page'    => $per_page      //WE have to determine how many items to show on a page
	  ) );
	  
	  $this->items = $found_data;
	}
	
	public function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
		case 'order_item_name':
		case 'order_id':
		case 'download_limit':
		case 'download_expiry_date':
		case 'download_count':
		case 'author':
		case 'action':
		  return $item[ $column_name ];
		default:
		  return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}
	
	public function get_sortable_columns() {
	  $sortable_columns = array(
		'order_item_name'      => array('order_item_name',false),
		'order_id'             => array('order_id',false),
		'author'               => array('author',false)
	  );
	  return $sortable_columns;
	}
	
}

?>