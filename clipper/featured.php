<?php
/**
 * The featured slider on the home page
 *
 */
?>

<?php query_posts('post_type='.APP_POST_TYPE.'&meta_key=clpr_featured&meta_value=1'); ?>

<?php if (have_posts()) : ?>

<div class="featured-slider">

    <div class="gallery-t">&nbsp;</div>

    <div class="gallery-c">

        <div class="gallery-holder">

            <div class="slide">

                <div class="link-l">

                    <a href="#" class="prev"><?php _e( 'prev', APP_TD ); ?></a>

                </div>

                <div class="slide-contain">

                    <ul class="slider">

                    <?php while (have_posts()) : the_post(); ?>

                        <li>

                            <div class="image">

																<a href="<?php the_permalink(); ?>"><img height="120" width="160" src="<?php echo clpr_get_store_image_url($post->ID, 'post_id', '160'); ?>" alt="" /></a>

                            </div>

                            <span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>

                        </li>

                    <?php endwhile; ?>

                    </ul>

                </div>

                <div class="link-r">

                    <a href="#" class="next"><?php _e( 'next', APP_TD ); ?></a>

                </div>

            </div>

        </div>

    </div>

		<div class="featured-button">

		    <span class="button-l">&nbsp;</span>

        <h1><?php _e( 'Featured Coupons', APP_TD ); ?></h1>

		    <span class="button-r">&nbsp;</span>

    </div>

    <div class="gallery-b">&nbsp;</div>

</div>

<?php endif; ?>

<?php wp_reset_query(); // clean up ?>
				