<?php get_header(); ?>
<?php get_template_part( 'top', 'knowledgebase' ); ?>

<div id="main" class="wiki">
	<div class="inner-wrap">
	
		<div class="row">
		
			<div class="content span8 content-right">
			
				<?php			
				if( have_posts() ) : while( have_posts() ) : the_post();
				?>
				
					<?php
					$meta			=	get_post_meta( $post->ID, '_royal_meta', true );
					
					$date_format	=	get_option( 'date_format' );
					$article_date	=	get_the_modified_time( $date_format );
					$article_author	=	get_the_author();
					
					$faq_group		=	!empty( $meta['faq_group'] ) ? $meta['faq_group'] : null;
					$faq_posts		=	!empty( $faq_group ) ? get_posts( array( 'post_type' => 'faq', 'faq_category' => $faq_group, 'showposts' => -1 ) ) : null;
					$faq_count		=	!empty( $faq_posts ) ? count( $faq_posts ) : 0;
					?>
			
					<article class="post post-wiki clearfix">
					
						<h1> <?php the_title(); ?> </h1>
						
						<div class="wiki-post-meta">
						
							<span class="post-meta">
								<?php printf( __( '<span><small>last updated on</small> %1$s</span> <span><small>by</small> %2$s</span>', 'cheerapp' ), $article_date, $article_author ); ?>
							</span>
							
							<a class="faq-count tooltip" href="<?php the_permalink(); ?>#faq" title="<?php printf( _n( '%d frequently asked question answered', '%d frequently asked questions answered', $faq_count, 'cheerapp' ), $faq_count ); ?>"><?php echo $faq_count; ?></a>
							<?php if( function_exists( 'royal_likes' ) ) royal_likes(); ?>
							
						</div><!-- end .archive-post-meta -->
						
						<div class="post-content">
						
							<?php the_content(); ?>
							
						</div><!-- end .post-content -->
						
						<?php
						if( $faq_posts ) {
						?>
						
							<hr />
							
							<div>
								<h5><?php _e( 'Frequently Asked Questions', 'cheerapp' ); ?></h5>
							
								<ul id="faq" class="faq">
								
									<?php
									foreach( $faq_posts as $faq ) {
									?>
										
										<li>
											<a href="#"><?php echo $faq->post_title; ?></a>
											<div class="target">
												<?php echo $faq->post_content; ?>
											</div><!-- end .target -->
										</li>
										
									<?php } ?>
								
								</ul><!-- end #faq -->
							</div>
						
						<?php } ?>
						
						<hr />
						
						<?php if( function_exists( 'cheerapp_like_button' ) ) cheerapp_like_button(); ?>
						<?php if( function_exists( 'cheerapp_send_comment_button' ) ) cheerapp_send_comment_button(); ?>
						
						<?php royal_contact_form( array( 'subject' => __( 'Feedback on', 'cheerapp' ) . ': ' . get_the_title() ) ); ?>
						
					</article><!-- end .post -->
					
				<?php
				endwhile;
				endif;
				?>
			
			</div><!-- end .content -->
			
			<?php get_template_part( 'sidebar', 'knowledgebase' ); ?>

		</div><!-- end .row -->
		
	</div><!-- end .inner-wrap -->
</div><!-- end #main -->

<?php get_footer(); ?>