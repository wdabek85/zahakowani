<?php

namespace Ilabs\Inpost_Pay\Integration\Shipping;

class ShippingMethodIntegrationFactory {

	public static function create(
		string $iziDeliveryMethodId,
		string $parcelLockerId = null
	): ShippingMethodIntegrationInterface {

		if ( $parcelLockerId ) {

			if ( WoocommerceInpostIntegrationApm::isEasyPack( $iziDeliveryMethodId ) !== false ) {
				return new WoocommerceInpostIntegrationApm( $iziDeliveryMethodId,
					$parcelLockerId );
			}

			if ( WoocommercePaczkomatyInpostIntegrationApm::isEasyPack( $iziDeliveryMethodId ) !== false ) {
				return new WoocommercePaczkomatyInpostIntegrationApm( $iziDeliveryMethodId,
					$parcelLockerId );
			}

		} else {
			if ( WoocommerceInpostIntegration::isEasyPack( $iziDeliveryMethodId ) !== false ) {
				return new WoocommerceInpostIntegration( $iziDeliveryMethodId );
			}

			if ( WoocommercePaczkomatyInpostIntegration::isEasyPack( $iziDeliveryMethodId ) !== false ) {
				return new WoocommercePaczkomatyInpostIntegration( $iziDeliveryMethodId );
			}
		}

		return new GenericIntegration( $iziDeliveryMethodId );
	}
}
