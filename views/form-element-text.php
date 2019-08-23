<?php
/**
 * File: views/form-element-text.php
 *
 * Template partial for Text fields.
 *
 * Example
 *
 * --- PHP
 *  'fieldname' => array(
 *   'type' => 'text',
 *   label' => __('Field label', 'text-domain'),
 *   'size' => 10,
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
	<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $size ); ?>" class="<?php echo esc_attr( $classname ); ?>" aria-describedby="<?php echo esc_attr( $id ); ?>-tip">
	<<?php echo $tip_element; ?> class="description" id="<?php echo esc_attr( $id ); ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
