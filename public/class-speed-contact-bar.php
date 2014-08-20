<?php
/**
 * Speed Contact Bar.
 *
 * @package   Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */

/**
 * @package Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Speed_Contact_Bar {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0
	 *
	 * @var     string
	 */
	private $plugin_version = '1.9.1';

	/**
	 * Name of this plugin.
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_name = 'Speed Contact Bar';

	/**
	 * Unique identifier for this plugin.
	 *
	 *
	 * The variable is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_slug = 'speed-contact-bar';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $settings_db_slug = 'speed-contact-bar-options';

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $stored_settings = array();

	/**
	 * Social networks
	 *
	 *
	 * @since    1.5
	 *
	 * @var      array
	 */
	private $social_networks = array( 'facebook', 'googleplus', 'twitter', 'pinterest', 'youtube', 'linkedin', 'xing', 'flickr', 'slideshare', 'tumblr' );

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// load contact bar near closing html body element with styles
		add_filter( 'template_include', array( $this, 'activate_buffer' ), 1 );
		add_filter( 'shutdown', array( $this, 'include_contact_bar' ), 0 );
		add_action( 'wp_head', array( $this, 'display_bar_styles' ) );
		
		// get current or default settings
		$this->stored_settings = $this->get_stored_settings();

		// hook on displaying a message after plugin activation
		// if single activation via link or bulk activation
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			$plugin_was_activated = get_transient( 'speed-contact-bar' );
			if ( false !== $plugin_was_activated ) {
				add_action( 'admin_notices', array( $this, 'display_activation_message' ) );
				delete_transient( 'speed-contact-bar' );
			}
		}
	}

	/**
	 * Return the plugin version.
	 *
	 * @since    1.5
	 *
	 * @return    Plugin version variable.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Return the plugin name.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the options slug in the WP options table.
	 *
	 * @since    1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_settings_db_slug() {
		return $this->settings_db_slug;
	}

	/**
	 * Return the supported social networks
	 *
	 * @since    1.5
	 *
	 * @return    Social networks variable.
	 */
	public function get_social_networks() {
		return $this->social_networks;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
	
		$required_wp_version = '3.5';

		// check minimum version
		if ( ! version_compare( $GLOBALS['wp_version'], $required_wp_version, '>=' ) ) {
			// deactivate plugin
			deactivate_plugins( plugin_basename( __FILE__ ), false, is_network_admin() );
			// load language file for a message in the language of the WP installation
			self::load_plugin_textdomain();
			// stop WP request and display the message with backlink. Is there a proper way than wp_die()?
			wp_die( 
				// message in browser viewport
				sprintf( 
					'<p>%s</p>', 
					sprintf( 
						__( 'The plugin requires WordPress version %s or higher. Therefore, WordPress did not activate it. If you want to use this plugin update the Wordpress files to the latest version.', 'speed-contact-bar' ), 
						$required_wp_version 
					)
				),
				// title in title tag
				'Wordpress &rsaquo; Plugin Activation Error', 
				array( 
					// HTML status code returned
					'response'  => 200, 
					// display a back link in the returned page
					'back_link' => true 
				)
			);
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0
	 */
	private static function single_activate() {
		// store the flag into the db to trigger the display of a message after activation
		set_transient( 'speed-contact-bar', '1', 60 );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), $this->plugin_version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), $this->plugin_version );
	}

	/**
	 * Set default settings
	 *
	 * @since    1.0
	 */
	private function set_default_settings() {
		if ( ! current_user_can( 'manage_options' ) )  {
			// use WordPress standard message for this case
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		}
		
		// define the default settings
		$domain_name = preg_replace( '/^www\./', '', $_SERVER[ 'SERVER_NAME' ] );
		$default_settings = array(
			'fixed' => 1,
			'bg_color'  => '#dfdfdf',
			'text_color'  => '#333333',
			'link_color'  => '#0074A2',
			'icon_family'  => 'dark',
			'content_alignment'  => 'center',
			'show_headline'  => 1,
			'open_new_window'  => 0,
			'show_labels'  => 1,
			'show_shadow'  => 1,
			'headline'  => __( 'Contact to us', $this->plugin_slug ),
			'email'  => 'info@' . $domain_name,
			'phone'  => '',
			'cellphone'  => '',
			'contact form'  => '',
		);
		// add social networks to array
		foreach ( $this->social_networks as $name ) {
			$default_settings[ $name ] = '';
		}

		// store default values in the db as a single and serialized entry
		add_option( $this->settings_db_slug, $default_settings );
		
		/** 
		* to do: finish check
		* // test if the options are stored successfully
		* if ( false === get_option( self::$settings_db_slug ) ) {
		* 	// warn if there is something wrong with the options
		* 	something like: printf( '<div class="error"><p>%s</p></div>', __( 'The settings for plugin Purify WP Menus are not stored in the database. Is the database server ok?', 'purify_wp_menus' ) );
		* }
		*/
	}

	/**
	 * Get current or default settings
	 *
	 * @since    1.0
	 */
	public function get_stored_settings() {
		// try to load current settings. If they are not in the DB return set default settings
		$stored_settings = get_option( $this->settings_db_slug, array() );
		// if empty array set and store default values
		if ( empty( $stored_settings ) ) {
			$this->set_default_settings();
			// try to load current settings again. Now there should be the data
			$stored_settings = get_option( $this->settings_db_slug );
		}
		
		return $stored_settings; # todo: return $this->sanitize_options( $stored_settings );
	}
	
	/**
	 * For development: Display a var_dump() of the variable; die if true
	 *
	 * @since    1.0
	 */
	public static function dump ( $v, $die = false ) {
		print "<pre>";
		var_dump( $v );
		print "</pre>";
		if ( $die ) die();
	} // dump()

	/**
	 * Activate output buffer
	 *
	 * @since    1.0
	 */
	public function activate_buffer( $template ) {
		// activate output buffer
		ob_start();
		// return html without changes
		return $template;
	}

	/**
	 * Print the contact bar
	 *
	 * @since    1.0
	 */
	public function include_contact_bar() {
		if ( ! ( $this->is_login_page() or is_admin() ) ) {
			// get current buffer content and clean buffer
			$content = ob_get_clean(); 
			// esc_url() should be used on all URLs, including those in the 'src' and 'href' attributes of an HTML element.
			// the bar
			$inject = '<div id="scb-wrapper"';
			if ( 1 == $this->stored_settings[ 'fixed' ] ) { 
				$inject .= ' class="scb-fixed"'; 
			}
			$inject .= '>';
			// the headline
			if ( isset( $this->stored_settings[ 'headline' ] ) && '' != $this->stored_settings[ 'headline' ] ) {
				$inject .= sprintf( '<h2>%s</h2>', esc_html( $this->stored_settings[ 'headline' ] ) );
			}
			// the icon family
			$icon_family = 'dark';
			if ( isset( $this->stored_settings[ 'icon_family' ] ) && '' != $this->stored_settings[ 'icon_family' ] ) {
				//if ( isset( $this->stored_settings[ 'show_labels' ] ) && 1 == $this->stored_settings[ 'show_labels' ] ) {}
				$icon_family = sanitize_file_name( $this->stored_settings[ 'icon_family' ] );
			}

			// the contact data
			$contact_list = array();
			$root_url = plugin_dir_url( __FILE__ );
			if ( isset( $this->stored_settings[ 'phone' ] ) && '' != $this->stored_settings[ 'phone' ] ) {
				$contact_list[] = sprintf( '<li id="scb-phone"><img src="%sassets/images/phone_%s.svg" width="26" height="26" alt="%s" />%s</li>', $root_url, $icon_family, __( 'Phone Number', $this->plugin_slug ), esc_html( $this->stored_settings[ 'phone' ] ) );
			}
			if ( isset( $this->stored_settings[ 'cellphone' ] ) && '' != $this->stored_settings[ 'cellphone' ] ) {
				$contact_list[] = sprintf( '<li id="scb-cellphone"><img src="%sassets/images/cellphone_%s.svg" width="26" height="26" alt="%s" />%s</li>', $root_url, $icon_family, __( 'Cell Phone Number', $this->plugin_slug ), esc_html( $this->stored_settings[ 'cellphone' ] ) );
			}
			if ( isset( $this->stored_settings[ 'email' ] ) && '' != $this->stored_settings[ 'email' ] ) {
				$safe_email = antispambot( esc_html( $this->stored_settings[ 'email' ] ) );
				$contact_list[] = sprintf( '<li id="scb-email"><img src="%sassets/images/email_%s.svg" width="26" height="26" alt="%s" /> <a href="mailto:%s">%s</a></li>', $root_url, $icon_family, __( 'E-Mail', $this->plugin_slug ), $safe_email, $safe_email );
			}
			if ( ! empty( $contact_list ) ) {
				// opens list
				$inject .= '<ul class="elastic">';
				// write the contact data item
				$inject .= implode( "", $contact_list );
				// closes list
				$inject .= '</ul>';
			}

			// the socia media data
			$target = '';
			if ( isset( $this->stored_settings[ 'open_new_window' ] ) && 1 == $this->stored_settings[ 'open_new_window' ] ) {
				$target = ' target="_blank"';
			}
			$contact_list = array();
			foreach ( $this->social_networks as $icon ) {
				if ( isset( $this->stored_settings[ $icon ] ) && '' != $this->stored_settings[ $icon ] ) {
					$contact_list[] = sprintf( '<li id="scb-%s"><a href="%s"%s><img src="%sassets/images/%s.svg" width="26" height="26" alt="%s" /></a></li>', $icon, esc_url( $this->stored_settings[ $icon ] ), $target, $root_url, $icon, ucfirst( $icon ) );
				}
			}
			if ( ! empty( $contact_list ) ) {
				// opens list
				$inject .= '<ul>';
				// write the contact data item
				$inject .= implode( "", $contact_list );
				// closes list
				$inject .= '</ul>';
			}

			// closes bar
			$inject .= '</div>';
			// finds opening body element and add contact bar html code after it
			$content = preg_replace('/<[bB][oO][dD][yY]([^>]*)>/',"<body$1>{$inject}", $content);
			// display it
			echo $content;
		}
	}

	/**
	 * Print a message after plugin activation
	 * 
	 * @since    1.0
	 */
	public function display_activation_message () {
		$url  = esc_url( admin_url( sprintf( 'options-general.php?page=%s', 'speed-contact-bar' ) ) );
		$link = sprintf( '<a href="%s">%s =&gt; %s</a>', $url, __( 'Settings' ), $this->plugin_name );
		$msg  = sprintf( __( 'Welcome to the plugin %s! You can configure it at %s.', 'speed-contact-bar' ), $this->plugin_name, $link );
		$html = sprintf( '<div class="updated"><p>%s</p></div>', $msg );
		print $html;
	}

	/**
	 * Returns whether user is on the login page
	 * 
	 * @since    1.0
	 */
	private function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php', 'wp-signup.php' ) );
	}

	/**
	 * Print the customized CSS block
	 * 
	 * @since    1.0
	 */
	public function display_bar_styles() {
		$content = '<style media="screen" type="text/css">';
		if ( 1 == $this->stored_settings[ 'fixed' ] ) { 
			/*<style type="text/css">
			html { margin-top: 32px !important; }
			* html body { margin-top: 32px !important; }
			@media screen and ( max-width: 782px ) {
			html { margin-top: 46px !important; }
			* html body { margin-top: 46px !important; }
			}
			</style>*/
			$content .= '@media screen and (min-width: 640px) { body { padding-top: 2.5em !important; } }';
		}
		/* styles of the bar */
		$bar_styles = '';
		$headline_styles = '';
		if ( isset( $this->stored_settings[ 'bg_color' ] ) && '' != $this->stored_settings[ 'bg_color' ] ) { 
			$bar_styles .= sprintf( ' background-color: %s;', esc_attr( $this->stored_settings[ 'bg_color' ] ) ); 
		}
		if ( isset( $this->stored_settings[ 'text_color' ] ) && '' != $this->stored_settings[ 'text_color' ] ) { 
			$color = esc_attr( $this->stored_settings[ 'text_color' ] );
			$bar_styles 	 .= sprintf( ' color: %s;', $color ); 
			$headline_styles .= sprintf( ' color: %s;', $color ); 
		}
		if ( isset( $this->stored_settings[ 'content_alignment' ] ) && '' != $this->stored_settings[ 'content_alignment' ] ) { 
			$bar_styles .= sprintf( ' text-align: %s;', esc_attr( $this->stored_settings[ 'content_alignment' ] ) ); 
		}
		if ( isset( $this->stored_settings[ 'show_shadow' ] ) && '' != $this->stored_settings[ 'show_shadow' ] ) { 
			$bar_styles .= ' box-shadow: 0 1px 6px 3px #CCCCCC;'; 
		}
		$link_styles = '';
		if ( isset( $this->stored_settings[ 'link_color' ] ) && '' != $this->stored_settings[ 'link_color' ] ) { 
			$link_styles .= sprintf( ' color: %s;', esc_attr( $this->stored_settings[ 'link_color' ] ) ); 
		}
		if ( $bar_styles ) {
			$content .= sprintf( '#scb-wrapper {%s } ', $bar_styles );
		}
		if ( $headline_styles ) {
			$content .= sprintf( '#scb-wrapper h2 {%s } ', $headline_styles );
		}
		if ( $link_styles ) {
			$content .= sprintf( '#scb-wrapper a {%s } ', $link_styles );
		}
		/* styles of the content */
		if ( isset( $this->stored_settings[ 'show_headline' ] ) && 0 == $this->stored_settings[ 'show_headline' ] ) {
			$content .= '#scb-wrapper h2 { display: inline; left: -32768px; margin: 0; padding: 0; position: absolute; top: 0; z-index: 1000; } ';
		}
		$content .= '</style>';
		$content .= '<style media="print" type="text/css">#scb-wrapper { display:none; }</style>';
		echo $content;
	}

}
