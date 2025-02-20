<?php

namespace Ilabs\Inpost_Pay\Lib;

use Ilabs\Inpost_Pay\hooks\DisplayWidget;
use Ilabs\Inpost_Pay\Lib\helpers\CacheHelper;
use Ilabs\Inpost_Pay\Logger;

class Controller extends Remote {

	public static $addToCart;

	public function __construct() {
		parent::__construct();
	}

	public function basketBindingDelete() {
		InPostIzi::getStorage()->insertSession( "basketLinked", false );
		$response = parent::basketBindingDelete();
		BasketIdentification::drop();

		return $response;
	}

	public function browserBindingDelete( $browserId = null ) {
		if ( ! $browserId ) {
			$browserId = BrowserIdStorage::get();
		}
		$response = '';
		if ( $browserId ) {
			$response = parent::browserBindingDelete( $browserId );
			InPostIzi::getStorage()->eraseSession( 'BrowserId' );
		}

		return $response;
	}

	public function basketBindingGet( $force = false ) {

		$response = parent::basketBindingGet( $force );

		if ( isset( $response, $this->basketId ) ) {
			$response->basketId = $this->basketId;
		}

		if ( isset( $binding->browser_trusted ) && $binding->browser_trusted ) {
			Logger::log( 'browser is trusted' );
			InPostIzi::getCartSessionClass()::setConfirmationToCart( BasketIdentification::get(), json_encode( $binding->client_details ) );
		}

		Logger::debug(
			sprintf( '[Controller basketBindingGet force=%s] [response: %s]',
				$force,
				print_r( $response, true ),
			) );

		return $response;
	}

	public function basketBindingPost( $prefix = null, $number = null ) {
		InPostIzi::getStorage()->insertSession( "basketLinked", true );
		$binding = $this->basketBindingGet( true );
		if ( isset( $binding->browser_trusted ) && $binding->browser_trusted ) {
			$izi = InPostIzi::getInstance();
			InPostIzi::getStorage()->eraseSession( 'binding_get' );
			if ( method_exists( InPostIzi::getCartSessionClass(),
				'initiateWCCart' ) ) {
				InPostIzi::getCartSessionClass()::initiateWCCart();
			}
			$izi->basketPut( true );
			InPostIzi::getStorage()->eraseSession( 'binding_get' );

			return [];
		}
		Logger::log( 'browser is not trusted' );
		$response = parent::basketBindingPost( $prefix, $number );
		if ( ! $response ) {
			$response = new \stdClass();
		}
		$response->basketId = $this->basketId;

		return $response;
	}

	public function basketBindingGetInterval( $id = '', $maxTicks = 0 ) {
		$ticks    = 0;
		$maxTicks = $maxTicks || 18;
		if ( ! $id ) {
			$id = BasketIdentification::get();
		}
		$sleepTime = get_option( 'izi_sse_sleep_time' ) * 100000;
		if ( $id ) {
			while ( $ticks < $maxTicks ) {
				$data = InPostIzi::getCartSessionClass()::getCartConfirmation( $id );
				if ( is_string( $data ) && strlen( $data ) > 10 ) {
					$data               = json_decode( $data );
					$data->baskedlinked = ( $data->status == 'SUCCESS' ) ? true : false;
					$data->basketId     = $id;

					return json_encode( $data );
				}
				$ticks ++;
				usleep( $sleepTime );
			}
		}

		return [];
	}

	private function putBasket() {
		if ( function_exists( 'did_action' ) ) {
			if ( ! did_action( 'woocommerce_load_cart_from_session' ) && function_exists( 'wc_load_cart' ) ) {
				wc_load_cart();
			}
		}
		$izi = InPostIzi::getInstance();
		$izi->basketPut();
	}

	public function orderGetInterval( $id = '' ) {
		if ( ! $id ) {
			$id = BasketIdentification::get();
		}
		CacheHelper::flushCache();
		$model = InPostIzi::getCartSessionClass()::getObjectById( $id );
		if ( ! $model || ! $model->id ) {
			return [];
		}

		$redirectUrl = isset( $model->confirmation_response ) && $model->confirmation_response == 'deleted' ? 'deleted' : ( isset( $model ) ? $model->redirect_url : '' );
		if ( $redirectUrl && $redirectUrl != 'deleted' ) {
			if ( ! InPostIzi::getCartSessionClass()::getRedirectedById( $id ) ) {
				return [
					'action'   => 'redirect',
					'redirect' => $redirectUrl,
				]; // Removed json_encode
			}
		}
		if ( 'deleted' === $redirectUrl ) {
			return [
				'action' => 'delete',
			];
		}
		if ( $model->coupons == 1 ) {

			if ( method_exists( $model, 'save' ) ) {
				$model->coupons = 0;
				$model->save();
			} else {
				InPostIzi::getCartSessionClass()::setBasketCouponsById( $id,
					0 );
			}
			Logger::log( 'SENDING REFRESH' );

			return [
				'action'  => 'refresh',
				'cart-id' => BasketIdentification::get(),
			];
		}

		if ( strlen($model->action) > 5 ) {
			Logger::log( 'SENDING ACTION: ' . $model->action );
			$action = $model->action;
			InPostIzi::getCartSessionClass()::setActionById( $id,
				0 );
			if ( strpos( $action, 'update-count' ) !== false ) {
				$ex    = explode( ':', $action, 2 );
				$count = $ex[1];

				return [
					'action'  => 'update-count',
					'count'   => $count,
					'cart-id' => BasketIdentification::get(),
				];
			}

			return [
				'action'  => $action,
				'cart-id' => BasketIdentification::get(),
			];
		}
		if ( method_exists( InPostIzi::getCartSessionClass(),
				'refresh' ) && InPostIzi::getCartSessionClass()::refresh( $id ) ) {
			return [
				'action'  => 'refresh',
				'cart-id' => BasketIdentification::get(),
			];
		}

		return [];
	}

	public function getSignatureKeys( $force = false ) {
		return $this->request( "v1/izi/signing-keys/public", "GET" );
	}
}
