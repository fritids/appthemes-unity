<?php

/**
 * Topic meta info
 */

?>

<div class="topic-meta">
	
	<?php if( function_exists( 'royal_bbp_topic_status_link' ) ) royal_bbp_topic_status_link(); ?>
		
	<div class="bbp-topic-info">
	
		<?php $replies = bbp_show_lead_topic() ? bbp_get_topic_reply_count() : bbp_get_topic_post_count(); ?>
		<span class="topic-post-count"><span class="forum-icon comment-icon"></span>
			<?php
			if( bbp_show_lead_topic() ) printf( _n( '%d reply', '%s replies', $replies, 'cheerapp' ), $replies );
			else printf( _n( '%d post', '%s posts', $replies, 'cheerapp' ), $replies );
			?>
		</span>
	
		<?php if( function_exists( 'royal_bbp_sticky_icon' ) ) royal_bbp_sticky_icon(); ?>
	
		<?php if( function_exists( 'royal_bbp_closed_icon' ) ) royal_bbp_closed_icon(); ?>
		
		<?php if( function_exists( 'royal_bbp_subscribe_icon' ) ) royal_bbp_subscribe_icon(); ?>
		
		<?php if( function_exists( 'royal_bbp_favorite_icon' ) ) royal_bbp_favorite_icon(); ?>
		
	</div><!-- end .bbp-topic-info -->
	
	<?php
	$args = array(
		'before'	=>	'<ul class="bbp-topic-tags clearfix"><li class="bbp-topic-tag">',
		'sep'		=>	'</li><li class="bbp-topic-tag">',
		'after'		=>	'</li></ul>'
	);
	bbp_topic_tag_list( '', $args );
	?>

</div>