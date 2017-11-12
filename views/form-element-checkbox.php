<?php
/**
 * Template partial for Checkbox fields.
 *
 * @package   	WPPlugin
 * @version   	1.0.0
 * @since 		1.0.0
 */

?>

<?php echo $label_start; ?>
	<label for="<?php echo $id; ?>"><?php echo $label; ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="1" aria-describedby="<?php echo $id; ?>-tip" <?php checked( $value, '1', true ); ?>>
	<<?php echo $tip_element; ?> class="description" id="<?php echo $id; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
