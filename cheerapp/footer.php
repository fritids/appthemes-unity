		</div>
		
		<footer id="footer">
			<div class="inner-wrap">
			
				<nav>
					<?php
					if ( has_nav_menu( 'footer-menu' ) ) {
						$menu_args = array(
							'theme_location'	=>	'footer-menu',
							'container'			=>	false,
							'menu_class'		=>	'clearfix',
							'menu_id'			=>	'footer-nav',
							'depth'				=>	1
						);
                   		wp_nav_menu( $menu_args );
                   	} else {
                   	?>
                   	<ul id="footer-nav" class="clearfix">
                       	<?php wp_list_pages( array( 'title_li' => '', 'depth' => 1 )); ?>
                   	</ul>
                   	<?php } ?>
				</nav>
				
				<!-- Set margin-top on logo to negative half of image height (to center it vertically) in CSS (.footer-logo) -->
				<?php
				$logo_path			= of_get_option( 'logo_footer', get_template_directory_uri() . '/images/logo-small.png' );
				$hidpi_logo_path	= of_get_option( 'logo_footer_hidpi' ) ? of_get_option( 'logo_footer_hidpi' ) : get_template_directory_uri() . '/images/logo-small.png';
				$hidpi_logo_width	= intval( of_get_option( 'logo_footer_hidpi_width' ) ) / 2;
				?>
				
				<a class="footer-logo" href="<?php echo home_url(); ?>">
					<img class="hidden-retina" src="<?php echo $logo_path; ?>" alt="<?php bloginfo( 'name' ); ?>" />
					<img class="visible-retina" src="<?php echo $hidpi_logo_path; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php if( $hidpi_logo_width ) : ?> style="width: <?php echo $hidpi_logo_width; ?>px; height: auto;"<?php endif; ?> />
				</a>
				
				<?php $footer_text = of_get_option( 'footer_text', 'CheerApp HTML template by Mateusz Hajdziony' ); ?>
				
				<small class="copyright">&copy;<?php echo date( 'Y' ); ?>&nbsp;&nbsp;&middot;&nbsp;&nbsp;<?php echo $footer_text; ?></small>
			
			</div>
		</footer><!-- end footer -->
	
		<?php wp_footer(); ?>
	
	</body>
	
</html>