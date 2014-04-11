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
	const VERSION = '1.0';

	/**
	 * Lowest Wordpress version to run with this plugin
	 *
	 * @since   1.0
	 *
	 * @var     string
	 */
	const REQUIRED_WP_VERSION = '3.4'; /* because of color wheel picker 'farbtastic' */

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
	 * Initial and default settings for the plugin's start
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $default_settings = array();
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
		
		// load contact bar near closing html body element
		#add_action( 'wp_footer', array( $this, 'print_contact_bar' ) );
		add_filter( 'template_include', array( $this, 'activate_buffer' ), 1 );
		add_filter( 'shutdown', array( $this, 'include_contact_bar' ), 0 );
		
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

		// check minimum version
		if ( ! version_compare( $GLOBALS['wp_version'], self::REQUIRED_WP_VERSION, '>=' ) ) {
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
						self::REQUIRED_WP_VERSION 
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

		$domain = $this->plugin_slug;
		#$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		#load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {
		if ( 1 == $this->stored_settings[ 'enable' ] ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
			if ( 1 == $this->stored_settings[ 'fixed' ] ) {
				add_action( 'body_class', array( $this, 'add_body_padding' ) );
			}
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
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
		
		$this->default_settings = array(
			'enable' => 1,
			'fixed' => 1,
			'bg_color'  => '#dfdfdf',
			'email'  => 'me@work.site',
			'phone'  => '+49 98 / 76 54 321',
			'headline'  => __( 'Contact to us', $this->plugin_slug ),
		);
		// store default values in the db as a single and serialized entry
		add_option( $this->settings_db_slug, $this->default_settings );
		
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
		}
		// try to load current settings again. Now there should be the data
		$stored_settings = get_option( $this->settings_db_slug );
		
		return $stored_settings;
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

	function activate_buffer( $template ) {
		// activate output buffer
		ob_start();
		// return html without changes
		return $template;
	}

	function include_contact_bar() {
		// get current buffer content and clean buffer
		$content = ob_get_clean(); 
		// only display contact bar if user selected 'enable'
		if ( 1 == $this->stored_settings[ 'enable' ] ) {
			$inject = '<div id="scb-wrapper"';
			/*if ( 1 == $this->stored_settings[ 'fixed' ] ) { 
				$inject .= ' class="speed-contact-bar-fixed"'; 
			}*/
			if ( '' != $this->stored_settings[ 'bg_color' ] ) { 
				$inject .= sprintf( ' style="background-color: %s;"', esc_attr( $this->stored_settings[ 'bg_color' ] ) ); 
			}
			$inject .= '>';
			if ( '' != $this->stored_settings[ 'headline' ] ) {
				$inject .= sprintf( '<h2 id="speed-contact-bar-headline">%s</h2>', esc_html( $this->stored_settings[ 'headline' ] ) );
			}
			$inject .= '<dl>';
			if ( '' != $this->stored_settings[ 'phone' ] ) {
				$inject .= sprintf( '<dt id="speed-contact-bar-phone">%s: </dt><dd>%s</dd>', __( 'Phone', $this->plugin_slug ), esc_html( $this->stored_settings[ 'phone' ] ) );
			}
			if ( '' != $this->stored_settings[ 'email' ] ) {
				$inject .= sprintf( '<dt id="speed-contact-bar-email">%s: </dt><dd><a href="mailto:%s">%s</a></dd>', __( 'E-Mail', $this->plugin_slug ), esc_attr( $this->stored_settings[ 'email' ] ), esc_html( $this->stored_settings[ 'email' ] ) );
			}
			$inject .= '</dl></div>';
			// esc_url() should be used on all URLs, including those in the 'src' and 'href' attributes of an HTML element.
			// find opening body element and add contact bar html code after it
			$content = preg_replace('/<[bB][oO][dD][yY]([^>]*)>/',"<body$1>{$inject}", $content);
		}
		echo $content;
	}

	/**
	 * Print a message after plugin activation
	 * 
	 * @since    1.0
	 */
	public function display_activation_message () {
		$url  = esc_url( admin_url( sprintf( 'options-general.php?page=%s', 'speed-contact-bar' ) ) );
		$link = sprintf( '<a href="%s">%s =&gt; %s</a>', $url, __( 'Settings' ), $this->plugin_name );
		$msg  = sprintf( __( 'Welcome to %s! You can find the plugin at %s.', 'speed-contact-bar' ), $this->plugin_name, $link );
		$html = sprintf( '<div class="updated"><p>%s</p></div>', $msg );
		print $html;
	}

	/**
	 * Add the custom body class 'scb-fixed'
	 * 
	 * @since    1.0
	 */
	public function add_body_padding( $classes ) {
	  $classes[] = 'scb-fixed';
	  return $classes;
	}
}
