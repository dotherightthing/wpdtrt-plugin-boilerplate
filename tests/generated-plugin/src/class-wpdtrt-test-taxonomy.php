<?php
/**
 * Taxonomy sub class.
 *
 * @package WPDTRT_Test
 * @since   0.7.16 DTRT WordPress Plugin Boilerplate Generator
 * @version 1.0.0
 */

/**
 * Extend the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since   1.0.0
 * @version 1.0.0
 */
class WPDTRT_Test_Taxonomy extends DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_5_4\Taxonomy {

	/**
	 * Supplement taxonomy initialisation.
	 *
	 * @param     array $options Taxonomy options.
	 * @since     1.0.0
	 * @version   1.1.0
	 */
	public function __construct( $options ) {

		// edit here.
		parent::__construct( $options );
	}

	/**
	 * ====== WordPress Integration ======
	 */

	/**
	 * Supplement taxonomy's WordPress setup.
	 * Note: Default priority is 10. A higher priority runs later.
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference Action order
	 */
	protected function wp_setup() {

		// edit here.
		parent::wp_setup();
	}

	/**
	 * ====== Getters and Setters ======
	 */

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
