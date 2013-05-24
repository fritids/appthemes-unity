<form id="searchform" method="get" action="<?php echo home_url(); ?>" class="clearfix">
	<p class="input-parent">
		<input type="text" value="" name="s" id="s" placeholder="<?php _e( 'Start searching...', 'cheerapp' ); ?>" autocomplete="off" />
	</p>
	<p class="submit-parent">
		<input type="submit" id="searchsubmit" value="" />
	</p>
	<div class="hidden-fields">
		<?php wp_nonce_field( 'live_search', 'search-nonce', true, true ); ?>
	</div>
</form>