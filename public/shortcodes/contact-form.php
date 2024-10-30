
<!-- cw-form start -->
<div class="cw-form cw-form-maxwidth">

<!-- jQuery -->
<div class="show-contact-form-return-data"></div>

<!-- contact form -->
<div id="ctlggi-contact-form-holder" class="cataloggi-margin-top-15">

<div class="textlabel-forms-normal"><?php echo esc_attr( $atts['title'] ); ?></div>

<form id="ctlggi-contact-form" action="" enctype="multipart/form-data" method="post">

<input type="hidden" name="ctlggi-contact-form-nonce" value="<?php echo wp_create_nonce('ctlggi_contact_form_nonce'); ?>"/> 

<fieldset>

<div class="r-row">

  <div class="c-col c-col-5">
    <label for="ctlggi_firstname"><?php _e('First Name', 'cataloggi'); ?> <span class="reqmark">*</span></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input id="ctlggi_firstname" name="ctlggi_firstname" type="text" value="" required>
    </div>
  </div>
  
  <div class="c-col c-col-5">
    <label for="ctlggi_lastname"><?php _e('Last Name', 'cataloggi'); ?> <span class="reqmark">*</span></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-user"></i>
      <input id="ctlggi_lastname" name="ctlggi_lastname" type="text" value="" required>
    </div>
  </div>

</div>

<div class="r-row">

  <div class="c-col c-col-5">
    <label for="ctlggi_email"><?php _e('E-mail', 'cataloggi'); ?> <span class="reqmark">*</span></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-envelope"></i>
      <input id="ctlggi_email" name="ctlggi_email" type="email" value="" required>
    </div>
  </div>
  
    <div class="c-col c-col-5">
    <label for="ctlggi_telephone"><?php _e('Telephone', 'cataloggi'); ?> </label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-phone"></i>
      <input id="ctlggi_telephone" name="ctlggi_telephone" type="text" value="">
    </div>
    </div>

</div>

<div class="r-row">

  <div class="c-col c-col-11">
    <label for="ctlggi_subject"><?php _e('Subject', 'cataloggi'); ?> <span class="reqmark">*</span></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-tag"></i>
      <input id="ctlggi_subject" name="ctlggi_subject" type="text" value="" required>
    </div>
  </div>
  
</div>

<div class="r-row">

  <div class="c-col c-col-12">
    <label for="ctlggi_message"><?php _e('Message', 'cataloggi'); ?> <span class="reqmark">*</span></label>
    <div class="inner-addon left-addon">
       <i class="glyphicon glyphicon-comment"></i>
      <textarea id="ctlggi_message" name="ctlggi_message" spellcheck="true" rows="10" placeholder="" required ></textarea>
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
        <button class="submit-button buttons submitbutton cataloggi-margin-top-bottom-15" type="submit" id="ctlggi-contact-form-submit" name="ctlggi-contact-form-submit">
          <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php _e('Send', 'cataloggi'); ?>
        </button>
      </div>
    </div>
  </div>
</div>

</form>

</div><!--/ contact-form -->

</div>
<!-- cw-form end -->