<?php
/**
 * Options Page
 *
 * @package   Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( self::$settings_fields_slug ); ?>
		<?php do_settings_sections( self::$main_options_page_slug ); ?>
		<?php submit_button(); ?>
	</form>

</div>
