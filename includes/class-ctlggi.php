<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current 
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
final class Cataloggi {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'cataloggi'; //plugin-name
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
		$this->load_shortcodes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ctlggi-loader.php';
		$this->loader = new CTLGGI_Loader();
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ctlggi-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ctlggi-public.php'; 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ctlggi-i18n.php';

		// custom post types
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-custom-post-types.php';
		// custom post types Capabilities
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-cpts-capabilities.php';
		// taxonomies
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-taxonomies.php';
		
		// Developer Mode
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-developer-mode.php';

		// WP Cron
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-wp-cron.php';
		
		// Cookies
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-cookies.php';
		
		// shopping cart - File Download API
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/api/class-ctlggi-file-download-api.php';
		
		// Orders Api
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/api/ctlggi-orders-api.php';
		
		// Manage Options
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-manage-options.php';
		
		// Manage Custom Tables
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-manage-custom-tables.php';
		
		// Custom Data Tables : ctlggi_order_items and ctlggi_order_itemmeta
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/custom-db-tables/ctlggi-db-tables-items.php';	
		// Custom Data Table : ctlggi_order_downloads
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/custom-db-tables/ctlggi-db-table-downloads.php';	
		
		// shopping cart - Single Order Data
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-single-order-data.php';
		
		// shopping cart - error log
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-error-log.php';
		// shopping cart - form validation
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-validate.php';
		// shopping cart - Validate Order Form
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-validate-order-form.php';

		// Admin Multisite
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-multisite.php';
		// Admin Notices
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-admin-notices.php';
		
		// admin order items data table
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-admin-order-items-data-table.php';
		// items metaboxes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-items-metaboxes.php';
		// orders metaboxes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-orders-metaboxes.php';

		// Custom Post Statuses
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-custom-post-statuses.php';
		
		// Export Table to CSV = to-do not finish
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-export-table-to-csv.php';
		
		// Downloads List Table
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-downloads-list-table.php';
		
		// Admin Manage Downloads
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/classes/class-manage-downloads.php';
		
		// Helper
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-helper.php';

		// emailer
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-emailer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-emailer-smtp.php';

		// template system
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-template-system.php';
		
		// shortcodes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-shortcodes.php';
		
		// login register
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-login-register.php';
		
		// contact
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-contact-us.php';
		
		// shopping cart - payment buttons
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-payment-buttons.php';
		
		// shopping cart - express checkout button 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-express-checkout-button.php';
		
		// shopping cart - cart
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-cart.php';
		
		// shopping cart - checkout
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-checkout.php';
		
		// shopping cart - payments
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-payments.php';
		
		// shopping cart - amount 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-amount.php';
		
		// shopping cart - countries
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/countries/class-ctlggi-countries.php';
		
		// shopping cart - payment gateways
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-payment-gateways.php';
		
		// shopping cart - process order
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-process-order.php';
		
		// Payment Gateway BACS - Process Payment
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/payment-gateways/bacs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/payment-gateways/none.php';
		
		// Payment Gateway PayPal Standard - Process Payment
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/payment-gateways/paypal/paypalstandard.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/payment-gateways/paypal/paypal-standard-buy-now.php';
		
		// shopping cart - Downloadable Products
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-downloadable-products.php';
		
		// shopping cart - Send Notification Emails
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shopping-cart/classes/class-notification-emails.php';
		
		// Basket Widget
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widgets/basket.php';
		// Search Widget
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widgets/search.php';
		// Categories Nav Widget
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widgets/categories-nav.php';
		// Product Docs and Demo widget
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widgets/docs-and-demo.php';
		
		// WP Rest API
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-wp-rest-api.php';
		// WP Rest API Support
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-wp-rest-api-support.php';
		// WP Rest API Endpoints
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-wp-rest-api-endpoints.php';
		
		// User Account
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/class-user-account.php';

	}
	
	private function load_shortcodes() 
	{
		$plugin_shortcodes = new CTLGGI_shortcodes( $this->get_plugin_name(), $this->get_version() );
		
		// pages
		add_shortcode('cataloggi_home_page', array( $plugin_shortcodes, 'ctlggi_home_page') );
		add_shortcode('cataloggi_products_list', array( $plugin_shortcodes, 'ctlggi_products_list') );
		add_shortcode('cataloggi_single_product_view', array( $plugin_shortcodes, 'ctlggi_single_product_view') );
		add_shortcode('cataloggi_search_results', array( $plugin_shortcodes, 'ctlggi_search_results_page') );
		
		// templates
		add_shortcode('ctlggi_inner_template_header', array( $plugin_shortcodes, 'ctlggi_inner_template_header') );
		add_shortcode('ctlggi_inner_template_footer', array( $plugin_shortcodes, 'ctlggi_inner_template_footer') );
		
		// products
		add_shortcode('ctlggi_products', array( $plugin_shortcodes, 'ctlggi_shortcode_products') ); // products
		
		// home page
		add_shortcode('ctlggi_home_products', array( $plugin_shortcodes, 'ctlggi_shortcode_home_products') ); // home page - products
		add_shortcode('ctlggi_home_category_boxes', array( $plugin_shortcodes, 'ctlggi_shortcode_category_boxes') ); // home page - category boxes
		
		add_shortcode('ctlggi_cat_boxes', array( $plugin_shortcodes, 'ctlggi_shortcode_cat_boxes') ); // cat boxes
		
		// sidebar - basket
		add_shortcode('ctlggi_sidebar_basket', array( $plugin_shortcodes, 'ctlggi_shortcode_sidebar_basket') ); // sidebar - basket
		// sidebar - search
		add_shortcode('ctlggi_sidebar_search', array( $plugin_shortcodes, 'ctlggi_shortcode_sidebar_search') ); // sidebar - search
		// sidebar - nav
		add_shortcode('ctlggi_sidebar_nav', array( $plugin_shortcodes, 'ctlggi_shortcode_sidebar_nav') ); // sidebar - nav
		
		// grid or list view selector
		add_shortcode('ctlggi_grid_or_list_view', array( $plugin_shortcodes, 'ctlggi_shortcode_grid_or_list_view') ); // grid or list view selector
		// categories drop down nav
		add_shortcode('ctlggi_categories_drop_down_nav', array( $plugin_shortcodes, 'shortcode_categories_drop_down_nav') ); 
		// product list - sub category boxes
        add_shortcode('ctlggi_sub_category_boxes', array( $plugin_shortcodes, 'ctlggi_shortcode_sub_category_boxes') ); // sub category boxes
		// breadcrumbs
        add_shortcode('ctlggi_breadcrumbs', array( $plugin_shortcodes, 'ctlggi_shortcode_breadcrumbs') ); // breadcrumbs
		// search results
        add_shortcode('ctlggi_search_results', array( $plugin_shortcodes, 'ctlggi_shortcode_search_results') ); // search results
		// single product view
        add_shortcode('ctlggi_product_view', array( $plugin_shortcodes, 'ctlggi_shortcode_product_view') ); // single product view
		
		// catalog - search
        add_shortcode('ctlggi_search', array( $plugin_shortcodes, 'ctlggi_shortcode_search') ); // catalog - search
		// catalog - categories nav
        add_shortcode('ctlggi_categories_nav', array( $plugin_shortcodes, 'ctlggi_shortcode_categories_nav') ); // catalog - categories nav
		// catalog - login form
        add_shortcode('ctlggi_login_form', array( $plugin_shortcodes, 'ctlggi_shortcode_login_form') ); // catalog - login form
		// catalog - register form
        add_shortcode('ctlggi_register_form', array( $plugin_shortcodes, 'ctlggi_shortcode_register_form') ); // catalog - register form
		// catalog - forgot password form
        add_shortcode('ctlggi_forgot_pw_form', array( $plugin_shortcodes, 'ctlggi_shortcode_forgot_pw_form') ); // catalog - forgot password form
		// catalog - account
        add_shortcode('ctlggi_account', array( $plugin_shortcodes, 'ctlggi_shortcode_account') ); // catalog - account
		// catalog - login, register and forgot password forms
        add_shortcode('ctlggi_login_manager', array( $plugin_shortcodes, 'ctlggi_shortcode_login_manager') ); // catalog - login manager
		// catalog - contact form
        add_shortcode('ctlggi_contact_form', array( $plugin_shortcodes, 'ctlggi_shortcode_contact_form') ); // catalog - contact form
		// catalog - order history
        add_shortcode('ctlggi_order_history', array( $plugin_shortcodes, 'ctlggi_shortcode_order_history') ); // catalog - order history
		// catalog - payment buttons, add to cart, buy now etc.
        add_shortcode('ctlggi_payment_button', array( $plugin_shortcodes, 'ctlggi_shortcode_payment_buttons') ); // catalog - payment buttons
		// catalog - order receipt
        add_shortcode('ctlggi_order_receipt', array( $plugin_shortcodes, 'ctlggi_shortcode_order_receipt') ); // catalog - order receipt
		
		// product docs and demo
        add_shortcode('ctlggi_docs_and_demo', array( $plugin_shortcodes, 'docs_and_demo') ); // product docs and demo
		
		// shopping cart - cart
        add_shortcode('ctlggi_cart', array( $plugin_shortcodes, 'ctlggi_shortcode_cart') ); // shopping cart - cart
		// shopping cart - cart totals
        add_shortcode('ctlggi_cart_totals', array( $plugin_shortcodes, 'ctlggi_shortcode_cart_totals') ); // shopping cart - cart totals
		// shopping cart - checkout
        add_shortcode('ctlggi_checkout', array( $plugin_shortcodes, 'ctlggi_shortcode_checkout') ); // shopping cart - checkout
		// shopping cart - checkout totals
        add_shortcode('ctlggi_checkout_totals', array( $plugin_shortcodes, 'ctlggi_shortcode_checkout_totals') ); // shopping cart - checkout totals
		// shopping cart - payments
        add_shortcode('ctlggi_payments', array( $plugin_shortcodes, 'ctlggi_shortcode_payments') ); // shopping cart - payments
		// shopping cart - payments totals
        add_shortcode('ctlggi_payments_totals', array( $plugin_shortcodes, 'ctlggi_shortcode_payments_totals') ); // shopping cart - payments totals
		// shopping cart - basket
        add_shortcode('ctlggi_basket', array( $plugin_shortcodes, 'ctlggi_shortcode_basket') ); // shopping cart - basket
		
		add_shortcode('ctlggi_list_products_by_category_slug', array( $plugin_shortcodes, 'list_products_by_category_slug') ); 
		
		// themes shorten shortcodes
        add_shortcode('ctlggi_themes_archive_page', array( $plugin_shortcodes, 'themes_shortcode_archive_page') ); // themes archive page
		add_shortcode('ctlggi_themes_search_page', array( $plugin_shortcodes, 'themes_shortcode_search_page') ); // themes search page
		add_shortcode('ctlggi_themes_categories_taxonomy_page', array( $plugin_shortcodes, 'themes_shortcode_categories_taxonomy_page') ); // themes categories page
		
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new CTLGGI_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
        //$this->loader->add_action( 'init', $plugin_i18n, 'load_i18n_debug' );
		//$this->loader->add_action( 'load_textdomain', $plugin_i18n, 'debug_load_textdomain', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin            = new CTLGGI_Admin( $this->get_plugin_name(), $this->get_version() );
		$customposttypes         = new CTLGGI_Custom_Post_Types( $this->get_plugin_name(), $this->get_version() );
		$cpts_caps               = new CTLGGI_CPTS_Capabilities( $this->get_plugin_name(), $this->get_version() );
		$taxonomies              = new CTLGGI_Taxonomies( $this->get_plugin_name(), $this->get_version() );
		$order_items_data_table  = new CTLGGI_ADMIN_Order_Items_Data_Table( $this->get_plugin_name(), $this->get_version() );
		$items_metaboxes         = new CTLGGI_Items_Metaboxes( $this->get_plugin_name(), $this->get_version() );
		$ordersmetaboxes         = new CTLGGI_Orders_Metaboxes( $this->get_plugin_name(), $this->get_version() );
		$custompoststatuses      = new CTLGGI_Custom_Post_Statuses( $this->get_plugin_name(), $this->get_version() );
		$admin_notices           = new CTLGGI_Admin_Notices( $this->get_plugin_name(), $this->get_version() );
		$manage_downloads        = new CTLGGI_Admin_Manage_Downloads( $this->get_plugin_name(), $this->get_version() );
		$multisite               = new CTLGGI_Multisite( $this->get_plugin_name(), $this->get_version() );
		$manage_options          = new CTLGGI_Manage_Options( $this->get_plugin_name(), $this->get_version() );
		$manage_custom_tables    = new CTLGGI_Manage_Custom_Tables( $this->get_plugin_name(), $this->get_version() );
		
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// register register_post_type
		$this->loader->add_action( 'init', $customposttypes, 'ctlggi_custom_post_types' );
		$this->loader->add_filter( 'manage_edit-cataloggi_columns', $customposttypes, 'ctlggi_cataloggi_items_columns' ); // use: manage_edit-{$post_type}_columns
		$this->loader->add_action( 'manage_cataloggi_posts_custom_column', $customposttypes, 'ctlggi_cataloggi_render_items_columns', 10, 2 ); //  use: manage_{$post_type}_posts_custom_column
		$this->loader->add_filter( 'manage_edit-cataloggi_orders_columns', $customposttypes, 'ctlggi_cataloggi_orders_columns' ); // use: manage_edit-{$post_type}_columns
		$this->loader->add_action( 'manage_cataloggi_orders_posts_custom_column', $customposttypes, 'ctlggi_cataloggi_shop_render_orders_columns', 10, 2 ); //  use: manage_{$post_type}_posts_custom_column
		$this->loader->add_filter( 'post_row_actions', $customposttypes, 'ctlggi_orders_remove_row_actions', 10, 2 ); // post_row_actions , remove Quick Edit, View etc.
		
		// manage options
		$this->loader->add_action( 'admin_init', $manage_options, 'ctlggi_create_option_if_not_exist' ); 
		$this->loader->add_action( 'admin_init', $manage_options, 'ctlggi_add_version_to_option_if_not_exist' ); 
		
		// manage order items table
		$this->loader->add_action( 'plugins_loaded', $manage_custom_tables, 'ctlggi_manage_order_items_table' ); 
		
		$this->loader->add_action( 'wpmu_new_blog', $multisite, 'ctlggi_multisite_new_site_activation', 10, 6 ); 
		$this->loader->add_action( 'delete_blog', $multisite, 'ctlggi_multisite_site_deletion', 10, 2 ); 
		$this->loader->add_filter( 'wpmu_drop_tables', $multisite, 'ctlggi_delete_tables_on_site_deletion', 10, 1 );
		
		// custom upload folder
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ctlggi_load_custom_upload_filter' ); 
		//$this->loader->add_filter( 'upload_dir', $plugin_admin, 'ctlggi_custom_prefix_upload_dir' ); right now not in use
		
		$this->loader->add_filter( 'upload_mimes', $plugin_admin, 'ctlggi_add_mimes_multisite' ); // mimes
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'ctlggi_output_admin_notices' ); // admin notices
		
		// capabilities
		$this->loader->add_action( 'init', $cpts_caps, 'ctlggi_manage_items_capabilities' ); // items, use: init
		$this->loader->add_action( 'init', $cpts_caps, 'ctlggi_manage_orders_capabilities' ); // orders, use: init 
		$this->loader->add_action( 'init', $cpts_caps, 'ctlggi_manage_cataloggicat_taxonomy_capabilities' );
		
		// register register_taxonomy
		$this->loader->add_action( 'init', $taxonomies, 'ctlggi_cataloggicat_taxonomy' ); // should use init	
		
		// custom post type: 'cataloggi_orders', custom messages for updates
		$this->loader->add_filter( 'post_updated_messages', $customposttypes, 'ctlggi_cataloggi_orders_update_messages' ); 

		// Adding Dashboard Menu - settings
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ctlggi_add_admin_menu' );
		
		// Source: https://pippinsplugins.com/adding-custom-meta-fields-to-taxonomies/
		// The first parameter is what determines the taxonomy that this field gets added to. 
		// It uses this format: {$taxonomy_name}_add_form_fields.
        $this->loader->add_action( 'cataloggicat_add_form_fields', $items_metaboxes, 'ctlggi_cataloggicat_tax_add_new_meta_field', 10, 2 );	
		// It uses this format: {$taxonomy_name}_edit_form_fields.
		$this->loader->add_action( 'cataloggicat_edit_form_fields', $items_metaboxes, 'ctlggi_cataloggicat_tax_edit_meta_field', 10, 2 );
		// It uses this format: edited_{$taxonomy_name}.
		$this->loader->add_action( 'edited_cataloggicat', $items_metaboxes, 'ctlggi_cataloggicat_save_tax_custom_meta', 10, 2 );
		// It uses this format: create_{$taxonomy_name}.
		$this->loader->add_action( 'create_cataloggicat', $items_metaboxes, 'ctlggi_cataloggicat_save_tax_custom_meta', 10, 2 );
		
		// custom image sizes
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_custom_image_sizes' ); // use init or after_setup_theme
		$this->loader->add_filter( 'image_size_names_choose', $plugin_admin, 'ctlggi_show_image_sizes' ); // use: image_size_names_choose

		// Meta Boxes payment button
		$this->loader->add_action( 'add_meta_boxes', $items_metaboxes, 'ctlggi_add_metabox_payment_button' );	
		$this->loader->add_action( 'save_post', $items_metaboxes, 'ctlggi_save_metabox_payment_button', 10, 2 );
		// Meta Boxes
		$this->loader->add_action( 'add_meta_boxes', $items_metaboxes, 'ctlggi_add_metabox_item_data' );	// meta box item data
		$this->loader->add_action( 'save_post', $items_metaboxes, 'ctlggi_save_metabox_item_data', 10, 2 );	// save meta box item data
		$this->loader->add_action( 'add_meta_boxes', $items_metaboxes, 'ctlggi_add_metabox_item_short_desc' );	// meta box item short desc
		$this->loader->add_action( 'save_post', $items_metaboxes, 'ctlggi_save_metabox_item_short_desc', 10, 2 );	// save meta box short desc
		
		// Meta Boxes - Save Order Title
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_order_save_new_post_title', 10, 1 );	// save new post title
		
		// Meta Boxes - Order General Details
		$this->loader->add_action( 'add_meta_boxes', $ordersmetaboxes, 'ctlggi_add_metabox_order_general_details' );	// meta box general details
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_save_metabox_order_general_details', 10, 1 );	// save meta box general details
		// Meta Boxes - Order Billing Details
		$this->loader->add_action( 'add_meta_boxes', $ordersmetaboxes, 'ctlggi_add_metabox_order_billing_details' );	// meta box order details
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_save_metabox_order_billing_details', 10, 1 );	// save meta box order details
		// Meta Boxes - Order Items
		$this->loader->add_action( 'add_meta_boxes', $ordersmetaboxes, 'ctlggi_add_metabox_order_items' );	// meta box order items
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_save_metabox_order_items', 10, 1 );	// save meta box order items
		
		// Meta Boxes - Resend Order Receipt (Important!!! fire after all metaboxes)
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_order_resend_order_receipt', 10, 1 );	// resend order receipt
		// Meta Boxes - Resend Payment Request (Important!!! fire after all metaboxes)
		$this->loader->add_action( 'save_post', $ordersmetaboxes, 'ctlggi_order_resend_payment_request', 10, 1 );	// resend payment request
		
		// It's important to note the 'before_delete_post' hook runs only when the WordPress user empties the Trash
		$this->loader->add_action( 'before_delete_post', $ordersmetaboxes, 'ctlggi_delete_single_order_data_if_empties_the_trash' );	// delete order data from custom tables
		
		// remove
		$this->loader->add_action( 'init', $ordersmetaboxes, 'ctlggi_remove_custom_post_type_support' );
		// hide publishing actions for publish metabox
		$this->loader->add_action( 'post_submitbox_misc_actions', $ordersmetaboxes, 'ctlggi_hide_publishing_actions' );
		
		// // publish meta add data (publish button)
		$this->loader->add_action( 'post_submitbox_misc_actions', $ordersmetaboxes, 'ctlggi_publish_meta_data' );
		
		// Admin Notices
		$this->loader->add_action( 'admin_notices', $admin_notices, 'ctlggi_admin_notice_order_receipt_sent' );
		$this->loader->add_action( 'admin_notices', $admin_notices, 'ctlggi_admin_notice_payment_request_sent' );
		
		// Settings - General Options
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_general_options_form_process' );
		
		// Settings - Currency Options
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_currency_options_form_process' );
		
		// Settings - Cart Options
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_cart_options_form_process' );
		
		// General Settings - Save Settings Options
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_save_settings_options_form_process' );
		
		// Settings - Template Options
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_template_options_form_process' );
		
		// Settings - Template System
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_template_system_form_process' );
		
		// Settings - Payment Gateways - main
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_payment_gateway_settings_form_process' );
		
		// Settings - Payment Gateways - BACS
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_payment_gateway_bacs_form_process' );
		// Settings - Payment Gateways - PayPal Standard
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_payment_gateway_paypalstandard_form_process' );
		
		// Settings - Emails - Email Settings
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_email_settings_options_form_process' );
		// Settings - Emails - Order Receipts
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_order_receipts_options_form_process' );
		// Settings - Emails - Order Notifications
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_order_notifications_options_form_process' );
		// Settings - Emails - Payment Requests
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_payment_requests_options_form_process' );
		
		// Settings - Misc - Google Analytics
		$this->loader->add_action( 'init', $plugin_admin, 'ctlggi_misc_google_analytics_options_form_process' );

		// custom post status - completed
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_completed' );
		// custom post status - processing
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_processing' );
		// custom post status - pending
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_pending' );
		// custom post status - failed
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_failed' );
		// custom post status - cancelled
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_cancelled' );
		// custom post status - refunded
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_refunded' );
		// custom post status - on hold
		$this->loader->add_action( 'init', $custompoststatuses, 'ctlggi_order_custom_post_status_on_hold' );
		
		// process update downloads
		$this->loader->add_action( 'wp_ajax_ctlggi_update_download_data_form_process', $manage_downloads, 'ctlggi_update_download_data_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_update_download_data_form_process', $manage_downloads, 'ctlggi_update_download_data_form_process' );
		
		// order view - order items table, display price options select field
		$this->loader->add_action( 'wp_ajax_ctlggi_admin_data_table_price_options_select_field', $order_items_data_table, 'ctlggi_admin_data_table_price_options_select_field' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_admin_data_table_price_options_select_field', $order_items_data_table, 'ctlggi_admin_data_table_price_options_select_field' );
		
		// order view - order items table, insert new row into the data table
		$this->loader->add_action( 'wp_ajax_ctlggi_admin_data_table_insert_new_item', $order_items_data_table, 'ctlggi_admin_data_table_insert_new_item' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_admin_data_table_insert_new_item', $order_items_data_table, 'ctlggi_admin_data_table_insert_new_item' );
		
		$this->loader->add_action( 'wp_ajax_data_table_insert_new_custom_item', $order_items_data_table, 'data_table_insert_new_custom_item' );
		$this->loader->add_action( 'wp_ajax_nopriv_data_table_insert_new_custom_item', $order_items_data_table, 'data_table_insert_new_custom_item' );
		
		$this->loader->add_action( 'wp_ajax_ctlggi_admin_data_table_update_total', $order_items_data_table, 'ctlggi_admin_data_table_update_total' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_admin_data_table_update_total', $order_items_data_table, 'ctlggi_admin_data_table_update_total' );
		

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public           = new CTLGGI_Public( $this->get_plugin_name(), $this->get_version() );
		//$rest_api               = new CTLGGI_Manage_Wp_Rest_Api( $this->get_plugin_name(), $this->get_version() );
		//$rest_api_support        = new CTLGGI_Wp_Rest_Api_Support( $this->get_plugin_name(), $this->get_version() );
		//$rest_api_endpoints      = new CTLGGI_Wp_Rest_Api_Endpoints( $this->get_plugin_name(), $this->get_version() );
		
		$template                = new CTLGGI_Template_System( $this->get_plugin_name(), $this->get_version() );
		
		$payment_buttons         = new CTLGGI_Payment_Buttons( $this->get_plugin_name(), $this->get_version() );
		//$express_checkout_button = new CTLGGI_Express_Checkout_Button( $this->get_plugin_name(), $this->get_version() );
		
		//$emailer                 = new CTLGGI_Emailer( $this->get_plugin_name(), $this->get_version() );
		$cataloggi_cart          = new CTLGGI_Cart( $this->get_plugin_name(), $this->get_version() );
		$cataloggi_checkout      = new CTLGGI_Checkout( $this->get_plugin_name(), $this->get_version() );
		$payments                = new CTLGGI_Payments( $this->get_plugin_name(), $this->get_version() );
		$cataloggi_amount        = new CTLGGI_Amount( $this->get_plugin_name(), $this->get_version() );
		$login_register          = new CTLGGI_Login_Register( $this->get_plugin_name(), $this->get_version() );
		$contact                 = new CTLGGI_Contact( $this->get_plugin_name(), $this->get_version() );
		$countries               = new CTLGGI_Countries( $this->get_plugin_name(), $this->get_version() );
		
		// shopping cart - process gateways
		$gateway_none            = new CTLGGI_Gateway_None( $this->get_plugin_name(), $this->get_version() ); // free payments
		$gateway_bacs            = new CTLGGI_Gateway_Bacs( $this->get_plugin_name(), $this->get_version() );
		$gateway_paypalstandard  = new CTLGGI_PayPal_Standard( $this->get_plugin_name(), $this->get_version() );
		$paypal_standard_buy_now = new CTLGGI_PayPal_Standard_Buy_Now( $this->get_plugin_name(), $this->get_version() );
		
		// shopping cart - File Download API
		$file_download           = new CTLGGI_File_Download_Api( $this->get_plugin_name(), $this->get_version() );
		
		$user_account            = new CTLGGI_User_Account( $this->get_plugin_name(), $this->get_version() );
		
		//$this->loader->add_action( 'init', $rest_api, 'ctlggi_manage_wp_rest_api' ); // manage wp rest api
		//$this->loader->add_action( 'plugins_loaded', $rest_api_support, 'ctlggi_cataloggi_rest_api_support', 25 ); // use plugins_loaded so fire only after CTPs loaded
		//$this->loader->add_action( 'init', $rest_api_support, 'ctlggi_cataloggi_orders_rest_api_support', 25 ); 
		//$this->loader->add_action( 'init', $rest_api_support, 'ctlggi_cataloggicat_rest_api_support', 25 ); 
		//$this->loader->add_action( 'rest_api_init', $rest_api_endpoints, 'ctlggi_prefix_register_example_routes' );  // reg endpoint
		//$this->loader->add_action( 'rest_api_init', $rest_api_endpoints, 'ctlggi_items_rest_register_routes' );  // reg endpoint
		
		//START WITH THIS!!!! allow redirection, even if the theme starts to send output to the browser
		$this->loader->add_action( 'init', $plugin_public, 'ctlggi_do_output_buffer' );
		
		//$this->loader->add_action( 'wp_head', $plugin_public, 'ctlggi_google_analytics_code' ); // Google Analytics, PAUSED
		$this->loader->add_action( 'template_redirect', $plugin_public, 'redirect_wp_homepage_to_cataloggi' );
		
		// File Download API Endpoint
		$this->loader->add_action( 'init', $file_download, 'endpoint' );
		$this->loader->add_filter( 'query_vars', $file_download, 'query_vars_filter', 10, 1  );
		$this->loader->add_action( 'template_redirect', $file_download, 'api_listener' );
		
        // <<<<<<<< load template pages >>>>>>>>>>>>
		$this->loader->add_filter( 'archive_template', $template, 'ctlggi_load_archive_template' ); // use WP  -> archive_template
		$this->loader->add_filter( 'taxonomy_template', $template, 'ctlggi_load_taxonomy_template' ); // use WP  -> taxonomy_template
		$this->loader->add_filter( 'single_template', $template, 'ctlggi_load_single_template' ); // use WP  -> single_template
		$this->loader->add_filter( 'template_include', $template, 'ctlggi_template_chooser_search' ); // use WP  -> template_include
		
		// remove hardcoded width and height from thumbnail images
		$this->loader->add_filter( 'post_thumbnail_html', $plugin_public, 'ctlggi_remove_thumbnail_dimensions', 10, 3 ); // use WP  -> post_thumbnail_html
		
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'ctlggi_cataloggicat_exclude_children' );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'ctlggi_cataloggicat_tax_query', 1 );
		
		// SHOPPING CART
		// ajax public, wp_ajax_nopriv_
		// ajax payment buttons process
		$this->loader->add_action( 'wp_ajax_ctlggi_payment_buttons_process', $payment_buttons, 'ctlggi_payment_buttons_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_payment_buttons_process', $payment_buttons, 'ctlggi_payment_buttons_process' );
		
		// Remove from cart form process
		$this->loader->add_action( 'wp_ajax_ctlggi_remove_from_cart_form_process', $cataloggi_cart, 'ctlggi_remove_from_cart_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_remove_from_cart_form_process', $cataloggi_cart, 'ctlggi_remove_from_cart_form_process' );
		// Update cart button
		$this->loader->add_action( 'wp_ajax_ctlggi_update_cart_process', $cataloggi_cart, 'ctlggi_update_cart_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_update_cart_process', $cataloggi_cart, 'ctlggi_update_cart_process' );
		// Items View: Normal, Large or List View
		$this->loader->add_action( 'wp_ajax_ctlggi_items_view_process', $plugin_public, 'ctlggi_items_view_process' ); // Items View: Normal, Large or List View
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_items_view_process', $plugin_public, 'ctlggi_items_view_process' );
		// login form
		$this->loader->add_action( 'wp_ajax_ctlggi_login_form_process', $login_register, 'ctlggi_login_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_login_form_process', $login_register, 'ctlggi_login_form_process' );
		// register form
		$this->loader->add_action( 'wp_ajax_ctlggi_register_form_process', $login_register, 'ctlggi_register_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_register_form_process', $login_register, 'ctlggi_register_form_process' );
		// forgot pw form
		$this->loader->add_action( 'wp_ajax_ctlggi_forgot_pw_form_process', $login_register, 'ctlggi_forgot_pw_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_forgot_pw_form_process', $login_register, 'ctlggi_forgot_pw_form_process' );
		// contact form
		$this->loader->add_action( 'wp_ajax_ctlggi_contact_form_process', $contact, 'ctlggi_contact_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_contact_form_process', $contact, 'ctlggi_contact_form_process' );
		// checkout form
		$this->loader->add_action( 'wp_ajax_ctlggi_checkout_form_process', $cataloggi_checkout, 'ctlggi_checkout_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_checkout_form_process', $cataloggi_checkout, 'ctlggi_checkout_form_process' );
		
		// payments form
		$this->loader->add_action( 'wp_ajax_ctlggi_payments_form_process', $payments, 'ctlggi_payments_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_payments_form_process', $payments, 'ctlggi_payments_form_process' );
		
		// shopping cart - process gateways
		
		// send order data for NONE gateway FREE Payments
		$this->loader->add_action( 'ctlggi_gateway_none', $gateway_none, 'ctlggi_order_data_for_gateway_none' ); // uses: ctlggi_gateway_$gateway
		$this->loader->add_action( 'wp_ajax_ctlggi_process_none_payment', $gateway_none, 'ctlggi_process_none_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_process_none_payment', $gateway_none, 'ctlggi_process_none_payment' );
		
		// send order data for BACS gateway 
		$this->loader->add_action( 'ctlggi_gateway_bacs', $gateway_bacs, 'ctlggi_order_data_for_gateway_bacs' ); // uses: ctlggi_gateway_$gateway
		$this->loader->add_action( 'wp_ajax_ctlggi_process_bacs_payment', $gateway_bacs, 'ctlggi_process_bacs_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_process_bacs_payment', $gateway_bacs, 'ctlggi_process_bacs_payment' );

		$this->loader->add_action( 'init', $gateway_paypalstandard, 'ctlggi_listen_paypal_ipn' );
		$this->loader->add_action( 'init', $gateway_paypalstandard, 'ctlggi_paypal_response' );
		$this->loader->add_action( 'init', $paypal_standard_buy_now, 'buy_now_listen_paypal_ipn' );
		$this->loader->add_action( 'init', $paypal_standard_buy_now, 'buy_now_paypal_response' );
		
		// send order data for PayPal Standard gateway
		$this->loader->add_action( 'ctlggi_gateway_paypalstandard', $gateway_paypalstandard, 'ctlggi_order_data_for_gateway_paypalstandard' ); // uses: ctlggi_gateway_$gateway
		$this->loader->add_action( 'wp_ajax_ctlggi_process_paypalstandard_payment', $gateway_paypalstandard, 'ctlggi_process_paypalstandard_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_ctlggi_process_paypalstandard_payment', $gateway_paypalstandard, 'ctlggi_process_paypalstandard_payment' );
		// PayPal Standard Buy Now button process payment
		$this->loader->add_action( 'wp_ajax_paypal_standard_buy_now_form_process', $paypal_standard_buy_now, 'paypal_standard_buy_now_form_process' );
		$this->loader->add_action( 'wp_ajax_nopriv_paypal_standard_buy_now_form_process', $paypal_standard_buy_now, 'paypal_standard_buy_now_form_process' );
		
        // load CSS after page load
		//$this->loader->add_action( 'wp_head', $plugin_public, 'load_public_js' );
		//$this->loader->add_action( 'wp_ajax_load_css_after_page_load', $plugin_public, 'load_css_after_page_load' );
		//$this->loader->add_action( 'wp_ajax_nopriv_load_css_after_page_load', $plugin_public, 'load_css_after_page_load' );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 15 ); // ### Important! Load style after theme style (15)
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

?>
