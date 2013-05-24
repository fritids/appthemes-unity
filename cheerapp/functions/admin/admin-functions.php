<?php

/**
 * Registers and loads admin styles
 *
 * @since 0.1
 */
function royal_register_admin_styles() {
	wp_register_style( 'admin-metaboxes', get_template_directory_uri() . '/functions/admin/css/admin-metaboxes.css', '', '1.0', 'all' );
	
	wp_enqueue_style( 'admin-metaboxes' );
}
add_action( 'admin_print_styles', 'royal_register_admin_styles' );

?>