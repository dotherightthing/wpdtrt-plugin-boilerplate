<?php
/**
 * File: src/Shortcode.php
 *
 * Plugin shortcode class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_14;

if ( ! class_exists( 'Shortcode' ) ) {

	/**
	 * Class: Shortcode
	 *
	 * Plugin Shortcode base class.
	 *
	 * Note:
	 * - Contains boilerplate functions, including
	 *   options support, template loading, access to Plugin methods.
	 *
	 * Uses:
	 * - ../../../../wp-includes/shortcodes.php
	 *
	 * See:
	 * - <https://codex.wordpress.org/Function_Reference/add_shortcode>
	 * - <https://codex.wordpress.org/Shortcode_API#Enclosing_vs_self-closing_shortcodes>
	 * - <http://php.net/manual/en/function.ob-start.php>
	 * - <http://php.net/manual/en/function.ob-get-clean.php>
	 *
	 * Since:
	 *   1.0.0
	 */
	class Shortcode {

		/**
		 * Constructor: __construct
		 *
		 * Hook the plugin in to WordPress.
		 *
		 * Note:
		 * - This constructor automatically initialises the object's properties
		 *   when it is instantiated.
		 * - This is a public method as every plugin uses a new instance.
		 *
		 * Example:
		 * --- php
		 * $wpdtrt_test_shortcode = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_14\Shortcode {}
		 * ---
		 *
		 * Parameters:
		 *   $options - Shortcode options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function __construct( array $options ) {

			// define variables.
			$name                      = null;
			$plugin                    = null;
			$template                  = null;
			$selected_instance_options = null;

			// extract variables.
			extract( $options, EXTR_IF_EXISTS );

			// Store a reference to the partner plugin object,
			// which stores global plugin options.
			$this->set_plugin( $plugin );
			$this->set_template_name( $template );

			$shortcode_instance_options = array();

			$plugin_instance_options = $plugin->get_instance_options();

			foreach ( $selected_instance_options as $option_name ) {
				$shortcode_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
			}

			$this->set_instance_options( $shortcode_instance_options );

			$this->set_name( $name ); // for render_shortcode.

			// hook in to WordPress.
			$this->wp_setup();
		}

		/**
		 * Group: WordPress Integration
		 * _____________________________________
		 */

		/**
		 * Method: wp_setup
		 *
		 * Initialise shortcode options ONCE.
		 *
		 * Since:
		 *   1.4.16
		 */
		protected function wp_setup() {
			$name = $this->get_name();

			if ( shortcode_exists( $name ) ) {
				remove_shortcode( $name ); // supports replacement of core shortcodes.
			}

			add_shortcode( $name, array( $this, 'render_shortcode' ) );
		}

		/**
		 * Group: Setters and Getters
		 * _____________________________________
		 */

		/**
		 * Method: set_template_name
		 *
		 * Set the template name.
		 *
		 * Parameters:
		 *   $template_name - Template name
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_template_name( string $template_name ) {
			$this->template_name = $template_name;
		}

		/**
		 * Method: get_template_name
		 *
		 * Get the template name.
		 *
		 * Returns:
		 *   Template name
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function get_template_name() : string {
			return $this->template_name;
		}

		/**
		 * Method: get_name
		 *
		 * Get the value of $name.
		 *
		 * Returns:
		 *   Name
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_name() : string {
			return $this->name;
		}

		/**
		 * Method: set_name
		 *
		 * Set the value of $name.
		 *
		 * Parameters:
		 *   $new_name - New name
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_name( string $new_name ) {
			$this->name = $new_name;
		}

		/**
		 * Method: get_instance_options
		 *
		 * Get default options.
		 *
		 * Returns:
		 *   Instance options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_instance_options() : array {
			return $this->instance_options;
		}

		/**
		 * Method: set_instance_options
		 *
		 * Set instance options.
		 *
		 * Parameters:
		 *   $instance_options - Instance options
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_instance_options( array $instance_options ) {
			$this->instance_options = $instance_options;
		}

		/**
		 * Method: set_plugin
		 *
		 * Set parent plugin, which contains shortcode/widget options.
		 *
		 * Note:
		 * - This is a global which is passed to the function which instantiates this object.
		 * - This is necessary because the object does not exist until the WordPress init action has fired.
		 *
		 * Parameters:
		 *   $plugin - Plugin instance
		 *
		 * TODO:
		 * - Shortcode/Widget implementation questions (#15)
		 * - Add the appropriate type for $plugin (not object)
		 *
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_plugin( $plugin ) {
			global $debug;
			$this->plugin = $plugin;
		}

		/**
		 * Method: get_plugin
		 *
		 * Get parent plugin, which contains shortcode/widget options.
		 *
		 * Returns:
		 *   Plugin
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_plugin() {
			return $this->plugin;
		}

		/**
		 * Group: Renderers
		 * _____________________________________
		 */

		/**
		 * Method: render_shortcode
		 *
		 * Render a shortcode.
		 *
		 * Parameters:
		 *   $atts - User defined attributes in shortcode tag
		 *   $content - Content between shortcode opening and closing tags
		 *
		 * Returns:
		 *   $content - Content
		 *
		 * Since:
		 *   1.0.0
		 */
		public function render_shortcode( $atts = array(), string $content = '' ) : string {

			/**
			 * Combine user attributes with known attributes and fill in defaults when needed.
			 *
			 * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
			 * @todo Do for widget
			 */
			$instance_options = $this->get_instance_options();
			$string_options   = array();

			foreach ( $instance_options as $key => $value ) {
				$string_options[ $key ] = $this->helper_instance_option_to_string( $value );
			}

			// merge shortcode options with user's shortcode $atts.
			$template_options = shortcode_atts(
				$string_options,
				$atts,
				$this->get_name()
			);

			// store a reference to the parent plugin.
			$plugin = $this->get_plugin();

			$template_options['plugin'] = $plugin;

			// Pass options to template-part as query var.
			// set_query_var( $this->get_prefix() . '_options_all', $options_all ).
			set_query_var( 'options', $template_options );

			/**
			 * Turn on output buffering
			 * This stores the HTML template in the buffer
			 * so that it can be output into the content
			 * rather than at the top of the page.
			 */
			ob_start();

			// mimic WordPress template loading,
			// to allow authors to override loaded templates.
			$templates = new Templateloader( array(
				'filter_prefix'             => $plugin->get_prefix(),
				'plugin_template_directory' => 'template-parts/' . $plugin->get_slug(),
				'theme_template_directory'  => 'template-parts/' . $plugin->get_slug(),
				'path'                      => $plugin->get_path(),
			));

			$template_data = array(
				'content' => $content, // content between shortcode tags.
			);

			// /template-parts/wpdtrt-plugin-boilerplate-name/content/foo.php.
			// $plugin_data is loaded in template.
			$templates
				->set_template_data( $template_data, 'context' )
				->get_template_part( 'content', $this->get_template_name() );

			/**
			 * Get current buffer contents and delete current output buffer.
			 */
			$content = ob_get_clean();

			return $content;
		}

		/**
		 * Group: Helpers
		 * _____________________________________
		 */

		/**
		 * Function: helper_instance_option_to_string
		 *
		 * If the parameter is not supplied with the shortcode,
		 * then use the 'default' key from the $instance_options array.
		 *
		 * Parameters:
		 *   $option - the shortcode/widget option
		 *
		 * Returns:
		 *   $option - the option as a string
		 *
		 * Example:
		 * --- php
		 * function wpdtrt_foo_plugin_init() {
		 *   ...
		 *   $instance_options = array(
		 *     'enlargement_link_text' => array(
		 *     'type'    => 'text',
		 *     'size'    => 30,
		 *     'label'   => __( 'Enlargement link text', 'wpdtrt-map' ),
		 *     'tip'     => __( 'e.g. View larger map', 'wpdtrt-map' ),
		 *     'default' => __( 'View in Google Maps', 'wpdtrt-map' ),
		 *   ),
		 * );
		 *
		 * $instance_options = $this->get_instance_options();
		 * $string_options   = array();
		 *
		 * foreach ( $instance_options as $key => $value ) {
		 *   $string_options[ $key ] = $this->helper_instance_option_to_string( $value );
		 * }
		 * ---
		 *
		 * Test:
		 * - ./tests/test-wpdtrt-plugin.php - test_shortcode_defaults()
		 */
		protected function helper_instance_option_to_string( $option ) : string {
			if ( is_array( $option ) ) {
				if ( array_key_exists( 'default', $option ) ) {
					$option = $option['default'];
				} else {
					$option = '';
				}
			}

			return $option;
		}
	}
}
