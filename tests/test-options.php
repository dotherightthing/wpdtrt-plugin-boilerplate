<?php
/**
 * File: tests/test-wpdtrt-plugin-options.php
 *
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *
 * Note:
 * - These tests are also run from child plugins.
 */

/**
 * Class: PluginTest
 *
 * WP_UnitTestCase unit tests for wpdtrt_plugin_boilerplate.
 */
class PluginTestOptions extends WP_UnitTestCase {

	/**
	 * Group: Lifecycle methods
	 * _____________________________________
	 */

	/**
	 * Method: setUp
	 *
	 * Automatically called by PHPUnit before each test method is run.
	 */
	public function setUp() {
		// Make the factory objects available.
		parent::setUp();

		$this->mock_data();
	}

	/**
	 * Method: tearDown
	 *
	 * Teardown; automatically called by PHPUnit after each test method is run.
	 */
	public function tearDown() {

		global $wpdtrt_test_plugin;

		parent::tearDown();

		// remove any previously saved options.
		$wpdtrt_test_plugin->unset_options();
	}

	/**
	 * Group: Mock data
	 * _____________________________________
	 */

	/**
	 * Method: mock_data
	 */
	public function mock_data() {

		$this->plugin_option_types = array(
			'checkbox_input' => array(
				'type'  => 'checkbox',
				'label' => __( 'Field label', 'text-domain' ),
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'file_input'     => array(
				'type'  => 'file',
				'label' => __( 'Field label', 'text-domain' ),
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'number_input'   => array(
				'type'  => 'number',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'password_input' => array(
				'type'  => 'password',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'select_input'   => array(
				'type'    => 'select',
				'label'   => __( 'Field label', 'fieldname' ),
				'options' => array(
					'option1value' => array(
						'text' => __( 'Label for option 1', 'text-domain' ),
					),
					'option2value' => array(
						'text' => __( 'Label for option 2', 'text-domain' ),
					),
				),
				'tip'     => __( 'Helper text', 'text-domain' ),
			),
			'text_input'     => array(
				'type'  => 'text',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
		);

		$this->plugin_options_config_novalues = array(
			'google_static_maps_api_key' => array(
				'type'  => 'text',
				'label' => __( 'Google Static Maps API Key', 'wpdtrt-test' ),
				'size'  => 50,
				'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
			),
		);

		$this->plugin_options_user_values = array(
			'google_static_maps_api_key' => array(
				'type'  => 'text',
				'label' => __( 'Google Static Maps API Key', 'wpdtrt-test' ),
				'size'  => 50,
				'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
				'value' => 'abc12345',
			),
		);

		$this->all_options_fallback = array(
			'plugin_options'      => array(),
			'plugin_data'         => array(),
			'plugin_data_options' => array(),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);

		$this->all_options_config = array(
			'plugin_options'      => array(
				'google_static_maps_api_key' => array(
					'type'  => 'text',
					'label' => 'Google Static Maps API Key',
					'size'  => 50,
					'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
				),
			),
			'plugin_data'         => array(),
			'plugin_data_options' => array(
				'force_refresh' => 1,
			),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);

		$this->all_options_user = array(
			'plugin_options'      => array(
				'google_static_maps_api_key' => array(
					'type'  => 'text',
					'label' => 'Google Static Maps API Key',
					'size'  => 50,
					'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
					'value' => 'abc12345',
				),
			),
			'plugin_data'         => array(),
			'plugin_data_options' => array(
				'force_refresh' => 1,
			),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);
	}

	/**
	 * Group: Tests
	 * _____________________________________
	 */

	/**
	 * Method: test__set_plugin_options
	 *
	 * Test that the raw config is saved as-is, sans values.
	 *
	 * Note:
	 * - For each option, the 'value' attribute is deliberately omitted,
	 *   this is to aid the checking of this value by helper_get_default_value().
	 * - If the value was set to '' by default,
	 *   it could erase a user value when the new and old options were merged.
	 * - Or, if blank values were ignored
	 *   it would prevent the user from erasing values they no longer required.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
	 */
	public function test__set_plugin_options() {

		global $wpdtrt_test_plugin;

		// save the raw config.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );

		// get config + user values (none).
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// assertions.
		$this->assertArrayHasKey(
			'type',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'text',
			$plugin_options['google_static_maps_api_key']['type']
		);

		$this->assertArrayHasKey(
			'label',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'Google Static Maps API Key',
			$plugin_options['google_static_maps_api_key']['label']
		);

		$this->assertArrayHasKey(
			'size',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'50',
			$plugin_options['google_static_maps_api_key']['size']
		);

		$this->assertArrayHasKey(
			'tip',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'https://developers.google.com/maps/documentation/maps-static/get-api-key',
			$plugin_options['google_static_maps_api_key']['tip']
		);

		$this->assertArrayNotHasKey(
			'value',
			$plugin_options['google_static_maps_api_key'],
			'The raw config should exclude user values'
		);
	}

	/**
	 * Method: test__set_plugin_option_values
	 *
	 * Test that the user values are correctly merged into the config.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
	 */
	public function test__set_plugin_option_values() {

		global $wpdtrt_test_plugin;

		// save the raw config.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );

		// save the user values.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_user_values );

		// get config + user values.
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// assertions.
		$this->assertArrayHasKey(
			'type',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'text',
			$plugin_options['google_static_maps_api_key']['type']
		);

		$this->assertArrayHasKey(
			'label',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'Google Static Maps API Key',
			$plugin_options['google_static_maps_api_key']['label']
		);

		$this->assertArrayHasKey(
			'size',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'50',
			$plugin_options['google_static_maps_api_key']['size']
		);

		$this->assertArrayHasKey(
			'tip',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'https://developers.google.com/maps/documentation/maps-static/get-api-key',
			$plugin_options['google_static_maps_api_key']['tip']
		);

		$this->assertArrayHasKey(
			'value',
			$plugin_options['google_static_maps_api_key'],
			'The raw config should exclude user values'
		);

		$this->assertEquals(
			'abc12345',
			$plugin_options['google_static_maps_api_key']['value']
		);
	}

	/**
	 * Method: test__update_option__get_option
	 *
	 * Test update_option().
	 *
	 * Note:
	 * - This is the WordPress function which adds the merged data to the options table
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
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

		// passes.
		$this->assertArrayHasKey(
			'value',
			$options['plugin_options']['google_static_maps_api_key'],
			'When the options are saved to the database and then retrieved, new values are lost'
		);
	}

	/**
	 * Method: test__render_form_element
	 *
	 * Test that the correct field 'type'
	 * is passed to the Plugin's render_form_element()
	 * as it us used to determine the include() name
	 * via options.php.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
	 */
	public function test__render_form_element() {

		foreach ( $this->plugin_options_user_values as $name => $attributes ) {

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
	 * Method: test__set_plugin_options__get_plugin_options__multi
	 *
	 * Test whether multiple calls to set and get plugin options
	 * result in duplicate keys.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
	 */
	public function test__set_plugin_options__get_plugin_options__multi() {

		global $wpdtrt_test_plugin;

		// when the page is first loaded,
		// we get the plugin options out of the coded config
		//
		// 1.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// 2.
		$wpdtrt_test_plugin->set_plugin_options( $plugin_options );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// 3.
		$wpdtrt_test_plugin->set_plugin_options( $plugin_options );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		foreach ( $plugin_options as $name => $attributes ) {

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
	 * Method: test__helper_get_default_value
	 *
	 * Test that form element values are correctly set
	 * when a form element is rendered
	 * for a plugin option which doesn't have a value attribute yet.
	 *
	 * See:
	 * - views/form-element-checkbox.php
	 * - views/form-element-file.php
	 * - views/form-element-number.php
	 * - views/form-element-password.php
	 * - views/form-element-select.php
	 * - views/form-element-text.php
	 *
	 * TODO:
	 * - Test HTML output to ensure that default values translate to semantic HTML
	 */
	public function test__helper_get_default_value() {

		global $wpdtrt_test_plugin;

		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_option_types, true );
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
	 * Method: todo__test__render_options_page_field
	 *
	 * Test that the options page fields display the correct attributes.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84>
	 */
	public function todo__test__render_options_page_field() {
		// .
	}
}
