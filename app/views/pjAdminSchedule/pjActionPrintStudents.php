
<?php
if($tpl['status'] == 0)
{ 
	?>
	<div style="margin-bottom: 10px; margin-top: 10px;"><?php __('lblClass'); ?>: <b><?php echo pjSanitize::html($tpl['class_arr']['course']);?></b></div>
	<div style="margin-bottom: 10px;"><?php __('lblStartDate'); ?>: <b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date'])); ?></b></div>
	<div style="margin-bottom: 10px;"><?php __('lblEndDate'); ?>: <b><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['end_date'])); ?></b></div>
	<div style="margin: 0 auto; width: 100%;">
		
		<table class="table" cellspacing="2" cellpadding="5" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('lblName')?></th>
					<th><?php __('email')?></th>
					<th><?php __('lblPhone')?></th>
					<th><?php __('lblDepositPaid')?></th>
					<th><?php __('lblStatus')?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($tpl['booking_arr']))
				{
					$bs = __('booking_statuses', true);
					foreach($tpl['booking_arr'] as $k => $v)
					{
						?>
						<tr>
							<td><?php echo pjSanitize::html($v['name']);?></td>
							<td><?php echo pjSanitize::html($v['email']);?></td>
							<td><?php echo pjSanitize::html($v['phone']);?></td>
							<td><?php echo pjUtil::formatCurrencySign($v['deposit'], $tpl['option_arr']['o_currency']);?></td>
							<td><?php echo $bs[$v['status']];?></td>
						</tr>
						<?php
					}
				}else{
					?><tr><td colspan="5"><?php __('lblNoStudentsFound');?></td></tr><?php
				} 
				?>
			</tbody>
		</table>
	</div>
	<?php
}else{
	$statuses = __('registered_students', true);
	echo $statuses[$tpl['status']];
} 
?>