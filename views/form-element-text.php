<?php
/**
 * Template partial for Text fields.
 *
 * @package     WPPlugin
 * @since 		0.6.0
 * @version 	1.0.0
 */

?>

<?php echo $label_start; ?>
	<label for="<?php echo $id; ?>"><?php echo $label; ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" class="<?php echo $classname; ?>" aria-describedby="<?php echo $id; ?>-tip">
	<<?php echo $tip_element; ?> class="description" id="<?php echo $id; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
