<?php
$STORE = $_SESSION[$controller->defaultStore];
$FORM = isset($_SESSION[$controller->defaultForm]) ? $_SESSION[$controller->defaultForm] : array(); 
?>
<div class="pjCss-class-heading"><?php __('front_your_booking');?></div><!-- /.pjCss-class-heading -->
			
<div class="pjCss-booking-row">
	<div class="row">
		<div class="col-sm-5">
			<p><?php echo pjSanitize::html($tpl['arr']['title'])?></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-5">
			<p><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['start_date']));?> - <?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['class_arr']['end_date']));?></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-2">
			<p class="text-right"><strong><?php echo pjUtil::formatCurrencySign($tpl['arr']['price'], $tpl['option_arr']['o_currency']);?></strong></p>
		</div><!-- /.col-sm-2 -->
	</div><!-- /.row -->
</div><!-- /.pjCss-booking-row -->

<div class="pjCss-booking-row">
	<div class="row">
		<div class="col-sm-5">
			<p><small><?php __('front_subtotal')?>:</small></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-5">
			
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-2">
			<p class="text-right"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['subtotal'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
		</div><!-- /.col-sm-2 -->
	</div><!-- /.row -->
</div><!-- /.pjCss-booking-row -->
<div class="pjCss-booking-row">
	<div class="row">
		<div class="col-sm-5">
			<p><small><?php __('front_tax')?>:</small></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-5">
			
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-2">
			<p class="text-right"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'],2), $tpl['option_arr']['o_currency']);?></strong></p>
		</div><!-- /.col-sm-2 -->
	</div><!-- /.row -->
</div><!-- /.pjCss-booking-row -->
<div class="pjCss-booking-row">
	<div class="row">
		<div class="col-sm-5">
			<p><small><?php __('front_total')?>:</small></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-5">
			
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-2">
			<p class="text-right"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'],2), $tpl['option_arr']['o_currency']);?></strong></p>
		</div><!-- /.col-sm-2 -->
	</div><!-- /.row -->
</div><!-- /.pjCss-booking-row -->
<div class="pjCss-booking-row">
	<div class="row">
		<div class="col-sm-5">
			<p><small><?php __('front_deposit')?>:</small></p>
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-5">
			
		</div><!-- /.col-sm-5 -->

		<div class="col-sm-2">
			<p class="text-right"><strong><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']);?></strong></p>
		</div><!-- /.col-sm-2 -->
	</div><!-- /.row -->
</div><!-- /.pjCss-booking-row -->