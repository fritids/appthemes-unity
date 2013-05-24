<?php
$meta			=	get_post_meta( $post->ID, '_royal_meta', true );

$date_format	=	get_option( 'date_format' );
$article_terms	=	get_the_term_list( $post->ID, 'kb_category', '', ' / ', '' );
$post_classes	=	array( 'post', 'post-archive', 'clearfix' );

$faq_group		=	!empty( $meta['faq_group'] ) ? $meta['faq_group'] : null;
$faq_posts		=	!empty( $faq_group ) ? get_posts( array( 'post_type' => 'faq', 'faq_category' => $faq_group, 'showposts' => -1 ) ) : null;
$faq_count		=	!empty( $faq_posts ) ? count( $faq_posts ) : 0;
?>

<article <?php post_class( $post_classes ); ?> id="post-<?php the_ID(); ?>">

	<h3 class="post-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>
	<span class="date"><?php the_time( $date_format ); ?></span>
	
	<div class="archive-post-meta">
	
		<span class="post-meta"><?php printf( __( '<small>in</small> %s', 'cheerapp' ), $article_terms ); ?></span>						
		<a class="faq-count tooltip" href="<?php the_permalink(); ?>#faq" title="<?php printf( _n( '%d frequently asked question answered', '%d frequently asked questions answered', $faq_count, 'cheerapp' ), $faq_count ); ?>"><?php echo $faq_count; ?></a>
		<?php if( function_exists( 'royal_likes' ) ) royal_likes(); ?>
		
	</div><!-- end .archive-post-meta -->
	
	<div class="post-content">
	
		<?php the_excerpt(); ?>
		
		<a class="more" href="<?php the_permalink(); ?>" title="<?php printf( __( 'Continue reading %s', 'cheerapp' ), '&ldquo;' . get_the_title() . '&rdquo;' ); ?>"><?php _e( 'Read more', 'cheerapp' ); ?></a>
		
	</div><!-- end .post-content -->
	
</article><!-- end .post -->

<hr />