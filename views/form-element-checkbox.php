<?php
/**
 * File: views/form-element-checkbox.php
 *
 * Template partial for Checkbox fields.
 *
 * Example
 * --- PHP
 * 'fieldname' => array(
 *   'type' => 'checkbox',
 *   'label' => esc_html__('Field label', 'text-domain'),
 *   tip' => __('Helper text', 'text-domain')
 * )
 * ---
 *
 * @package WPDTRT_Plugin_Boilerplate
 */

echo $label_start; ?>
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="1" aria-describedby="<?php echo esc_attr( $id ); ?>-tip" <?php checked( $value, '1', true ); ?>>
	<<?php echo $tip_element; ?> class="description" id="<?php echo esc_attr( $id ); ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
