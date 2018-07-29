<?php
/**
 * Plugin rewrite class.
 *
 * @package   WPDTRT_Plugin_Boilerplate
 * @version   1.0.0
 * @since     1.4.16
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_5_4;

if ( ! class_exists( 'Rewrite' ) ) {

	/**
	 * Plugin Rewrite base class.
	 *  Boilerplate functions.
	 *
	 * @return      Rewrite
	 * @since       1.4.15
	 * @version     1.0.0
	 */
	class Rewrite {

		/**
		 * Hook the plugin in to WordPress
		 * This constructor automatically initialises the object's properties
		 * when it is instantiated,
		 *
		 * This is a public method as every plugin uses a new instance:
		 * $wpdtrt_test_rewrite = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_5_4\Rewrite {}
		 *
		 * @param     array $options Rewrite options.
		 * @since     1.0.0
		 * @version   1.1.0
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
		 * Initialise rewrite options ONCE.
		 *
		 * @since 1.0.0
		 */
		protected function wp_setup() {
			$rewrite_name = $this->get_name();
		}

		/**
		 * ===== Setters and Getters =====
		 */

		/**
		 * Get the value of $name
		 *
		 * @return      string
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * Set the value of $name
		 *
		 * @param       string $new_name New name.
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		protected function set_name( $new_name ) {
			$this->name = $new_name;
		}

		/**
		 * Get default options
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function get_instance_options() {
			return $this->instance_options;
		}

		/**
		 * Set instance options
		 *
		 * @param array $instance_options Instance options.
		 * @since 1.0.0
		 */
		protected function set_instance_options( $instance_options ) {
			$this->instance_options = $instance_options;
		}

		/**
		 * Get the value of $labels
		 *
		 * @return      array
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		public function get_labels() {
			return $this->labels;
		}

		/**
		 * Set the value of $labels
		 *
		 * @param       array $labels Labels.
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		protected function set_labels( $labels ) {
			$this->labels = $labels;
		}

		/**
		 * Set parent plugin, which contains shortcode/widget options
		 * This is a global which is passed to the function which instantiates this object.
		 * This is necessary because the object does not exist until the WordPress init action has fired.
		 *
		 * @param object $plugin Plugin.
		 * @since 1.0.0
		 * @todo Shortcode/Widget implementation questions (#15)
		 */
		protected function set_plugin( $plugin ) {
			$this->plugin = $plugin;
		}

		/**
		 * Get parent plugin, which contains shortcode/widget options
		 *
		 * @return object
		 * @since 1.0.0
		 */
		public function get_plugin() {
			return $this->plugin;
		}

		/**
		 * Get the value of $options
		 *
		 * @return      array
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		public function get_options() {
			return $this->options;
		}

		/**
		 * Set the value of $options
		 *
		 * @param       array $new_options New options.
		 * @since       1.0.0
		 * @version     1.0.0
		 */
		protected function set_options( $new_options ) {
			$this->options = $new_options;
		}

		/**
		 * ===== Renderers =====
		 */

		/**
		 * ===== Filters =====
		 */

		/**
		 * ===== Helpers =====
		 */
	}
}
