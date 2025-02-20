<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\rest\Base;
use WP_REST_Response;

class Confirmation extends Base {

	protected function describe() {
		$this->get['/inpost/v1/izi/merchant/basket/confirmation'] = function (
			$request
		) {
			$start    = time();
			$id       = BasketIdentification::get();
			$response = [];
                // The site is connecting as SSE
                $this->sseHeaders();
                $this->sendHelloMessage();
                $sleepTime = get_option('izi_sse_sleep_time')*1000000;
                Logger::log_headers_sent();
                while (1) {
                    if ( connection_aborted() || time() - $start > 10 ) {
                        break;
                    }

                    $response = InpostPay::getInstance()
                        ->getLib()
                        ->getController()
                        ->basketBindingGetInterval( $id, 1 );


                    if ( ! empty( $response ) ) {
                        InPostIzi::getStorage()->sessionClose();
                        $this->sendEventMessage( 'message', $response );
                        Logger::debug(
                            sprintf( '[Confirmation sendEventMessage] [message: %s]',
                                print_r( $response, true )
                            ) );
                        Logger::log_headers_sent();

                    } else {
                        $this->sendEventMessage('time', []);
                    }

                    usleep($sleepTime);
                }
                die();

		};
	}
}
