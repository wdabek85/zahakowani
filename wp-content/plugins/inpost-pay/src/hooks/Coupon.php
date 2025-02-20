<?php

namespace Ilabs\Inpost_Pay\hooks;

class Coupon extends Base {

	public function attachHook() {
		( new \Ilabs\Inpost_Pay\Lib\Coupons\Coupon() )->hooks();
	}
}
