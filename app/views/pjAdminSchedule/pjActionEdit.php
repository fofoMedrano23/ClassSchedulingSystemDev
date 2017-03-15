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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	?>
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('tabDetails');?></a></li>
			<li><a href="#tabs-2"><?php __('tabStudents');?></a></li>
		</ul>
		<div id="tabs-1">
			<?php
			pjUtil::printNotice(__('infoUpdateScheduleTitle', true), __('infoUpdateScheduleDesc', true));
			?>
			<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
			<div class="multilang"></div>
			<?php endif; ?>
			
			<div class="clear_both">
				<div class="form pj-form">
					<p>
						<label class="title"><?php __('lblClass'); ?></label>
						<span class="inline_block">
							<label class="block t5 bold"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['class_arr']['course_id'];?>"><?php echo $tpl['class_arr']['course']; ?></a></label>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblStartDate'); ?></label>
						<span class="inline_block">
							<label class="block t5 bold"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date'])); ?></label>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblEndDate'); ?></label>
						<span class="inline_block">
							<label class="block t5 bold"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['end_date'])); ?></label>
						</span>
					</p>
					<div class="float_left w400">
						<p>
							<label class="title"><?php __('lblRegisteredStudents'); ?></label>
							<span class="block overflow">
								<label class="block t5 float_left"><a href="#" class="pjCssStudentsTab"><?php echo $tpl['class_arr']['cnt_students']; ?></a> <?php __('lblOf');?> <?php echo $tpl['class_arr']['size']; ?></label>
							</span>
						</p>
					</div>
					<?php
					if(!empty($tpl['schedule_arr']))
					{ 
						?>
						<div class="float_right w300">
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="float_right" method="get" target="_blank">
								<input type="hidden" name="controller" value="pjAdminSchedule" />
								<input type="hidden" name="action" value="pjActionPrintClass" />
								<input type="hidden" name="class_id" value="<?php echo $_GET['id'];?>"/>
								<input type="submit" class="pj-button" value="<?php __('lblPrint'); ?>"/>
							</form>
							
							<input type="button" value="<?php __('btnEmail', false, true); ?>" class="pj-button float_right r10 pjCssEmailTeacher" data-id="<?php echo $_GET['id'];?>"/>
						</div>
						<?php
					} 
					?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionEdit&amp;id=<?php echo $_GET['id'];?>" method="post" id="frmEditSchedule" class="form pj-form" autocomplete="off">
					<input type="hidden" name="schedule_edit" value="1" />
					<input type="hidden" name="class_id" value="<?php echo $_GET['id'];?>" />
					
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
						
						<tbody>
							<?php
							if(!empty($tpl['schedule_arr']))
							{ 
								foreach($tpl['schedule_arr'] as $key => $schedule)
								{
									?>
									<tr data-id="<?php echo $schedule['id'];?>">
										<td>
											<input type="hidden" name="schedule_id[<?php echo $schedule['id']; ?>]" value="<?php echo $schedule['id']; ?>" />
											<span class="pj-form-field-custom pj-form-field-custom-after">
												<input type="text" name="date[<?php echo $schedule['id'];?>]" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($schedule['start_ts']));?>" class="pj-form-field pointer w80 required datepick" value="" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
												<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
											</span>
										</td>
										<td>
											<input name="start_time[<?php echo $schedule['id'];?>]" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($schedule['start_ts']));?>" class="pj-timepicker pj-form-field w70 required" data-type="start" data-index="<?php echo $schedule['id'];?>"/>
										</td>
										<td>
											<input name="end_time[<?php echo $schedule['id'];?>]" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($schedule['end_ts']));?>" class="pj-timepicker pj-form-field w70 required" data-type="end" data-index="<?php echo $schedule['id'];?>"/>
										</td>
										<td>
											<select name="teacher_id[<?php echo $schedule['id'];?>]" class="pj-form-field required w160">
												<option value="">-- <?php __('lblChoose'); ?>--</option>
												<?php
												foreach ($tpl['teacher_arr'] as $k => $v)
												{
													?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $schedule['teacher_id'] ? ' selected="selected"' : NULL;?>><?php echo pjSanitize::html( $v['name']); ?></option><?php
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
														<input type="text" name="i18n[<?php echo $v['id']; ?>][venue][<?php echo $schedule['id'];?>]"  value="<?php echo isset($schedule['i18n']) ? htmlspecialchars(stripslashes(@$schedule['i18n'][$v['id']]['venue'])) : NULL; ?>" class="pj-form-field w140<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
														<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
														<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
														<?php endif; ?>
													</span>
												</p>
												<?php
											} 
											?>
										</td>
										<?php
										if($key == 0)
										{ 
											?>
											<td>
												<a class="pj-table-icon-menu pj-table-button" href="#" data-id="{INDEX}"><span class="pj-button-arrow-down"></span></a>
												<span id="pj_menu_<?php echo $schedule['id'];?>" class="pj-menu-list-wrap" style="display: none;">
													<span class="pj-menu-list-arrow"></span>
													<ul class="pj-menu-list">
														<li><a href="#" data-index="<?php echo $schedule['id'];?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
														<li><a href="#" data-index="<?php echo $schedule['id'];?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
													</ul>
												</span>
											</td>
											<?php
										}else{
											?>
											<td class="align_center">
												<a href="#" class="pj-delete cpRemoveSchedule"></a>&nbsp;
												<a class="pj-table-icon-menu pj-table-button" href="#" data-id="{INDEX}"><span class="pj-button-arrow-down"></span></a>
												<span id="pj_menu_<?php echo $schedule['id'];?>" class="pj-menu-list-wrap" style="display: none;">
													<span class="pj-menu-list-arrow"></span>
													<ul class="pj-menu-list">
														<li><a href="#" data-index="<?php echo $schedule['id'];?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
														<li><a href="#" data-index="<?php echo $schedule['id'];?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
													</ul>
												</span>
											</td>
											<?php
										} 
										?>
									</tr>
									<?php
								}
							}else{
								$index = 'cp_' . rand(1, 999999);
								?>
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
										<a class="pj-table-icon-menu pj-table-button" href="#" data-id="{INDEX}"><span class="pj-button-arrow-down"></span></a>
										<span id="pj_menu_<?php echo $index;?>" class="pj-menu-list-wrap" style="display: none;">
											<span class="pj-menu-list-arrow"></span>
											<ul class="pj-menu-list">
												<li><a href="#" data-index="<?php echo $index;?>" data-period="day" class="lnkNext"><?php __('btnNextDay'); ?></a></li>
												<li><a href="#" data-index="<?php echo $index;?>" data-period="week" class="lnkNext"><?php __('btnNextWeek'); ?></a></li>
											</ul>
										</span>
									</td>
								</tr>
								<?php
							} 
							?>
						</tbody>
					</table>
					<input type="button" value="<?php __('btnPlusAdd'); ?>" class="pj-button b15 cpAddSchedule" />
					<p style="padding: 0px;">
						<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&action=pjActionIndex';" />
					</p>
				</form>
			</div>
		</div>
		<div id="tabs-2">
			<?php
			$title = __('infoClassStudentsTitle', true) . ', ' . $tpl['class_arr']['course'] . ', ' . date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date']));
			pjUtil::printNotice($title, __('infoClassStudentsDesc', true));
			?>
			<div class="b10">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
					<input type="hidden" name="controller" value="pjAdminBookings" />
					<input type="hidden" name="action" value="pjActionCreate" />
					<input type="hidden" name="class_id" value="<?php echo $_GET['id'];?>" />
					<input type="submit" class="pj-button" value="<?php __('btnAddBooking'); ?>" />
				</form>
				<form action="" method="get" class="float_left pj-form frm-student-filter">
					<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
				</form>
				
				<div class="float_right">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" target="_blank">
						<input type="hidden" name="controller" value="pjAdminSchedule" />
						<input type="hidden" name="action" value="pjActionPrintStudents" />
						<input type="hidden" name="class_id" value="<?php echo $_GET['id'];?>"/>
						<input type="submit" class="pj-button" value="<?php __('lblPrint'); ?>"/>
					</form>
				</div>
				<input type="button" value="<?php __('btnEmail', false, true); ?>" class="pj-button float_right r10 pjCssEmailStudent" data-id="<?php echo $_GET['id'];?>"/>
				<br class="clear_both" />
			</div>
			<div id="student_grid"></div>
		</div>
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
	
	<div id="dialogEmailTeacher" title="<?php __('send_class_schedule_to_teachers'); ?>" style="display: none"></div>
	<div id="dialogEmailStudent" title="<?php __('send_student_list_to_teachers'); ?>" style="display: none"></div>
	
	<table id="tblTeacherClone" style="display: none">
		<tbody>
			<tr data-id="{INDEX}">
				<tr data-id="{INDEX}">
					<td><input name="name[{INDEX}]" class="pj-form-field w150 required" data-index="{INDEX}"/></td>
					<td><input name="email[{INDEX}]" class="pj-form-field w300 email required" data-index="{INDEX}"/></td>
					<td class="align_center"><a href="#" class="pj-delete cpRemoveRecipient"></a></td>
				</tr>
			</tr>
		</tbody>
	</table>
	
	<table id="tblStudentClone" style="display: none">
		<tbody>
			<tr data-id="{INDEX}">
				<tr data-id="{INDEX}">
					<td><input name="name[{INDEX}]" class="pj-form-field w150 required" data-index="{INDEX}"/></td>
					<td><input name="email[{INDEX}]" class="pj-form-field w300 email required" data-index="{INDEX}"/></td>
					<td class="align_center"><a href="#" class="pj-delete cpRemoveStudent"></a></td>
				</tr>
			</tr>
		</tbody>
	</table>
	
	<?php
	$show_period = 'false';
	if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
	{
		$show_period = 'true';
	}
	$bs = __('booking_statuses', true);
	?>
	<script type="text/javascript">
	
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
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['id']) && (int) $_GET['id'] > 0)
	{
		?>pjGrid.queryString += "&id=<?php echo (int) $_GET['id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName', false, true); ?>";
	myLabel.email = "<?php __('email', false, true); ?>";
	myLabel.phone = "<?php __('lblPhone', false, true); ?>";
	myLabel.deposit_paid = "<?php __('lblDepositPaid', false, true); ?>";
	myLabel.status = "<?php __('lblStatus', false, true); ?>";
	myLabel.pending = "<?php echo $bs['pending']; ?>";
	myLabel.confirmed = "<?php echo $bs['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $bs['cancelled']; ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>