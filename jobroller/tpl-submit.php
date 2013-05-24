<?php
// Template Name: Submit Job Template


	### Prevent Caching
	nocache_headers();

	global $post, $posted;

	$submitID = $post->ID;
	
	$posted = array();
	$errors = new WP_Error();
	
	if ( ! is_user_logged_in() ) {
		$step = 1; 
	} else {
		$step = 2;
		if ( ! current_user_can('can_submit_job') ) 
			redirect_myjobs();
	};

	if ( isset( $_POST['job_submit'] ) && $_POST['job_submit'] ) {

		$result = jr_process_submit_job_form();
		
		$errors = $result['errors'];
		$posted = $result['posted'];
		
		if ( $errors && sizeof($errors) > 0 && $errors->get_error_code() ) $step = 2; else $step = 3;

	}
	elseif ( isset($_POST['preview_submit']) && $_POST['preview_submit'] ) {
		
		$step = 4;
		
		$posted = json_decode($_POST['posted']);
		
	}
	elseif ( isset($_POST['confirm']) && $_POST['confirm'] ) {
		
		$step = 4;
		
		jr_process_confirm_job_form();
		
	}
	elseif ( isset($_POST['goback'] ) && $_POST['goback']) {
		$posted = json_decode( stripslashes($_POST['posted']), true );
	}
?>
	<div class="section">

		<div class="section_content">

			<h1><?php _e('Submit a Job', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' );	?>

			<?php 
				echo '<ol class="steps">';
				for ($i = 1; $i <= 4; $i++) :
					echo '<li class="';
					if ($step==$i) echo 'current ';
					if (($step-1)==$i) echo 'previous ';
					if ($i<$step) echo 'done';
					echo '"><span class="';
					if ($i==1) echo 'first';
					if ($i==4) echo 'last';
					echo '">';
					switch ($i) :
						case 1 : _e('Create account', APP_TD); break;
						case 2 : _e('Enter Job Details', APP_TD); break;
						case 3 : _e('Preview/Job Options', APP_TD); break;
						case 4 : _e('Confirm', APP_TD); break;
					endswitch;
					echo '</span></li>';
				endfor;
				echo '</ol><div class="clear"></div>';
				
				switch ($step) :
					
					case 1 :
						jr_before_step_one(); // do_action hook
						?>
						<p><?php _e('You must login or create an account in order to post a job &mdash; this will enable you to view, remove, or relist your listing in the future.', APP_TD); ?></p>

						<div class="col-1">
							<?php do_action( 'jr_display_register_form', get_permalink( $submitID ), 'job_lister' );  ?>
						</div>
						<div class="col-2">
							<?php do_action( 'jr_display_login_form', get_permalink( $submitID ), get_permalink( $submitID ) ); ?>
						</div>
						<div class="clear"></div>
						<?php
						jr_after_step_one(); // do_action hook
						break;
					case 2 :
						jr_before_step_two(); // do_action hook
						jr_submit_job_form();
						jr_after_step_two(); // do_action hook	
						break;
					case 3 :
						jr_before_step_three(); // do_action hook
						jr_preview_job_form();
						jr_after_step_three(); // do_action hook
						break;
					case 4 :
						jr_before_step_four(); // do_action hook
						jr_confirm_job_form();
						jr_after_step_four(); // do_action hook
						break;
					
				endswitch;	
			?>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('submit'); ?>
