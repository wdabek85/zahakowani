<?php

namespace Ilabs\Inpost_Pay\Integration\Basket\Availability;

class AvailabilityProductFactory {
	/**
	 * @throws ProductIsEmptyException
	 */
	public function create( $cart_item ) {
		if ( is_plugin_active( 'product-extras-for-woocommerce/product-extras-for-woocommerce.php' ) ) {
			return new ProductExtrasForWoocommerceAvailabilityIntegration( $cart_item );
		}

		return new GenericAvailabilityIntegration( $cart_item );
	}
}
