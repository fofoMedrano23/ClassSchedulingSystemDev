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
	
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('tabClassDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionPeriods&amp;course_id=<?php echo $tpl['arr']['id']?>"><?php __('tabPeriods'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionStudents&amp;course_id=<?php echo $tpl['arr']['id']?>"><?php __('tabStudents'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoPeriodsTitle', true, false), __('infoPeriodsDesc', true, false)); ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCourses&amp;action=pjActionPeriods&amp;course_id=<?php echo $tpl['arr']['id']?>" method="post" id="frmUpdatePeriod" class="pj-form form">
		<input type="hidden" name="period_update" value="1" />
		
		<table class="pj-table b15" id="tblPeriods" cellpadding="0" cellspacing="0" style="width: 100%;">
			<thead>
				<tr>
					<th style="width: 46%;"><?php __('lblStartDate'); ?></th>
					<th style="width: 46%;"><?php __('lblEndDate'); ?></th>
					<th style="width: 60px;">&nbsp;</th>
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
								<input type="hidden" name="id[<?php echo $class['id']; ?>]" value="<?php echo $class['id']; ?>" />
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
							<td class="align_center"><a href="#" class="pj-delete cpRemovePeriod"></a></td>
						</tr>
						<?php
					}
				}else{ 
					?>
					<tr>
						<td colspan="3" class="cpNoPeriods"><?php __('lblNoPeriodsDefined');?></td>
					</tr>
					<?php
				} 
				?>
			</tbody>
		</table>
		<input type="button" value="<?php __('btnAddPeriod'); ?>" class="pj-button b15 cpAddPeriod" />
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
	</form>
	
	<table id="tblPeriodsClone" style="display: none">
		<tbody>
			<tr data-id="{INDEX}">
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="start_date[{INDEX}]" class="pj-form-field pointer w90 datepick required" readonly="readonly" data-index="{INDEX}" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="end_date[{INDEX}]" class="pj-form-field pointer w90 datepick required" readonly="readonly" data-index="{INDEX}" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td class="align_center"><a href="#" class="pj-delete cpRemovePeriod"></a></td>
			</tr>
		</tbody>
	</table>
	
	<div id="dialogDuplicate" style="display: none" title="<?php __('lblDuplicatedPeriodTitle');?>"><?php __('lblDuplicatedPeriodDesc');?></div>
	<div id="dialogEmptyPeriod" style="display: none" title="<?php __('lblPeriodsSavedTitle');?>"><?php __('lblPeriodsSavedDesc');?></div>
	
	<script type="text/javascript">
		var myLabel = myLabel || {};
		myLabel.no_periods_defined = "<?php __('lblNoPeriodsDefined');?>";
	</script>
	
	<?php
}
?>