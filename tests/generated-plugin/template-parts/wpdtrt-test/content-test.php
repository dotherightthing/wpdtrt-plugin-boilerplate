<?php
/**
 * Template to display plugin output in shortcodes and widgets
 *
 * @package   DTRT Test
 * @version   0.0.1
 * @since     0.7.0 DTRT WordPress Plugin Boilerplate Generator
 */

// Predeclare variables

// Internal WordPress arguments available to widgets
// This allows us to use the same template for shortcodes and front-end widgets
$before_widget = null; // register_sidebar
$before_title  = null; // register_sidebar
$title         = null;
$after_title   = null; // register_sidebar
$after_widget  = null; // register_sidebar

// shortcode options
$hide = null;

// access to plugin
$plugin = null;

// Options: display $args + widget $instance settings + access to plugin
$options = get_query_var( 'options' );

// Overwrite variables from array values
// @link http://kb.network.dan/php/wordpress/extract/
extract( $options, EXTR_IF_EXISTS );

// content between shortcode tags
if ( isset( $context ) ) {
	$content = $context->content;
} else {
	$content = '';
}

/**
 * filter_var
 * Return variable value if it passes the filter, otherwise return false
 * Note: "0" is falsy
 * @link http://stackoverflow.com/a/15075609
 * @link http://php.net/manual/en/function.filter-var.php
 * @link http://php.net/manual/en/language.types.boolean.php#112190
 * @link http://php.net/manual/en/language.types.boolean.php#118181
 */
$is_hidden = filter_var( $hide, FILTER_VALIDATE_BOOLEAN );

if ( $is_hidden ) {
	$state_classname = 'wpdtrt-test_hide';
} else {
	$state_classname = 'wpdtrt-test_show';
}

// WordPress widget options (not output with shortcode)
echo $before_widget;
echo $before_title . $title . $after_title;
?>

<span class="wpdtrt-test <?php echo $state_classname; ?>">
	<?php
		echo $content;
	?>
</span>

<?php
// output widget customisations (not output with shortcode)
echo $after_widget;
