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
	private $plugin_version = null;

	/**
	 * Name of this plugin.
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_name = null;

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
	private $plugin_slug = null;

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
	private $settings_db_slug = null;

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $stored_settings = null;

	/**
	 * Allowed social networks
	 *
	 *
	 * @since    1.5
	 *
	 * @var      array
	 */
	private $valid_social_networks = null;

	/**
	 * Allowed headline HTML tags
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_headline_tags = null;

	/**
	 * Allowed icon families
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_icon_types = null;

	/**
	 * Allowed content alignments
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_content_alignments = null;

	/**
	 * Allowed font sizes
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_font_sizes = null;

	/**
	 * Allowed icon sizes
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $valid_icon_sizes = null;

	/**
	 * Allowed adjustment values
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_readjustments = null;

	/**
	 * Allowed vertical paddings
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_vertical_paddings = null;

	/**
	 * Allowed horizontal paddings
	 *
	 *
	 * @since    2.1
	 *
	 * @var      array
	 */
	private $valid_horizontal_paddings = null;

	/**
	 * Initial settings
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	private $default_settings = null;
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
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// load contact bar near closing html body element with styles
		add_filter( 'template_include', array( $this, 'activate_buffer' ), 1 );
		add_filter( 'shutdown', array( $this, 'include_contact_bar' ), 0 );
		add_action( 'wp_head', array( $this, 'display_bar_styles' ) );

		// set default values
		$this->plugin_version = '2.5';
		$this->plugin_name = 'Speed Contact Bar';
		$this->plugin_slug = 'speed-contact-bar';
		$this->settings_db_slug = 'speed-contact-bar-options';
		$this->stored_settings = array();
		$this->valid_social_networks = array( 'facebook', 'googleplus', 'twitter', 'pinterest', 'youtube', 'linkedin', 'xing', 'flickr', 'slideshare', 'tumblr', 'vimeo', 'imdb', 'instagram', 'yelp' );
		$this->valid_headline_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' );
		$this->valid_icon_types =  array( 'bright', 'dark' );
		$this->valid_content_alignments =  array( 'left', 'center', 'right' );
		$this->valid_font_sizes =  range( 8, 24 );
		$this->valid_icon_sizes =  range( 16, 48, 2 );
		$this->valid_readjustments =  range( 25, 75, 5 );
		$this->valid_vertical_paddings =  range( 8, 32 );
		$this->valid_horizontal_paddings =  range( 8, 32 );
		$this->default_settings = array(
			'fixed' => 1,
			'bg_transparent'  => 0,
			'bg_color'  => '#dfdfdf',
			'text_color'  => '#333333',
			'link_color'  => '#0074A2',
			'icon_type'  => 'dark',
			'content_alignment'  => 'center',
			'show_headline'  => 1,
			'open_new_window'  => 0,
			'show_labels'  => 1,
			'show_shadow'  => 1,
			'show_texts'  => 0,
			'font_size'  => 15,
			'icon_size'  => 30,
			'readjustment'  => 35,
			'vertical_padding'  => 15,
			'horizontal_padding'  => 15,
			'headline_tag'  => 'h2',
			'headline'  => 'Contact to us',
			'email'  => 'info@yourdomain.com',
			'phone'  => '',
			'cellphone'  => '',
			'contact form'  => '',
		);
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
		return $this->valid_social_networks;
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
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";

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
		
		// enhance the default settings
		$domain_name = preg_replace( '/^www\./', '', $_SERVER[ 'SERVER_NAME' ] );
		$this->default_settings[ 'email' ] = 'info@' . $domain_name;
		$this->default_settings[ 'headline' ] = __( 'Contact to us', $this->plugin_slug );

		// store default values in the db as a single and serialized entry
		update_option( $this->settings_db_slug, $this->default_settings );
		
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
		$settings = get_option( $this->settings_db_slug, array() );
		// sanitize: if key is not available, use default
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			foreach( $this->default_settings as $key => $default_value ) {
				$settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : $default_value;
			}
		} else {
			// set default settings
			$this->set_default_settings();
			// try to load current settings again. Now there should be the data
			$settings = get_option( $this->settings_db_slug );
		}
		
		return $settings;
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
			// open the bar
			$inject = '<div id="scb-wrapper"';

			// fixation of the bar
			if ( isset( $this->stored_settings[ 'fixed' ] ) && 1 == $this->stored_settings[ 'fixed' ] ) { 
				$inject .= ' class="scb-fixed"'; 
			}
			$inject .= '>';

			// the headline
			$headline_tag = $this->default_settings[ 'headline_tag' ];
			if ( isset( $this->stored_settings[ 'headline_tag' ] ) && in_array( $this->stored_settings[ 'headline_tag' ], $this->valid_headline_tags ) ) {
				$headline_tag = $this->stored_settings[ 'headline_tag' ];
			}
			if ( isset( $this->stored_settings[ 'headline' ] ) && '' != $this->stored_settings[ 'headline' ] ) {
				$inject .= sprintf(
					'<%s>%s</%s>',
					$headline_tag,
					esc_html( $this->stored_settings[ 'headline' ] ),
					$headline_tag
				);
			}

			// icon size
			$icon_size = $this->default_settings[ 'icon_size' ];
			if ( isset( $this->stored_settings[ 'icon_size' ] ) && in_array( $this->stored_settings[ 'icon_size' ], $this->valid_icon_sizes ) ) { 
				$icon_size = $this->stored_settings[ 'icon_size' ];
			}

			// icon type
			$icon_type = $this->default_settings[ 'icon_type' ];
			if ( isset( $this->stored_settings[ 'icon_type' ] ) && in_array( $this->stored_settings[ 'icon_type' ], $this->valid_icon_types ) ) {
				$icon_type = $this->stored_settings[ 'icon_type' ];
			}

			// the contact data
			$contact_list = array();
			$root_url = plugin_dir_url( __FILE__ );
			if ( isset( $this->stored_settings[ 'phone' ] ) && '' != $this->stored_settings[ 'phone' ] ) {
				$phone_number = esc_html( $this->stored_settings[ 'phone' ] );
				$phone_number_escaped = $this->esc_phonenumber( $this->stored_settings[ 'phone' ] );
				$contact_list[] = sprintf( 
					'<li id="scb-phone"><a href="tel:%s"><img src="%sassets/images/phone_%s.svg" width="%d" height="%d" alt="%s" />&nbsp;<span>%s</span></a></li>',
					$phone_number_escaped,
					$root_url,
					$icon_type,
					$icon_size,
					$icon_size,
					__( 'Phone Number', $this->plugin_slug ),
					$phone_number
				);
			}
			if ( isset( $this->stored_settings[ 'cellphone' ] ) && '' != $this->stored_settings[ 'cellphone' ] ) {
				$phone_number = esc_html( $this->stored_settings[ 'cellphone' ] );
				$phone_number_escaped = $this->esc_phonenumber( $this->stored_settings[ 'cellphone' ] );
				$contact_list[] = sprintf(
					'<li id="scb-cellphone"><a href="tel:%s"><img src="%sassets/images/cellphone_%s.svg" width="%d" height="%d" alt="%s" />&nbsp;<span>%s</span></a></li>',
					$phone_number_escaped,
					$root_url,
					$icon_type,
					$icon_size,
					$icon_size,
					__( 'Cell Phone Number', $this->plugin_slug ),
					$phone_number
				);
			}
			if ( isset( $this->stored_settings[ 'email' ] ) && '' != $this->stored_settings[ 'email' ] ) {
				$safe_email = antispambot( esc_html( $this->stored_settings[ 'email' ] ) );
				$contact_list[] = sprintf(
					'<li id="scb-email"><a href="mailto:%s"><img src="%sassets/images/email_%s.svg" width="%d" height="%d" alt="%s" />&nbsp;<span>%s</span></a></li>',
					$safe_email,
					$root_url,
					$icon_type,
					$icon_size,
					$icon_size,
					__( 'E-Mail', $this->plugin_slug ),
					$safe_email 
				);
			}
			if ( ! empty( $contact_list ) ) {
				// opens list
				$inject .= '<ul id="scb-directs">';
				// write the contact data item
				$inject .= implode( "", $contact_list );
				// closes list
				$inject .= '</ul>';
			}

			// socia media icons
			$target = '';
			if ( isset( $this->stored_settings[ 'open_new_window' ] ) && 1 == $this->stored_settings[ 'open_new_window' ] ) {
				$target = ' target="_blank"';
			}
			$contact_list = array();
			$pngs = array( 'imdb', 'yelp' ); // PNG image file names
			foreach ( $this->valid_social_networks as $icon ) {
				if ( in_array( $icon, $pngs ) && isset( $this->stored_settings[ $icon ] ) && '' != $this->stored_settings[ $icon ] ) {
					$contact_list[] = sprintf( 
						'<li id="scb-%s"><a href="%s"%s><img src="%sassets/images/%s.png" width="%d" height="%d" alt="%s" /></a></li>',
						$icon,
						esc_url( $this->stored_settings[ $icon ] ),
						$target,
						$root_url,
						$icon,
						$icon_size * 2,
						$icon_size * 2,
						ucfirst( $icon ) 
					);
				} else {
					if ( isset( $this->stored_settings[ $icon ] ) && '' != $this->stored_settings[ $icon ] ) {
						$contact_list[] = sprintf( 
							'<li id="scb-%s"><a href="%s"%s><img src="%sassets/images/%s.svg" width="%d" height="%d" alt="%s" /></a></li>',
							$icon,
							esc_url( $this->stored_settings[ $icon ] ),
							$target,
							$root_url,
							$icon,
							$icon_size,
							$icon_size,
							ucfirst( $icon ) 
						);
					}
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

			// close bar
			$inject .= '</div>';

			// finds opening body element and add contact bar html code after it
			$content = preg_replace('/<[bB][oO][dD][yY]([^>]*)>/',"<body$1>{$inject}", $content);

			// display it
			echo $content;
		}
	}

	/**
	 * Print the customized CSS block
	 * 
	 * @since    1.0
	 */
	public function display_bar_styles() {
		$content = '<style media="screen" type="text/css">';
		$content .= "\n";
		$content .= '#scb-wrapper ul,#scb-wrapper li,#scb-wrapper a {display:inline;margin:0;padding:0;font-family:sans-serif;font-size:0.96em;line-height:1;} #scb-wrapper li {margin:0 .5em;} #scb-wrapper img {display:inline;vertical-align:middle;margin:0;padding:0;border:0 none;} #scb-wrapper #scb-email {padding-right:1em;}';
		$content .= '#scb-wrapper li a span {white-space:nowrap;}';
		$content .= "\n";
		$content .= '@media screen and (min-width:640px) {#scb-wrapper.scb-fixed {position:fixed;top:0;left:0;z-index:10000;width:100%;}}';
		$content .= "\n";
		// if checked show email address and phone numbers in small displays
		if ( isset( $this->stored_settings[ 'show_texts' ] ) && 1 == $this->stored_settings[ 'show_texts' ] ) { 
			// show them without inline breaks and one below the other
			$content .= '@media screen and (max-width:480px) {#scb-wrapper #scb-directs li {margin-bottom:.5em;display:block;} #scb-wrapper ul {display:block;}} #scb-wrapper #scb-directs a {white-space:nowrap;}';
		} else {
			// hide them, show icon only
			$content .= '@media screen and (max-width:768px) {#scb-wrapper #scb-phone span,#scb-wrapper #scb-cellphone span,#scb-wrapper #scb-email span {display:none;}}';
		}
		$content .= "\n";
		$content .= '@media screen and (max-width:480px) {#scb-wrapper #scb-directs {margin-bottom:.5em;} #scb-wrapper ul {display:block;}}';
		$content .= "\n";
		/* fixation of bar */
		if ( isset( $this->stored_settings[ 'fixed' ] ) && 1 == $this->stored_settings[ 'fixed' ] ) { 
			/*<style type="text/css">
			html { margin-top: 32px !important; }
			* html body { margin-top: 32px !important; }
			@media screen and ( max-width: 782px ) {
			html { margin-top: 46px !important; }
			* html body { margin-top: 46px !important; }
			}
			</style>*/
			/* space between bar and page content */
			$readjustment = $this->default_settings[ 'readjustment' ];
			if ( isset( $this->stored_settings[ 'readjustment' ] ) && '' != $this->stored_settings[ 'readjustment' ] ) { 
				$readjustment = esc_attr( $this->stored_settings[ 'readjustment' ] ); 
			}
			$content .= sprintf( '@media screen and (min-width: 640px) { body { padding-top: %dpx !important; } }', $readjustment );
			$content .= "\n";
		}
		
		/* styles of the bar and headline */
		$bar_styles = '';

		$vertical_padding = $this->default_settings[ 'vertical_padding' ];
		if ( isset( $this->stored_settings[ 'vertical_padding' ] ) && in_array( $this->stored_settings[ 'vertical_padding' ], $this->valid_vertical_paddings ) ) { 
			$vertical_padding = absint( $this->stored_settings[ 'vertical_padding' ] ); 
		}
		$horizontal_padding = $this->default_settings[ 'horizontal_padding' ];
		if ( isset( $this->stored_settings[ 'horizontal_padding' ] ) && in_array( $this->stored_settings[ 'horizontal_padding' ], $this->valid_horizontal_paddings ) ) { 
			$horizontal_padding = absint( $this->stored_settings[ 'horizontal_padding' ] ); 
		}
		$bar_styles .= sprintf( ' padding: %spx %spx;', $vertical_padding, $horizontal_padding ); 

		if ( isset( $this->stored_settings[ 'bg_transparent' ] ) && 1 == $this->stored_settings[ 'bg_transparent' ] ) { 
			$bar_styles .= ' background-color: transparent;'; 
		} else {
			$bg_color = $this->default_settings[ 'bg_color' ];
			if ( isset( $this->stored_settings[ 'bg_color' ] ) && '' != $this->stored_settings[ 'bg_color' ] ) { 
				$bg_color = esc_attr( $this->stored_settings[ 'bg_color' ] ); 
			}
			$bar_styles .= sprintf( ' background-color: %s;', $bg_color ); 
		}

		$text_color = $this->default_settings[ 'text_color' ];
		if ( isset( $this->stored_settings[ 'text_color' ] ) && '' != $this->stored_settings[ 'text_color' ] ) { 
			$text_color = esc_attr( $this->stored_settings[ 'text_color' ] );
		}
		$bar_styles .= sprintf( ' color: %s;', $text_color ); 
		$headline_color = sprintf( ' color: %s;', $text_color ); 

		$content_alignment = $this->default_settings[ 'content_alignment' ];
		if ( isset( $this->stored_settings[ 'content_alignment' ] ) && in_array( $this->stored_settings[ 'content_alignment' ], $this->valid_content_alignments ) ) { 
			$content_alignment = esc_attr( $this->stored_settings[ 'content_alignment' ] ); 
		}
		$bar_styles .= sprintf( ' text-align: %s;', $content_alignment ); 

		if ( isset( $this->stored_settings[ 'show_shadow' ] ) && 1 == $this->stored_settings[ 'show_shadow' ] ) { 
			$bar_styles .= ' box-shadow: 0 1px 6px 3px #ccc;'; 
		}

		$content .= sprintf( '#scb-wrapper {%s } ', $bar_styles );
		$content .= "\n";
		
		/* styles of headline */
		$headline_tag = $this->default_settings[ 'headline_tag' ];
		if ( isset( $this->stored_settings[ 'headline_tag' ] ) && in_array( $this->stored_settings[ 'headline_tag' ], $this->valid_headline_tags ) ) {
			$headline_tag = $this->stored_settings[ 'headline_tag' ];
		}
		$content .= sprintf( '#scb-wrapper %s { display: inline; margin: 0; padding: 0; font: normal normal bold 15px/1 sans-serif; ', $headline_tag );
		$content .= $headline_color;
		$content .= ' }';
		$content .= "\n";

		/* hide headline in tablets and smartphones */
		$content .= sprintf( '@media screen and (max-width: 768px) { #scb-wrapper %s { display: none; } }', $headline_tag );
		$content .= "\n";

		/* color of links */
		$link_color = $this->default_settings[ 'link_color' ];
		if ( isset( $this->stored_settings[ 'link_color' ] ) && '' != $this->stored_settings[ 'link_color' ] ) { 
			$link_color = esc_attr( $this->stored_settings[ 'link_color' ] ); 
		}
		$content .= sprintf( '#scb-wrapper a { color: %s; } ', $link_color );
		$content .= "\n";

		/* size of text */
		$font_size = $this->default_settings[ 'font_size' ];
		if ( isset( $this->stored_settings[ 'font_size' ] ) && in_array( $this->stored_settings[ 'font_size' ], $this->valid_font_sizes ) ) { 
			$font_size = $this->stored_settings[ 'font_size' ]; 
		}
		$content .= sprintf( '#scb-wrapper %s, #scb-wrapper ul, #scb-wrapper li, #scb-wrapper a { font-size: %dpx; } ', $headline_tag, $font_size );
		$content .= "\n";

		/* headline visibility */
		if ( isset( $this->stored_settings[ 'show_headline' ] ) && 0 == $this->stored_settings[ 'show_headline' ] ) {
			$content .= sprintf( '#scb-wrapper %s { display: inline; left: -32768px; margin: 0; padding: 0; position: absolute; top: 0; z-index: 1000; } ', $headline_tag );
			$content .= "\n";
		}
		
		/* close style block */
		$content .= '</style>';
		$content .= "\n";

		/* add print style block */
		$content .= '<style media="print" type="text/css">#scb-wrapper { display:none; }</style>';

		/* print css */
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
	 * Returns whether user is on the login page
	 * 
	 * @since    2.3
	 */
	private function esc_phonenumber ( $tel ) {
		
		// only strings
		if ( ! is_string( $tel ) ) {
			return '';
		}

		// remove invalid chars
		$tel = preg_replace( '/[^0-9a-z+]/i', '', $tel );
		
		// remove plus sign within the number, only keep it at the start
		$tel_first_sign = substr( $tel, 0, 1 );
		$tel_substr = substr( $tel, 1 );
		$tel_substr = preg_replace( '/[+]/', '', $tel_substr );
		$tel = $tel_first_sign . $tel_substr;

		// convert vanity numbers
		if ( preg_match( '/[a-z]/i', $tel ) ) {
			#$tel = preg_replace( '/ /', '0', $tel );
			$tel = preg_replace( '/[abc]/i', '2', $tel );
			$tel = preg_replace( '/[def]/i', '3', $tel );
			$tel = preg_replace( '/[ghi]/i', '4', $tel );
			$tel = preg_replace( '/[jkl]/i', '5', $tel );
			$tel = preg_replace( '/[mno]/i', '6', $tel );
			$tel = preg_replace( '/[pqrs]/i', '7', $tel );
			$tel = preg_replace( '/[tuv]/i', '8', $tel );
			$tel = preg_replace( '/[wxyz]/i', '9', $tel );
		}

		// E.164: maximum number of digits: 15
		$tel = substr( $tel, 0, 15 );
		
		// change country area sign
		$tel = preg_replace( '|^[+]|i', '00', $tel );

		// return sanitized phone number
		return $tel;
		
	}

}
