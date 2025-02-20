<?php

namespace Ilabs\Inpost_Pay\Lib\helpers;

use Ilabs\Inpost_Pay\Lib\config\attribution\AttributionConfig;
use Ilabs\Inpost_Pay\Lib\item\cookie\AdditionalCookies;

class CookieHelper {

	public static function get( $key ): ?string {
		if ( isset( $_COOKIE[ $key ] ) ) {
			return sanitize_text_field( $_COOKIE[ $key ] ) ?? null;
		}

		return null;

	}

	public static function getCookies(): array {
		$cookie = [];

		$cookie_list = self::getCookieHeaderInformation();

		if ( self::get( 'BrowserId' ) ) {
			$cookie['BrowserId'] = self::get( 'BrowserId' );
		}

		if ( self::get( 'woocommerce_items_in_cart' ) ) {
			$cookie['woocommerce_items_in_cart'] = self::get( 'woocommerce_items_in_cart' );
		}

		if ( self::get( 'woocommerce_cart_hash' ) ) {
			$cookie['woocommerce_cart_hash'] = self::get( 'woocommerce_cart_hash' );
		}

		if ( ( new AttributionConfig() )->is_enabled() ) {

			if ( self::get( 'sbjs_session' ) ) {
				$cookie['sbjs_session'] = self::get( 'sbjs_session' );
			}

			if ( self::get( 'sbjs_udata' ) ) {
				$cookie['sbjs_udata'] = self::get( 'sbjs_udata' );
			}

			if ( self::get( 'sbjs_first' ) ) {
				$cookie['sbjs_first'] = self::get( 'sbjs_first' );
			}

			if ( self::get( 'sbjs_current' ) ) {
				$cookie['sbjs_current'] = self::get( 'sbjs_current' );
			}

			if ( self::get( 'sbjs_first_add' ) ) {
				$cookie['sbjs_first_add'] = self::get( 'sbjs_first_add' );
			}

			if ( self::get( 'sbjs_current_add' ) ) {
				$cookie['sbjs_current_add'] = self::get( 'sbjs_current_add' );
			}

			if ( self::get( 'sbjs_migrations' ) ) {
				$cookie['sbjs_migrations'] = self::get( 'sbjs_migrations' );
			}
		}


		foreach ( $_COOKIE as $key => $value ) {
			if ( false !== strpos( $key, "wp_woocommerce_session_" ) ) {
				$cookie[ $key ] = sanitize_text_field( $value );
			}
		}

		$custom_cookies = new AdditionalCookies();

		do_action( 'inpost_pay_store_custom_cookies', $custom_cookies );

		if ( $custom_cookies->cookies ) {
			foreach ( $custom_cookies->cookies as $custom_cookie ) {
				$cookie[ $custom_cookie ] = self::get( $custom_cookie );
			}
		}

		return $cookie;
	}

	public static function setIziBasket(): void {
		if ( ! headers_sent() ) {
			setcookie( 'izi_basket', self::generateIziBasket(), [
				'expires'  => self::readSessionExpirationTime(),
				'secure'   => true,
				'httpOnly' => true,
				'domain'   => parse_url( home_url(), PHP_URL_HOST ),
				'sameSite' => 'strict',
				'path'     => '/',
			] );
		}
	}

	public static function updateIziBasketExpiration( $expires ): void {
		if ( ! headers_sent() && self::get( 'izi_basket' ) ) {
			setcookie( 'izi_basket', self::get( 'izi_basket' ), [
				'expires'  => $expires,
				'secure'   => true,
				'httpOnly' => true,
				'domain'   => parse_url( home_url(), PHP_URL_HOST ),
				'sameSite' => 'strict',
				'path'     => '/',
			] );
		}
	}

	public static function generateIziBasket(): string {
		return wp_generate_uuid4();
	}

	public static function getCookieHeaderInformation(): array {
		$headers     = headers_list();
		$cookie_list = [];
		foreach ( $headers as $header ) {
			if ( strpos( $header, 'Set-Cookie:' ) === 0 ) {
				$cookie                         = self::parse( $header );
				$cookie_list[ $cookie['name'] ] = $cookie;
			}
		}

		return $cookie_list;
	}

	public static function parse( $cookieHeader ): ?array {
		if ( empty( $cookieHeader ) ) {
			return null;
		}

		if ( \preg_match( '/^Set-Cookie: (.*?)=(.*?)(?:; (.*?))?$/i', $cookieHeader, $matches ) ) {
			$cookie['name']      = $matches[1];
			$cookie['path']      = null;
			$cookie['http_only'] = 'false';
			$cookie['value']     = \urldecode( $matches[2] );
			$cookie['same_site'] = null;

			if ( count( $matches ) >= 4 ) {
				$attributes = \explode( '; ', $matches[3] );

				foreach ( $attributes as $attribute ) {
					if ( strcasecmp( $attribute, 'HttpOnly' ) === 0 ) {
						$cookie['http_only'] = 'true';
					} elseif ( strcasecmp( $attribute, 'Secure' ) === 0 ) {
						$cookie['secure'] = 'true';
					} elseif ( stripos( $attribute, 'Expires=' ) === 0 ) {
						$cookie['expires'] = (int) strtotime( substr( $attribute, 8 ) );
					} elseif ( stripos( $attribute, 'Domain=' ) === 0 ) {
						$cookie['domain'] = substr( $attribute, 7 );
					} elseif ( stripos( $attribute, 'Path=' ) === 0 ) {
						$cookie['path'] = substr( $attribute, 5 );
					} elseif ( stripos( $attribute, 'SameSite=' ) === 0 ) {
						$cookie['same_site'] = substr( $attribute, 9 );
					}
				}
			}

			return $cookie;
		}

		return null;
	}

	public static function get_from_header( string $name ): ?array {
		$cookies = self::getCookieHeaderInformation();

		return $cookies[ $name ] ?? null;
	}

	public static function readSessionExpirationDate() {
		if ( isset( WC()->session ) ) {
			$cookie = WC()->session->get_session_cookie();
			if ( isset( $cookie[1] ) ) {
				return date( DateHelper::DATE_API_FORMAT, WC()->session->get_session_cookie()[1] );
			}
		}

		return date( DateHelper::DATE_API_FORMAT, strtotime( '+2 days' ) );
	}

	public static function readSessionExpirationTime() {
		if ( isset( WC()->session ) ) {
			$cookie = WC()->session->get_session_cookie();
			if ( isset( $cookie[1] ) ) {
				return WC()->session->get_session_cookie()[1];
			}
		}

		return strtotime( '+2 days' );
	}
}
