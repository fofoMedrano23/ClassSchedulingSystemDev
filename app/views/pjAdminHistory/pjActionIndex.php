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
	if($controller->isAdmin())
	{
		?>
		<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
			<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
				<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionUpdate&amp;id=<?php echo $_GET['student_id'];?>"><?php __('tabDetails'); ?></a></li>
				<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminHistory&amp;action=pjActionIndex&amp;student_id=<?php echo $_GET['student_id'];?>"><?php __('tabPaymentHistory'); ?></a></li>
			</ul>
		</div>
		<?php
		pjUtil::printNotice(__('infoPaymentsHistoryTitle', true), __('infoPaymentsHistoryDesc', true));
	}
	if($controller->isStudent())
	{
		pjUtil::printNotice(__('infoPaymentsHistoryTitle', true), __('infoStudentPaymentsDesc', true));
	}
	?>
	<div class="b10">
		<?php
		if($controller->isAdmin())
		{ 
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left r5">
				<input type="hidden" name="controller" value="pjAdminHistory" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="hidden" name="student_id" value="<?php echo $_GET['student_id'];?>" />
				<input type="submit" class="pj-button" value="<?php __('btnAddPayment'); ?>" />
			</form>
			<?php
		} 
		?>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<?php
		$filter = __('history_filter', true);
		?>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="paid"><?php echo $filter['paid']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="refund"><?php echo $filter['refund']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="due"><?php echo $filter['due']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
	{
		?>pjGrid.queryString += "&student_id=<?php echo (int) $_GET['student_id']; ?>";<?php
	}
	?>
	pjGrid.isStudent = <?php echo $controller->isStudent() ? 'true' : 'false'; ?>;
	var myLabel = myLabel || {};
	myLabel.amount = "<?php __('lblAmount', false, true); ?>";
	myLabel.class = "<?php __('lblClass', false, true); ?>";
	myLabel.status = "<?php __('lblStatus', false, true); ?>";
	myLabel.created = "<?php __('lblCreatedOn', false, true); ?>";
	myLabel.paid = "<?php echo $filter['paid']; ?>";
	myLabel.refund = "<?php echo $filter['refund']; ?>";
	myLabel.due = "<?php echo $filter['due']; ?>";	
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	
	</script>
	<?php
}
?>