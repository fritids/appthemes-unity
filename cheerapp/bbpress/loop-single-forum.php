<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<tr id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

		<td class="forum-info">

			<?php do_action( 'bbp_theme_before_forum_title' ); ?>

			<a class="forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>"><?php bbp_forum_title(); ?></a>

			<?php do_action( 'bbp_theme_after_forum_title' ); ?>

			<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

			<?php
			$args = array(
				'before'            => '<ul class="sub-forums-list hidden-phone clearfix">',
				'after'             => '</ul>',
				'link_before'       => '<li class="sub-forum">',
				'link_after'        => '</li>',
				'count_before'      => '',
				'count_after'       => '',
				'count_sep'         => '',
				'separator'         => '',
				'show_topic_count'  => false,
				'show_reply_count'  => false,
			);
			bbp_list_forums( $args );
			?>

			<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

			<?php do_action( 'bbp_theme_before_forum_description' ); ?>

			<div class="forum-description hidden-phone"><?php the_content(); ?></div>

			<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		</td>

		<td class="forum-topic-count"><?php bbp_forum_topic_count(); ?></td>

		<td class="forum-reply-count"><?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></td>

		<td class="forum-freshness hidden-phone">

			<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>

			<?php royal_bbp_forum_freshness_link( '', 'author', 'tooltip', 'time' ); ?>

			<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
			
		</td>

	</tr><!-- bbp-forum-<?php bbp_forum_id(); ?> -->
