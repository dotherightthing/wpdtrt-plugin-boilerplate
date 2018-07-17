<?php
/**
 * Template partial for Text fields.
 *
 * @package WPDTRT_Plugin_Boilerplate
 * @version 1.0.0
 * @since   1.0.0
 * @uses    WordPress_Admin_Style
 * @example
 *  'fieldname' => array(
 *   'type' => 'text',
 *   label' => __('Field label', 'text-domain'),
 *   'size' => 10,
 *   tip' => __('Helper text', 'text-domain')
 * )
 */

echo $label_start; // phpcs:ignore ?>
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); // phpcs:ignore ?>:</label>
<?php echo $label_end; // phpcs:ignore ?>

<?php echo $field_start; // phpcs:ignore ?>
	<input type="text" name="<?php echo esc_attr( $name ); // phpcs:ignore ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); // phpcs:ignore ?>" size="<?php echo esc_attr( $size ); // phpcs:ignore ?>" class="<?php echo esc_attr( $classname ); // phpcs:ignore ?>" aria-describedby="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip">
	<<?php echo $tip_element; // phpcs:ignore ?> class="description" id="<?php echo esc_attr( $id ); // phpcs:ignore ?>-tip">
		<?php echo $tip; // phpcs:ignore ?>
	</<?php echo $tip_element; // phpcs:ignore ?>>
<?php echo $field_end; // phpcs:ignore ?>
