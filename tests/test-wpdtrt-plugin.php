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
	 * Test set_plugin_options
     * Tests that values authored on the plugin options page
     * are successfully saved and persist
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
        // rather than be replaced by the old subset of options
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

    /**
     * Test set_options
     * Tests that old and new options are successfully merged
     * and stored in the WordPress Options table
     */
    public function test_set_options() {

        global $wpdtrt_test_plugin;

        /**
         * Various options are stored in a single, multidimensional $options array,
         *  which is stored in the WordPress Options table.
         *
         *  $options['plugin_options']
         *      What: Global options available anywhere in the Plugin
         *      Format: An array of options, each describes a form input for collecting data
         *      Defaults: Set in wpdrt-pluginname.php
         *      Updates: Updated via user input on plugin options page
         *      Docs: https://github.com/dotherightthing/wpdtrt-plugin/wiki/Add-a-global-option
         *
         *  $options['plugin_dependencies']
         *      What: WordPress plugin dependencies required by the Plugin
         *      Format: An array of TGMPA dependencies, each describes a source repository
         *      Defaults: Set in wpdrt-pluginname.php (config object)
         *      Updates: Set in class-wpdtrt-pluginname-plugin (set_plugin_dependency in wp_setup)
         *      Docs: https://github.com/dotherightthing/wpdtrt-plugin/wiki/Add-a-WordPress-plugin-dependency
         *
         *  $options['instance_options']
         *      What: Widget & Shortcode options
         *      Format: An array of options, each describes a form input for collecting data
         *      Defaults: Set in wpdrt-pluginname.php
         *      Updates: Updated via user input on widget screen, and coded shortcode options
         *      Docs: https://github.com/dotherightthing/wpdtrt-plugin/wiki/Add-a-shortcode-or-widget-option
         *
         *  $options['plugin_data']
         *      What: API response
         *      Format: JSON
         *      Defaults: Set in class-wpdtrt-pluginname-plugin (get_api_data)
         *      Updates: Updated via repeat calls to the API after a timeout (via options page)
         *      Docs: -
         *
         *  $options['plugin_data_options']
         *      What: meta data attached to the plugin data, to determine refresh frequency
         *      Format: An array containing two options: last_updated and force_refresh
         *      Defaults: -
         *      Updates: TODO - Via options page?
         *      Docs: -
         */

        /**
         * Test data merging:
         * array_merge is used in several functions
         * to blend old data (especially old keys) with new data (especially values)
         */
        $old_options = array(
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

        $new_options = array(
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

        $options = array_merge( $old_options, $new_options );

        $this->assertArrayHasKey(
            'plugin_options',
            $options
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

        // fails - issue #84
        $this->assertArrayHasKey(
            'value',
            $options['plugin_options']['google_static_maps_api_key'],
            'When the old and new values are merged, new values are lost'
        );  
    }
}
