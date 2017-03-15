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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('tabBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPayments"><?php __('tabPayments'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoPaymentsListTitle', true), __('infoPaymentsListDesc', true));
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left r5">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreatePayment" />
			<input type="submit" class="pj-button" value="<?php __('btnAddPayment'); ?>" />
		</form>
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
	var myLabel = myLabel || {};
	myLabel.amount = "<?php __('lblAmount', false, true); ?>";
	myLabel.name = "<?php __('lblName', false, true); ?>";
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