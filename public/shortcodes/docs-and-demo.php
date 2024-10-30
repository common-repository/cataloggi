<?php 

// the_content() should be inside the loop
if ( have_posts() ) : while ( have_posts() ) : the_post();

	$post_id = get_the_ID();
	
	$demo_url_checkbox  = get_post_meta( $post_id, '_ctlggi_demo_url_checkbox', true );
	$demo_url           = get_post_meta( $post_id, '_ctlggi_demo_url', true );
	
	$docs_checkbox      = get_post_meta( $post_id, '_ctlggi_docs_checkbox', true );
	$docs_url           = get_post_meta( $post_id, '_ctlggi_docs_url', true );
	
	if( empty( $demo_url_checkbox ) ) $demo_url_checkbox = '';
	if( empty( $demo_url ) ) $demo_url = '';
	if( empty( $docs_checkbox ) ) $docs_checkbox = '';
	if( empty( $docs_url ) ) $docs_url = '';
	
	if ( $demo_url == '' ) {
		$demo_url = home_url();
	}
	
	if ( $docs_url == '' ) {
		$docs_url = home_url();
	}
		
?>


<div class="data-box" style="padding-top:8px;">

<?php if ( $demo_url_checkbox == '1' ) { ?>
<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" class="btn btn-cataloggi-mdl btn-cataloggi-tweet-blue">
<i class="glyphicon glyphicon-new-window"></i>&nbsp; <?php  _e( 'Demo', 'cataloggi' ); ?></a>
<?php } ?>

<a href="<?php echo esc_url( $docs_url ); ?>" class="btn btn-cataloggi-mdl btn-cataloggi-orange-light">
<i class="glyphicon glyphicon-folder-open"></i>&nbsp; <?php  _e( 'Documentation', 'cataloggi' ); ?></a>

</div>

<?php 
	
endwhile;
endif;
?>