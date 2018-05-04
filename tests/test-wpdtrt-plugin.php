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

        $this->plugin_option_types = array(
            'checkbox_input' => array(
                'type' => 'checkbox',
                'label' => esc_html__('Field label', 'text-domain'),
                'tip' => __('Helper text', 'text-domain')
            ),
            'file_input' => array(
                'type' => 'file',
                'label' => __('Field label', 'text-domain'),
                'tip' => __('Helper text', 'text-domain')
            ),
            'number_input' => array(
                'type' => 'number',
                'label' => __('Field label', 'text-domain'),
                'size' => 10,
                'tip' => __('Helper text', 'text-domain')
            ),
            'password_input' => array(
                'type' => 'password',
                'label' => __('Field label', 'text-domain'),
                'size' => 10,
                'tip' => __('Helper text', 'text-domain')
            ),
            'select_input' => array(
                'type' => 'select',
                'label' => __('Field label', 'fieldname'),
                'options' => array(
                    'option1value' => array(
                        'text' => __('Label for option 1', 'text-domain')
                    ),
                    'option2value' => array(
                        'text' => __('Label for option 2', 'text-domain')
                    )
                ),
                'tip' => __('Helper text', 'text-domain')
            ),
            'text_input' => array(
                'type' => 'text',
                'label' => __('Field label', 'text-domain'),
                'size' => 10,
                'tip' => __('Helper text', 'text-domain')
            ),
        );

        $this->plugin_options_config_novalues = array(
            'google_static_maps_api_key' => array(
                'type' => 'text',
                'label' => __('Google Static Maps API Key', 'wpdtrt-test-plugin'),
                'size' => 50,
                'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin')
            )
        );

        $this->plugin_options_user_values = array(
            'google_static_maps_api_key' => array(
                'type' => 'text',
                'label' => __('Google Static Maps API Key', 'wpdtrt-test-plugin'),
                'size' => 50,
                'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin'),
                'value' => 'abc12345'
            )
        );

        $this->all_options_fallback = array(
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
                    'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin'),
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
                    'tip' => __('https://developers.google.com/maps/documentation/static-maps/ > GET A KEY', 'wpdtrt-test-plugin'),
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
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues );
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
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_user_values );
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
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues );
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

        $options = get_option( $wpdtrt_test_plugin->get_prefix(), $this->all_options_fallback );

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

        foreach( $this->plugin_options_user_values as $name => $attributes ) {

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
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues );
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
        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues );
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
     * Test initial plugin options config
     *  For each option, the 'value' attribute is deliberately omitted,
     *  this is to aid the checking of this value by helper_get_default_value().
     *  If the value was set to '' by default,
     *  it could erase a user value when the new and old options were merged -
     *  or, if blank values were ignored
     *  it would prevent the user from erasing values they no longer required
     *  Note: 'value' attributes MAY be added for unit tests.
     * @todo helper_get_default_value
     */
    public function test__plugin_options_config_novalue() {

        global $wpdtrt_test_plugin;

        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues );
        $plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        foreach( $plugin_options as $name => $attributes ) {

            $this->assertArrayNotHasKey(
                'value',
                $attributes,
                'Config should not set a value for plugin options'
            );
        }
    }

    /**
     * Test that form element values are correctly set
     * when a form element is rendered
     * for a plugin option which doesn't have a value attribute yet
     *
     * @see views/form-element-checkbox.php
     * @see views/form-element-file.php
     * @see views/form-element-number.php
     * @see views/form-element-password.php
     * @see views/form-element-select.php
     * @see views/form-element-text.php
     *
     * @todo test HTML output to ensure that default values translate to semantic HTML
     */
    public function test__helper_get_default_value() {

        global $wpdtrt_test_plugin;

        $wpdtrt_test_plugin->set_plugin_options( $this->plugin_option_types );
        $stored_plugin_options = $wpdtrt_test_plugin->get_plugin_options();

        $this->assertEquals(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['checkbox_input']['type'] ),
            '',
            'When a plugin option does not have a value yet, a checkbox input should output an empty string (not checked)'
        );

        $this->assertNull(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['file_input']['type'] ),
            'When a plugin option does not have a value yet, a file input should output NULL (nothing selected)'
        );

        $this->assertNull(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['number_input']['type'] ),
            'When a plugin option does not have a value yet, a number input should output NULL [to check]'
        );

        $this->assertEquals(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['password_input']['type'] ),
            '',
            'When a plugin option does not have a value yet, a password input should output an empty string'
        );

        $this->assertNull(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['select_input']['type'] ),
            'When a plugin option does not have a value yet, a select input should output NULL (nothing selected)'
        );

        $this->assertEquals(
            $wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['text_input']['type'] ),
            '',
            'When a plugin option does not have a value yet, a text input should output an empty string'
        );
    }

    /**
     * Test that the options page fields display the correct attributes
     *
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/84
     */
    public function todo__test__render_options_page_field() {
        // 
    }

    /**
     * Test that get_api_data() returns data
     *  and that this is stored and retrievable.
     */
    public function test__get_api_data() {
        global $wpdtrt_test_plugin;

        $endpoint = 'http://jsonplaceholder.typicode.com/photos/1';
        $data = $wpdtrt_test_plugin->get_api_data( $endpoint );

        /*
        // Demo data format:
        {
            "albumId": 1,
            "id": 1,
            "title": "accusamus beatae ad facilis cum similique qui sunt",
            "url": "http://placehold.it/600/92c952",
            "thumbnailUrl": "http://placehold.it/150/92c952"
        }
        */

        $this->assertTrue(
            is_array($data),
            'Expected JSON data to be converted to an associative array'
        );

        $this->assertArrayHasKey(
            'id',
            $data,
            'Expected demo API data to contain an id key'
        );

        $this->assertEquals(
            $data,
            $wpdtrt_test_plugin->get_plugin_data(),
            'Expected API data to be stored as plugin_data'
        );
    }

    /*
    TODO
    so the value does not exist until it is created
    via user input
    once created it can be updated
    either by user input
    or by the config (unusual but possible)
    */
}
