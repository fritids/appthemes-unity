<div id="header">

	<div class="shadow">&nbsp;</div>

	<div class="holder">

		<div class="frame">

			<div class="panel">

				<?php wp_nav_menu( array( 'menu_id' => 'nav', 'theme_location' => 'primary', 'container' => '', 'fallback_cb' => 'clpr_primary_nav_menu' ) ); ?>

				<div class="bar">

					<ul class="social">

						<li><a class="rss" href="<?php echo appthemes_get_feed_url(); ?>" rel="nofollow" target="_blank"><?php _e( 'RSS', APP_TD ); ?></a></li>

						<?php if (get_option('clpr_facebook_id') <> '') { ?>
							<li><a class="facebook" href="<?php echo appthemes_make_fb_profile_url( get_option('clpr_facebook_id') ); ?>" rel="nofollow" target="_blank"><?php _e( 'Facebook', APP_TD ); ?></a></li>
						<?php } ?>

						<?php if (get_option('clpr_twitter_id') <> '') { ?>
							<li><a class="twitter" href="http://twitter.com/<?php echo stripslashes(get_option('clpr_twitter_id')); ?>" rel="nofollow" target="_blank"><?php _e( 'Twitter', APP_TD ); ?></a></li>
						<?php } ?>

					</ul>

					<ul class="add-nav">

						<?php clpr_login_head(); ?>

					</ul>

				</div>

			</div>

			<div class="header-bar">

				<?php get_search_form(); ?>

				<div id="logo">

						<?php if (get_option('clpr_use_logo') != 'no') { ?>

							<a href="<?php echo home_url('/'); ?>" title="<?php bloginfo( 'description' ); ?>">
								<img src="<?php if ( get_option( 'clpr_logo_url' ) ) echo get_option( 'clpr_logo_url' ); else { bloginfo( 'template_directory' ); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo( 'name' ); ?>" />
							</a>

						<?php } else { ?>

							<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
							<div class="description"><?php bloginfo( 'description' ); ?></div>

						<?php } ?>

				</div>

			</div>

		</div> <!-- #frame -->

	</div> <!-- #holder -->

</div> <!-- #header -->
