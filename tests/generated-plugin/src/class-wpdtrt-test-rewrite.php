<?php
/**
 * Rewrite sub class.
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
class WPDTRT_Test_Rewrite extends DoTheRightThing\WPDTRT_Plugin\r_1_4_15\Rewrite {

	/**
	 * Supplement plugin initialisation.
	 *
	 * @param     array $options Rewrite options.
	 * @since     1.0.0
	 * @version   1.1.0
	 */
	function __construct( $options ) {

		// edit here.

		parent::__construct( $options );
	}

	/**
	 * Supplement rewrite's WordPress setup.
	 */
	protected function wp_setup() {

		// edit here.

		parent::wp_setup();
	}
}
