<?php
/**
 * Plugin template loader class.
 *
 * @package   WPDTRT_Plugin_Boilerplate
 * @version   1.0.0
 * @since     0.6.0
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_4_27;

if ( ! class_exists( 'Templateloader' ) ) {

	/**
	 * Template loader sub class
	 *
	 * Extends the base class to inherit functionality.
	 * Displays templates in the Templates dropdown in the page edit screen.
	 * Allows the author to override these from the templates folder in their own theme.
	 *
	 * @since   0.6.0
	 * @version 1.0.0
	 * @uses    https://github.com/wpexplorer/page-templater
	 * @see     http://www.wpexplorer.com/wordpress-page-templates-plugin/
	 */
	class Templateloader extends \Gamajo_Template_Loader {

		/**
		 * Pass options to Gamajo class
		 * This constructor automatically initialises the object's properties
		 * when it is instantiated.
		 *
		 * This is a public method as every plugin uses a new instance:
		 * $wpdtrt_test_templateloader = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_4_27\Templateloader {}
		 *
		 * @param     array $options Plugin options.
		 * @since     1.0.0
		 * @version   1.1.0
		 */
		public function __construct( $options ) {

			// define variables.
			$filter_prefix             = null;
			$plugin_template_directory = null;
			$theme_template_directory  = null;
			$path                      = null;

			// extract variables.
			extract( $options, EXTR_IF_EXISTS );

			$this->filter_prefix             = $filter_prefix;
			$this->plugin_template_directory = $plugin_template_directory;
			$this->theme_template_directory  = $theme_template_directory;
			$this->plugin_directory          = $path;
		}
	}
}
