<?php
/**
 * Template partial for Checkbox fields.
 *
 * @example
 * 	<?php
 * 	echo $this->render_form_element( array(
 * 	  'type' => 'checkbox',
 * 	  'name' => $this->get_field_name('title'),
 * 	  'label' => esc_html__('Title', 'text-domain'),
 * 	  'tip' => esc_html__('A helpful tip', 'text-domain'),
 * 	) );
 *  ?>
 *
 * @package     WPDTRT_Attachment_Map
 * @subpackage  WPDTRT_Attachment_Map/templates
 * @since 		0.6.0
 * @version 	1.0.0
 */

?>

<?php echo $label_start; ?>
	<label for="<?php echo $id; ?>"><?php echo $label; ?>:</label>
<?php echo $label_end; ?>

<?php echo $field_start; ?>
	<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="1" aria-describedby="<?php echo $name; ?>-tip" <?php checked( $value, '1', true ); ?>>
	<<?php echo $tip_element; ?> class="description" id="<?php echo $name; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
