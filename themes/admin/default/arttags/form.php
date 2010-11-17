<h2>Arttags</h2>
<div id="form" class="wrap">
	<?php if(validation_errors()) {
		echo '<div class="error">'.validation_errors().'</div>';
	} ?>
	<form method="post" action="<?php echo $action; ?>" class="searchform">
		
		<p class="row1">
			<label for="tag_name"><?php echo lang('kb_title'); ?>: <em>(<?php echo lang('kb_required'); ?>)</em></label>
			<input tabindex="1" type="text" class="inputtext" name="tag_name" id="tag_name" value="<?php echo (isset($art->tag_name)) ? set_value('tag_name', $art->tag_name) : set_value('tag_name'); ?>" />
		</p>
		<p class="row2">
			<label for="tag_uri"><?php echo lang('kb_uri'); ?>:</label></td>
			<input tabindex="2" type="text" class="inputtext" name="tag_uri" id="tag_uri" value="<?php echo (isset($art->tag_uri)) ? set_value('tag_uri', $art->tag_uri) : set_value('tag_uri'); ?>" />
		</p>
		
		<p class="row1">
			<label for="tag_description"><?php echo lang('kb_description'); ?>:</label>
			<textarea tabindex="3" id="editcontent" name="tag_description" id="tag_description" cols="15" rows="15" class="inputtext"><?php echo (isset($art->tag_description)) ? set_value('tag_description', $art->tag_description) : set_value('tag_description'); ?></textarea>
		</p>
		
		<?php $this->core_events->trigger('arttag/form');?>
		
	<table width="100%" cellspacing="0">
		<tr>
			<td class="row2"><label for="tag_display"><?php echo lang('kb_display'); ?>:</label></td>
			<td class="row2">
				<select tabindex="6" name="tag_display" id="tag_display">
					<option value="Y"<?php if(isset($art->tag_display) && $art->tag_display == 'Y') echo ' selected'; ?>><?php echo lang('kb_yes'); ?></option>
					<option value="N"<?php if(isset($art->tag_display) && $art->tag_display == 'N') echo ' selected'; ?>><?php echo lang('kb_no'); ?></option>
				</select>
			</td>
		</tr>
		<?php $this->core_events->trigger('arttags/form', (isset($art->tag_id)) ? $art->tag_id : ''); ?>
	</table>
	<input type="hidden" name="tag_parent" id="tag_parent" value="0"/>
	<input type="hidden" name="tag_order" id="tag_order" value="0"/>
	<p><input type="submit" tabindex="7" name="submit" class="save" value="<?php echo lang('kb_save'); ?>" /></p>
	
	<input type="hidden" name="tag_id" value="<?php echo (isset($art->tag_id)) ? $art->tag_id : ''; ?>" />
	<?php echo form_close(); ?>
	
	<div class="clear"></div>
</div>