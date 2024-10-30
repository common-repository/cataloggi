
<!-- cw-form start -->
<div class="cw-form cw-form-maxwidth">

<!-- forgot-pw form -->
<div id="ctlggi-forgot-pw-form-holder" class="cataloggi-margin-top-15">

<!-- jQuery -->
<div class="show-forgot-pw-form-return-data"></div>

<div class="textlabel-forms-bold cataloggi-uppercase"><?php echo esc_attr( $atts['title'] ); ?></div>

<form id="ctlggi-forgot-pw-form" action="" enctype="multipart/form-data" method="post">

<input type="hidden" name="ctlggi-forgot-pw-form-nonce" value="<?php echo wp_create_nonce('ctlggi_forgot_pw_form_nonce'); ?>"/>

<fieldset>

<div class="r-row">

  <div class="c-col c-col-12">
    <label for="textinput"><?php _e( 'Enter your Username or E-mail address', 'cataloggi' ); ?></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input type="text" id="ctlggi_user_login" name="ctlggi_user_login" value="" required >
    </div>
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
        <button class="submit-button buttons submitbutton cataloggi-margin-top-bottom-15" type="submit" id="ctlggi-forgot-pw-form-submit" name="ctlggi-forgot-pw-form-submit">
          <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php _e('Get New Password', 'cataloggi'); ?>
        </button>
      </div>
    </div>
  </div>
</div>

</form>

</div><!--/ forgot-pw-form -->

</div>
<!-- cw-form end -->