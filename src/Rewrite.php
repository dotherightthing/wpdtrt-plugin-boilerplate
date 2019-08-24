<?php
/**
 * File: src/Rewrite.php
 *
 * Plugin rewrite class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_5_13;

if ( ! class_exists( 'Rewrite' ) ) {

	/**
	 * Class: Rewrite
	 *
	 * Plugin Rewrite base class.
	 *
	 * Boilerplate functions.
	 */
	class Rewrite {

		/**
		 * Hook the plugin in to WordPress
		 * This constructor automatically initialises the object's properties
		 * when it is instantiated,
		 *
		 * This is a public method as every plugin uses a new instance:
		 * $wpdtrt_test_rewrite = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_5_13\Rewrite {}
		 *
		 * Parameters:
		 *   (array) $options - Rewrite options.
		 */
		public function __construct( $options ) {

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
		 *   (string) name
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Method: set_name
		 *
		 * Set the value of $name.
		 *
		 * Parameters:
		 *   (string) $new_name
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
		 *   (array) instance_options
		 */
		public function get_instance_options() {
			return $this->instance_options;
		}

		/**
		 * Method: set_instance_options
		 *
		 * Set instance options.
		 *
		 * Parameters:
		 *   (array) $instance_options
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
		 *   (array) labels
		 */
		public function get_labels() {
			return $this->labels;
		}

		/**
		 * Method: set_labels
		 *
		 * Set the value of $labels.
		 *
		 * Parameters:
		 *   (array) $labels
		 */
		protected function set_labels( array $labels ) {
			$this->labels = $labels;
		}

		/**
		 * Method: set_plugin
		 *
		 * Set parent plugin, which contains shortcode/widget options.
		 *
		 * This is a global which is passed to the function which instantiates this object.
		 *
		 * This is necessary because the object does not exist until the WordPress init action has fired.
		 *
		 * Parameters:
		 *   (object) $plugin
		 *
		 * TODO:
		 *   Shortcode/Widget implementation questions (#15)
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
		 *   object - Plugin
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
		 *   (array) options
		 */
		public function get_options() {
			return $this->options;
		}

		/**
		 * Method: set_options
		 *
		 * Set the value of $options.
		 *
		 * Parameters:
		 *   (array) $new_options
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
