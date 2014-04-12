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

	<div class="th_wrapper">
		<div id="th_main">
			<div class="th_content">

				<form method="post" action="options.php">
<?php 
settings_fields( self::$settings_fields_slug );
do_settings_sections( self::$main_options_page_slug );
submit_button(); 
?>
				</form>

			</div><!-- .th_content -->
		</div><!-- #th_main -->
		<div id="th_footer">
			<div class="th_content">
				<h3><?php _e( 'Credits and informations', self::$plugin_slug ); ?></h3>
				<dl>
					<dt><?php _e( 'Do you like the plugin?', self::$plugin_slug ); ?></dt><dd><a href="http://wordpress.org/support/view/plugin-reviews/speed-contact-bar"><?php _e( 'Rate it at wordpress.org!', self::$plugin_slug ); ?></a></dd>
					<dt><?php _e( 'Do you need support or have an idea for the plugin?', self::$plugin_slug ); ?></dt><dd><a href="http://wordpress.org/support/plugin/speed-contact-bar"><?php _e( 'Post your questions and ideas in the forum at wordpress.org!', self::$plugin_slug ); ?></a></dd>
					<dt><?php _e( 'Idea by', self::$plugin_slug ); ?></dt><dd><a href="http://alexandra-mutter.de/?ref=speed-contact-bar"> <?php echo get_avatar( 'allamoda07@googlemail.com', 64 ); ?>Alexandra Mutter Design</a></dd>
				</dl>
			</div><!-- .th_content -->
		</div><!-- #th_footer -->
	</div><!-- .th_wrapper -->
</div><!-- .wrap -->
