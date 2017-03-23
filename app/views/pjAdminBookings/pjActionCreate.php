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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('tabBookings'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPayments"><?php __('tabPayments'); ?></a></li>
		</ul>
	</div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateBooking">
		<input type="hidden" name="booking_create" value="1" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		<?php
		if(isset($_GET['class_id']))
		{
			?><input type="hidden" name="edit_class_id" value="<?php echo $_GET['class_id'];?>" /><?php
		} 
		?>
		
		<?php pjUtil::printNotice(__('infoBookingDetailsTitle', true, false), __('infoBookingDetailsDesc', true, false)); ?>
		
		<fieldset class="fieldset white float_left w380 overflow">
			<legend><?php __('lblClassDetails'); ?></legend>
			<p class="p108">
				<label class="title"><?php __('lblClass'); ?></label>
				<span class="inline-block">
					<span class="block float_left r5">
						<select name="class_id" id="class_id" class="pj-form-field w230 required">
							<option value="" data-price="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach ($tpl['class_arr'] as $v)
							{
								if((int) $v['size'] > (int) $v['booked'])
								{	
									?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['class_id']) ? ($_GET['class_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL;?> data-price="<?php echo pjSanitize::html($v['price']);?>"><?php echo $v['course'] . ' ('.date($tpl['option_arr']['o_date_format'], strtotime($v['start_date'])).')'; ?></option><?php
								}
							}
							?>
						</select>
					</span>
					<a id="pjCssEditClass" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionEdit&id={ID}" class="pj-edit" style="display:none;"></a>
				</span>
			</p>
			<p class="p108">
				<label class="title"><?php __('lblStatus'); ?></label>
				<span class="inline-block">
					<select name="status" id="status" class="pj-form-field w150 required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('booking_statuses', true, false) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p class="p108">
				<label class="title">&nbsp;</label>
				<span id="tbSeatsForm" style="display: none;"></span>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
			</p>
		</fieldset>
		<fieldset class="fieldset white float_right w300 overflow">
			<legend><?php __('lblPaymentInformation'); ?></legend>
			<p class="p108">
				<label class="title"><?php __('lblSubTotal'); ?></label>
				<span class="inline-block">
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="subtotal" name="subtotal" class="pj-form-field number w108" readonly="readonly"/>
					</span>
				</span>
			</p>
			<p class="p108">
				<label class="title"><?php __('lblTax'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="tax" name="tax" class="pj-form-field number w108" readonly="readonly" data-tax="<?php echo $tpl['option_arr']['o_tax_payment'];?>"/>
				</span>
			</p>
			<p class="p108">
				<label class="title"><?php __('lblTotal'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="total" name="total" class="pj-form-field number w108" readonly="readonly"/>
				</span>
			</p>
			<p class="p108">
				<label class="title"><?php __('lblDeposit'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="deposit" name="deposit" class="pj-form-field number w108" readonly="readonly" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>"/>
				</span>
			</p>
			<p class="p108">
				<label class="title"><?php __('lblPaymentMethod');?></label>
				<span class="inline-block">
					<select name="payment_method" id="payment_method" class="pj-form-field w150 required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach (__('payment_methods', true, false) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p class="boxCC p108" style="display: none;">
				<label class="title"><?php __('lblCCType'); ?></label>
				<span class="inline-block">
					<select name="cc_type" class="pj-form-field pj-cc-field w150">
						<option value="">---</option>
						<?php
						foreach (__('cc_types', true, false) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p class="boxCC p108" style="display: none;">
				<label class="title"><?php __('lblCCNum'); ?></label>
				<span class="inline-block">
					<input type="text" name="cc_num" id="cc_num" class="pj-form-field pj-cc-field w136" />
				</span>
			</p>
			<p class="boxCC p108" style="display: none;">
				<label class="title"><?php __('lblCCExp'); ?></label>
				<span class="inline-block">
					<select name="cc_exp_month" class="pj-form-field pj-cc-field">
						<option value="">---</option>
						<?php
						$month_arr = __('months', true, false);
						ksort($month_arr);
						foreach ($month_arr as $key => $val)
						{
							?><option value="<?php echo $key;?>"><?php echo $val;?></option><?php
						}
						?>
					</select>
					<select name="cc_exp_year" class="pj-form-field pj-cc-field">
						<option value="">---</option>
						<?php
						$y = (int) date('Y');
						for ($i = $y; $i <= $y + 10; $i++)
						{
							?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p class="boxCC" style="display: none">
				<label class="title"><?php __('lblCCCode'); ?></label>
				<span class="inline-block">
					<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100 pj-cc-field" />
				</span>
			</p>
		</fieldset>
		
		<div class="clear_both">
			<fieldset class="fieldset white">
				<legend><?php __('tabClientDetails'); ?></legend>
				<p>
					<label class="title">&nbsp;</label>
					<span class="inline_block t5">
						<span class="block float_left r20"><input type="radio" name="student_type" id="type_new" value="new" checked="checked" class="block float_left r3"/><label for="type_new" class="block float_left"><?php __('lblNewStudent');?></label></span>
						<span class="block float_left r20"><input type="radio" name="student_type" id="type_existing" value="existing" class="block float_left r3"/><label for="type_existing" class="block float_left"><?php __('lblExistingStudent');?></label></span>
					</span>
				</p>
				<div id="existingBox" style="display: none;">
					<p>
						<label class="title"><?php __('lblStudent'); ?></label>
						<span class="inline_block">
							<span class="block float_left r5">
								<select name="student_id" id="student_id" class="pj-form-field w300">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ($tpl['student_arr'] as $k => $v)
									{
										?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html( $v['name']); ?></option><?php
									}
									?>
								</select>
							</span>
							<a id="pjCssEditStudent" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminStudents&amp;action=pjActionUpdate&id={ID}" class="pj-edit" style="display:none;"></a>
						</span>
					</p>
				</div>
				<div id="newBox">
					<?php
					if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblResvTitle'); ?></label>
							<span class="inline-block">
								<select name="title" id="title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' css-required required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ( __('personal_titles', true, false) as $k => $v)
									{
										?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvName'); ?></label>
							<span class="inline-block">
								<input type="text" name="name" id="name" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblResvEmail'); ?></label>
							<span class="inline-block">
								<input type="text" name="email" id="email" class="pj-form-field email w400<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvPhone'); ?></label>
							<span class="inline-block">
								<input type="text" name="phone" id="phone" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvCompany'); ?></label>
							<span class="inline-block">
                                                            <select name="education" id="education" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
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
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvAddress'); ?></label>
						<span class="inline_block">
                                                        <input type="text" id="birthdate" name="birthdate" readonly="true" onclick="$('#birthdate').datepicker({changeYear: true,defaultDate: '1-1-1994',dateFormat: 'dd-mm-yy'});$('#birthdate').datepicker('show');">
                                                </span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvCity'); ?></label>
							<span class="inline-block">
								<select name="gender" id="gender" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
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
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvState'); ?></label>
							<span class="inline-block">
                                                            <textarea name="experience" id="experience" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"></textarea>
							</span>
                                                        
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_zip'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvZip'); ?></label>
							<span class="inline-block">
								<input type="text" name="zip" id="zip" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblResvCountry'); ?></label>
							<span class="inline-block">
								<select name="country_id" id="country_id" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' css-required required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>">
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
						<?php
					}
					?>
				</div>
				<?php
				if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
				{
					?>
					<p>
						<label class="title"><?php __('lblResvNotes'); ?></label>
						<span class="inline-block">
							<textarea name="notes" id="notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>" data-msg-required="<?php __('pj_field_required');?>"></textarea>
						</span>
					</p>
					<?php
				}
				?>
				
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
				</p>
			</fieldset>
		</div>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery);
		</script>
		<?php
	}
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>