<?php

namespace Ilabs\Inpost_Pay\Lib\config\payment;

use Ilabs\Inpost_Pay\Lib\Connection;

class PaymentMethodsGet extends Connection {

	public const CACHE_KEY = 'inpost_pay_payment_methods';

	public function get(): array {
		$paymentMethods = get_transient(self::CACHE_KEY);
		if ( $paymentMethods === false ) {
			$paymentMethods = $this->request( 'v1/izi/payment-methods' );
			if ( isset( $paymentMethods->payment_type ) ) {
				set_transient(self::CACHE_KEY, $paymentMethods->payment_type, 60*60*24);

				return $paymentMethods->payment_type;
			}
		} else {
			return $paymentMethods;
		}

		return PaymentMethodsInterface::IZI_PAYMENT_METHODS_DEFAULT;

	}
}
