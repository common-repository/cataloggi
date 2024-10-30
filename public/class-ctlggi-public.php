<?php

/**
 * Public Main class.
 *
 * @package     cataloggi
 * @subpackage  Public
 * @copyright   Copyright (c) 2016, Codeweby - Attila Abraham
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
 * @since       1.0.0
*/
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
class CTLGGI_Public {

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
	 * Allow redirection, even if the theme starts to send output to the browser.
	 * Usees:  page=cart and page=checkout
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function ctlggi_do_output_buffer() {
	  ob_start();
	}
	
	/**
	 * Google Analytics.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function ctlggi_google_analytics_code() {
	    $options      = get_option('ctlggi_google_analytics_options');
	    $tracking_id  = isset( $options['google_analytics_tracking_id'] ) ? sanitize_text_field( $options['google_analytics_tracking_id'] ) : '';
		if ( !current_user_can('edit_posts') && !empty($tracking_id) ) {
			?>
			
			<!-- Google Analytics -->
			<script type="text/javascript">
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			  ga('create', '<?php echo $tracking_id; ?>', 'auto');
			  ga('send', 'pageview');
			
			</script>	
			<?php
		}
	}

	/**
	 * Auto redirect WP home page to Cataloggi's hoome page.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function redirect_wp_homepage_to_cataloggi() {
		
		if( ! is_home() && ! is_front_page() )
			return;
			
		$save_settings_options = get_option('ctlggi_save_settings_options');
		if ( isset($save_settings_options['ctlggi_redirect_wp_home_to_cataloggi']) && $save_settings_options['ctlggi_redirect_wp_home_to_cataloggi'] == '1'  ) {
			// do redirect
			$rewrite_slug = CTLGGI_Custom_Post_Types::ctlggi_cataloggi_cpt_rewrite_slug(); // Cataloggi rewrite slug
			$cataloggi_home_page = home_url() . '/' . $rewrite_slug . '/';
			
			wp_redirect( $cataloggi_home_page, 301 );
			exit();
		} else {
			return;
		}
	}
	
	/**
	 * Check if multisite is enabled then get network or home url
	 *
	 * @since 1.0.0
	 * @return void url
	 */
	public static function ctlggi_get_site_url() {
		if ( is_multisite() ) {
			return network_home_url();
		} else {
			return home_url();
		}
	}

	/**
	 * Check if page exist by post name (slug).
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param  string $post_name
	 * @return bool
	 */
	public static function ctlggi_check_if_page_slug_exists($post_name) {
		
	    if ( empty( $post_name ) )
	    return;
		
		global $wpdb;
		$posts = $wpdb->prefix . 'posts'; // table, do not forget about tables prefix 
		
		if($wpdb->get_row("SELECT post_name FROM $posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Ajax Front end items view selector. Save the selected view in cookie.
	 * Items View: Normal, Large or List View
	 *
	 * @since 1.0.0
	 * @return void
	 */
    public function ctlggi_items_view_process() 
	{
		if ( isset ($_POST['itemsview']) ) {

			// get domain name
			$domain = CTLGGI_Helper::ctlggi_site_domain_name();
			// get form data
			$cataloggiitemsview = sanitize_text_field( $_POST['itemsview'] );
			// cookie name
			$items_view_cookie_name  = CTLGGI_Cookies::ctlggi_items_view_cookie_name();
			// delete cookie
			$del_cookie = CTLGGI_Cookies::ctlggi_delete_cookie($name=$items_view_cookie_name, $path = '/', $domain, $remove_from_global = false);
			// set cookie, expires in 1 year
			$set_cookie = CTLGGI_Cookies::ctlggi_set_cookie($name=$items_view_cookie_name, $value=$cataloggiitemsview, $expiry = 31536000, $path = '/', $domain, $secure = false, $httponly = false );
			
		} else {
			return;
		}
		
	    exit; // don't forget to exit!
		
	}

	/**
	 * Exclude children in taxonomy query.
	 *
	 * @since 1.0.0
	 * @param  object $query
	 * @return void
	 */
	public function ctlggi_cataloggicat_exclude_children($query) {
	
		if ($query->is_main_query() && $query->is_tax('cataloggicat')):
			$tax_obj = $query->get_queried_object();
			$tax_query = array(
				'taxonomy' => $tax_obj->taxonomy,
				'field' => 'slug',
				'terms' => $tax_obj->slug,
				'include_children' => FALSE
			);
			$query->tax_query->queries[] = $tax_query;
			$query->query_vars['tax_query'] = $query->tax_query->queries;
	
		endif;
	
	}

	/**
	 * Set Taxonomy query.
	 *
	 * @since 1.0.0
	 * @param  object $query
	 * @return void
	 */
	public function ctlggi_cataloggicat_tax_query($query) {
	
		if (is_admin() || !$query->is_main_query())
			return;
	     
		 // taxonomy=cataloggicat&post_type=cataloggi
		if ( is_tax('cataloggicat') ) {	
            $ctlggi_general_options = get_option('ctlggi_general_options'); 
			//$query->set('posts_per_page', '6'); // or use variable key: posts_per_page or posts_per_archive_page
			$query->set('posts_per_page', $ctlggi_general_options['number_of_items_per_page']); // items per page
			$query->set('orderby', $ctlggi_general_options['items_order_by']); // ID, date, title...
			$query->set('order', $ctlggi_general_options['items_order']);// ASC, DESC
			return;
		}
	
	}

	/**
	 * Set Taxonomy query post per page.
	 *
	 * @since 1.0.0
	 * @param  object $query
	 * @return void
	 */
	public function ctlggi_limit_archive_posts_per_page( $query ) {
	  if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'cataloggi' ) ) {
		$query->set( 'posts_per_page', '5' );
		//$query->set( 'posts_per_archive_page', 5 );
	  }
	}

	/**
	 * Archive page limit post per page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ctlggi_limit_posts_per_archive_page() {
		if ( is_category() )
			set_query_var('posts_per_page', '5'); // or use variable key: posts_per_page or posts_per_archive_page
	}
	
	/**
	 * Remove hardcoded width and height from thumbnail images.
	 *
	 * @since 1.0.0
	 * @param  string $html
	 * @param  int    $post_id
	 * @param  int    $post_image_id
	 * @return void   $html
	 */
	public function ctlggi_remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
		return $html;
	}

	/**
	 * Shorten titles for cataloggi categories.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param  string $after
	 * @param  int    $length
	 * @return string $s_title
	 */
	public static function ctlggi_shorten_title($after = '', $length) {
		$s_title = explode(' ', get_the_title(), $length);
		if (count($s_title)>=$length) {
			array_pop($s_title);
			$s_title = implode(" ",$s_title). $after;
		} else {
			$s_title = implode(" ",$s_title);
		}
		return $s_title;
	}

	/**
	 * Shorten text.
	 *
	 * @since 1.0.0
	 * @access public static
	 * @param  string $text
	 * @param  int    $limit
	 * @return string $text
	 */
	public static function ctlggi_shorten_text($text, $limit) {
		  if (str_word_count($text, 0) > $limit) {
			  $words = str_word_count($text, 2);
			  $pos = array_keys($words);
			  $text = substr($text, 0, $pos[$limit]) . '...';
		  }
		  return $text;
	}
	
	/**
	 * Find out if there is a shortcode on the page.
	 *
	 * @global $post
	 *
	 * @since 1.0.0
	 * @access public static
	 * @return bool
	 */
	public static function ctlggi_check_if_has_shortcode() {
		global $post;

		// Currently ( 5/8/2014 ) the has_shortcode() function will not find a 
		// nested shortcode. This seems to do the trick currently, will switch if 
		// has_shortcode() gets updated. -NY
		// check if post exist
		if ($post) { 
		// check if has shortcode
			if ( strpos($post->post_content, '[cataloggi') != false  ) {							   							   
				return true;
			}
		}

		return false;
	}
	
	public function load_public_js() {
		wp_enqueue_script( 'ctlggi-public-js', plugin_dir_url( __FILE__ ) . 'assets/js/ctlggi-public.js', array( 'jquery' ), $this->version, false );
	}
	
	/**
	 * Load styles after page loaded..
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function load_css_after_page_load() {
		//add_action( 'wp_enqueue_scripts', array('CTLGGI_Public', 'enqueue_styles'), 15 ); // ### Important! Load style after theme style (15)
	   if ( isset ($_POST['ok']) ) {
		   
	   }
       return;
	   
	   exit; // don't forget!
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'ctlggi-front-end', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/ctlggi-front-end.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-widgets-style', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/widgets-style.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-cataloggi-grid', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/cataloggi-grid.css', array(), $this->version, 'all' );  
		wp_enqueue_style( 'ctlggi-table-responsive', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/table-responsive.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-responsive-pricing-table', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/responsive-pricing-table.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( 'ctlggi-pricing-table', plugin_dir_url( __FILE__ ) . 'assets/' . CTLGGI_CSS_MODE . '/pricing-table.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-glyphicon', plugins_url() . '/' . $this->plugin_name . '/assets/' . CTLGGI_CSS_MODE . '/glyphicon.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ctlggi-cw-form', plugins_url() . '/' . $this->plugin_name . '/assets/' . CTLGGI_CSS_MODE . '/cw-form.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since      1.0.0
     * @return     void
	 */
	public function enqueue_scripts() {
		
		
			
		$formloaderimg = CTLGGI_Process_Order::ctlggi_order_processing_form_loader_image(); // form loader image
		$ctlggi_success_redirect_url = CTLGGI_Process_Order::ctlggi_order_processing_success_redirect_url(); // redirect url (option)
		
		$login_success_redirect_url = CTLGGI_Login_Register::ctlggi_login_success_redirect_url(); // redirect url (option)
			
		// Add to cart button
		wp_enqueue_script( 'ctlggi-shopping-cart', plugin_dir_url( __FILE__ ) . 'shopping-cart/assets/' . CTLGGI_JS_MODE . '/ctlggi-shopping-cart.js', array( 'jquery' ), $this->version, true );
		
		wp_localize_script( 'ctlggi-shopping-cart', 'ctlggi_ajax_shopping_cart', array( 
			'ctlggi_wp_ajax_url'           => admin_url( 'admin-ajax.php' ),
			'ctlggi_login_redirect_url'    => $login_success_redirect_url,
			'ctlggi_register_redirect_url' => $_SERVER['REQUEST_URI'],
			'ctlggi_form_loader_img'       => $formloaderimg,
			'ctlggi_refress_page'          => $_SERVER['REQUEST_URI'], // for payment gateways
			'ctlggi_success_redirect_url'  => $ctlggi_success_redirect_url, // for payment gateways
			'ctlggi_loading_message'       => __('Signing in, please wait...', 'cataloggi')
		));
		
		// REST API
		wp_localize_script( 'wp-api', 'wpApiSettings', array(
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		) );

	}

}

?>
