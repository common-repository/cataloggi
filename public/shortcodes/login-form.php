
<!-- cw-form start -->
<div class="cw-form cw-form-maxwidth">

<!-- login form -->
<div id="ctlggi-login-form-holder" class="cataloggi-margin-top-15">

<!-- jQuery -->
<div class="show-login-form-return-data"></div>

<div class="textlabel-forms-bold cataloggi-uppercase"><?php echo esc_attr( $atts['title'] ); ?></div>

<form id="ctlggi-login-form" action="" enctype="multipart/form-data" method="post">

<input type="hidden" name="ctlggi-login-form-nonce" value="<?php echo wp_create_nonce('ctlggi_login_form_nonce'); ?>"/> 

<fieldset>

<div class="r-row">

  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Username', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_username" name="ctlggi_username" value="" required >
    </div>
  </div>
  
  <div class="c-col c-col-6">
    <label for="textinput"><?php _e( 'Password', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-lock"></i>
      <input type="password" id="ctlggi_password" name="ctlggi_password"value="" required >
    </div>
  </div>
  
</div>

<div class="r-row">

      <div class="c-col c-col-12">
        <label>
            <input type="checkbox" id="ctlggi_remember" name="ctlggi_remember" value="1" >
            <span class="lbl padding-8"><?php _e( 'Remember me', 'cataloggi' ); ?></span>
        </label>
        
      </div>
  
</div>

</fieldset>

<div class="cw-footer">
  <div class="formsubmit">
    <div class="r-row">
      <div class="c-col c-col-6"> 
         &nbsp;<div class="loading-img"></div>
         </div>
       <div class="c-col c-col-6"> 
        <button class="submit-button buttons submitbutton cataloggi-margin-top-bottom-15" type="submit" id="ctlggi-login-form-submit" name="ctlggi-login-form-submit">
          <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php _e('Login', 'cataloggi'); ?>
        </button>
      </div>
    </div>
  </div>
</div>

</form>

</div><!--/ login-form -->

</div>
<!-- cw-form end -->