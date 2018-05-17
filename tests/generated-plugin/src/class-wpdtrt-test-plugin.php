<?php
/**
 * Plugin sub class.
 *
 * @package WPDTRT_Test
 * @since   1.0.0
 * @version 1.0.0
 */

/**
 * Plugin sub class.
 *
 * Extends the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class WPDTRT_Test_Plugin extends DoTheRightThing\WPDTRT_Plugin\r_1_4_15\Plugin {

	/**
	 * Hook the plugin in to WordPress
	 * This constructor automatically initialises the object's properties
	 * when it is instantiated,
	 * using new WPDTRT_Test
	 *
	 * @param     array $settings Plugin options
	 * @since     1.0.0
	 * @version   1.1.0
	 */
	function __construct( $settings ) {

		// add any initialisation specific to wpdtrt-test here.

		// Instantiate the parent object
		parent::__construct( $settings );
	}

	/**
	 * ===== WordPress Integration =====
	 */

	/**
	 * Initialise plugin options ONCE.
	 *
	 * @param array $default_options
	 * @since 1.0.0
	 */
	protected function wp_setup() {
		parent::wp_setup();
	}
}
