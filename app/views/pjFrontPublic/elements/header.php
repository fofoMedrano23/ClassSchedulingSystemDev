<div class="pjCss-head">
	<div class="row">
		<div class="col-sm-5 col-xs-12">
			<a href="#" class="btn btn-default pjCssHome"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a>
			<select class="form-control pjCssMenuNav">
				<?php
				switch ($_GET['action']) {
					case 'pjActionClasses':
						?>
						<option value="loadClasses" selected="selected"><?php __('front_step_1');?></option>
						<option value="loadClass" disabled><?php __('front_step_2');?></option>
						<option value="loadCheckout" disabled><?php __('front_step_3');?></option>
						<option value="loadPreview" disabled><?php __('front_step_4');?></option>
						<?php
					;
					break;
					
					case 'pjActionClass':
						?>
						<option value="loadClasses"><?php __('front_step_1');?></option>
						<option value="loadClass" selected="selected"><?php __('front_step_2');?></option>
						<option value="loadCheckout" disabled><?php __('front_step_3');?></option>
						<option value="loadPreview" disabled><?php __('front_step_4');?></option>
						<?php
					;
					break;
					
					case 'pjActionCheckout':
						$STORE = $_SESSION[$controller->defaultStore];
						?>
						<option value="loadClasses"><?php __('front_step_1');?></option>
						<option value="loadClass/id:<?php echo $STORE['course_id'];?>"><?php __('front_step_2');?></option>
						<option value="loadCheckout" selected="selected"><?php __('front_step_3');?></option>
						<option value="loadPreview" disabled><?php __('front_step_4');?></option>
						<?php
					;
					break;
				
					case 'pjActionPreview':
						$STORE = $_SESSION[$controller->defaultStore];
						?>
						<option value="loadClasses"><?php __('front_step_1');?></option>
						<option value="loadClass/id:<?php echo $STORE['course_id'];?>"><?php __('front_step_2');?></option>
						<option value="loadCheckout"><?php __('front_step_3');?></option>
						<option value="loadPreview" selected="selected"><?php __('front_step_4');?></option>
						<?php
					;
					break;
				} 
				?>
			</select>
		</div>
		<?php
		if($_GET['action'] == 'pjActionClasses')
		{
			if(!empty($tpl['arr']))
			{ 
				?>
				<div class="col-sm-offset-2 col-sm-5 col-xs-12">
					<div class="row">
						<div class="col-sm-5">
							<label class="pjCssOrderTitle"><?php __('front_order_by');?>:</label>
						</div><!-- /.col-sm-4 -->
			
						<div class="col-sm-7">
							<select name="order_by" class="form-control pjCssOrderBy">
								<option value="date"<?php echo isset($_GET['order']) ? ($_GET['order'] == 'date' ? ' selected="selected"' : NULL) : NULL;?>><?php __('front_starting_date_asc')?></option>
								<option value="size"<?php echo isset($_GET['order']) ? ($_GET['order'] == 'size' ? ' selected="selected"' : NULL) : NULL;?>><?php __('front_class_size_asc')?></option>
								<option value="price"<?php echo isset($_GET['order']) ? ($_GET['order'] == 'price' ? ' selected="selected"' : NULL) : NULL;?>><?php __('front_price_asc')?></option>
							</select>
						</div><!-- /.col-sm-8 -->
					</div><!-- /.row -->
				</div>
				<?php
			}
		} 
		?>
	</div>
</div><!-- /.pjCss-head -->