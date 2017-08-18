<?php
$existing_custom_dimensions = $gawd_client->get_custom_dimensions('default');
if (!is_array($existing_custom_dimensions)) {
    $existing_custom_dimensions = array();
}
$tracking_dimensions = $gawd_client->get_custom_dimensions_tracking();
$supported_dimensions = array("Logged in","Post type","Author","Category","Tags", "Published Month", "Published Year");
?>
<div class="dimension_wrapper">
	<form action="" method="post" id="gawd_dimensions_form">
		<div class="gawd_dimension_row">
			<span class="gawd_dimension_label">Name</span>
			<span class="gawd_dimension_input">
				<select name="gawd_custom_dimension_name" id="gawd_custom_dimension_name">
                    <?php
                    foreach ($supported_dimensions as $supported_dimension) {
                        $disabled = '';
                        if (is_array($tracking_dimensions) && !empty($tracking_dimensions)) {
                            foreach ($tracking_dimensions as $tracking_dimension) {
                                if ($supported_dimension == $tracking_dimension['name']) {
                                    $disabled = 'disabled="disabled"';
                                    break;
                                }
                            }
                        }
                        ?>
						<option value="<?php echo $supported_dimension; ?>" <?php echo $disabled; ?>><?php echo $supported_dimension; ?></option>
					<?php } ?>	
				</select>
			</span>
      <div class="gawd_info" title="Pick a name for your custom dimension."></div>
			<div class="clear"></div>
		</div>
		<div class="gawd_dimension_row">
			<span class="gawd_dimension_label">Scope</span>
			<span class="gawd_dimension_input">
				<select name="gawd_custom_dimension_scope" id="gawd_custom_dimension_scope">
					<option value="Hit">Hit</option>
					<option value="Session">Session</option>
					<option value="User">User</option>
					<option value="Product">Product</option>
				</select>
			</span>
      <div class="gawd_info" title="Select a scope, hit, session, user or product."></div>
			<div class="clear"></div>
		</div>
		<div class="gawd_dimension_row">
			<div class="onoffswitch">
				<input type="checkbox" name="gawd_custom_dimension_tracking" class="onoffswitch-checkbox" id="gawd_custom_dimension_tracking" checked>
				<label class="onoffswitch-label" for="gawd_custom_dimension_tracking">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<div class="gawd_info" title="Enable this option to track relevant activity and view statistics based on this custom dimension on Reports page."></div>
			<div class="onoffswitch_text">
				Tracking for this custom dimension
			</div>
			<div class="clear"></div>
		</div>
		<div class="gawd_buttons" id="goal_submit">
			<input class="button_gawd" type="submit" value="Save"/>
			<input name="gawd_custom_dimension_id" type="hidden" value="<?php echo count($existing_custom_dimensions);?>"/>
			<div class="clear"></div>
		</div>
     <?php wp_nonce_field('gawd_save_form', 'gawd_save_form_fild'); ?>
	</form>
    <?php if (!empty($existing_custom_dimensions)) { ?>
		<table border="1" class="gawd_table">
			<tr>
				<th>Name</th>
				<th>Id</th>
			</tr>
			<?php foreach($existing_custom_dimensions as $existing_custom_dimension) { ?>
				<tr>
					<td><?php echo $existing_custom_dimension['name']; ?></td>
					<td><?php echo substr($existing_custom_dimension['id'],-1); ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
</div>
