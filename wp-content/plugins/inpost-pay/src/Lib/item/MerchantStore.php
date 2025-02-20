<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\exception\CookieEmptyValueException;
use Ilabs\Inpost_Pay\Lib\Item;

class MerchantStore extends Item {

	protected string $url;

	protected array $cookies;

	/**
	 * @throws CookieEmptyValueException
	 */
	public function __construct() {
		$shop_url = get_permalink( wc_get_page_id( 'shop' ) );
		if ( empty( $shop_url ) ) {
			$shop_url = home_url();
		}

		$this->url = $shop_url;

		$cookie = (new MerchantCookie())->wp_woocommerce_session();

		$this->cookies = [ $cookie ];

	}

}
