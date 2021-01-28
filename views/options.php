<?php
/**
 * File: views/options.php
 *
 * Template partial for Admin Options page.
 *
 * Note:
 * - WP Admin > Settings > PluginName.
 */

$plugin_options              = $this->get_plugin_options();
$form_submitted              = ( $this->helper_options_saved() === true );
$plugin_version              = $this->get_version();
$plugin_title                = $this->get_developer_prefix() . ' ' . $this->get_menu_title();
$plugin_data_length          = $this->get_plugin_data_length();
$demo_date_last_updated_date = $this->render_last_updated_humanised() ? $this->render_last_updated_humanised() : '';
$messages                    = $this->get_messages();
$options_form_title          = $messages['options_form_title'];
$options_form_description    = $messages['options_form_description'];
$no_options_form_description = $messages['no_options_form_description'];
$options_form_submit         = $messages['options_form_submit'];
$demo_shortcode_params       = $this->get_demo_shortcode_params();
$demo_display                = isset( $demo_shortcode_params ) && ( $form_submitted || ( '' !== $demo_date_last_updated_date ) );
$demo_shortcode              = $demo_display ? $this->helper_build_demo_shortcode() : '';
$demo_data_maxlength         = $demo_shortcode_params ? $demo_shortcode_params['number'] : 0;
$noscript_warning            = $messages['noscript_warning'];

if ( $demo_display ) {
	$demo_sample_title          = $messages['demo_sample_title'];
	$demo_data_title            = $messages['demo_data_title'];
	$demo_shortcode_title       = $messages['demo_shortcode_title'];
	$demo_data_description      = $messages['demo_data_description'];
	$demo_data_length           = str_replace( '#', $plugin_data_length, $messages['demo_data_length'] );
	$demo_data_displayed_length = str_replace( '#', $demo_data_maxlength, $messages['demo_data_displayed_length'] );
	$demo_date_last_updated     = $messages['demo_date_last_updated'];
}
?>

<div class="wrap wpdtrt-plugin-boilerplate__options">

	<div id="icon-options-general" class="icon32"></div>
		<h1>
			<?php echo esc_html( $plugin_title ); ?>
			<span class="wpdtrt-scss-plugin-version">
				<?php echo esc_html( $plugin_version ); ?>
			</span>
		</h1>
		<noscript>
			<div class="notice notice-warning">
				<p>
					<?php echo esc_html( $noscript_warning ); ?>
				</p>
			</div>
		</noscript>

		<form name="data_form" method="post" action="">

			<?php // hidden field is used by options_saved(). ?>
			<input type="hidden" name="wpdtrt_plugin_boilerplate_form_submitted" value="Y" />

			<h2 class="title">
				<?php echo esc_html( $options_form_title ); ?>
			</h2>
			<p>
				<?php
				if ( ! empty( $plugin_options ) ) {
					echo esc_html( $options_form_description );
				} else {
					echo esc_html( $no_options_form_description );
				}
				?>
			</p>

			<fieldset>
				<legend class="screen-reader-text">
					<span>
						<?php echo esc_html( $options_form_title ); ?>
					</span>
				</legend>
				<table class="form-table">
					<tbody>
						<?php
						foreach ( $plugin_options as $name => $attributes ) {
							echo $this->render_form_element( $name, $attributes );
						}
						?>
					</tbody>
				</table>
			</fieldset>

			<?php
			if ( ! empty( $plugin_options ) ) {
				submit_button(
					$options_form_submit, // $text.
					'primary', // $type.
					true, // $wrap in paragraph.
					null // $other_attributes.
				);
			}
			?>

		</form>

		<?php
		if ( $demo_display ) :
			?>

		<h2>
			<span>
				<?php echo esc_html( $demo_data_title ); ?>
			</span>
		</h2>

		<p>
			<?php echo esc_html( $demo_shortcode_title ); ?>:
			<code>
				<?php echo esc_html( $demo_shortcode ); ?>
			</code>
		</p>

		<p>
			<?php echo esc_html( $demo_data_length ); ?>.
		</p>

		<p>
			<?php echo esc_html( $demo_data_displayed_length ); ?>:
		</p>

		<div class="wpdtrt-plugin-boilerplate-ajax-response wpdtrt-scss-plugin-ajax-response" data-format="ui"></div>

			<h2>
				<span>
					<?php echo esc_html( $demo_data_title ); ?>
				</span>
			</h2>

			<p>
				<?php echo esc_html( $demo_data_description ); ?>.
			</p>

			<div class="wpdtrt-plugin-boilerplate-ajax-response wpdtrt-scss-plugin-ajax-response" data-format="data"></div>

			<p class="wpdtrt-scss-plugin-date">
				<em>
					<?php echo esc_html( $demo_date_last_updated . ': ' . $demo_date_last_updated_date ); ?>
				</em>
			</p>

			<?php
			endif;
		?>
	</div>
	<!-- .wrap -->
