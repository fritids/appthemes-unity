<?php 
// Template Name: Share Coupon Template


global $posted, $user_ID, $app_abbr;
$posted = array();
$errors = new WP_Error();	
?>


<?php
// call tinymce init code if html is enabled
if (get_option('clpr_allow_html') == 'yes')
		clpr_tinymce(420, 300);
?>

<div id="content">

	<div class="content-box">
	
		<div class="box-t">&nbsp;</div>
		
		<div class="box-c">
		
			<div class="box-holder">	
			
			
				<?php

				// check and make sure the form was submitted from step1
				if(isset($_POST['submitted'])) {

					include_once(TEMPLATEPATH . '/includes/forms/submit-coupon/submit-coupon-process.php');

				} else {

					include_once(TEMPLATEPATH . '/includes/forms/submit-coupon/submit-coupon-form.php');

				}
				?>   
				
				
			</div>
			
		</div>
		
		<div class="box-b">&nbsp;</div>
		
	</div>
	
</div>

<?php get_sidebar('submit'); ?>

