<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	pjUtil::printNotice(__('infoUpdateScheduleTitle', true), __('infoUpdateScheduleDesc', true));
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionUpdate" method="post" id="frmUpdateSchedule" class="form pj-form" autocomplete="off">
			<input type="hidden" name="schedule_update" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			
			<p>
				<label class="title"><?php __('lblClass'); ?></label>
				<span class="inline_block">
					<select name="class_id" id="class_id" class="pj-form-field required w300">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['class_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['class_id'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblTeacher'); ?></label>
				<span class="inline_block">
					<select name="teacher_id" id="teacher_id" class="pj-form-field required w300">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['teacher_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['teacher_id'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html( $v['name']); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblDate'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date" class="pj-form-field pointer w100 required datepick" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['start_ts']));?>" readonly="readonly" data-date="" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblStartTime'); ?></label>
				<span class="inline_block">
					<input type="text" name="start_time" class="pj-timepicker pj-form-field w80 required" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['start_ts']));?>" readonly="readonly" data-date="" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblEndTime'); ?></label>
				<span class="inline_block">
					<input type="text" name="end_time" class="pj-timepicker pj-form-field w80 required" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['end_ts']));?>" readonly="readonly" data-date="" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
				</span>
			</p>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblVenue'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][venue]" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['venue'])); ?>" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&action=pjActionIndex';" />
			</p>
		</form>
	</div>
	
	<div id="dialogDuplicate" style="display: none" title="<?php __('lblAvailabilityCheck');?>"></div>
	
	<?php
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>