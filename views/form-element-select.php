<?php
/**
 * Template partial for Select fields.
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
	<select name="<?php echo $name; ?>" id="<?php echo $id; ?>" id="<?php echo $name; ?>" class="regular-text" aria-describedby="<?php echo $id; ?>-tip">
		<option value="null"<?php echo ( $value === null ) ? ' selected' : ''; ?>>Please select an option</option>
		<?php foreach( $options as $name => $attributes ): ?>
		<option value="<?php echo $name; ?>"<?php echo ( $value === $name ) ? ' selected' : ''; ?>>
			<?php echo $attributes['text']; ?>
		</option>
		<?php endforeach; ?>
	</select>
	<<?php echo $tip_element; ?> class="description" id="<?php echo $id; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
