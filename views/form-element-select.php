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

echo $label_start; ?>
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $name ); ?>" class="regular-text" aria-describedby="<?php echo esc_attr( $id ); ?>-tip">
		<option value="null"<?php echo ( null === $value ) ? ' selected' : ''; ?>>Please select an option</option>
		<?php foreach ( $options as $name => $attributes ) : ?>
		<option value="<?php echo esc_attr( $name ); ?>"<?php echo ( $value === $name ) ? ' selected' : ''; ?>>
			<?php echo esc_html( $attributes['text'] ); ?>
		</option>
		<?php endforeach; ?>
	</select>
	<<?php echo $tip_element; ?> class="description" id="<?php echo esc_attr( $id ); ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
