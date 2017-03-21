<?php
include_once PJ_VIEWS_PATH . 'pjFrontPublic/elements/header.php';
?>

<form id="pjCssCheckoutForm_<?php echo $_GET['index']?>" action="" method="post">
	<input type="hidden" name="css_checkout" value="1" />
	<div class="pjCss-class">
		<div class="pjCss-class-body">
			<?php include_once dirname(__FILE__) . '/elements/booking.php';?>
			
		</div><!-- /.pjCss-class-body -->
	
		<div class="pjCss-class-footer">
			<br>
	
			<div class="pjCss-class-heading"><?php __('front_personal_details');?></div><!-- /.pjCss-class-heading -->
	
			<?php
			if(!$controller->isFrontLogged())
			{
				$login_message = __('front_login_message', true);
				$login_message = str_replace("{STAG}", '<a href="#" class="pjCssLogin">', $login_message);
				$login_message = str_replace("{ETAG}", '</a>', $login_message);
				?>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group"><label><?php echo $login_message;?></label></div>
					</div>
				</div>
				<?php
			}else{
				$logout_message = __('front_logout_message', true);
				$logout_message = str_replace("{STAG}", '<a href="#" class="pjCssLogout">', $logout_message);
				$logout_message = str_replace("{ETAG}", '</a>', $logout_message);
				?>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group"><label><?php echo $logout_message;?></label></div>
					</div>
				</div>
				<?php
			}
			$CLIENT = $controller->isFrontLogged() ? $_SESSION[$controller->defaultFrontStudent] : array();
			
			ob_start();
			$columns = 0;
			if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_title'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_title'] === 3) {?><span>*</span><?php }?></label>
	
						<select id="c_title" name="c_title" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value="">-- <?php __('front_choose');?> --</option>
							<?php
							foreach(__('personal_titles', true) as $k => $v) 
							{
								?><option value="<?php echo $k;?>"<?php echo isset($FORM['c_title']) ? ($FORM['c_title'] == $k ? ' selected="selected"' : null) : (isset($CLIENT['title']) ? ($CLIENT['title'] == $k ? ' selected="selected"' : NULL) : NULL);?>><?php  echo $v;?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_name'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_name'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_name" name="c_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_name']) ? pjSanitize::html($FORM['c_name']) : ( isset($CLIENT['name']) ? pjSanitize::html($CLIENT['name']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_email'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_email'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_email" name="c_email" class="form-control email<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_email']) ? pjSanitize::html($FORM['c_email']) : ( isset($CLIENT['email']) ? pjSanitize::html($CLIENT['email']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-email="<?php __('pj_email_validation'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_password'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_password'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_password'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="password" id="c_password" name="c_password" class="form-control <?php echo (int) $tpl['option_arr']['o_bf_include_password'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_password']) ? pjSanitize::html($FORM['c_password']) : ( isset($CLIENT['password']) ? pjSanitize::html($CLIENT['password']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_phone'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_phone'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_phone" name="c_phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_phone']) ? pjSanitize::html($FORM['c_phone']) : ( isset($CLIENT['phone']) ? pjSanitize::html($CLIENT['phone']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_company'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_company'] === 3) {?><span>*</span><?php }?></label>
						
						<select id="c_education" name="c_education" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value="">-- <?php __('front_choose');?> --</option>
							<?php
							foreach($tpl['education_arr'] as $v) 
							{
								?><option value="<?php echo $v['education'];?>"<?php echo isset($FORM['c_education']) ? ($FORM['c_education'] == $v['id'] ? ' selected="selected"' : NULL) : ( isset($CLIENT['education_id']) ? ($CLIENT['education_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL );?>><?php  echo pjSanitize::html($v['education']);?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_address'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_address'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_address" name="c_address" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_address']) ? pjSanitize::html($FORM['c_address']) : ( isset($CLIENT['address']) ? pjSanitize::html($CLIENT['address']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_city'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_city'] === 3) {?><span>*</span><?php }?></label>
						
						<select id="c_gender" name="c_gender" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value="">-- <?php __('front_choose');?> --</option>
							<?php
							foreach($tpl['gender_arr'] as $v) 
							{
								?><option value="<?php echo $v['gender'];?>"<?php echo isset($FORM['c_gender']) ? ($FORM['c_gender'] == $v['id'] ? ' selected="selected"' : NULL) : ( isset($CLIENT['gender_id']) ? ($CLIENT['gender_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL );?>><?php  echo pjSanitize::html($v['gender']);?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_state'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_state'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_state" name="c_state" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_state']) ? pjSanitize::html($FORM['c_state']) : ( isset($CLIENT['state']) ? pjSanitize::html($CLIENT['state']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_zip'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_zip'] === 3) {?><span>*</span><?php }?></label>
						
						<input type="text" id="c_zip" name="c_zip" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_zip']) ? pjSanitize::html($FORM['c_zip']) : ( isset($CLIENT['zip']) ? pjSanitize::html($CLIENT['zip']) : NULL ); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_country'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_country'] === 3) {?><span>*</span><?php }?></label>
	
						<select id="c_country" name="c_country" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value="">-- <?php __('front_choose');?> --</option>
							<?php
							foreach($tpl['country_arr'] as $k => $v) 
							{
								?><option value="<?php echo $v['id'];?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : NULL) : ( isset($CLIENT['country_id']) ? ($CLIENT['country_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL );?>><?php  echo pjSanitize::html($v['country_title']);?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
			}
			$field_content = ob_get_contents();
			ob_end_clean();
			if(!empty($field_content))
			{
				?>
				<div class="row"><?php echo $field_content;?></div>
				<?php
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
			{
				?>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label><?php __('front_notes'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_notes'] === 3) {?><span>*</span><?php }?></label>
							
							<textarea id="c_notes" name="c_notes" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : NULL; ?>" style="height: 150px;" data-msg-required="<?php __('pj_field_required'); ?>"><?php echo pjSanitize::html(@$FORM['c_notes']); ?></textarea>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
	
			<br>
	
			<?php
			if ($tpl['option_arr']['o_payment_disable'] == 'No')
			{ 
				?>
				<div class="pjCss-class-heading"><?php __('front_payment_method')?></div><!-- /.pjCss-class-heading -->
		
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_payment_method')?><span>*</span></label>
							
							<select name="payment_method" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
								<option value="">-- <?php __('front_choose');?> --</option>
								<?php
								foreach (__('payment_methods', true) as $k => $v)
								{
									if ($tpl['option_arr']['o_allow_' . $k] === "Yes")
									{
										?><option value="<?php echo $k; ?>"<?php echo @$FORM['payment_method'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
									}
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div><!-- /.col-sm-6 -->
					
					<div class="col-sm-6 pjSbsBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
						<div class="form-group">
							<label><?php __('front_bank_account')?></label>
							
							<div class="text-muted"><strong><?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?></strong></div>
						</div>
					</div><!-- /.col-sm-6 -->
				</div>
				
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_type')?></label>
							
							<select name="cc_type" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
					    		<option value="">---</option>
					    		<?php
								foreach (__('cc_types', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo @$FORM['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
								}
								?>
					    	</select>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_num')?></label>
							
							<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_num']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
				</div>
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_code')?></label>
							
							<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_code']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_exp')?></label>
							<div class="row">
								<div class="col-sm-7">
									<?php
									$rand = rand(1, 99999);
									$time = pjTime::factory()
										->attr('name', 'cc_exp_month')
										->attr('id', 'cc_exp_month_' . $rand)
										->attr('class', 'form-control required')
										->prop('format', 'F');
									if (isset($FORM['cc_exp_month']) && !is_null($FORM['cc_exp_month']))
									{
										$time->prop('selected', $FORM['cc_exp_month']);
									}
									echo $time->month();
									?>
								</div>
								<div class="col-sm-5">
									<?php
									$time = pjTime::factory()
										->attr('name', 'cc_exp_year')
										->attr('id', 'cc_exp_year_' . $rand)
										->attr('class', 'form-control required')
										->prop('left', 0)
										->prop('right', 10);
									if (isset($FORM['cc_exp_year']) && !is_null($FORM['cc_exp_year']))
									{
										$time->prop('selected', $FORM['cc_exp_year']);
									}
									echo $time->year();
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			} 
			if (in_array((int) $tpl['option_arr']['o_bf_include_captcha'], array(2, 3)))
			{
				?>
				<br>
		
				<div class="pjCss-class-heading"><?php __('front_human_verification');?></div><!-- /.pjCss-class-heading -->
		
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php __('front_captcha'); ?> <span><?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? '*' : NULL; ?></span></label>
		
							<input type="text" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-remote="<?php __('front_incorrect_captcha');?>">
  							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-4 -->
		
					<div class="col-sm-4">
						<div class="pjCss-captcha-image">
							<img id="pjCssImage_<?php echo $_GET['index']?>" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . $_GET['session_id'] : NULL;?>" alt="Captcha" style="vertical-align: middle" />
						</div><!-- /.pjCss-captcha-image -->
					</div><!-- /.col-sm-4 -->
				</div><!-- /.row -->
				<?php
			}
			?>
		
			<br>
	
			<div class="pjCss-class-heading"><?php __('front_terms_title');?></div><!-- /.pjCss-class-heading -->
	
			<div class="pjCss-checkbox">
				<label>
					<input type="checkbox" name="terms" value="1" class="required" data-msg-required="<?php __('pj_field_required'); ?>">
			      	<?php __('front_i_read_terms');?>
					<a href="#" class="pjTbModalTrigger" data-toggle="modal" data-target="#pjNcbTermModal" data-title="<?php __('front_terms_title');?>"><?php __('front_read_terms');?></a>
				</label>
				<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
			</div><!-- /.pjCss-checkbox -->
	
			<br>
			<div id="pjCaptchaMsg_<?php echo $_GET['index']?>" class="row" style="display: none;">
				<div class="col-xs-12 text-center">
					<label class="text-danger"><?php __('front_incorrect_captcha');?></label>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-2 col-sm-3 col-xs-10 col-sm-offset-0 col-xs-offset-1">
					<a href="#" class="btn btn-secondary btn-block pjCssBackToClass" data-id="<?php echo $STORE['course_id'];?>"><?php __('front_btn_back');?></a>
				</div><!-- /.col-md-2 -->
	
				<div class="col-md-offset-6 col-sm-offset-5 col-sm-4 col-xs-10 col-xs-offset-1">
					<input type="submit" class="btn btn-primary" value="<?php __('front_btn_preview_booking');?>">
				</div><!-- /.col-sm-4 -->
			</div><!-- /.row -->
			
		</div><!-- /.pjCss-class-footer -->
	</div><!-- /.pjCss-class -->
</form>