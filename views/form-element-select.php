<?php
/**
 * Template partial for Select fields.
 *
 * @package WPDTRT_Plugin_Boilerplate
 * @version 1.0.0
 * @since   1.0.0
 * @uses    WordPress_Admin_Style
 * @example
 * 'fieldname' => array(
 *   'type' => 'select',
 *   label' => __('Field label', 'fieldname'),
 *   'options' => array(
 *      'option1value' => array(
 *      'text' => __('Label for option 1', 'text-domain')
 *   ),
 *   'option2value' => array(
 *      'text' => __('Label for option 2', 'text-domain')
 *      )
 *   ),
 *   tip' => __('Helper text', 'text-domain')
 * )
 */

echo $label_start; // phpcs:ignore ?>
	<label for="<?php echo esc_attr( $id ); // phpcs:ignore ?>"><?php echo esc_html( $label ); // phpcs:ignore ?>:</label>
<?php echo $label_end; // phpcs:ignore ?>

<?php echo $field_start; // phpcs:ignore ?>
	<select name="<?php echo esc_attr( $name ); // phpcs:ignore ?>" id="<?php echo esc_attr( $id ); // phpcs:ignore ?>" id="<?php echo esc_attr( $name ); // phpcs:ignore ?>" class="regular-text" aria-describedby="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip">
		<option value="null"<?php echo ( null === $value ) ? ' selected' : ''; // phpcs:ignore ?>>Please select an option</option>
		<?php foreach ( $options as $name => $attributes ) : ?>
		<option value="<?php echo esc_attr( $name ); // phpcs:ignore ?>"<?php echo ( $value === $name ) ? ' selected' : ''; // phpcs:ignore ?>>
			<?php echo esc_html( $attributes['text'] ); // phpcs:ignore ?>
		</option>
		<?php endforeach; ?>
	</select>
	<<?php echo $tip_element; // phpcs:ignore ?> class="description" id="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip">
		<?php echo $tip; // phpcs:ignore ?>
	</<?php echo $tip_element; // phpcs:ignore ?>>
<?php echo $field_end; // phpcs:ignore ?>
