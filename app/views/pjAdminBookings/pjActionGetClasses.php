<select name="class_id" id="class_id" class="pj-form-field required w300" data-msg-required="<?php __('pj_field_required');?>">
	<option value="">-- <?php __('lblChoose'); ?>--</option>
	<?php
	if(isset($tpl['class_arr']))
	{
		foreach ($tpl['class_arr'] as $k => $v)
		{
			?><option value="<?php echo $v['id']; ?>"><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
		}
	}
	?>
</select>