<?php
/**
 * Shortcode sub class.
 *
 * @package WPDTRT_Test
 * @since   1.0.0
 * @version 1.0.0
 */

/**
 * Extend the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class WPDTRT_Test_Shortcode extends DoTheRightThing\WPDTRT_Plugin\r_1_4_15\Shortcode {

	/**
	 * Supplement shortcode initialisation.
	 *
	 * @param     array $options Shortcode options.
	 * @since     1.0.0
	 * @version   1.1.0
	 */
	function __construct( $options ) {

		// edit here.

		parent::__construct( $options );
	}

	/**
	 * Supplement shortcode's WordPress setup.
	 */
	protected function wp_setup() {

		// edit here.

		parent::wp_setup();
	}
}
