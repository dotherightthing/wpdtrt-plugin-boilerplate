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
     * using new WPDTRT_Attachment_Map_Plugin
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
      $plugin_options = null;
      $instance_options = null;
      $version = null;

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

      $options = array(
        'plugin_options' => $plugin_options,
        'instance_options' => $instance_options
      );

      $this->setup($options);

      // attempt 1 - infers that no args are to be passed, fails
      //add_action( 'wp_enqueue_scripts', $this->render_js_data_frontend() );
      //add_shortcode( $this->shortcode_name, $this->render_shortcode() );

      // attempt 2 - as the expected callback
      // https://stackoverflow.com/questions/28954168/php-how-to-use-a-class-function-as-a-callback
      add_action( 'admin_menu',         [$this, 'render_options_menu'] );
      add_action( 'admin_notices',      [$this, 'render_settings_errors'] );
      add_action( 'admin_notices',      [$this, 'render_admin_notices'] );
      add_action( 'admin_head',         [$this, 'render_css_backend'] );
      add_action( 'wp_enqueue_scripts', [$this, 'render_css_frontend'] );
      add_action( 'wp_enqueue_scripts', [$this, 'render_js_frontend'] );
      add_action( 'wp_enqueue_scripts', [$this, 'render_js_data_frontend'] );

      //add_action('wp_ajax_data_refresh', [$this, 'get_api_data_again'] );
    }

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
     * Set the value of $url
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
     * @todo        Add field validation feedback
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
     * Generate a JavaScript object which the front-end can query
     *
     * @since 1.0.0
     */
    public function render_js_data_frontend() {

      // Load existing options
      $options = $this->get_options();

      // configure mobile JS
      wp_localize_script(
        $this->get_prefix(),
        $this->get_prefix() . '_options',
        $options
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
     * Render the appropriate UI on Settings > DTRT Attachment Map
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
     * @todo        Translate wp_die ?
     */
    function render_options_page() {

      if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Sorry, you do not have sufficient permissions to access this page.' );
      }

      /**
       * Load existing options
       */
      $plugin_options = $this->get_plugin_options();

      /**
       * If the form was submitted, update the options
       */
      if ( isset( $_POST[$this->get_prefix() . '_form_submitted'] ) ) {

        // check that the form submission was legitimate
        $hidden_field = esc_html( $_POST[$this->get_prefix() . '_form_submitted'] );

        if ( $hidden_field !== 'Y' ) {
          return;
        }

        /**
         * Save default/user values from form submission
         * @see https://stackoverflow.com/a/13461680/6850747
         * @todo https://github.com/dotherightthing/generator-wp-plugin-boilerplate/issues/16
         * @todo https://github.com/dotherightthing/generator-wp-plugin-boilerplate/issues/17
         */
        foreach( $plugin_options as $name => $attributes ) {

          // if a value was submitted
          if ( !empty( $_POST[ $name ] ) ) {
            // overwrite the existing value
            $plugin_options[ $name ]['value'] = esc_html( $_POST[ $name ] );
          }
          else {
            // if the form contained an unchecked checkbox
            // save the value as empty, rather than discarding it
            if ( $plugin_options[ $name ]['type'] === 'checkbox') {
              $plugin_options[ $name ]['value'] = '';
            }
            // if the form contained an unselected select
            // save the value as empty, rather than discarding it
            else if ( $plugin_options[ $name ]['type'] === 'select') {
              $plugin_options[ $name ]['value'] = '';
            }
          }
        }

        // If we've updated our options
        // get the latest data from the API

        // Call API and store response in options object
        $plugin_options['data'] = $this->get_api_data();

        // Store timestamp in options object
        $plugin_options['last_updated'] = time(); // UNIX timestamp for the current time

        // Update options object in database
        $this->set_plugin_options( $plugin_options );
      }
      // if data has already been retrieved from API
      // get the latest data from the API
      else if ( isset( $plugin_options['last_updated'] ) ) {

        // Call API and store response in options object
        $plugin_options['data'] = $this->get_api_data();

        // Store timestamp in options object
        $plugin_options['last_updated'] = time(); // UNIX timestamp for the current time

        // Update options object in database
        $this->set_plugin_options( $plugin_options );
      }

      /**
       * Load the HTML template
       * This function's variables will be available to this template,
       * includng $this
       * $plugin_options are retrieved afresh inside the template
       */
      require_once($this->get_path() . 'templates/options.php');
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
     * @todo        Add field validation feedback
     */
    public function render_form_element( $name, $attributes=array() ) {

      // these options don't have attributes
      if ( ( $name === 'data' ) || ( $name === 'last_updated' ) ) {
        return;
      }

      // define variables
      $type = null;
      $label = null;
      $size = null;
      $tip = null;
      $options = null;
      $value = null;

      // populate variables
      extract( $attributes, EXTR_IF_EXISTS );

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
     * @see     https://digwp.com/2016/05/wordpress-admin-notices/
     * @since       1.0.0
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
     * @see     https://digwp.com/2016/05/wordpress-admin-notices/
     * @since       1.0.0
     * @version   1.0.0
     *
     * @todo      Pass in an array of translated messages
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

      wp_enqueue_style( $this->get_prefix() . '_backend',
        $this->get_url() . 'css/' . $this->get_slug() . '-admin.css',
        array(),
        $this->get_version() ,
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
        $this->get_version() ,
        $media
      );
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
          // a_dependency
        ),
        $this->get_version() ,
        $attach_to_footer
      );

      wp_localize_script( $this->get_prefix(),
        $this->get_prefix() . '_config',
        array(
          'ajax_url' => admin_url( 'admin-ajax.php' ) // wpdtrt_blocks_config.ajax_url
        )
      );
    }

    //// END RENDERERS \\\\

  }
}

?>
