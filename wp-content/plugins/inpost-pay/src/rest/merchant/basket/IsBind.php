<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;



use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\rest\Base;
use WP_REST_Response;

class IsBind extends Base
{

    protected function describe()
    {
        add_action('wc_ajax_inpost_basket_is_bind', [$this, 'inpost_basket_is_bind']);
    }

    function inpost_basket_is_bind()
    {
	    LSCacheHelper::no_cache();
		CartSession::initiateWCCart();
        $storage = InPostIzi::getStorage();

            $binding = BindingProvider::getBinding(true);
            if (isset($binding->basketId)) {
                $object = CartSession::getObjectById($binding->basketId);
                if ($object && $object->redirect_url == 'deleted') {
                    Logger::log("DROP BINDING ON DELETE");
                    BasketIdentification::drop();
                    unset($binding);
                }
            }


        $data = [
            'is_bind' => (int) (isset($binding->basket_linked) && $binding->basket_linked),
            'wc_session' => $storage->getSessionCustomerId(),
        ];
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode($data));
    }
}
