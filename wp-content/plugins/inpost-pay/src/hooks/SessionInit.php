<?php


namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\Lib\helpers\CookieHelper;

class SessionInit extends Base {

	public function attachHook(): void {
		add_action( 'woocommerce_set_cart_cookies', array( $this, 'init' ), 10 );
	}

	public function init(): void {
		$expiration = CookieHelper::readSessionExpirationTime();
		CookieHelper::updateIziBasketExpiration( $expiration );
	}
}

