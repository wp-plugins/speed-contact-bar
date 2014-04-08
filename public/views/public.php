<?php
/**
 * Frontend Output
 *
 * @package   Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */
 
$bg_color = $this->stored_settings[ 'bg_color' ] ? sprintf( ' style="background-color: %s;"', esc_attr( $this->stored_settings[ 'bg_color' ] ) ) : '';
$email = esc_attr( $this->stored_settings[ 'email' ] );
$phone = esc_attr( $this->stored_settings[ 'phone' ] );
?>
<div id="speed-contact-bar-wrapper"<?php echo $bg_color; ?>>
<?php 
if ( '' != $phone ) {
?>
	<span id="speed-contact-bar-phone"><?php _e( 'Phone', $this->plugin_slug ); echo ': '; echo $phone; ?></span>
	|
<?php 
} // phone
if ( '' != $email ) {
?>
	<span id="speed-contact-bar-email"><?php _e( 'E-Mail', $this->plugin_slug ); echo ': '; ?><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></span>
<?php 
} // email
?>
</div>
<?php
// that's all