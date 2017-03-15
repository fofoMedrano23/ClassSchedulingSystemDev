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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		$bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$bodies[$_GET['err']]);
		pjUtil::printNotice(@$titles[$_GET['err']], $bodies_text);
	}
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	?>
	
	<?php pjUtil::printNotice(__('infoUpdateCourseTitle', true, false), __('infoUpdateCourseDesc', true, false)); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionUpdate" method="post" id="frmUpdateCourse" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="course_update" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblTitle'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][title]" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['title'])); ?>" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h100" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			?>
			<?php
			$tip = __('lblImageTip', true);
			$tip = str_replace("{MAX}", ini_get('post_max_size'), $tip);
			$tip = str_replace("{MAXFILE}", ini_get('upload_max_filesize'), $tip);
			?>
			<p>
				<label class="title"><?php __('lblImage', false, true); ?></label>
				<span class="inline_block">
					<input type="file" name="image" id="image" class="pj-form-field w400"/>
					<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php echo $tip;?>"></a>
				</span>
			</p>
			<?php
			if(!empty($tpl['arr']['source_path']))
			{
				$thumb_url = PJ_INSTALL_URL . $tpl['arr']['thumb_path'];
				?>
				<p id="image_container">
					<label class="title">&nbsp;</label>
					<span class="inline_block">
						<img class="pj-css-image" src="<?php echo $thumb_url; ?>" />
						<a href="#" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('btnDelete');?></a>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title"><?php __('lblPrice'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="price" name="price" value="<?php echo pjSanitize::html($tpl['arr']['price'])?>" class="pj-form-field number w108 required" data-msg-number="<?php __('pj_number_validation');?>" data-msg-required="<?php __('pj_field_required');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblClassSize'); ?></label>
				<input type="text" id="size" name="size" value="<?php echo pjSanitize::html($tpl['arr']['size'])?>" class="pj-form-field field-int w80 required digits" data-msg-required="<?php __('pj_field_required');?>" data-msg-digits="<?php __('pj_digits_validation');?>"/>
			</p>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDuration'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][duration]" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['duration'])); ?>" class="pj-form-field w200" lang="<?php echo $v['id']; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			} 
			?>
			<p>
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required" data-msg-required="<?php __('pj_field_required');?>">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('u_statarr', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : null;?>><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCourses&action=pjActionIndex';" />
				</span>
			</p>
			<?php pjUtil::printNotice(__('infoPeriodsTitle', true, false), __('infoPeriodsDesc', true, false)); ?>
			<table class="pj-table b15" id="tblPeriods" cellpadding="0" cellspacing="0" style="width: 100%;">
				<thead>
					<tr>
						<th style="width: 150px;"><?php __('lblStartDate'); ?></th>
						<th style="width: 150px;"><?php __('lblEndDate'); ?></th>
						<th style="width: 120px;"><?php __('lblRegisteredStudents'); ?></th>
						<th style="width: 100px;">&nbsp;</th>
						<th style="width: 30px;">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(!empty($tpl['class_arr']))
					{
						foreach ($tpl['class_arr'] as $class)
						{
							?>
							<tr data-id="<?php echo $class['id']; ?>">
								<td>
									<input type="hidden" name="class_id[<?php echo $class['id']; ?>]" value="<?php echo $class['id']; ?>" />
									<span class="pj-form-field-custom pj-form-field-custom-after">
										<input type="text" name="start_date[<?php echo $class['id'];?>]" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($class['start_date']));?>" class="pj-form-field pointer w90 datepick required" readonly="readonly" data-index="<?php echo $class['id'];?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
										<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
									</span>
								</td>
								<td>
									<span class="pj-form-field-custom pj-form-field-custom-after">
										<input type="text" name="end_date[<?php echo $class['id'];?>]" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($class['end_date']));?>" class="pj-form-field pointer w90 datepick required" readonly="readonly" data-index="<?php echo $class['id'];?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
										<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
									</span>
								</td>
								<td class="align_center">
									<?php
									if($class['cnt_students'] > 0)
									{
										?><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;class_id=<?php echo $class['id'];?>"><?php echo $class['cnt_students'];?></a><?php
									}else{
										echo $class['cnt_students'];
									} 
									?>
								</td>
								<td class="align_center"><input type="button" value="<?php __('btnSchedule'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&action=pjActionEdit&id=<?php echo $class['id'];?>';" /></td>
								<td class="align_center"><a href="#" class="pj-delete cpRemovePeriod" data-students="<?php echo $class['cnt_students'];?>"></a></td>
							</tr>
							<?php
						}
					}else{ 
						?>
						<tr>
							<td colspan="4" class="cpNoPeriods"><?php __('lblNoPeriodsDefined');?></td>
						</tr>
						<?php
					} 
					?>
				</tbody>
			</table>
			<input type="button" value="<?php __('btnAddPeriod'); ?>" class="pj-button b15 cpAddPeriod" />
			<br/>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCourses&action=pjActionIndex';" />
		</form>
	</div>
	
	<table id="tblPeriodsClone" style="display: none">
		<tbody>
			<tr data-id="{INDEX}">
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="start_date[{INDEX}]" class="pj-form-field pointer w90 datepick dateClone required" readonly="readonly" data-index="{INDEX}" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="end_date[{INDEX}]" class="pj-form-field pointer w90 datepick dateClone required" readonly="readonly" data-index="{INDEX}" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="align_center"><a href="#" class="pj-delete cpRemovePeriod" data-students="0"></a></td>
			</tr>
		</tbody>
	</table>
	
	<div id="dialogDuplicate" style="display: none" title="<?php __('lblDuplicatedPeriodTitle');?>"><?php __('lblDuplicatedPeriodDesc');?></div>
	<div id="dialogEmptyPeriod" style="display: none" title="<?php __('lblPeriodsSavedTitle');?>"><?php __('lblPeriodsSavedDesc');?></div>
	<div id="dialogDeleteImage" style="display: none" title="<?php __('delete_image');?>"><?php __('delete_image_confirmation');?></div>
	<div id="dialogDeletePeriod" style="display: none" title="<?php __('lblDeletePeriodTitle');?>"><?php __('lblDeletePeriodDesc');?></div>
	
	<script type="text/javascript">
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	myLabel.no_periods_defined = "<?php __('lblNoPeriodsDefined');?>";
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>