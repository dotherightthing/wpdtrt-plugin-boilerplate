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
    }

    /**
     * TearDown
     * Automatically called by PHPUnit after each test method is run
     */
    public function tearDown() {

    	parent::tearDown();
    }

    // ########## MOCK DATA ########## //


    // ########## TEST ########## //

	/**
	 * Test
	 */
	public function test_set_plugin_options() {

        global $wpdtrt_test_plugin;

        $old_plugin_options = array(
          'google_static_maps_api_key' => array(
            'type' => 'text',
            'label' => __('Google Static Maps API Key', 'wpdtrt-test-plugin'),
            'size' => 50,
            'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin')
          )
        );

        $new_plugin_options = array(
          'google_static_maps_api_key' => array(
            'type' => 'text',
            'label' => __('Google Static Maps API Key', 'wpdtrt-test-plugin'),
            'size' => 50,
            'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin'),
            'value' => 'abc12345'
          )
        );

        // when the page is first loaded,
        // we get the plugin options out of the coded config
        $wpdtrt_test_plugin->set_plugin_options($old_plugin_options);
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
        $wpdtrt_test_plugin->set_plugin_options($new_plugin_options);
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
        // rather than be removed by the old subset of options
        $wpdtrt_test_plugin->set_plugin_options($old_plugin_options);
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

        // fails - issue #84
        $this->assertArrayHasKey(
            'value',
            $plugin_options['google_static_maps_api_key'],
            'When the page is reloaded, the user value should persist (#84)'
        );  
	}
}
