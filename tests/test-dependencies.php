<?php
/**
 * File: tests/test-wpdtrt-plugin-dependencies.php
 *
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *
 * Note:
 * - These tests are also run from child plugins.
 */

require_once 'helpers/helpers.php';

/**
 * Class: PluginTestShortcode
 *
 * WP_UnitTestCase unit tests for wpdtrt_plugin_boilerplate.
 */
class PluginTestDependencies extends WP_UnitTestCase {

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

		$this->mock_plugin_dependency_old = array(
			'name'         => 'DTRT Content Sections',
			'slug'         => 'wpdtrt-contentsections',
			'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.0.1/release.zip',
			'version'      => '0.0.1',
			'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
			'required'     => true,
		);

		$this->mock_plugin_dependency_new = array(
			'name'         => 'DTRT Content Sections',
			'slug'         => 'wpdtrt-contentsections',
			'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.0.2/release.zip',
			'version'      => '0.0.2',
			'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
			'required'     => true,
		);
	}

	/**
	 * Group: Tests
	 * _____________________________________
	 */

	/**
	 * Method: test_get_wp_composer_dependencies
	 *
	 * Test that plugin dependencies are correctly loaded from composer-tgmpa.json
	 */
	public function test_get_wp_composer_dependencies() {
		$composer_json = dirname( __FILE__ ) . '/data/composer-tgmpa.json';

		$this->assertFileExists(
			$composer_json,
			'File does not exist at this location'
		);

		$composer_dependencies = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );

		$this->assertNotEmpty(
			$composer_dependencies,
			'Composer dependencies not retrieved'
		);

		$this->assertTrue(
			is_array( $composer_dependencies ),
			'Expected Composer dependencies to be converted to an associative array'
		);

		$this->assertEquals(
			array(
				array(
					'name'         => 'DTRT Content Sections (0.2.2)',
					'slug'         => 'wpdtrt-contentsections',
					'required'     => true,
					'file'         => 'wpdtrt-contentsections.php',
					'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.2.2/release.zip',
					'version'      => '0.2.2',
					'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
					'vendor'       => 'dotherightthing',
				),
				array(
					'name'     => 'Better Anchor Links (1.7.*)',
					'slug'     => 'better-anchor-links',
					'required' => true,
					'file'     => 'auto-anchor-list.php',
					'version'  => '1.7.*',
					'vendor'   => 'wpackagist-plugin',
				),
			),
			$composer_dependencies
		);
	}

	/**
	 * Method: test__set_wp_composer_dependencies_tgmpa
	 *
	 * Test that TGMPA dependencies are correctly registered.
	 */
	public function test__set_wp_composer_dependencies_tgmpa() {
		global $wpdtrt_test_plugin;

		$composer_json               = dirname( __FILE__ ) . '/data/composer-tgmpa.json';
		$updated_plugin_dependencies = $wpdtrt_test_plugin->set_wp_composer_dependencies_tgmpa( $composer_json );

		$this->assertNotCount(
			0,
			$updated_plugin_dependencies,
			'No plugin dependencies returned, path to composer-tgmpa.json is bad'
		);

		$this->assertEquals(
			array(
				array(
					'name'         => 'DTRT Content Sections (0.2.2)',
					'slug'         => 'wpdtrt-contentsections',
					'required'     => true,
					'version'      => '0.2.2',
					'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.2.2/release.zip',
					'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
				),
				array(
					'name'     => 'Better Anchor Links (1.7.*)',
					'slug'     => 'better-anchor-links',
					'required' => true,
					'version'  => '1.7.*',
				),
			),
			$updated_plugin_dependencies,
			'TGMPA plugin dependencies not updated correctly'
		);
	}

	/**
	 * Method: test__not_set_wp_composer_dependencies_tgmpa
	 *
	 * Test that no TGMPA dependencies does not cause an error.
	 */
	public function test__not_set_wp_composer_dependencies_tgmpa() {
		global $wpdtrt_test_plugin;

		$composer_json               = dirname( __FILE__ ) . '/data/composer-not-tgmpa.json';
		$updated_plugin_dependencies = $wpdtrt_test_plugin->set_wp_composer_dependencies_tgmpa( $composer_json );

		$this->assertEquals(
			array(),
			$updated_plugin_dependencies,
			'TGMPA plugin dependencies not updated correctly'
		);
	}

	/**
	 * Method: test__get_wp_composer_dependencies_wpunit
	 *
	 * Test static method get_wp_composer_dependencies_wpunit.
	 */
	public function test__get_wp_composer_dependencies_wpunit() {
		$composer_json                    = dirname( __FILE__ ) . '/data/composer-tgmpa.json';
		$composer_dependencies            = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );
		$composer_dependencies_to_require = WPDTRT_Test_Plugin::get_wp_composer_dependencies_wpunit( $composer_dependencies );

		$this->assertEquals(
			array(
				dirname( dirname( __FILE__ ) ) . '/vendor/dotherightthing/wpdtrt-contentsections/wpdtrt-contentsections.php',
				dirname( dirname( __FILE__ ) ) . '/wp-content/plugins/better-anchor-links/auto-anchor-list.php',
			),
			$composer_dependencies_to_require,
			'WP Unit plugin dependencies not correct'
		);
	}

	/**
	 * Method: test__not_get_wp_composer_dependencies_wpunit
	 *
	 * Test that no TGMPA dependencies does not cause an error.
	 */
	public function test__not_get_wp_composer_dependencies_wpunit() {
		$composer_json                    = dirname( __FILE__ ) . '/data/composer-not-tgmpa.json';
		$composer_dependencies            = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );
		$composer_dependencies_to_require = WPDTRT_Test_Plugin::get_wp_composer_dependencies_wpunit( $composer_dependencies );

		$this->assertEquals(
			array(),
			$composer_dependencies_to_require,
			'WP Unit plugin dependencies not correct'
		);
	}


	/**
	 * Method: test_set_plugin_dependency
	 *
	 * Test that setting a single dependency,
	 *  will supercede an outdated duplicate.
	 */
	public function test_set_plugin_dependency() {

		global $wpdtrt_test_plugin;

		$wpdtrt_test_plugin->set_plugin_dependency( $this->mock_plugin_dependency_old );

		$wpdtrt_test_plugin->set_plugin_dependency( $this->mock_plugin_dependency_new );

		$new_plugin_dependencies = $wpdtrt_test_plugin->get_plugin_dependencies();

		// reindex array (only required in this test).
		$new_plugin_dependencies = array_values( $new_plugin_dependencies );

		$this->assertEquals(
			'0.0.1',
			$this->mock_plugin_dependency_old['version'],
			'Expected old plugin dependency version'
		);

		$this->assertEquals(
			'0.0.2',
			$new_plugin_dependencies[0]['version'],
			'Expected old plugin dependency to be replaced with new version'
		);
	}
}
