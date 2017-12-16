<?php
/**
 * Template partial for File fields.
 *
 * @package   	WPPlugin
 * @version   	1.0.0
 * @since 		1.0.1
 * @uses        WordPress_Admin_Style
 * @example
 * 	'fieldname' => array(
 * 		'type' => 'file',
 * 		'label' => __('Field label', 'text-domain'),
 * 		'tip' => __('Helper text', 'text-domain')
 * 	)
 */

?>

<?php echo $label_start; ?>
	<label for="<?php echo $id; ?>"><?php echo $label; ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<input type="file" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" class="<?php echo $classname; ?>" aria-describedby="<?php echo $id; ?>-tip">
	<<?php echo $tip_element; ?> class="description" id="<?php echo $id; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
