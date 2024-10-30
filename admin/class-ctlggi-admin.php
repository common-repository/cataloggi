<?php

/**
 * Admin Main class.
 *
 * @package     cataloggi
 * @subpackage  Admin
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CTLGGI_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Admin notices.
	 */
	public $admin_notices;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string    $plugin_name  The name of this plugin.
	 * @param      string    $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Get all pages.
	 *
	 * @since      1.0.0
	 * @param      array    $data
	 */
	public static function get_all_pages() {
		global $wpdb;
		
		$data = ''; // def
		
		$table = $wpdb->prefix . 'posts'; // table, do not forget about tables prefix
		$sql  = "
				SELECT *
				FROM $table
				WHERE post_status = 'publish' and post_type = 'page' ORDER BY ID DESC
				";
		// save each result in array		
		$results = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
		if ( ! empty($results) ) {	
			$data = $results;
		}
		
		return $data;
	}
	
	/**
	 * If multisite enabled add mimes.
	 *
	 * @since     1.0.0
	 * @param     array    $existing_mimes
     * @return    array    $existing_mimes
	 */
	public function ctlggi_add_mimes_multisite( $existing_mimes=array() ) {
		if ( is_multisite() ) {
			// add your extension to the mimes array as below
			$existing_mimes['zip']     = 'application/zip';
			$existing_mimes['gz|gzip'] = 'application/x-gzip';
			$existing_mimes['rar']     = 'application/rar';
		} 
		return $existing_mimes;
	}
	
	/**
	 * Create custom upload dir.
	 *
	 * @since      1.0.0
	 * @param      array    $pathdata
     * @return     array    $pathdata
	 */
	public function ctlggi_custom_prefix_upload_dir( $pathdata ) {
		global $current_user,$pagenow;
		$posttype = 'cataloggi';
		if ( ( 'async-upload.php' == $pagenow || 'media-upload.php' == $pagenow ) && false != strpos( wp_get_referer(),'post_type=' . $posttype ) ) {
			$custom_dir = '/cataloggi-uploads';
			if ( empty( $pathdata[ 'subdir' ] ) ) {
				$pathdata[ 'path' ] = $pathdata[ 'path' ] . $custom_dir;
				$pathdata[ 'url' ] = $pathdata[ 'url' ] . $custom_dir;
				$pathdata[ 'subdir' ] = '/cataloggi-uploads-sub';
			} else {
				$new_subdir = $custom_dir . $pathdata[ 'subdir' ];

				$pathdata[ 'path' ] = str_replace( $pathdata[ 'subdir' ], $new_subdir, $pathdata[ 'path' ] );
				$pathdata[ 'url' ] = str_replace( $pathdata[ 'subdir' ], $new_subdir, $pathdata[ 'url' ] );
				$pathdata[ 'subdir' ] = str_replace( $pathdata[ 'subdir' ], $new_subdir, $pathdata[ 'subdir' ] );
			}
		}

		return $pathdata;
	}
	
	
	/**
	 * Handle upload.
	 *
	 * @since      1.0.0
	 * @param      array    $pathdata
	 */
	public function ctlggi_load_custom_upload_filter() {
		global $pagenow;
	    $posttype = 'cataloggi';
		if ( ! empty( $_REQUEST['post_id'] ) && ( 'async-upload.php' == $pagenow || 'media-upload.php' == $pagenow ) ) {
			if ( $posttype == get_post_type( $_REQUEST['post_id'] ) ) {
				add_filter( 'upload_dir', array($this, 'ctlggi_set_custom_upload_dir') );
			}
		}
	}
	
	/**
	 * custom upload folder for media uploads - e.g. custom post type featured images, donloadable products.
	 *
	 * @since      1.0.0
	 * @param      array    $upload
     * @return     array    $upload
	 */
	public function ctlggi_set_custom_upload_dir($upload) {
		$upload['subdir']   = '/cataloggi-uploads' . $upload['subdir'];
		$upload['path'] = $upload['basedir'] . $upload['subdir'];
		$upload['url'] = $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}

	/**
	 * Create custom image sizes.
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function ctlggi_custom_image_sizes() {
		
		// source: https://developer.wordpress.org/reference/functions/add_image_size/
		add_image_size( 'cataloggi-item-thumb', 440, 330, true ); // 220 pixels wide by 180 pixels tall, hard crop mode
		add_image_size( 'cataloggi-item-view', 640, 480, true ); // 220 pixels wide by 180 pixels tall, hard crop mode
		
		/* Additional Image Sizes by KB */
		add_image_size( 'cataloggi-small-size-p', '300', '400', true ); /* portrait */
		add_image_size( 'cataloggi-small-size-l', '400', '300', true ); /* landscape */
		
	}

	/**
	 * Show custom image sizes.
	 *
	 * @since      1.0.0
	 * @param      array    $sizes
     * @return     array    $sizes
	 */
	public function ctlggi_show_image_sizes($sizes) {
		$sizes['cataloggi-item-thumb'] = __( 'Cataloggi Item Thumb 440 x 330', 'cataloggi' );
		$sizes['cataloggi-item-view'] = __( 'Cataloggi Item View 640 x 480', 'cataloggi' );
		$sizes['cataloggi-small-size-p'] = __( 'Cataloggi Small Portrait', 'cataloggi' );
		$sizes['cataloggi-small-size-l'] = __( 'Cataloggi Small Landscape', 'cataloggi' );
		return $sizes;
	}

	/**
	 * Create admin submenu and pages.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_add_admin_menu() 
	{
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		// if shopping cart enabled
		if ( $ctlggi_cart_options['enable_shopping_cart'] == '1' ) 
		{
			// Downloads
			add_submenu_page (  
							$parent_slug = 'edit.php?post_type=cataloggi', 
							$page_title  = __( 'Cataloggi - Order Downloads', 'cataloggi' ), 
							$menu_title  = __( 'Downloads', 'cataloggi' ), 
							$capability  = 'manage_options', // manage_options for only admin, admin, editor, user etc.
							$menu_slug   = 'cataloggi-order-downloads', 
							$function    = array( $this, 'ctlggi_order_downloads_page')
							);
		}
	    // Settings
		add_submenu_page (  
						$parent_slug = 'edit.php?post_type=cataloggi', 
						$page_title  = __( 'Cataloggi - Settings', 'cataloggi' ), 
						$menu_title  = __( 'Settings', 'cataloggi' ), 
						$capability  = 'manage_options', // manage_options for only admin, admin, editor, user etc.
						$menu_slug   = 'cataloggi-settings', 
						$function    = array( $this, 'ctlggi_settings_page')
						);
	}
	
	/**
	 * Admin order downloads page. List ordered downloads.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_order_downloads_page() 
	{
		$newDownloadsListTable = new CTLGGI_Downloads_List_Table();
		echo '<div class="wrap"><h2>' . __( 'Cataloggi - Manage Downloads', 'cataloggi' ) . '</h2>'; 
		
		$s_query = ''; // def
        //Fetch, prepare, sort, and filter our data...
        if( isset($_POST['s']) ){
		   $string = $_POST['s'];
           $newDownloadsListTable->search_items( $string );
        } else {
           $newDownloadsListTable->prepare_items();
        }
		
		// display the search box
		echo '<form method="post">';
		echo '<input type="hidden" name="page" value="' . $_REQUEST['page'] . '" />';
		$newDownloadsListTable->search_box('search', 's');
		echo '</form>';
		
		$newDownloadsListTable->display(); // call display() to actually display the table
		echo '</div>'; 
	}
	
	/**
	 * Admin settings MAIN page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_settings_page() 
	{
		// get options
		$ctlggi_general_options = get_option('ctlggi_general_options');
		// get options
		$ctlggi_currency_options = get_option('ctlggi_currency_options');
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		// get options
		$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
		// get options
		$ctlggi_payment_gateway_options = get_option('ctlggi_payment_gateway_options');
		// get options
		$ctlggi_template_options = get_option('ctlggi_template_options');
		$ctlggi_template_system_options = get_option('ctlggi_template_system_options');
		// get options
		$ctlggi_gateway_bacs_options = get_option('ctlggi_gateway_bacs_options');
		// get options
		$ctlggi_gateway_paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
		// get options
		$ctlggi_google_analytics_options = get_option('ctlggi_google_analytics_options');
		//echo 'Settigs page';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/settings-main-page.php'; 
	}

	/**
	 * Output admin notification messages.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_output_admin_notices() 
	{
		if ( ! empty($this->admin_notices) ) {
			echo $this->admin_notices;
		}
	}

	/**
	 * Output admin forms validation messages.
	 *
	 * @since      1.0.0
	 * @param      array    $validation
	 * @param      string   $type
     * @return     void
	 */
    public function adminFormsValidation($validation='', $type='success') 
	{
		$output = '';
		
	    if ( $validation != '') {
		
			if ($type == 'success') {
				$type = 'notice notice-success is-dismissible'; // css
			} elseif ($type == 'error') {
				$type = 'notice notice-error'; // css
			}
			
			// display validation error messages
			if( $validation != '' ) {
				//$output .= '<div class="cw-form-msgs">';
				foreach ($validation as $validate ) {
				  $output .= '<div class="' . esc_attr( $type ) . '" id="setting-error-" >';
				  $output .= '<p><strong>' . esc_attr( $validate ) . '</strong></p>'; 
				  $output .= '<button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'cataloggi' ) . '</span></button>'; 
				  $output .= '</div>';
				}
				//$output .= '</div>';
			}
			//return $output;	
			$this->admin_notices = $output;
		
		} else {
			return false;
		}
	}
	
	/**
	 * Settings general sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_general_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-general-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-general-options-form-nonce'], 'ctlggi_general_options_form_nonce') )
	    {
			// Items Options
			$default_items_view    = sanitize_text_field( $_POST['default_items_view'] );
			
			// Checkbox - Price
			if( isset( $_POST['display_item_thumb_img'] ) ) {
				$display_item_thumb_img = '1';
			} else {
				$display_item_thumb_img = '0';
			}
			
			// Checkbox - Price
			if( isset( $_POST['display_item_price'] ) ) {
				$display_item_price = '1';
			} else {
				$display_item_price = '0';
			}
			
			// Checkbox - Display item short description on item listing page.
			if( isset( $_POST['display_item_short_desc'] ) ) {
				$display_item_short_desc = '1';
			} else {
				$display_item_short_desc = '0';
			}
			
			$number_of_items_per_page = sanitize_text_field( $_POST['number_of_items_per_page'] );
			$items_order_by           = sanitize_text_field( $_POST['items_order_by'] );	
			$items_order_radio        = sanitize_text_field( $_POST['items_order'] ); // radio
			if ( $items_order_radio == 'ASC' ) {
				$items_order = 'ASC';
			} else {
				$items_order = 'DESC';
			}

			// Category Options
			
			// Checkbox - // Display Category Boxes
			if( isset( $_POST['display_cat_boxes'] ) ) {
				$display_cat_boxes = '1';
			} else {
				$display_cat_boxes = '0';
			}
			
			// Checkbox - // Display Main Categories on the catalog homepage and sub category boxes on the catalog pages
			if( isset( $_POST['display_category_boxes'] ) ) {
				$display_category_boxes = '1';
			} else {
				$display_category_boxes = '0';
			}
			
			// Checkbox - // Display the categories drop down navigation top of the products list
			if( isset( $_POST['categories_drop_down_nav'] ) ) {
				$display_cat_drop_down_nav = '1';
			} else {
				$display_cat_drop_down_nav = '0';
			}
			
			$category_order_by    = sanitize_text_field( $_POST['category_order_by'] );
			// radio
			$category_order_radio = sanitize_text_field( $_POST['category_order'] );
			if ( $category_order_radio == 'ASC' ) {
				$category_order = 'ASC';
			} else {
				$category_order = 'DESC';
			}

			// Parent Menu Options
			$parent_menu_order_by = sanitize_text_field( $_POST['parent_menu_order_by'] );
			// radio
			$parent_menu_order_radio   = sanitize_text_field( $_POST['parent_menu_order'] );
			if ( $parent_menu_order_radio == 'ASC' ) {
				$parent_menu_order = 'ASC';
			} else {
				$parent_menu_order = 'DESC';
			}
			
			// Sub Menu Options
			$sub_menu_order_by = sanitize_text_field( $_POST['sub_menu_order_by'] );
			// radio
			$sub_menu_order_radio        = sanitize_text_field( $_POST['sub_menu_order'] );
			if ( $sub_menu_order_radio == 'ASC' ) {
				$sub_menu_order = 'ASC';
			} else {
				$sub_menu_order = 'DESC';
			}
			
			$product_view_featured_image  = isset( $_POST['product_view_featured_image'] ) ? sanitize_text_field( $_POST['product_view_featured_image'] ) : '';
			
			$ctlggi_general_options = get_option('ctlggi_general_options');
			$version = $ctlggi_general_options['version'];
			if( trim($version) == false ) $version = '';
			
			$arr = array(
				'version'                      => $version,
				'enable_tangible_items'        => '0', // important!!! leave this 0 until tangible items codes created
				'display_item_price'           => $display_item_price,
				'default_items_view'           => $default_items_view,
				'display_item_thumb_img'       => $display_item_thumb_img,
				'display_item_short_desc'      => $display_item_short_desc,
				'product_view_featured_image'  => $product_view_featured_image,
				'number_of_items_per_page'     => $number_of_items_per_page,
				'items_order_by'               => $items_order_by,
				'items_order'                  => $items_order, 
				'display_cat_boxes'            => $display_cat_boxes,
				'display_category_boxes'       => $display_category_boxes,
				'categories_drop_down_nav'     => $display_cat_drop_down_nav,
				'category_order_by'            => $category_order_by, 
				'category_order'               => $category_order, 
				'parent_menu_order_by'         => $parent_menu_order_by, 
				'parent_menu_order'            => $parent_menu_order,
				'sub_menu_order_by'            => $sub_menu_order_by, 
				'sub_menu_order'               => $sub_menu_order,
				'save_options_settings'        => '1'
			);

            update_option('ctlggi_general_options', $arr);  
			// success message
			$validation[] = __('General settings has been updated. ', 'cataloggi');
			// validation
			$this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
	    }
	  }

    }
	
	/**
	 * Settings currency sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_currency_options_form_process() 
	{	
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-currency-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-currency-options-form-nonce'], 'ctlggi_currency_options_form_nonce') )
	    {
			// Currency
			$catalog_currency      = sanitize_text_field( $_POST['catalog_currency'] );
            
			// get currency name
			$catalog_currency_name = CTLGGI_Amount::ctlggi_get_currency_name( $catalog_currency );
			
			$currency_position     = sanitize_text_field( $_POST['currency_position'] );
			
			$thousand_separator    = sanitize_text_field( $_POST['thousand_separator'] );
			$decimal_separator     = sanitize_text_field( $_POST['decimal_separator'] );
			$number_of_decimals    = sanitize_text_field( $_POST['number_of_decimals'] );

			$ctlggi_currency_options = get_option('ctlggi_currency_options');
			$version = $ctlggi_currency_options['version'];
			if( trim($version) == false ) $version = '';
			
			$arr = array(
				'version'               => $version,
				'catalog_currency'      => $catalog_currency,
				'catalog_currency_name' => sanitize_text_field( $catalog_currency_name ),
				'currency_position'     => $currency_position, // Left or Right
				'thousand_separator'    => $thousand_separator,
				'decimal_separator'     => $decimal_separator,
				'number_of_decimals'    => $number_of_decimals
			);

            update_option('ctlggi_currency_options', $arr);
				  
			// success message
			$validation[] = __('Currency settings has been updated. ', 'cataloggi');
			// validation
			$this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error'); 
	    }
	  }

    }
	
	/**
	 * Settings cart sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_cart_options_form_process() 
	{
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-cart-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-cart-options-form-nonce'], 'ctlggi_cart_options_form_nonce') )
	    {
			
			// Checkbox - Shopping Cart
			if( isset( $_POST['enable_shopping_cart'] ) ) {
				$enable_shopping_cart = '1';
			} else {
				$enable_shopping_cart = '0';
			}
			
			$display_payment_button  = isset( $_POST['display_payment_button'] ) ? sanitize_text_field( $_POST['display_payment_button'] ) : '';
			
			$cart_page            = sanitize_text_field( $_POST['cart_page'] );
			$checkout_page        = sanitize_text_field( $_POST['checkout_page'] );
			$payments_page        = sanitize_text_field( $_POST['payments_page'] );
			$terms_page           = sanitize_text_field( $_POST['terms_page'] );
			$success_page         = sanitize_text_field( $_POST['success_page'] );
			$order_history_page   = sanitize_text_field( $_POST['order_history_page'] );
			$login_redirect_page  = sanitize_text_field( $_POST['login_redirect_page'] );

			$ctlggi_cart_options = get_option('ctlggi_cart_options');
			$version = $ctlggi_cart_options['version'];
			if( trim($version) == false ) $version = '';

			$arr = array(
				'version'                => $version,
				'enable_shopping_cart'   => $enable_shopping_cart,
				'display_payment_button' => $display_payment_button, // Display Payment Buttons on the product listing pages
				'cart_page'              => $cart_page,
				'terms_page'             => $terms_page,
				'checkout_page'          => $checkout_page,
				'payments_page'          => $payments_page,
				'success_page'           => $success_page,
				'order_history_page'     => $order_history_page,
				'login_redirect_page'    => $login_redirect_page
			);

            update_option('ctlggi_cart_options', $arr);
				  
			// success message
			$validation[] = __('Cart settings has been updated. ', 'cataloggi');
			// validation
			$this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');  
	    }
	  }

    }
	
	/**
	 * Settings save settings sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_save_settings_options_form_process() 
	{
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-save-settings-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-save-settings-options-form-nonce'], 'ctlggi_save_settings_options_form_nonce') )
	    {
			
			$cataloggi_cpt_rewrite_slug   = sanitize_text_field( $_POST['ctlggi_cataloggi_cpt_rewrite_slug'] );
			$categories_tax_rewrite_slug  = sanitize_text_field( $_POST['ctlggi_categories_tax_rewrite_slug'] );
			
			// Checkbox - Plugin Deactivation
			if( isset( $_POST['ctlggi_plugin_deactivation_save_settings'] ) ) {
				$plugin_deactivation = '1';
			} else {
				$plugin_deactivation = '0';
			}
			
			// Checkbox - Plugin Uninstall
			if( isset( $_POST['ctlggi_plugin_uninstall_save_settings'] ) ) {
				$plugin_uninstall = '1';
			} else {
				$plugin_uninstall = '0';
			}
			
			// Checkbox - Display Cataloggi as a Home page
			if( isset( $_POST['ctlggi_redirect_wp_home_to_cataloggi'] ) ) {
				$cataloggi_home = '1';
			} else {
				$cataloggi_home = '0';
			}
			
			// Checkbox - Display the Grid Buttons
			if( isset( $_POST['ctlggi_display_grid_buttons'] ) ) {
				$display_grid_buttons = '1';
			} else {
				$display_grid_buttons = '0';
			}

			$ctlggi_save_settings_options = get_option('ctlggi_save_settings_options');
			$version = $ctlggi_save_settings_options['version'];
			if( trim($version) == false ) $version = '';

			$arr = array(
				'version'                                  => $version,
				'ctlggi_plugin_deactivation_save_settings' => $plugin_deactivation,
				'ctlggi_plugin_uninstall_save_settings'    => $plugin_uninstall,
				'ctlggi_cataloggi_cpt_rewrite_slug'        => $cataloggi_cpt_rewrite_slug,
				'ctlggi_categories_tax_rewrite_slug'       => $categories_tax_rewrite_slug,
				'ctlggi_redirect_wp_home_to_cataloggi'     => $cataloggi_home,
				'ctlggi_display_grid_buttons'              => $display_grid_buttons // normal, large, list buttons
			);

            update_option('ctlggi_save_settings_options', $arr);
			
			// refress rewrite rules for cataloggi
			if ( post_type_exists('cataloggi') ) {
			   flush_rewrite_rules( false ); // soft flush. Default is true (hard), update rewrite rules
			}
				  
			// success message
			$validation[] = __('Settings has been updated. ', 'cataloggi');
			// validation
			$this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		 $this->adminFormsValidation($validation, $type='error');  
	    }
	  }

    }

	/**
	 * Switch items view.
	 *
	 * @since      1.0.0
     * @return     string    $item_view
	 */
    public static function ctlggi_default_items_view_switch( $itemsview ) 
	{
		if ( !empty($itemsview) ) {
			$item_view = '';
			if ( $itemsview == 'Normal' ) {
				$item_view = 'cataloggi-item-box-grid columns-3';
			} elseif ( $itemsview == 'Large' ) {
				$item_view = 'cataloggi-item-box-grid columns-2';
			} elseif ( $itemsview == 'List' ) {
				$item_view = 'cataloggi-item-box-list-view';
			} else {
			   // default	
			   $item_view = 'cataloggi-item-box-grid columns-3';
			}
			return $item_view;
		}
	}
	
	/**
	 * Settings payment gateway sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_payment_gateway_settings_form_process() 
	{	
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-payment-gateway-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-payment-gateway-options-form-nonce'], 'ctlggi_payment_gateway_options_form_nonce') )
	    {
			
            $default_payment_gateway = sanitize_text_field( $_POST['ctlggi_default_payment_gateway'] );
			
			$checkout_notes_field  = isset( $_POST['checkout_notes_field'] ) ? sanitize_text_field( $_POST['checkout_notes_field'] ) : '';

			$ctlggi_payment_gateway_options = get_option('ctlggi_payment_gateway_options');
			$version = $ctlggi_payment_gateway_options['version'];
			if( trim($version) == false ) $version = '';

			$arr = array(
				'version'                 => $version,
				'default_payment_gateway' => $default_payment_gateway,
				'checkout_notes_field'    => $checkout_notes_field
			);
	
			update_option('ctlggi_payment_gateway_options', $arr);
				  
				  // success message
				  $validation[] = __('Payment Gateway settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings payment gateways bacs sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_payment_gateway_bacs_form_process() 
	{
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-bacs-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-bacs-options-form-nonce'], 'ctlggi_bacs_options_form_nonce') )
	    {
			// Checkbox
			if( isset( $_POST['ctlggi_bacs_enabled'] ) ) {
				$ctlggi_bacs_enabled = '1';
			} else {
				$ctlggi_bacs_enabled = '0';
			}
			
			// Checkbox - show billing details fields on checkout
			if( isset( $_POST['ctlggi_bacs_show_billing_details'] ) ) {
				$ctlggi_bacs_show_billing_details = '1';
			} else {
				$ctlggi_bacs_show_billing_details = '0';
			}
			
            $ctlggi_bacs_title                = sanitize_text_field( $_POST['ctlggi_bacs_title'] );
			$ctlggi_bacs_description          = wp_kses_post( $_POST['ctlggi_bacs_description'] );
			$ctlggi_bacs_notes                = wp_kses_post( $_POST['ctlggi_bacs_notes'] ); 
			$ctlggi_bacs_bank_account_details = wp_kses_post( $_POST['ctlggi_bacs_bank_account_details'] ); 

			$ctlggi_gateway_bacs_options = get_option('ctlggi_gateway_bacs_options');
			$version = $ctlggi_gateway_bacs_options['version'];
			if( trim($version) == false ) $version = '';

			$arr = array(
				'version'                          => $version,
				'ctlggi_bacs_enabled'              => $ctlggi_bacs_enabled,
				'ctlggi_bacs_show_billing_details' => $ctlggi_bacs_show_billing_details,
				'ctlggi_bacs_title'                => $ctlggi_bacs_title,
				'ctlggi_bacs_description'          => $ctlggi_bacs_description,
				'ctlggi_bacs_notes'                => $ctlggi_bacs_notes,
				'ctlggi_bacs_bank_account_details' => $ctlggi_bacs_bank_account_details
			);
	
			update_option('ctlggi_gateway_bacs_options', $arr);
				  
				  // success message
				  $validation[] = __('BACS settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Payment gateways page Paypal Standard settings form processing
	 *
	 * @uses wp_verify_nonce()
	 * @uses update_option()
	 * @uses static adminFormsValidation()
	 *
	 * @since 1.0.0
	 * @return void
	 */
    public function ctlggi_payment_gateway_paypalstandard_form_process() 
	{	
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-paypal-standard-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-paypal-standard-options-form-nonce'], 'ctlggi_paypalstandard_options_form_nonce') )
	    {
			
			// Checkbox - Shopping Cart
			if( isset( $_POST['ctlggi_paypalstandard_enabled'] ) ) {
				$ctlggi_paypalstandard_enabled = '1';
			} else {
				$ctlggi_paypalstandard_enabled = '0';
			}
			
			// Checkbox - show billing details fields on checkout
			if( isset( $_POST['ctlggi_paypalstandard_show_billing_details'] ) ) {
				$show_billing_details = '1';
			} else {
				$show_billing_details = '0';
			}
			
            $ctlggi_paypalstandard_title                 = sanitize_text_field( $_POST['ctlggi_paypalstandard_title'] );
			$ctlggi_paypalstandard_description           = sanitize_text_field( $_POST['ctlggi_paypalstandard_description'] );
			
			$ctlggi_paypalstandard_email                 = sanitize_text_field( $_POST['ctlggi_paypalstandard_email'] );
			$ctlggi_paypalstandard_page_style            = sanitize_text_field( $_POST['ctlggi_paypalstandard_page_style'] );
			
			$ctlggi_paypalstandard_live_api_username     = sanitize_text_field( $_POST['ctlggi_paypalstandard_live_api_username'] );
			$ctlggi_paypalstandard_live_api_password     = sanitize_text_field( $_POST['ctlggi_paypalstandard_live_api_password'] );
			$ctlggi_paypalstandard_live_api_signature    = sanitize_text_field( $_POST['ctlggi_paypalstandard_live_api_signature'] );
			
			$ctlggi_paypalstandard_test_api_username     = sanitize_text_field( $_POST['ctlggi_paypalstandard_test_api_username'] );
			$ctlggi_paypalstandard_test_api_password     = sanitize_text_field( $_POST['ctlggi_paypalstandard_test_api_password'] );
			$ctlggi_paypalstandard_test_api_signature    = sanitize_text_field( $_POST['ctlggi_paypalstandard_test_api_signature'] );
			
			$ctlggi_paypalstandard_account_mode          = sanitize_text_field( $_POST['ctlggi_paypalstandard_account_mode'] );
			
			if ( $ctlggi_paypalstandard_account_mode == 'test' ) {
				$accountmode = 'test';
			} else {
				$accountmode = 'live';
			}
			
			$ctlggi_paypalstandard_IPN_verification = '0'; // not ready yet
			
			$ctlggi_gateway_paypalstandard_options = get_option('ctlggi_gateway_paypalstandard_options');
			$version = $ctlggi_gateway_paypalstandard_options['version'];
			if( trim($version) == false ) $version = '';
			
			$arr = array(
				'version'                                    => $version,
				'ctlggi_paypalstandard_enabled'              => $ctlggi_paypalstandard_enabled,
				'ctlggi_paypalstandard_show_billing_details' => $show_billing_details,
				'ctlggi_paypalstandard_title'                => $ctlggi_paypalstandard_title,
				'ctlggi_paypalstandard_description'          => $ctlggi_paypalstandard_description,
				'ctlggi_paypalstandard_email'                => $ctlggi_paypalstandard_email,
				'ctlggi_paypalstandard_page_style'           => $ctlggi_paypalstandard_page_style,
				'ctlggi_paypalstandard_IPN_verification'     => $ctlggi_paypalstandard_IPN_verification,
				'ctlggi_paypalstandard_live_api_username'    => $ctlggi_paypalstandard_live_api_username,
				'ctlggi_paypalstandard_live_api_password'    => $ctlggi_paypalstandard_live_api_password,
				'ctlggi_paypalstandard_live_api_signature'   => $ctlggi_paypalstandard_live_api_signature,
				'ctlggi_paypalstandard_test_api_username'    => $ctlggi_paypalstandard_test_api_username,
				'ctlggi_paypalstandard_test_api_password'    => $ctlggi_paypalstandard_test_api_password,
				'ctlggi_paypalstandard_test_api_signature'   => $ctlggi_paypalstandard_test_api_signature,
				'ctlggi_paypalstandard_account_mode'         => $accountmode // test or live
			);
	
			update_option('ctlggi_gateway_paypalstandard_options', $arr);
				  
				  // success message
				  $validation[] = __('PayPal Standard settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings template sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_template_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-template-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-template-options-form-nonce'], 'ctlggi_template_options_form_nonce') )
	    {
			// MAKE IT SAFE
			// use addslashes before you save the option. Then when displaying the code use the stripslashes
			$inner_template_header = wp_kses_post($_POST['inner_template_header']); // "wp_kses_post" Sanitize content for allowed HTML tags for post content. 
			$inner_template_footer = wp_kses_post($_POST['inner_template_footer']); // "wp_kses_post" Sanitize content for allowed HTML tags for post content. 

			$ctlggi_template_options = get_option('ctlggi_template_options');
			$version = $ctlggi_template_options['version'];

			// PaymeButtons for Stripe options
			$arr = array(
				'version'               => $version,
				'inner_template_header' => $inner_template_header,
				'inner_template_footer' => $inner_template_footer,
			);

            update_option('ctlggi_template_options', $arr);
				  
				  // success message
				  $validation[] = __('Template settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings template system sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_template_system_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-template-system-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-template-system-options-form-nonce'], 'ctlggi_template_system_options_form_nonce') )
	    {
            $ctlggi_default_template = sanitize_text_field( $_POST['ctlggi_default_template'] );

			$ctlggi_template_system_options = get_option('ctlggi_template_system_options');
			$version = $ctlggi_template_system_options['version'];

			$arr = array(
				'version'          => $version,
				'default_template' => $ctlggi_default_template
			);

            update_option('ctlggi_template_system_options', $arr);
				  
				  // success message
				  $validation[] = __('Template system settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }

	/**
	 * Settings page tabs.
	 *
	 * @since      1.0.0
     * @return     array   $tabs
	 */
    public static function ctlggi_admin_settings_tabs() 
	{
		$tabs = array(
		'general-main'      => __( 'General', 'cataloggi' ),
		'template-main'     => __( 'Template', 'cataloggi' ),
		'cart-main'         => __( 'Cart', 'cataloggi' ),
		'gateway-main'      => __( 'Payment Gateways', 'cataloggi' ),
		'emails-main'       => __( 'Emails', 'cataloggi' ),
		//'misc-main'         => __( 'Misc', 'cataloggi' ), // removed
		);
		return apply_filters( 'ctlggi_admin_settings_tabs', $tabs ); // <- extensible
	}
	
	/**
	 * Settings webshop page sub tabs.
	 *
	 * @since      1.0.0
     * @return     array   $subs
	 */
    public static function ctlggi_admin_settings_cart_subs() 
	{
		$subs = array(
		'cart-settings' => __( 'Settings', 'cataloggi' ),
		);
		return apply_filters( 'ctlggi_admin_settings_cart_subs', $subs ); // <- extensible
	}
	
	/**
	 * Settings template page sub tabs.
	 *
	 * @since      1.0.0
     * @return     array   $subs
	 */
    public static function ctlggi_admin_settings_template_subs() 
	{
		$subs = array(
		'template-layout' => __( 'Layout', 'cataloggi' ),
		'template-system' => __( 'Template System', 'cataloggi' ),
		);
		return apply_filters( 'ctlggi_admin_settings_template_subs', $subs ); // <- extensible
	}

	/**
	 * Settings general page sub tabs.
	 *
	 * @since      1.0.0
     * @return     array   $subs
	 */
    public static function ctlggi_admin_settings_general_subs() 
	{
		$subs = array(
		'general-catalog'  => __( 'Catalog', 'cataloggi' ),
		'general-currency' => __( 'Currency', 'cataloggi' ),
		'general-settings' => __( 'Site Settings', 'cataloggi' ),
		);
		return apply_filters( 'ctlggi_admin_settings_general_subs', $subs ); // <- extensible
	}

	/**
	 * Settings email page sub tabs.
	 *
	 * @since      1.0.0
     * @return     array   $subs
	 */
    public static function ctlggi_admin_settings_emails_subs() 
	{
		$subs = array(
		'email-settings'      => __( 'Email Settings', 'cataloggi' ),
		'order-notifications' => __( 'Order Notifications', 'cataloggi' ),
		'order-receipts'      => __( 'Order Receipts', 'cataloggi' ),
		'payment-request'     => __( 'Payment Request', 'cataloggi' ),
		);
		return apply_filters( 'ctlggi_admin_settings_emails_subs', $subs ); // <- extensible
	}
	
	/**
	 * Settings misc page sub tabs.
	 *
	 * @since      1.0.0
     * @return     array   $subs
	 */
    public static function ctlggi_admin_settings_misc_subs() 
	{
		$subs = array(
		'google-analytics' => __( 'Google Analytics', 'cataloggi' ), 
		);
		return apply_filters( 'ctlggi_admin_settings_misc_subs', $subs ); // <- extensible
	}
	
	/**
	 * Settings emails page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_email_settings_options_form_process() 
	{
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-email-settings-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-email-settings-options-form-nonce'], 'ctlggi_email_settings_options_form_nonce') )
	    {
            $emails_logo           = isset( $_POST['ctlggi_emails_logo'] ) ? sanitize_text_field( $_POST['ctlggi_emails_logo'] ) : '';

			// Checkbox - Shopping Cart
			if( isset( $_POST['enable_smtp'] ) ) {
				$enable_smtp = '1';
			} else {
				$enable_smtp = '0';
			}
			
			$smtp_host             = isset( $_POST['smtp_host'] ) ? sanitize_text_field( $_POST['smtp_host'] ) : '';
			$smtp_auth             = isset( $_POST['smtp_auth'] ) ? sanitize_text_field( $_POST['smtp_auth'] ) : '';
			$smtp_username         = isset( $_POST['smtp_username'] ) ? sanitize_text_field( $_POST['smtp_username'] ) : '';
			$smtp_password         = isset( $_POST['smtp_password'] ) ? sanitize_text_field( $_POST['smtp_password'] ) : '';
			$type_of_encryption    = isset( $_POST['type_of_encryption'] ) ? sanitize_text_field( $_POST['type_of_encryption'] ) : '';
			$smtp_port             = isset( $_POST['smtp_port'] ) ? sanitize_text_field( $_POST['smtp_port'] ) : '';
			$from_email            = isset( $_POST['from_email'] ) ? sanitize_text_field( $_POST['from_email'] ) : '';
			$from_name             = isset( $_POST['from_name'] ) ? sanitize_text_field( $_POST['from_name'] ) : '';

			$ctlggi_email_settings_options = get_option('ctlggi_email_settings_options');
			$version = $ctlggi_email_settings_options['version'];
			
			$arr = array(
				'version'             => $version,
				'emails_logo'         => $emails_logo,
				'enable_smtp'         => $enable_smtp,
				'smtp_host'           => $smtp_host,
                'smtp_auth'           => $smtp_auth,
                'smtp_username'       => $smtp_username,
                'smtp_password'       => $smtp_password,
                'type_of_encryption'  => $type_of_encryption,
                'smtp_port'           => $smtp_port,
                'from_email'          => $from_email,
                'from_name'           => $from_name
			);

            update_option('ctlggi_email_settings_options', $arr);
				  
				  // success message
				  $validation[] = __('Email settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings emails order receipts sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_order_receipts_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-order-receipts-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-order-receipts-options-form-nonce'], 'ctlggi_order_receipts_options_form_nonce') )
	    {
            $from_name         = sanitize_text_field( $_POST['ctlggi_from_name'] );
			$from_email        = sanitize_text_field( $_POST['ctlggi_from_email'] );
			$subject           = sanitize_text_field( $_POST['ctlggi_subject'] );
			$email_content     = wp_kses_post( $_POST['ctlggi_email_content'] );

			$ctlggi_order_receipts_options = get_option('ctlggi_order_receipts_options');
			$version = $ctlggi_order_receipts_options['version'];

			// PaymeButtons for Stripe options
			$arr = array(
				'version'        => $version,
				'from_name'      => $from_name,
				'from_email'     => $from_email,
				'subject'        => $subject,
				'email_content'  => $email_content
			);

            update_option('ctlggi_order_receipts_options', $arr);
				  
				  // success message
				  $validation[] = __('Order receipt settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings emails payment request sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_payment_requests_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-payment-request-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-payment-request-options-form-nonce'], 'ctlggi_payment_requests_options_form_nonce') )
	    {
            $from_name         = sanitize_text_field( $_POST['ctlggi_from_name'] );
			$from_email        = sanitize_text_field( $_POST['ctlggi_from_email'] );
			$subject           = sanitize_text_field( $_POST['ctlggi_subject'] );
			$email_content     = wp_kses_post( $_POST['ctlggi_email_content'] );

			$ctlggi_payment_requests_options = get_option('ctlggi_payment_requests_options');
			$version = $ctlggi_payment_requests_options['version'];

			// PaymeButtons for Stripe options
			$arr = array(
				'version'        => $version,
				'from_name'      => $from_name,
				'from_email'     => $from_email,
				'subject'        => $subject,
				'email_content'  => $email_content
			);

            update_option('ctlggi_payment_requests_options', $arr);
				  
				  // success message
				  $validation[] = __('Payment request settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings emails order notifications sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_order_notifications_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-order-notifications-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-order-notifications-options-form-nonce'], 'ctlggi_order_notifications_options_form_nonce') )
	    {
            $notifications_enabled  = sanitize_text_field( $_POST['ctlggi_notifications_enabled'] );
			$subject                = sanitize_text_field( $_POST['ctlggi_subject'] );
			$email_content          = wp_kses_post( $_POST['ctlggi_email_content'] );
			$send_to                = sanitize_text_field( $_POST['ctlggi_send_to'] );

			$ctlggi_order_notifications_options = get_option('ctlggi_order_notifications_options');
			$version = $ctlggi_order_notifications_options['version'];

			// PaymeButtons for Stripe options
			$arr = array(
				'version'               => $version,
				'notifications_enabled' => $notifications_enabled,
				'subject'               => $subject,
				'email_content'         => $email_content,
				'send_to'               => $send_to 
			);

            update_option('ctlggi_order_notifications_options', $arr);
				  
				  // success message
				  $validation[] = __('Order notification settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }
	
	/**
	 * Settings misc google analytics sub page.
	 *
	 * @since      1.0.0
     * @return     void
	 */
    public function ctlggi_misc_google_analytics_options_form_process() 
	{
		
	  // store validation results in array
	  $validation = array();
	  if ( isset($_POST['ctlggi-misc-google-analytics-options-form-nonce']) ) {
	    // verify nonce
	    if ( wp_verify_nonce( $_POST['ctlggi-misc-google-analytics-options-form-nonce'], 'ctlggi_misc_google_analytics_options_form_nonce') )
	    {
			$google_analytics_tracking_id = sanitize_text_field($_POST['google_analytics_tracking_id']); 

			$ctlggi_google_analytics_options = get_option('ctlggi_google_analytics_options');
			$version = $ctlggi_google_analytics_options['version'];

			// PaymeButtons for Stripe options
			$arr = array(
				'version'                      => $version,
				'google_analytics_tracking_id' => $google_analytics_tracking_id,
			);

            update_option('ctlggi_google_analytics_options', $arr);
				  
				  // success message
				  $validation[] = __('Google Analytics settings has been updated. ', 'cataloggi');
				  // validation
				  $this->adminFormsValidation($validation, $type='success');
		          
        } else {
		  
		  // error message
		  $validation[] = __('Failed to save data.', 'cataloggi');
		  // validation
		  $this->adminFormsValidation($validation, $type='error');
		  
	    }
	  }

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function enqueue_styles() {
		// main style
		wp_enqueue_style( 'ctlggi-back-end', plugin_dir_url( __FILE__ ) . 'assets/css/ctlggi-back-end.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-table-responsive', plugin_dir_url( __FILE__ ) . 'assets/css/table-responsive.css', array(), $this->version, 'all' );
		
		//jQuery UI theme css file for date picker
		wp_enqueue_style('ctlggi-admin-ui-css','https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',array(),"1.9.0",false);
			
		// load only if we are at the right pages
		// check if get page
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'cataloggi-settings' ) {
			// allowed pages
			$allowedpagesArray = array(
							 'cataloggi-settings'
							);
			foreach($allowedpagesArray as $key)
			{
				if ( $_GET['page'] == $key ) {
					//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/ctlggi-admin.css', array(), $this->version, 'all' );
				}
			}	
			global $post_type;
			if( 'cataloggi' == $post_type ) { // cataloggi; cataloggi_orders
				
			}
		
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function enqueue_scripts() {
	    wp_enqueue_media();
	    // if true load at footer
		$extend_sripts = wp_enqueue_script( 'ctlggi-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/ctlggi-admin.js', array( 'jquery' ), $this->version, false );
		
		//$extend_sripts = apply_filters( 'ctlggi_extend_admin_enqueue_scripts_filter', $extend_sripts );
		
		$formloaderimg = CTLGGI_Process_Order::ctlggi_order_processing_form_loader_image(); // form loader image
		wp_localize_script( 'ctlggi-admin-js', 'ctlggi_admin_js', array( 
			'ctlggi_admin_wp_ajax_url'  => admin_url( 'admin-ajax.php' ),
			'ctlggi_form_loader_img'    => $formloaderimg,
		));
		
		//jQuery UI date picker file
		wp_enqueue_script('jquery-ui-datepicker');
	}

}

?>
