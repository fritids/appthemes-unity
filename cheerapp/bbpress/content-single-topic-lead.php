<?php

/**
 * Single Topic Part
 */

?>

<div class="bbp-topic forum-lead-post clearfix" id="bbp-topic-<?php bbp_topic_id(); ?>">
				
	<div class="forum-post-info clearfix">
	
		<?php bbp_topic_author_link( array( 'sep' => '', 'size' => 80, 'type' => 'avatar' ) ); ?>
		
		<div class="forum-post-author">
			<?php bbp_topic_author_link( array( 'sep' => '', 'type' => 'name' ) ); ?>
		</div>
		
		<?php if ( is_super_admin() ) : ?>
			<div class="bbp-topic-ip"><?php bbp_author_ip( bbp_get_topic_id() ); ?></div>
		<?php endif; ?>
		
		<div class="forum-post-date">
			<?php printf( __( '%1$s <small>at</small> %2$s', 'cheerapp' ), get_the_date(), esc_attr( get_the_time() ) ); ?><br />
			<a href="#bbp-topic-<?php bbp_topic_id(); ?>" title="<?php _e( 'Permalink', 'cheerapp' ); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>
		</div>
		
	</div><!-- end .forum-post-info -->
	
	<div class="forum-post-content">
	
		<?php bbp_topic_content(); ?>
		
	</div><!-- end .forum-post-content -->
	
	<?php
	$args = array(
		'before'		=>	'',
		'after'			=>	'',
		'sep'			=>	''
	);
	$args['links'] = array(
		'edit'	=>	royal_bbp_get_topic_edit_link ( $args ),
		'close'	=>	'',
		'stick'	=>	'',
		'merge'	=>	royal_bbp_get_topic_merge_link( $args ),
		'trash'	=>	royal_bbp_get_topic_trash_link( $args ),
		'spam'	=>	royal_bbp_get_topic_spam_link ( $args )
	);
	$args['before'] = '<div class="forum-post-admin-links clearfix">';
	$args['after'] = '</div>';
	bbp_topic_admin_links( $args );
	?>
									
</div><!-- end #bbp-topic-<?php bbp_topic_id(); ?> -->

<hr />