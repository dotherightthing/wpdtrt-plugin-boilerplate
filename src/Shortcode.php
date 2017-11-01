<?php
/**
 * Plugin shortcode class.
 *
 * @package     WPPlugin
 * @version     1.0.0
 *
 * @todo Convert CONSTANTS into $this->properties
 */

namespace DoTheRightThing\WPPlugin;

if ( !class_exists( 'Shortcode' ) ) {

  /**
   * Plugin Shortcode base class
   *
   * Boilerplate functions, including
   * options support, template loading, access to Plugin methods.
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
  class Shortcode {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new Widget
     *
     * @param     array $options Shortcode options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $options ) {

      // define variables
      $name = null;
      $plugin = null;
      $template = null;
      $selected_instance_options = null;

      // extract variables
      extract( $options, EXTR_IF_EXISTS );

      // Store a reference to the partner plugin object
      // which stores global plugin options
      $this->set_plugin( $plugin );
      $this->set_template_name( $template );

      $shortcode_instance_options = array(
        //'classname' => $classname,
      );

      $plugin_instance_options = $plugin->get_instance_options();

      foreach( $selected_instance_options as $option_name ) {
        $shortcode_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
      }

      $this->set_instance_options( $shortcode_instance_options );

      $this->set_name( $name ); // for render_shortcode

      add_shortcode( $name, [$this, 'render_shortcode'] );
    }

    //// START GETTERS AND SETTERS \\\\

    /**
     * Set the template name
     *
     * @param string $template_name
     *
     * @since 1.0.0
     *
     */
    protected function set_template_name( $template_name ) {
      $this->template_name = $template_name;
    }

    /**
     * Get the template name
     *
     * @return string
     *
     * @since 1.0.0
     *
     */
    protected function get_template_name() {
      return $this->template_name;
    }

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
     * Set parent plugin, which contains shortcode/widget options
     * This is a global which is passed to the function which instantiates this object.
     * This is necessary because the object does not exist until the WordPress init action has fired.
     *
     * @todo Can this be improved? Setting a high priority (of 0) on the init action
     *  does not make the object available to the widget_init action
     *  which should run afterwards.
     *  Can the reference be passed in a better way?
     *
     * @since 1.0.0
     *
     * @param object
     */
    protected function set_plugin( $plugin ) {
      $this->plugin = $plugin;
    }

    /**
     * Get parent plugin, which contains shortcode/widget options
     *
     * @since 1.0.0
     *
     * @return object
     * @todo $plugin_options_reduced is weeding out the API data, which shouldn't be in here anyway
     */
    public function get_plugin() {
      return $this->plugin;
    }

    //// END GETTERS AND SETTERS \\\\

    //// START RENDERERS \\\\

    /**
     * Render a shortcode
     *
     * @since 1.0.0
     *
     * @param array $atts User defined attributes in shortcode tag
     * @param string $content Content between shortcode opening and closing tags
     *
     * @return string
     */
    public function render_shortcode( $atts, $content = null ) {

      // post object to get info about the post in which the shortcode appears
      // global $post;

      /**
       * Combine user attributes with known attributes and fill in defaults when needed.
       * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
       */

      // merge shortcode options with user's shortcode $atts
      $template_options = shortcode_atts(
        $this->get_instance_options(),
        $atts,
        $this->get_name()
      );

      // store a reference to the parent plugin
      $plugin = $this->get_plugin();

      $template_options['plugin'] = $plugin;

      // Pass options to template-part as query var
      //set_query_var( $this->get_prefix() . '_options_all', $options_all );
      set_query_var( 'options', $template_options );

      /**
       * ob_start — Turn on output buffering
       * This stores the HTML template in the buffer
       * so that it can be output into the content
       * rather than at the top of the page.
       */
      ob_start();

      // mimic WordPress template loading
      // to allow authors to override loaded templates
      $templates = new TemplateLoader( array(
        'filter_prefix' => $plugin->get_prefix(),
        'plugin_template_directory' => 'template-parts/' . $plugin->get_slug(),
        'theme_template_directory' => 'template-parts/' . $plugin->get_slug(),
        'path' => $plugin->get_path()
      ));;

      // /template-parts/wpdtrt-plugin-name/content/foo.php
      $templates->get_template_part( 'content', $this->get_template_name() );

      /**
       * ob_get_clean — Get current buffer contents and delete current output buffer
       */
      $content = ob_get_clean();

      return $content;
    }

    //// END RENDERERS \\\\
  }
}
