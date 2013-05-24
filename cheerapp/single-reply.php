<?php

/**
 * Single Reply
 */

?>

<?php get_header(); ?>

<?php get_template_part( 'top', 'forum' ); ?>

<div id="main" class="forum">
	<div class="inner-wrap">
	
		<div class="content">

			<?php do_action( 'bbp_template_notices' ); ?>

			<?php while ( have_posts() ) : the_post(); ?>
			
				<h1 class="entry-title"><?php bbp_reply_title(); ?></h1>

				<article id="bbp-reply-wrapper-<?php bbp_reply_id(); ?>" class="bbp-reply-content-wrapper clearfix">

					<div class="bbp-reply-content box">

						<div class="bbp-post clearfix" id="post-<?php bbp_reply_id(); ?>">

							<div class="bbp-post-author-avatar">
							
								<?php bbp_reply_author_link( array( 'sep' => '', 'size' => 80, 'type' => 'avatar' ) ); ?>
								
								<?php if ( is_super_admin() ) : ?>
									<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>
								
									<div class="bbp-reply-ip visible-desktop"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>
									
									<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>
								<?php endif; ?>
								
							</div><!-- end .bbp-post-author-avatar -->

							<div class="bbp-post-content">

								<div class="bbp-post-info clearfix">
							
									<div class="bbp-post-author">
									
										<?php bbp_reply_author_link( array( 'sep' => '', 'type' => 'name' ) ); ?>
										
									</div>
									
									<div class="bbp-post-date">
										<?php printf( __( '%1$s <small>at</small> %2$s', 'cheerapp' ), get_the_date(), esc_attr( get_the_time() ) ); ?>
										&nbsp;/&nbsp;
										<a href="<?php bbp_reply_url(); ?>" title="<?php _e( 'Permalink', 'cheerapp' ); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>
									</div>
								
								</div><!-- end .bbp-post-info -->

								<?php bbp_reply_content(); ?>
								
								<?php
								$args = array(
									'before'		=>	'',
									'after'			=>	'',
									'sep'			=>	''
								);
								$args['links'] = array(
									'edit'	=>	royal_bbp_get_reply_edit_link ( $args ),
									'split'	=>	royal_bbp_get_topic_split_link( $args ),
									'trash'	=>	royal_bbp_get_reply_trash_link( $args ),
									'spam'	=>	royal_bbp_get_reply_spam_link ( $args )
								);
								$args['before'] = '<div class="forum-post-admin-links clearfix">';
								$args['after'] = '</div>';
								bbp_reply_admin_links( $args );
								?>

							</div><!-- end .post-content -->
							
						</div><!-- end .bbp-post -->

					</div><!-- .bbp-reply-content -->
					
				</article><!-- #bbp-reply-wrapper-<?php bbp_reply_id(); ?> -->
				
				<?php bbp_get_template_part( 'bbpress/sidebar', 'single-reply' ); ?>

			<?php endwhile; ?>

		</div><!-- end .content -->

	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>