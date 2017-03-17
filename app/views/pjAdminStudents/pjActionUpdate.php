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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('tabDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminHistory&amp;action=pjActionIndex&amp;student_id=<?php echo $tpl['arr']['id']?>"><?php __('tabPaymentHistory'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoUpdateStudentTitle', true), __('infoUpdateStudentDesc', true));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionUpdate" method="post" id="frmUpdateStudent" class="form pj-form" autocomplete="off">
		<input type="hidden" name="student_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		
		<p>
			<label class="title"><?php __('email'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="email" id="email" value="<?php echo pjSanitize::html($tpl['arr']['email'])?>" class="pj-form-field required email w200" data-msg-remote="<?php __('pj_email_taken');?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="password" name="password" id="password" value="<?php echo pjSanitize::html($tpl['arr']['password'])?>" class="pj-form-field required w200" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblName'); ?></label>
			<span class="inline_block">
				<input type="text" name="name" id="name" value="<?php echo pjSanitize::html($tpl['arr']['name'])?>"  class="pj-form-field w250 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="inline_block">
				<input type="text" name="phone" id="phone" value="<?php echo pjSanitize::html($tpl['arr']['phone'])?>" class="pj-form-field w250" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCompany'); ?></label>
			<span class="inline_block">
				<input type="text" name="company" id="company" value="<?php echo pjSanitize::html($tpl['arr']['company'])?>" class="pj-form-field w250" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvAddress'); ?></label>
			<span class="inline_block">
                        <input type="text" id="address" name="address" value="<?php echo pjSanitize::html($tpl['arr']['address'])?>" readonly="true" onclick="$('#address').datepicker({changeYear: true,dateFormat: 'dd-mm-yy'});$('#address').datepicker('show');">
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvCity'); ?></label>
			<span class="inline-block">
				<select name="genero" id="genero" class="pj-form-field w400">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['gender_arr'] as $v)
					{
						?><option value="<?php echo $v['gender']; ?>"<?php echo $tpl['arr']['genero'] == $v['gender'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['gender']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvState'); ?></label>
			<span class="inline_block">
				<textarea name="experiencia" rows="5" cols="40" id="experiencia"><?php echo pjSanitize::html($tpl['arr']['experiencia'])?></textarea>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblResvZip'); ?></label>
			<span class="inline_block">
				<input type="text" name="zip" id="zip" value="<?php echo pjSanitize::html($tpl['arr']['zip'])?>" class="pj-form-field w250" />
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
						?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['country_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['country_title']); ?></option><?php
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
						?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
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