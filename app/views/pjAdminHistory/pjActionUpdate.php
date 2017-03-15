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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionUpdate&amp;id=<?php echo $_GET['student_id'];?>"><?php __('tabDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminHistory&amp;action=pjActionIndex&amp;student_id=<?php echo $_GET['student_id'];?>"><?php __('tabPaymentHistory'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoUpdatePaymentTitle', true), __('infoUpdatePaymentDesc', true));?>
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<div class="clear_both">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminHistory&amp;action=pjActionUpdate&amp;student_id=<?php echo $_GET['student_id'];?>" method="post" id="frmUpdatePayment" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="payment_update" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<input type="hidden" name="student_id" value="<?php echo $_GET['student_id'];?>" />
			<p>
				<label class="title"><?php __('lblAmount'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="amount" name="amount" value="<?php echo pjSanitize::html($tpl['arr']['amount']);?>" class="pj-form-field number w108 required" data-msg-number="<?php __('pj_number_validation');?>" data-msg-required="<?php __('pj_field_required');?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblClass'); ?></label>
				<span class="inline_block">
					<select name="class_id" id="class_id" class="pj-form-field required w300">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['class_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['class_id'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblDescription'); ?></label>
					<span class="inline_block">
						<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h100" lang="<?php echo $v['id']; ?>"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif; ?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field required" data-msg-required="<?php __('pj_field_required');?>">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('history_filter', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['status'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminHistory&action=pjActionIndex&student_id=<?php echo $_GET['student_id'];?>';" />
			</p>
		</form>
	</div>
	
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>