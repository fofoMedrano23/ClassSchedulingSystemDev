<?php
include_once PJ_VIEWS_PATH . 'pjFrontPublic/elements/header.php';

if(!empty($tpl['arr']))
{
	foreach($tpl['arr'] as $k => $v)
	{
		$removed_classes = 0;
		$classes_of_course = 0;
		if(isset($tpl['class_arr'][$v['id']]) && !empty($tpl['class_arr'][$v['id']]))
		{
			$classes_of_course = count($tpl['class_arr'][$v['id']]);
			foreach($tpl['class_arr'][$v['id']] as $class)
			{
				if((int)$v['size'] <= (int)$class['booked'])
				{
					$removed_classes++;
				}
			}
		}
		if($removed_classes == 0 || ($removed_classes > 0 && $classes_of_course > $removed_classes))
		{
			?>
			<div class="pjCss-class">
				<div class="pjCss-class-body">
					<?php
					if(!empty($v['thumb_path']) && file_exists(PJ_INSTALL_PATH . $v['thumb_path']))
					{ 
						?>
						<a href="#" class="pjCssViewDetails"  data-id="<?php echo $v['id'];?>"><img src="<?php echo PJ_INSTALL_URL . $v['thumb_path'];?>" class="pjCss-img-left img-responsive" alt=""></a>
						<?php
					} 
					?>
	
					<div class="pjCss-class-heading"><a href="#" class="pjCssViewDetails"  data-id="<?php echo $v['id'];?>"><?php echo pjSanitize::html($v['title']);?></a></div><!-- /.pjCss-class-heading -->
	
					<div class="pjCss-class-desc"><?php echo nl2br(pjSanitize::html($v['description']));?></div><!-- /.pjCss-class-desc -->
				</div><!-- /.pjCss-class-body -->
	
				<div class="pjCss-class-footer">
					<div class="row">
						<div class="col-md-5">
							<div class="row">
								<div class="col-sm-4 col-xs-6">
									<label><?php __('front_price')?>:</label>
	
									<p><strong><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></strong></p>
								</div><!-- /.col-sm-4 -->
	
								<div class="col-sm-4 col-xs-6">
									<label><?php __('front_class_size');?>:</label>
	
									<p><strong><?php echo pjSanitize::html($v['size']);?></strong></p>
								</div><!-- /.col-sm-4 -->
	
								<div class="col-sm-4 col-xs-6">
									<label><?php __('front_duration');?>:</label>
	
									<p><strong><?php echo pjSanitize::html($v['duration'])?></strong></p>
								</div><!-- /.col-sm-4 -->
							</div><!-- /.row -->
						</div><!-- /.col-md-5 -->
						<?php
						if(isset($tpl['class_arr'][$v['id']]) && !empty($tpl['class_arr'][$v['id']]))
						{ 
							?>
							<div class="col-md-7">
								<div class="row">
									<div class="col-sm-6">
										<label><?php __('front_available_from');?>:</label>
		
										<select name="class_id_<?php echo $v['id'];?>" class="form-control">
											<?php
											foreach($tpl['class_arr'][$v['id']] as $class)
											{ 
												if((int)$v['size'] >= (int)$class['booked'])
												{
													?>
													<option value="<?php echo $class['id'];?>"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($class['start_date']));?></option>
													<?php
												}
											} 
											?>
										</select>
									</div><!-- /.col-sm-2 -->
		
									<div class="col-sm-6">
										<a href="#" class="btn btn-primary pjCssViewDetails" data-id="<?php echo $v['id'];?>"><?php __('front_btn_view_and_book');?></a>
									</div><!-- /.col-sm-2 -->
								</div><!-- /.row -->
							</div><!-- /.col-md-5 -->
							<?php
						} 
						?>
					</div><!-- /.row -->
				</div><!-- /.pjCss-class-footer -->
			</div><!-- /.pjCss-class -->
			<?php
		}
	}
}else{
	?><label><?php __('front_no_classes_found');?></label><?php
} 
?>