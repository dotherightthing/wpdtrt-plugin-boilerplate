<?php
/**
 * File: src/Plugin.php
 *
 * Plugin class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_9;

if ( ! class_exists( 'Plugin' ) ) {

	/**
	 * Class: Plugin
	 *
	 * Plugin base class.
	 *
	 * Note:
	 * - Contains boilerplate functions, including
	 * options page, field templating, error messaging, CSS, JS.
	 * - Use Shortcode for dependent shortcodes.
	 * - Use Widget for dependent widgets.
	 *
	 * Since:
	 *   1.0.0 - Added
	 */
	class Plugin {

		// https://secure.php.net/manual/en/language.oop5.constants.php.
		const WPDTRT_PLUGIN_VERSION = '1.7.9';

		/**
		 * Constructor: __construct
		 *
		 * Initialise the object's properties when it is instantiated.
		 *
		 * Note:
		 * - This is a protected method as every plugin uses a sub class:
		 * --- php
		 * class WPDTRT_Test_Plugin extends DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_7_9\Plugin {...}
		 * ---
		 *
		 * - A plugin-specific instance of this class is created on init:
		 * --- php
		 * add_action( 'init', '<%= nameSafe %>_init', 0 );
		 * ---
		 *
		 * - So this construct CANNOT contain anything that needs to run
		 * BEFORE the WordPress 'init'.
		 *
		 * Parameters:
		 *   $settings - Plugin options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function __construct( array $settings ) {

			// option variables.
			$url                   = null;
			$prefix                = null;
			$slug                  = null;
			$menu_title            = null;
			$settings_title        = null;
			$developer_prefix      = null;
			$path                  = null;
			$messages              = null;
			$version               = null;
			$demo_shortcode_params = null;

			// option arrays.
			// note that these should exclude 'value' keys,
			// to avoid overwriting existing user settings.
			$plugin_options      = null;
			$plugin_data         = null;
			$instance_options    = null;
			$plugin_dependencies = null;

			// overwrite options with values from the settings array.
			extract( $settings, EXTR_IF_EXISTS );

			// store option variables.
			$this->set_url( $url );
			$this->set_prefix( $prefix );
			$this->set_slug( $slug );
			$this->set_menu_title( $menu_title );
			$this->set_settings_title( $settings_title );
			$this->set_developer_prefix( $developer_prefix );
			$this->set_path( $path );
			$this->set_messages( $messages );
			$this->set_version( $version );
			$this->set_demo_shortcode_params( $demo_shortcode_params );

			// store option arrays.
			$this->set_plugin_options( $plugin_options, true );
			$this->set_plugin_data( isset( $plugin_data ) ? $plugin_data : array() );
			$this->set_instance_options( $instance_options );
			$this->set_wp_composer_dependencies_tgmpa( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/composer.json' );

			// hook in to WordPress.
			$this->wp_setup();
		}

		/**
		 * Group: WordPress Integration
		 * _____________________________________
		 */

		/**
		 * Method: wp_setup
		 *
		 * Initialise plugin options ONCE.
		 *
		 * Note:
		 * - Default priority is 10. A higher priority runs later.
		 * - register_activation_hook() is run before any of the provided hooks.
		 *
		 * See:
		 * - <Action order:https://codex.wordpress.org/Plugin_API/Action_Reference>
		 * - <Trying to get class to instantiate ONCE:https://wordpress.stackexchange.com/a/209772>
		 */
		protected function wp_setup() {

			/**
			 * $this->render_foobar() - infers that no args are to be passed, fails
			 *
			 * See:
			 * - <https://stackoverflow.com/questions/28954168/php-how-to-use-a-class-function-as-a-callback>
			 * - <https://tommcfarlin.com/wordpress-plugin-constructors-hooks/>
			 */
			add_action( 'admin_menu', array( $this, 'render_options_menu' ) );
			add_action( 'admin_notices', array( $this, 'render_settings_errors' ) );
			add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
			add_action( 'admin_head', array( $this, 'render_css_backend' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'render_css_frontend' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'render_js_frontend' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'render_js_backend' ) );
			add_action( 'tgmpa_register', array( $this, 'wp_register_plugin_dependencies' ) );
			add_action( 'post_type_link', array( $this, 'render_cpt_permalink_placeholders' ), 10, 3 ); // Custom Post Type.

			add_action( 'init', array( $this, 'helper_flush_rewrite_rules' ), 99 );

			// call the server side PHP function through admin-ajax.php.
			// see js/backend.js.
			add_action( 'wp_ajax_' . $this->get_prefix() . '_refresh_api_data', array( $this, 'refresh_api_data' ) );

			$plugin_root_relative_to_plugin_folder = $this->get_slug() . '/' . $this->get_slug() . '.php';
			add_filter( 'plugin_action_links_' . $plugin_root_relative_to_plugin_folder, array( $this, 'render_settings_link' ) );
			add_filter( 'plugin_action_links_' . $plugin_root_relative_to_plugin_folder, array( $this, 'render_library_link' ) );
		}

		/**
		 * Method: get_wp_composer_dependencies
		 *
		 * Get the WP plugin dependencies required for both TGMPA and WP Unit.
		 *
		 * Parameters:
		 *   $composer_json - Full (not relative) path to plugin's composer.json
		 *
		 * Returns:
		 *   Plugin dependencies
		 */
		public static function get_wp_composer_dependencies( $composer_json ) : array {

			if ( ! file_exists( $composer_json ) ) {
				return array();
			}

			$composer_config = file_get_contents( $composer_json ); // phpcs:ignore

			$obj = json_decode( $composer_config );

			// an alternative to $obj->{'varname'};
			// @see https://stackoverflow.com/a/758458 How do I access this object property with a hyphenated name?
			$composer_vars = get_object_vars( $obj );

			if ( key_exists( 'extra', $composer_vars ) ) {
				$extra      = $composer_vars['extra'];
				$extra_vars = get_object_vars( $extra );

				if ( key_exists( 'require-wp', $extra_vars ) ) {
					$require_wp_array = $extra_vars['require-wp'];
				}
			}

			$require_dev         = $composer_vars['require-dev'];
			$plugin_dependencies = array();

			if ( isset( $require_dev, $require_wp_array ) ) {
				foreach ( $require_wp_array as $require_wp ) {

					$require_dev_vars = get_object_vars( $require_dev );
					$require_wp_vars  = get_object_vars( $require_wp );

					$name         = $require_wp_vars['name'];
					$host         = $require_wp_vars['host'];
					$repository   = $require_wp_vars['repository'];
					$vendor       = explode( '/', $repository )[0];
					$slug         = explode( '/', $repository )[1];
					$version      = null;
					$source       = null;
					$external_url = null;
					$file         = $require_wp_vars['file'];

					if ( isset( $require_dev_vars[ $repository ] ) ) {
						$version = str_replace( '^', '', $require_dev_vars[ $repository ] );
					}

					if ( 'github' === $host ) {
						$source       = 'https://github.com/' . $repository . '/releases/download/' . $version . '/release.zip';
						$external_url = 'https://github.com/' . $repository;
					}

					$plugin_dependency = array(
						'name'     => $name,
						'slug'     => $slug,
						'required' => true, // this is output as 1.
					);

					if ( isset( $file ) ) {
						$plugin_dependency['file'] = $file;
					}

					if ( isset( $source ) ) {
						$plugin_dependency['source'] = $source;
					}

					if ( isset( $vendor ) ) {
						$plugin_dependency['vendor'] = $vendor;
					}

					if ( isset( $version ) ) {
						$plugin_dependency['name']    = $name . ' (' . $version . ')';
						$plugin_dependency['version'] = $version;
					}

					if ( isset( $external_url ) ) {
						$plugin_dependency['external_url'] = $external_url;
					}

					$plugin_dependencies[] = $plugin_dependency;
				}
			}

			return $plugin_dependencies;
		}

		/**
		 * Method: set_wp_composer_dependencies_tgmpa
		 *
		 * Register WP plugin dependencies with TGMPA.
		 *
		 * Parameters:
		 *   $composer_json - Full (not relative) path to plugin's composer.json
		 *
		 * Returns:
		 *   Updated plugin dependencies
		 *
		 * Example:
		 * --- PHP
		 * // wpdtrt-plugin-boilerplate/src/Plugin.php
		 * global $wpdtrt_test_plugin; // Access non-static methods of plugin class.
		 * $composer_json = dirname( __FILE__ ) . '/data/composer.json';
		 * $updated_plugin_dependencies = $wpdtrt_test_plugin->set_wp_composer_dependencies_tgmpa( $composer_json );
		 * ---
		 *
		 * See:
		 * - <Adding WordPress plugin dependencies: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Options:-Adding-WordPress-plugin-dependencies>.
		 */
		public function set_wp_composer_dependencies_tgmpa( string $composer_json ) : array {
			$wp_composer_dependencies = $this->get_wp_composer_dependencies( $composer_json );
			$tgmpa_props              = array(
				'name',
				'slug',
				'required',
				'source',
				'version',
				'external_url',
			);

			// remove array keys which don't map to the TGMPA dependency config.
			foreach ( $wp_composer_dependencies as $plugin_dependency ) {
				$tgmpa_dependency = array();

				foreach ( $tgmpa_props as $prop ) {
					if ( array_key_exists( $prop, $plugin_dependency ) ) {
						$tgmpa_dependency[ $prop ] = $plugin_dependency[ $prop ];
					}
				}

				$this->set_plugin_dependency( $tgmpa_dependency );
			}

			// test that the operation was successful.
			$plugin_dependencies = $this->get_plugin_dependencies();

			return $plugin_dependencies;
		}

		/**
		 * Method: get_wp_composer_dependencies_wpunit
		 *
		 * Used in wpdtrt-foobar/tests/bootstrap.php,
		 * to get the WP plugin dependency files required by WP Unit.
		 *
		 * Parameters:
		 *   $plugin_dependencies
		 *
		 * Returns:
		 *   Plugin files to require
		 *
		 * Example:
		 * --- PHP
		 * // wpdtrt-foobar/tests/bootstrap.php
		 * $composer_dependencies_to_require = WPDTRT_Foobar_Plugin::get_wp_composer_dependencies_wpunit( $composer_dependencies );
		 * }
		 * ---
		 */
		public static function get_wp_composer_dependencies_wpunit( array $plugin_dependencies ) : array {

			$plugin_files_to_require = array();

			foreach ( $plugin_dependencies as $plugin_dependency ) {
				$file   = $plugin_dependency['file'];
				$slug   = $plugin_dependency['slug'];
				$vendor = $plugin_dependency['vendor'];

				if ( 'dotherightthing' === $vendor ) {
					$vendor_dir = 'vendor/dotherightthing';

					// CONSTANT tells `pluginroot.php` where to find Composer's `autoload.php`.
					$constant = strtoupper( str_replace( '-', '_', $slug ) ) . '_TEST_DEPENDENCY';

					if ( ! defined( $constant ) ) {
						define( $constant, true );
					}
				} elseif ( 'wpackagist-plugin' === $vendor ) {
					$vendor_dir = 'wp-content/plugins';
				}

				$require = ( '/' . $vendor_dir . '/' . $slug . '/' . $file );

				$plugin_files_to_require[] = dirname( dirname( __FILE__ ) ) . $require;
			}

			return $plugin_files_to_require;
		}

		/**
		 * Method: wp_register_plugin_dependencies
		 *
		 * TGM Plugin Activation Configuration.
		 *
		 * Note:
		 * - Registers the required plugins for this theme.
		 * - This function is hooked into `tgmpa_register`,
		 *   which is fired on the WP `init` action on priority 10.
		 *
		 * See:
		 * - <https://github.com/TGMPA/TGM-Plugin-Activation>
		 * - <http://tgmpluginactivation.com/configuration/>
		 * - <http://tgmpluginactivation.com/download/> for more options
		 */
		public function wp_register_plugin_dependencies() {

			/**
			 * Include the TGM_Plugin_Activation class.
			 *
			 * Plugin:
			 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
			 */
			require_once $this->get_path() . 'vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php';

			/**
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$plugins = $this->get_plugin_dependencies();

			/**
			 * Array of configuration settings.
			 */
			$config = array(
				'id'           => $this->get_slug(),        // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',                       // Default absolute path to bundled plugins.
				'menu'         => 'tgmpa-install-plugins',  // Menu slug.
				'parent_slug'  => 'plugins.php',            // Parent menu slug.
				'capability'   => 'manage_options',         // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,                     // Show admin notices or not.
				'dismissable'  => true,                     // If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => '',                       // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => false,                    // Automatically activate plugins after installation or not.
				'message'      => '',                       // Message to output right before the plugins table.
			);

			tgmpa( $plugins, $config );
		}

		/**
		 * Group: Setters and Getters
		 * _____________________________________
		 */

		/**
		 * Method: set_url
		 *
		 * Set the value of $url.
		 *
		 * Parameters:
		 *   $new_url - New URL
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_url( string $new_url ) {
			$this->url = $new_url;
		}

		/**
		 * Method: get_url
		 *
		 * Get the value of $url.
		 *
		 * Returns:
		 *   The url which was set using set_url()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_url() : string {
			return $this->url;
		}

		/**
		 * Method: set_demo_shortcode_params
		 *
		 * Set the value of $demo_shortcode_params.
		 *
		 * Parameters:
		 *   $new_demo_shortcode_params - New demo shortcode params
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_demo_shortcode_params( array $new_demo_shortcode_params ) {
			$this->demo_shortcode_params = $new_demo_shortcode_params;
		}

		/**
		 * Method: get_demo_shortcode_params
		 *
		 * Get the value of $demo_shortcode_params.
		 *
		 * Returns:
		 *   The demo_shortcode_params which were set using set_demo_shortcode_params()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_demo_shortcode_params() : array {
			return $this->demo_shortcode_params;
		}

		/**
		 * Method: set_prefix
		 *
		 * Set the value of $prefix.
		 *
		 * Parameters:
		 *   $new_prefix - New prefix
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_prefix( string $new_prefix ) {
			$this->prefix = $new_prefix;
		}

		/**
		 * Method: get_prefix
		 *
		 * Get the value of $prefix (wpdtrt_foo).
		 *
		 * Returns:
		 *   The prefix which was set using set_prefix()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_prefix() : string {
			return $this->prefix;
		}

		/**
		 * Method: set_slug
		 *
		 * Set the value of $slug.
		 *
		 * Parameters:
		 *   $new_slug - New slug
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_slug( string $new_slug ) {
			$this->slug = $new_slug;
		}

		/**
		 * Method: get_slug
		 *
		 * Get the value of $slug.
		 *
		 * Returns:
		 *   The slug which was set using set_slug()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_slug() : string {
			return $this->slug;
		}

		/**
		 * Method: set_menu_title
		 *
		 * Set the value of $menu_title.
		 *
		 * Parameters:
		 *   $new_menu_title - New menu title
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_menu_title( string $new_menu_title ) {
			$this->menu_title = $new_menu_title;
		}

		/**
		 * Method: get_menu_title
		 *
		 * Get the value of $menu_title.
		 *
		 * Returns:
		 *   The menu_title which was set using set_menu_title()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_menu_title() : string {
			return $this->menu_title;
		}

		/**
		 * Method: set_settings_title
		 *
		 * Set the value of $settings_title.
		 *
		 * Parameters:
		 *   $new_settings_title - New settings title
		 *
		 * Since:
		 *   1.3.4 - Added
		 */
		protected function set_settings_title( string $new_settings_title = '' ) {
			$this->settings_title = $new_settings_title;
		}

		/**
		 * Method: get_settings_title
		 *
		 * Get the value of $settings_title.
		 *
		 * Returns:
		 *   The settings_title which was set using set_settings_title()
		 *
		 * Since:
		 *   1.3.4 - Added
		 */
		public function get_settings_title() : string {
			return $this->settings_title;
		}

		/**
		 * Method: set_developer_prefix
		 *
		 * Set the value of $developer_prefix.
		 *
		 * Parameters:
		 *   $new_developer_prefix - New developer prefix
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_developer_prefix( string $new_developer_prefix ) {
			$this->developer_prefix = $new_developer_prefix;
		}

		/**
		 * Method: get_developer_prefix
		 *
		 * Get the value of $developer_prefix.
		 *
		 * Returns:
		 *   The developer_prefix which was set using set_developer_prefix()
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_developer_prefix() : string {
			return $this->developer_prefix;
		}

		/**
		 * Method: set_messages
		 *
		 * Set the value of $messages.
		 *
		 * Parameters:
		 *   $new_messages - New messages
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_messages( array $new_messages ) {
			$this->messages = $new_messages;
		}

		/**
		 * Method: get_messages
		 *
		 * Get the value of $messages.
		 *
		 * Returns:
		 *   Messages
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_messages() : array {
			return $this->messages;
		}

		/**
		 * Method: get_success_message
		 *
		 * Get the value of the $success_message.
		 *
		 * Returns:
		 *   Success message
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_success_message() : string {
			$messages        = $this->get_messages();
			$success_message = $messages['success'];
			return $success_message;
		}

		/**
		 * Method: set_path
		 *
		 * Set the value of $path.
		 *
		 * Parameters:
		 *   $new_path - New path
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_path( string $new_path ) {
			$this->path = $new_path;
		}

		/**
		 * Method: get_path
		 *
		 * Get the value of $path.
		 *
		 * Returns:
		 *   Path
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_path() : string {
			return $this->path;
		}

		/**
		 * Method: set_options
		 *
		 * Set plugin options.
		 *
		 * Parameters:
		 *   $new_options - New options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_options( array $new_options ) {
			$old_options = $this->get_options();

			/**
			 * Merge old options with new options
			 * This overwrites the old values with any new values
			 */
			$options = array_merge( $old_options, $new_options );

			/**
			 * Save options object to WP Options table in database, as an array
			 *
			 * So that we only have to consume one row in the WP Options table
			 * WordPress automatically serializes this (into a string)
			 * because MySQL does not support arrays as a data type
			 *
			 * This function may be used in place of add_option, although it is not as flexible.
			 * update_option will check to see if the option already exists.
			 * If it does not, it will be added with add_option('option_name', 'option_value').
			 * Unless you need to specify the optional arguments of add_option(),
			 * update_option() is a useful catch-all for both adding and updating options.
			 *
			 * @example update_option( string $option, mixed $value, string|bool $autoload = null )
			 * @see https://codex.wordpress.org/Function_Reference/update_option
			 */
			update_option( $this->get_prefix(), $options, null );
		}

		/**
		 * Method: get_options
		 *
		 * Get plugin options, user values merged with the defaults.
		 *
		 * Returns:
		 *   Options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_options() : array {

			/**
			 * Load any plugin user settings, falling back to an empty $options_array if they don't exist yet
			 *
			 * @see https://developer.wordpress.org/reference/functions/get_option/#parameters
			 */
			$fallback_options_array = array(
				'plugin_options'      => array(),
				'plugin_data'         => array(),
				'plugin_data_options' => array(),
				'instance_options'    => array(),
				'plugin_dependencies' => array(),
			);

			$options = get_option( $this->get_prefix(), $fallback_options_array );

			return $options;
		}

		/**
		 * Method: unset_options
		 *
		 * Remove plugin options.
		 */
		public function unset_options() {
			delete_option( $this->get_prefix() );
		}

		/**
		 * Method: set_plugin_options
		 *
		 * Set the value of $plugin_options.
		 *
		 * Note:
		 * - Add any new options or attributes in the configuration.
		 * - Adds the value attribute once this has been supplied by the user.
		 *
		 * Parameters:
		 *   $new_plugin_options - New plugin options
		 *   $is_raw_config_options - Whether the options are raw config options
		 *
		 * Returns:
		 *   Merged options (for unit testing)
		 *
		 * Since:
		 *   1.0.0 - Added
		 *   1.3.0 - Fixed option merging
		 *   1.7.6 - Fixed check for raw config options
		 */
		public function set_plugin_options( array $new_plugin_options, bool $is_raw_config_options = false ) : array {

			// old options stored in database.
			$old_plugin_options = $this->get_plugin_options();

			if ( ! isset( $new_plugin_options ) ) {
				return $old_plugin_options;
			}

			if ( $is_raw_config_options ) {
				// the config array is the canonical set of options.
				$config_options = $new_plugin_options;
				$user_values    = $old_plugin_options;
			} else {
				// else we're updating the existing config options.
				// also applying the user value if one has been set (on submit).
				$config_options = $old_plugin_options;
				$user_values    = $new_plugin_options;
			}

			// merged config and values.
			$merged_plugin_options = $this->helper_add_values_to_options( $config_options, $user_values );

			// save the merged options.
			$options                   = $this->get_options();
			$options['plugin_options'] = $merged_plugin_options;
			$this->set_options( $options );

			// return array for unit testing.
			return $options['plugin_options'];
		}

		/**
		 * Method: get_plugin_options
		 *
		 * Get the value of $plugin_options.
		 *
		 * Returns:
		 *   Plugin options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_plugin_options() : array {
			$options        = $this->get_options();
			$plugin_options = $options['plugin_options'];
			return $plugin_options;
		}

		/**
		 * Method: set_plugin_dependency
		 *
		 * Store a plugin dependency for loading via TGMPA.
		 *
		 * Note:
		 * - This is only called by set_wp_composer_dependencies().
		 *
		 * Parameters:
		 *   $new_plugin_dependency - New plugin dependency
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function set_plugin_dependency( array $new_plugin_dependency ) {

			if ( ! isset( $new_plugin_dependency ) ) {
				return;
			}

			// these are the dependencies set by set_plugin_dependencies().
			$old_plugin_dependencies = $this->get_plugin_dependencies();

			foreach ( $old_plugin_dependencies as $key => $value ) {

				// clean up old, malformed dependencies
				// possibly due to set_plugin_dependencies() called
				// instead of set_plugin_dependency()
				// #150.
				if ( ! is_array( $value ) ) {
					unset( $old_plugin_dependencies[ $key ] );
				}

				// if the new dependency already exists.
				if ( $value['slug'] === $new_plugin_dependency['slug'] ) {
					// remove the old entry so we can set it again below,
					// with the updated version,
					// or at least the one set last.
					unset( $old_plugin_dependencies[ $key ] );
				}
			}

			// return all the values from the array and indexes the array numerically.
			$old_plugin_dependencies_reindexed = array_values( $old_plugin_dependencies );

			// this changes the array order, but that's not important to us.
			array_push( $old_plugin_dependencies_reindexed, $new_plugin_dependency );

			/**
			 * Merge old options with new options
			 * This overwrites the old values with any new values
			 */
			$options                        = $this->get_options();
			$options['plugin_dependencies'] = $old_plugin_dependencies_reindexed;
			$this->set_options( $options );
		}

		/**
		 * Method: get_plugin_dependencies
		 *
		 * Get a list of plugin dependencies to load via TGMPA.
		 *
		 * Returns:
		 *   Plugin dependencies
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_plugin_dependencies() : array {
			$options             = $this->get_options();
			$plugin_dependencies = $options['plugin_dependencies'];
			// remove empty array elements before returning.
			return array_filter( $plugin_dependencies );
		}

		/**
		 * Method: set_plugin_data
		 *
		 * Set the value of $plugin_data.
		 *
		 * Parameters:
		 *   $new_plugin_data - New plugin data
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function set_plugin_data( array $new_plugin_data ) {

			if ( ! isset( $new_plugin_data ) ) {
				return;
			}

			$options                = $this->get_options();
			$options['plugin_data'] = $new_plugin_data;
			$this->set_options( $options );
		}

		/**
		 * Method: get_plugin_data
		 *
		 * Get the value of $plugin_data.
		 *
		 * Returns:
		 *   Plugin data
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_plugin_data() : array {
			$options     = $this->get_options();
			$plugin_data = $options['plugin_data'];
			return $plugin_data;
		}

		/**
		 * Methods: get_plugin_data_length
		 *
		 * Get the number of items in $plugin_data.
		 *
		 * Returns:
		 *   Number of items
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_plugin_data_length() : int {
			$plugin_data     = $this->get_plugin_data();
			$number_of_items = count( $plugin_data );
			return $number_of_items;
		}

		/**
		 * Method: set_plugin_data_options
		 *
		 * Set the value of $plugin_data_options.
		 *
		 * Parameters:
		 *   $new_plugin_data_options - New plugin data options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function set_plugin_data_options( array $new_plugin_data_options ) {
			$options                        = $this->get_options();
			$options['plugin_data_options'] = $new_plugin_data_options;
			$this->set_options( $options );
		}

		/**
		 * Method: get_plugin_data_options
		 *
		 * Get the value of $plugin_data_options.
		 *
		 * Returns:
		 *   Plugin data options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_plugin_data_options() : array {
			$options             = $this->get_options();
			$plugin_data_options = $options['plugin_data_options'];

			if ( ! isset( $plugin_data_options ) ) {
				$plugin_data_options = array();
			}

			return $plugin_data_options;
		}

		/**
		 * Method: set_instance_options
		 *
		 * Set the value of $instance_options, replacing the existing options.
		 *
		 * Parameters:
		 *   $new_instance_options - New instance options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function set_instance_options( array $new_instance_options ) {
			$options                     = $this->get_options();
			$options['instance_options'] = $new_instance_options;
			$this->set_options( $options );
		}

		/**
		 * Method: get_instance_options
		 *
		 * Get the value of $instance_options.
		 *
		 * Returns:
		 *   Instance options
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_instance_options() : array {
			$options          = $this->get_options();
			$instance_options = $options['instance_options'];
			return $instance_options;
		}

		/**
		 * Method: set_version
		 *
		 * Set the value of $version.
		 *
		 * Parameters:
		 *   $new_version - New version
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function set_version( string $new_version ) {
			$this->version = $new_version;
		}

		/**
		 * Method: get_version
		 *
		 * Get the value of $version.
		 *
		 * Returns:
		 *   Version
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function get_version() : string {
			return $this->version;
		}

		/**
		 * Method: get_api_data
		 *
		 * Request the data from the API.
		 *
		 * Note:
		 * - The endpoint URL is sometimes constructed from plugin specific settings fields.
		 * - We need to manually assemble the endpoint string from dynamic data.
		 * - As this data and assembly is contextual, it is done in the child plugin.
		 *
		 * - A WordPress filter is used to get the correct value of $endpoint at runtime.
		 * - This allows us to keep the get_api_data() code in the parent class,
		 * - rather than expecting authors to overwrite get_api_data() with in the child class.
		 *
		 * Parameters:
		 *   $endpoint - Endpoint, used for unit testing
		 *
		 * Returns:
		 *   The body of the JSON response, as an associative array
		 *
		 * Example:
		 * --- php
		 * public function wpdtrt_forms_set_api_endpoint { return $endpoint; }
		 * add_filter( 'wpdtrt_forms_set_api_endpoint', [$this, 'set_api_endpoint'] );
		 * ---
		 *
		 * Uses:
		 * - ../../../../wp-includes/http.php
		 *
		 * See:
		 * - get_api_endpoint()
		 * - <https://developer.wordpress.org/reference/functions/wp_remote_get/>
		 * - <http://php.net/manual/en/function.json-decode.php>
		 *
		 * Since:
		 *   1.0.0 - Added
		 *   1.3.4 - Use get_api_endpoint() to pass in the endpoint
		 */
		public function get_api_data( string $endpoint = '' ) {

			// Call child plugin method:
			// A filter is used rather than an action as actions do not return a value.
			// A prefix prevents the filter from affecting other active instances of wpplugin.
			$child_plugin_filter = $this->get_prefix() . '_set_api_endpoint';
			$endpoint            = apply_filters( $child_plugin_filter, $endpoint );

			/**
			 * About: Demo shortcode Ajax
			 *
			 * This method is called by refresh_api_data()
			 * for ALL demo shortcodes,
			 * regardless of whether they are populated from API calls
			 * or just from the regular
			 * template-parts/pluginname/content-foo.php.
			 *
			 * If no $endpoint is passed into this method
			 * then we know that the plugin/shortcode
			 * doesn't actually need any API data.
			 * So we just return an info message.
			 */
			$fallback_for_plugin_that_does_not_need_api_data = '<p>No data was required to render this example</p>';

			if ( '' !== $endpoint ) {

				$args = array(
					'timeout'  => 30, // seconds to wait for the request to complete.
					'blocking' => true, // false = nothing loads.
				);

				$response = wp_remote_get(
					$endpoint,
					$args
				);

				/**
				 * Return the body, not the header
				 * Note: There is an optional boolean argument,
				 * which returns an associative array if TRUE.
				 */
				$api_data = json_decode( $response['body'], true );
			} else {
				/**
				 * Return a message.
				 * This is redundant UI,
				 * but it works as learning tool.
				 */
				$api_data = $fallback_for_plugin_that_does_not_need_api_data;
			}

			// the data has to be stored,
			// because the return below does not send
			// the data back to the calling function,
			// rather it gets it from the plugin data store.
			$this->set_plugin_data( $api_data );

			// Save the data and retrieval time.
			$plugin_data_options                 = $this->get_plugin_data_options();
			$plugin_data_options['last_updated'] = time();
			$this->set_plugin_data_options( $plugin_data_options );

			return $api_data;
		}

		/**
		 * Method: refresh_api_data
		 *
		 * Refresh the data from the API.
		 *
		 * Note:
		 * - The 'action' key's value, 'refresh_api_data',
		 *    matches the latter half of the action 'wp_ajax_refresh_api_data' in our AJAX handler.
		 * - This is because it is used to call the server side PHP function through admin-ajax.php.
		 * - If an action is not specified, admin-ajax.php will exit, and return 0 in the process.
		 *
		 * Parameters:
		 *   $format - The data format ('ui'|'data')
		 *
		 * See:
		 * - $this->__construct()
		 * - $this->render_js_backend()
		 * - js/backend.js
		 * - <https://codex.wordpress.org/AJAX_in_Plugins>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function refresh_api_data( string $format ) {
			$format              = sanitize_text_field( $_POST['format'] );
			$plugin_data_options = $this->get_plugin_data_options();
			$existing_data       = $this->get_plugin_data();
			$last_updated        = isset( $plugin_data_options['last_updated'] ) ? $plugin_data_options['last_updated'] : false;
			$force_refresh       = isset( $plugin_data_options['force_refresh'] ) ? $plugin_data_options['force_refresh'] : false;

			// if the data has previously been requested AND has loaded,
			// only update it if it is stale.
			if ( $last_updated && $existing_data ) {
				$current_time      = time();
				$update_difference = $current_time - $last_updated;
				$one_hour          = ( 1 * 60 * 60 );
				$do_refresh        = ( $update_difference > $one_hour );
			} else {
				$do_refresh = false;
			}

			if ( $force_refresh ) {
				$do_refresh = true;
			}

			if ( $do_refresh ) {
				$data = $this->get_api_data();
			} else {
				$data = $existing_data;
			}

			// backend.js makes 2 calls to this function:
			// 1. to render the data as a shortcode
			// 2. to display the data used to construct the shortcode.
			//
			// update the UI.
			if ( 'ui' === $format ) {
				// build the shortcode.
				$shortcode = $this->helper_build_demo_shortcode();
				// render the shortcode HTML.
				echo $this->render_demo_shortcode( $shortcode );
			} elseif ( 'data' === $format ) {
				// render the JSON response
				// from get_api_data()
				// (if there was data).
				echo $this->render_demo_shortcode_data();
			}

			/**
			 * Let the Ajax know when the entire function has completed
			 *  wp_die() vs die() vs exit().
			 *  Most of the time you should be using wp_die() in your Ajax callback function.
			 *  This provides better integration with WordPress and makes it easier to test your code.
			 */
			wp_die();
		}

		/**
		 * Group: Helpers
		 * _____________________________________
		 */

		/**
		 * Method: helper_flush_rewrite_rules
		 *
		 * Flush all plugin rewrite rules.
		 *
		 * Note:
		 * - This is hooked into the init action,
		 *   but as flushing is an expensive operation,
		 *   and the init action fires once on every page load.
		 * - This is only run if $plugin_options['flush_rewrite_rules'].
		 * - This could also be called on activation and deactivation, but see #128.
		 *
		 * Parameters:
		 *   $force - Whether to force run the function, in spite of the toggle setting
		 *
		 * Returns:
		 *   Whether the rules were flushed or not
		 *
		 * See:
		 * - <How do call flush_rewrite_rules?: https://carlalexander.ca/wordpress-adventurous-rewrite-api/>
		 * - <#128: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/128>
		 *
		 * Since:
		 *   1.4.16 - Added
		 */
		public function helper_flush_rewrite_rules( bool $force ) : bool {

			$flushed        = false;
			$plugin_options = $this->get_plugin_options();

			if ( $force || ( array_key_exists( 'flush_rewrite_rules', $plugin_options ) && ( true === $plugin_options['flush_rewrite_rules'] ) ) ) {

				$flushed = true;
				flush_rewrite_rules();

				$plugin_options['flush_rewrite_rules'] = false;
				$this->set_plugin_options( $plugin_options );
			}

			return $flushed;
		}

		/**
		 * Method: helper_normalise_field_value
		 *
		 * Get a usable value for every form element type.
		 *
		 * Parameters:
		 *   $field_value - Field value
		 *   $field_type - Field type
		 *
		 * Returns:
		 *   Normalised field value
		 *
		 * TODO:
		 * - Add field validation feedback (#10)
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function helper_normalise_field_value( ?string $field_value, string $field_type ) : ?string {
			$normalised_field_value = null;

			if ( isset( $field_value ) ) {
				// If something was entered into the field,
				// then save the new value.
				// ( '1', '0', '', true, false ) === isset;
				// ( null ) === !isset;.
				$normalised_field_value = $field_value;
			} else {
				if ( 'checkbox' === $field_type ) {
					// but if a checkbox is unchecked,
					// then do change the saved instance value,
					// otherwise the checkbox will stay checked.
					$normalised_field_value = '';
				} elseif ( 'select' === $field_type ) {
					// but if the null option in a select is selected,
					// then do change the saved instance value,
					// otherwise the old option will stay selected.
					$normalised_field_value = '';
				} elseif ( 'file' === $field_type ) {
					$normalised_field_value = '';
				}
			}

			return $normalised_field_value;
		}

		/**
		 * Method: helper_array_filter_not_null
		 *
		 * Callback function for array_filter which only removes NULL keys.
		 *
		 * Parameters:
		 *   $arr - The array to filter
		 *
		 * Returns:
		 *   The filtered array
		 *
		 * See:
		 * - <http://php.net/manual/en/function.array-filter.php#115777>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function helper_array_filter_not_null( array $arr ) : array {
			return ! is_null( $arr );
		}

		/**
		 * Method: helper_get_default_value
		 *
		 * Get the default value from an input type.
		 *
		 * Parameters:
		 *   $input_type - Input type
		 *
		 * Returns:
		 *   Default value
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function helper_get_default_value( string $input_type ) : ?string {

			if ( 'select' === $input_type ) {
				$default_value = null;
			} elseif ( 'checkbox' === $input_type ) {
				$default_value = '';
			} elseif ( 'radio' === $input_type ) {
				$default_value = '';
			} elseif ( 'password' === $input_type ) {
				$default_value = '';
			} elseif ( 'text' === $input_type ) {
				$default_value = '';
			} else {
				$default_value = null;
			}

			return $default_value;
		}

		/**
		 * Method: helper_options_saved
		 *
		 * Determine whether the options page form has been submitted or not.
		 *
		 * Returns:
		 *   Whether the options were saved
		 *
		 * TODO:
		 * - Incorporate validation checks to ensure that all expected inputs are present (#10)
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function helper_options_saved() : bool {
			$helper_options_saved = false;

			if ( isset( $_POST['wpdtrt_plugin_boilerplate_form_submitted'] ) ) {

				// check that the form submission was legitimate.
				$hidden_field = esc_html( $_POST['wpdtrt_plugin_boilerplate_form_submitted'] );

				if ( 'Y' === $hidden_field ) {
					$helper_options_saved = true;
				}
			}

			return $helper_options_saved;
		}

		/**
		 * Method: helper_build_demo_shortcode
		 *
		 * Build demo shortcode.
		 *
		 * Returns:
		 *   Options page demo shortcode
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function helper_build_demo_shortcode() : string {
			$params = $this->get_demo_shortcode_params();

			if ( ! isset( $params ) || empty( $params ) ) {
				return '';
			}

			$options_page_demo_shortcode = '[';

			foreach ( $params as $key => $value ) {

				if ( 'name' === $key ) {
					$options_page_demo_shortcode .= $value;
				} elseif ( substr( $key, 0, 5 ) !== 'mock_' ) {
					$options_page_demo_shortcode .= " {$key}=\"{$value}\"";
				}
			}

			$options_page_demo_shortcode .= ']';

			/**
			* Render demo shortcode (update the UI)
			*/
			return $options_page_demo_shortcode;
		}

		/**
		 * Method: helper_add_values_to_options
		 *
		 * Merge option arrays, adding any new items.
		 *
		 * Parameters:
		 *   $config_options - Config options
		 *   $user_values - User values
		 *
		 * Returns:
		 *   Merged options
		 *
		 * Since:
		 *   1.4.0
		 */
		public function helper_add_values_to_options( array $config_options, array $user_values ) : array {

			if ( ! is_array( $config_options ) ) {
				$config_options = array();
			}

			if ( ! is_array( $user_values ) ) {
				$user_values = array();
			}

			// the config options are the canonical source,
			// and determine which options are added or removed.
			$merged_options = $config_options;

			// all existing 'old' options, e.g. 'google_static_maps_api_key' etc.
			foreach ( $config_options as $option_name => $form_input ) {

				// each option describes a $form_input using an array of HTML attributes,
				// 'type', 'label', 'size' etc, plus a 'value' if one has been set or selected by the user.
				if ( is_array( $form_input ) ) {
					// if this option exists in the user array.
					if ( array_key_exists( $option_name, $user_values ) ) {
						// and if it has a value.
						if ( isset( $user_values[ $option_name ]['value'] ) ) {
							// then copy the value into the config.
							$merged_options[ $option_name ]['value'] = $user_values[ $option_name ]['value'];
						}
					}
				}
			}

			return $merged_options;
		}

		/**
		 * Group: Renderers
		 * _____________________________________
		 */

		/**
		 * Method: render_cpt_permalink_placeholders
		 *
		 * Support Custom Field %placeholders% in Custom Post Type permalinks.
		 *
		 * Note:
		 * - This replacement is only applied when the permalink is generated
		 *   e.g. on an archive listing or wpadmin edit page,
		 *   NOT in the rewrite rules / when the page is loaded.
		 *
		 * Parameters:
		 *   $permalink - The post URL
		 *   $post - The post object
		 *   $leavename - Whether to keep the post name
		 *
		 * Returns:
		 *   Permalink
		 *
		 * Example:
		 * --- php
		 * // wpdtrt-dbth/library/register_post_type_tourdiaries.php
		 * 'rewrite' => array(
		 *   'slug' => 'tourdiaries/%wpdtrt_tourdates_taxonomy_tour%/%wpdtrt_tourdates_cf_daynumber%',
		 *   'with_front' => false
		 * )
		 * ---
		 *
		 * See:
		 * - <Add Custom Taxonomy Tags to Your WordPress Permalinks: http://shibashake.com/wordpress-theme/add-custom-taxonomy-tags-to-your-wordpress-permalinks>
		 * - <Custom Post Type Permalinks - Part 2: http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2#conflict>
		 * - <WordPress Custom Type permalink containing Taxonomy slug: https://stackoverflow.com/questions/7723457/wordpress-custom-type-permalink-containing-taxonomy-slug>
		 * <Edit Slug Button Missing in WordPress: https://kellenmace.com/edit-slug-button-missing-in-wordpress/>
		 * <#44: Permalink Edit button missing: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/44>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_cpt_permalink_placeholders( $permalink, $post, $leavename ) : string {

			// Get post.
			$post_id = $post->ID;
			$prefix  = $this->get_prefix();

			// extract all %placeholders% from the permalink.
			// https://regex101.com/.
			preg_match_all( '/(?<=\/%' . $prefix . '_cf_).+?(?=%\/)/', $permalink, $placeholders, PREG_OFFSET_CAPTURE );

			// placeholders in an array of taxonomy/term arrays.
			foreach ( $placeholders[0] as $placeholder ) {

				$placeholder_name = $prefix . '_cf_' . $placeholder[0];

				if ( metadata_exists( 'post', $post_id, $placeholder_name ) ) {
					$replacement = get_post_meta( $post_id, $placeholder_name, true );
					$permalink   = str_replace( ( '%' . $placeholder_name . '%' ), $replacement, $permalink );
				}
			}

			return $permalink;
		}

		/**
		 * Method: render_demo_shortcode
		 *
		 * Render demo shortcode.
		 *
		 * Parameters:
		 *   $shortcode - Shortcode
		 *
		 * Returns:
		 *   Shortcode HTML
		 *
		 * TODO:
		 *   Error when dumping some data objects (#37)
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function render_demo_shortcode( string $shortcode ) : string {
			return do_shortcode( $shortcode );
		}

		/**
		 * Method: render_demo_shortcode_data
		 *
		 * Render demo shortcode data.
		 *
		 * Note:
		 * - For the purposes of debugging, we also display the raw data.
		 * - var_dump is prefereable to print_r,
		 *   because it reveals the data types used,
		 *   so we can check whether the data is in the expected format.
		 *
		 * Returns:
		 *   Indented data
		 *
		 * See:
		 * - <print_r vs var_dump: https://gist.github.com/dotherightthing/fa154026945c9c8bb8996db1b5ae15e3>
		 * - <How can I capture the result of var_dump to a string?: https://stackoverflow.com/a/139553/6850747>
		 *
		 * TODO:
		 * - Error when dumping some data objects (#37)
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		protected function render_demo_shortcode_data() : string {
			$plugin_data           = $this->get_plugin_data();
			$data_str              = '';
			$demo_shortcode_params = $this->get_demo_shortcode_params();
			$max_length            = $demo_shortcode_params['number'];

			if ( empty( $plugin_data ) ) {
				return $data_str;
			}

			$data_str .= '<pre><code>';
			$data_str .= "{\r\n";

			$count = 0;

			foreach ( $plugin_data as $key => $val ) {
				$data_str .= var_export( $plugin_data[ $key ], true );

				$count++;

				// when we reach the end of the sample, stop looping.
				if ( $count === $max_length ) {
					break;
				}
			}

			$data_str .= "}\r\n";
			$data_str .= '</code></pre>';

			return $data_str;
		}

		/**
		 * Method: render_last_updated_humanised
		 *
		 * Render a human readable last updated date.
		 *
		 * Note:
		 * - Works best with General Settings > Date Format > Custom
		 *
		 * Returns:
		 *   Humanised date
		 *
		 * See:
		 * - <https://codex.wordpress.org/Option_Reference>
		 * - <https://codex.wordpress.org/Function_Reference/get_gmt_from_date>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_last_updated_humanised() : string {
			$last_updated_str    = '';
			$plugin_data_options = $this->get_plugin_data_options();
			$last_updated        = isset( $plugin_data_options['last_updated'] ) ? $plugin_data_options['last_updated'] : false;

			if ( ! $last_updated ) {
				return $last_updated_str;
			}

			// use the date format set by the user.
			$wp_date_format = get_option( 'date_format' );
			$wp_time_format = get_option( 'time_format' );

			// get the Local Time from a GMT/UTC timestamp.
			$last_updated_str .= get_date_from_gmt(
				date( 'Y-m-d H:i:s', $last_updated ),
				( $wp_time_format . ', ' . $wp_date_format ) // http://php.net/manual/en/datetime.format.php.
			);

			return $last_updated_str;
		}

		/**
		 * Method: render_js_frontend
		 *
		 * Attach JS for front-end widgets and shortcodes.
		 *
		 * Note:
		 * - Generate a configuration object which the JavaScript can access.
		 * - When an Ajax command is submitted, pass it to our function via the Admin Ajax page.
		 *
		 * See:
		 * - <https://codex.wordpress.org/AJAX_in_Plugins>
		 * - <https://codex.wordpress.org/Function_Reference/wp_localize_script>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_js_frontend() {
			$attach_to_footer = true;

			/**
			 * Registering scripts is technically not necessary, but highly recommended nonetheless.
			 *
			 * Scripts that have been pre-registered using wp_register_script()
			 * do not need to be manually enqueued using wp_enqueue_script()
			 * if they are listed as a dependency of another script that is enqueued.
			 * WordPress will automatically include the registered script
			 * before it includes the enqueued script that lists the registered script’s handle as a dependency.
			 *
			 * Note: If a dependency is shared between plugins/theme,
			 *  the hook must match, otherwise the dependency will be loaded twice,
			 *  potentially overriding variables and generating errors.
			 *
			 * @see https://developer.wordpress.org/reference/functions/wp_register_script/#more-information
			 */

			wp_enqueue_script( $this->get_prefix(),
				$this->get_url() . 'js/frontend-es5.js',
				array(
					// load these registered dependencies first.
					'jquery',
				),
				$this->get_version(),
				$attach_to_footer
			);

			wp_localize_script( $this->get_prefix(),
				$this->get_prefix() . '_config',
				array(
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php,
					// but we need to explicitly expose it to frontend pages.
					'ajaxurl' => admin_url( 'admin-ajax.php' ), // wpdtrt_foobar_config.ajaxurl.
					'options' => $this->get_options(), // wpdtrt_foobar_config.options.
				)
			);
		}

		/**
		 * Method: render_js_backend
		 *
		 * Attach JS for back-end admin pages.
		 *
		 * Note:
		 * - For consistency with render_js_frontend,
		 *   generate a configuration object which the JavaScript can access.
		 * - When an Ajax command is submitted, pass it to our function via the Admin Ajax page.
		 *
		 * Parameters:
		 *   $hook_suffix - The current admin page
		 *
		 * See:
		 * - <https://codex.wordpress.org/AJAX_in_Plugins>
		 * - <https://codex.wordpress.org/Function_Reference/wp_localize_script>
		 * - <https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_js_backend( string $hook_suffix ) {
			if ( ( 'settings_page_' . $this->get_slug() ) !== $hook_suffix ) {
				return;
			}

			$attach_to_footer = true;

			wp_enqueue_script( $this->get_prefix() . '_backend',
				$this->get_url() . 'js/backend-es5.js',
				array(
					// load these registered dependencies first.
					'jquery',
				),
				$this->get_version(),
				$attach_to_footer
			);

			$plugin_data_options = $this->get_plugin_data_options();

			$demo_shortcode_params = $this->get_demo_shortcode_params();

			$force_refresh = isset( $plugin_data_options['force_refresh'] ) ? $plugin_data_options['force_refresh'] : false;

			wp_localize_script( $this->get_prefix() . '_backend',
				'wpdtrtPluginBoilerplateConfig',
				array(
					'prefix'           => $this->get_prefix(),
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'messages'         => $this->get_messages(),
					'force_refresh'    => $force_refresh,
					'refresh_api_data' => isset( $demo_shortcode_params ) ? 'true' : 'false',
				)
			);
		}

		/**
		 * Method: render_options_menu
		 *
		 * Display a link to the options page in the admin menu.
		 *
		 * Uses:
		 *   ../../../../wp-admin/includes/plugin.php
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/functions/add_options_page/>
		 */
		public function render_options_menu() {
			add_options_page(
				$this->get_developer_prefix() . ' ' . $this->get_menu_title(), // <title>.
				$this->get_menu_title(), // menu.
				'manage_options', // capability.
				$this->get_slug(), // menu_slug.
				array( $this, 'render_options_page' ) // function callback.
			);
		}

		/**
		 * Method: render_settings_link
		 *
		 * Display a link to the plugin settings page in the plugins list.
		 *
		 * Parameters:
		 *   $links - Links
		 *
		 * Returns:
		 *   Links
		 *
		 * See:
		 * - <https://isabelcastillo.com/settings-link-plugin-plugins>
		 * - <https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)>
		 *
		 * Since:
		 *   1.3.4 - Added
		 */
		public function render_settings_link( array $links ) : array {
			$settings_url  = get_admin_url() . 'options-general.php?page=' . $this->get_slug();
			$settings_link = '<a href="' . $settings_url . '">' . $this->settings_title . '</a>';

			// prepend $setting_link to the beginning of the $links array.
			array_unshift( $links, $settings_link );

			return $links;
		}

		/**
		 * Method: render_library_link
		 *
		 * Display a link to the wpdtrt-plugin-boilerplate library.
		 *
		 * Parameters:
		 *   $links - Links
		 *
		 * Returns:
		 *   Links
		 *
		 * Since:
		 *   1.3.6 - Added
		 */
		public function render_library_link( array $links ) : array {

			$library_link = '<a href="https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/releases/tag/' . self::WPDTRT_PLUGIN_VERSION . '">DTRT WP Plugin ' . self::WPDTRT_PLUGIN_VERSION . '</a>';

			array_push( $links, $library_link );

			return $links;
		}

		/**
		 * Method: render_options_page
		 *
		 * Render the appropriate UI on Settings > DTRT PluginName
		 *
		 * Note:
		 * - Take the user's options (from the form input)
		 * - Store the user's options
		 * - Render the options page
		 *
		 * - Shortcode/widget options are specific to each instance of the shortcode/widget
		 * and are thus stored with those individual instances.
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_options_page() {
			$messages                         = $this->get_messages();
			$insufficient_permissions_message = $messages['insufficient_permissions'];

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( $insufficient_permissions_message );
			}

			/**
			 * Load existing options
			 */
			$plugin_options      = $this->get_plugin_options();
			$plugin_data_options = $this->get_plugin_data_options();

			/**
			* If the form was submitted, update the options,
			* regardless of whether they have changed or not.
			*/
			if ( $this->helper_options_saved() === true ) {

				/**
				* Save default/user values from form submission
				*
				* @see https://stackoverflow.com/a/13461680/6850747
				*/
				foreach ( $plugin_options as $name => $attributes ) {
					$plugin_options[ $name ]['value'] = esc_html( $_POST[ $name ] );
				}
				// If we've updated our options,
				// get the latest data from the API.
				//
				// Tell the Ajax to get the latest data even if it is not stale.
				$plugin_data_options['force_refresh'] = true;

				// Update options object in database.
				$this->set_plugin_options( $plugin_options );
				$this->set_plugin_data_options( $plugin_data_options );
			} elseif ( isset( $plugin_data_options['last_updated'] ) ) {
				// if data has already been retrieved from API,
				// get the saved data.
				//
				// Only get the latest data if the existing data is stale.
				$plugin_data_options['force_refresh'] = false;

				// Update options object in database.
				$this->set_plugin_options( $plugin_options );
				$this->set_plugin_data_options( $plugin_data_options );
			} else {
				// else if form not submitted yet,
				// Don't get the data until we know which data to get,
				// (once the form is submitted).
				$plugin_data_options['force_refresh'] = false;
				$this->set_plugin_data_options( $plugin_data_options );
			}

			/**
			 * Load the HTML template
			 * This function's variables will be available to this template,
			 * includng $this
			 * $plugin_options are retrieved afresh inside the template
			 */
			require_once $this->get_path() . 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/views/options.php';
		}

		/**
		 * Method: render_form_element
		 *
		 * Form field templating for the options page.
		 *
		 * Parameters:
		 *   $name Name.
		 *   $attributes Attributes.
		 *
		 * Returns:
		 * - Form element HTML
		 *
		 * TODO:
		 * - Add field validation feedback (#10)
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_form_element( string $name, array $attributes = array() ) : string {
			// define variables.
			$type    = null;
			$label   = null;
			$size    = null;
			$tip     = null;
			$options = null;
			$value   = null;

			// populate variables.
			extract( $attributes, EXTR_IF_EXISTS );

			if ( ! isset( $type, $label ) ) {
				return '';
			}

			if ( ! isset( $value ) ) {
				$value = $this->helper_get_default_value( $type );
			}

			// name as a string.
			$name_str = $name;

			// plugin options page layout.
			$label_start = '<tr><th scope="row">';
			$label_end   = '</th>';
			$field_start = '<td>';
			$field_end   = '</td></tr>';
			$tip_element = 'div';

			if ( $size <= 2 ) {
				$classname = 'tiny-text';
			} elseif ( $size <= 4 ) {
				$classname = 'small-text';
			} elseif ( $size <= 37 ) {
				$classname = 'regular-text';
			} else {
				$classname = 'large-text';
			}

			// same.
			$id = $name_str;

			/**
			* Load the HTML template
			* The supplied arguments will be available to this template.
			*/

			/**
			 * Turn on output buffering.
			 *  This stores the HTML template in the buffer
			 *  so that it can be output into the content
			 *  rather than at the top of the page.
			 */
			ob_start();

			require $this->get_path() . 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/views/form-element-' . $type . '.php';

			/**
			 * Get current buffer contents and delete current output buffer.
			 */
			return ob_get_clean();
		}

		/**
		 * Method: render_settings_errors
		 *
		 * Admin Notices: Errors.
		 *
		 * Note:
		 * - Displayed below the H1.
		 *
		 * See:
		 * - <https://digwp.com/2016/05/wordpress-admin-notices/>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_settings_errors() {
			settings_errors();
		}

		/**
		 * Method: render_admin_notices
		 *
		 * Admin Notices: Custom.
		 *
		 * Note:
		 * - Displayed below the H1.
		 * - Possible classes: notice-error, notice-warning, notice-success, or notice-info
		 *
		 * See:
		 * - <https://digwp.com/2016/05/wordpress-admin-notices/>
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_admin_notices() {
			$screen = get_current_screen();

			if ( 'settings_page_' . $this->get_slug() === $screen->id ) :

				if ( isset( $_POST['wpdtrt_plugin_boilerplate_form_submitted'] ) ) :
					?>
					<div class="notice notice-success is-dismissible">
						<p>
							<?php
								echo $this->get_developer_prefix() . ' ' . $this->get_menu_title() . ' ' . $this->get_success_message();
							?>
						</p>
					</div>
					<?php
				endif;
			endif;
		}

		/**
		 * Method: render_css_backend
		 *
		 * Attach CSS for options page.
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_css_backend() {
			$media = 'all';

			wp_enqueue_style( $this->get_prefix() . '_backend',
				$this->get_url() . 'css/backend.css',
				array(
					// load these registered dependencies first.
				),
				$this->get_version(),
				$media
			);
		}

		/**
		 * Method: render_css_frontend
		 *
		 * Attach CSS for front-end widgets and shortcodes.
		 *
		 * Since:
		 *   1.0.0 - Added
		 */
		public function render_css_frontend() {
			$media                     = 'all';
			$stylesheets_to_load_first = array();

			wp_enqueue_style( $this->get_prefix() . '_variables',
				$this->get_url() . 'css/' . $this->get_slug() . '-variables.css',
				$stylesheets_to_load_first,
				$this->get_version(),
				$media
			);

			wp_enqueue_style( $this->get_prefix(),
				$this->get_url() . 'css/frontend.css',
				$stylesheets_to_load_first,
				$this->get_version(),
				$media
			);
		}
	}
}
