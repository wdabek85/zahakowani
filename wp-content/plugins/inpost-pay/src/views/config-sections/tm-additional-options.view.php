<div class="consent-item">
	<h3 class="mt-2 mb-1 text-bold">
		<?php _e(
			"Additional options:",
			"inpost-pay"
		); ?>
	</h3>
	<table class="net-transport-price-table">
		<tr>
			<td class="form-group form-group--row">
				<p><?php _e("Check shipping method availability", "inpost-pay"); ?></p>
				<div class="toggleWrapper">
					<input class="mobileToggle" type="checkbox" id="izi_check_shipping_availability" name="izi_check_shipping_availability"
						   value="1" <?= get_option(
											 "izi_check_shipping_availability"
										 ) == 1
						? "checked"
						: "" ?>>
					<label for="izi_check_shipping_availability"></label>
				</div>
			</td>
		</tr>
	</table>
</div>
