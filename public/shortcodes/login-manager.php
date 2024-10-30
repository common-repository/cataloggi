<?php 

	// check if user logged in
	if ( ! is_user_logged_in() ) {
		
?>		
<div class="cataloggi-log-reg-buttons">
 <a onclick="return false;" href="/" cataloggi-form-type="login" class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-light-green"><?php _e( 'Login', 'cataloggi' ); ?></a>
 <a onclick="return false;" href="/" cataloggi-form-type="register" class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-light-green"><?php _e( 'Register', 'cataloggi' ); ?></a>
 <a onclick="return false;" href="/" cataloggi-form-type="forgot_pw" class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-light-green"><?php _e( 'Forgot Password', 'cataloggi' ); ?></a>
 </div>
 
<div class="cataloggi-display-login-form">
<?php echo do_shortcode('[ctlggi_login_form]'); // login form ?>
</div>

<div class="cataloggi-display-register-form">
<?php echo do_shortcode('[ctlggi_register_form title="Create an Account" role="' . $atts['role'] . '"]'); // register form, roles= cataloggi_subscriber or cataloggi_customer ?>
</div>

<div class="cataloggi-display-forgot-pw-form">
<?php echo do_shortcode('[ctlggi_forgot_pw_form]'); // forgot password form ?>
</div>
<?php 

    } // end if logged in
	else {
		// redirect to page if login_redirect_page defined in Admin/Settings/General/Shopping Cart
		// get options
		$ctlggi_cart_options = get_option('ctlggi_cart_options');
		$redirect_page_id = $ctlggi_cart_options['login_redirect_page']; // redirect page id
		
		// redirect to page if exist
		if ( $redirect_page_id != '0' && ! empty($redirect_page_id)  ) {
			// get page link by id
			$page_link = get_permalink( $redirect_page_id );
			// if exist redirect to page
			wp_redirect( $page_link, 302 );
			exit();
		} else {
			// default
			$login_redirect_url = admin_url('index.php'); // default,wp-admin/index.php
			wp_redirect( $login_redirect_url, 302 );
			exit();
		}
		
	}

?>