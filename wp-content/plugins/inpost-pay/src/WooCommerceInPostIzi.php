<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Lib\Storage;

class WooCommerceInPostIzi extends InPostIzi
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBasket()
    {
        return WooCommerceBasket::getBasket();
    }

    public function getOrder()
    {
        return WooCommerceOrder::getOrder();
    }
}
