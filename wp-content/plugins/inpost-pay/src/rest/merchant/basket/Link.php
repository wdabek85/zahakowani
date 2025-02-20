<?php

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\rest\Base;

class Link extends Base {

	protected function describe() {
		add_action( 'wc_ajax_merchant_basket_get_link',
			[ $this, 'merchant_basket_get_link' ] );
	}

	function merchant_basket_get_link() {
		LSCacheHelper::no_cache();
		$binding          = BindingProvider::getBinding();
		$inpost_basket_id = $binding->inpost_basket_id;

		if ( ! $inpost_basket_id ) {

			$result = [
				'link'             => '',
				'inpost_basket_id' => '',
			];

            Logger::debug(
				sprintf( '[Link merchant_basket_get_link] [result: %s]',
					print_r( $result, true )
				) );

			die( json_encode( $result ) );
		}
		$link = InPostIzi::getLinkUrl() . '?basket_id=' . $inpost_basket_id;

		$result = [
			'link'             => $link,
			'inpost_basket_id' => $inpost_basket_id,
		];

        Logger::debug(
			sprintf( '[Link merchant_basket_get_link] [result: %s]',
				print_r( $result, true )
			) );

		die( json_encode( $result ) );
	}
}
