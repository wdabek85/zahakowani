<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class Basket extends Item {

	protected string $browser_id;
	protected Summary $summary;
	protected array $delivery;
	protected array $promo_codes;
	protected array $products;
	protected array $related_products;
	protected array $consents;

//	protected ?MerchantStore $merchant_store = null;

//	protected array $promotions_available;
}
