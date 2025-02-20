<?php

namespace Ilabs\Inpost_Pay\rest\basket;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\Base;

class Delete extends Base
{
    public function __construct()
    {
        $this->restricted = true;
    }

    protected function describe()
    {
        $this->delete['/inpost/v1/izi/basket/(?P<id>[a-zA-Z0-9-]+)/binding'] = function ($request) {

            $this->checkSignature($request);

            try {
                $id = $request->get_param('id');
                Logger::response('200');
                CartSession::deleteByCartId($id);
            } catch (\Exception $e) {
            }
            return json_encode(['success' => true]);
        };
    }
}
