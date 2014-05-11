<?php
/**
 * Speed Contact Bar.
 *
 * @package   Speed_Contact_Bar_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */

/**
 * @package Speed_Contact_Bar_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Speed_Contact_Bar_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected static $plugin_screen_hook_suffix = null;

	/**
	 * Name of this plugin.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected static $plugin_name = null;

	/**
	 * Unique identifier for this plugin.
	 *
	 * It is the same as in class Speed_Contact_Bar
	 * Has to be set here to be used in non-object context, e.g. callback functions
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected static $plugin_slug = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected static $settings_db_slug = null;

	/**
	 * Slug of the menu page on which to display the form sections
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	protected static $main_options_page_slug = 'scb_options_page';

	/**
	 * Group name of options
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	protected static $settings_fields_slug = 'scb_options_group';
	
	/**
	 * Structure of the form sections with headline, description and options
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	protected static $form_structure = null;

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	protected static $stored_settings = array();

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		// Call variables from public plugin class.
		$plugin = Speed_Contact_Bar::get_instance();
		self::$plugin_name = $plugin->get_plugin_name();
		self::$plugin_slug = $plugin->get_plugin_slug();
		self::$settings_db_slug = $plugin->get_settings_db_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . self::$plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_init', array( $this, 'register_options' ) );

		// get current or default settings
		self::$stored_settings = $plugin->get_stored_settings();

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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( self::$plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( self::$plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( self::$plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array( ), Speed_Contact_Bar::VERSION );
		}

		/* collect css for the color picker */
		#wp_enqueue_style( 'farbtastic' );
		wp_enqueue_style( 'wp-color-picker' );
 	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( self::$plugin_screen_hook_suffix ) ) {
			return;
		}

		/* collect js for the color picker */
		$screen = get_current_screen();
		if ( self::$plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( self::$plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Speed_Contact_Bar::VERSION );
		}
		#wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0
	 */
	public function add_plugin_admin_menu() {

		// Add a settings page for this plugin to the Settings menu.
		self::$plugin_screen_hook_suffix = add_options_page(
			sprintf( '%s %s', self::$plugin_name, __( 'Options', self::$plugin_slug ) ),
			self::$plugin_name,
			'manage_options',
			self::$plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . self::$plugin_slug ) . '">' . __( 'Settings' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	* Define and register the options
	* Run on admin_init()
	*
	* @since   1.0
	*/
	public static function register_options () {

		$title = null;
		$html = null;
		
		// define the form sections, order by appereance, with headlines, and options
		self::$form_structure = array(
			'1st_section' => array(
				'headline' => __( 'Contact Data', self::$plugin_slug ),
				'description' => __( 'Set the contact informations. To supress displaying a field leave it empty.', self::$plugin_slug ),
				'options' => array(
					'show_headline' => array(
						'type'    => 'checkbox',
						'title'   => __( 'Show headline', self::$plugin_slug ),
						'desc'    => __( 'Activate to show the headline', self::$plugin_slug ),
					),
					'headline' => array(
						'type'    => 'textfield',
						'title'   => __( 'Headline', self::$plugin_slug ),
						'desc'    => __( 'Enter a short headline for the contact bar', self::$plugin_slug ),
					),
					'email' => array(
						'type'    => 'email',
						'title'   => __( 'E-Mail', self::$plugin_slug ),
						'desc'    => __( 'Enter a valid email address. If the email address is invalid it will not be used.', self::$plugin_slug ),
					),
					'phone' => array(
						'type'    => 'textfield',
						'title'   => __( 'Phone Number', self::$plugin_slug ),
						'desc'    => __( 'Enter your official contact phone number', self::$plugin_slug ),
					),
					'cellphone' => array(
						'type'    => 'textfield',
						'title'   => __( 'Cell Phone Number', self::$plugin_slug ),
						'desc'    => __( 'Enter your official contact cell phone number', self::$plugin_slug ),
					),
					/*'contact form' => array(
						'type'    => 'url',
						'title'   => __( 'Contact Form URL', self::$plugin_slug ),
						'desc'    => __( 'Enter a valid URL. If the URL is invalid it will not be used.', self::$plugin_slug ),
					),*/
					'facebook' => array(
						'type'    => 'url',
						'title'   => __( 'Facebook Page URL', self::$plugin_slug ),
						'desc'    => __( 'Enter a valid URL. If the URL is invalid it will not be used.', self::$plugin_slug ),
					),
					'googleplus' => array(
						'type'    => 'url',
						'title'   => __( 'Google Plus Profile URL', self::$plugin_slug ),
						'desc'    => __( 'Enter a valid URL. If the URL is invalid it will not be used.', self::$plugin_slug ),
					),
				),
			),
			'2nd_section' => array(
				'headline' => __( 'Appeareance', self::$plugin_slug ),
				'description' => __( 'Set the style of the contact bar.', self::$plugin_slug ),
				'options' => array(
					'fixed' => array(
						'type'    => 'checkbox',
						'title'   => __( 'Enable fixed position', self::$plugin_slug ),
						'desc'    => __( 'Always on top', self::$plugin_slug ),
					),
					'content_alignment' => array(
						'type'    => 'selection',
						'title'   => __( 'Text Alignment', self::$plugin_slug ),
						'desc'    => __( 'Select the alignment of the content within the bar', self::$plugin_slug ),
						'values'  => array( 'left' => __( 'left-aligned', self::$plugin_slug ), 'center' => __( 'centered', self::$plugin_slug ), 'right' => __( 'right-aligned', self::$plugin_slug ) ),
					),
					'bg_color' => array(
						'type'    => 'colorpicker',
						'title'   => __( 'Background Color', self::$plugin_slug ),
						'desc'    => __( 'Select the background color', self::$plugin_slug ),
					),
					'text_color' => array(
						'type'    => 'colorpicker',
						'title'   => __( 'Text Color', self::$plugin_slug ),
						'desc'    => __( 'Select the text color', self::$plugin_slug ),
					),
					'link_color' => array(
						'type'    => 'colorpicker',
						'title'   => __( 'Link Color', self::$plugin_slug ),
						'desc'    => __( 'Select the link color', self::$plugin_slug ),
					),
					'icon_family' => array(
						'type'    => 'selection',
						'title'   => __( 'Icon Brightness', self::$plugin_slug ),
						'desc'    => __( 'Select the brightness of the icons', self::$plugin_slug ),
						'values'  => array( 'bright' => __( 'bright', self::$plugin_slug ), 'dark' => __( 'dark', self::$plugin_slug ) ),
					),
					'show_shadow' => array(
						'type'    => 'checkbox',
						'title'   => __( 'Show shadow', self::$plugin_slug ),
						'desc'    => __( 'Activate to show a slight shadow under the bar', self::$plugin_slug ),
					),
				),
			),
			#'3rd_section' => array(
			#),
		);
		// build form with sections and options
		foreach ( self::$form_structure as $section_key => $section_values ) {
		
			// assign callback functions to form sections (options groups)
			add_settings_section(
				// 'id' attribute of tags
				$section_key, 
				// title of the section.
				self::$form_structure[ $section_key ][ 'headline' ],
				// callback function that fills the section with the desired content
				array( __CLASS__, 'print_section_' . $section_key ),
				// menu page on which to display this section
				self::$main_options_page_slug
			); // end add_settings_section()
			
			// set labels and callback function names per option name
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				// set default description
				$desc = '';
				if ( isset( $option_values[ 'desc' ] ) and '' != $option_values[ 'desc' ] ) {
					if ( 'checkbox' == $option_values[ 'type' ] ) {
						$desc =  $option_values[ 'desc' ];
					} else {
						$desc =  sprintf( '<p class="description">%s</p>', $option_values[ 'desc' ] );
					}
				}
				// build the form elements values
				switch ( $option_values[ 'type' ] ) {
					case 'radiobuttons':
						$title = $option_values[ 'title' ];
						$stored_value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $title );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$checked = $stored_value ? checked( $stored_value, $value, false ) : '';
							$html .= sprintf( '<label><input type="radio" name="%s[%s]" value="%s"%s /> <span>%s</span></label><br />', self::$settings_db_slug, $option_name, $value, $checked, $label );
						}
						$html .= '</fieldset>';
						$html .= $desc;
						break;
					case 'checkboxes':
						$title = $option_values[ 'title' ];
						$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $title );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$stored_value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '0';
							$checked = $stored_value ? checked( '1', $value, false ) : '';
							$html .= sprintf( '<label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s /> %s</label><br />' , $value, self::$settings_db_slug, $value, $value, $checked, $label );
						}
						$html .= '</fieldset>';
						$html .= $desc;
						break;
					case 'selection':
						$title = $option_values[ 'title' ];
						$stored_value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<select id="%s" name="%s[%s]">', $option_name, self::$settings_db_slug, $option_name );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$selected = $stored_value ? selected( $stored_value, $value, false ) : '';
							$html .= sprintf( '<option value="%s"%s>%s</option>', $value, $selected, $label );
						}
						$html .= '</select>';
						$html .= $desc;
						break;
					case 'checkbox':
						$title = $option_values[ 'title' ];
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '0';
						$checked = $value ? checked( '1', $value, false ) : '';
						$html = sprintf( '<label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s /> %s</label>' , $option_name, self::$settings_db_slug, $option_name, $option_name, $checked, $desc );
						break;
					case 'url':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_url( self::$stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, self::$settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					case 'textarea':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_textarea( self::$stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<textarea id="%s" name="%s[%s]" cols="30" rows="5">%s</textarea>', $option_name, self::$settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					case 'farbtastic':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '#cccccc';
						$html = '<div class="farbtastic-container" style="position: relative;">';
						$html .= sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, self::$settings_db_slug, $option_name, $value );
						$html .= sprintf( '<div id="farbtastic-%s"></div></div>', $option_name );
						$html .= $desc;
						break;
					case 'colorpicker':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '#cccccc';
						$html = sprintf( '<input type="text" id="%s" class="wp-color-picker" name="%s[%s]" value="%s">', $option_name, self::$settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					// else text field
					default:
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( self::$stored_settings[ $option_name ] ) ? esc_attr( self::$stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, self::$settings_db_slug, $option_name, $value );
						$html .= $desc;
				} // end switch()

				// register the option
				add_settings_field(
					// form field name for use in the 'id' attribute of tags
					$option_name,
					// title of the form field
					$title,
					// callback function to print the form field
					array( __CLASS__, 'print_option' ),
					// menu page on which to display this field for do_settings_section()
					self::$main_options_page_slug,
					// section where the form field appears
					$section_key,
					// arguments passed to the callback function 
					array(
						'html' => $html,
					)
				); // end add_settings_field()

			} // end foreach( section_values )

		} // end foreach( section )

		// finally register all options. They will be stored in the database in the wp_options table under the options name self::$settings_db_slug.
		register_setting( 
			// group name in settings_fields()
			self::$settings_fields_slug,
			// name of the option to sanitize and save in the db
			self::$settings_db_slug,
			// callback function that sanitizes the option's value.
			array( __CLASS__, 'sanitize_options' )
		); // end register_setting()
		
	} // end register_options()

	/**
	* Check and return correct values for the settings
	*
	* @since   1.0
	*
	* @param   array    $input    Options and their values after submitting the form
	* 
	* @return  array              Options and their sanatized values
	*/
	public static function sanitize_options ( $input ) {
		foreach ( self::$form_structure as $section_name => $section_values ) {
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				switch ( $option_values[ 'type' ] ) {
					// if checkbox is set assign '1', else '0'
					case 'checkbox':
						$input[ $option_name ] = isset( $input[ $option_name ] ) ? 1 : 0 ;
						break;
					// clean email value
					case 'email':
						$email = sanitize_email( $input[ $option_name ] );
						$input[ $option_name ] = is_email( $email ) ? $email : '';
						break;
					// clean url values
					case 'url':
						$input[ $option_name ] = esc_url_raw( $input[ $option_name ] );
						break;
					// clean all other form elements values
					default:
						$input[ $option_name ] = sanitize_text_field( $input[ $option_name ] );
				} // end switch()
			} // foreach( options )
		} // foreach( sections )
		return $input;
	} // end sanitize_options()

	/**
	* Print the option
	*
	* @since   1.0
	*
	*/
	public static function print_option ( $args ) {
		print $args[ 'html' ];
	}

	/**
	* Print the explanation for section 1
	*
	* @since   1.0
	*/
	public static function print_section_1st_section () {
		printf( "<p>%s</p>\n", self::$form_structure[ '1st_section' ][ 'description' ] );
	}

	/**
	* Print the explanation for section 2
	*
	* @since   1.0
	*/
	public static function print_section_2nd_section () {
		printf( "<p>%s</p>\n", self::$form_structure[ '2nd_section' ][ 'description' ] );
	}

	/**
	* Print the explanation for section 3
	*
	* @since   1.0
	*/
	public static function print_section_3rd_section () {
		printf( "<p>%s</p>\n", self::$form_structure[ '3rd_section' ][ 'description' ] );
	}
	
}
