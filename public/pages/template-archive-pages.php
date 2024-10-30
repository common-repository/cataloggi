<?php 

if ( isset($_GET['page'] ) ) {
	// sub pages ()shopping cart	
  if($_GET['page'] == "cart") { 
	// cart
	require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/cart.php';
  } elseif($_GET['page'] == "checkout") { 
	// checkout
	require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/checkout.php';
  } elseif($_GET['page'] == "payments") { 
	// payments
	require_once CTLGGI_PLUGIN_DIR . 'public/shopping-cart/pages-front-end/payments.php';
  } else {
	// home page
	require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-home.php';
  }
	
} else {
	// DEFAULT - home page
	require_once CTLGGI_PLUGIN_DIR . 'public/pages/cataloggi-home.php';
}

?>