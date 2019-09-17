<?php
/**
 * File: src/Widget.php
 *
 * Plugin widget class.
 *
 * Note:
 * - Boilerplate to generate a widget, which is configured in WP Admin, and can be displayed in sidebars.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_9;

if ( ! class_exists( 'Widget' ) ) {

	/**
	 * Class: Widget
	 *
	 * Plugin Widget sub class.
	 *
	 * Note:
	 * - Extends and inherits from WP_Widget.
	 * - WP_Widget must be extended for each widget, and WP_Widget::widget() must be overridden.
	 * - Class names should use capitalized words separated by underscores. Any acronyms should be all upper case.
	 *
	 * Uses:
	 * - ../../../../wp-includes/class-wp-widget.php:
	 *
	 * See:
	 * - <https://developer.wordpress.org/reference/classes/wp_widget/>
	 * - <https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions>
	 *
	 * Since:
	 *   0.1.0
	 */
	class Widget extends \WP_Widget {

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
		 * $wpdtrt_test_widget = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_9\Widget {}
		 * ---
		 *
		 * Parameters:
		 *   $options - Widget options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function __construct( array $options ) {

			// define variables.
			$name                      = null;
			$title                     = null;
			$description               = null;
			$plugin                    = null;
			$template                  = null;
			$selected_instance_options = null;

			// extract variables.
			extract( $options, EXTR_IF_EXISTS );

			// Store a reference to the partner plugin object,
			// which stores global plugin options.
			$this->set_plugin( $plugin );
			$this->set_template_name( $template );

			$widget_instance_options = array(
				'description' => $description,
			);

			$plugin_instance_options = $plugin->get_instance_options();

			foreach ( $selected_instance_options as $option_name ) {
				$widget_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
			}

			$this->set_instance_options( $widget_instance_options );

			// Instantiate the WordPress parent object.
			parent::__construct( $name, $title, $widget_instance_options );
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
		protected function get_instance_options() : array {
			return $this->instance_options;
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
		 *   $plugin - Plugin.
		 *
		 * TODO:
		 * - Shortcode/Widget implementation questions (#15)
		 * - Set correct type (not object)
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_plugin( $plugin ) {
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
		 * Method: render_form_element
		 *
		 * Form field templating for the widget admin page.
		 *
		 * Parameters:
		 * $instance - The WordPress Widget instance
		 * $name - Name
		 * $attributes - Attributes
		 *
		 * Returns:
		 * - Form element HTML
		 *
		 * TODO:
		 * - Add field validation feedback (#10)
		 *
		 * Since:
		 *   1.0.0
		 */
		public function render_form_element( array $instance, string $name, array $attributes = [] ) : string {

			// these options don't have attributes.
			if ( 'description' === $name ) {
				return '';
			}

			// define variables.
			$type    = null;
			$label   = null;
			$size    = null;
			$tip     = null;
			$options = null;

			// populate variables.
			extract( $attributes, EXTR_IF_EXISTS );

			// name as a string.
			$name_str = $name;

			// widget admin layout.
			$label_start = '<p class="wpdtrt-plugin-boilerplate--widget-field">';
			$label_end   = '';
			$field_start = '';
			$field_end   = '</p>';
			$tip_element = 'span';
			$classname   = 'widefat'; // full width.

			/**
			 * Set the value to the variable with the same name as the $name string
			 * e.g. $name="wpdtrt_attachment_map_toggle_label" => $wpdtrt_attachment_map_toggle_label => ('Open menu', 'wpdtrt-attachment-map')
			 *
			 * @see http://php.net/manual/en/language.variables.variable.php
			 * @see https://developer.wordpress.org/reference/classes/wp_widget/get_field_name/
			 */

			$plugin = $this->get_plugin();

			$value = $plugin->helper_normalise_field_value(
				( isset( $instance[ $name_str ] ) ? $instance[ $name_str ] : null ),
				$type
			);

			/**
			 * Construct name attributes for use in form() fields
			 *  translating e.g. 'number' to 'wp-widget-foobar[1]-number'
			 */
			$name = $this->get_field_name( $name_str );

			if ( 'title' === $name_str ) :
				// Display dynamic widget title in .in-widget-title via appendTitle() in wp-admin/js/widgets.min.js;.
				$id = $name . '-' . $name_str;
			else :
				$id = $name;
			endif;

			$plugin = $this->get_plugin();

			/**
			 * Load the HTML template
			 * The supplied arguments will be available to this template.
			 */

			/**
			 * Turn on output buffering
			 * This stores the HTML template in the buffer
			 * so that it can be output into the content
			 * rather than at the top of the page.
			 */
			ob_start();

			require $plugin->get_path() . 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/views/form-element-' . $type . '.php';

			/**
			 * Get current buffer contents and delete current output buffer
			 */
			return ob_get_clean();
		}

		/**
		 * Method: widget
		 *
		 * Echoes the widget content to the front-end.
		 *
		 * Parameters:
		 *   $args - Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
		 *   $instance - The settings for the particular instance of the widget.
		 */
		public function widget( $args, $instance ) {

			/**
			 * Get the unique ID
			 *
			 * @link https://kylebenk.com/how-to-wordpress-widget-id/
			 */

			// merge display $args with $instance settings.
			$template_options = array_merge( $args, $instance );

			// store a reference to the parent plugin.
			$template_options['plugin'] = $this->get_plugin();

			/**
			 * Apply_filters( $tag, $value );
			 * Apply the 'title' filter to get the title of the instance.
			 * Display the title of this instance, which the user can optionally customise
			 */
			$template_options['title'] = apply_filters( 'title', $instance['title'] );

			// store a reference to the parent plugin.
			$plugin = $this->get_plugin();

			// Pass options to template-part as query var,
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

			// /template-parts/wpdtrt-plugin-boilerplate-name/content/foo.php
			$templates->get_template_part( 'content', $this->get_template_name() );

			/**
			 * Get current buffer contents and delete current output buffer
			 */
			$content = ob_get_clean();

			// echo not return.
			echo $content;
		}

		/**
		 * Method: update
		 *
		 * Updates a particular instance of a widget, by replacing the old instance with data from the new instance.
		 *
		 * Parameters:
		 *   $new_instance New settings for this instance as input by the user via WP_Widget::form().
		 *   $old_instance Old settings for this instance.
		 *
		 * Returns:
		 *   $instance - Instance
		 */
		public function update( $new_instance, $old_instance ) : array {

			// Save user input (widget options).
			$instance         = $old_instance;
			$instance_options = $this->get_instance_options();

			/**
			 * Strip_tags — Strip HTML and PHP tags from a string
			 *
			 * @example string strip_tags ( string $str [, string $allowable_tags ] )
			 * @link http://php.net/manual/en/function.strip-tags.php
			 */
			if ( isset( $new_instance['title'] ) ) {
				$instance['title'] = strip_tags( $new_instance['title'] );
			}

			// for each form element name.
			foreach ( $instance_options as $name => $attributes ) {

				// these options don't have attributes.
				if ( 'description' === $name ) {
					continue;
				}

				$plugin = $this->get_plugin();

				$value = $plugin->helper_normalise_field_value(
					( isset( $new_instance[ $name ] ) ? $new_instance[ $name ] : null ),
					$attributes['type']
				);

				$instance[ $name ] = $value;
			}

			return $instance;
		}

		/**
		 * Method: form
		 *
		 * Outputs the settings update form in wp-admin.
		 *
		 * Note:
		 * - Default return is 'noform'.
		 *
		 * Parameters:
		 *   $instance - Current settings
		 */
		public function form( $instance ) {

			// get a reference to the parent plugin.
			$plugin           = $this->get_plugin();
			$instance_options = $this->get_instance_options();

			/**
			 * Escape HTML attributes to sanitize the data.
			 *
			 * @example esc_attr( string $text )
			 * @link https://developer.wordpress.org/reference/functions/esc_attr/
			 */
			if ( isset( $instance['title'] ) ) {
				$title = esc_attr( $instance['title'] );
			} else {
				$title = null;
			}

			/**
			 * Output the HTML
			 *
			 * @todo Currently redundant but could be used to indicate data ranges: $data = $plugin->get_api_data();
			 * @todo Make widget form Title translateable (#16)
			 */
			echo $this->render_form_element( $instance, 'title', array(
				'type'  => 'text',
				'label' => 'Title', // esc_html__('Title', 'wpdtrt-plugin-boilerplate').
			));

			foreach ( $instance_options as $name => $attributes ) {
				echo $this->render_form_element( $instance, $name, $attributes );
			}
		}
	}
}
