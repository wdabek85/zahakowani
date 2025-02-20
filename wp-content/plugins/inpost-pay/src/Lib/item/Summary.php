<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class Summary extends Item
{
    protected $basket_base_price;
    protected $basket_final_price;
    protected $basket_promo_price;
    protected $currency;
    protected $basket_expiration_date;
    protected $basket_additional_information;
    protected $payment_type;
    protected $basket_notice;
}
