<?php
/**
 * File: src/Templateloader.php
 *
 * Plugin template loader class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_18;

if ( ! class_exists( 'Templateloader' ) ) {

	/**
	 * Class: Templateloader
	 *
	 * Template loader sub class.
	 *
	 * Note:
	 * - Extends the base class to inherit functionality.
	 * - Displays templates in the Templates dropdown in the page edit screen.
	 * - Allows the author to override these from the templates folder in their own theme.
	 *
	 * Uses:
	 * - <https://github.com/wpexplorer/page-templater>
	 * - <http://www.wpexplorer.com/wordpress-page-templates-plugin/>
	 *
	 * Since:
	 *   0.6.0
	 */
	class Templateloader extends \Gamajo_Template_Loader {

		/**
		 * Constructor: __construct
		 *
		 * Pass options to Gamajo class.
		 *
		 * Note:
		 * - This constructor automatically initialises the object's properties
		 *   when it is instantiated.
		 * - This is a public method as every plugin uses a new instance
		 *
		 * Example:
		 * --- php
		 * $wpdtrt_test_templateloader = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_18\Templateloader {}
		 * ---
		 *
		 * Parameters:
		 *   $options - Plugin options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function __construct( array $options ) {

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
