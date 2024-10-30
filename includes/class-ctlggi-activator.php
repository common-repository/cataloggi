<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cataloggi.com
 * @since      1.0.0
 *
 * @package    Cataloggi
 * @subpackage Cataloggi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cataloggi
 * @subpackage Cataloggi/includes
 * @author     Attila Abraham
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class CTLGGI_Activator {
	
	const VERSION = '1.0.0';
	
	/**
	 * Multisite manage options.
	 *
	 * @since    1.0.0
	 * @param    int    $is_networkwide
	 * @param    string $optionname
	 * @param    string $todo values: get, update, delete
	 * @param    array  $args
	 * @return   void   $result
	 */
	public static function ctlggi_manage_options($is_networkwide, $optionname, $todo, $args = '') 
	{
		if ( empty($is_networkwide) && empty($optionname) && empty($todo) )
		    return;
		
		$result = ''; // default
		
		// get option
		if ( $todo == 'get' ) {
			if ( $is_networkwide == '1' ) {
				$result = get_site_option($optionname);
			} else {
				$result = get_option($optionname);
			}
		}
		// update option
		elseif ( $todo == 'update' ) {
			if ( ! empty($args) ) {
				if ( $is_networkwide == '1' ) {
					$result = update_site_option($optionname, $args);
				} else {
					$result = update_option($optionname, $args);
				}		   
			}
		}
		// deletee option
		elseif ( $todo == 'delete' ) {
			if ( $is_networkwide == '1' ) {
				$result = delete_site_option($optionname);
			} else {
				$result = delete_option($optionname);
			}	
		}
		
		return $result;
	}

	/**
	 * Install order items db table.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_order_items_table_install() {
		
		// sql to create your table
		// NOTICE that:
		// 1. each field MUST be in separate line
		// 2. There must be two spaces between PRIMARY KEY and its name
		//    Like this: PRIMARY KEY[space][space](id)
		// otherwise dbDelta will not work
		
		global $ctlggi_order_items_db_version;
		$ctlggi_order_items_db_version = '1.0.1';
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_items'; // do not forget about tables prefix 
		
		// check if table exist
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		
		    //table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();
			$sql =
			"CREATE TABLE {$table_name} (
			order_item_id bigint(20) NOT NULL AUTO_INCREMENT,
			order_item_name longtext NOT NULL,
			payment_type varchar(255) NOT NULL,
			order_item_type varchar(255) NOT NULL,
			price_option_id bigint(20) NOT NULL,
			price_option_name varchar(255) NOT NULL,
			order_id bigint(20) NOT NULL,
			PRIMARY KEY (order_item_id)
			) $charset_collate;";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			
			update_option( 'ctlggi_order_items_db_version', $ctlggi_order_items_db_version ); // save version in options
		
		}
		
	}

	/**
	 * Install order item meta db table.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_order_itemmeta_table_install() {
		
		// sql to create your table
		// NOTICE that:
		// 1. each field MUST be in separate line
		// 2. There must be two spaces between PRIMARY KEY and its name
		//    Like this: PRIMARY KEY[space][space](id)
		// otherwise dbDelta will not work
		
		global $ctlggi_order_itemmeta_db_version;
		$ctlggi_order_itemmeta_db_version = '1.0.0';
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_itemmeta'; // do not forget about tables prefix 
		
		// check if table exist
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		
		    //table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();
			$sql =
			"CREATE TABLE {$table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			order_item_id bigint(20) NOT NULL,
			meta_key varchar(255) NULL,
			meta_value longtext NULL,
			PRIMARY KEY (meta_id)
			) $charset_collate;";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			
			update_option( 'ctlggi_order_itemmeta_db_version', $ctlggi_order_itemmeta_db_version ); // save version in options
		
		}
	}

	/**
	 * Install order downloads db table.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_order_downloads_table_install() {
		
		// sql to create your table
		// NOTICE that:
		// 1. each field MUST be in separate line
		// 2. There must be two spaces between PRIMARY KEY and its name
		//    Like this: PRIMARY KEY[space][space](id)
		// otherwise dbDelta will not work
		
		global $ctlggi_order_downloads_db_version;
		$ctlggi_order_downloads_db_version = '1.0.0';
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ctlggi_order_downloads'; // do not forget about tables prefix 
		
		// check if table exist
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		
		    //table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();
			$sql =
			"CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			order_id bigint(20) NOT NULL,
			item_id bigint(20) NOT NULL,
			user_id bigint(20) NOT NULL,
			user_email varchar(255) NOT NULL,
			order_key varchar(355) NOT NULL,
			download_limit varchar(10) NOT NULL,
			order_date datetime NOT NULL,
			download_expiry_date date NOT NULL,
			download_count bigint(20) NOT NULL,
			PRIMARY KEY (id)
			) $charset_collate;";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			
			update_option( 'ctlggi_order_downloads_db_version', $ctlggi_order_downloads_db_version ); // save version in options
		
		}
	}


	/**
	 * Install site settings.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_install_single_site() 
	{
		CTLGGI_Activator::ctlggi_save_settings_options();
		
		CTLGGI_Activator::ctlggi_general_options();
		CTLGGI_Activator::ctlggi_currency_options();
		CTLGGI_Activator::ctlggi_cart_options();
		CTLGGI_Activator::ctlggi_payment_gateway_options();
		CTLGGI_Activator::ctlggi_template_options();
		CTLGGI_Activator::ctlggi_template_system_options();
		CTLGGI_Activator::ctlggi_create_custom_upload_dir();
		
		CTLGGI_Activator::ctlggi_gateway_bacs_options();
		CTLGGI_Activator::ctlggi_gateway_paypalstandard_options();
		
		CTLGGI_Activator::ctlggi_custom_roles();
		
		CTLGGI_Activator::ctlggi_email_settings_options();
		CTLGGI_Activator::ctlggi_order_receipts_options();
		CTLGGI_Activator::ctlggi_order_notifications_options();
		CTLGGI_Activator::ctlggi_payment_requests_options();
		
		CTLGGI_Activator::ctlggi_misc_google_analytics_options();
		
		CTLGGI_Activator::ctlggi_flush_rewrite_rules();
	}
	
	/**
	 * Choose to save the site settings on plugin deactivation or uninstallation.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_save_settings_options() 
	{
		// use get_option for multisite instead of get_option
		// check if option exist
		if ( get_option('ctlggi_save_settings_options') )
			return;
		
			$args = array(
				'version'                                  => '1.0.0',
				'ctlggi_plugin_deactivation_save_settings' => '1', // should be 1
				'ctlggi_plugin_uninstall_save_settings'    => '0', // should be 0
				'ctlggi_cataloggi_cpt_rewrite_slug'        => 'cataloggi', // can be cataloggi, products, items or anything.
				'ctlggi_categories_tax_rewrite_slug'       => 'cataloggi-category', // can be cataloggi-category, categories or anything.
				'ctlggi_redirect_wp_home_to_cataloggi'     => '0',
				'ctlggi_display_grid_buttons'              => '1' // normal, large, list buttons
			);
			
			//CTLGGI_Activator::ctlggi_manage_options($is_networkwide, $optionname='ctlggi_save_settings_options', $todo='update', $args);
	        
			// use update_option for multisite instead of update_option
			update_option('ctlggi_save_settings_options', $args);

	}
	
	/**
	 * General settings option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_general_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_general_options') )
			return;
	
			$arr = array(
				'version'                      => '1.0.0',
				'enable_tangible_items'        => '0', // important!!! leave this 0 until tangible items codes created
				'default_items_view'           => 'Normal',
				'display_item_thumb_img'       => '1',
				'display_item_price'           => '1',
				'display_item_short_desc'      => '1',
				'product_view_featured_image'  => '1', // Display the product featured image on the product view page
				'number_of_items_per_page'     => '6',
				'items_order_by'               => 'ID', // ID, date, title...
				'items_order'                  => 'DESC', // ASC, DESC
				'display_cat_boxes'            => '0', // category boxes
				'display_category_boxes'       => '0', // Display Main Categories on the catalog homepage and sub category boxes on the catalog pages
				'categories_drop_down_nav'     => '1', // Display the categories drop down navigation top of the products list
				'category_order_by'            => 'ID', // ID, date, title...
				'category_order'               => 'ASC', // ASC, DESC
				'parent_menu_order_by'         => 'ID', // ID, date, title...
				'parent_menu_order'            => 'DESC', // ASC, DESC
				'sub_menu_order_by'            => 'ID', // ID, date, title...
				'sub_menu_order'               => 'ASC', // ASC, DESC
				'save_options_settings'        => '1'
			);
	
			update_option('ctlggi_general_options', $arr);

	}

	/**
	 * General settings currency option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_currency_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_currency_options') )
			return;
	
			$arr = array(
				'version'               => '1.0.0',
				'catalog_currency'      => 'usd',
				'catalog_currency_name' => 'United States Dollar',
				'currency_position'     => 'Left',
				'thousand_separator'    => ',',
				'decimal_separator'     => '.',
				'number_of_decimals'    => '2'
			);
	
			update_option('ctlggi_currency_options', $arr);

	}

	/**
	 * General settings cart option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_cart_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_cart_options') )
			return;
	
			$arr = array(
				'version'                => '1.0.0',
				'enable_shopping_cart'   => '1',
				'display_payment_button' => '1', // Display Payment Buttons on the product listing pages
				'cart_page'              => '0',
				'terms_page'             => '0',
				'checkout_page'          => '0',
				'payments_page'          => '0', // new, payment requests payment page
				'success_page'           => '0',
				'order_history_page'     => '0',
				'login_redirect_page'    => '0'
			);
	
			update_option('ctlggi_cart_options', $arr);

	}

	/**
	 * Payment Gateway settings option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_payment_gateway_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_payment_gateway_options') )
			return;
			
			$default_payment_gateway = 'paypalstandard';
			
			$arr = array(
				'version'                 => '1.0.0',
				'default_payment_gateway' => $default_payment_gateway,
				'checkout_notes_field'    => '1'
			);
	
			update_option('ctlggi_payment_gateway_options', $arr);

	}
	
	/**
	 * Payment gateway: paypal standard options.
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_gateway_paypalstandard_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_gateway_paypalstandard_options') )
			return;
			
			//$args = apply_filters( 'ctlggi_gateway_stripe_options_filters', array( // <- extensible 
			$args = array(
				'version'                                    => '1.0.0',
				'ctlggi_paypalstandard_enabled'              => '1',
				'ctlggi_paypalstandard_show_billing_details' => '1',
				'ctlggi_paypalstandard_title'                => 'PayPal',
				'ctlggi_paypalstandard_description'          => __('Secure payments via PayPal.', 'cataloggi'),
				'ctlggi_paypalstandard_email'                => '',
				'ctlggi_paypalstandard_page_style'           => '',
				'ctlggi_paypalstandard_IPN_verification'     => '',
				'ctlggi_paypalstandard_live_api_username'    => '',
				'ctlggi_paypalstandard_live_api_password'    => '',
				'ctlggi_paypalstandard_live_api_signature'   => '',
				'ctlggi_paypalstandard_test_api_username'    => '',
				'ctlggi_paypalstandard_test_api_password'    => '',
				'ctlggi_paypalstandard_test_api_signature'   => '',
				'ctlggi_paypalstandard_account_mode'         => 'test' // test or live
			 );
	
			update_option('ctlggi_gateway_paypalstandard_options', $args);

	}
	
	/**
	 * Payment Gateway bacs option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_gateway_bacs_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_gateway_bacs_options') )
			return;
			
		$ctlggi_bacs_description = "Complete your payment directly into our bank account.";
			
		$ctlggi_bacs_notes = "Complete your payment directly into our bank account. Don't forget to include your Order ID as the payment reference. Your order will not begin processing until the funds have cleared in our account.";
		
			$n_br = "\n"; // \n or </br>
			$po   = ""; // <p>
			$pc   = ""; // </p>
			
			// the email template content
			$bank_account_details = $n_br;
			$bank_account_details .= $po . "<strong>Account Holder: </strong> Your Name " . $pc . $n_br;
			$bank_account_details .= $po . "<strong>Bank Name: </strong> Your Bank " . $pc . $n_br;
			$bank_account_details .= $po . "<strong>Bank Account Number: </strong> 0000 0000 0000 0000 " . $pc . $n_br;
			$bank_account_details .= $po . "<strong>Sort Code: </strong> 0000 0000 " . $pc . $n_br . $n_br;
			
			$bank_account_details .= $po . "<strong>International Bank Account Number (IBAN): </strong>  " . $pc . $n_br;
			$bank_account_details .= $po . "<strong>BIC / Swift: </strong>  " . $pc . $n_br;
			
			$arr = apply_filters( 'ctlggi_gateway_bacs_options_filters', array( // <- extensible
				'version'                          => '1.0.0',
				'ctlggi_bacs_enabled'              => '1',
				'ctlggi_bacs_show_billing_details' => '1',
				'ctlggi_bacs_title'                => 'Direct Bank Transfer',
				'ctlggi_bacs_description'          => $ctlggi_bacs_description,
				'ctlggi_bacs_notes'                => $ctlggi_bacs_notes,
				'ctlggi_bacs_bank_account_details' => $bank_account_details
			) );
	
			update_option('ctlggi_gateway_bacs_options', $arr);

	}

	/**
	 * Template settings option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_template_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_template_options') )
			return;
			
			$inner_header = '<div class="container" style="margin-left: auto; margin-right: auto;">';
			$inner_footer = '</div>';
			
			$arr = array(
				'version'               => '1.0.0',
				'inner_template_header' => $inner_header,
				'inner_template_footer' => $inner_footer
			);
	
			update_option('ctlggi_template_options', $arr);

	}
	
	/**
	 * Template system option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_template_system_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_template_system_options') )
			return;
			
			$arr = array(
				'version'          => '1.0.0',
				'default_template' => 'default'
			);
	
			update_option('ctlggi_template_system_options', $arr);

	}

	/**
	 * Email settings option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_email_settings_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_email_settings_options') )
			return;
			
			// get site title
			if ( !empty( get_bloginfo('name') ) ) {
				$blog_title = get_bloginfo('name');
			} else {
				$blog_title = 'WordPress';
			}
			
			if ( !empty( get_bloginfo('admin_email') ) ) {
				$admin_email = get_bloginfo('admin_email');
			} else {
				$admin_email = '';
			}
			
			$arr = array(
				'version'             => '1.0.0',
				'emails_logo'         => '',
				'enable_smtp'         => '',
				'smtp_host'           => '',
                'smtp_auth'           => '',
                'smtp_username'       => '',
                'smtp_password'       => '',
                'type_of_encryption'  => '',
                'smtp_port'           => '',
                'from_email'          => $admin_email,
                'from_name'           => $blog_title
			);

            update_option('ctlggi_email_settings_options', $arr);

	}

	/**
	 * Email settings order receipt option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_order_receipts_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_order_receipts_options') )
			return;
			
			// defaults
			$displayname = '';
			$email       = '';
			$admin_email = '';
			
			// check if user logged in
			if ( is_user_logged_in() ) {
			  // if logged in get current user data
			  $current_user = wp_get_current_user();
			  
			  $username    = $current_user->user_login;
			  $email       = $current_user->user_email;
			  $first_name  = $current_user->user_firstname;
			  $last_name   = $current_user->user_lastname;
			  $displayname = $current_user->display_name;
			
			}
			
			// get site title
			if ( !empty( get_bloginfo('name') ) ) {
				$blog_title = get_bloginfo('name');
			} else {
				$blog_title = 'WordPress';
			}
			
			if ( !empty( get_bloginfo('admin_email') ) ) {
				$admin_email = get_bloginfo('admin_email');
			} else {
				$admin_email = '';
			}
			
			$n_br = "\n"; // \n or </br>
			$po   = ""; // <p>
			$pc   = ""; // </p>
			
			// the email template content
			$email_content = $n_br;
			$email_content .= "<h3>Dear [user_first_name], thank you for your purchase!</h3> ";
			
			$email_content .= $po . "<strong>Order Date:</strong> [order_date] " . $pc . $n_br;
			$email_content .= $po . "<strong>Order ID:</strong> #[order_id] " . $pc . $n_br;
			$email_content .= $po . "<strong>Transaction ID:</strong> [transaction_id] " . $pc . $n_br;
			$email_content .= $po . "<strong>Order Key:</strong> [order_key] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Billing Details:</strong>" . $pc . $n_br . $n_br;
			$email_content .= $po . "[billing_addr_1] [billing_addr_2]" . $pc . $n_br;
			$email_content .= $po . "[billing_city]" . $pc . $n_br;
			$email_content .= $po . "[billing_state]" . $pc . $n_br;
			$email_content .= $po . "[billing_country]" . $pc . $n_br;
			$email_content .= $po . "[billing_zip]" . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Product(s):</strong> " . $pc . $n_br . $n_br;
			$email_content .= "[items] " . $n_br;
			
			$email_content .= $po . "<strong>Total:</strong> [order_total] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Payment Status:</strong> [order_status] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "[payment_gateway_data] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "With Kind Regards, [from_name] " . $pc . $n_br;
			$email_content .= $po . "[current_site_url] " . $pc . $n_br;
			
			$subject = __( 'Order Receipt', 'cataloggi' );
			
			$arr = array(
				'version'        => '1.0.0',
				'from_name'      => $blog_title,
				'from_email'     => $admin_email,
				'subject'        => $subject,
				'email_content'  => $email_content
			);
	
			update_option('ctlggi_order_receipts_options', $arr);

	}
	
	/**
	 * Email settings payment request option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_payment_requests_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_payment_requests_options') )
			return;
			
			// defaults
			$displayname = '';
			$email       = '';
			$admin_email = '';
			
			// check if user logged in
			if ( is_user_logged_in() ) {
			  // if logged in get current user data
			  $current_user = wp_get_current_user();
			  
			  $username    = $current_user->user_login;
			  $email       = $current_user->user_email;
			  $first_name  = $current_user->user_firstname;
			  $last_name   = $current_user->user_lastname;
			  $displayname = $current_user->display_name;
			
			}
			
			// get site title
			if ( !empty( get_bloginfo('name') ) ) {
				$blog_title = get_bloginfo('name');
			} else {
				$blog_title = 'WordPress';
			}
			
			$admin_email = get_bloginfo('admin_email');
			
			$n_br = "\n"; // \n or </br>
			$po   = ""; // <p>
			$pc   = ""; // </p>
			
			// the email template content
			$email_content = $n_br;
			$email_content .= "<h3>Dear [user_first_name],</h3> ";
			
			$email_content .= $po . "<strong>This is a payment request for Invoice #[order_id] created on [order_date].</strong>" . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Invoice ID:</strong> #[order_id] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Product(s):</strong> " . $pc . $n_br . $n_br;
			
			$email_content .= "[items]" . $pc . $n_br;
			
			$email_content .= $po . "<strong>Total:</strong> [order_total] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Payment Status:</strong> [order_status] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Please click on the following button to complete your payment.</strong>" . $pc . $n_br . $n_br;
			
			$email_content .= $po . " [payment_request_button] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "If the payment button isn't clickable, please copy and paste the following URL into your browser's address bar and press Enter." . $pc . $n_br . $n_br;
			
			$email_content .= $po . " [payment_request_link] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "Thank you!" . $pc . $n_br . $n_br;
			
			$email_content .= $po . "With Kind Regards, [from_name] " . $pc . $n_br;
			$email_content .= $po . "[current_site_url] " . $pc . $n_br;
			
			$subject = __( 'Payment Request', 'cataloggi' );
			
			$arr = array(
				'version'        => '1.0.0',
				'from_name'      => $blog_title,
				'from_email'     => $admin_email,
				'subject'        => $subject,
				'email_content'  => $email_content
			);
	
			update_option('ctlggi_payment_requests_options', $arr);

	}

	/**
	 * Email settings order notifications option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_order_notifications_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_order_notifications_options') )
			return;
			
			// defaults
			$displayname = '';
			$email       = '';
			$admin_email = '';
			
			// check if user logged in
			if ( is_user_logged_in() ) {
			  // if logged in get current user data
			  $current_user = wp_get_current_user();
			  
			  $username    = $current_user->user_login;
			  $email       = $current_user->user_email;
			  $first_name  = $current_user->user_firstname;
			  $last_name   = $current_user->user_lastname;
			  $displayname = $current_user->display_name;
			
			}
			
			$admin_email = get_bloginfo('admin_email');
			
			$n_br = "\n"; // \n or </br>
			$po   = ""; // <p>
			$pc   = ""; // </p>
			
			// the email template content
			$email_content = $n_br;
			$email_content .= "<h3>New Order Received!</h3> ";
			
			$email_content .= $po . "<strong>First Name:</strong> [user_first_name] " . $pc . $n_br;
			$email_content .= $po . "<strong>Last Name:</strong> [user_last_name] " . $pc . $n_br;
			$email_content .= $po . "<strong>Email:</strong> [user_email] " . $pc . $n_br;
			$email_content .= $po . "<strong>Phone:</strong> [user_phone] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Order Date:</strong> [order_date] " . $pc . $n_br;
			$email_content .= $po . "<strong>Order ID:</strong> #[order_id] " . $pc . $n_br;
			$email_content .= $po . "<strong>Transaction ID:</strong> [transaction_id] " . $pc . $n_br;
			$email_content .= $po . "<strong>Order Key:</strong> [order_key] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Product(s):</strong> " . $pc . $n_br . $n_br;
			$email_content .= "[items] " . $n_br;
			
			$email_content .= $po . "<strong>Total:</strong> [order_total] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Payment Status:</strong> [order_status] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Payment Gateway:</strong> [payment_gateway] " . $pc . $n_br . $n_br;
			
			$email_content .= $po . "<strong>Customer Notes:</strong> " . $pc . $n_br . $n_br;
			$email_content .= "[customer_extra_notes] " . $n_br;
			
			$email_content .= $po . "Have a great day! " . $pc . $n_br;
			$email_content .= $po . "[current_site_url] " . $pc . $n_br;
			
			$subject = __( 'Order Notification', 'cataloggi' );
			
			$arr = array(
				'version'                  => '1.0.0',
				'notifications_enabled'    => '1',
				'send_to'                  => $admin_email, 
				'subject'                  => $subject,
				'email_content'            => $email_content
			);

            update_option('ctlggi_order_notifications_options', $arr);

	}
	
	/**
	 * Misc google analytics settings option
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_misc_google_analytics_options() 
	{
		// check if option exist
		if ( get_option('ctlggi_google_analytics_options') )
			return;
			
			$arr = array(
				'version'                      => '1.0.0',
				'google_analytics_tracking_id' => ''
			);
	
			update_option('ctlggi_google_analytics_options', $arr);

	}

	/**
	 * Create custom folder for uploads and creates index and htaccess files
	 *
	 * @since    1.0.0
	 */
    public static function ctlggi_create_custom_upload_dir()
    {
		
		$upload_dir = wp_upload_dir(); // wp upload dir ARRAY
		//print_r( $upload_dir );
		$upload_dir_path = $upload_dir['path']; 
		// custom folder for uploads
		$customfolder = 'cataloggi-uploads'; // <- check if folder exist, if not create
		// custom media folder dir path
		$uploadpath = $upload_dir_path . '/' . $customfolder;
		
		// create sub dir
		wp_mkdir_p( $uploadpath );
		
		if ( wp_mkdir_p( $uploadpath ) === TRUE )
		{
			//echo "Folder $customfolder successfully created";
			
			// create index.php
			if ( ! file_exists( $uploadpath . '/index.php' ) && wp_is_writable( $uploadpath ) ) {
				file_put_contents( $uploadpath . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
			}
			
			$htrules = CTLGGI_Activator::ctlggi_upload_dir_htaccess();
			// create .htaccess
			if ( ! file_exists( $uploadpath . '/.htaccess' ) && wp_is_writable( $uploadpath ) ) {
				file_put_contents( $uploadpath . '/.htaccess', $htrules );
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Create htaccess file content
	 *
	 * @since    1.0.0
	 */
    public static function ctlggi_upload_dir_htaccess()
    {
			// Prevent directory browsing and direct access to all files, except images (they must be allowed for featured images / thumbnails)
			$allowed_filetypes = apply_filters( 'ctlggi_upload_dir_allowed_filetypes', array( 'jpg', 'jpeg', 'png', 'gif', 'mp3', 'ogg', 'zip', 'rar' ) );
			$htrules = "Options -Indexes\n";
			$htrules .= "deny from all\n";
			$htrules .= "<FilesMatch '\.(" . implode( '|', $allowed_filetypes ) . ")$'>\n";
			$htrules .= "Order Allow,Deny\n";
			$htrules .= "Allow from all\n";
			$htrules .= "</FilesMatch>\n";
			
			$htrules = apply_filters( 'ctlggi_upload_dir_htaccess_rules', $htrules );
			return $htrules;
			
	}

	/**
	 * Create custom roles
	 *
	 * @since    1.0.0
	 */
    public static function ctlggi_custom_roles()
    {
		 add_role('cataloggi_subscriber',
					'Cataloggi Subscriber',
					array(
						'read' => true,
						'read_item' => true,
						'read_order' => true,
						//'edit_posts' => false,
						//'delete_posts' => false,
						//'publish_posts' => false,
						//'upload_files' => false,
					)
		         );
		 
		 add_role('cataloggi_customer',
					'Cataloggi Customer',
					array(
						'read' => true,
						'read_item' => true,
						'read_order' => true,
						//'edit_posts' => false,
						//'delete_posts' => false,
						//'publish_posts' => false,
						//'upload_files' => false,
					)
		         );
		 
		 
	}
	
	/**
	 * This is how you would flush rewrite rules when a plugin is activated
	 *
	 * @since    1.0.0
	 */
	public static function ctlggi_flush_rewrite_rules() {
	   // check if custom post types exist
       //if ( post_type_exists('cataloggi') && post_type_exists('cataloggi_orders') ) {
	   if ( post_type_exists('cataloggi') ) {
		   flush_rewrite_rules( false ); // soft flush. Default is true (hard), update rewrite rules
	   }
	}

}

?>
