<?php
/**
 * File: tests/test-wpdtrt-plugin-shortcode.php
 *
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *
 * Note:
 * - These tests are run from child plugins.
 */

// Note: this causes the warning
// No tests found in class "PluginTestHelpers".
require_once 'helpers/helpers.php';

/**
 * Class: PluginTestShortcode
 *
 * WP_UnitTestCase unit tests for wpdtrt_plugin_boilerplate.
 */
class PluginTestShortcode extends WP_UnitTestCase {

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

		global $test_helpers;

		// Post (for testing manually entered, naked shortcode).
		// See ./tests/generated-plugin/template-parts/wpdtrt-test/content-test.php.
		$this->post_id_1 = $test_helpers->create_post( array(
			'post_title'   => 'DTRT Test shortcode test',
			'post_content' => '[wpdtrt_test_shortcode]Text to style[/wpdtrt_test_shortcode]',
		));

		$this->post_id_2 = $test_helpers->create_post( array(
			'post_title'   => 'DTRT Test shortcode test',
			'post_content' => '[wpdtrt_test_shortcode]Text to style[/wpdtrt_test_shortcode]',
		));

		$this->post_id_3 = $test_helpers->create_post( array(
			'post_title'   => 'DTRT Test shortcode test',
			'post_content' => '[wpdtrt_test_shortcode color="green"]Text to style[/wpdtrt_test_shortcode]',
		));
	}

	/**
	 * Group: Tests
	 * _____________________________________
	 */

	/**
	 * Method: test_shortcode
	 *
	 * Test that shortcode wraps content, _show class is applied.
	 *
	 * Uses:
	 * - $this->mock_data() - post_id_1 - page containing shortcode in page content
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_plugin_init() - instance_options
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_shortcode_init() - selected_instance_options
	 * - ./tests/generated-plugin/template-parts/wpdtrt-test/content-test.php - output template
	 *
	 * See:
	 * - <Filter your content before displaying it: https://stackoverflow.com/a/22270259/6850747>.
	 */
	public function test_shortcode() {

		global $test_helpers;

		$this->go_to(
			get_post_permalink( $this->post_id_1 )
		);

		$content = apply_filters( 'the_content', get_post_field( 'post_content', $this->post_id_1 ) );

		$test_helpers->assert_equal_html(
			'<span class="wpdtrt-test wpdtrt-test_show" style="color:red;">Text to style</span>',
			trim( do_shortcode( trim( do_shortcode( $content ) ) ) ),
			'wpdtrt_text_shortcode does not wrap text'
		);
	}

	/**
	 * Method: test_shortcode_defaults
	 *
	 * Test that 'default' color value is output, in lieu of the author specifying this.
	 *
	 * Uses:
	 * - $this->mock_data() - post_id_2 - page containing shortcode in page content
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_plugin_init() - instance_options
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_shortcode_init() - selected_instance_options
	 * - ./tests/generated-plugin/template-parts/wpdtrt-test/content-test.php - output template
	 *
	 * See:
	 * - <Filter your content before displaying it: https://stackoverflow.com/a/22270259/6850747>.
	 */
	public function test_shortcode_defaults() {

		global $test_helpers;

		$this->go_to(
			get_post_permalink( $this->post_id_2 )
		);

		$content = apply_filters( 'the_content', get_post_field( 'post_content', $this->post_id_2 ) );

		$test_helpers->assert_equal_html(
			'<span class="wpdtrt-test wpdtrt-test_show" style="color:red;">Text to style</span>',
			trim( do_shortcode( trim( do_shortcode( $content ) ) ) ),
			'wpdtrt_text_shortcode default option value not output'
		);
	}

	/**
	 * Method: test_shortcode_options
	 *
	 * Test that author color value is respected.
	 *
	 * Uses:
	 * - $this->mock_data() - post_id_3 - page containing shortcode in page content
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_plugin_init() - instance_options
	 * - ./tests/generated-plugin/wpdtrt-test.php: wpdtrt_test_shortcode_init() - selected_instance_options
	 * - ./tests/generated-plugin/template-parts/wpdtrt-test/content-test.php - output template
	 *
	 * See:
	 * - <Filter your content before displaying it: https://stackoverflow.com/a/22270259/6850747>.
	 */
	public function test_shortcode_options() {

		global $test_helpers;

		$this->go_to(
			get_post_permalink( $this->post_id_3 )
		);

		$content = apply_filters( 'the_content', get_post_field( 'post_content', $this->post_id_3 ) );

		$test_helpers->assert_equal_html(
			'<span class="wpdtrt-test wpdtrt-test_show" style="color:green;">Text to style</span>',
			trim( do_shortcode( trim( do_shortcode( $content ) ) ) ),
			'wpdtrt_text_shortcode user option value not output'
		);
	}
}
