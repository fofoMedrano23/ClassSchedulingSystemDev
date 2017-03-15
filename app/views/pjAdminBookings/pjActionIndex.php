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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('tabBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPayments"><?php __('tabPayments'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoBookingsListTitle', true, false), __('infoBookingsListDesc', true, false)); 
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddBooking'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<?php
		$bs = __('booking_statuses', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-filter btn-all"><?php __('lblAll')?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $bs['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $bs['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $bs['cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	
	<div id="grid"></div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
	{
		?>pjGrid.queryString += "&class_id=<?php echo (int) $_GET['class_id']; ?>";<?php
	}
	if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
	{
		?>pjGrid.queryString += "&student_id=<?php echo (int) $_GET['student_id']; ?>";<?php
	}
	if (isset($_GET['course_id']) && (int) $_GET['course_id'] > 0)
	{
		?>pjGrid.queryString += "&course_id=<?php echo (int) $_GET['course_id']; ?>";<?php
	}
	if (isset($_GET['status']) && in_array($_GET['status'], array('confirmed', 'pending', 'cancelled')))
	{
		?>pjGrid.queryString += "&status=<?php echo $_GET['status']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.class = "<?php __('lblClass'); ?>";
	myLabel.student = "<?php __('lblStudent'); ?>";
	myLabel.exported = "<?php __('lblExport'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.pending = "<?php echo $bs['pending']; ?>";
	myLabel.confirmed = "<?php echo $bs['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $bs['cancelled']; ?>";
	</script>
	<?php
}
?>