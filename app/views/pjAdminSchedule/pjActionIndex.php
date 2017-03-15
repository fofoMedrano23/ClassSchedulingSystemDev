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
	$u_statarr = __('u_statarr', true);
	
	pjUtil::printNotice(__('infoScheduleTitle', true), __('infoScheduleDesc', true));
	?>
	<div class="b10 pjCssScheduleBox">
		<?php
		if($controller->isAdmin())
		{ 
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left r5">
				<input type="hidden" name="controller" value="pjAdminSchedule" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="submit" class="pj-button" value="<?php __('btnManageSchedule'); ?>" />
			</form>
			<?php
		} 
		?>
		<form action="" method="get" class="float_left pj-form frm-filter overflow">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150 b5" placeholder="<?php __('btnSearch'); ?>" />
			<?php
			if( (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0) || (isset($_GET['teacher_id']) && (int) $_GET['teacher_id'] > 0) )
			{ 
				?>
				<br class="clear_both" />
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionIndex" class="float_right"><?php __('lblClearFilter');?></a>
				<?php
			} 
			?>
		</form>
		<div class="float_right">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" target="_blank">
				<input type="hidden" name="controller" value="pjAdminSchedule" />
				<input type="hidden" name="action" value="pjActionPrintSchedule" />

				<div class="float_left t3 r5">
					<select name="class_id" id="filter_class_id" class="pj-form-field w200">
						<option value="">-- <?php __('lblAllClasses'); ?>--</option>
						<?php
						foreach ($tpl['class_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['class_id']) && $_GET['class_id'] == $v['id'] ? ' selected="selected"' : NULL?>><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
						}
						?>
					</select>
					<?php
					if($controller->isAdmin() || $controller->isStudent())
					{ 
						?>
						<select name="teacher_id" id="filter_teacher_id" class="pj-form-field w150">
							<option value="">-- <?php __('lblAllTeachers'); ?>--</option>
							<?php
							foreach ($tpl['teacher_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['teacher_id']) && $_GET['teacher_id'] == $v['id'] ? ' selected="selected"' : NULL?>><?php echo pjSanitize::html( $v['name']); ?></option><?php
							}
							?>
						</select>
						<?php
					} 
					?>
				</div>
				<input type="submit" class="pj-button float_left" value="<?php __('lblPrint'); ?>"/>
			</form>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid" class="<?php echo !$controller->isAdmin() ? 'readOnlyGrid' : NULL;?>"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
	{
		?>pjGrid.queryString += "&class_id=<?php echo (int) $_GET['class_id']; ?>";<?php
	}
	if (isset($_GET['teacher_id']) && (int) $_GET['teacher_id'] > 0)
	{
		?>pjGrid.queryString += "&teacher_id=<?php echo (int) $_GET['teacher_id']; ?>";<?php
	}
	if (isset($_GET['course_id']) && (int) $_GET['course_id'] > 0)
	{
		?>pjGrid.queryString += "&course_id=<?php echo (int) $_GET['course_id']; ?>";<?php
	}
	?>
	pjGrid.isTeacher = <?php echo $controller->isTeacher() ? 'true' : 'false'; ?>;
	pjGrid.isStudent = <?php echo $controller->isStudent() ? 'true' : 'false'; ?>;
	var myLabel = myLabel || {};
	myLabel.class = "<?php __('lblClass', false, true); ?>";
	myLabel.teacher = "<?php __('lblTeacher', false, true); ?>";
	myLabel.date = "<?php __('lblDate', false, true); ?>";
	myLabel.time = "<?php __('lblTime', false, true); ?>";
	myLabel.venue = "<?php __('lblVenue', false, true); ?>";
	myLabel.btnPrint = "<?php __('lblPrint', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	</script>
	<?php
}
?>