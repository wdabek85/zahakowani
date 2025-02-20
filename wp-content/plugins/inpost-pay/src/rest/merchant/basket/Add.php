<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class Add extends Base
{

	protected function describe()
	{
		add_action('wc_ajax_wc_ajax_inpost_add_product', [$this, 'wc_ajax_inpost_add_product']);
	}

	function wc_ajax_inpost_add_product()
	{
		LSCacheHelper::no_cache();
		$productId = absint($_POST['product_id'] ?? 0);;
		$variationId = absint($_POST['variation_id'] ?? 0);
		$items = \WC()->cart->get_cart();
		$found = false;
		foreach ($items as $cart_item_key => $item) {
			if (isset($item['product_id'])) {
				if (($item['product_id']) == $productId) {
						$found = true;
					if($item['variation_id'] > 0 && $item['variation_id'] != $variationId) {
						$found = false;
					}
				}
			}
		}

		if ($variationId > 0) {
			$_POST['product_id'] = $variationId;
		}

		if (!$found) {
			do_action('wc_ajax_add_to_cart');
		}

//        Logger::log('SHOULD ADD AT NEXT LINE');
//        $izi = WooCommerceInPostIzi::getInstance();
//        InPostIzi::blockPut();
//        $izi->basketPut(false, true);
//        $data = $izi->getBasket()->encode();
//        InPostIzi::getCartSessionClass()::setBasketCacheById(BasketIdentification::get(), json_encode(json_decode($data)));

		die (json_encode([
			'basket' => BasketIdentification::get(),
//            'products' => \WC()->cart->get_cart(),
//            'data' => $data
		]));
	}
}
