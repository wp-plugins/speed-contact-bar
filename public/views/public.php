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
 ?>
<div id="speed-contact-bar-wrapper"<?php 
if ( '' != $this->stored_settings[ 'bg_color' ] ) { 
	printf( ' style="background-color: %s;"', esc_attr( $this->stored_settings[ 'bg_color' ] ) ); 
}
?>>
<?php 
if ( '' != $this->stored_settings[ 'headline' ] ) {
	printf( '<h2 id="speed-contact-bar-headline">%s</h2> | ', esc_html( $this->stored_settings[ 'headline' ] ) );
} // phone
if ( '' != $this->stored_settings[ 'phone' ] ) {
	printf( '<span id="speed-contact-bar-phone">%s: %s</span> | ', __( 'Phone', $this->plugin_slug ), esc_html( $this->stored_settings[ 'phone' ] ) );
} // phone
if ( '' != $this->stored_settings[ 'email' ] ) {
	printf( '<span id="speed-contact-bar-email">%s: <a href="mailto:%s">%s</a></span>', __( 'E-Mail', $this->plugin_slug ), esc_attr( $this->stored_settings[ 'email' ] ), esc_html( $this->stored_settings[ 'email' ] ) );
} // email
?>
</div>
<?php
// esc_url() should be used on all URLs, including those in the 'src' and 'href' attributes of an HTML element.
// that's all