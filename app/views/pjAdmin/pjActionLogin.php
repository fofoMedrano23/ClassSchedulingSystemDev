<div class="login-box">
	
	<h3><?php __('adminLogin'); ?></h3>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogin" method="post" id="frmLoginAdmin" class="form">
		<input type="hidden" name="login_user" value="1" />
		<p>
			<label class="title"><?php __('email'); ?>:</label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="login_email" id="login_email" class="pj-form-field required email w250" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?>:</label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="password" name="login_password" id="login_password" class="pj-form-field required w250" autocomplete="off" />
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span class="block float_left overflow">
				<input type="submit" value="<?php __('btnLogin', false, true); ?>" class="pj-button float_left" />
				<a class="no-decor l10 block float_left r5 t5" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdmin&action=pjActionForgot"><?php __('lblForgot'); ?></a>
				<span class="no-decor block float_left r5 t5">|</span>
				<a class="no-decor block float_left r5 t5" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdmin&action=pjActionTeacherLogin"><?php __('lblTeacherLogin'); ?></a>
				<span class="no-decor block float_left r5 t5">|</span>
				<a class="no-decor block float_left t5" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdmin&action=pjActionStudentLogin"><?php __('lblStudentLogin'); ?></a>
			</span>
		</p>
		<?php
		if (isset($_GET['err']))
		{
			$err = __('login_err', true);
			switch ($_GET['err'])
			{
				case 1:
					?><em><label class="err" style="display: inline;"><?php echo $err[1]; ?></label></em><?php
					break;
				case 2:
					?><em><label class="err" style="display: inline;"><?php echo $err[2]; ?></label></em><?php
					break;
				case 3:
					?><em><label class="err" style="display: inline;"><?php echo $err[3]; ?></label></em><?php
					break;
			}
		}
		?>
	</form>
</div>