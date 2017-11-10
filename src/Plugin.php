<?php
/**
 * Plugin class.
 *
 * @package   WPPlugin
 * @version   1.0.0
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
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new PluginName
     *
     * @param     array $settings Plugin options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $settings ) {

      // define variables
      $url = null;
      $prefix = null;
      $slug = null;
      $menu_title = null;
      $developer_prefix = null;
      $path = null;
      $messages = null;
      $version = null;
      $demo_shortcode_params = null;

      // options
      $plugin_options = null;
      $plugin_data = array();
      $plugin_data_options = null;
      $plugin_data_options['force_refresh'] = false;
      $instance_options = null;
      $plugin_dependencies = array();

      // extract variables
      extract( $settings, EXTR_IF_EXISTS );

      $this->set_url( $url );
      $this->set_prefix( $prefix );
      $this->set_slug( $slug );
      $this->set_menu_title( $menu_title );
      $this->set_developer_prefix( $developer_prefix );
      $this->set_path( $path );
      $this->set_messages( $messages );
      $this->set_version( $version );
      $this->demo_shortcode_params = $demo_shortcode_params;

      // Delete old options during testing
      //$this->delete_options();

      $options = array(
        'plugin_options' => $plugin_options,
        'plugin_data' => $plugin_data,
        'plugin_data_options' => $plugin_data_options,
        'instance_options' => $instance_options,
        'plugin_dependencies' => $plugin_dependencies
      );

      $this->setup($options);
    }

    //// START PROPERTIES \\\\

    protected $demo_shortcode_params = array();

    //// END PROPERTIES \\\\

    //// START GETTERS AND SETTERS \\\\

    /**
     * Initialise plugin options ONCE.
     *
     * @param array $default_options
     *
     * @since 1.0.0
     *
     * @see https://wordpress.stackexchange.com/a/209772
     */
    protected function setup( $default_options ) {
      $existing_options = $this->get_options();

      // if the user hasn't set some options in a previous session
      if ( empty( $existing_options ) ) {
        $this->set_options($default_options);
      }

      $this->set_plugin_dependency(
        array(
          'name'          => 'WordPress Admin Style',
          'slug'          => 'wordpress-admin-style',
          'source'        => 'https://github.com/bueltge/wordpress-admin-style/archive/master.zip',
          'external_url'  => 'https://github.com/bueltge/wordpress-admin-style',
          'required'      => false
        )
      );

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
      add_action( 'tgmpa_register',           [$this, 'register_required_plugins'] );
    
      // call the server side PHP function through admin-ajax.php.
      add_action( 'wp_ajax_refresh_api_data',  [$this, 'refresh_api_data'] );
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
     */
    public function register_required_plugins() {

      /**
       * Include the TGM_Plugin_Activation class.
       *
       * Parent Theme:
       * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
       *
       * Child Theme:
       * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
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
       * Array of configuration settings. Amend each line as needed.
       *
       * TGMPA will start providing localized text strings soon. If you already have translations of our standard
       * strings available, please help us make TGMPA even better by giving us access to these translations or by
       * sending in a pull-request with .po file(s) with the translations.
       *
       * Only uncomment the strings in the config array if you want to customize the strings.
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

        /*
        'strings'      => array(
          'page_title'                      => __( 'Install Required Plugins', 'text-domain' ),
          'menu_title'                      => __( 'Install Plugins', 'text-domain' ),
          /* translators: %s: plugin name. * /
          'installing'                      => __( 'Installing Plugin: %s', 'text-domain' ),
          /* translators: %s: plugin name. * /
          'updating'                        => __( 'Updating Plugin: %s', 'text-domain' ),
          'oops'                            => __( 'Something went wrong with the plugin API.', 'text-domain' ),
          'notice_can_install_required'     => _n_noop(
            /* translators: 1: plugin name(s). * /
            'This theme requires the following plugin: %1$s.',
            'This theme requires the following plugins: %1$s.',
            'text-domain'
          ),
          'notice_can_install_recommended'  => _n_noop(
            /* translators: 1: plugin name(s). * /
            'This theme recommends the following plugin: %1$s.',
            'This theme recommends the following plugins: %1$s.',
            'text-domain'
          ),
          'notice_ask_to_update'            => _n_noop(
            /* translators: 1: plugin name(s). * /
            'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
            'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
            'text-domain'
          ),
          'notice_ask_to_update_maybe'      => _n_noop(
            /* translators: 1: plugin name(s). * /
            'There is an update available for: %1$s.',
            'There are updates available for the following plugins: %1$s.',
            'text-domain'
          ),
          'notice_can_activate_required'    => _n_noop(
            /* translators: 1: plugin name(s). * /
            'The following required plugin is currently inactive: %1$s.',
            'The following required plugins are currently inactive: %1$s.',
            'text-domain'
          ),
          'notice_can_activate_recommended' => _n_noop(
            /* translators: 1: plugin name(s). * /
            'The following recommended plugin is currently inactive: %1$s.',
            'The following recommended plugins are currently inactive: %1$s.',
            'text-domain'
          ),
          'install_link'                    => _n_noop(
            'Begin installing plugin',
            'Begin installing plugins',
            'text-domain'
          ),
          'update_link'             => _n_noop(
            'Begin updating plugin',
            'Begin updating plugins',
            'text-domain'
          ),
          'activate_link'                   => _n_noop(
            'Begin activating plugin',
            'Begin activating plugins',
            'text-domain'
          ),
          'return'                          => __( 'Return to Required Plugins Installer', 'text-domain' ),
          'plugin_activated'                => __( 'Plugin activated successfully.', 'text-domain' ),
          'activated_successfully'          => __( 'The following plugin was activated successfully:', 'text-domain' ),
          /* translators: 1: plugin name. * /
          'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'text-domain' ),
          /* translators: 1: plugin name. * /
          'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'text-domain' ),
          /* translators: 1: dashboard link. * /
          'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'text-domain' ),
          'dismiss'                         => __( 'Dismiss this notice', 'text-domain' ),
          'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'text-domain' ),
          'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'text-domain' ),

          'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
        ),
        */
      );

      tgmpa( $plugins, $config );
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
     * Get the value of $prefix
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_prefix() {
      return $this->prefix;
    }

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
     * Get plugin options, user values merged with the defaults
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_options() {
      /**
       * Load any plugin user settings, falling back to an empty array if they don't exist yet
       * @see https://developer.wordpress.org/reference/functions/get_option/#parameters
       */
      $options = get_option( $this->get_prefix(), array() );
      return $options;
    }

    /**
     * Remove plugin options
     *
     * @since 1.0.0
     */
    protected function delete_options() {
      delete_option( $this->get_prefix() );
    }

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

    /**
     * Set the value of $plugin_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_options( $new_plugin_options ) {
      $options = $this->get_options();
      $old_plugin_options = $this->get_plugin_options();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options['plugin_options'] = array_merge( $old_plugin_options, $new_plugin_options );
      $this->set_options($options);
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
      return $plugin_dependencies;
    }

    /**
     * Store a plugin dependency for loading via TGMA
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_dependency( $new_plugin_dependency ) {
      $options = $this->get_options();
      $old_plugin_dependencies = $this->get_plugin_dependencies();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options['plugin_dependencies'] = array_merge( $old_plugin_dependencies, array( $new_plugin_dependency ) );
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

    /**
     * Set the value of $plugin_data
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_data( $new_plugin_data ) {
      $options = $this->get_options();
      $options['plugin_data'] = $new_plugin_data;
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
      return $plugin_data_options;
    }

    /**
     * Set the value of $plugin_data_options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    public function set_plugin_data_options( $new_plugin_data_options ) {
      $options = $this->get_options();
      $old_plugin_data_options = $this->get_plugin_data_options();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options['plugin_data_options'] = array_merge( $old_plugin_data_options, $new_plugin_data_options );
      $this->set_options($options);
    }

    /**
     * Get the value of $instance_options
     * Note: Setting only takes place within Shortcodes and Widgets
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
     * Get API data
     * This should be overwritten in the extending class
     *
     * @since 1.0.0
     *
     * @return object
     */
    protected function get_api_data() {
      return (object)[];
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
     * See also js/wpdtrt-foo-backend.js
     *
     * @param       string $format The data format ('ui'|'data')
     *
     * @since       0.1.0
     * @version     1.0.0
     *
     * @see         https://codex.wordpress.org/AJAX_in_Plugins
     * @todo        $last_updated check prevents an option change from resulting in new data
     */
    public function refresh_api_data( $format ) { // ?
      $format = sanitize_text_field( $_POST['format'] ); // ?

      if ( $format === 'ui' ) {
        $shortcode = $this->build_demo_shortcode();
      }

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

      if ( $do_refresh ) {
        $data = $this->get_api_data();
      }
      else {
        $data = $existing_data;
      }
      
      // update the UI
      if ( $format === 'ui' ) {
        $shortcode = $this->build_demo_shortcode();
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

    /**
     * Determine whether the options page form has been submitted or not
     *
     * @return      boolean
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @todo        Incorporate validation checks to ensure that all expected inputs are present (#10)
     */
    public function options_saved() {
      $options_saved = false;

      if ( isset( $_POST[$this->get_prefix() . '_form_submitted'] ) ) {

        // check that the form submission was legitimate
        $hidden_field = esc_html( $_POST[$this->get_prefix() . '_form_submitted'] );

        if ( $hidden_field === 'Y' ) {
          $options_saved = true;
        }
      }

      return $options_saved;
    }

    //// END GETTERS AND SETTERS \\\\

    //// START TRANSFORMATIONS \\\\

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
    public function normalise_field_value( $field_value, $field_type ) {
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
      }

      return $normalised_field_value;
    }

    //// END TRANSFORMATIONS \\\\

    //// START RENDERERS \\\\

    /**
     * Build demo shortcode
     *
     * @since 1.0.0
     *
     * @return string Shortcode
     */
    protected function build_demo_shortcode() {
      $params = $this->demo_shortcode_params;
      $options_page_demo_shortcode = '[';

      foreach( $params as $key => $value ) {

        if ( $key === 'id' ) {
          $options_page_demo_shortcode .= $value;
        }
        else {
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
     */
    protected function render_demo_shortcode_data() {
      $plugin_data = $this->get_plugin_data();
      $data_str = '';
      $demo_shortcode_params = $this->demo_shortcode_params;
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

      $last_updated_str .= get_gmt_from_date(
        date( $wp_time_format, $last_updated ),
        ( $wp_time_format . ', ' . $wp_date_format )
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

      wp_enqueue_script( $this->get_prefix(),
        $this->get_url()  . 'js/' . $this->get_slug() . '.js',
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
     * @version     1.0.0
     */
    public function render_js_backend( $hook_suffix ) {
      if ( $hook_suffix !== ( 'settings_page_' . $this->get_slug() ) ) {
        return;
      }

      $attach_to_footer = true;

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

      wp_localize_script( $this->get_prefix() . '_backend',
        'wpdtrt_plugin_config',
        array(
          'ajaxurl' => admin_url( 'admin-ajax.php' ),
          'messages' => $this->get_messages(),
          'force_refresh' => $plugin_data_options['force_refresh']
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
      if ( $this->options_saved() === true ) {

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
    public function render_form_element( $name, $attributes=array() ) {
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

        if ( isset( $_POST[$this->get_prefix() . '_form_submitted'] ) ):
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

      wp_enqueue_style( $this->get_prefix() . '_backend_1',
        $this->get_url()  . 'vendor/dotherightthing/wpdtrt-plugin/css/backend.css',
        array(),
        $this->get_version(),
        $media
      );

      wp_enqueue_style( $this->get_prefix() . '_backend_2',
        $this->get_url() . 'css/' . $this->get_slug() . '-admin.css',
        array(),
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

      wp_enqueue_style( $this->get_prefix(),
        $this->get_url()  . 'css/' . $this->get_slug() . '.css',
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
