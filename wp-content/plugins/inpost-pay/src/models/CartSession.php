<?php

namespace Ilabs\Inpost_Pay\models;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;

use Ilabs\Inpost_Pay\Lib\helpers\CookieHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\Lib\interfaces\ICartSession;
use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\WooCommerceBasket;
use Ilabs\Inpost_Pay\WooCommerceBasketCache;
use JsonException;
use function WC;

/**
 * @property string $session_id
 * @property string $cart_id
 * @property int $order_id
 * @property string $redirect_url
 * @property string $wc_cart_session
 * @property string $session_expiry
 * @property ?string $action
 * @property ?int $coupons
 * @property ?string $confirmation_response
 * @property ?string $redirect_response
 * @property ?string $izi_basket
 */
class CartSession extends Base implements ICartSession {

	protected $table = 'izi_cart_session';
	protected $className = 'Ilabs\Inpost_Pay\models\CartSession';
	private static array $cache = [];

	public static function instance(): CartSession {
		$data = new \stdClass();

		return new self( $data );
	}

	/**
	 * @throws JsonException
	 */
	public static function storeCurrent(): void {
		$model  = self::instance();
		$cartId = BasketIdentification::get();
		$fromDb = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( $fromDb->hasSet( 'id' ) ) {
			$model = $fromDb;
		}
		$model->session_id      = json_encode( CookieHelper::getCookies(), JSON_THROW_ON_ERROR );
		$model->wc_cart_session = WC()->session->get_customer_id();
		$model->session_expiry  = time() + (int) apply_filters( 'wc_session_expiring', 60 * 60 * 47 );
		$model->cart_id         = $cartId;
		$model->izi_basket = CookieHelper::get('izi_basket');
		$model->save( [ 'session_id', 'wc_cart_session', 'session_expiry', 'cart_id', 'izi_basket' ] );
	}

	public static function forceBasketStore(): void {
		self::initiateWCCart();
		$basket = WooCommerceBasket::getBasket()->encode();
		self::setBasketCacheById( BasketIdentification::get(), $basket );
		self::setBasketCachedById( BasketIdentification::get() );
	}

	/**
	 * @throws JsonException
	 */
	public static function setSessionByCartId( $id ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $id,
		] );
		if ( ! $model->id ) {
			die( json_encode( '[]', JSON_THROW_ON_ERROR ) );
		}

		if ( self::getWCCartSession( $id ) !== WC()->session->get_customer_id() ) {
			Logger::log('Restore session for ' . $id);
			InPostIzi::getStorage()->destroySession();
			if ( WC()->session instanceof \WC_Session) {
				WC()->session = null;
			}
			if (WC()->cart instanceof \WC_Cart) {
				WC()->cart = null;
			}

			unset( $_COOKIE );
			$_COOKIE = self::get_session_id( $id );
			self::initiateWCCart();

			if ( method_exists( WC()->session, 'get_session_data' ) ) {
				WC()->session->get_session_data();
			}

			if ( method_exists( WC()->cart, 'get_cart_for_session' ) ) {
				WC()->cart->get_cart_for_session();
			}

			if ( method_exists( WC()->cart, 'get_cart_from_session' ) ) {
				WC()->cart->get_cart_from_session();
			}
		}
	}

	public static function deleteByCartId( $id ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $id,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->redirect_url = 'deleted';
		$model->save( [ 'redirect_url' ] );
	}

	public static function setOrderToCart( $cartId, $orderId, $redirectUrl ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->order_id     = $orderId;
		$model->redirect_url = $redirectUrl;
		$model->save( [ 'order_id', 'redirect_url' ] );
	}

	public static function getCartIdByOrderId( $orderId ) {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'order_id' => $orderId,
		] );
		if ( ! $model->id ) {
			return false;
		}

		return $model->cart_id;
	}

	public static function getCartIdByIziBasket( $iziBasket ) {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'izi_basket' => $iziBasket,
		] );
		if ( ! $model->id ) {
			return false;
		}

		return $model->cart_id;
	}

	public static function tryRestore( $iziBasket ) {
		$cart_id = self::getCartIdByIziBasket( $iziBasket );
		if ( $cart_id ) {
			self::setSessionByCartId( $cart_id );
		}

	}

	public static function setConfirmationToCart( $cartId, $confirmation ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			Logger::response( "NOT FOUND" );

			return;
		}
		Logger::response( $confirmation );
		$model->confirmation_response = $confirmation;
		$model->save( [ 'confirmation_response' ] );
	}

	public static function getCartOrderRedirectUrl( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return false;
		}
		if ( $model->confirmation_response === 'deleted' ) {
			return 'deleted';
		}

		return $model->redirect_url;
	}

	public static function getCartConfirmation( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model->confirmation_response;
	}

	public static function dropCartConfirmation( $cartId ): void {
		global $wpdb;

		$table_name = $wpdb->prefix . 'izi_cart_session';
		$sql        = $wpdb->prepare(
			"UPDATE {$table_name} SET confirmation_response=NULL WHERE cart_id = %s",
			$cartId
		);
		$wpdb->query( $sql );
	}

	public static function initiateWCCart(): void {
		include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		include_once WC_ABSPATH . 'includes/class-wc-cart.php';

		if ( is_null( WC()->cart ) ) {
			\wc_load_cart();
		}

	}

	public static function setBasketCacheById( $cartId, $data ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			Logger::log( "CANNOT ADD CACHE! BASKET NOT FOUND {$cartId}" );

			return;
		}

		Logger::log( "BASKET FOUND {$cartId} ADDING CACHE" );
		$model->basket_cache = base64_encode( $data );
		$model->save( [ 'basket_cache' ] );
	}

	public static function getBasketCacheById( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return base64_decode( $model->basket_cache );
	}

	public static function getObjectById( $cartId ): ?Base {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model;
	}


	public static function setBasketCachedById( $cartId ): void {
		$data  = WooCommerceBasketCache::store( $cartId );
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->basket_cached = $data;
		$model->save( [ 'basket_cached' ] );
	}

	public static function getBasketCachedById( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model->basket_cached;
	}

	public static function setBasketCouponsById( $cartId, $data ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->coupons = $data;
		$model->save( [ 'coupons' ] );
	}

	public static function getBasketCouponsById( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model->coupons;
	}

	public static function setRedirectedById( $cartId, $data ) {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->redirected = $data;
		$model->save( [ 'redirected' ] );
	}

	public static function getRedirectedById( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model->redirected;
	}

//    public function getByAttributes($attributes): self
//    {
//        if (isset(self::$cache[$attributes['cart_id']])) {
//            return self::$cache[$attributes['cart_id']];
//        }
//        $model = parent::getByAttributes($attributes);
//        self::$cache[$attributes['cart_id']] = $model;
//        return $model;
//    }
//
//    public function save($returnSql = false)
//    {
//        self::$cache[$this->cart_id] = $this;
//        return parent::save($returnSql);
//    }
	public static function getWCCartSession( $cartId ): ?string {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return null;
		}

		return $model->wc_cart_session;
	}

	public static function setActionById( string $cartId, string $data ): void {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $cartId,
		] );
		if ( ! $model->id ) {
			return;
		}
		$model->action = $data;
		$model->save( [ 'action' ] );
	}

	/**
	 * @param $id
	 *
	 * @return array
	 * @throws JsonException
	 */
	public static function get_session_id( $id ): array {
		$model = self::instance();
		$model = $model->getByAttributes( [
			'cart_id' => $id,
		] );
		if ( ! $model->id ) {
			die( json_encode( '[]', JSON_THROW_ON_ERROR ) );
		}

		return json_decode( $model->session_id, true, 512, JSON_THROW_ON_ERROR );
	}
}
