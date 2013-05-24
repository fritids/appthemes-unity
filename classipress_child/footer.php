<div class="footer">

		<div class="footer_menu">

				<div class="footer_menu_res">

						<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container' => false, 'menu_id' => 'footer-nav-menu', 'depth' => 1, 'fallback_cb' => false ) ); ?>

						<div class="clr"></div>

				</div><!-- /footer_menu_res -->

		</div><!-- /footer_menu -->

		<div class="footer_main">

				<div class="footer_main_res">

						<div class="dotted">

								<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_footer') ) : else : ?> <!-- no dynamic sidebar so don't do anything --> <?php endif; ?>

								<div class="clr"></div>

						</div><!-- /dotted -->

						<div class="clr"></div>

				</div><!-- /footer_main_res -->

		</div><!-- /footer_main -->

</div><!-- /footer -->
<div class="footer_b">
	<div class="footer_c">
&copy; <?php echo date_i18n('Y'); ?> - Brentbook by <a href="http://paintmine.com">Paintmine</a>.
	</div>
</div>