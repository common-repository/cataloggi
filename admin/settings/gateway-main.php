<?php 

// DEFAULT SETTINGS
$showgatewaypagecontent = '';
// set current on selected page

if( isset($_GET['post_type']) && $_GET['post_type'] == "cataloggi") // post type
{   
  if( isset($_GET['tab']) && $_GET['tab'] == "gateway-main" )
 {

?>

<div>
    <ul class="subsubsub">
<?php 
	 // set current
	 $currentmain = '';
	 if ( ! isset($_GET['sub']) || $_GET['sub'] == 'gateway-settings' ) {
		 $currentmain = 'current';
	 } else {
		$currentmain = ''; 
	 }
?>
        <li>
        <a href="<?php echo esc_url( home_url() . '/wp-admin/edit.php?post_type=cataloggi&page=cataloggi-settings&tab=gateway-main&sub=gateway-settings' ); ?>" class="<?php echo esc_attr( $currentmain ); ?>"><?php _e('Gateway Settings', 'cataloggi'); ?></a> | 
        </li>
 
<?php 

	$payment_gateways = CTLGGI_Payment_Gateways::ctlggi_payment_gateways(); 
	$json = json_encode( $payment_gateways ); // convert array to json

	$gateways_obj   = json_decode( $json ); // Translate into an object
	$gateways_array = json_decode( $json, true ); // Translate into an array
	
	$baseurl = home_url() . '/wp-admin/edit.php?post_type=cataloggi&page=cataloggi-settings&tab=gateway-main&sub=';
	
	// if has contents
	if(count($gateways_obj)>0)
	{
	  $current = '';
	  $currentgateway = '';
	  foreach( $gateways_obj as $gateway => $value )
	  {
		 // set current
		 if ( isset($_GET['sub']) && $_GET['sub'] == $gateway ) {
			 $current = 'current';
			 $currentgateway = $value->payment_gateway_name;
		 } else {
			$current = ''; 
		 }
		  // do not display the "none" gateway
		 if ( $gateway != 'none' ) {
			 echo '<li>'; 
			 echo '<a href="' . esc_url( $baseurl . $gateway ) . '" class="' . esc_attr( $current ) . '"> ' . esc_attr( $value->payment_gateway_label ) . '</a> | &nbsp;'; 
			 echo '</li>'; 
		 }
		 
	  }
	  
	}

?> 

    </ul>
</div>

<br class="clear">

<?php 

  // set content
  if( isset($_GET['sub']) && $_GET['sub'] != 'gateway-settings' ) 
  {
	
	// default pages - check if file exist
	if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'settings/gateways/' . $_GET['sub'] . '.php' ) ) {
		// content
		require $showgatewaypagecontent = plugin_dir_path( dirname( __FILE__ ) ) . 'settings/gateways/' . $_GET['sub'] . '.php'; 
	} else {
		// make it extensible
		do_action( 'ctlggi_admin_add_settings_gateways_sub_page' ); // <- extensible	
		
	}
 
  } else {
	 // default page
	require $showgatewaypagecontent = plugin_dir_path( dirname( __FILE__ ) ) . 'settings/gateways/gateway-settings.php'; 
  }
	  


 }
 
}

?>