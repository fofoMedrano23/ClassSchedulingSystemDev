<?php
$message = '';
$message .= '<div style="margin-bottom: 10px;">'.__('lblDear', true).' <b>{NAME},</b></div>';
$message .= '<div style="margin-bottom: 10px;">'.__('lblClass', true).': <b>'.pjSanitize::html($tpl['class_arr']['course']).'</b></div>';
$message .= '<div style="margin-bottom: 10px;">'.__('lblStartDate', true).': <b>'.date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date'])).'</b></div>';
$message .= '<div style="margin-bottom: 10px;">'.__('lblEndDate', true).': <b>'.date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['end_date'])).'</b></div>';
$message .= '<div style="margin-bottom: 10px;">'.__('lblRegisteredStudents', true).': <b>'.$tpl['class_arr']['cnt_students']. ' ' . __('lblOf', true) . ' ' . $tpl['class_arr']['size'] . '</b></div>';
$message .= '<div style="margin: 0 auto; width: 100%;">';
$message .= '<table style="border-collapse: collapse;width: 100%;" cellspacing="2" cellpadding="5" style="width: 100%">';
$message .= '<thead>';
$message .= '<tr>';
$message .= '<th style="padding: 4px;text-align: left;border: 1px solid #000;">'.__('lblName', true).'</th>';
$message .= '<th style="padding: 4px;text-align: left;border: 1px solid #000;">'.__('email', true).'</th>';
$message .= '<th style="padding: 4px;text-align: left;border: 1px solid #000;">'.__('lblPhone', true).'</th>';
$message .= '</tr>';
$message .= '</thead>';
$message .= '<tbody>';
foreach($tpl['student_arr'] as $k => $student)
{
	$message .= '<tr>';
	$message .= '<td style="border: 1px solid #000;padding: 4px;">'.pjSanitize::html($student['name']).'</td>';
	$message .= '<td style="border: 1px solid #000;padding: 4px;">'.pjSanitize::html($student['email']).'</td>';
	$message .= '<td style="border: 1px solid #000;padding: 4px;">'.pjSanitize::html($student['phone']).'</td>';
	$message .= '</tr>';
}
$message .= '</tbody>';
$message .= '</table>';
$message .= '</div>';

?>
<div class="b15"><?php __('infoEmailStudentListTitle');?></div>

<form action="" method="post" class="form pj-form">
	<input type="hidden" name="send_schedule" value="1" />
	<input type="hidden" name="from" value="<?php echo $tpl['arr']['from']; ?>"/>
	<input type="hidden" name="subject" value="<?php echo pjSanitize::html($tpl['arr']['subject']); ?>"/>
	<textarea name="message" style="display:none;"><?php echo $message;?></textarea>
	
	<table class="pj-table b15" id="tblStudent" cellpadding="0" cellspacing="0" style="width: 100%;">
		<thead>
			<tr>
				<th width="150"><?php __('lblName'); ?></th>
				<th width="300"><?php __('email'); ?></th>
				<th width="60">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(isset($tpl['teacher_arr']))
			{
				foreach($tpl['teacher_arr'] as $k => $v)
				{
					?>
					<tr data-id="<?php echo $v['id'];?>">
						<td><input name="name[<?php echo $v['id'];?>]" value="<?php echo pjSanitize::html($v['name']);?>" class="pj-form-field w150 required" data-index="<?php echo $v['id'];?>"/></td>
						<td><input name="email[<?php echo $v['id'];?>]" value="<?php echo pjSanitize::html($v['email']);?>" class="pj-form-field w300 email required" data-index="<?php echo $v['id'];?>"/></td>
						<td class="align_center"><a href="#" class="pj-delete cpRemoveRecipient"></a></td>
					</tr>
					<?php
				}
			} 
			?>
		</tbody>
	</table>
	
	<input type="button" value="<?php __('btnPlusAdd'); ?>" class="pj-button b15 cpAddStudent" />
</form>
<?php
?>