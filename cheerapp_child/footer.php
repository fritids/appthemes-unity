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
			
			</div>
		</footer><!-- end footer -->
	
		<?php wp_footer(); ?>
<div class="footer_b">
	<div class="footer_c">
&copy; <?php echo date_i18n('Y'); ?> - Brentbook by <a href="http://paintmine.com">Paintmine</a>.
	</div>
</div>
	</body>
	
</html>