<?php
/**
 * Plugin class.
 *
 * @package     WPPlugin
 * @version     1.0.0
 *
 * @todo Convert CONSTANTS into $this->properties
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
     * @param     array $options Plugin options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $options ) {

      // define variables
      $prefix = null;
      $slug = null;
      $menu_title = null;
      $developer_prefix = null;
      $plugin_directory = null;
      $option_defaults = null;

      // extract variables
      extract( $options, EXTR_IF_EXISTS );

      $this->set_prefix( $prefix );
      $this->set_slug( $slug );
      $this->set_menu_title( $menu_title );
      $this->set_developer_prefix( $developer_prefix );
      $this->set_plugin_directory( $plugin_directory );
      $this->set_option_defaults( $option_defaults );

      $this->set_options();

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
     * Get the value of $option_defaults
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       array
     */
    public function get_option_defaults() {
      return $this->option_defaults;
    }

    /**
     * Set the value of $option_defaults
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_option_defaults( $new_option_defaults ) {
      $this->option_defaults = $new_option_defaults;
    }

    /**
     * Get the value of $plugin_directory
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return       string
     */
    public function get_plugin_directory() {
      return $this->plugin_directory;
    }

    /**
     * Set the value of $plugin_directory
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_plugin_directory( $new_plugin_directory ) {
      $this->plugin_directory = $new_plugin_directory;
    }

    /**
     * Get plugin options, user values merged with the defaults
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_options() {

      $defaults = $this->get_option_defaults();

      /**
       * Load any existing options, falling back to an empty array if they don't exist yet
       * @see https://developer.wordpress.org/reference/functions/get_option/#parameters
       */
      $user_settings = get_option( $this->get_prefix(), array() );

      /**
       * Merge defaults with existing options
       * This overwrites the defaults with any user specified values
       */
      $options = array_merge( $defaults, $user_settings );

      return $options;
    }

    /**
     * Set plugin options
     *
     * @since 1.0.0
     *
     * @param array New options
     */
    protected function set_options( $new_options = array() ) {

      $old_options = $this->get_options();

      /**
       * Merge old options with new options
       * This overwrites the old values with any new values
       */
      $options = array_merge( $old_options, $new_options );

      /**
       * Save options object to WP Options table in database, as an array
       *
       * So that we only use have to consume one row in the WP Options table
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
     */
    function render_options_page() {

      if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Sorry, you do not have sufficient permissions to access this page.' );
      }

      /**
       * Make this global available within the required statement
       */
      //global $options;

      /**
       * Load existing options
       */
      $options = $this->get_options();

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
        foreach( $options as $key => $value ) {

          // if a value was submitted
          if ( !empty( $_POST[ $key ] ) ) {
            // overwrite the existing value
            $options[ $key ] = esc_html( $_POST[ $key ] );
          }
          else {
            // if a checkbox's unchecked option
            // value="1"
            if ( ( $key === ($this->get_prefix() . '_slidedown') ) || ( $key === ($this->get_prefix() . '_reveal_labels')) ) {
              // also overwrite the existing value
              $options[ $key ] = '';
            }
            // if a select's default option
            // value=""
            if ( $key === ($this->get_prefix() . '_datatype') ) {
              // also overwrite the existing value
              $options[ $key ] = '';
            }
          }
        }

        // Update options object in database
        update_option( $this->get_prefix(), $options, null );
      }

      /**
       * Use the options to get the data
       */

      // Call API and store response in options object
      $options['data'] = $this->get_api_data();

      // Store timestamp in options object
      $options['last_updated'] = time(); // UNIX timestamp for the current time

      // Update options object in database
      update_option( $this->get_prefix(), $options, null );

      // Assign values to variables
      extract( $options );

      /**
       * Load the HTML template
       * This function's variables will be available to this template,
       * includng $this
       */
      require_once($this->get_plugin_directory() . 'templates/options.php');
    }

    /**
     * Form field templating for the options page
     *
     * @param       array $author_attributes
     *
     * @return      string
     *
     * @since       1.0.0
     * @version     1.0.0
     * @todo        Add field validation feedback
     */
    public function render_form_element( $author_attributes ) {

      $default_attributes = array(
        'type' => 'textfield',
        'name' => null,
        'label' => 'Label',
        'size' => 20,
        'tip' => null,
        'usage' => 'option', // option | widget
        'scope' => null
      );

      $type = null;
      $name = null;
      $label = null;
      $tip = null;
      $size = null;
      $usage = null; // option | widget
      $scope = null;

      $attributes = array_merge( $default_attributes, $author_attributes );
      extract( $attributes, EXTR_IF_EXISTS );

      $nameStr = $name;

      // layout
      $label_start = '<tr><th scope="row">';
      $label_end   = '</th>';
      $field_start = '<td>';
      $field_end   = '</td></tr>';
      $tip_element = 'div';
      $classname   = 'regular-text';

      // Load options array
      $options = get_option( $this->get_prefix() );

      // Assign values to variables
      extract( $options );

      /**
       * Set the value to the variable with the same name as the $name string
       * e.g. $name="wpdtrt_attachment_map_toggle_label" => $wpdtrt_attachment_map_toggle_label => ('Open menu', 'wpdtrt-attachment-map')
       * @see http://php.net/manual/en/language.variables.variable.php
       */

      // if the option variable doesn't exist yet, don't output it
      if ( ! isset( ${$nameStr} ) ) {
        return;
      }

      // same
      $id = $nameStr;

      $value = ${$nameStr};

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

      require($this->get_plugin_directory() . 'vendor/dotherightthing/wpdtrt-plugin/views/form-element-' . $type . '.php');

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
              __('settings successfully updated', 'wpdtrt-attachment-map');
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

      wp_enqueue_style( 'render_css_backend',
        WPDTRT_ATTACHMENT_MAP_URL . 'css/wpdtrt-attachment-map-admin.css',
        array(),
        WPDTRT_ATTACHMENT_MAP_VERSION,
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

      wp_enqueue_style( 'wpdtrt_attachment_map',
        WPDTRT_ATTACHMENT_MAP_URL . 'css/wpdtrt-attachment-map.css',
        array(
          // load these registered dependencies first:
          //'a_dependency'
        ),
        WPDTRT_ATTACHMENT_MAP_VERSION,
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
        WPDTRT_ATTACHMENT_MAP_URL . 'vendor/bower_components/a_dependency/a_dependency.js',
        array(
          'jquery'
        ),
        DEPENDENCY_VERSION,
        $attach_to_footer
      );
      */

      wp_enqueue_script( 'wpdtrt_attachment_map',
        WPDTRT_ATTACHMENT_MAP_URL . 'js/wpdtrt-attachment-map.js',
        array(
          // load these registered dependencies first:
          'jquery'
          // a_dependency
        ),
        WPDTRT_ATTACHMENT_MAP_VERSION,
        $attach_to_footer
      );

      wp_localize_script( 'wpdtrt_attachment_map',
        'wpdtrt_attachment_map_config',
        array(
          'ajax_url' => admin_url( 'admin-ajax.php' ) // wpdtrt_attachment_map_config.ajax_url
        )
      );
    }

    //// END RENDERERS \\\\

  }
}

?>