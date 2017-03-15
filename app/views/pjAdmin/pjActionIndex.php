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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat classes">
				<div class="info">
					<abbr><?php echo $tpl['cnt_active_classes'];?></abbr>
					<label><?php $tpl['cnt_active_classes'] != 1 ? __('dash_active_classes') : __('dash_active_class');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings_received'];?></abbr>
					<label><?php $tpl['cnt_bookings_received'] != 1 ? __('dash_bookings_received') : __('dash_booking_received');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat teachers">
				<div class="info">
					<abbr><?php echo $tpl['cnt_active_teachers'];?></abbr>
					<label><?php $tpl['cnt_active_teachers'] != 1 ? __('dash_active_teachers') : __('dash_active_teacher');?></label>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dash_upcoming_classes');?></div>
			<div class="dashboard_column_top"><?php __('dash_latest_bookings');?></div>
			<div class="dashboard_column_top"><?php __('dash_quick_links');?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['upcoming_classes']) > 0)
					{
						foreach($tpl['upcoming_classes'] as $v)
						{
							?>
							<div class="dashboard_row">							
								<label class="bold"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&action=pjActionEdit&id=<?php echo $v['class_id'];?>"><?php echo pjSanitize::html($v['class_name'])?></a></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_ts']));?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['start_ts'])) . ' - ' . date($tpl['option_arr']['o_time_format'], strtotime($v['end_ts']));?></label>
								<label><?php echo pjSanitize::html($v['teacher_name'])?>, <?php echo pjSanitize::html($v['venue'])?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('dash_no_classes_found');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_bookings']) > 0)
					{
						foreach($tpl['latest_bookings'] as $v)
						{
							?>
							<div class="dashboard_row">							
								<label class="bold"><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionUpdate&id=<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['student_name'])?></a></label>
								<label><?php echo pjSanitize::html($v['class_name'])?></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])) . ' - ' . date($tpl['option_arr']['o_date_format'], strtotime($v['end_date']));?></label>
								
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row"><label><?php __('dash_no_bookings_found');?></label></div>
						<?php
					} 
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<div class="dashboard_row">
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&amp;action=pjActionIndex"><?php __('dash_view_schedule');?></a>
						<a class="block no-decor fs14 b20" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('dash_view_bookings');?></a>
						
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_booking');?></a>
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminStudents&amp;action=pjActionCreate"><?php __('dash_add_student');?></a>
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCourses&amp;action=pjActionCreate"><?php __('dash_add_class');?></a>
						<a class="block no-decor fs14 b10" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminTeachers&amp;action=pjActionCreate"><?php __('dash_add_teacher');?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ', ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo date($tpl['option_arr']['o_time_format'], time()); ?></div>
		</div>
	</div>
	<?php
}
?>