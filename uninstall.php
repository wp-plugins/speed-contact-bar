<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
* Delete options from the database while deleting the plugin files
* Run before deleting the plugin
*
* @since   1.0
*/
// remove settings
delete_option( 'speed-contact-bar-options' ); 
// clean DB
global $wpdb;
$wpdb->query( "OPTIMIZE TABLE `" .$wpdb->options. "`" );

