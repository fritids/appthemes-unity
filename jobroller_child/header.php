<div id="topNav">

	<div class="inner">

		<?php wp_nav_menu( array( 'theme_location' => 'top', 'sort_column' => 'menu_order', 'container' => 'menu-header', 'fallback_cb' => 'default_top_nav' ) ); ?>

		<div class="clear"></div>

	</div><!-- end inner -->

</div><!-- end topNav -->

<div id="header">

	<div class="inner">

		<div class="logo_wrap">

			<?php if (is_front_page()) { ?><h1 id="logo"><?php } else { ?><div id="logo"><?php } ?>

			<?php if (get_option('jr_use_logo') != 'no') { ?>

					<?php if (get_option('jr_logo_url')) { ?>

						<a href="<?php bloginfo('url'); ?>"><img class="logo" src="<?php echo get_option('jr_logo_url'); ?>" alt="<?php bloginfo('name'); ?>" /></a>

					<?php } else { ?>

							<a href="<?php bloginfo('url'); ?>"><img class="logo" src="<?php bloginfo('template_directory'); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" /></a>

					<?php } ?>

			<?php } else { ?>

				<a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a> <small><?php bloginfo('description'); ?></small>
		   
			<?php } ?>

			<?php if (is_front_page()) { ?></h1><?php } else { ?></div><?php } ?>

			<?php if (get_option('jr_enable_header_banner')=='yes') : ?>
				<div id="headerAd"><?php echo stripslashes(get_option('jr_header_banner')); ?></div>
			<?php else : ?>

			<?php endif; ?>

			<div class="clear"></div>

		</div><!-- end logo_wrap -->

	</div><!-- end inner -->
			<div id="sitenav">
				<ul><li><a href="http://brentbook.com">News</a></li><li><a href="http://directory.brentbook.com">Directory</a></li><li class="bbcurrent"><a href="http://jobs.brentbook.com">jobs</a></li><li><a href="http://forums.brentbook.com">Forums</a></li><li><a href="http://classifieds.brentbook.com">Classifieds</a></li></ul>
			</div>
<div class="bbnav"><div id="mainNav"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => '', 'depth' => 1, 'fallback_cb' => 'default_primary_nav' ) );?></div></div>
</div><!-- end header -->
<div class="bbsearch">
<?php
	// Empty search fixes
	if ( isset($_GET['resume_search']) && $_GET['resume_search'] ) : 
		if (isset($_GET['s']) && isset($_GET['location']) && !empty($_GET['location'])) : get_template_part('search-resume'); return; endif;
		wp_safe_redirect(get_post_type_archive_link('resume'));
		exit;
	endif;
	if (isset($_GET['s']) && isset($_GET['location']) && !empty($_GET['location'])) : get_template_part('search'); return; endif;
?>

<?php get_header('search'); ?>
</div>
<div class="headerfeat">
<?php do_action('jobs_will_display'); ?>
</div>