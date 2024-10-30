<?php 

	// check if user logged in
	if ( is_user_logged_in() ) {
		
		// if logged in get current user data
		$current_user = wp_get_current_user();
		
		/*
		echo '<pre>';
		print_r($current_user);
		echo '</pre>';
		*/
		
		$user_id     = $current_user->ID;
		$username    = $current_user->user_login;
		$email       = $current_user->user_email;
		$first_name  = $current_user->user_firstname;
		$last_name   = $current_user->user_lastname;
		$displayname = $current_user->display_name;
		
		$user_roles = $current_user->roles;
		
		if( isset($_REQUEST['view-order']) && $_REQUEST['view-order'] != "") { 
		    // ORDER HISTORY VIEW PAGE
		    require_once CTLGGI_PLUGIN_DIR . 'public/account/order-history-view.php';
		} else {
			// ORDER HISTORY PAGE
			require_once CTLGGI_PLUGIN_DIR . 'public/account/order-history.php';
		}
		
    } // end if logged in

?>