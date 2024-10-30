<?php 

		global $wpdb;
		
		// GET DB LICENSE DATA
		$postmeta = $wpdb->prefix . 'postmeta'; // table, do not forget about tables prefix
		//  and meta_key = '_ctlggi_lic_order_id'
		$sql  = "
				SELECT *
				FROM $postmeta
				WHERE meta_key = '_ctlggi_order_cus_user_id' and meta_value = '$user_id' ORDER BY meta_id DESC
				";
		// save each result in array		
		$results = $wpdb->get_results( $sql, ARRAY_A ); // returns array: ARRAY_A
		
		########## UPDATE LICENSE(S) ##########
		if ( ! empty($results) ) {	
			
			/*
			echo '<pre>';
			print_r($results);
			echo '</pre>';
			*/
			
			// get current page url
			//$curr_page_url = esc_url(the_permalink());
			
			foreach( $results as $result )
			{
				// get licenses post id
				if ( $result['meta_key'] == '_ctlggi_order_cus_user_id' ) {
					// save in array
					$order_post_ids[] = $result['post_id'];
				}
			}
			
			$url = ''; // def
			$ctlggi_cart_options = get_option('ctlggi_cart_options');
			$id_order_history = $ctlggi_cart_options['order_history_page']; 
			if ( $id_order_history != '0' ) {
			  $title = esc_attr( get_the_title( $ctlggi_cart_options['order_history_page'] ) ); 
			  $url   = esc_attr( get_permalink( $id_order_history ) ); //  post slug
			}
			// navigation
			$nav_buttons  = ' <a class="btn-cataloggi btn-cataloggi-sm btn-cataloggi-blue" href="' . $url . '">' . __( 'Order History', 'cataloggi' ) . ' </a> '; 
			echo $nav_buttons = apply_filters( 'ctlggi_slm_display_account_nav_buttons', $nav_buttons  );
			
		?>
        
        <div class="cataloggi-boxes">
        
        <div class="cataloggi-boxes-title font-size-16"><?php _e( 'Order History', 'cataloggi' ); ?></div>
                
        <!-- table-responsive start -->
        <div class="cw-table-responsive">
        
        <table id="cwtable">
        
        <thead>
          <tr>
            <th class="cataloggi-uppercase"><?php _e( 'ID', 'cataloggi' ); ?></th>
            <th class="cataloggi-uppercase"><?php _e( 'Date', 'cataloggi' ); ?></th>
            <th class="cataloggi-uppercase"><?php _e( 'Status', 'cataloggi' ); ?></th>
            <th class="cataloggi-uppercase"><?php _e( 'Total', 'cataloggi' ); ?></th>
            <th class="cataloggi-uppercase"><?php _e( 'Details', 'cataloggi' ); ?></th>
            </tr>
        </thead>
        
        <tbody>
        <?php
			
			// License item ids
			foreach( $order_post_ids as $order_post_id )
			{	
			
				//echo $order_post_id . '<br>';
				// Order Items
				// get data from 'ctlggi_order_items' where order_id = $order_post_id 
				
		 ?>			
          <tr>
            <td><?php echo '#' . $order_post_id; ?></td>
            <td>
            <?php 
            $order_date = get_post_meta( $order_post_id, '_order_date', true );
			$order_date = CTLGGI_Helper::formatDate( $date=$order_date );
            echo esc_attr( $order_date );
            ?>
            </td>
            <td>
            <?php 
            $order_status = get_post_meta( $order_post_id, '_order_status', true );
			$statuses = CTLGGI_Custom_Post_Statuses::ctlggi_order_custom_post_statuses();
			if ( ! empty( $statuses[$order_status] ) ) {
				$status = $statuses[$order_status];
				echo esc_attr( $status );
			} else {
				_e( 'None', 'cataloggi' );
			}
            //echo $order_status;
            ?>
            </td>
            <td>
            <?php 
            $order_currency = get_post_meta( $order_post_id, '_order_currency', true ); 
            $order_total = get_post_meta( $order_post_id, '_order_total', true );
			// get the currency symbol
	        $order_currency_symbol = CTLGGI_Amount::ctlggi_get_currency_data_symbol( $currency=$order_currency );
			echo CTLGGI_Amount::ctlggi_orders_currency_symbol_position($amount=$order_total, $order_currency_symbol); // return span
            ?>
            </td>
            <td>
            <a href="<?php echo esc_url( the_permalink() . '?view-order=' . $order_post_id ); ?>"><?php _e( 'View', 'cataloggi' ); ?></a>
            </td>
          </tr> 
		<?php		
			} // end foreach
		?>
        </tbody>
        
        </table>
        
        
        </div>
        <!-- table-responsive end -->
        
        </div><!--/ cataloggi-boxes -->	
		<?php
		
		} // end if results
		else {
			echo '<p>' . __( "You haven't got any orders yet", 'cataloggi' ) . '</p>';
		}

?>