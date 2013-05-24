<?php
/*
Bottom widgets
*/
?>

<?php if( is_active_sidebar( 'bottombar' ) ) : ?>

	<hr />
	
	<div class="row">
	
		<?php dynamic_sidebar( 'bottombar' ); ?>
	
	</div><!-- end .row -->
	
<?php endif; ?>