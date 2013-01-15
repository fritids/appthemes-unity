<div id="header">
	<div class="holder">

		<div class="frame">

			<div class="panel">

				<div class="bar">

					<ul class="social">

						<li><a class="rss" href="<?php echo clpr_get_feed_url(); ?>" rel="nofollow" target="_blank"><?php _e('RSS', 'appthemes'); ?></a></li>

						<?php if (get_option('clpr_facebook_id') <> '') { ?>
							<li><a class="facebook" href="http://www.facebook.com/profile.php?id=<?php echo stripslashes(get_option('clpr_facebook_id')); ?>" rel="nofollow" target="_blank"><?php _e('Facebook', 'appthemes'); ?></a></li>
						<?php } ?>

						<?php if (get_option('clpr_twitter_id') <> '') { ?>
							<li><a class="twitter" href="http://twitter.com/<?php echo stripslashes(get_option('clpr_twitter_id')); ?>" rel="nofollow" target="_blank"><?php _e('Twitter', 'appthemes'); ?></a></li>
						<?php } ?>

					</ul>

					<ul class="add-nav">

						<?php clpr_login_head(); ?>

					</ul>

				</div>

			</div>

			<div class="header-bar">

	

				<div id="logo">

						<?php if (get_option('clpr_use_logo') != 'no') { ?>

							<a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'description' ); ?>">
								<img src="<?php if ( get_option( 'clpr_logo_url' ) ) echo get_option( 'clpr_logo_url' ); else { bloginfo( 'template_directory' ); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo( 'name' ); ?>" />
							</a>

						<?php } else { ?>

							<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
							<div class="description"><?php bloginfo( 'description' ); ?></div>

						<?php } ?>

				</div>
                            

		</div> <!-- #frame -->
                <div class="pmenucontainer">
                    <div class="pmhmenu"><?php wp_nav_menu( array( 'menu_id' => 'nav', 'theme_location' => 'primary', 'container' => '', 'fallback_cb' => 'clpr_primary_nav_menu' ) ); ?>
                    </div>
                </div>
                            <div class="pmhsearch">
                                <?php get_search_form(); ?>
                            </div>
	</div> <!-- #holder -->

</div> <!-- #header -->
