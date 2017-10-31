<?php
/**
 * Template partial for Select fields.
 *
 * @package     WPDTRT_Attachment_Map
 * @subpackage  WPDTRT_Attachment_Map/templates
 * @since 		0.6.0
 * @version 	1.0.0
 */

?>

<tr>
	<th scope="row">
		<label for="<?php echo $name; ?>"><?php echo $label; ?>:</label>
	</th>
	<td>
		<!-- value="<?php echo $value; ?>"-->
		<select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="regular-text">
			<option value="null">Please select an option</option>
			<?php foreach( $options as $name => $attributes ): ?>
			<option value="<?php echo $name; ?>"<?php echo $attributes['selected'] ? ' selected' : ''; ?>>
				<?php echo $attributes['text']; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php if ( isset($tip) ): ?>
		<p class="description"><?php echo $tip; ?></p>
		<?php endif; ?>
	</td>
</tr>
