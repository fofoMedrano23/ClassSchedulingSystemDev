<?php
include_once PJ_VIEWS_PATH . 'pjFrontPublic/elements/header.php';
?>

<div class="pjCss-class">
	<div class="pjCss-class-body">
		<div class="pjCss-class-info">
			<?php
			if(!empty($tpl['arr']['thumb_path']) && file_exists(PJ_INSTALL_PATH . $tpl['arr']['thumb_path']))
			{ 
				?>
				<img src="<?php echo PJ_INSTALL_URL . $tpl['arr']['thumb_path'];?>" class="pjCss-img-right img-responsive" alt="">
				<?php
			} 
			?>
			
			<div class="pjCss-class-heading"><span><?php echo pjSanitize::html($tpl['arr']['title']);?></span></div><!-- /.pjCss-class-heading -->
	
			<div class="pjCss-class-desc"><?php echo nl2br(pjSanitize::html($tpl['arr']['description']));?></div><!-- /.pjCss-class-desc -->
		</div>
		<?php
		if(isset($tpl['teacher_arr']) && !empty($tpl['teacher_arr']))
		{ 
			?>
			<div class="pjCss-teachers">
				<div class="pjCss-teachers-heading"><?php __('front_teachers');?></div><!-- /.pjCss-teachers-heading -->
	
				<?php
				foreach($tpl['teacher_arr'] as $k => $v)
				{ 
					$image_url = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/144x155.png';
					if(!empty($v['image']) && file_exists(PJ_INSTALL_PATH . $v['image']))
					{
						$image_url = PJ_INSTALL_URL . $v['image'];
					}
					?>
					<div class="row">
						<div class="col-sm-3">
							<img src="<?php echo $image_url;?>" class="img-responsive" alt="">
						</div><!-- /.col-sm-3 -->
					
						<div class="col-sm-9">
							<div class="pjCss-teacher-name"><?php echo pjSanitize::html($v['name'])?></div><!-- /.pjCss-teacher-name -->
					
							<div class="pjCss-teacher-desc"><?php echo nl2br(pjSanitize::html($v['description']));?></div><!-- /.pjCss-teacher-desc -->
						</div><!-- /.col-sm-9 -->
					</div><!-- /.row --><!-- /.col-sm-6 -->
					<?php
				} 
				?><!-- /.row -->
			</div><!-- /.pjCss-teachers -->
			<?php
		} else {
			?>
			<div class="pjCss-teachers">
				<div class="pjCss-teachers-heading"><?php __('front_teachers');?></div><!-- /.pjCss-teachers-heading -->
	
				<div class="row">
					<div class="col-sm-12">
						<?php __('front_no_teachers');?>
					</div>
				</div><!-- /.row -->
			</div><!-- /.pjCss-teachers -->
			<?php
		}
		?>
	</div><!-- /.pjCss-class-body -->

	<div class="pjCss-class-footer">
		<div class="row">
			<div class="col-md-5">
				<div class="row">
					<div class="col-sm-4 col-xs-6">
						<label><?php __('front_price')?>:</label>

						<p><strong><?php echo pjUtil::formatCurrencySign($tpl['arr']['price'], $tpl['option_arr']['o_currency']);?></strong></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-4 col-xs-6">
						<label><?php __('front_class_size');?>:</label>

						<p><strong><?php echo pjSanitize::html($tpl['arr']['size']);?></strong></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-4 col-xs-6">
						<label><?php __('front_duration');?>:</label>

						<p><strong><?php echo pjSanitize::html($tpl['arr']['duration']);?></strong></p>
					</div><!-- /.col-sm-4 -->
				</div><!-- /.row -->
			</div><!-- /.col-md-5 -->
			<?php
			if(isset($tpl['class_arr']) && !empty($tpl['class_arr']))
			{ 
				$STORE = $_SESSION[$controller->defaultStore];
				
				$removed_classes = 0;
				$classes_of_course = 0;
				foreach($tpl['class_arr'] as $class)
				{
					$classes_of_course = count($tpl['class_arr']);
					foreach($tpl['class_arr'] as $class)
					{
						if((int)$tpl['arr']['size'] <= (int)$class['booked'])
						{
							$removed_classes++;
						}
					}
				}
				if($removed_classes == 0 || ($removed_classes > 0 && $classes_of_course > $removed_classes))
				{
					?>
					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-6">
								<label><?php __('front_available_from');?>:</label>
		
								<select id="class_id_<?php echo $_GET['index'];?>" name="class_id" class="form-control">
									<?php
									foreach($tpl['class_arr'] as $class)
									{ 
										if((int)$tpl['arr']['size'] >= (int)$class['booked'])
										{
											?>
											<option value="<?php echo $class['id'];?>"<?php echo isset($STORE['class_id']) ? ($STORE['class_id'] == $class['id'] ? ' selected="selected"' : NULL) : NULL;?>><?php echo date($tpl['option_arr']['o_date_format'], strtotime($class['start_date']));?></option>
											<?php
										}
									} 
									?>
								</select>
							</div><!-- /.col-sm-2 -->
		
							<div class="col-sm-6">
								<a href="#" class="btn btn-primary pjCssBtnBook"><?php __('front_btn_book');?></a>
							</div><!-- /.col-sm-2 -->
						</div><!-- /.row -->
					</div><!-- /.col-md-5 -->
					<?php
				}else{
					?>
					<div class="col-md-7">
						<div class="row">
							<label><?php __('front_no_class_available');?></label>
						</div>
					</div>
					<?php
				}
			} 
			?>
		</div><!-- /.row -->
	</div><!-- /.pjCss-class-footer -->
</div><!-- /.pjCss-class -->