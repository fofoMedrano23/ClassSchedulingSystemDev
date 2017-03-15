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
	
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	pjUtil::printNotice(__('infoStudentsTitle', true), __('infoStudentsDesc', true));
	?>
	<div class="b10">
		<?php
		if($controller->isAdmin())
		{ 
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left r5">
				<input type="hidden" name="controller" value="pjAdminStudents" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="submit" class="pj-button" value="<?php __('btnAddStudent'); ?>" />
			</form>
			<?php
		} 
		?>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		<?php
		$filter = __('filter', true);
		?>
		<div class="float_right">
			<select name="class_id" id="class_id" class="pj-form-field required w300">
				<option value="">-- <?php __('lblAllClasses'); ?>--</option>
				<?php
				foreach ($tpl['class_arr'] as $k => $v)
				{
					?><option value="<?php echo $v['id']; ?>"><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
				}
				?>
			</select>
		</div>
		<br class="clear_both" />
	</div>

	<div class="pj-form-filter-advanced" style="display: none;">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="overflow float_left w300">
				<p>
					<label class="title100"><?php __('lblName'); ?></label>
					<span class="inline_block">
						<input type="text" name="name" id="name" class="pj-form-field w150" />
					</span>
				</p>
				<p>
					<label class="title100"><?php __('lblPhone'); ?></label>
					<span class="inline_block">
						<input type="text" name="phone" id="phone" class="pj-form-field w150" />
					</span>
				</p>
				<p>
					<label class="title100">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="overflow float_left">
				<p>
					<label class="title100"><?php __('email'); ?></label>
					<span class="inline_block">
						<input type="text" name="email" id="email" class="pj-form-field w250" />
					</span>
				</p>
				
				<p>
					<label class="title100"><?php __('lblRegistrationDate'); ?></label>
					<span class="block float_left">
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="from_date" class="pj-form-field pointer w80 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
					<span class="block float_left t6 r5 l5"><?php __('lblTo');?></span>
					<span class="block float_left">
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="to_date" class="pj-form-field pointer w80 datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</span>
				</p>
				<p>
					<label class="title100"><?php __('lblStatus'); ?></label>
					
					<span class="inline_block">
						<select name="status" id="status" class="pj-form-field w150">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach (__('u_statarr', true) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	pjGrid.isTeacher = <?php echo $controller->isTeacher() ? 'true' : 'false'; ?>;
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName', false, true); ?>";
	myLabel.email = "<?php __('email', false, true); ?>";
	myLabel.phone = "<?php __('lblPhone', false, true); ?>";
	myLabel.payment = "<?php __('lblPayment', false, true); ?>";
	myLabel.na = "<?php __('lblNA', false, true); ?>";
	myLabel.revert_status = "<?php __('revert_status', false, true); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.active = "<?php echo $u_statarr['T']; ?>";
	myLabel.inactive = "<?php echo $u_statarr['F']; ?>";	
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.status = "<?php __('lblStatus', false, true); ?>";
	</script>
	<?php
}
?>