
        <!-- payment button -->
        <div class="ctlggi-payment-buttons-wrapper">       
            <form class="ctlggi-payment-buttons-form-class" action="" method="post" id="ctlggi-payment-buttons-form">
              <input type="hidden" name="ctlggi-global-nonce-for-payment-forms" value="<?php echo wp_create_nonce('ctlggi_global_nonce_for_payment_forms'); ?>"/>
              <input type="hidden" name="ctlggi_item_id" id="ctlggi_item_id" value="<?php echo esc_attr( $postid ); ?>"/>
              <input type="hidden" name="ctlggi_item_price" value="<?php echo esc_attr( $item_sale_price_hidden ); ?>"/>
              <input type="hidden" name="ctlggi_item_name" value="<?php echo esc_attr( get_the_title( $postid ) ); ?>"/>
              <input type="hidden" name="ctlggi_item_downloadable" value="<?php echo esc_attr( $item_downloadable ); ?>"/>
            
                <div class="ctlggi-payment-button-1" id="ctlggi-payment-button-1">
                
                    <div class="ctlggi-item-quantity-input"> 
                        <input type="number"  max="" min="1" value="1" id="ctlggi_item_quantity" name="ctlggi_item_quantity"> 
                    </div> 
                    
                    <div class="ctlggi-payment-button-1-submit"> 
                    <button type="submit" class="btn-cataloggi btn-cataloggi-md btn-cataloggi-orange" > 
                    <?php echo ' ' . $item_sale_price_public . ' '; esc_attr_e('- Add to Cart', 'cataloggi'); ?>
                    </button>
                    </div>
                    
                </div>
                
                <div class="ctlggi-payment-button-2" id="ctlggi-payment-button-2">
                    <a href="<?php echo esc_url( $cataloggiurl . '?page=cart' ); ?>">
                    <button type="button" class="btn-cataloggi btn-cataloggi-md btn-cataloggi-green" > 
                    <?php esc_attr_e('+ View Cart', 'cataloggi'); ?> 
                    </button>
                    </a>
                </div>
               
               <!--
                <div class="ctlggi-item-variable-prices">
                    <label>
                      <select id="ctlggi_item_price_AAAAAAAAAAA" name="ctlggi_item_price_AAAAAAAAAAA" required>
                        <option value="01">Single Site - $29.00</option>
                        <option value="02">2-5 Sites - $49.00</option>
                        <option value="02">6-10 Sites - $79.00</option>
                        <option value="03">Unlimited Sites - $129.00</option>
                      </select>
                    </label>
                </div>
                --> 
            
            </form>
        </div><!--/ payment button -->