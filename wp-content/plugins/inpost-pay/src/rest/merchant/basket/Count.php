<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\Base;
class Count extends Base
{

	protected function describe()
	{
		add_action('wc_ajax_inpost_count_basket', [$this, 'wc_ajax_inpost_count_basket']);
	}

	function wc_ajax_inpost_count_basket()
	{
		LSCacheHelper::no_cache();
		CartSession::initiateWCCart();
		$count = \WC()->cart->get_cart_contents_count();

		die (json_encode([
			'count' => $count,
		]));
	}
}
