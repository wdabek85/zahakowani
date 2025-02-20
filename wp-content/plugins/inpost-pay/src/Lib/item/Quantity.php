<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class Quantity extends Item
{
    protected $quantity;
    protected $quantity_type;
    protected $quantity_unit;
    protected $available_quantity;
    protected $max_quantity;
	protected $quantity_jump = 1;
}
