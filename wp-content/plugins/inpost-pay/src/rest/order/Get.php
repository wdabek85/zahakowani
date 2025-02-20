<?php

namespace Ilabs\Inpost_Pay\rest\order;

use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\WooCommerceOrder;

class Get extends Base
{
    public function __construct()
    {
        $this->restricted = true;
    }

    protected function describe()
    {
        $this->get['/inpost/v1/izi/order/(?P<id>[a-zA-Z0-9-]+)'] = function ($request) {

            $this->checkSignature($request);

            try {
                $oid = $request->get_param('id');
                $order = WooCommerceOrder::getOrder($oid);
                if (!$order) {
                    throw new \Exception('Order not found');
                }
                $order = $order->encode();
                Logger::response($order);
                die(mb_convert_encoding($order, 'UTF-8'));
            } catch (\Exception $e) {
                http_response_code(404);
                die(json_encode([
                    "error_code" => "ORDER_READ_FAILURE",
                    "error_message" => 'ORDER NOT FOUND'
                ]));
            }
        };
    }
}
