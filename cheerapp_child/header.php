<!DOCTYPE html>

<?php $theme_options = get_option('option_tree'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head>
		
		<!-- Title -->
		<title><?php wp_title( '&raquo;', true, 'right' ); ?> <?php bloginfo( 'name' ); ?></title>
		
		<!-- Meta tags -->
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
		
		<!-- CSS -->
		<?php $theme = wp_get_theme(); ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_stylesheet_uri(); ?>?<?php echo $theme->Version; ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo get_template_directory_uri(); ?>/css/prettyPhoto.css" />
		
		<!-- RSS & Pingbacks -->
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php bloginfo( 'rss2_url' ); ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		
		<script type="text/javascript">
			document.documentElement.className += 'js-ready';
		</script>
		
		<!-- Theme Hook -->
		<?php wp_head(); ?>
		
		<!--[if lt IE 9]>
			<?php wp_print_scripts( array( 'html5shiv' ) ); ?>
			<?php wp_print_styles( array( 'ie-styles' ) ); ?>
		<![endif]-->
			
	</head>
	
	<?php $body_classes = function_exists( 'bbpress' ) ? 'bbp' : ''; ?>
	
	<body <?php body_class( $body_classes ); ?>>
	
		<div id="wrap">
		
			<header id="header">
				<div class="bb_top">
					<div class="bb_top_in">
						<?php if( function_exists( 'bbpress' ) ) royal_login(); ?>
					</div>
				</div>
				<div class="inner-wrap clearfix">
					
					<?php
					$logo_path			= of_get_option( 'logo_top', get_template_directory_uri() . '/images/logo.png' );
					$hidpi_logo_path	= of_get_option( 'logo_top_hidpi' ) ? of_get_option( 'logo_top_hidpi' ) : get_template_directory_uri() . '/images/logo.png';
					$hidpi_logo_width	= intval( of_get_option( 'logo_top_hidpi_width' ) ) / 2;
					?>
					
					<a id="logo" href="<?php echo home_url(); ?>">
						<img class="hidden-retina" src="<?php echo $logo_path; ?>" alt="<?php bloginfo( 'name' ); ?>" />
						<img class="visible-retina" src="<?php echo $hidpi_logo_path; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php if( $hidpi_logo_width ) : ?> style="width: <?php echo $hidpi_logo_width; ?>px; height: auto;"<?php endif; ?> />
					</a>					
				</div><!-- end .inner-wrap -->
			<div id="sitenav">
				<ul><li><a href="http://brentbook.com">News</a></li><li><a href="http://directory.brentbook.com">Directory</a></li><li><a href="http://jobs.brentbook.com">jobs</a></li><li class="bbcurrent"><a href="http://forums.brentbook.com">Forums</a></li><li><a href="http://classifieds.brentbook.com">Classifieds</a></li></ul>
			</div>
					<nav>
						<div class="bb_nav">
						<?php
						if ( has_nav_menu( 'top-menu' ) ) {
							$menu_args = array(
								'theme_location'	=>	'top-menu',
								'container'			=>	false,
								'menu_class'		=>	'clearfix dropdown',
								'menu_id'			=>	'nav',
								'walker'			=>	new Royal_Menu_Walker()
							);
	                		wp_nav_menu( $menu_args );
	                	} else {
	                	?>
	                	<ul id="nav" class="clearfix dropdown">
	                    	<?php wp_list_pages( array( 'title_li' => '' )); ?>
	                	</ul>
	                	<?php } ?>
	                	</div>
					</nav>
			</header><!-- end header -->