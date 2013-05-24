<?php 
// Template Name: Store Template


// get all the stores
$stores = get_terms(APP_TAX_STORE, array('hide_empty' => 0, 'child_of' => 0, 'pad_counts' => 0, 'app_pad_counts' => 1));
// get ids of all hidden stores 
$hidden_stores = clpr_hidden_stores();
$list = '';
$groups = array();


if ($stores && is_array($stores) ) {

	// unset child stores
	foreach($stores as $key => $value)
    if($value->parent != 0)
		  unset($stores[$key]);
	
	foreach($stores as $store)
		$groups[mb_strtoupper(mb_substr($store->name, 0, 1))][] = $store;
	
	if (!empty($groups)) :
	
		foreach($groups as $letter => $stores) {
      $old_list = $list;
      $letter_items = false;
			$list .= "\n\t" . '<h2 class="stores">' . apply_filters( 'the_title', $letter ) . '</h2>';
			$list .= "\n\t" . '<ul class="stores">';
			
			foreach($stores as $store) {
				if (!in_array($store->term_id, $hidden_stores)) {
					$list .= "\n\t\t" . '<li><a href="' . get_term_link($store, APP_TAX_STORE) . '">' . apply_filters('the_title', $store->name). '</a> (' . intval($store->count) . ')</li>';
          $letter_items = true;
        }
			}	
				
			$list .= "\n\t" . '</ul>';

      if(!$letter_items)
        $list = $old_list;
		}
		
	endif;
	
} else {

	$list .= "\n\t" . '<p>' . __( 'Sorry, but no stores were found.', APP_TD ) .'</p>';
	
}
?>



<div id="content">

	<div class="content-box">
			
		<div class="box-t">&nbsp;</div>
		
		<div class="box-c">
		
			<div class="box-holder">
			
				<div class="blog">
				
				<h1><?php _e( 'Browse by Store', APP_TD ); ?></h1>
				
					<div class="text-box">

						<?php print $list; ?>

					</div>
					
				</div> <!-- #blog -->
				
			</div> <!-- #box-holder -->
		
		</div> <!-- #box-c -->
		
		<div class="box-b">&nbsp;</div>
		
	</div> <!-- #content-box -->	



</div>

<?php get_sidebar('store'); ?>

