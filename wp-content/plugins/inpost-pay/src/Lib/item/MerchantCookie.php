<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\exception\CookieEmptyValueException;
use Ilabs\Inpost_Pay\Lib\helpers\CookieHelper;
use Ilabs\Inpost_Pay\Lib\Item;

class MerchantCookie extends Item {
	protected string $key = 'wp_woocommerce_session_';

	protected string $value;

	protected string $path = '/';

	protected string $domain;

	protected ?bool $secure = true;

	protected ?bool $http_only = false;

	protected ?string $same_site = 'NONE';

	protected ?string $priority = 'MEDIUM';

//	protected int $max_age = 0;

	protected string $expires = '';


	/**
	 * @throws CookieEmptyValueException
	 */
	public function wp_woocommerce_session(): MerchantCookie {
		foreach ( $_COOKIE as $key => $value ) {
			if ( false !== strpos( $key, "wp_woocommerce_session_" ) ) {
				$cookie    = CookieHelper::get_from_header( $key );
				$this->key = $key;
			}
		}

		if ( isset( $cookie ) ) {
			$this->value  = CookieHelper::get( $this->key );
			$this->path   = $cookie['path'] ?? '/';
			$this->domain = 'https://' . parse_url( home_url(), PHP_URL_HOST );
//			$this->secure    = (bool) $cookie['secure'];
//			$this->http_only = (bool) $cookie['http_only'];
		} else {
			$cookie       = CookieHelper::get( $this->key );
			$this->value  = $cookie;
			$this->domain = 'https://' . parse_url( home_url(), PHP_URL_HOST );
		}


		$this->expires = CookieHelper::readSessionExpirationDate();

		if ( empty( $this->value ) ) {
			throw new CookieEmptyValueException();
		}

		return $this;
	}


}
