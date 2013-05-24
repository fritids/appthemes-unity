<?php $class = of_get_option( 'cta_enable', '1' ) ? 'with-padding' : ''; ?>

<div id="featured" class="<?php echo $class; ?>">

	<?php
	// If displayed page used index.php template (if it's home page)...
	if ( is_page_template( 'index.php' ) ) {
	
		get_template_part( 'slider' );
		
	}
	// If displayed page is not home page
	else {
	?>
	
		<div class="inner-wrap">
			
			<div class="row">
				
				<?php
				if ( function_exists( 'royal_breadcrumb' ) ) {
					$show_home_link = intval( of_get_option( 'breadcrumb_show_home_link', '1' ) ) ? true : false;
					$args = array(
						'show_home_link'	=>	$show_home_link,
						'before'			=>	'<div class="breadcrumb span8"><h2>',
						'after'				=>	'</h2></div>',
						'before_trail'		=>	'<small>',
						'after_trail'		=>	'</small>',
						'sep'				=>	' / '
					);
					royal_breadcrumb( $args );
				} else {
					the_title();
				}
				?>
				
				<div class="live-search-container span4">
				
				<?php get_search_form(); ?>
				
				</div>
			
			</div>
			
		</div><!-- end .inner-wrap -->
		
	<?php
	}
	?>

</div><!-- end #featured -->