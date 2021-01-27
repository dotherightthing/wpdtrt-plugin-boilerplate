<?php
/**
 * File: tests/generated-plugin/src/class-wpdtrt-test-taxonomy.php
 *
 * Taxonomy sub class.
 *
 * Since:
 *   0.7.16 - DTRT WordPress Plugin Boilerplate Generator
 */

/**
 * Class: WPDTRT_Test_Taxonomy
 *
 * Extend the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * Since:
 *   0.7.16 - DTRT WordPress Plugin Boilerplate Generator
 */
class WPDTRT_Test_Taxonomy extends DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_12\Taxonomy {

	/**
	 * Constructor: __construct
	 *
	 * Supplement taxonomy initialisation.
	 *
	 * Parameters:
	 *   $options - Taxonomy options
	 *
	 * Since:
	 *   0.7.16 - DTRT WordPress Plugin Boilerplate Generator
	 */
	public function __construct( array $options ) {

		// edit here.
		parent::__construct( $options );
	}

	/**
	 * Group: WordPress Integration
	 * _____________________________________
	 */

	/**
	 * Method: wp_setup
	 *
	 * Supplement taxonomy's WordPress setup.
	 *
	 * Note:
	 * - Default priority is 10. A higher priority runs later.
	 *
	 * See:
	 * - <Action order: https://codex.wordpress.org/Plugin_API/Action_Reference>
	 *
	 * Since:
	 *   0.7.16 - DTRT WordPress Plugin Boilerplate Generator
	 */
	protected function wp_setup() {

		// edit here.
		parent::wp_setup();
	}

	/**
	 * Group: Getters and Setters
	 * _____________________________________
	 */

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
