<?php
if($tpl['status'] == 0)
{ 
	?>
	<div style="margin-bottom: 10px;"><?php __('lblClass'); ?>: <b><?php echo pjSanitize::html($tpl['class_arr']['course']);?></b></div>
	<div style="margin-bottom: 10px;"><?php __('lblStartDate'); ?>: <b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date'])); ?></b></div>
	<div style="margin-bottom: 10px;"><?php __('lblEndDate'); ?>: <b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['end_date'])); ?></b></div>
	<?php
	if(!$controller->isStudent())
	{ 
		?>
		<div style="margin-bottom: 10px;"><?php __('lblRegisteredStudents'); ?>: <b><?php echo $tpl['class_arr']['cnt_students']; ?> <?php __('lblOf');?> <?php echo $tpl['class_arr']['size']; ?></b></div>
		<?php
	} 
	?>
	<div style="margin: 0 auto; width: 100%;">
		
		<table class="table" cellspacing="2" cellpadding="5" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('lblDate')?></th>
					<th><?php __('lblStartTime')?></th>
					<th><?php __('lblEndTime')?></th>
					<th><?php __('lblTeacher')?></th>
					<th><?php __('lblVenue')?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($tpl['schedule_arr']))
				{
					foreach($tpl['schedule_arr'] as $k => $schedule)
					{
						?>
						<tr>
							<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($schedule['start_ts']));?></td>
							<td><?php echo date($tpl['option_arr']['o_time_format'], strtotime($schedule['start_ts']));?></td>
							<td><?php echo date($tpl['option_arr']['o_time_format'], strtotime($schedule['end_ts']));?></td>
							<td><?php echo pjSanitize::html($schedule['name']);?></td>
							<td><?php echo pjSanitize::html($schedule['venue']);?></td>
						</tr>
						<?php
					}
				}else{
					?><tr><td colspan="5"><?php __('lblNoRecorsFound');?></td></tr><?php
				} 
				?>
			</tbody>
		</table>
	</div>
	<?php
}else{
	$statuses = __('class_statuses', true);
	echo $statuses[$tpl['status']];
} 
?>