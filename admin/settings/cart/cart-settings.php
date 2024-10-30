
<div id="tab_container">

	<?php 
	    
/*
echo '<pre>';
print_r($ctlggi_cart_options);
echo '</pre>';
*/
		
$display_payment_button  = isset( $ctlggi_cart_options['display_payment_button'] ) ? sanitize_text_field( $ctlggi_cart_options['display_payment_button'] ) : '';
		
$results =  CTLGGI_Admin::get_all_pages();

/*
echo '<pre>';
print_r($results);
echo '</pre>';
*/
    ?>
    
<form method="post" action="" id="ctlggi-cart-options-form">

<input type="hidden" name="ctlggi-cart-options-form-nonce" value="<?php echo wp_create_nonce('ctlggi_cart_options_form_nonce'); ?>"/>

    <table class="form-table">
        <h2 class="padding-top-15"><?php _e('Cart Settings', 'cataloggi'); ?></h2>
        <tbody>
            <tr>
                <th scope="row"><?php _e('Activate the Cart', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="enable_shopping_cart" id="enable_shopping_cart" <?php echo ($ctlggi_cart_options['enable_shopping_cart'] == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Activate the ecommerce cart system.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Payment Button', 'cataloggi'); ?></th>
                <td>
                    <input type="checkbox" value="1" name="display_payment_button" id="display_payment_button" <?php echo ($display_payment_button == '1') ? 'checked' : '' ?>>
                    <p class="description"><?php _e('Display the Payment Buttons on the product listing pages.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Cart Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="cart_page" id="cart_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['cart_page'] ); ?>">
					<?php 
					$id_cart = $ctlggi_cart_options['cart_page']; 
					if ( $id_cart != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['cart_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                  </select>
                        <p class="description"><?php _e('Shopping Cart page. Shortcodes [ctlggi_cart] and [ctlggi_cart_totals] must be on this page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Checkout Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="checkout_page" id="checkout_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['checkout_page'] ); ?>">
					<?php 
					$id_checkout = $ctlggi_cart_options['checkout_page']; 
					if ( $id_checkout != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['checkout_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                        </select>
                        <p class="description"><?php _e('Checkout page. Shortcodes [ctlggi_checkout_totals] and [ctlggi_checkout] must be on this page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Payments Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="payments_page" id="payments_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['payments_page'] ); ?>">
					<?php 
					$id_payments = $ctlggi_cart_options['payments_page']; 
					if ( $id_payments != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['payments_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                        </select>
                        <p class="description"><?php _e('Payments page. Shortcodes [ctlggi_payments_totals] and [ctlggi_payments] must be on this page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Terms Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="terms_page" id="terms_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['terms_page'] ); ?>">
					<?php 
					$id_terms = $ctlggi_cart_options['terms_page']; 
					if ( $id_terms != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['terms_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                        </select>
                        <p class="description"><?php _e('Select your terms & conditions page. This page link will be visible on the checkout page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Success Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="success_page" id="success_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['success_page'] ); ?>">
                    <?php
					$id_success = $ctlggi_cart_options['success_page']; 
					if ( $id_success != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['success_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                        </select>
                        <p class="description"><?php _e('Buyers will be redirected to this page upon successful payments. You can use [ctlggi_order_receipt] shortcode on this page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Order History Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="order_history_page" id="order_history_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['order_history_page'] ); ?>">
                    <?php
					$id_order_history = $ctlggi_cart_options['order_history_page']; 
					if ( $id_order_history != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['order_history_page'] ) ); 
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . '' . '</option>';
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					} 
                    ?>
                        </select>
                        <p class="description"><?php _e('Current user order history page. Shortcode [ctlggi_order_history] must be on this page.', 'cataloggi'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Login Redirect Page', 'cataloggi'); ?></th>
                <td>
                <select class="small-text" name="login_redirect_page" id="login_redirect_page">
                    <option selected="selected" value="<?php echo esc_attr( $ctlggi_cart_options['login_redirect_page'] ); ?>">
                    <?php
					
					$id_login_redirect = $ctlggi_cart_options['login_redirect_page']; 
					if ( $id_login_redirect != '0' ) {
					  echo esc_attr( get_the_title( $ctlggi_cart_options['login_redirect_page'] ) ); 
					} else {
						_e('Dashboard', 'cataloggi');
					}
					?>
                    </option>
					<?php
					echo '<option value="0">' . __('Dashboard', 'cataloggi') . '</option>'; // default,wp-admin/index.php
					if ( !empty($results) ) {
						foreach( $results as $result ) {
							echo '<option value="' . esc_attr( $result['ID'] ) . '">' . esc_attr( $result['post_title'] ) . '</option>';
						}
					}
                    ?>
                        </select>
                        <p class="description"><?php _e('Users will be redirected to this page upon successful login.', 'cataloggi'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>

    <?php submit_button(); ?>
</form>

</div><!--/ #tab_container-->