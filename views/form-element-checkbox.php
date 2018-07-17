<?php
/**
 * Template partial for Checkbox fields.
 *
 * @package WPDTRT_Plugin_Boilerplate
 * @version 1.0.0
 * @since   1.0.0
 * @uses    WordPress_Admin_Style
 * @example
 * 'fieldname' => array(
 *   'type' => 'checkbox',
 *   'label' => esc_html__('Field label', 'text-domain'),
 *   tip' => __('Helper text', 'text-domain')
 * )
 */

echo $label_start; // phpcs:ignore ?>
	<label for="<?php echo esc_attr( $id ); // phpcs:ignore ?>"><?php echo esc_html( $label ); // phpcs:ignore ?>:</label>
<?php echo $label_end; // phpcs:ignore ?>

<?php echo $field_start; // phpcs:ignore ?>
	<input type="checkbox" name="<?php echo esc_attr( $name ); // phpcs:ignore ?>" id="<?php echo esc_attr( $id ); // phpcs:ignore ?>" value="1" aria-describedby="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip" <?php checked( $value, '1', true ); // phpcs:ignore ?>>
	<<?php echo $tip_element; // phpcs:ignore ?> class="description" id="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip">
		<?php echo $tip; // phpcs:ignore ?>
	</<?php echo $tip_element; // phpcs:ignore ?>>
<?php echo $field_end; // phpcs:ignore ?>
