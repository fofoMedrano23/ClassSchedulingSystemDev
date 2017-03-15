<div class="pjCss-class">
	<div class="pjCss-class-body">
		<div class="pjCss-class-heading text-center"><span><?php __('front_thank_you');?></span></div><!-- /.pjCss-class-heading -->
		
		<div class="pjCss-booking-thanks">
			<?php
			if (isset($tpl['get']['payment_method']))
			{
				$status = __('front_booking_statuses', true);
				switch ($tpl['get']['payment_method'])
				{
					case 'paypal':
						?><p class="text-success"><?php echo $status[2]; ?></p><?php
						if (pjObject::getPlugin('pjPaypal') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'authorize':
						?><p class="text-success"><?php echo $status[3]; ?></p><?php
						if (pjObject::getPlugin('pjAuthorize') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'bank':
						?><p class="text-success"><?php echo $status[1]; ?></p><?php
						break;
					case 'creditcard':
					case 'cash':
					default:
						?><p class="text-success"><?php echo $status[1]; ?></p><?php
				}
			}
			
			if($tpl['get']['payment_method'] == 'bank' || $tpl['get']['payment_method'] == 'creditcard' || $tpl['get']['payment_method'] == 'cash' || $tpl['option_arr']['o_payment_disable'] == 'Yes') 
			{
				?>
				<input type="button" class="btn btn-primary pjCssBtnStartOver" value="<?php __('front_btn_start_over')?>" />
				<?php
			} 
			?>
		</div><!-- /.pjCss-booking-thanks -->
	</div><!-- /.pjCss-class-body -->
</div>