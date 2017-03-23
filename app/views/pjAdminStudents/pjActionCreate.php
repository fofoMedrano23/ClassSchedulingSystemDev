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
	pjUtil::printNotice(__('infoAddStudentTitle', true), __('infoAddStudentDesc', true));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionCreate" method="post" id="frmCreateStudent" class="form pj-form" autocomplete="off">
		<input type="hidden" name="student_create" value="1" />
		
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" class="pj-form-field required email w200" data-msg-remote="<?php __('pj_email_taken');?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="password" name="password" id="password" class="pj-form-field required w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="inline_block">
				<input type="text" name="phone" id="phone" class="pj-form-field w250" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCompany'); ?></label>
			<span class="inline-block">
				<select name="education" id="education" class="pj-form-field w400">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['education_arr'] as $v)
					{
						?><option value="<?php echo $v['education']; ?>"><?php echo stripslashes($v['education']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvAddress'); ?></label>
			<span class="inline_block">
                        <input type="text" id="birthdate" name="birthdate" readonly="true" onclick="$('#birthdate').datepicker({changeYear: true,defaultDate: '1-1-1994',dateFormat: 'dd-mm-yy'});$('#birthdate').datepicker('show');">
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvCity'); ?></label>
			<span class="inline-block">
				<select name="gender" id="gender" class="pj-form-field w400">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['gender_arr'] as $v)
					{
						?><option value="<?php echo $v['gender']; ?>"><?php echo stripslashes($v['gender']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvState'); ?></label>
			<span class="inline_block">
				<textarea name="experience" rows="5" cols="40" id="experience"><?php echo pjSanitize::html($tpl['arr']['experience'])?></textarea>
			</span>
		</p>
		
		<p>
			<label class="title"><?php __('lblResvCountry'); ?></label>
			<span class="inline-block">
				<select name="country_id" id="country_id" class="pj-form-field w400">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['country_arr'] as $v)
					{
						?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == 'T' ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminStudents&action=pjActionIndex';" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	</script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
       

        

	<?php
}
?>