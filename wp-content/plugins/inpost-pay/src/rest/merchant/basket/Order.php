<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\hooks\BasketChange;
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

class Order extends Base
{
	protected function describe()
	{
		add_action('wc_ajax_merchant_order_confirmation_get', [$this, 'merchant_order_confirmation_get']);
	}

	function merchant_order_confirmation_get()
	{
		LSCacheHelper::no_cache();
		$id = BasketIdentification::get();
		if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/event-stream') !== false) {
			// The site is connecting as SSE
			$start = time();
			$response = [];
			$this->sseHeaders();
			$this->sendHelloMessage();
			if ($this->checkEventRunning()) {
				$this->sendEventMessage('message', ['error' => 'Event already running']);
				exit();
			} else {
				$this->createPidFile();
			}
			$sleepTime = get_option('izi_sse_sleep_time')*1000000;
			do {
				$this->flushMessage();
				if (connection_aborted() || time() - $start > 100) {
					$this->deletePidFile();
					exit();
				}
				$response = InpostPay::getInstance()->getLib()->getController()->orderGetInterval($id);
				if ($response) {
					BasketChange::$BLOCK_ACTION_SET = true;
					WooCommerceBasketCache::restore(BasketIdentification::get());
					usleep(2000);
				}
				$this->sendEventMessage('message', $response);
				if ($response) {
					$this->deletePidFile();
					exit();
				}

				usleep($sleepTime);
			} while (empty($response));

			if (is_user_logged_in() && !empty($response['action']) && $response['action'] == 'refresh') {
				InPostIzi::blockPut();
				Logger::log('ACTION REFRESH FOR LOGGED IN');
				WooCommerceBasketCache::restore($id);
			}

			if (!empty($response['action']) && $response['action'] == 'delete') {
				BasketIdentification::drop();
			}

			$this->sendEventMessage('message', $response);
			$this->deletePidFile();
			exit();
		}


		$response = InpostPay::getInstance()->getLib()->getController()->orderGetInterval($id);

		if (is_user_logged_in() && !empty($response['action']) && $response['action'] == 'refresh') {
			InPostIzi::blockPut();
			Logger::log('ACTION REFRESH FOR LOGGED IN');
			WooCommerceBasketCache::restore($id);
		}

		if (!empty($response['action']) && $response['action'] == 'delete') {
			BasketIdentification::drop();
		}

		$this->sendEventMessage('message', $response);
		$this->deletePidFile();
		die($response);

	}

	function checkEventRunning(): bool
	{
		if (file_exists(INPOST_PAY_ORDER_SEE_PID_FILE)) {
			$time = time() - filemtime(INPOST_PAY_ORDER_SEE_PID_FILE);
			if ($time > 90) {
				$this->deletePidFile();
				return false;
			}
			$pid = file_get_contents(INPOST_PAY_ORDER_SEE_PID_FILE);
			if (posix_getsid($pid)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function createPidFile() {
		$pid = getmypid();
		file_put_contents(INPOST_PAY_ORDER_SEE_PID_FILE, $pid);
	}

	function deletePidFile()
	{
		unlink(INPOST_PAY_ORDER_SEE_PID_FILE);
	}

}
