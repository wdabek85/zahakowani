<?php

namespace Ilabs\Inpost_Pay\Lib\config\payment;

use Ilabs\Inpost_Pay\Lib\Authorization;
use Ilabs\Inpost_Pay\Lib\config\ConfigInterface;
use Ilabs\Inpost_Pay\Lib\exception\AuthorizationException;
use Ilabs\Inpost_Pay\Lib\form\AbstractOption;
use Ilabs\Inpost_Pay\Lib\form\exception\NotAllowedConfigOptionException;
use Ilabs\Inpost_Pay\Lib\form\exception\NotFoundConfigOptionException;
use Ilabs\Inpost_Pay\Lib\form\exception\RequiredConfigOptionException;
use Ilabs\Inpost_Pay\Lib\form\FormFieldInterface;
use Ilabs\Inpost_Pay\Lib\form\Select;

final class PaymentMethodsOptions extends AbstractOption implements PaymentMethodsInterface {
	private array $payment_methods = [];
	private array $default_not_selected = [ 'PAY_BY_LINK', 'SHOPPING_LIMIT', 'DEFERRED_PAYMENT' ];

	public function __construct() {
		$this->payment_methods = ( new PaymentMethodsGet() )->get();
		parent::__construct( self::IZI_PAYMENT_METHODS, 'Payment methods' );
	}

	public function register( array $args = [] ): void {
		parent::register();
		if ( $this->get() === false ) {
			if ( ! empty( $this->payment_methods ) ) {
				$this->update( $this->default() );
			} else {
				$this->update( [] );
			}
		}
	}

	public function get( $default = false ) {
		$payment_methods = parent::get();
		if ( is_string( $payment_methods ) ) {
			return [];
		}

		return $payment_methods;
	}


	private function default(): array {
		$paymentMethods = $this->payment_methods;

		foreach ( $paymentMethods as $key ) {
			if ( in_array( $key, $this->default_not_selected, true ) ) {
				unset( $paymentMethods[ $key ] );
			}
		}

		return $paymentMethods;
	}


	/**
	 * @throws RequiredConfigOptionException
	 * @throws NotAllowedConfigOptionException
	 * @throws NotFoundConfigOptionException
	 */
	public function get_form_field(): FormFieldInterface {
		return new Select(
			$this->payment_methods,
			$this->get(),
			[
				'label'        => $this->get_label(),
				'name'         => $this->get_field_name(),
				'label_class'  => 'label-gray',
				'multiple'     => true,
				'value_as_key' => true,
			]
		);
	}

	public function can_show_in_form(): bool {
		$authorization = new Authorization();
		try {
			$authorization->getToken();

			return true;
		} catch ( AuthorizationException $ex ) {
			return false;
		}

	}
}
