<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\Lib\helpers\HPOSHelper;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class OrderUpdate extends Base {

	public static $block = false;

	public function attachHook() {
		$hook = function ( $id, $meta_key, $trackingId ) {
			if ( self::$block ) {
				return;
			}
			if ( $meta_key !== '_easypack_parcel_tracking' || ! $trackingId ) {
				return;
			}
			$hposHelper     = new HPOSHelper( $id );
			$iziPaymentType = $hposHelper->get_meta( 'izi_payment_type', true );
			if ( ! $iziPaymentType ) {
				return;
			}

			$data = $hposHelper->get_meta( 'inpost_consents', true );
			if ( ! $data ) {
				return;
			}

			$izi = WooCommerceInPostIzi::getInstance();

			$order         = wc_get_order( $id );
			$status        = $order->get_status();
			$status_labels = get_option( 'izi_status_map' );
			$status        = ( ! empty( $status_labels[ 'wc-' . $status ] ) ) ? $status_labels[ 'wc-' . $status ] : $status;

			$refList = [ $trackingId ];
			$izi->orderEvent( $id, $status, $refList );
		};

		$changeStatusHook = function ( $id, $status_transition_from, $status_transition_to ) {
			if ( self::$block ) {
				return;
			}
			$hposHelper     = new HPOSHelper( $id );
			$iziPaymentType = $hposHelper->get_meta( 'izi_payment_type', true );
			if ( ! $iziPaymentType ) {
				return;
			}
			$izi = WooCommerceInPostIzi::getInstance();

			$status_labels = get_option( 'izi_status_map' );
			$status        = ( ! empty( $status_labels[ 'wc-' . $status_transition_to ] ) ) ? $status_labels[ 'wc-' . $status_transition_to ] : $status_transition_to;
			$order_status  = '';

			$orderPayment_status = $hposHelper->get_meta( 'izi_payment_status', true );
			$izi_order_status    = $hposHelper->get_meta( 'izi_order_status', true );
			if ( $orderPayment_status !== 'AUTHORIZED' && $izi_order_status !== 'ORDER_COMPLETED' ) {
				if ( ( $status_transition_from === 'pending'
				       || $status_transition_from === 'awaiting-payment'
				       || $status_transition_from === 'on-hold' )
				     && $status_transition_to === 'cancelled'
				) {
					$order_status = 'ORDER_REJECTED';

				}

				if ( $status_transition_to === 'completed' && $izi_order_status !== 'ORDER_REJECTED' ) {
					$order_status = 'ORDER_COMPLETED';
				}
			}

			if ( $orderPayment_status === 'AUTHORIZED' && 'processing' === $status_transition_to &&
			     in_array( $status_transition_from, [ 'pending', 'awaiting-payment', 'on-hold' ] ) ) {
				$order_status = 'ORDER_PROCESSING';
			}
			$hposHelper->update_meta( 'izi_order_status', $order_status );


			$trackingId = $hposHelper->get_meta( '_easypack_parcel_tracking', true ) ?: '';
			$refList    = [ $trackingId ];

			Logger::log('Order status changed from ' . $status_transition_from . ' to ' . $status_transition_to .', technical order status: ' . $order_status);

			$izi->orderEvent( $id, $status, $refList, $order_status );

		};

		add_action( 'add_post_meta', $hook, 1, 4 );
		add_action( 'woocommerce_order_status_changed', $changeStatusHook, 1, 4 );
	}
}
