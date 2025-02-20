<?php
global $post;

use Ilabs\Inpost_Pay\Lib\helpers\HPOSHelper;

$order = new \WC_Order($post->ID);
$HPOSHelper = new HPOSHelper($order)
?>
<style>
	.izi_row td {
		border-bottom: solid 1px #eee;
		padding-bottom: 13px;
	}
</style>
<table style="width:100%">
	<tbody>
	<tr class="izi_row">
		<td>
			<?php _e('Shipment:', 'inpost-pay') ?>
		</td>
		<td>
			<?= $HPOSHelper->get_meta('_easypack_send_method',
				true) == 'parcel_machine' ? __('Inpost Parcel locker', 'inpost-pay') : __('Inpost Courier', 'inpost-pay') ?>
		</td>
	</tr>
	<?php if ($HPOSHelper->get_meta('_easypack_send_method',
			true) == 'parcel_machine'): ?>
		<tr class="izi_row">
			<td>
				<?php _e('Parcel locker:', 'inpost-pay') ?>
			</td>
			<td>
				<?= $HPOSHelper->get_meta('delivery_point', true); ?>
			</td>
		</tr>
	<?php endif ?>

	<?php if ($HPOSHelper->get_meta('izi_payment_type')): ?>
		<tr class="izi_row">
			<td>
				<?php _e('Payment type:', 'inpost-pay') ?>
			</td>
			<td>
				<?= $HPOSHelper->get_meta('izi_payment_type', true); ?>
			</td>
		</tr>
	<?php endif ?>

	<?php if ($HPOSHelper->get_meta('izi_payment_id')): ?>
		<tr class="izi_row">
			<td>
				<?php _e('Payment ID:', 'inpost-pay') ?>
			</td>
			<td>
				<?= $HPOSHelper->get_meta('izi_payment_id', true); ?>
			</td>
		</tr>
	<?php endif ?>

	<?php if ($HPOSHelper->get_meta('izi_payment_reference')): ?>
		<tr class="izi_row">
			<td>
				<?php _e('Payment Reference:', 'inpost-pay') ?>
			</td>
			<td>
				<?= $HPOSHelper->get_meta('izi_payment_reference', true); ?>
			</td>
		</tr>
	<?php endif ?>
	</tbody>
</table>
