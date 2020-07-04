<?php
/**
 * File: src/Rewrite.php
 *
 * Plugin rewrite class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_1;

if ( ! class_exists( 'Rewrite' ) ) {

	/**
	 * Class: Rewrite
	 *
	 * Plugin Rewrite base class.
	 *
	 * Note:
	 * - Contains boilerplate functions.
	 *
	 * Since:
	 *   1.4.15 - Added
	 */
	class Rewrite {

		/**
		 * Constructor: __construct
		 *
		 * Hook the plugin in to WordPress.
		 *
		 * Note:
		 * - This constructor automatically initialises the object's properties
		 *   when it is instantiated.
		 * - This is a public method as every plugin uses a new instance
		 *
		 * Example:
		 * --- php
		 * $wpdtrt_test_rewrite = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_1\Rewrite {}
		 * ---
		 *
		 * Parameters:
		 *   $options - Rewrite options.
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		public function __construct( array $options ) {

			// define variables.
			$name   = null;
			$plugin = null;
			$labels = null;

			// extract variables.
			extract( $options, EXTR_IF_EXISTS );

			// Store a reference to the partner plugin object
			// which stores global plugin options.
			$this->set_plugin( $plugin );
			$this->set_labels( $labels );
			$this->set_name( $name );

			// hook in to WordPress.
			$this->wp_setup();
		}

		/**
		 * Method: wp_setup
		 *
		 * Initialise rewrite options ONCE.
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		protected function wp_setup() {
			$rewrite_name = $this->get_name();
		}

		/**
		 * Group: Setters and Getters
		 * _____________________________________
		 */

		/**
		 * Method: get_name
		 *
		 * Get the value of $name.
		 *
		 * Returns:
		 *   Name
		 *
		 * Since:
		 *   1.4.15 - Added
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
		 *   1.4.15 - Added
		 */
		protected function set_name( string $new_name ) {
			$this->name = $new_name;
		}

		/**
		 * Method: get_instance_options
		 *
		 * Get instance options.
		 *
		 * Returns:
		 *   Instance options
		 *
		 * Since:
		 *   1.4.15 - Added
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
		 *   $instance_options - New instance options
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		protected function set_instance_options( array $instance_options ) {
			$this->instance_options = $instance_options;
		}

		/**
		 * Method: get_labels
		 *
		 * Get the value of $labels.
		 *
		 * Returns:
		 *   Labels
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		public function get_labels() : array {
			return $this->labels;
		}

		/**
		 * Method: set_labels
		 *
		 * Set the value of $labels.
		 *
		 * Parameters:
		 *   $labels - Labels
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		protected function set_labels( array $labels ) {
			$this->labels = $labels;
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
		 *   (object) $plugin
		 *
		 * TODO:
		 *   Shortcode/Widget implementation questions (#15)
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		protected function set_plugin( object $plugin ) {
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
		 * TODO:
		 * - Add return type (not object)
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		public function get_plugin() {
			return $this->plugin;
		}

		/**
		 * Method: get_options
		 *
		 * Get the value of $options.
		 *
		 * Returns:
		 *   Options
		 */
		public function get_options() : array {
			return $this->options;
		}

		/**
		 * Method: set_options
		 *
		 * Set the value of $options.
		 *
		 * Parameters:
		 *   $new_options
		 *
		 * Since:
		 *   1.4.15 - Added
		 */
		protected function set_options( array $new_options ) {
			$this->options = $new_options;
		}

		/**
		 * Group: Renderers
		 * _____________________________________
		 */

		/**
		 * Group: Filters
		 * _____________________________________
		 */

		/**
		 * Group: Helpers
		 * _____________________________________
		 */
	}
}
