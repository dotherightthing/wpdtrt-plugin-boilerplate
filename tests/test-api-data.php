<?php
/**
 * File: tests/test-wpdtrt-plugin-api-data.php
 *
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *
 * Note:
 * - These tests are also run from child plugins.
 */

/**
 * Import helpers
 */
require_once 'helpers/helpers.php';

/**
 * Class: PluginTestShortcode
 *
 * WP_UnitTestCase unit tests for wpdtrt_plugin_boilerplate.
 */
class PluginTestApiData extends WP_UnitTestCase {

	/**
	 * Group: Lifecycle methods
	 * _____________________________________
	 */

	/**
	 * Group: Mock data
	 * _____________________________________
	 */

	/**
	 * Group: Tests
	 * _____________________________________
	 */

	/**
	 * Method: test__get_api_data
	 *
	 * Test that get_api_data() returns data,
	 *  and that this is stored and retrievable.
	 *
	 * See:
	 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Data:-Loading-from-an-API#data-is-stored-in-an-associative-array>
	 */
	public function test__get_api_data() {
		global $wpdtrt_test_plugin;

		$endpoint = 'http://jsonplaceholder.typicode.com/photos/1';
		$data     = $wpdtrt_test_plugin->get_api_data( $endpoint );

		$this->assertTrue(
			is_array( $data ),
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
}
