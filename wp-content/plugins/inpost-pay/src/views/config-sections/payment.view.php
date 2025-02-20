<div class="input-wrapper mt-2 mb-2">
	<div class="form-group form-group--row">
		<div class="input-tooltip">
			<label class="label-gray">
				<?php

				_e(
					"Enable payments in accordance with the signed agreement with Aion",
					"inpost-pay"
				); ?>
			</label>
			<div class="input-tooltip-wrapper">
				<img src="<?php echo plugin_dir_url(
										 __FILE__
									 ) .
									 "../../../assets/img/tooltip.svg"; ?>" alt="">
				<div class="input-tooltip-box">
					<p>
						<?php _e(
							"Payment methods have been specified in the payment gateway service agreement",
							"inpost-pay"
						); ?>
					</p>
				</div>
			</div>
		</div>
		<input <?= esc_attr(
					   get_option( "izi_payment_aion", 1 )
				   ) == 1
			? "checked"
			: "" ?> type="checkbox" name="izi_payment_aion" value="1">
	</div>
	<div class="form-group form-group--row">
		<div class="input-tooltip">
			<label class="label-gray">
				<?php _e(
					"Enable payment on delivery according to the signed agreement with InPost",
					"inpost-pay"
				); ?>
			</label>
			<div class="input-tooltip-wrapper">
				<img src="<?php echo plugin_dir_url(
										 __FILE__
									 ) .
									 "../../../assets/img/tooltip.svg"; ?>" alt="">
				<div class="input-tooltip-box">
					<p>
						<?php
						_e(
							"Cash on delivery payment will be available only if you have a signed agreement
                        with InPost to provide this service in your store",
							"inpost-pay"
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<input <?= esc_attr(
					   get_option( "izi_payment_inpost" )
				   ) == 1
			? "checked"
			: "" ?> type="checkbox" name="izi_payment_inpost" value="1">
	</div>
</div>
<?php $payment_method_options = new \Ilabs\Inpost_Pay\Lib\config\payment\PaymentMethodsOptions(); ?>
	<?php if ($payment_method_options->can_show_in_form()) : ?>
	<?php $payment_method_options_field = $payment_method_options->get_form_field(); ?>
	<div class="form-group">
		<div class="input-tooltip">
			<?php $payment_method_options_field->print_label(); ?>
			<div class="input-tooltip-wrapper">
				<img src="<?php echo plugin_dir_url(
										 __FILE__
									 ) .
									 "../../../assets/img/tooltip.svg"; ?>" alt="">
				<div class="input-tooltip-box">
					<p>
						<?php
						_e(
							"Select available payment methods for your store",
							"inpost-pay"
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<?php $payment_method_options_field->print_field(); ?>
	</div>
	<?php endif; ?>
