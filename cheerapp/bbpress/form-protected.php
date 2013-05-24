<?php

/**
 * Password Protected
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<fieldset class="bbp-form" id="bbp-protected">
		<Legend><h4><?php _e( 'Protected', 'cheerapp' ); ?></h4></legend>

		<?php echo get_the_password_form(); ?>

	</fieldset>
