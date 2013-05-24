<?php
$heading	=	of_get_option( 'cta_heading',		'CheerApp is now available on ThemeForest for as low as $35' );
$text		=	of_get_option( 'cta_text',			'Get this WordPress theme for ridiculously low price and give your awesome app some great exposure!' );
$button_url	=	of_get_option( 'cta_button_url',	'http://themeforest.net/user/pogoking/portfolio?ref=pogoking' );
$button_text=	of_get_option( 'cta_button_text',	'Get CheerApp' );
?>

<div id="action" class="clearfix box padding-box">
	
	<div class="row">
		
		<div class="action-text span8">
			<h3 class="no-margin"><?php echo $heading; ?></h3>
			<p class="no-margin"><?php echo $text; ?></p>
		</div><!-- end .action-text -->
		
		<?php if( $button_url ) : ?>
			<a class="button icon button-download" href="<?php echo $button_url; ?>"><?php echo $button_text; ?></a>
		<?php endif; ?>
		
	</div>
	
</div><!-- end #action -->