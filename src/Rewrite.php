<?php
/**
 * Plugin rewrite class.
 *
 * @package   WPPlugin
 * @version   1.0.0
 */

namespace DoTheRightThing\WPPlugin\r_1_4_15;

if ( !class_exists( 'Rewrite' ) ) {

  /**
   * Plugin Rewrite base class
   *
   * Boilerplate functions
   *
   * @return      Rewrite
   *
   * @since       1.4.15
   * @version     1.0.0
   */
  class Rewrite {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new Rewrite
     *
     * @param     array $options Rewrite options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $options ) {

      // define variables
      $name = null;
      $plugin = null;
      //$selected_instance_options = null;
      $labels = null;
      //$rewrite_options = null;

      // extract variables
      extract( $options, EXTR_IF_EXISTS );

      // Store a reference to the partner plugin object
      // which stores global plugin options
      $this->set_plugin( $plugin );

      /*
      $rewrite_instance_options = array();

      $plugin_instance_options = $plugin->get_instance_options();

      foreach( $selected_instance_options as $option_name ) {
        $rewrite_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
      }

      $this->set_instance_options( $rewrite_instance_options );
      */

      $this->set_labels( $labels );

      $this->set_name( $name );

      //$this->set_options( $rewrite_options );

      //$this->register_taxonomy();

      // hook in to WordPress
      $this->wp_setup();
    }

    /**
     * Initialise rewrite options ONCE.
     *
     * @param array $default_options
     *
     * @since 1.0.0
     */
    protected function wp_setup() {

      $rewrite_name = $this->get_name();

      /**
       * $this->render_foobar() - infers that no args are to be passed, fails
       * @see https://stackoverflow.com/questions/28954168/php-how-to-use-a-class-function-as-a-callback
       * @see https://tommcfarlin.com/wordpress-plugin-constructors-hooks/
       */
      // add_filter( 'post_type_link',                                       [$this, 'replace_rewrite_in_cpt_permalinks'], 10, 3); // Custom post type
    }

    //// START GETTERS AND SETTERS \\\\

    /**
     * Get the value of $name
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      string
     */
    public function get_name() {
      return $this->name;
    }

    /**
     * Set the value of $name
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       string
     */
    protected function set_name( $new_name ) {
      $this->name = $new_name;
    }

    /**
     * Get default options
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_instance_options() {
      return $this->instance_options;
    }

    /**
     * Set instance options
     *
     * @param array $instance_options
     *
     * @since 1.0.0
     *
     */
    protected function set_instance_options( $instance_options ) {
      $this->instance_options = $instance_options;
    }

    /**
     * Get the value of $labels
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      array
     */
    public function get_labels() {
      return $this->labels;
    }

    /**
     * Set the value of $labels
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_labels( $labels ) {
      $this->labels = $labels;
    }

    /**
     * Set parent plugin, which contains shortcode/widget options
     * This is a global which is passed to the function which instantiates this object.
     * This is necessary because the object does not exist until the WordPress init action has fired.
     *
     * @param object
     *
     * @since 1.0.0
     *
     * @todo Shortcode/Widget implementation questions (#15)
     */
    protected function set_plugin( $plugin ) {
      $this->plugin = $plugin;
    }

    /**
     * Get parent plugin, which contains shortcode/widget options
     *
     * @return object
     *
     * @since 1.0.0
     */
    public function get_plugin() {
      return $this->plugin;
    }

    /**
     * Get the value of $options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @return      array
     */
    public function get_options() {
      return $this->options;
    }

    /**
     * Set the value of $options
     *
     * @since       1.0.0
     * @version     1.0.0
     *
     * @param       array
     */
    protected function set_options( $new_options ) {
      $this->options = $new_options;
    }

    //// END GETTERS AND SETTERS \\\\

    //// START RENDERERS \\\\
    //// END RENDERERS \\\\

    //// START FILTERS \\\\
    //// END FILTERS \\\\

    //// START HELPERS \\\\
    //// END HELPERS \\\\
  }
}
