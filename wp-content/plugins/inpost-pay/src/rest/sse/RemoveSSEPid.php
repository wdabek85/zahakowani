<?php

namespace Ilabs\Inpost_Pay\rest\sse;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\Lib\Storage;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\WooCommerceBasketCache;
use WP_REST_Response;

class RemoveSSEPid extends Base
{
	protected function describe()
	{
		add_action('wc_ajax_remove_sse_pid', [$this, 'remove_sse_pid']);
	}

	function remove_sse_pid()
	{
		LSCacheHelper::no_cache();
		if (file_exists(INPOST_PAY_ORDER_SEE_PID_FILE)) {
			unlink(INPOST_PAY_ORDER_SEE_PID_FILE);
		}
		die();
	}


}
