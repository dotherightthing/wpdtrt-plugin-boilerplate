<?php
/**
 * File: ./tests/helpers/helpers.php
 *
 * Unit test helpers.
 */

/**
 * Class: PluginTestHelpers
 *
 * WP_UnitTestCase unit test helpers for wpdtrt_plugin_boilerplate.
 */
class PluginTestHelpers extends WP_UnitTestCase {

	/**
	 * Method: test_workaround
	 *
	 * This file is included via a require_once
	 * which causes PHPUnit to search it for tests to run.
	 * This dummy test prevents the warning
	 * No tests found in class "PluginTestHelpers".
	 */
	public function test_dummy() {
		$this->assertEquals(
			true,
			true
		);
	}

	/**
	 * Method: assert_equal_html
	 *
	 * Compare two HTML fragments.
	 *
	 * Parameters:
	 *   $expected - Expected value
	 *   $actual - Actual value
	 *   $error_message - Message to show when strings don't match
	 *
	 * Uses:
	 * - <https://stackoverflow.com/a/26727310/6850747>
	 */
	public function assert_equal_html( string $expected, string $actual, string $error_message ) {
		$from = [ '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s' ];
		$to   = [ '>', '<', '\\1', '><' ];
		$this->assertEquals(
			preg_replace( $from, $to, $expected ),
			preg_replace( $from, $to, $actual ),
			$error_message
		);
	}

	/**
	 * Method: create_post
	 *
	 * Create post.
	 *
	 * Parameters:
	 *   $options - Options [$post_title, $post_date, $post_content].
	 *
	 * Returns:
	 *   Post ID
	 *
	 * See:
	 * - <https://developer.wordpress.org/reference/functions/wp_insert_post/>
	 * - <https://wordpress.stackexchange.com/questions/37163/proper-formatting-of-post-date-for-wp-insert-post
	 * - <https://codex.wordpress.org/Function_Reference/wp_update_post>
	 */
	public function create_post( array $options ) : int {

		$post_title   = null;
		$post_date    = null;
		$post_content = null;

		extract( $options, EXTR_IF_EXISTS );

		$post_id = $this->factory->post->create([
			'post_title'   => $post_title,
			'post_date'    => $post_date,
			'post_content' => $post_content,
			'post_type'    => 'post',
			'post_status'  => 'publish',
		]);

		return $post_id;
	}
}

$test_helpers = new PluginTestHelpers();
