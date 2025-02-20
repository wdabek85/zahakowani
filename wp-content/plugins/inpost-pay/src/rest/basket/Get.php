<?php
namespace Ilabs\Inpost_Pay\rest\basket;

use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\Lib\Storage;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\WooCommerceBasket;

class Get extends Base
{
    public function __construct()
    {
        $this->restricted = true;
    }

    protected function describe()
    {
        $this->get['/inpost/v1/izi/basket/(?P<id>[a-zA-Z0-9-]+)'] = function ($request) {

            $this->checkSignature($request);

            $id = $request->get_param('id');
            CartSession::setSessionByCartId($id);
            $basket = CartSession::getBasketCacheById($id);
            $basket = str_replace('\/', '/', mb_convert_encoding($basket, 'UTF-8'));
//            $basket = WooCommerceBasket::getBasket()->encode();
	        Logger::log('###GET BASKET###');
            Logger::response($basket);
            header('content-type: application/json');
            die($basket);
        };
    }
}
