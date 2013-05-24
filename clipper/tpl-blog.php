<?php 
// Template Name: Blog Template
?>


<div id="content">


    <?php $args = array('post_type=' => 'post','paged'=> $paged); query_posts($args); ?>

    <?php get_template_part('loop'); ?>


</div>

<?php get_sidebar('blog'); ?>

