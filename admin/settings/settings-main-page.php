
<div class="wrap">
<h1>Cataloggi <?php _e('Settings', 'cataloggi'); ?></h1>
 <h1 class="nav-tab-wrapper">
<?php

// tabs extensible 
$tabs = CTLGGI_Admin::ctlggi_admin_settings_tabs();

// DEFAULTS
$pageslug = '';
$pagerequire = '';

if( isset($_GET['post_type']) && $_GET['post_type'] == "cataloggi") // post type
{  

	if( isset( $_GET['tab'] ) )
	{
		$get_tab = $_GET['tab'];
	} else {
		$get_tab = '';
	}
	
	$baseurl = home_url() . '/wp-admin/edit.php?post_type=cataloggi&page=cataloggi-settings&tab=';

	foreach( $tabs as $tab => $title )
	{  
	   // set current
	  if ( $get_tab == $tab ) {
		 $active   = 'nav-tab-active';
		 $pageslug = $tab;
	  } else {
		 $active = ''; 
	  }
	  
	  // set default page active
	  if ( empty ($get_tab) && $tab == 'general-main' ) {
		 $active_default  = 'nav-tab-active';
		 $pageslug = 'general-main';
	  } else {
		 $active_default = ''; 
	  }
		 
	  echo '<a href="' . esc_url( $baseurl . $tab ) . '" title="' . esc_attr( $title ) . '" class="nav-tab ' . esc_attr( $active ) . ' ' . esc_attr( $active_default ) . '"> ' . esc_html( $title ) . '</a>';
	  
	}

}

?>
 </h1>
<?php

if( isset($_GET['post_type']) && $_GET['post_type'] == "cataloggi") // post type
{  
  // page content
  if ( file_exists( CTLGGI_PLUGIN_DIR . 'admin/settings/' . $pageslug . '.php' ) ) {
	 require $pagerequire = CTLGGI_PLUGIN_DIR . 'admin/settings/' . $pageslug . '.php';
  } else {
	// make it extensible
	do_action( 'ctlggi_admin_add_settings_main_page' ); // <- extensible
	//echo 'Page Not Exist.'; // test
  }
  
}

?>
    
</div><!--/ .wrap -->