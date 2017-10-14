<?php
/**
 * Plugin widget class.
 *
 * Boilerplate to generate a widget,  which is configured in WP Admin, and can be displayed in sidebars.
 *
 * @package     WPDTRT_Attachment_Map
 * @subpackage  WPDTRT_Attachment_Map/app
 * @since       0.1.0
 */

namespace DoTheRightThing\WPPlugin;

if ( !class_exists( 'Widget' ) ) {

  /**
   * Plugin Widget sub class.
   *
   * Extends and inherits from WP_Widget.
   * WP_Widget must be extended for each widget, and WP_Widget::widget() must be overridden.
   * Class names should use capitalized words separated by underscores. Any acronyms should be all upper case.
   *
   * @uses        ../../../../wp-includes/class-wp-widget.php:
   * @see         https://developer.wordpress.org/reference/classes/wp_widget/
   * @see         https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
   *
   * @since       0.1.0
   * @version     1.0.0
   */

  class Widget extends \WP_Widget {

    /**
     * Hook the plugin in to WordPress
     * This constructor automatically initialises the object's properties
     * when it is instantiated,
     * using new Widget
     *
     * @param     array $options Plugin options
     *
     * @version   1.1.0
     * @since     1.0.0
     */
    function __construct( $options ) {

      // define variables
      $widget_name = null;
      $widget_title = null;
      $parent_plugin = null;
      $template = null;
      $option_defaults = null;
      //$classname = null;
      $description = null;

      // extract variables
      extract( $options, EXTR_IF_EXISTS );

      // Store a reference to the partner plugin object
      // which stores global plugin options
      $this->set_parent_plugin( $parent_plugin );
      $this->set_template_name( $template );
      $this->set_option_defaults( $option_defaults );
      //$this->set_options();

      $widget_options = array(
        //'classname' => $classname,
        'description' => $description,
      );

      // Instantiate the parent object
      parent::__construct( $widget_name, $widget_title, $widget_options );
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
     * Set default options
     *
     * @param array $option_defaults
     *
     * @since 1.0.0
     *
     */
    protected function set_option_defaults( $option_defaults ) {
      $this->option_defaults = $option_defaults;
    }

    /**
     * Get default options
     *
     * @return array
     *
     * @since 1.0.0
     *
     */
    protected function get_option_defaults() {
      return $this->option_defaults;
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
    protected function set_parent_plugin( $parent_plugin ) {
      $this->parent_plugin = $parent_plugin;
    }

    /**
     * Get parent plugin, which contains shortcode/widget options
     *
     * @since 1.0.0
     *
     * @return object
     * @todo $parent_plugin_options_reduced is weeding out the API data, which shouldn't be in here anyway
     */
    public function get_parent_plugin() {
      return $this->parent_plugin;
    }

    //// END GETTERS AND SETTERS \\\\

    //// START RENDERERS \\\\

    /**
     * Form field templating for the widget admin page
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
        'instance' => null
      );

      $type = null;
      $name = null;
      $label = null;
      $tip = null;
      $size = null;
      $instance = null;

      $attributes = array_merge( $default_attributes, $author_attributes );
      extract( $attributes, EXTR_IF_EXISTS );

      $nameStr = $name;

      // layout
      $label_start = '<p>';
      $label_end   = '';
      $field_start = '';
      $field_end   = '</p>';
      $tip_element = 'span';
      $classname   = 'widefat'; // full width

      /**
       * Set the value to the variable with the same name as the $name string
       * e.g. $name="wpdtrt_attachment_map_toggle_label" => $wpdtrt_attachment_map_toggle_label => ('Open menu', 'wpdtrt-attachment-map')
       * @see http://php.net/manual/en/language.variables.variable.php
       */

      // translate e.g. 'number' to 'wp-widget-foobar[1]-number'
      $name = $this->get_field_name($nameStr);
      $value = isset( $instance[ $nameStr ] ) ? $instance[ $nameStr ] : '';

      if ( $nameStr === 'title' ):
        // Display dynamic widget title in .in-widget-title via appendTitle() in wp-admin/js/widgets.min.js;
        $id = $name . '-' . $nameStr;
      else:
        $id = $name;
      endif;

      $parent_plugin = $this->get_parent_plugin();

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

      require($parent_plugin->get_plugin_directory() . 'vendor/dotherightthing/wpdtrt-plugin/views/form-element-' . $type . '.php');

      /**
       * ob_get_clean — Get current buffer contents and delete current output buffer
       */
      return ob_get_clean();
    }

    /**
     * Echoes the widget content to the front-end
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance The settings for the particular instance of the widget.
     *
     * @todo Should $title be passed via query var? as we are using the template loader rather than an include?
     */
    function widget( $args, $instance ) {

      /**
       * Get the unique ID
       * @link https://kylebenk.com/how-to-wordpress-widget-id/
       */
      // $instance_id = $this->id;

      // merge display $args with $instance settings
      $template_options = array_merge( $args, $instance );

      // store a reference to the parent plugin
      $template_options['parent_plugin'] = $this->get_parent_plugin();

      /**
       * apply_filters( $tag, $value );
       * Apply the 'widget_title' filter to get the title of the instance.
       * Display the title of this instance, which the user can optionally customise
       */
      $template_options['title'] = apply_filters( 'widget_title', $instance['title'] );

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
      $templates = new Template_Loader( array(
        'filter_prefix' => $parent_plugin->get_prefix(),
        'plugin_template_directory' => 'template-parts/' . $parent_plugin->get_prefix(),
        'theme_template_directory' => 'template-parts/' . $parent_plugin->get_prefix(),
        'plugin_directory' => $parent_plugin->get_plugin_directory()
      ));;

      // /template-parts/wpdtrt-plugin-name/content/foo.php
      $templates->get_template_part( 'content', $this->get_template_name() );

      /**
       * ob_get_clean — Get current buffer contents and delete current output buffer
       */
      $content = ob_get_clean();

      // echo not return
      echo $content;
    }

    /**
     * Updates a particular instance of a widget, by replacing the old instance with data from the new instance
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Settings to save or bool false to cancel saving.
     */
    function update( $new_instance, $old_instance ) {
      // Save user input (widget options)

      $parent_plugin = $this->get_parent_plugin();
      $instance = $old_instance;
      $option_defaults = $this->get_option_defaults();

      /**
       * strip_tags — Strip HTML and PHP tags from a string
       * @example string strip_tags ( string $str [, string $allowable_tags ] )
       * @link http://php.net/manual/en/function.strip-tags.php
       */
      if ( isset( $new_instance['title'] ) ) {
        $instance['title'] = strip_tags( $new_instance['title'] );
      }

      foreach( $option_defaults as $key=>$value ) {

        // todo: does this check prevent empty values from being saved?
        if ( isset( $new_instance[ $key ] ) ) {
          $instance[ $key ] = strip_tags( $new_instance[ $key ] );
        }
      }

      return $instance;
    }

    /**
     * Outputs the settings update form in wp-admin.
     *
     * @param array $instance Current settings.
     * @return string Default return is 'noform'.
     */
    function form( $instance ) {

      // get a reference to the instance
      $parent_plugin = $this->get_parent_plugin();

      /**
        * Escape HTML attributes to sanitize the data.
        * @example esc_attr( string $text )
        * @link https://developer.wordpress.org/reference/functions/esc_attr/
        */
      if ( isset( $instance['title'] ) ) {
        $title = esc_attr( $instance['title'] );
      }
      else {
        $title = null;
      }

      foreach( $this->get_option_defaults() as $key=>$value ) {

        // evaluate variables to populate the 'value' attribute
        if ( isset( $instance[ $key ] ) ) {
          // $enlargement = $instance[ 'enlargement' ];
          ${$key} = esc_attr( $instance[ $key ] );
        }
        else {
          ${$key} = null;
        }
      }

      $options = $parent_plugin->get_options();
      $data = $options['data'];

      /**
       * Load the HTML template
       * This function's variables will be available to this template.
       */

      // invoke a method of the instance
      $plugin_directory = $parent_plugin->get_plugin_directory(); // plugin and shortcode/widget options

      require($plugin_directory . 'templates/widget-admin.php');
    }

    //// END RENDERERS \\\\
  }
}

?>
