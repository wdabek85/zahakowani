<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class CreateOrder extends Base
{

    public function attachHook()
    {
        $izi = WooCommerceInPostIzi::getInstance( );
//        add_action( 'woocommerce_new_order', [$izi, 'sendOrder'] );
    }
}
