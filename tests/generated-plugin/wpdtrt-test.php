<?php
/**
 * File: tests/generated-plugin/wpdtrt-test.php
 *
 * This plugin is used in unit testing,
 * to verify that a generated plugin can
 * run alongside wpdtrt-plugin-boilerplate.
 *
 * @wordpress-plugin
 * Plugin Name:  DTRT Test
 * Plugin URI:   https://github.com/dotherightthing/wpdtrt-plugin-boilerplate
 * Description:  Test plugin using the wpdtrt-plugin-boilerplate base classes.
 * Version:      1.7.12
 * Author:       Dan Smith
 * Author URI:   https://profiles.wordpress.org/dotherightthingnz
 * License:      GPLv2 or later
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wpdtrt-test
 * Domain Path:  /languages
 */

/**
 * Group: Constants
 *
 * WordPress makes use of the following constants when determining the path to the content and plugin directories.
 *
 * Note:
 * - These should not be used directly by plugins or themes, but are listed here for completeness.
 *   - WP_CONTENT_DIR  // no trailing slash, full paths only
 *   - WP_CONTENT_URL  // full url
 *   - WP_PLUGIN_DIR  // full path, no trailing slash
 *   - WP_PLUGIN_URL  // full url, no trailing slash
 *
 * - WordPress provides several functions for easily determining where a given file or directory lives.
 * - Always use these functions in your plugins instead of hard-coding references to the wp-content directory
 * or using the WordPress internal constants.
 *   - plugins_url()
 *   - plugin_dir_url()
 *   - plugin_dir_path()
 *   - plugin_basename()
 *
 * See:
 * - <https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Constants>
 * - <https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Plugins>
 * _____________________________________
 */

if ( ! defined( 'WPDTRT_TEST_VERSION' ) ) {
	/**
	 * Constant: WPDTRT_TEST_VERSION
	 *
	 * Plugin version.
	 *
	 * Note:
	 * - WP provides get_plugin_data(), but it only works within WP Admin,
	 *   so we define a constant instead.
	 *
	 * See:
	 * --- php
	 * $plugin_data = get_plugin_data( __FILE__ );
	 * $plugin_version = $plugin_data['Version'];
	 * ---
	 *
	 * - <https://wordpress.stackexchange.com/questions/18268/i-want-to-get-a-plugin-version-number-dynamically>
	 */
	define( 'WPDTRT_TEST_VERSION', '1.7.12' );
}

if ( ! defined( 'WPDTRT_TEST_PATH' ) ) {
	/**
	 * Constant: WPDTRT_TEST_PATH
	 *
	 * Plugin directory filesystem path.
	 *
	 * Note:
	 * - Value is the filesystem directory path (with trailing slash)
	 *
	 * See:
	 * - <https://developer.wordpress.org/reference/functions/plugin_dir_path/>
	 * - <https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything>
	 */
	define( 'WPDTRT_TEST_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPDTRT_TEST_URL' ) ) {
	/**
	 * Constant: WPDTRT_TEST_URL
	 *
	 * Plugin directory URL path.
	 *
	 * Note:
	 * - The URL (with trailing slash)
	 *
	 * See:
	 * - <https://codex.wordpress.org/Function_Reference/plugin_dir_url>
	 * - <https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything>
	 */
	define( 'WPDTRT_TEST_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Constant: WPDTRT_PLUGIN_CHILD
 *
 * Determines the correct path, from wpdtrt-plugin-boilerplate
 * to the PSR-4 autoloader.
 *
 * See:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51>
 */
if ( ! defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
	define( 'WPDTRT_PLUGIN_CHILD', true );
}

/**
 * Group: Variables
 * _____________________________________
 */

/**
 * Variable: $project_root_path
 *
 * Uses WPDTRT_TEST_TEST_DEPENDENCY to determine the correct path, from wpdtrt-foobar
 * to the PSR-4 autoloader.
 *
 * See:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/104>
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-WordPress-plugin-dependencies>
 */
if ( defined( 'WPDTRT_TEST_TEST_DEPENDENCY' ) ) {
	$project_root_path = realpath( __DIR__ . '/../../..' ) . '/';
} else {
	$project_root_path = '';
}

/**
 * Group: Require dependencies
 * _____________________________________
 */

require_once $project_root_path . 'vendor/autoload.php';

// sub classes, not loaded via PSR-4.
// comment out the ones you don't need, edit the ones you do.
require_once WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-plugin.php';
require_once WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-rewrite.php';
require_once WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-shortcode.php';
require_once WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-taxonomy.php';
require_once WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-widget.php';

// log & trace helpers.
global $debug;
$debug = new DoTheRightThing\WPDebug\Debug();

/**
 * Group: WordPress Integration
 *
 * Note:
 * - Default priority is 10. A higher priority runs later.
 * - register_activation_hook() is run before any of the provided hooks.
 *
 * See:
 * - <https://developer.wordpress.org/plugins/hooks/actions/#priority>
 * - <https://codex.wordpress.org/Function_Reference/register_activation_hook>
 * _____________________________________
 */

register_activation_hook( dirname( __FILE__ ), 'wpdtrt_test_helper_activate' );

add_action( 'init', 'wpdtrt_test_plugin_init', 0 );
add_action( 'init', 'wpdtrt_test_shortcode_init', 100 );
add_action( 'init', 'wpdtrt_test_taxonomy_init', 100 );
add_action( 'widgets_init', 'wpdtrt_test_widget_init', 10 );

register_deactivation_hook( dirname( __FILE__ ), 'wpdtrt_test_helper_deactivate' );

/**
 * Group: Plugin config
 * _____________________________________
 */

/**
 * Function: wpdtrt_test_helper_activate
 *
 * Register functions to be run when the plugin is activated.
 *
 * See:
 * - <https://codex.wordpress.org/Function_Reference/register_activation_hook>
 * - Plugin::helper_flush_rewrite_rules()
 *
 * TODO:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/128>
 */
function wpdtrt_test_helper_activate() {
	flush_rewrite_rules();
}

/**
 * Function: wpdtrt_test_helper_deactivate
 *
 * Register functions to be run when the plugin is deactivated.
 *
 * Note:
 * - WordPress 2.0+
 *
 * See:
 * - <https://codex.wordpress.org/Function_Reference/register_deactivation_hook>
 * - Plugin::helper_flush_rewrite_rules()
 *
 * TODO:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/128>
 */
function wpdtrt_test_helper_deactivate() {
	flush_rewrite_rules();
}

/**
 * Function: wpdtrt_test_plugin_init
 *
 * Plugin initialisaton.
 *
 * Note:
 * - We call init before widget_init so that the plugin object properties are available to it.
 * - If widget_init is not working when called via init with priority 1, try changing the priority of init to 0.
 * - init: Typically used by plugins to initialize. The current user is already authenticated by this time.
 * - widgets_init: Used to register sidebars. Fired at 'init' priority 1 (and so before 'init' actions with priority ≥ 1!)
 *
 * See:
 * - <https://wp-mix.com/wordpress-widget_init-not-working/>
 * - <https://codex.wordpress.org/Plugin_API/Action_Reference>
 * - <$plugin_options: Adding global options: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-global-options>
 * - <Options: Adding shortcode or widget options: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-shortcode-or-widget-options>
 * - <Options: Adding WordPress plugin dependencies: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-WordPress-plugin-dependencies>
 * - <Settings page: Adding a demo shortcode: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Settings-page:-Adding-a-demo-shortcode>
 *
 * TODO:
 * - Add a constructor function to WPDTRT_Blocks_Plugin, to explain the options array.
 */
function wpdtrt_test_plugin_init() {
	// pass object reference between classes via global
	// because the object does not exist until the WordPress init action has fired.
	global $wpdtrt_test_plugin;

	// Global options.
	$plugin_options = array(
		'api_key' => array(
			'type'  => 'password',
			'label' => __( 'API Key', 'wpdtrt-test' ),
			'size'  => 50,
		),
	);

	// Shortcode or Widget options.
	$instance_options = array(
		'hide'  => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Hide?', 'wpdtrt-test' ),
			'default' => 0,
		),
		'color' => array(
			'type'    => 'text',
			'size'    => 10,
			'label'   => __( 'Text color', 'wpdtrt-test' ),
			'tip'     => __( 'e.g. red', 'wpdtrt-test' ),
			'default' => __( 'red', 'wpdtrt-test' ),
		),
	);

	$plugin_dependencies = array();

	$ui_messages = array(
		'demo_data_description'       => __( 'This demo was generated from the following data', 'wpdtrt-test' ),
		'demo_data_displayed_length'  => __( 'results displayed', 'wpdtrt-test' ),
		'demo_data_length'            => __( 'results', 'wpdtrt-test' ),
		'demo_data_title'             => __( 'Demo data', 'wpdtrt-test' ),
		'demo_date_last_updated'      => __( 'Data last updated', 'wpdtrt-test' ),
		'demo_sample_title'           => __( 'Demo sample', 'wpdtrt-test' ),
		'demo_shortcode_title'        => __( 'Demo shortcode', 'wpdtrt-test' ),
		'insufficient_permissions'    => __( 'Sorry, you do not have sufficient permissions to access this page.', 'wpdtrt-test' ),
		'no_options_form_description' => __( 'There aren\'t currently any options.', 'wpdtrt-test' ),
		'noscript_warning'            => __( 'Please enable JavaScript', 'wpdtrt-test' ),
		'options_form_description'    => __( 'Please enter your preferences.', 'wpdtrt-test' ),
		'options_form_submit'         => __( 'Save Changes', 'wpdtrt-test' ),
		'options_form_title'          => __( 'General Settings', 'wpdtrt-test' ),
		'loading'                     => __( 'Loading latest data...', 'wpdtrt-test' ),
		'success'                     => __( 'settings successfully updated', 'wpdtrt-test' ),
	);

	// TODO: redundant - use defaults.
	$demo_shortcode_params = array(
		'name'   => 'wpdtrt_test_shortcode',
		'hide'   => 1,
		'number' => 1,
	);

	/**
	 * Variable: wpdtrt_test_plugin
	 *
	 * Instance of WPDTRT_Test_Plugin.
	 */
	$wpdtrt_test_plugin = new WPDTRT_Test_Plugin(
		array(
			'path'                  => WPDTRT_TEST_PATH,
			'url'                   => WPDTRT_TEST_URL,
			'version'               => WPDTRT_TEST_VERSION,
			'prefix'                => 'wpdtrt_test',
			'slug'                  => 'wpdtrt-test',
			'menu_title'            => __( 'Test', 'wpdtrt-test' ),
			'settings_title'        => __( 'Settings', 'wpdtrt-test' ),
			'developer_prefix'      => 'DTRT',
			'messages'              => $ui_messages,
			'plugin_options'        => $plugin_options,
			'instance_options'      => $instance_options,
			'plugin_dependencies'   => $plugin_dependencies,
			'demo_shortcode_params' => $demo_shortcode_params,
		)
	);
}

/**
 * Group: Rewrite config
 * _____________________________________
 */

/**
 * Function: wpdtrt_test_rewrite_init
 *
 * Register Rewrite.
 */
function wpdtrt_test_rewrite_init() {

	global $wpdtrt_test_plugin;

	$wpdtrt_test_rewrite = new WPDTRT_Test_Rewrite(
		array()
	);
}

/**
 * Group: Shortcode config
 * _____________________________________
 */

/**
 * Function: wpdtrt_test_shortcode_init
 *
 * Register Shortcode.
 */
function wpdtrt_test_shortcode_init() {

	global $wpdtrt_test_plugin;

	$wpdtrt_test_shortcode = new WPDTRT_Test_Shortcode(
		array(
			'name'                      => 'wpdtrt_test_shortcode',
			'plugin'                    => $wpdtrt_test_plugin,
			'template'                  => 'test',
			'selected_instance_options' => array(
				'hide',
				'color',
			),
		)
	);
}

/**
 * Group: Taxonomy config
 * _____________________________________
 */

/**
 * Function: wpdtrt_test_taxonomy_init
 *
 * Register Taxonomy.
 *
 * Returns:
 *   An instance of the WPDTRT_Test_Taxonomy.
 *
 * TODO:
 * - what is the type of this return?
 */
function wpdtrt_test_taxonomy_init() {

	global $wpdtrt_test_plugin;

	$wpdtrt_test_taxonomy = new WPDTRT_Test_Taxonomy(
		array(
			'name'                      => 'wpdtrt_test_taxonomy',
			'plugin'                    => $wpdtrt_test_plugin,
			'selected_instance_options' => array(),
			'taxonomy_options'          => array(
				'option1' => array(
					'type'              => 'text',
					'label'             => esc_html__( 'Option 1', 'wpdtrt-test' ),
					'admin_table'       => true,
					'admin_table_label' => esc_html__( 'Opt 1', 'wpdtrt-test ' ),
					'admin_table_sort'  => true,
					'tip'               => 'Enter something',
					'todo_condition'    => 'foo !== "bar"',
				),
			),
			'labels'                    => array(
				'slug'                       => 'wpdtrt_test_thing',
				'description'                => __( 'Things', 'wpdtrt-test ' ),
				'posttype'                   => 'post', // tourdiaries.
				'name'                       => __( 'Things', 'taxonomy general name' ),
				'singular_name'              => _x( 'Thing', 'taxonomy singular name' ),
				'menu_name'                  => __( 'Things', 'wpdtrt-test' ),
				'all_items'                  => __( 'All Things', 'wpdtrt-test' ),
				'add_new_item'               => __( 'Add New Thing', 'wpdtrt-test' ),
				'edit_item'                  => __( 'Edit Thing', 'wpdtrt-test' ),
				'view_item'                  => __( 'View Thing', 'wpdtrt-test' ),
				'update_item'                => __( 'Update Thing', 'wpdtrt-test' ),
				'new_item_name'              => __( 'New Thing Name', 'wpdtrt-test' ),
				'parent_item'                => __( 'Parent Thing', 'wpdtrt-test' ),
				'parent_item_colon'          => __( 'Parent Thing:', 'wpdtrt-test' ),
				'search_items'               => __( 'Search Things', 'wpdtrt-test' ),
				'popular_items'              => __( 'Popular Things', 'wpdtrt-test' ),
				'separate_items_with_commas' => __( 'Separate Things with commas', 'wpdtrt-test' ),
				'add_or_remove_items'        => __( 'Add or remove Things', 'wpdtrt-test' ),
				'choose_from_most_used'      => __( 'Choose from most used Things', 'wpdtrt-test' ),
				'not_found'                  => __( 'No Things found', 'wpdtrt-test' ),
			),
		)
	);

	// return a reference for unit testing.
	return $wpdtrt_test_taxonomy;
}

/**
 * Group: Widget config
 * _____________________________________
 */

/**
 * Function: wpdtrt_test_widget_init
 *
 * Register a WordPress widget, passing in an instance of our custom widget class.
 *
 * - The plugin does not require registration, but widgets and shortcodes do.
 * - Note: widget_init fires before init, unless init has a priority of 0
 *
 * Uses:
 * - ../../../../wp-includes/widgets.php
 * - <https://github.com/dotherightthing/wpdtrt/tree/master/library/sidebars.php>
 *
 * See:
 * - <https://codex.wordpress.org/Function_Reference/register_widget#Example
 * - <https://wp-mix.com/wordpress-widget_init-not-working/
 * - <https://codex.wordpress.org/Plugin_API/Action_Reference
 *
 * TODO:
 * - Add form field parameters to the options array.
 * - Investigate the 'classname' option.
 */
function wpdtrt_test_widget_init() {

	global $wpdtrt_test_plugin;

	$wpdtrt_test_widget = new WPDTRT_Test_Widget(
		array(
			'name'                      => 'wpdtrt_test_widget',
			'title'                     => __( 'Test Widget', 'wpdtrt-test' ),
			'description'               => __( 'Widget description.', 'wpdtrt-test' ),
			'plugin'                    => $wpdtrt_test_plugin,
			'template'                  => 'test',
			'selected_instance_options' => array(
				'hide',
				'color',
			),
		)
	);

	register_widget( $wpdtrt_test_widget );
}
