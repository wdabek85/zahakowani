<?php

namespace Ilabs\Inpost_Pay\rest\basket;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\WooCommerceBasket;

class Confirmation extends Base
{
    public function __construct()
    {
        $this->restricted = true;
    }

    protected function describe()
    {
        $this->post['/inpost/v1/izi/basket/(?P<id>[a-zA-Z0-9-]+)/confirmation'] = function ($request) {

            $this->checkSignature($request);

            $content = $request->get_body();
            $id = $request->get_param('id');

            Logger::response($content);

            $status = json_decode($content, true)['status'];
            if ($status == 'REJECT') {
                die (json_encode(['STATUS' => 'REJECT']));
            }

//            CartSession::setSessionByCartId($id);
            CartSession::setConfirmationToCart($id, $content);
            $basket = CartSession::getBasketCacheById($id);
            $basket = str_replace('\/', '/', mb_convert_encoding($basket, 'UTF-8'));
//            $basket = WooCommerceBasket::getBasket()->encode();
            Logger::response($basket);
            header('content-type: application/json');
            die($basket);
        };
    }
}
