<?php
if(isset($tpl['arr']))
{ 
	?>
	<div style="margin: 0 auto; width: 100%;">
		
		<table class="table" cellspacing="2" cellpadding="5" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('lblClass')?></th>
					<th><?php __('lblTeacher')?></th>
					<th><?php __('lblVenue')?></th>
					<th><?php __('lblDate')?></th>
					<th><?php __('lblTime')?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($tpl['arr']))
				{
					foreach($tpl['arr'] as $k => $v)
					{
						?>
						<tr>
							<td><?php echo $v['class'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')';?></td>
							<td><?php echo pjSanitize::html($v['teacher']);?></td>
							<td><?php echo pjSanitize::html($v['venue']);?></td>
							<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_ts']));?></td>
							<td><?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['start_ts'])) . ' - ' . date($tpl['option_arr']['o_time_format'], strtotime($v['end_ts']));?></td>
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
} 
?>