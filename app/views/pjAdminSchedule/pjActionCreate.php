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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	pjUtil::printNotice(__('infoManageScheduleTitle', true), __('infoManageScheduleDesc', true));
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionCreate" method="post" id="frmCreateSchedule" class="form pj-form" autocomplete="off">
			<input type="hidden" name="schedule_create" value="1" />
			
			<p>
				<label class="title"><?php __('lblClass'); ?></label>
				<span class="inline_block">
					<span class="block float_left r5">
						<select name="class_id" id="class_id" class="pj-form-field required w300">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach ($tpl['class_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['class_id']) ? ($_GET['class_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL;?>><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
							}
							?>
						</select>
					</span>
					<a id="pjCssEditClass" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionEdit&id={ID}" class="pj-edit" style="display:none;"></a>
				</span>
			</p>
			<table class="pj-table b15" id="tblSchedule" cellpadding="0" cellspacing="0" style="width: 100%;">
				<thead>
					<tr>
						<th width="130"><?php __('lblDate'); ?></th>
						<th width="80"><?php __('lblStartTime'); ?></th>
						<th width="80"><?php __('lblEndTime'); ?></th>
						<th width="160"><?php __('lblTeacher'); ?></th>
						<th width="150"><?php __('lblVenue'); ?></th>
						<th width="60">&nbsp;</th>
					</tr>
				</thead>
				<?php
				$index = 'cp_' . rand(1, 999999);
				?>
				<tbody>
					<tr>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-after">
								<input type="text" name="date[<?php echo $index;?>]" class="pj-form-field pointer w80 required datepick dateClone" value="" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</td>
						<td>
							<input name="start_time[<?php echo $index;?>]" class="pj-timepicker pj-form-field w70 required" data-type="start" data-index="<?php echo $index;?>"/>
						</td>
						<td>
							<input name="end_time[<?php echo $index;?>]" class="pj-timepicker pj-form-field w70 required" data-type="end" data-index="<?php echo $index;?>"/>
						</td>
						<td>
							<select name="teacher_id[<?php echo $index;?>]" class="pj-form-field required w160">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['teacher_arr'] as $k => $v)
								{
									?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html( $v['name']); ?></option><?php
								}
								?>
							</select>
						</td>
						<td>
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<span class="inline_block">
										<input type="text" name="i18n[<?php echo $v['id']; ?>][venue][<?php echo $index;?>]" class="pj-form-field w140<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
										<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
										<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif; ?>
									</span>
								</p>
								<?php
							} 
							?>
						</td>
						<td>
							<a class="pj-table-icon-menu pj-table-button" href="#" data-id="<?php echo $index;?>"><span class="pj-button-arrow-down"></span></a>
							<span id="pj_menu_<?php echo $index;?>" class="pj-menu-list-wrap" style="display: none;">
								<span class="pj-menu-list-arrow"></span>
								<ul class="pj-menu-list">
									<li><a href="#" data-index="<?php echo $index;?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
									<li><a href="#" data-index="<?php echo $index;?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
								</ul>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="button" value="<?php __('btnPlusAdd'); ?>" class="pj-button b15 cpAddSchedule" />
			<p style="padding: 0px;">
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&action=pjActionIndex';" />
			</p>
		</form>
	</div>
	
	<table id="tblScheduleClone" style="display: none">
		<tbody>
			<tr data-id="{INDEX}">
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date[{INDEX}]" class="pj-form-field pointer w80 required datepick dateClone" value="" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<input name="start_time[{INDEX}]" class="pj-timepicker pj-form-field w70 required" data-type="start" data-index="{INDEX}"/>
				</td>
				<td>
					<input name="end_time[{INDEX}]" class="pj-timepicker pj-form-field w70 required" data-type="end" data-index="{INDEX}"/>
				</td>
				<td>
					<select name="teacher_id[{INDEX}]" class="pj-form-field required w160">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['teacher_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html( $v['name']); ?></option><?php
						}
						?>
					</select>
				</td>
				<td>
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][venue][{INDEX}]" class="pj-form-field w140<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
								<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
							</span>
						</p>
						<?php
					} 
					?>
				</td>
				<td class="align_center">
					<a href="#" class="pj-delete cpRemoveSchedule"></a>&nbsp;
					<a class="pj-table-icon-menu pj-table-button" href="#" data-id="{INDEX}"><span class="pj-button-arrow-down"></span></a>
					<span id="pj_menu_{INDEX}" class="pj-menu-list-wrap" style="display: none;">
						<span class="pj-menu-list-arrow"></span>
						<ul class="pj-menu-list">
							<li><a href="#" data-index="{INDEX}" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
							<li><a href="#" data-index="{INDEX}" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
						</ul>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
	
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