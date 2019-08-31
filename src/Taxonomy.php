<?php
/**
 * File: src/Taxonomy.php
 *
 * Plugin taxonomy class.
 */

namespace DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_1;

if ( ! class_exists( 'Taxonomy' ) ) {

	/**
	 * Class: Taxonomy
	 *
	 * Plugin Taxonomy base class.
	 *
	 * Note:
	 * - Contains boilerplate functions, including
	 *   options support, registration, template loading, custom fields
	 *
	 * See:
	 * - <http://php.net/manual/en/function.ob-start.php>
	 * - <http://php.net/manual/en/function.ob-get-clean.php>
	 *
	 * Since:
	 *   1.0.0
	 */
	class Taxonomy {

		/**
		 * Constructor: __construct
		 *
		 * Hook the plugin in to WordPress.
		 *
		 * Note:
		 * - This constructor automatically initialises the object's properties
		 *   when it is instantiated.
		 * - This is a public method as every plugin uses a new instance.
		 *
		 * Example:
		 * --- php
		 * $wpdtrt_test_taxonomy = new DoTheRightThing\WPDTRT_Plugin_Boilerplate\r_1_6_1\Taxonomy {}
		 * ---
		 *
		 * Parameters:
		 *   $options - Shortcode options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function __construct( array $options ) {

			// define variables.
			$name                      = null;
			$register                  = true;
			$plugin                    = null;
			$selected_instance_options = null;
			$labels                    = null;
			$taxonomy_options          = null;

			// extract variables.
			extract( $options, EXTR_IF_EXISTS );

			// Store a reference to the partner plugin object,
			// which stores global plugin options.
			$this->set_plugin( $plugin );

			$taxonomy_instance_options = array();

			$plugin_instance_options = $plugin->get_instance_options();

			foreach ( $selected_instance_options as $option_name ) {
				$taxonomy_instance_options[ $option_name ] = $plugin_instance_options[ $option_name ];
			}

			$this->set_instance_options( $taxonomy_instance_options );

			$this->set_name( $name );

			$this->set_options( $taxonomy_options );

			if ( true === $register ) {
				$this->set_labels( $labels );
				$this->register_taxonomy();
			}

			// hook in to WordPress.
			$this->wp_setup();
		}

		/**
		 * Method: wp_setup
		 *
		 * Initialise taxonomy options ONCE.
		 *
		 * See:
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 * - <https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/>
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function wp_setup() {
			$taxonomy_name = $this->get_name();

			/**
			 * $this->render_foobar() - infers that no args are to be passed, fails
			 *
			 * @see https://stackoverflow.com/questions/28954168/php-how-to-use-a-class-function-as-a-callback
			 * @see https://tommcfarlin.com/wordpress-plugin-constructors-hooks/
			 */
			add_action( $taxonomy_name . '_add_form_fields', array( $this, 'render_options' ), 10, 2 );
			add_action( 'created_' . $taxonomy_name, array( $this, 'create_options' ), 10, 2 );
			add_action( $taxonomy_name . '_edit_form_fields', array( $this, 'edit_options' ), 10, 2 );
			add_action( 'edited_' . $taxonomy_name, array( $this, 'update_options' ), 10, 2 );
			add_filter( 'manage_edit-' . $taxonomy_name . '_columns', array( $this, 'options_columns' ) );
			add_filter( 'manage_' . $taxonomy_name . '_custom_column', array( $this, 'options_columns_content' ), 10, 3 );
			add_filter( 'manage_edit-' . $taxonomy_name . '_sortable_columns', array( $this, 'options_columns_sortable' ) );
			add_filter( 'post_type_link', array( $this, 'replace_taxonomy_in_cpt_permalinks' ), 10, 3 ); // Custom post type.
		}

		/**
		 * Group: Setters and Getters
		 * _____________________________________
		 */

		/**
		 * Method: get_name
		 *
		 * Get the value of $name.
		 *
		 * Returns:
		 *   Name
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_name() : string {
			return $this->name;
		}

		/**
		 * Method: set_name
		 *
		 * Set the value of $name.
		 *
		 * Parameters:
		 *   $new_name - New name
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_name( string $new_name ) {
			$this->name = $new_name;
		}

		/**
		 * Method: get_instance_options
		 *
		 * Get default options.
		 *
		 * Returns:
		 *   Instance options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_instance_options() : array {
			return $this->instance_options;
		}

		/**
		 * Method: set_instance_options
		 *
		 * Set instance options.
		 *
		 * Parameters:
		 *   $instance_options - Instance options
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_instance_options( array $instance_options ) {
			$this->instance_options = $instance_options;
		}

		/**
		 * Method: get_labels
		 *
		 * Get the value of $labels.
		 *
		 * Returns:
		 *   Labels
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_labels() : array {
			return $this->labels;
		}

		/**
		 * Method: set_labels
		 *
		 * Set the value of $labels.
		 *
		 * Parameters:
		 *   $labels - Labels
		 *
		 * TODO:
		 * - Limit to allowed labels
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_labels( array $labels ) {
			$this->labels = $labels;
		}

		/**
		 * Method: set_plugin
		 *
		 * Set parent plugin, which contains shortcode/widget options.
		 *
		 * Note:
		 * - This is a global which is passed to the function which instantiates this object.
		 * - This is necessary because the object does not exist until the WordPress init action has fired.
		 *
		 * Parameters:
		 *   $plugin - Plugin
		 *
		 * TODO:
		 * - Shortcode/Widget implementation questions (#15)
		 * - Add the appropriate type for $plugin (not object)
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_plugin( $plugin ) {
			$this->plugin = $plugin;
		}

		/**
		 * Method: get_plugin
		 *
		 * Get parent plugin, which contains shortcode/widget options.
		 *
		 * Returns:
		 *   Plugin
		 *
		 * TODO:
		 * - Add return type (not object)
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_plugin() {
			return $this->plugin;
		}

		/**
		 * Method: get_options
		 *
		 * Get the value of $options.
		 *
		 * Returns:
		 *   Options
		 *
		 * Since:
		 *   1.0.0
		 */
		public function get_options() : array {
			return $this->options;
		}

		/**
		 * Method: set_options
		 *
		 * Set the value of $options.
		 *
		 * Parameters:
		 *   $new_options - New options
		 *
		 * Since:
		 *   1.0.0
		 */
		protected function set_options( array $new_options ) {
			$this->options = $new_options;
		}

		/**
		 * Group: Data management
		 * _____________________________________
		 */

		/**
		 * Method: create_options
		 *
		 * Save custom meta from the term create screen.
		 *
		 * Parameters:
		 *   $term_id - Term ID
		 *   $tt_id - Term taxonomy ID
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/hooks/created_taxonomy/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function create_options( int $term_id, int $tt_id ) {

			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {

				if ( isset( $_POST[ $name ] ) && '' !== $_POST[ $name ] ) {
					$group = sanitize_title( $_POST[ $name ] );
					add_term_meta( $term_id, $name, $group, false );
				}
			}
		}

		/**
		 * Method: edit_options
		 *
		 * Display custom meta on the term edit screen.
		 *
		 *
		 * Parameters:
		 *   $tag - Current taxonomy term object.
		 *   $taxonomy - Current taxonomy slug.
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/hooks/taxonomy_edit_form_fields/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 *
		 * Since:
		 *   1.0.0
		 */
		public function edit_options( object $tag, string $taxonomy ) {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {
				echo $this->render_form_element( $name, $attributes, $tag->term_id );
			}
		}

		/**
		 * Method: update_options
		 *
		 * Save custom meta from the term edit screen.
		 *
		 * Parameters:
		 *   $term_id - Term ID
		 *   $tt_id - Term taxonomy ID
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/hooks/edited_taxonomy/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function update_options( int $term_id, int $tt_id ) {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {

				if ( isset( $_POST[ $name ] ) && '' !== $_POST[ $name ] ) {
					$group = sanitize_title( $_POST[ $name ] );
					update_term_meta( $term_id, $name, $group );
				}
			}
		}

		/**
		 * Method: options_columns
		 *
		 * Display options in the term list table.
		 *
		 * Parameters:
		 *   $columns - Table columns
		 *
		 * Returns:
		 *   Columns
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function options_columns( array $columns ) : array {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {
				if ( $attributes['admin_table'] ) {
					$columns[ $name ] = $attributes['admin_table_label'];
				}
			}

			return $columns;
		}

		/**
		 * Method: options_columns_content
		 *
		 * Display options in the term list table.
		 *
		 * Parameters:
		 *   $content - Content
		 *   $column_name - Content name
		 *   $term_id - Term ID
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function options_columns_content( string $content, string $column_name, int $term_id ) {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {

				if ( $name === $column_name ) {

					$term_id = absint( $term_id );

					$user_value = get_term_meta( $term_id, $name, true );

					if ( ! empty( $user_value ) ) {
						$content .= esc_attr( $user_value );
					}
				}
			}

			// return $content - fails for me.
			echo esc_html( $content );
		}

		/**
		 * Method: options_columns_sortable
		 *
		 * Make options sortable in the term list table.
		 *
		 * Parameters:
		 *   $sortable - Sortable array.
		 *
		 * Returns:
		 *   Sortable array
		 *
		 * See:
		 * - <https://developer.wordpress.org/reference/>
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * TODO:
		 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/42>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function options_columns_sortable( array $sortable ) : array {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {
				if ( true === $attributes['admin_table_sort'] ) {
					$sortable[ $name ] = $name;
				}
			}

			return $sortable;
		}

		/**
		 * Group: Renderers
		 * _____________________________________
		 */

		/**
		 * Method: render_options
		 *
		 * Display custom meta on the term create screen.
		 *
		 * Parameters:
		 *   $taxonomy - The taxonomy slug
		 *
		 * See:
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 * - <https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/>
		 *
		 * Since:
		 *   1.0.0
		 */
		public function render_options( string $taxonomy ) {
			$taxonomy_options = $this->get_options();

			foreach ( $taxonomy_options as $name => $attributes ) {
				echo $this->render_form_element( $name, $attributes );
			}
		}

		/**
		 * Method: render_form_element
		 *
		 * Form field templating for the taxonomy admin page.
		 *
		 * Parameters:
		 *   $name - Name
		 *   $attributes - Attributes
		 *   $term_id - Taxonomy term ID (available in edit_options)
		 *
		 * See:
		 * - <https://www.smashingmagazine.com/2015/12/how-to-use-term-meta-data-in-wordpress/>
		 *
		 * Returns:
		 * - Form element HTML
		 *
		 * TODO:
		 * - Add field validation feedback (#10)
		 *
		 * Since:
		 *  1.0.0
		 */
		public function render_form_element( string $name, array $attributes = [], string $term_id ) : string {

			// these options don't have attributes.
			if ( 'description' === $name ) {
				return '';
			}

			// define variables.
			$type    = null;
			$label   = null;
			$size    = null;
			$tip     = null;
			$options = null;

			// populate variables.
			extract( $attributes, EXTR_IF_EXISTS );

			// name as a string.
			$name_str = $name;
			$id       = $name;

			// widget admin layout.
			$label_start = '';
			$label_end   = '';
			$field_start = '';
			$field_end   = '';
			$tip_element = 'p';
			$classname   = '';

			if ( isset( $term_id ) ) {
				$label_start = '<tr class="form-field term-group-wrap wpdtrt-plugin-boilerplate--taxonomy-form-field"><th scope="row">';
				$label_end   = '</th>';
				$field_start = '<td>';
				$field_end   = '</td></tr>';

				$user_value = get_term_meta( $term_id, $name, true );
			} else {
				$user_value = null;
			}

			$plugin = $this->get_plugin();

			$value = $plugin->helper_normalise_field_value(
				( isset( $user_value ) ? $user_value : null ),
				$type
			);

			/**
			 * Load the HTML template
			 * The supplied arguments will be available to this template.
			 */

			/**
			 * Turn on output buffering
			 *  This stores the HTML template in the buffer
			 *  so that it can be output into the content
			 *  rather than at the top of the page.
			 */
			ob_start();

			require $plugin->get_path() . 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/views/form-element-' . $type . '.php';

			/**
			 * Get current buffer contents and delete current output buffer.
			 */
			return ob_get_clean();
		}

		/**
		 * Group: Registerers
		 * _____________________________________
		 */

		/**
		 * Method: register_taxonomy
		 *
		 * Register taxonomy.
		 *
		 * Uses:
		 * - ../../../../wp-includes/taxonomy.php
		 *
		 * Note:
		 * - Register Custom Taxonomy BEFORE the Custom Post Type,
		 *   for the rewrite rule to work and
		 *   for WordPress to build the URL correctly
		 *
		 * TODO:
		 * - Test how redundant it is to specify the advanced options here (#126)
		 *
		 * See:
		 * - <https://codex.wordpress.org/Function_Reference/register_taxonomy>
		 * - <https://www.smashingmagazine.com/2012/01/create-custom-taxonomies-wordpress/>
		 * - <https://code.tutsplus.com/articles/the-rewrite-api-post-types-taxonomies--wp-25488>
		 * - <https://cnpagency.com/blog/the-right-way-to-do-wordpress-custom-taxonomy-rewrites/>
		 * - <https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/>
		 */
		public function register_taxonomy() {

			$name = $this->get_name();

			if ( ! taxonomy_exists( $name ) ) {

				$plugin      = $this->get_plugin();
				$text_domain = $plugin->get_slug();
				$tax_labels  = $this->get_labels();

				$labels = array(
					// The same as and overridden by $tax->label.
					'name'                       => $tax_labels['name'],

					// Default: Post Tag.
					'singular_name'              => $tax_labels['singular_name'],

					// Defaults to value of name label.
					'menu_name'                  => $tax_labels['menu_name'],

					// Default:  All Tags / All Categories.
					'all_items'                  => $tax_labels['all_items'],

					// Default: Add New Tag / Add New Category.
					'add_new_item'               => $tax_labels['add_new_item'],

					// Default: Edit Tag / Edit Category.
					'edit_item'                  => $tax_labels['edit_item'],

					// Default: View Tag / View Category.
					'view_item'                  => $tax_labels['view_item'],

					// Default: Update Tag / Update Category.
					'update_item'                => $tax_labels['update_item'],

					// Default: New Tag Name / New Category Name.
					'new_item_name'              => $tax_labels['new_item_name'],

					// This string is not used on non-hierarchical taxonomies such as post tags.
					// Default: null / Parent Category.
					'parent_item'                => $tax_labels['parent_item'],

					// The same as parent_item, but with colon : in the end
					// Default: null / Parent Category:.
					'parent_item_colon'          => $tax_labels['parent_item_colon'],

					// Default: Search Tags / Search Categories.
					'search_items'               => $tax_labels['search_items'],

					// This string is not used on hierarchical taxonomies.
					// Default: null / Popular Tags.
					'popular_items'              => $tax_labels['popular_items'],

					// Used in the taxonomy meta box.
					// This string is not used on hierarchical taxonomies.
					// Default: null / Separate tags with commas.
					'separate_items_with_commas' => $tax_labels['separate_items_with_commas'],

					// Used in the meta box when JavaScript is disabled.
					// This string is not used on hierarchical taxonomies.
					// Default: null / Add or remove tags.
					'add_or_remove_items'        => $tax_labels['add_or_remove_items'],

					// Used in the taxonomy meta box.
					// This string is not used on hierarchical taxonomies.
					// Default: null / Choose from the most used tags.
					'choose_from_most_used'      => $tax_labels['choose_from_most_used'],

					// (3.6+) - the text displayed via clicking 'Choose from the most used tags' in the taxonomy meta box when no tags are available
					// (4.2+) - the text used in the terms list table when there are no items for a taxonomy.
					// Default: No tags found / No categories found.
					'not_found'                  => $tax_labels['not_found'],
				);

				$args = array(

					// Labels - defined above.
					'labels'       => $labels,

					// Whether a taxonomy is intended for use publicly
					// either via the admin interface or by front-end users.
					// Default: true
					// 'public' => true,
					//
					// Whether the taxonomy is publicly queryable.
					// Default: $public.
					// 'publicly_queryable' => true,
					//
					// Whether to generate a default UI for managing this taxonomy.
					// 3.5+ setting this to false for attachment taxonomies will hide the UI.
					// Default: $public.
					// 'show_ui' => true,
					//
					// Where to show the taxonomy in the admin menu.
					// show_ui must be true.
					// Default: $show_ui.
					// 'show_in_menu' => true,
					//
					// Make this taxonomy available for selection in navigation menus.
					// Default: $public.
					// 'show_in_nav_menus' => true,
					//
					// Make this taxonomy available for selection in navigation menus.
					// Default: $public.
					// 'show_in_rest' => true,
					//
					// To change the base url of REST API route.
					// 'rest_base' => $tax_labels['slug'],
					//
					// REST API Controller class name.
					// 'rest_controller_class' => WP_REST_Terms_Controller,
					//
					// Whether to allow the Tag Cloud widget to use this taxonomy.
					// Default: $show_ui.
					// 'show_tagcloud' => true,
					//
					// 4.2+ Whether to show the taxonomy in the quick/bulk edit panel.
					// Default: $show_ui.
					// 'show_in_quick_edit' => true,
					//
					// 3.8+  Provide a callback function name for the meta box display.
					// No meta box is shown if set to false.
					// Default: null
					// 'meta_box_cb' => null,
					//
					// 3.5+  Whether to allow automatic creation of taxonomy columns on associated post-types table.
					// Default: false
					// 'show_admin_column' => false,
					//
					// Default: empty string.
					'description'  => $tax_labels['description'],

					// Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.
					// Default: false.
					'hierarchical' => true,

					// A function name that will be called when the count of an associated $object_type, such as post, is updated.
					// Works much like a hook.
					// Default: None - but see Note
					// @see https://codex.wordpress.org/Function_Reference/register_taxonomy
					// 'update_count_callback' => '_update_post_term_count',
					//
					// false = disable the query_var
					// string = use custom query_var instead of default which is $taxonomy
					// Default: $taxonomy
					// 'query_var' => true, // $tax_labels['slug'],
					//
					// Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks".
					// Pass an $args array to override default URL settings for permalinks as outlined below:
					// Default: true.
					'rewrite'      => array(

						/**
						 * Used as pretty permalink text (i.e. /tag/)
						 * Default: $taxonomy
						 * The full post permalink is specified when registering the Custom Post Type,
						 * (e.g. in the theme),
						 * see: https://gist.github.com/dotherightthing/3fa74207ffc4c3759cea47b1304e5273
						 *
						 * Note: if this slug matches the CPT slug, a 404 will result (POST not found)
						 * setting 'tourdiaries' fails (like CPT)
						 * setting 'tourdiaries/tours' fails (like CPT)
						 * setting 'tours' works with:
						 * /tours/east-asia/ AND /tourdiaries/east-asia/
						 *
						 * @todo /tours/east-asia/russia/ BUT NOT /tourdiaries/east-asia/russia/
						 * @todo #133
						 */
						'slug'         => $tax_labels['slug'],

						// Allows permalinks to be prepended with front base
						// Default: true
						// @see https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/.
						'with_front'   => false,

						// 3.1+ Allow hierarchical urls
						// Default: false.
						'hierarchical' => true,

						/**
						 * Assign an endpoint (EP) mask for this taxonomy.
						 * If you do not specify the EP_MASK, pretty permalinks will not work.
						 * If pretty permalinks are not enabled then endpoints are not going to work.
						 * This is because endpoints rely on WordPress’s internal rewrite system
						 * which is disabled for the default links.
						 *
						 * Endpoints make it easier to get the variable out of a URL when pretty permalinks are enabled.
						 *
						 * Using endpoints allows you to easily create rewrite rules to catch the normal WordPress URLs,
						 * but with a little extra at the end.
						 * For example, you could use an endpoint to match all post URLs followed by “gallery”
						 * and display all of the images used in a post, e.g. http://example.com/my-fantastic-post/gallery/.
						 *
						 * Note: resave permalinks or $wp_rewrite->flush_rules() once, after the taxonomy has been created.
						 *
						 * Default: EP_NONE
						 *
						 * @see https://make.wordpress.org/plugins/2012/06/07/rewrite-endpoints-api/
						 */
						// 'ep_mask' => EP_NONE,.
					),

					/**
					 * An array of the capabilities for this taxonomy.
					 * manage_terms / manage_categories
					 * edit_terms / manage_categories
					 * delete_terms / manage_categories
					 * assign_terms / edit_posts
					 * Default: None
					 */
					// 'capabilities' => None,.
					/**
					 * Whether this taxonomy should remember the order in which terms are added to objects.
					 * Default: None
					 */
					// 'sort' => None,.
					/**
					 * Whether this taxonomy is a native or "built-in" taxonomy.
					 * Do not edit.
					 * Default: false
					 */
					// '_builtin' => false,.
				);

				register_taxonomy(
					// Taxonomy Name should only contain lowercase letters and the underscore character,
					// and not be more than 32 characters long (database structure restriction).
					// Default: None.
					$name,
					// Object-types can be built-in Post Type or any Custom Post Type that may be registered.
					// Default: None.
					$tax_labels['posttype'],
					// Optional array of Arguments.
					// Default: None.
					$args
				);

				// if a custom post type.
				if ( 'post' !== $tax_labels['posttype'] ) {
					/**
					 * Better be safe than sorry when registering custom taxonomies for custom post types.
					 * Use register_taxonomy_for_object_type() right after the function to interconnect them.
					 * Else you could run into minetraps where the post type isn't attached inside filter callback
					 * that run during parse_request or pre_get_posts.
					 *
					 * Define the taxonomy first
					 * So we can piggyback the base URL
					 * tours/tourname/tourday/postname
					 * matching the WP Admin structure makes administration easier for clients
					 *
					 * @see https://codex.wordpress.org/Function_Reference/register_taxonomy#Usage
					 * @see https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
					 */
					register_taxonomy_for_object_type(
						// The name of the taxonomy.
						// Default: None.
						$name,
						// A name of the object type for the taxonomy object.
						// Default: None.
						$tax_labels['posttype']
					);
				}
			}
		}

		/**
		 * Method: get_taxonomy_hierarchy
		 *
		 * Recursively get taxonomy and its children.
		 *
		 * Parameters:
		 *   $post_id - Post ID
		 *   $taxonomy - Taxonomy name
		 *   $parent - Parent Term ID (0 == no parent / topmost term in hierarchy)
		 *
		 * Returns:
		 *   Multidimensional array preserving relationship between parents and children
		 *
		 * See:
		 * - Adapted from <https://www.daggerhart.com/wordpress-get-taxonomy-hierarchy-including-children/>
		 */
		public function get_taxonomy_hierarchy( int $post_id, string $taxonomy, int $parent = 0 ) : array {
			$taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;

			// Get all direct decendants of the $parent.
			// If parent => 0 is passed, only top-level terms will be returned.
			$terms = get_terms( array(
				'object_ids' => $post_id,
				'taxonomy'   => $taxonomy,
				'parent'     => $parent,
			) );

			if ( ! is_wp_error( $terms ) ) {
				// Prepare a new array, these are the children of $parent.
				// We'll ultimately copy all the $terms into this new array,
				// but only after they find their own children.
				$children = array();

				// go through all the direct decendants of $parent, and gather their children.
				foreach ( $terms as $term ) {
					// recurse to get the direct decendants of 'this' term.
					$term->children = $this->get_taxonomy_hierarchy( $post_id, $taxonomy, $term->term_id );

					// add the term to our new array.
					$children[ $term->term_id ] = $term;
				}

				// send the results back to the caller.
				return $children;
			}
		}

		/**
		 * Method: replace_taxonomy_in_cpt_permalinks
		 *
		 * Replace Taxonomy %placeholders% in Custom Post Type permalinks.
		 *
		 * Note:
		 * - This is required because taxonomy terms do not automatically appear in Custom Post Type permalinks.
		 * - The placeholder will be replaced by the hierarchical term selection (parent_term/child_term/grandchild_term).
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
		 *   'slug' => 'tourdiaries/%tours%/%wpdtrt_tourdates_cf_daynumber%'
		 *   'with_front' => false
		 * )
		 * ---
		 *
		 * See:
		 * - <https://codex.wordpress.org/Plugin_API/Filter_Reference/post_type_link>
		 * - <http://shibashake.com/wordpress-theme/add-custom-taxonomy-tags-to-your-wordpress-permalinks>
		 * - <http://shibashake.com/wordpress-theme/custom-post-type-permalinks-part-2#conflict>
		 * - <https://stackoverflow.com/questions/7723457/wordpress-custom-type-permalink-containing-taxonomy-slug>
		 * - <https://kellenmace.com/edit-slug-button-missing-in-wordpress/>
		 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/44> - Permalink Edit button missing
		 *
		 * TODO:
		 * - Make this less generic as this class is instantiated for EACH taxonomy
		 * - Replace manual 3 level check for children with a (while?) loop
		 */
		public function replace_taxonomy_in_cpt_permalinks( $permalink, $post, $leavename ) : string {

			// Get post.
			$post_id          = $post->ID;
			$placeholder_name = $this->get_name();

			// Permalink Settings = Post name.
			if ( strpos( $permalink, '%' . $placeholder_name . '%' ) === false ) {
				return $permalink;
			}

			// if taxonomy.
			if ( taxonomy_exists( $placeholder_name ) ) {

				/**
				 * Get the taxonomy terms related to the current post object
				 * wp_get_object_terms() doesn't cache the results but does implement a sort order
				 * get_the_terms() does cache the results but doesn't implement a sort order
				 *
				 * If a post only belongs to one parent, one child and/or one grandchild, you can order the terms by term_id.
				 * It is widely accepted that the parent will have a lower numbered ID than the child
				 * and the child will have a lower numbered ID than the grandchild.
				 * This wasn't true for me, as the term hierarchy was shuffled after creation,
				 * so this seems like a risky assumption.
				 *
				 * Returns Array of WP_Term objects on success
				 * Return false if there are no terms or the post does not exist
				 * Returns WP_Error on failure.
				 *
				 * @see https://wordpress.stackexchange.com/questions/172118/get-the-term-list-by-hierarchy-order
				 */
				$terms = $this->get_taxonomy_hierarchy(
					$post_id,
					$placeholder_name
				);

				if ( is_array( $terms ) ) {

					/**
					 * Retrieve the slug value of the first custom taxonomy object linked to the current post.
					 * If no terms are retrieved, then replace our term tag with the fallback value.
					 * This prevents // in permalink
					 */
					$replacements = array();

					if ( ! is_wp_error( $terms ) ) {

						foreach ( $terms as $term ) {
							$term_level_1 = $term;

							if ( ! empty( $term_level_1 ) && is_object( $term_level_1 ) ) {
								// level 1.
								$replacements[] = $term_level_1->slug;

								// level 2.
								if ( array_key_exists( 'children', $term_level_1 ) ) {
									$children         = array_keys( $term_level_1->children );
									$term_level_2_key = $children[0];
									$term_level_2     = $term_level_1->children[ $term_level_2_key ];
									$replacements[]   = $term_level_2->slug;

									// level 3.
									if ( array_key_exists( 'children', $term_level_2 ) ) {
										$children         = array_keys( $term_level_2->children );
										$term_level_3_key = $children[0];
										$term_level_3     = $term_level_2->children[ $term_level_3_key ];
										$replacements[]   = $term_level_3->slug;
									}
								}
							}
						}

						$replacements = implode( '/', $replacements );
					}

					/**
					 * Replace the %taxonomy% tag with our custom taxonomy slug.
					 * Permalink Settings = Post name.
					 */
					$permalink = str_replace( ( '%' . $placeholder_name . '%' ), $replacements, $permalink );
				}
			}

			return $permalink;
		}
	}
}
