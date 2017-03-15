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
	$filter = __('filter', true, false);
	
	pjUtil::printNotice(__('infoCourseStudentsTitle', true, false), __('infoCourseStudentsDesc', true, false)); ?>
	<div class="b10">
		
		<form action="" method="get" class="float_left pj-form stident-frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		
		<br class="clear_both" />
	</div>
	<div id="student_grid"></div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if(isset($_GET['course_id']) && (int)$_GET['course_id'] > 0 )
	{
		?>pjGrid.queryString += "&course_id=<?php echo (int) $_GET['course_id']; ?>";<?php
	} 
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.email = "<?php __('email'); ?>";
	myLabel.phone = "<?php __('lblPhone'); ?>";
	</script>
	<?php
}
?>