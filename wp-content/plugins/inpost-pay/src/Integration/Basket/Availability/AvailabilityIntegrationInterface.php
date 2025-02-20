<?php

namespace Ilabs\Inpost_Pay\Integration\Basket\Availability;

interface AvailabilityIntegrationInterface {
	public function __construct( array $cart_item);

	public function checkAvailability();
}
