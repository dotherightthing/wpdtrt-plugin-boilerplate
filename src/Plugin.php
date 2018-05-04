<?php
/**
 * Plugin class.
 *
 * @package   WPPlugin
 * @since     1.0.0
 * @version   1.0.1
 */

namespace DoTheRightThing\WPPlugin;

if ( !class_exists( 'Plugin' ) ) {

  /**
   * Plugin base class
   *
   * Boilerplate functions, including
   * options page, field templating, error messaging, CSS, JS.
   *
   * Use Shortcode for dependent shortcodes.
   * Use Widget for dependent widgets.
   *
   * @param       array $atts Optional shortcode attributes specified by the user
   * @param       string $content Content within the enclosing shortcode tags
   * @return      Shortcode
   *
   * @uses        ../../../../wp-includes/shortcodes.php
   * @see         https://codex.wordpress.org/Function_Reference/add_shortcode
   * @see         https://codex.wordpress.org/Shortcode_API#Enclosing_vs_self-closing_shortcodes
   * @see         http://php.net/manual/en/function.ob-start.php
   * @see         http://php.net/manual/en/function.ob-get-clean.php
   *
   * @since       1.0.0
   * @version     1.0.0
   */
  class Plugin {

    /**
     * Initialise the object's properties when it is instantiated,
     * using new DoTheRightThing\WPPluginPlugin
     *
     * @param     array $settings Plugin options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $settings ) {

      // option variables
      $url = null;
      $prefix = null;
      $slug = null;
      $menu_title = null;
      $settings_title = null;
      $developer_prefix = null;
      $path = null;
      $messages = null;
      $version = null;
      $demo_shortcode_params = null;

      // option arrays
      // note that these should exclude 'value' keys,
      // to avoid overwriting existing user settings
      $plugin_options = null;
      $plugin_data = null;
      $plugin_data_options = null;
      $instance_options = null;
      $plugin_dependencies = null;

      // overwrite options with values from the settings array
      extract( $settings, EXTR_IF_EXISTS );

      // store option variables
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

      // Delete old options during testing
      //$this->unset_options();

      // store option arrays
      $this->set_plugin_options( $plugin_options );
      $this->set_plugin_data( $plugin_data );
      $this->set_plugin_data_options( isset($plugin_data_options) ? $plugin_data_options : array() );
      $this->set_instance_options( $instance_options );
      $this->set_plugin_dependencies( isset($plugin_dependencies) ? $plugin_dependencies : array() );

      // defaults
      $plugin_data_options['force_refresh'] = false;

      // hook in to WordPress
      $this->wp_setup();
    }

    //// START WORDPRESS INTEGRATION \\\\

    /**
     * Initialise plugin options ONCE.
     *
     * @since 1.0.0
     *
     * @see https://wordpress.stackexchange.com/a/209772
     * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/24
     */
    protected function wp_setup() {

      /**
       * $this->render_foobar() - infers that no args are to be passed, fails
       * @see https://stackoverflow.com/questions/28954168/php-how-to-use-a-class-function-as-a-callback
       * @see https://tommcfarlin.com/wordpress-plugin-constructors-hooks/
       */
      add_action( 'admin_menu',               [$this, 'render_options_menu'] );
      add_action( 'admin_notices',            [$this, 'render_settings_errors'] );
      add_action( 'admin_notices',            [$this, 'render_admin_notices'] );
      add_action( 'admin_head',               [$this, 'render_css_backend'] );
      add_action( 'wp_enqueue_scripts',       [$this, 'render_css_frontend'] );
      add_action( 'wp_enqueue_scripts',       [$this, 'render_js_frontend'] );
      add_action( 'admin_enqueue_scripts',    [$this, 'render_js_backend'] );
      add_action( 'tgmpa_register',           [$this, 'wp_register_plugin_dependencies'] );
      add_action( 'post_type_link',           [$this, 'render_cpt_permalink_placeholders'], 10, 3 ); // Custom Post Type

      // call the server side PHP function through admin-ajax.php.
      add_action( 'wp_ajax_refresh_api_data',  [$this, 'refresh_api_data'] );

      $plugin_root_relative_to_plugin_folder =  $this->get_slug() . '/' . $this->get_slug() . '.php'; // plugin_basename(__FILE__)
      add_filter( 'plugin_action_links_' . $plugin_root_relative_to_plugin_folder, [$this, 'render_settings_link'] );
    }

    /**
     * TGM Plugin Activation Configuration.
     * Registers the required plugins for this theme.
     * This function is hooked into `tgmpa_register`,
     * which is fired on the WP `init` action on priority 10.
     *
     * @version     2.6.1 for WPPlugin
     * @author      Thomas Griffin, Gary Jones, Juliette Reinders Folmer
     * @copyright   Copyright (c) 2011, Thomas Griffin
     * @license     http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
     * @link        https://github.com/TGMPA/TGM-Plugin-Activation
     *
     * @since       1.0.0
     * @see         http://tgmpluginactivation.com/configuration/
     * @see         http://tgmpluginactivation.com/download/ for more options
     */
    public function wp_register_plugin_dependencies() {

      /**
       * Include the TGM_Plugin_Activation class.
       *
       * Plugin:
       * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
       */
      require_once( $this->get_path() . 'vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php');

      /*
       * Array of plugin arrays. Required keys are name and slug.
       * If the source is NOT from the .org repo, then source is also required.
       */
      $plugins = $this->get_plugin_dependencies();

      /*
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

    //// END WORDPRESS INTEGRATION \\\\

    //// START GETTERS AND SETTERS (SET, GET, REFRESH, UNSET) \\\\

    // URL

    /**
     * Set the value of $url
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_url( $new_url ) {
      $this->url = $new_url;
    }

    /**
     * Get the value of $url
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_url() {
      return $this->url;
    }

    // SHORTCODE PARAMS

    /**
     * Set the value of $demo_shortcode_params
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_demo_shortcode_params( $new_demo_shortcode_params ) {
      $this->demo_shortcode_params = $new_demo_shortcode_params;
    }

    /**
     * Get the value of $demo_shortcode_params
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      array
     */
    public function get_demo_shortcode_params() {
      return $this->demo_shortcode_params;
    }

    // PREFIX

    /**
     * Set the value of $prefix
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_prefix( $new_prefix ) {
      $this->prefix = $new_prefix;
    }

    /**
     * Get the value of $prefix (wpdtrt_foo)
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_prefix() {
      return $this->prefix;
    }

    // SLUG

    /**
     * Set the value of $slug
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_slug( $new_slug ) {
      $this->slug = $new_slug;
    }

    /**
     * Get the value of $slug
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_slug() {
      return $this->slug;
    }

    // MENU TITLE

    /**
     * Set the value of $menu_title
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_menu_title( $new_menu_title ) {
      $this->menu_title = $new_menu_title;
    }

    /**
     * Get the value of $menu_title
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_menu_title() {
      return $this->menu_title;
    }

    // SETTINGS TITLE

    /**
     * Set the value of $settings_title
     *
     * @since       1.3.4
     * @version     1.0.0
     *
     * @param string
     */
    protected function set_settings_title( $new_settings_title ) {
      $this->settings_title = $new_settings_title;
    }

    /**
     * Get the value of $settings_title
     *
     * @since       1.3.4
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_settings_title() {
      return $this->settings_title;
    }

    // DEVELOPER PREFIX

    /**
     * Set the value of $developer_prefix
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_developer_prefix( $new_developer_prefix ) {
      $this->developer_prefix = $new_developer_prefix;
    }

    /**
     * Get the value of $developer_prefix
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_developer_prefix() {
      return $this->developer_prefix;
    }

    // MESSAGES i18n

    /**
     * Set the value of $messages
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_messages( $new_messages ) {
      $this->messages = $new_messages;
    }

    /**
     * Get the value of $messages
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_messages() {
      return $this->messages;
    }

    // SUCCESS MESSAGE

    /**
     * Get the value of the $success_message
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       string
     */
    public function get_success_message() {
      $messages = $this->get_messages();
      $success_message = $messages['success'];
      return $success_message;
    }

    // PATH

    /**
     * Set the value of $path
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_path( $new_path ) {
      $this->path = $new_path;
    }

    /**
     * Get the value of $path
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       string
     */
    public function get_path() {
      return $this->path;
    }

    // OPTIONS

    /**
     * Set plugin options
     *
     * @since 1.0.0
     *
     * @param array $options
     */
    protected function set_options( $new_options ) {
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
       * @example update_option( string $option, mixed $value, string|bool $autoload = null )
       * @see https://codex.wordpress.org/Function_Reference/update_option
       */
      update_option( $this->get_prefix(), $options, null );
    }

    /**
     * Get plugin options, user values merged with the defaults
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_options() {

      /**
       * Load any plugin user settings, falling back to an empty $options_array if they don't exist yet
       * @see https://developer.wordpress.org/reference/functions/get_option/#parameters
       */
      $fallback_options_array = array(
        'plugin_options' => array(),
        'plugin_data' => array(),
        'plugin_data_options' => array(),
        'instance_options' => array(),
        'plugin_dependencies' => array()
      );

      $options = get_option( $this->get_prefix(), $fallback_options_array );

      return $options;
    }

    /**
     * Remove plugin options
     *
     * @since 1.0.0
     */
    protected function unset_options() {
      delete_option( $this->get_prefix() );
    }

    // PLUGIN OPTIONS

    /**
     * Set the value of $plugin_options.
     *  Add any new options or attributes in the configuration.
     *  Adds the value attribute once this has been supplied by the user.
     *
     * @version     1.1.0
     * @since       1.0.0
     * @since       1.3.0 Fixed option merging
     *
     * @param       array $plugin_options
     * @return      array $options Merged options (for unit testing)
     */
    public function set_plugin_options( $new_plugin_options ) {

      // old options stored in database
      $old_plugin_options = $this->get_plugin_options();

      // to remove persistent options:
      // unset($old_plugin_options['option_name']);
      // unset($new_plugin_options['option_name']);

      // new array to save to database
      $merged_plugin_options = $this->helper_merge_option_arrays( $old_plugin_options, $new_plugin_options );

      // Save the merged options
      $options = $this->get_options();
      $options['plugin_options'] = $merged_plugin_options;

      $this->set_options($options);

      // return array for unit testing
      return $options['plugin_options'];
    }

    /**
     * Get the value of $plugin_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_plugin_options() {
      $options = $this->get_options();
      $plugin_options = $options['plugin_options'];
      return $plugin_options;
    }

    // PLUGIN DEPENDENCIES

    /**
     * Store a plugin dependency for loading via TGMA
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_dependency( $new_plugin_dependency ) {
      $old_plugin_dependencies = $this->get_plugin_dependencies();

      foreach( $old_plugin_dependencies as $key => $value ) {
        if ( $value['slug'] === $new_plugin_dependency['slug'] ) {
          // remove the old entry so we can set it again below
          unset( $old_plugin_dependencies[$key] );
        }
      }

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options = $this->get_options();
      $options['plugin_dependencies'] = array_merge( $old_plugin_dependencies, array( $new_plugin_dependency ) );
      $this->set_options($options);
    }

    /**
     * Store all plugin dependencies for loading via TGMA
     *  Merges new dependencies with any old ones
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_plugin_dependencies( $new_plugin_dependencies ) {

      // old options stored in database
      $old_plugin_dependencies = $this->get_plugin_dependencies();

      // new array to save to database
      $merged_plugin_dependencies = $this->helper_merge_option_arrays( $old_plugin_dependencies, $new_plugin_dependencies );

      // Save the merged options
      $options = $this->get_options();
      $options['plugin_dependencies'] = $merged_plugin_dependencies;

      $this->set_options($options);

      // return array for unit testing
      return $options['plugin_dependencies'];
    }

    /**
     * Get a list of plugin dependencies to load via TGMA
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_plugin_dependencies() {
      $options = $this->get_options();
      $plugin_dependencies = $options['plugin_dependencies'];
      // remove empty array elements before returning
      return array_filter( $plugin_dependencies );
    }

    // PLUGIN DATA

    /**
     * Set the value of $plugin_data
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_data( $new_plugin_data ) {

      if ( ! isset( $new_plugin_data ) ) {
        return;
      }

      $options = $this->get_options();
      $options['plugin_data'] = $new_plugin_data;
      $this->set_options($options);
    }

    /**
     * Get the value of $plugin_data
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_plugin_data() {
      $options = $this->get_options();
      $plugin_data = $options['plugin_data'];
      return $plugin_data;
    }

    /**
     * Get the number of items in $plugin_data
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      number
     */
    public function get_plugin_data_length() {
      $plugin_data = $this->get_plugin_data();
      return count( $plugin_data );
    }

    // PLUGIN DATA OPTIONS

    /**
     * Set the value of $plugin_data_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_data_options( $new_plugin_data_options ) {
      $old_plugin_data_options = $this->get_plugin_data_options();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options = $this->get_options();
      $options['plugin_data_options'] = array_merge( $old_plugin_data_options, $new_plugin_data_options );
      $this->set_options($options);
    }


    /**
     * Get the value of $plugin_data_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_plugin_data_options() {
      $options = $this->get_options();
      $plugin_data_options = $options['plugin_data_options'];

      if ( !isset( $plugin_data_options) ) {
        $plugin_data_options = array();
      }

      return $plugin_data_options;
    }

    // INSTANCE OPTIONS

    /**
     * Set the value of $instance_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_instance_options( $new_instance_options ) {
      $old_instance_options = $this->get_instance_options();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options = $this->get_options();
      $options['instance_options'] = array_merge( $old_instance_options, $new_instance_options );
      $this->set_options($options);
    }

    /**
     * Get the value of $instance_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      array
     */
    public function get_instance_options() {
      $options = $this->get_options();
      $instance_options = $options['instance_options'];
      return $instance_options;
    }

    // VERSION

    /**
     * Set the value of $version
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_version( $new_version ) {
      $this->version = $new_version;
    }

    /**
     * Get the value of $version
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_version() {
      return $this->version;
    }

    // API DATA

    /**
     * Get the API Endpoint (URL) from which to pull data
     *
     * The endpoint URL is sometimes constructed from plugin specific settings fields.
     * We need to manually assemble the endpoint string from dynamic data.
     * As this data and assembly is contextual, it is done in the child plugin.
     *
     * A WordPress filter is used to get the correct value of $endpoint at runtime.
     * This allows us to keep the get_api_data() code in the parent class,
     * rather than expecting authors to overwrite get_api_data() with in the child class.
     * Alternatively, plugin authors could simply override $this->get_api_endpoint()
     *
     * @return string $endpoint
     *
     * @example
     *  public function wpdtrt_forms_set_api_endpoint { return $endpoint; }
     *  add_filter( 'wpdtrt_forms_set_api_endpoint', [$this, 'set_api_endpoint'] );
     */
    public function get_api_endpoint() {
      // Call child plugin method:
      // A filter is used rather than an action as actions do not return a value.
      // A prefix prevents the filter from affecting other active instances of wpplugin.
      $child_plugin_filter = $this->get_prefix() . '_set_api_endpoint';
      $default_endpoint = '';
      $endpoint = apply_filters( $child_plugin_filter, $default_endpoint );

      return $endpoint;
    }

    /**
     * Request the data from the API
     *
     * @return      object $data The body of the JSON response
     *
     * @since       0.1.0
     * @since       1.0.0
     * @since       1.3.4 Use get_api_endpoint() to pass in the endpoint
     *
     * @see         get_api_endpoint()
     * @uses        ../../../../wp-includes/http.php
     * @see         https://developer.wordpress.org/reference/functions/wp_remote_get/
     */
    protected function get_api_data() {

      $endpoint = $this->get_api_endpoint();
      $data = false;

      if ( $endpoint ) {

        $args = array(
            'timeout' => 30, // seconds to wait for the request to complete
            'blocking' => true // false = nothing loads
        );

        $response = wp_remote_get(
            $endpoint,
            $args
        );

        /**
        * Return the body, not the header
        * Note: There is an optional boolean argument, which returns an associative array if TRUE
        */
        $data = json_decode( $response['body'], true );

        // Save the data and retrieval time
        $this->set_plugin_data( $data );
        $this->set_plugin_data_options( array(
            'last_updated' => time()
        ));
      }
      
      return $data;
    }

    /**
     * Refresh the data from the API
     *    The 'action' key's value, 'refresh_api_data',
     *    matches the latter half of the action 'wp_ajax_refresh_api_data' in our AJAX handler.
     *    This is because it is used to call the server side PHP function through admin-ajax.php.
     *    If an action is not specified, admin-ajax.php will exit, and return 0 in the process.
     *
     * See also $this->__construct()
     * See also $this->render_js_backend()
     * See also js/backend.js
     *
     * @param       string $format The data format ('ui'|'data')
     *
     * @since       1.0.0
     * @version     1.0.1
     *
     * @see         https://codex.wordpress.org/AJAX_in_Plugins
     */
    public function refresh_api_data( $format ) { // ?
      $format = sanitize_text_field( $_POST['format'] ); // ?

      $plugin_data_options = $this->get_plugin_data_options();
      $existing_data = $this->get_plugin_data();
      $last_updated = isset( $plugin_data_options['last_updated'] ) ? $plugin_data_options['last_updated'] : false;
      $force_refresh = isset( $plugin_data_options['force_refresh'] ) ? $plugin_data_options['force_refresh'] : false;

      // if the data has previously been requested AND has loaded
      // only update it if it is stale
      if ( $last_updated && $existing_data ) {
        $current_time = time();
        $update_difference = $current_time - $last_updated;
        $one_hour = (1 * 60 * 60);
        $do_refresh = ( $update_difference > $one_hour );
      }
      else {
        $do_refresh = false;
      }

      if ( $force_refresh ) {
        $do_refresh = true;
      }

      // TODO: should this data be passed somewhere?
      if ( $do_refresh ) {
        $data = $this->get_api_data();
      }
      else {
        $data = $existing_data;
      }
      
      // update the UI
      if ( $format === 'ui' ) {
        $shortcode = $this->helper_build_demo_shortcode();
        echo $this->render_demo_shortcode( $shortcode );
      }
      else if ( $format === 'data' ) {
        echo $this->render_demo_shortcode_data();
      }

      /**
       * Let the Ajax know when the entire function has completed
       *
       * wp_die() vs die() vs exit()
       * Most of the time you should be using wp_die() in your Ajax callback function.
       * This provides better integration with WordPress and makes it easier to test your code.
       */
      wp_die();
    }

    //// END GETTERS AND SETTERS \\\\

    //// START HELPERS \\\\

   /**
     * Get a usable value for every form element type
     * @param       string $field_value
     * @param       string $field_type
     *
     * @return      string $normalised_field_value;
     *
     * @since       1.0.0
     * @version     1.0.0
     * @todo        Add field validation feedback (#10)
     */
    public function helper_normalise_field_value( $field_value, $field_type ) {
      $normalised_field_value = null;

      // If something was entered into the field
      // then save the new value.
      // ( '1', '0', '', true, false ) === isset
      // ( null ) === !isset
      if ( isset( $field_value ) ) {
        $normalised_field_value = $field_value;
      }
      else {
        // but if a checkbox is unchecked
        // then do change the saved instance value,
        // otherwise the checkbox will stay checked
        if ( $field_type === 'checkbox') {
          $normalised_field_value = '';
        }
        // but if the null option in a select is selected
        // then do change the saved instance value,
        // otherwise the old option will stay selected
        else if ( $field_type === 'select') {
          $normalised_field_value = '';
        }
        else if ( $field_type === 'file') {
          $normalised_field_value = '';
        }
      }

      return $normalised_field_value;
    }

    /**
     * Callback function for array_filter which only removes NULL keys
     *
     * @param array $arr The array to filter
     * @return array The filtered array
     *
     * @see http://php.net/manual/en/function.array-filter.php#115777
     */
    protected function helper_array_filter_not_null( $arr ) {
      return !is_null( $arr );
    }

    /**
     * Get the default value from an input type
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string $input_type
     * @return      mixed $default_value
     */
    public function helper_get_default_value( $input_type ) {

      if ( $input_type === 'select' ) {
        $default_value = null;
      }
      else if ( $input_type === 'checkbox' ) {
        $default_value = '';
      }
      else if ( $input_type === 'radio' ) {
        $default_value = '';
      }
      else if ( $input_type === 'password' ) {
        $default_value = '';
      }
      else if ( $input_type === 'text' ) {
        $default_value = '';
      }
      else {
        $default_value = null;
      }

      return $default_value;
    }

    /**
     * Determine whether the options page form has been submitted or not
     *
     * @return      boolean
     *
     * @since       1.0.0
     * @version     1.0.1
     *
     * @todo        Incorporate validation checks to ensure that all expected inputs are present (#10)
     */
    public function helper_options_saved() {
      $helper_options_saved = false;

      if ( isset( $_POST['wpdtrt_plugin_form_submitted'] ) ) {

        // check that the form submission was legitimate
        $hidden_field = esc_html( $_POST['wpdtrt_plugin_form_submitted'] );

        if ( $hidden_field === 'Y' ) {
          $helper_options_saved = true;
        }
      }

      return $helper_options_saved;
    }

    /**
     * Build demo shortcode
     *
     * @return string Shortcode
     *
     * @since 1.0.0
     * @version 1.0.1
     *
     */
    protected function helper_build_demo_shortcode() {
      $params = $this->get_demo_shortcode_params();

      if ( !isset($params) || empty($params) ) {
        return '';
      }

      $options_page_demo_shortcode = '[';

      foreach( $params as $key => $value ) {

        if ( $key === 'name' ) {
          $options_page_demo_shortcode .= $value;
        }
        else if ( substr($key, 0, 5) !== 'mock_' ) {
          $options_page_demo_shortcode .= ' ' . $key . '=' . '"' . $value . '"';
        }
      }

      $options_page_demo_shortcode .= ']';

      /**
       * Render demo shortcode (update the UI)
       */
      return $options_page_demo_shortcode;
    }

    /**
     * Merge option arrays
     *  Adds any new items.
     *
     * @version     1.0.0
     * @since       1.3.0
     *
     * @param       array $old_options
     * @param       array $new_options
     * @return      array $merged_options
     */
    public function helper_merge_option_arrays( $old_options, $new_options ) {

      $merged_options = array();

      if ( empty( $old_options ) ) {
        $merged_options = $new_options;
      }
      else {

        // all existing 'old' options, e.g. 'google_maps_api_key' etc
        foreach( $old_options as $option_name => $option_value ) {

          // each option describes a form input using an array
          if ( is_array( $option_value ) ) {

            // the form input attributes: 'type', 'label', 'size', 'value' etc
            foreach( $option_value as $attribute => $value) {

              // if a 'new' value is supplied for an existing attribute, use it
              if ( array_key_exists( $attribute, $new_options[$option_name] ) ) {
               $merged_options[$option_name][$attribute] = $new_options[$option_name][$attribute];
              }
              // else use the existing value
              else {
                $merged_options[$option_name][$attribute] = $value;
              }
            }
          }
        }

        // all 'new'/unknown options
        foreach( $new_options as $option_name => $option_value ) {

          // each option describes a form input using an array
          if ( is_array( $option_value ) ) {

            // the form input attributes: 'type', 'label', 'size', 'value' etc
            foreach( $option_value as $attribute => $value) {

              // if a 'new' attribute is not existing, add it
              if ( ! array_key_exists( $attribute, $merged_options[$option_name] ) ) {
                $merged_options[$option_name][$attribute] = $value;
              }
            }
          }
        }
      }

      return $merged_options;
    }

    //// END HELPERS \\\\

    //// START RENDERERS \\\\

    /**
     * Support Custom Field %placeholders% in Custom Post Type permalinks
     *  This replacement is only applied when the permalink is generated
     *  eg on an archive listing or wpadmin edit page
     *  NOT in the rewrite rules / when the page is loaded
     *
     * @param $permalink See WordPress function options
     * @param $post See WordPress function options
     * @param $leavename See WordPress function options
     * @return $permalink
     *
     * @example
     *  // wpdtrt-dbth/library/register_post_type_tourdiaries.php
     *  'rewrite' => array(
     *    'slug' => 'tourdiaries/%wpdtrt_tourdates_taxonomy_tour%/%wpdtrt_tourdates_cf_daynumber%',
     *    'with_front' => false
     *  )
     *
     * @see http://shibashake.com/wordpress-theme/add-custom-taxonomy-tags-to-your-wordpress-permalinks
     * @see http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2#conflict
     * @see https://stackoverflow.com/questions/7723457/wordpress-custom-type-permalink-containing-taxonomy-slug
     * @see https://kellenmace.com/edit-slug-button-missing-in-wordpress/
     * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/44 - Permalink Edit button missing
     */
    public function render_cpt_permalink_placeholders($permalink, $post, $leavename) {

      // Get post
      $post_id = $post->ID;
      $prefix = $this->get_prefix();

      // extract all %placeholders% from the permalink
      // https://regex101.com/
      preg_match_all('/(?<=\/%' . $prefix . '_cf_).+?(?=%\/)/', $permalink, $placeholders, PREG_OFFSET_CAPTURE);

      // placeholders in an array of taxonomy/term arrays
      foreach ( $placeholders[0] as $placeholder ) {

        $placeholder_name = $prefix . '_cf_' . $placeholder[0];

        if ( metadata_exists( 'post', $post_id, $placeholder_name ) ) {
          $replacement = get_post_meta( $post_id, $placeholder_name, true );
          $permalink = str_replace( ( '%' . $placeholder_name . '%' ), $replacement, $permalink);
        }
      }

      return $permalink;
    }

    /**
     * Render demo shortcode
     *
     * @param string $shortcode
     *
     * @since 1.0.0
     *
     * @return string Shortcode HTML
     */
    protected function render_demo_shortcode( $shortcode ) {
      return do_shortcode( $shortcode );
    }

    /**
     * Render demo shortcode data
     *
     * For the purposes of debugging, we also display the raw data.
     * var_dump is prefereable to print_r,
     * because it reveals the data types used,
     * so we can check whether the data is in the expected format.
     *
     * @return string Indented data
     *
     * @since 1.0.0
     *
     * @link http://kb.dotherightthing.co.nz/php/print_r-vs-var_dump/
     * @see https://stackoverflow.com/a/139553/6850747
     * @todo Error when dumping some data objects (#37)
     */
    protected function render_demo_shortcode_data() {
      $plugin_data = $this->get_plugin_data();
      $data_str = '';
      $demo_shortcode_params = $this->get_demo_shortcode_params();
      $max_length = $demo_shortcode_params['number'];

      if ( empty( $plugin_data ) ) {
        return $data_str;
      }

      $data_str .= '<pre><code>';
      $data_str .= "{\r\n";

      $count = 0;

      foreach( $plugin_data as $key => $val ) {
        $data_str .= var_export( $plugin_data[$key], true );

        $count++;

        // when we reach the end of the sample, stop looping
        if ($count === $max_length) {
          break;
        }
      }

      $data_str .= "}\r\n";
      $data_str .= '</code></pre>';

      return $data_str;
    }

    /**
     * Render a human readable last updated date.
     *  Works best with General Settings > Date Format > Custom
     *
     * @return      string $humanised_date
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @see https://codex.wordpress.org/Option_Reference
     * @see https://codex.wordpress.org/Function_Reference/get_gmt_from_date
     */
    public function render_last_updated_humanised() {
      $last_updated_str = '';
      $plugin_data_options = $this->get_plugin_data_options();
      $last_updated = isset( $plugin_data_options['last_updated'] ) ? $plugin_data_options['last_updated'] : false;

      if ( ! $last_updated ) {
        return $last_updated_str;
      }

      // use the date format set by the user
      $wp_date_format = get_option('date_format');
      $wp_time_format = get_option('time_format');

      // get the Local Time from a GMT/UTC timestamp
      $last_updated_str .= get_date_from_gmt(
        date( 'Y-m-d H:i:s', $last_updated ),
        ( $wp_time_format . ', ' . $wp_date_format ) // http://php.net/manual/en/datetime.format.php
      );

      return $last_updated_str;
    }

    /**
     * Attach JS for front-end widgets and shortcodes
     *    Generate a configuration object which the JavaScript can access.
     *    When an Ajax command is submitted, pass it to our function via the Admin Ajax page.
     *
     * @see         https://codex.wordpress.org/AJAX_in_Plugins
     * @see         https://codex.wordpress.org/Function_Reference/wp_localize_script
     *
     * @since       1.0.0
     * @version     1.0.0
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

      /*
      wp_register_script( 'a_dependency',
        $this->get_url()  . 'vendor/bower_components/a_dependency/a_dependency.js',
        array(
          'jquery'
        ),
        DEPENDENCY_VERSION,
        $attach_to_footer
      );
      */

      do_action ( 'wpdtrt_plugin__register_js_frontend' );

      wp_enqueue_script( $this->get_prefix(),
        $this->get_url()  . 'js/frontend.js',
        array(
          // load these registered dependencies first:
          'jquery'
        ),
        $this->get_version(),
        $attach_to_footer
      );

      wp_localize_script( $this->get_prefix(),
        $this->get_prefix() . '_config',
        array(
          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
          // but we need to explicitly expose it to frontend pages
          'ajaxurl' => admin_url( 'admin-ajax.php' ), // wpdtrt_foobar_config.ajaxurl
          'options' => $this->get_options() // wpdtrt_foobar_config.options
        )
      );
    }

    /**
     * Attach JS for back-end admin pages
     *    For consistency with render_js_frontend,
     *    Generate a configuration object which the JavaScript can access.
     *    When an Ajax command is submitted, pass it to our function via the Admin Ajax page.
     *
     * @param       string $hook_suffix The current admin page.
     *
     * @see         https://codex.wordpress.org/AJAX_in_Plugins
     * @see         https://codex.wordpress.org/Function_Reference/wp_localize_script
     * @see         https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     *
     * @since       1.0.0
     * @version     1.0.1
     */
    public function render_js_backend( $hook_suffix ) {
      if ( $hook_suffix !== ( 'settings_page_' . $this->get_slug() ) ) {
        return;
      }

      $attach_to_footer = true;

      do_action ( 'wpdtrt_plugin__register_js_backend' );

      wp_enqueue_script( $this->get_prefix() . '_backend',
        $this->get_url()  . 'vendor/dotherightthing/wpdtrt-plugin/js/backend.js',
        array(
          // load these registered dependencies first:
          'jquery'
        ),
        $this->get_version(),
        $attach_to_footer
      );

      $plugin_data_options = $this->get_plugin_data_options();

      $demo_shortcode_params = $this->get_demo_shortcode_params();

      wp_localize_script( $this->get_prefix() . '_backend',
        'wpdtrt_plugin_config',
        array(
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
          'messages' => $this->get_messages(),
          'force_refresh' => $plugin_data_options['force_refresh'],
          'refresh_api_data' => isset( $demo_shortcode_params ) ? 'true' : 'false'
        )
      );
    }

    /**
     * Display a link to the options page in the admin menu
     *
     * @uses        ../../../../wp-admin/includes/plugin.php
     * @see         https://developer.wordpress.org/reference/functions/add_options_page/
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function render_options_menu() {
      add_options_page(
        $this->get_developer_prefix() . ' ' . $this->get_menu_title(), // <title>
        $this->get_menu_title(), // menu
        'manage_options', // capability
        $this->get_slug(), // menu_slug
        [$this, 'render_options_page'] // function callback
      );
    }

    /**
     * Display a link to the plugin settings page in the plugins list
     *
     * @param array $links
     * @return array $links
     * @since 1.3.4
     *
     * @see https://isabelcastillo.com/settings-link-plugin-plugins
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
     */
    public function render_settings_link( $links ) {
      $settings_url = get_admin_url() . 'options-general.php?page=' . $this->get_slug();
      $settings_link = '<a href="' . $settings_url . '">' . $this->settings_title . '</a>';

      // prepend $setting_link to the beginning of the $links array
      array_unshift( $links, $settings_link );

      return $links;
    }

    /**
     * Render the appropriate UI on Settings > DTRT PluginName
     *
     *    1. Take the user's options (from the form input)
     *    2. Store the user's options
     *    3. Render the options page
     *
     *    Note: Shortcode/widget options are specific to each instance of the shortcode/widget
     *    and are thus stored with those individual instances.
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    function render_options_page() {
      $messages = $this->get_messages();
      $insufficient_permissions_message = $messages['insufficient_permissions'];

      if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( $insufficient_permissions_message );
      }

      /**
       * Load existing options
       */
      $plugin_options = $this->get_plugin_options();
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
        foreach( $plugin_options as $name => $attributes ) {
          $plugin_options[ $name ]['value'] = esc_html( $_POST[ $name ] );
        }
        // If we've updated our options
        // get the latest data from the API

        // Tell the Ajax to get the latest data even if it is not stale
        $plugin_data_options['force_refresh'] = true;

        // Update options object in database
        $this->set_plugin_options( $plugin_options );
        $this->set_plugin_data_options( $plugin_data_options );
      }
      // if data has already been retrieved from API
      // get the saved data
      else if ( isset( $plugin_data_options['last_updated'] ) ) {

        // Only get the latest data if the existing data is stale
        $plugin_data_options['force_refresh'] = false;

        // Update options object in database
        $this->set_plugin_options( $plugin_options );
        $this->set_plugin_data_options( $plugin_data_options );
      }
      // else if form not submitted yet
      else {
        // Don't get the data until we know which data to get,
        // (once the form is submitted)
        $plugin_data_options['force_refresh'] = false;
        $this->set_plugin_data_options( $plugin_data_options );
      }


      /**
       * Load the HTML template
       * This function's variables will be available to this template,
       * includng $this
       * $plugin_options are retrieved afresh inside the template
       */
      require_once($this->get_path() . 'vendor/dotherightthing/wpdtrt-plugin/views/options.php');
    }

    /**
     * Form field templating for the options page
     *
     * @param       string $name
     * @param       array $attributes
     *
     * @return      string
     *
     * @since       1.0.0
     * @version     1.0.0
     * @todo        Add field validation feedback (#10)
     */
    public function render_form_element( $name, $attributes = array() ) {
      // define variables
      $type = null;
      $label = null;
      $size = null;
      $tip = null;
      $options = null;
      $value = null;

      // populate variables
      extract( $attributes, EXTR_IF_EXISTS );

      if ( !isset( $type, $label ) ) {
        return;
      }

      if ( !isset( $value ) ) {
        $value = $this->helper_get_default_value( $type );
      }

      // name as a string
      $nameStr = $name;

      // plugin options page layout
      $label_start = '<tr><th scope="row">';
      $label_end   = '</th>';
      $field_start = '<td>';
      $field_end   = '</td></tr>';
      $tip_element = 'div';
      $classname   = 'regular-text';

      // same
      $id = $nameStr;

      /**
       * Load the HTML template
       * The supplied arguments will be available to this template.
       */

      /**
       * ob_start — Turn on output buffering
       * This stores the HTML template in the buffer
       * so that it can be output into the content
       * rather than at the top of the page.
       */
      ob_start();

      require($this->get_path() . 'vendor/dotherightthing/wpdtrt-plugin/views/form-element-' . $type . '.php');

      /**
       * ob_get_clean — Get current buffer contents and delete current output buffer
       */
      return ob_get_clean();
    }

    /**
     * Admin Notices: Errors
     * Displayed below the H1
     *
     * @see       https://digwp.com/2016/05/wordpress-admin-notices/
     * @since     1.0.0
     * @version   1.0.0
     */

    public function render_settings_errors() {
      settings_errors();
    }

    /**
     * Admin Notices: Custom
     * Displayed below the H1
     * Possible classes: notice-error, notice-warning, notice-success, or notice-info
     *
     * @see       https://digwp.com/2016/05/wordpress-admin-notices/
     * @since     1.0.0
     * @version   1.0.0
     */

    public function render_admin_notices() {
      $screen = get_current_screen();

      if ($screen->id === 'settings_page_' . $this->get_slug() ):

        if ( isset( $_POST['wpdtrt_plugin_form_submitted'] ) ):
    ?>
          <div class="notice notice-success is-dismissible">
            <p><?php echo
              $this->get_developer_prefix() . ' ' .
              $this->get_menu_title() . ' ' .
              $this->get_success_message()
            ?></p>
          </div>
    <?php
        endif;
      endif;
    }

    /**
     * Attach CSS for options page
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function render_css_backend() {
      $media = 'all';

      do_action ( 'wpdtrt_plugin__register_css_backend' );

      wp_enqueue_style( $this->get_prefix() . '_backend',
        $this->get_url() . 'css/backend.css',
        array(
          // load these registered dependencies first:
          //'a_dependency'
        ),
        $this->get_version(),
        $media
      );
    }

    /**
     * Attach CSS for front-end widgets and shortcodes
     *
     * @since       1.0.0
     * @version     1.0.0
     */
    public function render_css_frontend() {
      $media = 'all';

      do_action ( 'wpdtrt_plugin__register_css_frontend' );

      wp_enqueue_style( $this->get_prefix(),
        $this->get_url()  . 'css/frontend.css',
        array(
          // load these registered dependencies first:
          //'a_dependency'
        ),
        $this->get_version(),
        $media
      );
    }

    //// END RENDERERS \\\\
  }
}

?>
