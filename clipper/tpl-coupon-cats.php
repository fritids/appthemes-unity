<?php 
// Template Name: Coupon Category Template


// get all the coupon categories except for child cats
$categories = get_terms(APP_TAX_CAT, array('hide_empty' => 0, 'child_of' => 0, 'pad_counts' => 0, 'app_pad_counts' => 1));
$list = '';
$groups = array();

if ($categories && is_array($categories)) {

	foreach($categories as $key => $value)
    if($value->parent != 0)
		  unset($categories[$key]);
	
	foreach($categories as $category)
		$groups[mb_strtoupper(mb_substr($category->name, 0, 1))][] = $category;
	
	if (!empty($groups)) :
	
		foreach($groups as $letter => $categories) {
			$list .= "\n\t" . '<h2 class="categories">' . apply_filters('the_title', $letter) . '</h2>';
			$list .= "\n\t" . '<ul class="categories">';
			
			foreach($categories as $category)
				$list .= "\n\t\t" . '<li><a href="' . get_term_link($category, APP_TAX_STORE) . '">' . apply_filters('the_title', $category->name) . '</a> (' . intval($category->count) . ')</li>';
				
			$list .= "\n\t" . '</li></ul>';
		}
		
	endif;
	
} else {

	$list .= "\n\t" . '<p>' . __( 'Sorry, but no coupon categories were found.', APP_TD ) .'</p>';
	
}
?>



<div id="content">

	<div class="content-box">
			
		<div class="box-t">&nbsp;</div>
		
		<div class="box-c">
		
			<div class="box-holder">
			
				<div class="blog">
				
				<h1><?php _e( 'Browse by Coupon Category', APP_TD ); ?></h1>
				
					<div class="text-box">

						<?php echo $list; ?>

					</div>
					
				</div> <!-- #blog -->
				
			</div> <!-- #box-holder -->
		
		</div> <!-- #box-c -->
		
		<div class="box-b">&nbsp;</div>
		
	</div> <!-- #content-box -->	



</div>

<?php get_sidebar('main'); ?>

