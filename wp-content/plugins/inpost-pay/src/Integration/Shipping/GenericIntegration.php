<?php

namespace Ilabs\Inpost_Pay\Integration\Shipping;

use Ilabs\Inpost_Pay\WooDeliveryPrice;

class GenericIntegration extends AbstractShippingMethodIntegration
	implements ShippingMethodIntegrationInterface {

	public function __construct(
		string $iziDeliveryMethodId
	) {
		$this->iziDeliveryMethodId = $iziDeliveryMethodId;
	}
}
