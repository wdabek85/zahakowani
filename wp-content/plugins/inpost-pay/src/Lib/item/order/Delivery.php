<?php

namespace Ilabs\Inpost_Pay\Lib\item\order;

use Ilabs\Inpost_Pay\item\Price;

class Delivery extends \Ilabs\Inpost_Pay\Lib\Item {

	protected $delivery_type;
	protected $delivery_price;
	protected $delivery_date;
	protected $delivery_options;
	protected $mail;
	protected $phone;
	protected $delivery_point;
	protected $delivery_address;
	protected $courier_note;
}
