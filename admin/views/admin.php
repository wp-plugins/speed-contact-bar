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

// get and set locale code for paypal button
// source: https://developer.paypal.com/docs/classic/archive/buttons/
// source: http://wpcentral.io/internationalization/
$paypal_locale = get_locale();
// if locale is not in registered locale code try to find the nearest match
if ( ! in_array( $paypal_locale, array( 'en_US', 'en_AU', 'es_ES', 'fr_FR', 'de_DE', 'ja_JP', 'it_IT', 'pt_PT', 'pt_BR', 'pl_PL', 'ru_RU', 'sv_SE', 'tr_TR', 'nl_NL', 'zh_CN', 'zh_HK', 'he_IL' ) ) ) {
	if ( 'ja' == $paypal_locale ) { // japanese language
		$paypal_locale = 'ja_JP';
	} else {
		$language_codes = explode( '_', $paypal_locale );
		// test the language
		switch ( $language_codes[ 0 ] ) {
			case 'en':
				$paypal_locale = 'en_US';
				break;
			case 'nl':
				$paypal_locale = 'nl_NL';
				break;
			case 'es':
				$paypal_locale = 'es_ES';
				break;
			case 'de':
				$paypal_locale = 'de_DE';
				break;
			default:
				$paypal_locale = 'en_US';
		} // switch()
	} // if ('ja')
} // if !in_array()
?>
				</form>

			</div><!-- .th_content -->
		</div><!-- #th_main -->
		<div id="th_footer">
			<div class="th_content">
				<h3><?php _e( 'Credits and informations', self::$plugin_slug ); ?></h3>
				<dl>
					<dt><?php _e( 'Do you like the plugin?', self::$plugin_slug ); ?></dt><dd><a href="http://wordpress.org/support/view/plugin-reviews/speed-contact-bar"><?php _e( 'Rate it at wordpress.org!', self::$plugin_slug ); ?></a></dd>
					<dt><?php _e( 'The plugin is for free. But the plugin author would be delighted to your small contribution.', self::$plugin_slug ); ?></dt><dd><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=TPCX6FVZ5NSJ6"><img src="https://www.paypalobjects.com/<?php echo $paypal_locale; ?>/i/btn/btn_donateCC_LG.gif" alt="(<?php _e( 'Donation Button', self::$plugin_slug ); ?>)" id="paypal_button" /><br /><?php _e( 'Donate by PayPal', self::$plugin_slug ); ?></a><img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1" /></dd>
					<dt><?php _e( 'Do you need support or have an idea for the plugin?', self::$plugin_slug ); ?></dt><dd><a href="http://wordpress.org/support/plugin/speed-contact-bar"><?php _e( 'Post your questions and ideas in the forum at wordpress.org!', self::$plugin_slug ); ?></a></dd>
					<dt><?php _e( 'Idea and styles by', self::$plugin_slug ); ?></dt><dd><a href="http://alexandra-mutter.de/?ref=speed-contact-bar"> <?php echo get_avatar( 'allamoda07@googlemail.com', 44 ); ?>alexandra mutter design</a></dd>
					<dt><?php _e( 'Plugin development by', self::$plugin_slug ); ?></dt><dd><a href="http://stehle-internet.de/?ref=speed-contact-bar"> <?php echo get_avatar( 'm.stehle@gmx.de', 44 ); ?>Stehle Internet</a></dd>
				</dl>
			</div><!-- .th_content -->
		</div><!-- #th_footer -->
	</div><!-- .th_wrapper -->
</div><!-- .wrap -->
