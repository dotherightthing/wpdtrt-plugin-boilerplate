<?php
/**
 * Template partial for Password fields.
 *
 * @example
 * 	<?php
 * 	echo $this->render_form_element( array(
 * 	  'type' => 'password',
 * 	  'name' => $this->get_field_name('title'),
 * 	  'size' => 10,
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
	<input type="password" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" size="<?php echo $size; ?>" class="<?php echo $classname; ?>" aria-describedby="<?php echo $name; ?>-tip">
	<<?php echo $tip_element; ?> class="description" id="<?php echo $name; ?>-tip">
		<?php echo $tip; ?>
	</<?php echo $tip_element; ?>>
<?php echo $field_end; ?>
