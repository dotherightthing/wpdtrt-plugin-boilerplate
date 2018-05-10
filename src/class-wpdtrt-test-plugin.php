<?php
/**
 * Plugin sub class.
 *
 * @package     wpdtrt_test
 * @since       1.0.0
 * @version 	1.0.0
 */

/**
 * Plugin sub class.
 *
 * Extends the base class to inherit boilerplate functionality.
 * Adds application-specific methods.
 *
 * @since       1.0.0
 * @version 	1.0.0
 */
class WPDTRT_Test_Plugin extends DoTheRightThing\WPPlugin\r_1_4_12\Plugin {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new WPDTRT_Test_Plugin
     *
     * @param     array $settings Plugin options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $settings ) {

    	// add any initialisation specific to wpdtrt-blocks here

		// Instantiate the parent object
		parent::__construct( $settings );
    }

    //// START WORDPRESS INTEGRATION \\\\

    /**
     * Initialise plugin options ONCE.
     *
     * @param array $default_options
     *
     * @since 1.0.0
     *
     * @todo update
     * @todo support this function in child plugin
     */
    protected function wp_setup() {
    	parent::wp_setup();
    }

    //// END WORDPRESS INTEGRATION \\\\
}

?>