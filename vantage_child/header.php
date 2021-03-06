<div id="masthead" class="container">
	<div class="row">
		<hgroup>
			<?php va_display_logo(); ?>
		</hgroup>
		<div class="advert">
			<?php dynamic_sidebar( 'va-header' ); ?>
		</div>
	</div>
</div>
			<div id="sitenav">
				<ul><li><a href="http://brentbook.com">News</a></li><li class="bbcurrent"><a href="http://directory.brentbook.com">Directory</a></li><li><a href="http://jobs.brentbook.com">jobs</a></li><li><a href="http://forums.brentbook.com">Forums</a></li><li><a href="http://classifieds.brentbook.com">Classifieds</a></li></ul>
			</div>
<div id="main-navigation">
    <div id="rounded-nav-box-overlay">
        <div id="pmhmenu"
				<?php wp_nav_menu( array(
					'theme_location' => 'header',
					'container_class' => 'menu rounded',
					'items_wrap' => '<ul>%3$s</ul>',
					'fallback_cb' => false
				) ); ?>
				<?php if ( !is_page_template( 'create-listing.php' ) ) : ?>
				<form method="get" action="<?php echo trailingslashit( get_bloginfo( 'url' ) ); ?>">
        </div>
    </div>
	<div class="row">
		<div id="rounded-nav-box" class="rounded">
			
					<div id="main-search">
						<div class="search-for">
							<label for="search-text">
								<span class="search-title"><?php _e( 'Search For ', APP_TD ); ?></span><span class="search-help"><?php _e( '(e.g. restaurant, web designer, florist)', APP_TD ); ?></span>
							</label>
							<div class="input-cont h39">
								<div class="left h39"></div>
								<div class="mid h39">
									<input type="text" name="ls" id="search-text" class="text" value="<?php va_show_search_query_var( 'ls' ); ?>" />
								</div>
								<div class="right h39"></div>
							</div>
						</div>

						<div class="search-location">
							<label for="search-location">
								<span class="search-title"><?php _e( 'Near ', APP_TD ); ?></span><span class="search-help"><?php _e( '(city, country)', APP_TD ); ?></span>
							</label>
							<div class="input-cont h39">
								<div class="left h39"></div>
								<div class="mid h39">
									<input type="text" name="location" id="search-location" class="text" value="<?php va_show_search_query_var( 'location' ); ?>" />
								</div>
								<div class="right h39"></div>
							</div>
						</div>

						<div class="search-button">
							<!-- <input type="image" src="<?php echo get_bloginfo('template_directory'); ?>/images/search.png" value="<?php _e( 'Search', APP_TD ); ?>" /> -->
							<button type="submit" id="search-submit" class="rounded-small"><?php _e( 'Search', APP_TD ); ?></button>
						</div>
					</div>
					<?php the_search_refinements(); ?>
				</form>
				<?php endif; ?>
		</div>
	</div>
</div>

<?php if (is_front_page()) { 
	$arg = array('w' => '946px','h' => '250px','zoom' => '14','ismapnormal' => '1');?>
	<div class="busmap">
<?php do_action('wpw_vantage_gmap_code',$arg); ?>
	</div>
<?php } ?>

<?php if ( !is_front_page()) { ?>
<div id="breadcrumbs" class="container">
	<div class="row">
		<?php breadcrumb_trail( array(
			'separator' => '&raquo;',
			'before' => '',
			'show_home' => '<img src="' . get_template_directory_uri() . '/images/breadcrumb-home.png" />',
		) ); ?>
	</div>
</div> <?php } ?>