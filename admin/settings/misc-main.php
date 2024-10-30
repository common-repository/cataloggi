<?php 

// subs extensible 
$subs = CTLGGI_Admin::ctlggi_admin_settings_misc_subs();

// DEFAULTS
$pageslug = '';
$pagerequire = '';

if( isset($_GET['post_type']) && $_GET['post_type'] == "cataloggi") // post type 
{  

	if( isset($_GET['tab']) && $_GET['tab'] == "misc-main")
	{
	
		if( isset( $_GET['sub'] ) )
		{
			$get_sub = $_GET['sub'];
		} else {
			$get_sub = '';
		}
		
		$baseurl = home_url() . '/wp-admin/edit.php?post_type=cataloggi&page=cataloggi-settings&tab=misc-main&sub=';
	
		echo '<div>';
		echo '<ul class="subsubsub">';
	
		foreach( $subs as $sub => $title )
		{  
		   // set current
		  if ( $get_sub == $sub ) {
			 $active   = 'current';
			 $pageslug = $sub;
		  } else {
			 $active = ''; 
		  }
		  
		  // set default page active
		  if ( empty ($get_sub) && $sub == 'google-analytics' ) {
			 $active_default  = 'current';
			 $pageslug = 'google-analytics';
		  } else {
			 $active_default = ''; 
		  }
		  
		  echo '<li>';
		  echo '<a href="' . esc_url( $baseurl . $sub ) . '" title="' . esc_attr( $title ) . '" class=" ' . esc_attr( $active ) . ' ' . esc_attr( $active_default ) . '"> ' . esc_html( $title ) . '</a> | &nbsp;';
		  echo '</li>';
		  
		}
		
		echo '</ul>';
		echo '</div>';
		
		echo '<br class="clear">';
		
		// page content
		if ( file_exists( CTLGGI_PLUGIN_DIR . 'admin/settings/misc/' . $pageslug . '.php' ) ) {
		  require $pagerequire = plugin_dir_path( dirname( __FILE__ ) ) . 'settings/misc/' . $pageslug . '.php';
		} else {
		  // make it extensible
		  do_action( 'ctlggi_admin_add_settings_misc_sub_page' ); // <- extensible
		  //echo 'Page Not Exist.'; // test
		    
			// example for extensible, only have to check the GET sub
			/*
			if( isset($_GET['sub']) && $_GET['sub'] == 'your-sub-page-slug' ) 
			{
			  
			}
			*/
			
		}
		
	}

}

?>