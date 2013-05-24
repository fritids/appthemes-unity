<?php

/**
 * Builds a 'like' button
 *
 * @since 1.0
 *
 * @uses royal_get_like_url
 * @param int $post_id Post ID. If not supplied the ID of global $post is used.
 */
function cheerapp_like_button( $post_id = 0 ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	
	$button = '<a class="button icon button-like" ';
	$button .= 'href="#">';
	$button .= __( 'I found this helpful', 'cheerapp' );
	$button .= '</a>';
	
	echo $button;
}

/**
 * Build a send comment button
 *
 * @since 1.0
 */
function cheerapp_send_comment_button() {
	$button = '<a class="button icon button-mail feedback-button">';
	$button .= __( 'Send your comments', 'cheerapp' );
	$button .= '</a>';
	
	echo $button;
}

/**
 * Sets excerpt length
 *
 * @since 1.0
 */
function cheerapp_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'cheerapp_excerpt_length' );

/**
 * Formats blog comments
 *
 * @since 1.0
 */
function cheerapp_blog_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	
		<li class="comment post pingback">
			<div class="comment-content">
				<span class="comment-author">
					<small><?php _e( 'Pingback:', 'cheerapp' ); ?></small>
					<?php comment_author_link(); ?>
				</span>
			</div><!-- end .comment-content -->
			<div class="comment-meta">
				<?php edit_comment_link( __( 'Edit', 'cheerapp' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- end .comment-meta -->
			
	<?php
			break;
			
		default :
	?>
	
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
		<div class="comment-avatar clearfix">
			<?php
			$avatar_size = ( '0' != $comment->comment_parent ) ? 40 : 60;
			echo get_avatar( $comment, $avatar_size );
			?>
		</div>
		
		<div class="comment-content">
		
			<span class="comment-author"><?php printf( __( '%s <small>said:</small>', 'cheerapp' ), get_comment_author_link() ); ?></span>
			
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'cheerapp' ); ?></em>
			<?php endif; ?>
			
			<div class="comment-content"><?php comment_text(); ?></div>
			
			<div class="comment-meta">
			
				<span class="comment-date"><?php echo sprintf( __( '%1$s <small>at</small> %2$s', 'cheerapp' ), get_comment_date(), get_comment_time() ); ?></span>
				<?php edit_comment_link( __( 'Edit', 'cheerapp' ), '&nbsp;&middot;&nbsp;<span class="edit-link">', '</span>' ); ?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'cheerapp' ), 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '&nbsp;&middot;&nbsp;<span class="comment-reply-link">', 'after' => '</span>' ) ) ); ?>
				
			</div><!-- end .comment-meta -->
		
		</div><!-- end .comment-content -->
		
	<?php
			break;
	endswitch;
}

/**
 * Returns a set of CSS rules containing custom coloring
 *
 * @since 1.0
 *
 * @return string A string of CSS rules
 */
function cheerapp_custom_colors() {
	
	$main					=	of_get_option( 'color_main',					'#79ba73' );
	
	$link					=	of_get_option( 'color_link',					'#71a66c' );
	$link_hover				=	of_get_option( 'color_link_hover',				'#82bf7c' );
	$link_active			=	of_get_option( 'color_link_active',				'#5f8c5b' );
	
	$button_color			=	of_get_option( 'color_button_text',				'#736840' );
	$button_top				=	of_get_option( 'color_button_top',				'#ffeeb2' );
	$button_bottom			=	of_get_option( 'color_button_bottom',			'#ebcf6a' );
	$button_border			=	of_get_option( 'color_button_border',			'#ccb972' );
	$button_fallback		=	of_get_option( 'color_button_fallback',			'#f5d86e' );
	
	$button_hover_color		=	of_get_option( 'color_button_hover_text',		'#665c39' );
	$button_hover_top		=	of_get_option( 'color_button_hover_top',		'#ffeeb2' );
	$button_hover_bottom	=	of_get_option( 'color_button_hover_bottom',		'#f7da6f' );
	$button_hover_fallback	=	of_get_option( 'color_button_hover_fallback',	'#ffe173' );
	
	$button_active_color	=	of_get_option( 'color_button_active_text',		'#665c39' );
	$button_active_top		=	of_get_option( 'color_button_active_top',		'#f5e5ab' );
	$button_active_bottom	=	of_get_option( 'color_button_active_bottom',	'#ebcf6a' );
	$button_active_fallback	=	of_get_option( 'color_button_active_fallback',	'#ebcf6a' );
	
	$css;
	
	$css .= '#featured{';
	$css .= 'background-color:' . $main . ';';
	$css .= '}';
	
	$css .= 'a{';
	$css .= 'color:' . $link . ';';
	$css .= '}';
	
	$css .= 'a:hover{';
	$css .= 'color:' . $link_hover . ';';
	$css .= '}';
	
	$css .= 'a:active{';
	$css .= 'color:' . $link_active . ';';
	$css .= '}';
	
	$css .= '.button,#header .button,#submit,input[type=submit],button{';
	$css .= 'color:' . $button_color . ';';
	$css .= 'background:' . $button_fallback . ';';
	$css .= 'background-image:-ms-linear-gradient(top,' . $button_top . ' 0%,' . $button_bottom . ' 100%);';
	$css .= 'background-image:-moz-linear-gradient(top,' . $button_top . ' 0%,' . $button_bottom . ' 100%);';
	$css .= 'background-image:-o-linear-gradient(top,' . $button_top . ' 0%,' . $button_bottom . ' 100%);';
	$css .= 'background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,' . $button_top . '),color-stop(1,' . $button_bottom . '));';
	$css .= 'background-image:-webkit-linear-gradient(top,' . $button_top . ' 0%,' . $button_bottom . ' 100%);';
	$css .= 'background-image:linear-gradient(top,' . $button_top . ' 0%,' . $button_bottom . ' 100%);';
	$css .= 'border-color:' . $button_border . ';';
	$css .= '}';
	
	$css .= '.button:hover,#header .button:hover,#submit:hover,input[type=submit]:hover,button:hover{';
	$css .= 'color:' . $button_hover_color . ';';
	$css .= 'background:' . $button_hover_fallback . ';';
	$css .= 'background-image:-ms-linear-gradient(top,' . $button_hover_top . ' 0%,' . $button_hover_bottom . ' 100%);';
	$css .= 'background-image:-moz-linear-gradient(top,' . $button_hover_top . ' 0%,' . $button_hover_bottom . ' 100%);';
	$css .= 'background-image:-o-linear-gradient(top,' . $button_hover_top . ' 0%,' . $button_hover_bottom . ' 100%);';
	$css .= 'background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,' . $button_hover_top . '),color-stop(1,' . $button_hover_bottom . '));';
	$css .= 'background-image:-webkit-linear-gradient(top,' . $button_hover_top . ' 0%,' . $button_hover_bottom . ' 100%);';
	$css .= 'background-image:linear-gradient(top,' . $button_hover_top . ' 0%,' . $button_hover_bottom . ' 100%);';
	$css .= '}';
	
	$css .= '.button:active,#header .button:active,#submit:active,input[type=submit]:active,button:active{';
	$css .= 'color:' . $button_active_color . ';';
	$css .= 'background:' . $button_active_fallback . ';';
	$css .= 'background-image:-ms-linear-gradient(top,' . $button_active_top . ' 0%,' . $button_active_bottom . ' 100%);';
	$css .= 'background-image:-moz-linear-gradient(top,' . $button_active_top . ' 0%,' . $button_active_bottom . ' 100%);';
	$css .= 'background-image:-o-linear-gradient(top,' . $button_active_top . ' 0%,' . $button_active_bottom . ' 100%);';
	$css .= 'background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,' . $button_active_top . '),color-stop(1,' . $button_active_bottom . '));';
	$css .= 'background-image:-webkit-linear-gradient(top,' . $button_active_top . ' 0%,' . $button_active_bottom . ' 100%);';
	$css .= 'background-image:linear-gradient(top,' . $button_active_top . ' 0%,' . $button_active_bottom . ' 100%);';
	$css .= '}';
	
	return $css;
}

/**
 * Prints custom user styles
 *
 * @since 1.0
 *
 * @uses cheerapp_custom_colors()
 */
function cheerapp_print_custom_styles() {
	
	$use_user_styles = of_get_option( 'use_custom_colors', '0' );
	
	if( $use_user_styles ) { ?>
	
		<style type="text/css">
			<?php echo cheerapp_custom_colors(); ?>
		</style>
	
	<?php }
}
add_action( 'wp_head', 'cheerapp_print_custom_styles' );

?>