<?php

namespace Ilabs\Inpost_Pay\Lib;


use Ilabs\Inpost_Pay\Lib\helpers\LangHelper;
use Ilabs\Inpost_Pay\Logger;
use WC_Cart;

class InPostIzi {

	const ENVIRONMENT_DEVELOP = 1;

	const ENVIRONMENT_PRODUCTION = 2;

	const ENVIRONMENT_SANDBOX = 3;

	const BINDING_PLACE_PRODUCT_CARD = 'PRODUCT_CARD';

	const BINDING_PLACE_BASKET_SUMMARY = 'BASKET_SUMMARY';

	const BINDING_PLACE_ORDER_CREATE = 'ORDER_CREATE';

	const BINDING_PLACE_CHECKOUT_PAGE = 'CHECKOUT_PAGE';

	const BINDING_PLACE_LOGIN_PAGE = 'LOGIN_PAGE';

	const BINDING_PLACE_BASKET_POPUP = 'BASKET_POPUP';

	const BINDING_PLACE_THANK_YOU_PAGE = 'THANK_YOU_PAGE';

	const BINDING_PLACE_MINICART_PAGE = 'MINICART_PAGE';

	protected Controller $controller;
	protected static InPostIzi $instance;

	private static Storage $storage;
	private static bool $blockPut = false;

	private static $clientId;
	private static $clientSecret;

	private static $environment;

	private static $cartSessionClass;
	private static $loggerClass;

	private static $tokenCache = null;

	public function __construct() {
		self::$storage    = new Storage();
		$this->controller = new Controller();
	}

	public function getController(): Controller {
		return $this->controller;
	}

	public static function setCartSessionClass( $class ) {
		self::$cartSessionClass = $class;
	}

	public static function getCartSessionClass() {
		return self::$cartSessionClass;
	}

	public static function setLoggerClass( $class ) {
		self::$loggerClass = $class;
	}

	public static function getLoggerClass() {
		return self::$loggerClass;
	}

	public static function getStorage(): Storage {
		return self::$storage;
	}

	public static function setEnvironment( $environment ) {
		self::$environment = $environment;
	}

	public static function getApiUrl(): string {
		switch ( self::$environment ) {
			case self::ENVIRONMENT_PRODUCTION:
				return 'https://api.inpost.pl';
			case self::ENVIRONMENT_SANDBOX:
				return 'https://sandbox-api.inpost.pl';
			default:
				return 'https://uat-api.inpost.pl';
		}
	}

	public static function getAuthUrl(): string {
		switch ( self::$environment ) {
			case self::ENVIRONMENT_PRODUCTION:
				return 'https://login.inpost.pl';
			case self::ENVIRONMENT_SANDBOX:
				return 'https://sandbox-login.inpost.pl';
			default:
				return 'https://uat-auth.easypack24.net';
		}
	}

	public static function getLinkUrl(): string {
		switch ( self::$environment ) {
			case self::ENVIRONMENT_PRODUCTION:
				return 'inpost://izilink';
			case self::ENVIRONMENT_SANDBOX:
				return 'inpostsandbox://izilink';
			default:
				return 'inpostuat://izilink';
		}
	}

	public static function getJsUrl(): string {
		switch ( self::$environment ) {
			case self::ENVIRONMENT_PRODUCTION:
				return 'https://izi.inpost.pl/inpostizi.js';
			case self::ENVIRONMENT_SANDBOX:
				return 'https://izi-sandbox.inpost.pl/inpostizi.js';
			default:
				return 'https://izi-uat.inpost.pl/inpostizi.js';
		}
	}

	public static function blockPut() {
		//        ob_start();
		//        debug_print_backtrace(0, 1);
		//        $trace = ob_get_contents();
		//        ob_end_clean();
		//        self::$loggerClass::log($trace);
		self::$blockPut = true;
	}

	public static function unblockPut() {
		self::$blockPut = false;
	}

	public function orderEvent(
		$orderId,
		$status,
		$refList,
		$order_status = null
	) {
		$this->controller->orderEvent( $orderId,
			$status,
			$refList,
			$order_status );
	}

	public function basketPut( $forceUnbound = false, $justStore = false ) {
		self::$loggerClass::Log( 'PERFORMING PUT WITH PARAMETERS: $forceUnbound = ' . (int) $forceUnbound . ', $justStore = ' . (int) $justStore . ' self::$blockPut = ' . (int) self::$blockPut );
		if ( ! self::$blockPut ) {
			$data = $this->getBasket()->encode();
			self::getCartSessionClass()::setBasketCacheById( BasketIdentification::get(),
				$data );
			//self::getCartSessionClass()::setBasketCachedById( BasketIdentification::get() );

			if ( $justStore ) {
				return;
			}

			$binding            = BindingProvider::getBinding(); //!! removed true
			$basketLinkedForLog = false;
			if ( isset( $binding->basket_linked ) ) {
				$basketLinkedForLog = $binding->basket_linked;
			}

			if ( ! $forceUnbound && ( ! $binding || ! $basketLinkedForLog ) ) {
				$forceUnbound       = print_r( (int) $forceUnbound, true );
				$basketLinkedForLog = print_r( (int) $basketLinkedForLog,
					true );
				self::$loggerClass::response( '',
					"NO put: forceUnbound:{$forceUnbound} binding->basket_linked:{$basketLinkedForLog}" );

				return;
			}

			self::$loggerClass::response( '',
				"Performing put: forceUnbound:{$forceUnbound} binding->basket_linked:{$basketLinkedForLog}" );

			$basket = InPostIzi::getCartSessionClass()::getBasketCacheById( BasketIdentification::get() );
			$basket = str_replace( '\/',
				'/',
				mb_convert_encoding( $basket, 'UTF-8' ) );
			self::getCartSessionClass()::setBasketCacheById( BasketIdentification::get(),
				$basket );
			$this->controller->basketPut( $basket, true );
		} else {
			Logger::log( 'Block PUT' );
		}
	}

	public static function setTokenCacheObject( $object ) {
		self::$tokenCache = $object;
	}

	public static function getCachedToken() {
		if ( self::$tokenCache ) {
			return self::$tokenCache->getCachedToken( true );
		}

		return null;
	}

	public static function setCachedToken( $token, $expiration ) {
		if ( self::$tokenCache ) {
			return self::$tokenCache->setCachedToken( $token, $expiration );
		}
	}

	public static function print() {
		echo '<inpost-izi-button language="pl"></inpost-izi-button>';
	}

	public function sendOrder() {
		$basket        = $this->getBasket();
		$orderRespanse = $this->controller->orderPost( $this->getOrder()
		                                                    ->toArray() );

		InPostIzi::getStorage()
		         ->insertSession( "sameBasket",
			         $basket->compareProduct( $orderRespanse->products ) );
	}

	public static function getInstance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * @param $clientId string
	 */
	public static function setClintId( $clientId ) {
		self::$clientId = $clientId;
	}

	public static function getClientId() {
		return self::$clientId;
	}

	public static function setClientSecret( $clientSecret ) {
		self::$clientSecret = $clientSecret;
	}

	public static function getClientSecret() {
		return self::$clientSecret;
	}

	public static function render(
		$productId = null,
		$echo = true,
		$addBasketId = false,
		$variationId = '',
		$count = 0,
		$dark = false,
		$yellow = false,
		$cart = false,
		$float = 'left',
		$bindingPlace = 'BASKET_POPUP',
		$round = "none",
		$maxWidth = 220,
		$minHeight = 64
	) {
		$basketId      = $addBasketId ? ' basket-id="' . BasketIdentification::get() . '" ' : ' ';
		$id            = BasketIdentification::get();
		$html          = '';
		$variationHtml = '';

		$maskedPhoneNumber = '';
		$name              = '';
		$inpost_basket_id  = '';

		$binding = BindingProvider::getBinding();


		if ( $variationId ) {
			$variationHtml = ' variationId="' . $variationId . '" ';
		}

		$data = self::getCartSessionClass()::getCartOrderRedirectUrl( $id );
		if ( $data != null ) {
			BasketIdentification::drop();
			$id = BasketIdentification::get();
		}

		$cartConfirmation = self::getCartSessionClass()::getCartConfirmation( $id );
		if ( ! $cartConfirmation ) {
			Logger::debug( 'Modify masked phone number' );
			$maskedPhoneNumber = '';
		} elseif ( empty( $binding->basket_linked ) ) {
			Logger::debug( 'Get forced binding data' );
			$binding = BindingProvider::getBinding( true );
		}

		if ( ! empty( $binding->basket_linked ) ) {
			$maskedPhoneNumber = ! empty( $binding->client_details->masked_phone_number ) ? $binding->client_details->masked_phone_number : '';
			$name              = ! empty( $binding->client_details->name ) ? $binding->client_details->name : '';
			$inpost_basket_id  = $binding->inpost_basket_id ?? '';
		}


		switch ( $round ) {
			case "round":
				$frameStyle = 'frame_style="round"';
				break;
			case "rounded":
				$frameStyle = 'frame_style="rounded"';
				break;
			default:
				$frameStyle = '';
		}

		if ( $maxWidth >= 220 && $maxWidth <= 600 ) {
			$maxWidth = "max_width=\"$maxWidth\"";
		}

		if ( $minHeight >= 48 && $minHeight <= 64 ) {
			$minHeight = "min_height=\"$minHeight\"";
		}

		$count = "count=\"{$count}\"";

		$dark   = $dark ? ' dark_mode="true" ' : '';
		$yellow = $yellow ? ' variant="primary" ' : ' variant="secondary" ';
		$cart   = $cart ? ' basket="true" ' : '';

		$float = 'class="float-' . $float . '"';

		$bindingPlace = " binding_place=\"{$bindingPlace}\" ";

		$language = LangHelper::getWidgetLangAttr();

		if ( $productId ) {
			$html = '<inpost-izi-button ' . $bindingPlace . $float . $cart . $dark . $yellow . $count . ' ' . $inpost_basket_id . ' ' . $variationHtml . ' name="' . $name . '" masked_phone_number="' . $maskedPhoneNumber . '" data-product-id="' . $productId . '" language="' . $language . '" ' . $basketId . ' ' . $frameStyle . ' ' . $maxWidth . ' ' . $minHeight . '></inpost-izi-button>';
		} else {
			$html = '<inpost-izi-button ' . $bindingPlace . $float . $cart . $dark . $yellow . $count . ' ' . $inpost_basket_id . ' ' . $variationHtml . ' name="' . $name . '" masked_phone_number="' . $maskedPhoneNumber . '" language="' . $language . '" ' . $basketId . ' ' . $frameStyle . ' ' . $maxWidth . ' ' . $minHeight . '></inpost-izi-button>';
		}

		//        $html = "<!-- mfunc mysecurestring --><!--esi \n {$html} \n --><!-- /mfunc mysecurestring -->";

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
}
