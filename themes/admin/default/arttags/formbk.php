<h2><?php echo lang('kb_categories'); ?></h2>
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
			<td class="row2"><label for="tag_parent"><?php echo lang('kb_parent_cat'); ?>:</label></td>
			<td class="row2">
			
				<select tabindex="4" name="tag_parent" id="tag_parent">
					<option value="0"><?php echo lang('kb_no_parent'); ?></option>
					<?php foreach($options as $row): ?>
					<?php $default = ((isset($art->tag_parent) && $art->tag_parent == $row['tag_id'])) ? true : false; ?>
					<option value="<?php echo $row['tag_id']; ?>" <?php echo set_select('tag_parent', $row['tag_id'], $default); ?>><?php echo $row['tag_name']; ?></option>
					<?php endforeach; ?>
				</select>
			
			</td>
		</tr>
		<tr>
			<td class="row1"><label for="tag_order"><?php echo lang('kb_weight'); ?>:</label></td>
			<td class="row1">
				<input tabindex="5" type="text" name="tag_order" id="tag_order" value="<?php echo (isset($art->tag_order)) ? set_value('tag_order', $art->tag_order) : set_value('tag_order'); ?>" />
				<a href="javascript:void(0);" title="<?php echo lang('kb_weight_desc'); ?>" class="tooltip">
				<img src="<?php echo base_url(); ?>images/help.png" border="0" alt="<?php echo lang('kb_edit'); ?>" />	
				</a>
			</td>
		</tr>
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
	
	<p><input type="submit" tabindex="7" name="submit" class="save" value="<?php echo lang('kb_save'); ?>" /></p>
	
	<input type="hidden" name="tag_id" value="<?php echo (isset($art->tag_id)) ? $art->tag_id : ''; ?>" />
	<?php echo form_close(); ?>
	
	<div class="clear"></div>
</div>