<?php

	/**
	 * Public Shortcodes.
	 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_shortcodes {
	
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
	 * Cataloggi catalog home page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_home_page()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-home.php'; // home and shopping cart pages template-archive-pages.php
	}
	
	/**
	 * Cataloggi catalog products list page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_products_list()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-products-list.php';
	}
	
	/**
	 * Cataloggi catalog product view page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_single_product_view()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-single-product-view.php';
	}
	
	/**
	 * Cataloggi catalog search results page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_search_results_page()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-search-results.php'; // home and shopping cart pages
	}
	
	/**
	 * Cataloggi template files inner template header.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_inner_template_header() 
	{		
		// get options
		$ctlggi_template_options = get_option('ctlggi_template_options');
		$html = stripslashes($ctlggi_template_options['inner_template_header']);
		//$html = '<div class="container" style="margin-left: auto; margin-right: auto;">';
		return apply_filters( 'ctlggi_inner_template_header', $html ); // <- extensible
	}

	/**
	 * Cataloggi template files inner template footer.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_inner_template_footer() 
	{
		// get options
		$ctlggi_template_options = get_option('ctlggi_template_options');
		$html = stripslashes($ctlggi_template_options['inner_template_footer']);
		//$html = '</div>';
		return apply_filters( 'ctlggi_inner_template_footer', $html ); // <- extensible
	}
	
	/**
	 * Cataloggi catalog products.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_products()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/products.php';
	}
	
	/**
	 * Cataloggi catalog home page products.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_home_products()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/home-products.php';
	}
	
	/**
	 * Cataloggi catalog home page category boxes. [ctlggi_home_category_boxes]
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_category_boxes()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/home-category-boxes.php';
	}
	
	/**
	 * Cataloggi catalog category boxes. [ctlggi_cat_boxes]
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_cat_boxes()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/category-boxes.php';
	}
	
	/**
	 * Cataloggi catalog product list sub category boxes.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_sub_category_boxes()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/sub-category-boxes.php';
	}
	
	/**
	 * Cataloggi catalog sidebar basket.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_sidebar_basket()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/includes/sidebar-basket.php';
	}
	
	/**
	 * Cataloggi catalog sidebar search.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_sidebar_search()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/includes/sidebar-search.php';
	}
	
	/**
	 * Cataloggi catalog sidebar categories.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_sidebar_nav()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/pages/includes/categories-nav.php';
	}
	
	/**
	 * Cataloggi catalog grid or list view selector.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_grid_or_list_view()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/grid-or-list-view.php';
	}
	
	/**
	 * Cataloggi drop down categories nav.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function shortcode_categories_drop_down_nav()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/categories-drop-down-nav.php';
	}

	/**
	 * Cataloggi catalog breadcrumbs.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_breadcrumbs()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/breadcrumbs.php';
	}
	
	/**
	 * Cataloggi catalog search results.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_search_results()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/search-results.php';
	}
	
	/**
	 * Cataloggi catalog single product view.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_product_view()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/product-view.php';
	}
	
	/**
	 * Cataloggi shopping cart cart page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_cart()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/cart.php';
	}
	
	/**
	 * Cataloggi shopping cart cart totals.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_cart_totals()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/cart-totals.php';
	}
	
	/**
	 * Cataloggi shopping cart checkout page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_checkout()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/checkout.php';
	}
	
	/**
	 * Cataloggi shopping cart checkout totals.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_checkout_totals()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/checkout-totals.php';
	}
	
	/**
	 * Cataloggi payments page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_payments()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/payments.php';
	}
	
	/**
	 * Cataloggi payments totals.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_payments_totals()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/payments-totals.php';
	}
	
	/**
	 * Cataloggi shopping cart basket.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_basket()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/basket.php';
	}
	
	/**
	 * Cataloggi catalog search.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_search()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/search.php';
	}
	
	/**
	 * Cataloggi catalog categories nav.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_categories_nav()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/categories-nav.php';
	}
	
	
	##### ->
	
	
	/**
	 * Cataloggi login form.
	 *
	 * @since   1.0.0
	 * @param   array $atts
	 * @return  void
	 */
	public function ctlggi_shortcode_login_form( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'title' => __( 'Login to your Account', 'cataloggi' ),
      ), $atts );
	
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/login-form.php';
	}
	
	/**
	 * Cataloggi register form.
	 *
	 * @since   1.0.0
	 * @param   array $atts
	 * @return  void
	 */
	public function ctlggi_shortcode_register_form( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'title' => __( 'Create an Account', 'cataloggi' ),
        'role'  => 'subscriber',
      ), $atts );
	
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/register-form.php';
	}
	
	/**
	 * Cataloggi forgot password form.
	 *
	 * @since   1.0.0
	 * @param   array $atts
	 * @return  void
	 */
	public function ctlggi_shortcode_forgot_pw_form( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'title' => __( 'Forgot Password', 'cataloggi' ),
      ), $atts );
	
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/forgot-pw-form.php';
	}
	
	/**
	 * Cataloggi account.
	 *
	 * @since   1.0.0
	 * @param   array $atts
	 * @return  void
	 */
	public function ctlggi_shortcode_account( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'role'  => 'subscriber',
      ), $atts );
	
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/account.php';
	}
	
	/**
	 * Cataloggi login manager.
	 * Include login, register and forgot password forms.
	 *
	 * @since   1.0.0
	 * @param   array $atts
	 * @return  void
	 */
	public function ctlggi_shortcode_login_manager( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'role'  => 'subscriber',
      ), $atts );
	
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/login-manager.php';
	}
	
	/**
	 * Cataloggi contact form.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_contact_form( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'title' => __( 'Please enter your contact details and a short message below, we will try to answer your query as soon as possible.', 'cataloggi' ), //e.g. Please enter your contact details and a short message below, we will try to answer your query as soon as possible.
      ), $atts );
	  
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/contact-form.php';
	}
	
	/**
	 * Cataloggi order history.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_order_history()
	{ 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/order-history.php';
	}
	
	/**
	 * Cataloggi payment buttons. add to cart, buy now etc.
	 *
	 * @since   1.0.0
	 * @param   array $button_atts
	 * @return  void
	 */
	public function ctlggi_shortcode_payment_buttons( $button_atts )
	{ 
	  // Buy Now Button defaults: 
      $button_atts = shortcode_atts( array(
		'id'                => '0', 
		'size'              => 'normal', // values:  small, normal, large (button, select, quantity size)
		'type'              => 'buynow', // buynow or cart
		'gateway'           => 'paypalstandard', // default gateway for buy now buttons
		'custom_item'       => 'no', // custom item: yes or no
		'custom_item_name'  => '', // only for custom buttons
		'custom_item_price' => '', // only for custom buttons
		'custom_field'      => '', // This is for Custom Made Plugins Only!!!! can be use for any extra data 
		'grouped_products'  => '', // use this for grouped products (e.g. store IDs, product names etc.)
		'quantity'          => 'no', // display quantity field: yes or no
		'guest_payment'     => 'no', // allow guest payments:  yes or no
		'label_one'         => __( ' - Buy Now', 'cataloggi' ), // - Buy Now, - Add To Cart
		'label_two'         => __( ' + Checkout', 'cataloggi' ), // + Checkout, + View Cart
		'color_one'         => '#28a0e5', // blue: #28a0e5, orange: #ec7a5c
		'color_two'         => '#28a0e5', // blue: #28a0e5, orange: #ec7a5c
      ), $button_atts );
	  
	  //$shortcode = '[ctlggi_payment_button id="" page="" label_one="" color_one="" label_two="" color_two=""]';
	  
	  // payment button
	  return CTLGGI_Payment_Buttons::ctlggi_output_payment_button( $button_atts ); 

	}
	
	/**
	 * Cataloggi order receipt.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ctlggi_shortcode_order_receipt( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'title' => __( '', 'cataloggi' ), //e.g. Thank you for your purchase!
      ), $atts );
	  
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/order-receipt.php';
	}
	
	/**
	 * Docs and demo widget shortcode.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function docs_and_demo() { 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/docs-and-demo.php';
	}
	
	/**
	 * Shorten shortcode for themes archive page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function themes_shortcode_archive_page() { 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/themes/archive-cataloggi.php';
	}
	
	/**
	 * Shorten shortcode for themes search page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function themes_shortcode_search_page() { 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/themes/search-cataloggi.php';
	}
	
	/**
	 * Shorten shortcode for themes categories taxonomy page.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function themes_shortcode_categories_taxonomy_page() { 
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/themes/taxonomy-cataloggicat.php';
	}
	
	/**
	 * List all the products from the defined category.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function list_products_by_category_slug( $atts )
	{ 
	  // defaults
      $atts = shortcode_atts( array(
        'slug' => __( '', 'cataloggi' ), //e.g. Thank you for your purchase!
      ), $atts );
	  
      require_once CTLGGI_PLUGIN_DIR . 'public/shortcodes/products-by-category-slug.php';
	}
	
	
}

?>