<?php
/**
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase
 * 	These tests are run from child plugins.
 */

/**
 * WP_UnitTestCase unit tests for wpdtrt_plugin
 */
class PluginTest extends WP_UnitTestCase {

    /**
     * SetUp
     * Automatically called by PHPUnit before each test method is run
     */
    public function setUp() {
  		// Make the factory objects available.
        parent::setUp();

        $this->mock_data();
    }

    /**
     * TearDown
     * Automatically called by PHPUnit after each test method is run
     */
    public function tearDown() {

        global $wpdtrt_test_plugin;

    	parent::tearDown();
        //$wpdtrt_test_plugin->set_plugin_options( array() );
    }

    // ########## MOCK DATA ########## //

    public function mock_data() {

        $this->plugin_options_config = array(
            'google_static_maps_api_key' => array(
                'type' => 'text',
                'label' => 'Google Static Maps API Key',
                'size' => 50,
                'tip' => 'https://developers.google.com/maps/documentation/static-maps/ > GET A KEY'
            )
        );

        $this->plugin_options_user = array(
            'google_static_maps_api_key' => array(
                'type' => 'text',
                'label' => __('Google Static Maps API Key', 'wpdtrt-test-plugin'),
                'size' => 50,
                'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin'),
                'value' => 'abc12345'
            )
        );

        $this->fallback_plugin_options = array(
            'plugin_options' => array(),
            'plugin_data' => array(),
            'plugin_data_options' => array(),
            'instance_options' => array(),
            'plugin_dependencies' => array()
        );

        $this->all_options_config = array(
            'plugin_options' => array(
                'google_static_maps_api_key' => array(
                    'type' => 'text',
                    'label' => 'Google Static Maps API Key',
                    'size' => 50,
                    'tip' => 'https://developers.google.com/maps/documentation/static-maps/ > GET A KEY'
                )
            ),
            'plugin_data' => array(),
            'plugin_data_options' => array(
                'force_refresh' => 1
            ),
            'instance_options' => array(),
            'plugin_dependencies' => array()
        );

        $this->all_options_user = array(
            'plugin_options' => array(
                'google_static_maps_api_key' => array(
                    'type' => 'text',
                    'label' => 'Google Static Maps API Key',
                    'size' => 50,
                    'tip' => 'https://developers.google.com/maps/documentation/static-maps/ > GET A KEY',
                    'value' => 'abc12345'
                )
            ),
            'plugin_data' => array(),
            'plugin_data_options' => array(
                'force_refresh' => 1
            ),
            'instance_options' => array(),
            'plugin_dependencies' => array()
        );
    }

    // ########## TESTS ########## //

	/**
	 * Test set_plugin_options() and get_plugin_options()
     * These plugin methods manage keys/values which appear
     * and are authored on the plugin options page
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
	public function test__set_plugin_options__get_plugin_options() {

        global $wpdtrt_test_plugin;

        // when the page is first loaded,
        // we get the plugin options out of the coded config
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        $this->assertArrayHasKey(
            'type',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'label',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'size',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'tip',
            $plugin_options['google_static_maps_api_key']
        );  

		$this->assertArrayNotHasKey(
            'value',
			$plugin_options['google_static_maps_api_key'],
            'When the page is first loaded the user value should not exist'
		);	

        // the user enters values and saves the page
        // we expect their entry to be saved in a new 'value' key
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_user );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        $this->assertArrayHasKey(
            'type',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'label',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'size',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'tip',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'value',
            $plugin_options['google_static_maps_api_key'],
            'When the user submits the form, the user value should persist'
        );  

        // the page is reloaded
        // we expect the user's entry to be persistent
        // rather than be replaced by the old subset of options
        // TODO: how are old options are saved over the top of new options?
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        $this->assertArrayHasKey(
            'type',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'label',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'size',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'tip',
            $plugin_options['google_static_maps_api_key']
        );  

        $this->assertArrayHasKey(
            'value',
            $plugin_options['google_static_maps_api_key'],
            'When the page is reloaded, the user value should persist'
        );  
	}

    /**
     * Test array_merge()
     * This is the PHP function used in several plugin methods
     * to blend old data (especially old keys) with new data (especially values)
     * This approach fails, but array_merge_recursive works.
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function archived_test__array_merge() {

        global $wpdtrt_test_plugin;

        $options = array_merge( $this->all_options_config, $this->all_options_user );

        $this->assertArrayHasKey(
            'plugin_options',
            $options
        );  

        $this->assertArrayHasKey(
            'google_static_maps_api_key',
            $options['plugin_options']
        );  

        $this->assertArrayHasKey(
            'plugin_data',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_data_options',
            $options
        );  

        $this->assertArrayHasKey(
            'instance_options',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_dependencies',
            $options
        );  

        // passes
        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When old and new values are merged, new values are lost'
        ); 

        /**
         * Test array_merge in reverse
         * When the page is subsequently reloaded
         * the plugin appears to reapply the original config
         * causing user data to be erased
         */

        $options = array_merge( $this->all_options_user, $this->all_options_config );

         $this->assertArrayHasKey(
            'plugin_options',
            $options
        );  

        $this->assertArrayHasKey(
            'google_static_maps_api_key',
            $options['plugin_options']
        );  

        $this->assertArrayHasKey(
            'plugin_data',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_data_options',
            $options
        );  

        $this->assertArrayHasKey(
            'instance_options',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_dependencies',
            $options
        );  

        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When new and old values are merged, new values are lost'
        ); 
    }

    /**
     * Test array_merge_recursive()
     * A potential replacement for array_merge
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function test__array_merge_recursive() {

        global $wpdtrt_test_plugin;

        $options = array_merge_recursive( $this->all_options_config, $this->all_options_user );

        $this->assertArrayHasKey(
            'plugin_options',
            $options
        );  

        $this->assertArrayHasKey(
            'google_static_maps_api_key',
            $options['plugin_options']
        );  

        $this->assertArrayHasKey(
            'plugin_data',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_data_options',
            $options
        );  

        $this->assertArrayHasKey(
            'instance_options',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_dependencies',
            $options
        );  

        // passes
        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When old and new values are merged, new values are lost'
        ); 

        /**
         * Test array_merge in reverse
         * When the page is subsequently reloaded
         * the plugin appears to reapply the original config
         * causing user data to be erased
         */

        $options = array_merge_recursive( $this->all_options_user, $this->all_options_config );

         $this->assertArrayHasKey(
            'plugin_options',
            $options
        );  

        $this->assertArrayHasKey(
            'google_static_maps_api_key',
            $options['plugin_options']
        );  

        $this->assertArrayHasKey(
            'plugin_data',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_data_options',
            $options
        );  

        $this->assertArrayHasKey(
            'instance_options',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_dependencies',
            $options
        );  

        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When new and old values are merged, new values are lost'
        ); 
    }

    /**
     * Test update_option()
     * This is the WordPress function which adds the merged data to the options table
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function test__update_option__get_option() {

        global $wpdtrt_test_plugin;

        /**
         * Testing set_options()
         */

        $options = array_merge( $this->all_options_config, $this->all_options_user );

        update_option( $wpdtrt_test_plugin->get_prefix(), $options, null );

        /**
         * Testing get_options()
         */

        $options = get_option( $wpdtrt_test_plugin->get_prefix(), $this->fallback_plugin_options );

        $this->assertArrayHasKey(
            'plugin_options',
            $options
        );  

        $this->assertArrayHasKey(
            'google_static_maps_api_key',
            $options['plugin_options']
        );  

        $this->assertArrayHasKey(
            'plugin_data',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_data_options',
            $options
        );  

        $this->assertArrayHasKey(
            'instance_options',
            $options
        );  

        $this->assertArrayHasKey(
            'plugin_dependencies',
            $options
        );  

        // passes
        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When the options are saved to the database and then retrieved, new values are lost'
        );  
    }

    /**
     * Test that the correct field 'type'
     * is passed to the Plugin's render_form_element()
     * as it us used to determine the include() name
     * via options.php
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function test__render_form_element() {

        foreach( $this->plugin_options_user as $name => $attributes ) {

            $this->assertArrayHasKey(
                'type',
                $attributes
            );

            $this->assertEquals(
                'text',
                $attributes['type']
            );
        }
    }

    /**
     * Test whether single calls to set and get plugin options
     * result in duplicate keys
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function test__set_plugin_options__get_plugin_options__single() {

        global $wpdtrt_test_plugin;

        // when the page is first loaded,
        // we get the plugin options out of the coded config

        // 1
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        foreach( $plugin_options as $name => $attributes ) {

            $this->assertArrayHasKey(
                'type',
                $attributes
            );

            $this->assertEquals(
                'text',
                $attributes['type'],
                'A string is expected for the field type'
            );
        }
    }

    /**
     * Test whether multiple calls to set and get plugin options
     * result in duplicate keys
      *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function test__set_plugin_options__get_plugin_options__multi() {

        global $wpdtrt_test_plugin;

        // when the page is first loaded,
        // we get the plugin options out of the coded config

        // 1
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        // 2
        $wpdtrt_test_plugin->set_plugin_options( $plugin_options );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        // 3
        $wpdtrt_test_plugin->set_plugin_options( $plugin_options );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        foreach( $plugin_options as $name => $attributes ) {

            $this->assertArrayHasKey(
                'type',
                $attributes
            );

            $this->assertEquals(
                'text',
                $attributes['type'],
                'A string is expected for the field type'
            );
        }
    }

    /**
     * Test that the options page fields display the correct attributes
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function todo__test__render_options_page_field() {
        // 
    }
}
