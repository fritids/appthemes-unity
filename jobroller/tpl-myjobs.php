<?php
/*
Template Name: My Jobs Template
*/
?>

<?php
### Prevent Caching
nocache_headers();

appthemes_auth_redirect_login();
if (!current_user_can('can_submit_job')) redirect_profile();

do_action('jr_lister_dashboard_process');

$myjobsID = $post->ID;

global $userdata, $user_ID, $message;
?>
	<div class="section myjobs">
		
		<div class="section_content">
		
		<?php if (isset($_GET['remove_listing']) && is_numeric($_GET['remove_listing'])) : ?>
			
			<?php
			
				if (isset($_GET['confirm'])) :

					$post_id = $_GET['remove_listing'];
					$post_to_remove = get_post($post_id);

					if ($post_to_remove->ID==$post_id && $post_to_remove->post_author==$user_ID) :
						$job_post = array();
						$job_post['ID'] = $post_id;
						$job_post['post_status'] = 'private';
						wp_update_post( $job_post );
						$message = __('Job listing was ended early.',APP_TD);
					else :
						header('Location: '.get_permalink($myjobsID));
						exit;
					endif;

				else :

					$post_id = $_GET['remove_listing'];
					$post_to_remove = get_post($post_id);

					global $user_ID;

					if ($post_to_remove->ID==$post_id && $post_to_remove->post_author==$user_ID && $post_to_remove->post_status=='publish') :
							$message = __('Are you sure you want to end ',APP_TD);
							$message .= '&ldquo;'.$post_to_remove->post_title.'&rdquo; [<a href="'.trailingslashit(get_permalink($myjobsID)).'?remove_listing='.$post_to_remove->ID.'&amp;confirm=true">'.__('Yes',APP_TD).'</a>] [<a href="'.trailingslashit(get_permalink($myjobsID)).'">'.__('No',APP_TD).'</a>]';
					else :
							header('Location: '.trailingslashit(get_permalink($myjobsID)));
							exit;
					endif;

				endif;
			?>
			
		<?php elseif(isset($_GET['pay_for_listing']) && is_numeric($_GET['pay_for_listing'])) : 
			
			global $user_ID;
			
			$post_id = $_GET['pay_for_listing'];
			
			$jr_order = new jr_order();
			
			if ($jr_order->find_order_for_job($post_id)) :

				if ($jr_order->status=='pending_payment' && $jr_order->user_id==$user_ID) :
					
					$job_post = get_post($jr_order->job_id); 
					header('Location: '.$jr_order->generate_paypal_link());
					exit;
				
				endif;
				
			endif;
			
		endif; ?>
		
		<h1><?php printf(__("%s's Dashboard", APP_TD), ucwords($userdata->user_login)); ?></h1>
		
		<?php
			do_action( 'appthemes_notices' );

			$sizeof_jobpacks = sizeof(jr_get_job_packs());

			// check job packs for temporary Resumes access
			$pack = jr_get_user_job_packs_access( $user_ID );

			$resume_temp_access='';
			if (!empty($pack)):
				$resume_temp_access  = in_array('resume_browse', $pack['access']) ? __('Browse',APP_TD) : '';
				$resume_temp_access .= in_array('resume_view', $pack['access']) ? ($resume_temp_access?__(' and ', APP_TD):'') . __('View',APP_TD) : '';			
			endif;
		?>
		
		<ul class="display_section">
			<li><a href="#live" class="noscroll"><?php _e('Live', APP_TD); ?></a></li>
			<li><a href="#pending" class="noscroll"><?php _e('Pending', APP_TD); ?></a></li>
			<li><a href="#ended" class="noscroll"><?php _e('Ended/Expired', APP_TD); ?></a></li>
			<?php if ( $sizeof_jobpacks > 0 ) : ?><li><a href="#packs" class="noscroll"><?php _e('Job Packs', APP_TD); ?></a></li><?php endif; ?>
			<?php if ( jr_resume_is_active_manual_subscr() || $resume_temp_access ) : ?><li><a href="#subscriptions" class="noscroll"><?php _e('Subscriptions', APP_TD); ?></a></li><?php endif; ?>
		</ul>
		
		<div id="live" class="myjobs_section">
		
			<h2><?php _e('Live Jobs', APP_TD); ?></h2>
				
				<p><?php _e('Below you will find a list of jobs you have previously posted which are visible on the site.', APP_TD); ?></p>
		
				<table cellpadding="0" cellspacing="0" class="data_list">
					<thead>
						<tr>
							<th><?php _e('Job Title',APP_TD); ?></th>
							<th class="center"><?php _e('Date Posted',APP_TD); ?></th>
							<th class="center"><?php _e('Days Remaining',APP_TD); ?></th>
							<th class="center"><?php _e('Views',APP_TD); ?></th>
							<th class="right"><?php _e('Actions',APP_TD); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							global $user_ID;
							$args = array(
									'ignore_sticky_posts'	=> 1,
									'posts_per_page' => -1,
									'author' => $user_ID,
									'post_type' => 'job_listing',
									'post_status' => 'publish'
							);
							$my_query = new WP_Query($args);
							$count = 0;
						?>
						<?php if ($my_query->have_posts()) : ?>
						
							<?php while ($my_query->have_posts()) : ?>
							
								<?php $my_query->the_post(); ?>

								<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>
								
						
								<?php if (jr_check_expired($post)) continue; ?>

								<tr>
									<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></td>
									<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
									<td class="center days"><?php echo jr_remaining_days($my_query->post); ?></td>
									<td class="center"><?php echo $job_views; ?></td>
									<td class="actions"><?php if (get_option('jr_allow_editing')=='yes') : ?><a href="<?php echo add_query_arg('edit', $my_query->post->ID, get_permalink(get_option('jr_edit_job_page_id'))); ?>"><?php _e('Edit&nbsp;&rarr;',APP_TD); ?></a>&nbsp;<?php endif; ?><a href="<?php echo add_query_arg('remove_listing', $my_query->post->ID, get_permalink($myjobsID)); ?>" class="delete"><?php _e('End',APP_TD); ?></a></td>
								</tr>
								<?php 
								$count++; 
							endwhile;
							if ($count==0) : ?>
								<tr>
									<td colspan="4"><?php _e('No live jobs found.',APP_TD); ?></td>
								</tr>
							<?php endif;
						endif; 
						
						?>
					</tbody>
				</table>
				
			</div>
			
			<?php if ( $sizeof_jobpacks > 0 ) : ?>
			<div id="packs" class="myjobs_section">
			
				<?php				
					jr_job_pack_select('dashboard', array('user'));
												
					if ( $enable_buy = get_option('jr_packs_dashboard_buy') == 'yes' ):
						//display job pack selection form
						jr_lister_packs_form();
					endif;
				?>
			
			</div>
			<?php endif; ?>
			
			<div id="pending" class="myjobs_section">
			
				<h2><?php _e('Pending Jobs', APP_TD); ?></h2>
				
				<?php
					global $user_ID;
					$args = array(
						'ignore_sticky_posts'	=> 1,
						'posts_per_page' => -1,
						'author' => $user_ID,
						'post_type' => 'job_listing',
						'post_status' => 'pending'
					);
					$my_query = new WP_Query($args);
				?>
				<?php if ($my_query->have_posts()) : ?>

				<p><?php _e('The following jobs are pending and are not visible to users.', APP_TD); ?></p>
				
				<table cellpadding="0" cellspacing="0" class="data_list">
					<thead>
						<tr>
							<th><?php _e('Job Title',APP_TD); ?></th>
							<th class="center"><?php _e('Date Posted',APP_TD); ?></th>
							<th class="center"><?php _e('Status',APP_TD); ?></th>
							<th class="right"><?php _e('Actions',APP_TD); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
							<tr>
								<td>
								<?php
									// only users with 'edit_posts' capability can preview pending posts
									if ( current_user_can( 'edit_posts', $post->ID ) ) { ?>

										<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>

								<?php } else { ?>

										<strong><?php the_title(); ?></strong>

								<?php } ?>
								</td>
								<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
								<td class="center"><?php 
									$jr_order = new jr_order();
			
									if ($jr_order->find_order_for_job($my_query->post->ID)) :
										if ($jr_order->status!='completed') :
											echo __('Pending Payment', APP_TD);
										else :
											echo __('Pending Approval', APP_TD);
										endif;
									else :
										echo __('Pending', APP_TD);
									endif;				
								?></td>
								<td class="actions">
									<?php 
										if ($jr_order->status && $jr_order->status!='completed') : ?><a href="<?php echo trailingslashit(get_permalink($myjobsID)); ?>?pay_for_listing=<?php echo $my_query->post->ID; ?>"><?php _e('Pay&nbsp;&rarr;',APP_TD); ?></a>&nbsp;<?php 
										endif; 
										if (get_option('jr_allow_editing')=='yes') :
											?><a href="<?php echo trailingslashit(get_permalink(get_option('jr_edit_job_page_id'))); ?>?edit=<?php echo $my_query->post->ID; ?>"><?php _e('Edit&nbsp;&rarr;',APP_TD); ?></a>&nbsp;<?php
										endif;
										?><a href="<?php echo trailingslashit(get_permalink($myjobsID)); ?>?remove_listing=<?php echo $my_query->post->ID; ?>" class="delete"><?php _e('Cancel',APP_TD); ?></a>
								</td>
							</tr>
						<?php endwhile; ?>				
					</tbody>
				</table>
				<?php else : ?>
					<p><?php _e('No pending jobs found.', APP_TD); ?></p>
				<?php endif; ?>
			</div>
			
			<div id="ended" class="myjobs_section">
			
				<?php
					global $user_ID;
					$args = array(
						'ignore_sticky_posts'	=> 1,
						'posts_per_page' => -1,
						'author' => $user_ID,
						'post_type' => 'job_listing',
						'post_status' => 'private'
					);
					$my_query = new WP_Query($args);
					$args = array(
							'ignore_sticky_posts'	=> 1,
							'posts_per_page' => -1,
							'author' => $user_ID,
							'post_type' => 'job_listing',
							'post_status' => 'publish'
					);
					$my_query2 = new WP_Query($args);
					$count = 0;
				?>
				<?php if ($my_query->have_posts() || $my_query2->have_posts()) : ?>
				<h2><?php _e('Ended/Expired Jobs', APP_TD); ?></h2>
				
				<p><?php _e('The following jobs have expired or have been ended and are not visible to users.', APP_TD); ?></p>
				
				<table cellpadding="0" cellspacing="0" class="data_list">
					<thead>
						<tr>
							<th><?php _e('Job Title',APP_TD); ?></th>
							<th class="center"><?php _e('Date Posted',APP_TD); ?></th>
							<th class="center"><?php _e('Status',APP_TD); ?></th>
							<th class="center"><?php _e('Views',APP_TD); ?></th>
							<th class="right"><?php _e('Actions',APP_TD); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ($my_query->have_posts()) while ($my_query->have_posts()) : $my_query->the_post(); ?>

						<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

							<tr>
								<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></td>
								<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
								<td class="center"><?php 
									$jr_order = new jr_order();
			
									if ($jr_order->find_order_for_job($my_query->post->ID)) :
										if ($jr_order->status!='completed') :
											echo __('Ended (order incomplete)', APP_TD);
										else :
											echo __('Ended', APP_TD);
										endif;
									else :
										echo __('Ended', APP_TD);
									endif;				
								?></td>
								<td class="center"><?php echo $job_views; ?></td>
								<td class="actions">
									<?php if (get_option('jr_allow_relist')=='yes') : ?><a href="<?php echo trailingslashit(get_permalink(get_option('jr_edit_job_page_id'))); ?>?edit=<?php echo $my_query->post->ID; ?>&amp;relist=true"><?php _e('Relist&nbsp;&rarr;',APP_TD); ?></a><?php endif; ?>
								</td>
							</tr>
						<?php $count++; endwhile; ?>
						<?php if ($my_query2->have_posts()) while ($my_query2->have_posts()) : $my_query2->the_post(); if (!jr_check_expired($post)) continue; ?>
							<tr>
								<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></td>
								<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
								<td class="center"><?php _e('Expired', APP_TD); ?></td>
								<td class="center"><?php echo $job_views; ?></td>
								<td class="actions">
									<?php if (get_option('jr_allow_relist')=='yes') : ?><a href="<?php echo trailingslashit(get_permalink(get_option('jr_edit_job_page_id'))); ?>?edit=<?php echo $my_query2->post->ID; ?>&amp;relist=true"><?php _e('Relist&nbsp;&rarr;',APP_TD); ?></a><?php endif; ?>
								</td>
							</tr>
						<?php $count++; endwhile;
						
						if ($count==0) : ?>
								<tr>
									<td colspan="4"><?php _e('No jobs found.',APP_TD); ?></td>
								</tr>
						<?php endif; ?>			
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			
			<div id="subscriptions" class="myjobs_section">
				<h2><?php _e('Resume Subscriptions ', APP_TD); ?></h2>

					<?php if (jr_resume_is_active_manual_subscr()) :
						$valid_subscription = (get_user_meta( $user_ID, '_valid_resume_subscription', true ) == '1');
						$valid_trial =  (get_user_meta( $user_ID, '_valid_resume_trial', true ) == '1');
						$valid_subscr_date = get_user_meta( $user_ID, '_valid_resume_subscription_end', true );
						$active_subscription = ($valid_subscription && $valid_subscr_date);
					?>

					<?php if ($active_subscription): ?>
						<p><?php echo sprintf (__('Your Resume <em>%s</em> ends <strong>%s</strong>.',APP_TD), ($valid_trial?'Trial':'Subscription'), date_i18n(__('F d, Y @ g:i:s a',APP_TD), $valid_subscr_date)); ?></p>
					<?php else: ?>
						<p><?php echo sprintf(__('No active Resume subscriptions. <a href=\'%s\'>Subscribe</a>. ',APP_TD),get_post_type_archive_link('resume')) ?></p>
					<?php endif;?>

				<?php endif; ?>

				<?php if ($resume_temp_access) { ?>

					<p><?php echo sprintf( __('Your purchased Job Pack(s) give you temporary access to <strong>%s</strong> Resumes. ***',APP_TD), $resume_temp_access ); ?></p>
					<p class="temp_subsc_info"><em><?php echo __('*** Access is granted for the duration of the Pack (limited duration Packs) or while the Pack is active (unlimited duration Packs).',APP_TD); ?></em></p>

				<?php } ?>
			</div>

			<script type="text/javascript">
				/* <![CDATA[ */				
					jQuery(function() {
												
						jQuery('a.delete').click(function(){
							var answer = confirm("<?php _e('Are you sure you want to end this job listing? This action cannot be undone.', APP_TD); ?>")
							if (answer){
								jQuery(this).attr('href', jQuery(this).attr('href') + '&confirm=true');
								return true;
							}
							else{
								return false;
							}					
						});	
						
						jQuery('.myjobs ul.display_section li a').click(function(){
							
							jQuery('.myjobs div.myjobs_section').hide();
							
							jQuery(jQuery(this).attr('href')).show();
							
							jQuery('.myjobs ul.display_section li').removeClass('active');
							
							jQuery(this).parent().addClass('active');
							
							return false;
						});
						jQuery('.myjobs ul.display_section li a:eq(0)').click();						
						
						// trigger the selected tab
						<?php if ( isset($_GET['tab']) ): ?>
								jQuery('.myjobs ul.display_section li a[href="#<?php echo $_GET['tab']; ?>"]').trigger('click');
						<?php endif; ?>

					});
				/* ]]> */
			</script>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('user'); ?>
