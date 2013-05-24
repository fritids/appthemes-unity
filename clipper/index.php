<?php if(file_exists(STYLESHEETPATH . '/featured.php')) include_once(STYLESHEETPATH . '/featured.php'); else include_once(TEMPLATEPATH . '/featured.php'); ?>

<div id="content">

    <div class="content-box">

        <div class="box-t">&nbsp;</div>

        <div class="box-c">

            <div class="box-holder">

                <div class="head">

                    <h2><?php _e( 'New Coupons', APP_TD ); ?></h2>

										<div class="counter"><?php printf( _n( 'There are currently %s active coupon', 'There are currently %s active coupons', clpr_count_posts( APP_POST_TYPE, array( 'publish', 'unreliable' ) ), APP_TD ), '<span>' . clpr_count_posts( APP_POST_TYPE, array( 'publish', 'unreliable' ) ) . '</span>'); ?></div>

                </div> <!-- #head -->

                <?php
                // show all coupons and setup pagination
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                query_posts(array('post_type' => APP_POST_TYPE, 'ignore_sticky_posts' => 1, 'paged' => $paged));
                ?>

                <?php get_template_part('loop', 'coupon'); ?>


            </div> <!-- #box-holder -->

        </div> <!-- #box-c -->

        <div class="box-b">&nbsp;</div>

    </div> <!-- #content-box -->

</div><!-- #container -->

<?php get_sidebar('home'); ?>
