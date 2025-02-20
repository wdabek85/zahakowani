<?php

namespace Ilabs\Inpost_Pay\Integration\Shipping;

use WC_Order;

abstract class AbstractShippingMethodIntegration {

	protected string $iziDeliveryMethodId;
	protected WC_Order $order;

	public function configure() {

		if ( $this instanceof ParcelLockerIntegrationInterface ) {
			$_POST[ $this->getFormFieldParcelLockerId() ] = $this->getParcelLockerId();
		}
	}

	public function getIziDeliveryMethodId(): string {
		return $this->iziDeliveryMethodId;
	}

	public function setWcOrder( WC_Order $order ) {
		$this->order = $order;
	}

	public function filterTotal( callable $callable ) {
		$shipping_items = $this->order->get_items( 'shipping' );
		$shipping_tax   = 0;


		foreach ( $shipping_items as $shipping_item ) {
			$current_total = $shipping_item->get_total();
			$new_total     = $callable( $current_total );

			$shipping_item->set_total( $new_total );
			$shipping_item->save();
			$shipping_tax = $shipping_item->get_total_tax();

			if ( '0' === (string) get_option( "izi_transport_add_tax" ) ) {
				$shipping_item->set_total( (float) $new_total - (float) $shipping_tax );
				$shipping_item->save();
			}
		}

		if ( $shipping_tax > 0 ) {
			$this->order->set_shipping_tax( $shipping_tax );
		}

		$this->order->save();

		$this->order->update_taxes();
		$this->order->calculate_totals( false );
		$this->order->save();
	}
}
