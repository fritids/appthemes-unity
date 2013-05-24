<?php
/**
 * Check $module_name parameter, load appropriate files and
 * init appropriate functions
 *
 * @since 0.1
 *
 * @uses add_featured_posts_module
 * @uses add_knowledgebase_module
 * @param string $module_name The name of module to load
 * @param array|mixed $module_args Module options
 */
function init_module( $module_name, $module_args = array() ) {
	
	switch ( $module_name ) {
	
		case 'featured' :
			require_once ( 'modules/module-featured-posts.php' );
			add_featured_posts_module( $module_args );
		
			break;
			
		case 'knowledgebase' :
			require_once ( 'modules/module-knowledgebase.php' );
			add_knowledgebase_module( $module_args );
			
			break;
			
		case 'quick-links' :
			require_once ( 'modules/module-quick-links.php' );
			add_quick_links_module( $module_args );
			
			break;
			
		case 'pricing' :
			require_once ( 'modules/module-pricing.php' );
			add_pricing_module( $module_args );
			
			break;
			
		}
		
}

?>