


<div class="padding-left-right-15">

<!-- table-responsive start -->
<div class="cw-table-responsive cw-admin-forms"> 

<table id="cwtable">

<tbody>

  <tr>
    <td>
    <p><?php _e("First Name", 'cataloggi'); ?></p>
    <input class="inputfield" id="_first_name" name="_first_name" type="text" value="<?php echo esc_attr__( $first_name ); ?>">
    </td>
    <td>
    <p><?php _e("Last Name", 'cataloggi'); ?></p>
    <input class="inputfield" id="_last_name" name="_last_name" type="text" value="<?php echo esc_attr__( $last_name ); ?>">
    </td>
    <td>
    <p><?php _e("Email (Used for Mailing)", 'cataloggi'); ?></p>
	<?php 
	if( ! empty( $email ) ) {
		$email = $email;
	} else {	
		if ( ! empty($cus_user_id) ) {
			$user_customer = get_user_by( 'id', $cus_user_id );
			$email = $user_customer->user_email;
		} else {
			$email = '';
		}
	}
	?>
    <input class="inputfield" id="_email" name="_email" type="email" value="<?php echo esc_attr__( $email ); ?>">
    </td>
  </tr>   
  
  <tr>
    <td>
    <p><?php _e("Phone", 'cataloggi'); ?></p>
    <input class="inputfield" id="_phone" name="_phone" type="text" value="<?php echo esc_attr__( $phone ); ?>">
    </td>
    <td>
    <p><?php _e("Company", 'cataloggi'); ?></p>
    <input class="inputfield" id="_company" name="_company" type="text" value="<?php echo esc_attr__( $company ); ?>">
    </td>
    <td>
    
    </td>
  </tr>   
  
  <tr>
    <td>
    <p><?php _e("Address 1", 'cataloggi'); ?></p>
    <input class="inputfield" id="_billing_addr_1" name="_billing_addr_1" type="text" value="<?php echo esc_attr__( $billing_addr_1 ); ?>">
    </td>
    <td>
    <p><?php _e("Country", 'cataloggi'); ?></p>
    <select class="selectfield" id="_billing_country" name="_billing_country" >
    <?php 
	
	if ( empty($billing_country) ) {
	   echo '<option selected="selected" value=""> </option>';
	} else {
	  // Countries
	  $countries = CTLGGI_Countries::ctlggi_country_list(); // function
	  $country_name = $countries[$billing_country]; // get country name
	  echo '<option selected="selected" value="' . esc_attr( $billing_country ) . '">' . esc_attr( $country_name ) . '</option>';
	}
	  // Countries
	  $countries = CTLGGI_Countries::ctlggi_country_list(); // function
	  foreach($countries as $countrycode => $country)
	  {
		  
		$states = CTLGGI_Countries::ctlggi_get_states( $countrycode ); // function
		
		// if returns empty country do not have states
		if (empty($states)) {
			$dropdisplay = '0';
		} else {
			$dropdisplay = '1';
		}
		
		echo '<option data-billing-country-code="' . esc_attr( $countrycode ) . '" data-billing-state-drop-display="' . esc_attr( $dropdisplay ) . '" value="' . esc_attr( $countrycode ) . '">' . esc_attr( $country ) . '</option>';  

	  }
         
    ?>
        
    </select>
    </td>
    <td>
    <p><?php _e("State", 'cataloggi'); ?></p>
	<?php 
    if ( !empty($billing_state) ) {
		// convert accented chars to html
		$state_name = htmlentities($billing_state, 0, "UTF-8");
		if ($state_name == "") {
			$state_name = htmlentities(utf8_encode($state_name), 0, "UTF-8");
		}
    } else {
        $state_name = '';
    }
    ?>
    
   <div id="ctlggi-billing-country-state-field">
     <input class="inputfield" id="_billing_state" name="_billing_state" type="text" value="<?php echo esc_attr__( $state_name ); ?>">
   </div>   
    
   <div id="ctlggi-billing-country-state-dropdown" style="display:none;" > <!-- style="display:none;" -->
    
	<?php 
        // style="width: 240px;"
        echo '<select class="selectfield" id="_billing_state" name="_billing_state" disabled="disabled" >'; // required
        echo '<option value="" selected="selected">' . __('Please Select ...', 'cataloggi') . '</option>';
                
          // Countries
          $getcountries = CTLGGI_Countries::ctlggi_country_list(); // function
          foreach($getcountries as $getcountrycode => $getcountry)
          {
            
            if ( $getcountrycode ) {
                
                $getstates = CTLGGI_Countries::ctlggi_get_states( $getcountrycode ); // function
                
                  foreach($getstates as $key => $value)
                  {
                     // convert accented chars to html
                    $output = htmlentities($value, 0, "UTF-8");
                    if ($output == "") {
                        $output = htmlentities(utf8_encode($value), 0, "UTF-8");
                    }
                    //$trusted_value = CTLGGI_Countries::ctlggi_replace_accents($value);
                    echo '<option id="ctlggi_billing_states_' . esc_attr( $getcountrycode ) . '" value="' . esc_attr($output) . '">' . esc_attr($output) . '</option>'; 
                  }
                  
            } 
            
    
          }
          
        echo '</select>';
    
    ?>
  
   </div> 

    </td>
  </tr>  
  
  <tr>
    <td>
    <p><?php _e("Address 2", 'cataloggi'); ?></p>
    <input class="inputfield" id="_billing_addr_2" name="_billing_addr_2" type="text" value="<?php echo esc_attr__( $billing_addr_2 ); ?>">

    
    </td>
    <td>
    <p><?php _e("City", 'cataloggi'); ?></p>
    <input class="inputfield" id="_billing_city" name="_billing_city" type="text" value="<?php echo esc_attr__( $billing_city ); ?>">
    </td>
    <td>
    <p><?php _e("Postcode/Zip", 'cataloggi'); ?></p>
    <input class="inputfield" id="_billing_zip" name="_billing_zip" type="text" value="<?php echo esc_attr__( $billing_zip ); ?>">
    </td>
  </tr> 
  
</tbody>

</table>


</div>
<!-- table-responsive end -->

</div><!--/ padding-left-right-15 -->

