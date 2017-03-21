<?php
include_once PJ_VIEWS_PATH . 'pjFrontPublic/elements/header.php';
?>

<form id="pjCssPreviewForm_<?php echo $_GET['index']?>" action="" method="post">
	<input type="hidden" name="css_preview" value="1" />
	<div class="pjCss-class">
		<div class="pjCss-class-body">
			<?php include_once dirname(__FILE__) . '/elements/booking.php';?>
			
		</div><!-- /.pjCss-class-body -->
	
		<div class="pjCss-class-footer">
			<br>
	
			<div class="pjCss-class-heading"><?php __('front_personal_details');?></div><!-- /.pjCss-class-heading -->
	
			<?php
			ob_start();
			$columns = 0;
			if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
			{
				$personal_titles = __('personal_titles', true);
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_title'); ?></label>
	
						<div class="text-muted"><?php echo $personal_titles[$FORM['c_title']];?></div>	
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
						<label><?php __('front_name'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_name']); ?></div>	
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
						<label><?php __('front_email'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_email']); ?></div>	
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
						<label><?php __('front_phone'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_phone']); ?></div>	
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
						<label><?php __('front_company'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_education']); ?></div>	
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
			if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_address'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_address']); ?></div>	
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
						<label><?php __('front_city'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_gender']); ?></div>
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
						<label><?php __('front_state'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_experiencia']); ?></div>
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
			if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_zip'); ?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_zip']); ?></div>	
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
			if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)) && isset($FORM['c_country']) && (int) $FORM['c_country'] > 0)
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_country'); ?></label>
	
						<div class="text-muted"><?php echo $tpl['country_arr']['country_title'];?></div>
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
							<label><?php __('front_notes'); ?></label>
							
							<div class="text-muted"><?php echo nl2br(pjSanitize::html(@$FORM['c_notes'])); ?></div>	
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
				$payment_methods = __('payment_methods', true);
				$cc_types = __('cc_types', true);
				?>
				<div class="pjCss-class-heading"><?php __('front_payment_method')?></div><!-- /.pjCss-class-heading -->
		
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_payment_method')?></label>
							
							<div class="text-muted"><?php echo $payment_methods[$FORM['payment_method']]; ?></div>
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
							
							<div class="text-muted"><?php echo $cc_types[$FORM['cc_type']]; ?></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_num')?></label>
							
							<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_num']); ?></div>
						</div>
					</div>
				</div>
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_code')?></label>
							
							<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_code']); ?></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_exp')?></label>
							<div class="row">
								<div class="col-sm-7">
									<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_exp_month']); ?></div>
								</div>
								<div class="col-sm-5">
									<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_exp_year']); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			} 
			?>
			<div class="row">
				<div class="col-md-2 col-sm-3 col-xs-10 col-sm-offset-0 col-xs-offset-1">
					<a href="#" class="btn btn-secondary btn-block pjCssBackToCheckout"><?php __('front_btn_back');?></a>
				</div><!-- /.col-md-2 -->
	
				<div class="col-md-offset-6 col-sm-offset-5 col-sm-4 col-xs-10 col-xs-offset-1">
					<input type="submit" class="btn btn-primary" value="<?php __('front_btn_confirm');?>">
				</div><!-- /.col-sm-4 -->
			</div><!-- /.row -->
		</div><!-- /.pjCss-class-footer -->
	</div><!-- /.pjCss-class -->
</form>