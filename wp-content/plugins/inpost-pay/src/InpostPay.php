<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\hooks\Coupon;
use Ilabs\Inpost_Pay\hooks\TemplateRedirect;
use Ilabs\Inpost_Pay\Lib\Authorization;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Lib\Cron;
use Ilabs\Inpost_Pay\Lib\exception\AuthorizationException;
use Ilabs\Inpost_Pay\Lib\helpers\CacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\hooks\BasketChange;
use Ilabs\Inpost_Pay\hooks\BillingFields;
use Ilabs\Inpost_Pay\hooks\CartCount;
use Ilabs\Inpost_Pay\hooks\DisplayWidget;
use Ilabs\Inpost_Pay\hooks\OrderReceived;
use Ilabs\Inpost_Pay\hooks\OrderUpdate;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\hooks\SessionInit;
use Ilabs\Inpost_Pay\rest\basket\Confirmation;
use Ilabs\Inpost_Pay\rest\basket\Delete;
use Ilabs\Inpost_Pay\rest\merchant\basket\Count;
use Ilabs\Inpost_Pay\rest\merchant\basket\IsBind;
use Ilabs\Inpost_Pay\rest\merchant\basket\Add;
use Ilabs\Inpost_Pay\rest\merchant\basket\Binding;
use Ilabs\Inpost_Pay\rest\merchant\basket\Link;
use Ilabs\Inpost_Pay\rest\merchant\basket\Order;
use Ilabs\Inpost_Pay\rest\order\Create;
use Ilabs\Inpost_Pay\rest\order\Get;
use Ilabs\Inpost_Pay\rest\order\Update;
use Ilabs\Inpost_Pay\rest\sse\RemoveSSEPid;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetCheckoutPage;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetLoginPage;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetMinicart;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetOrderCreate;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetPlaceBasketSummary;
use Ilabs\Inpost_Pay\rest\widget\get\WidgetPlaceProductCard;

//require_once __DIR__ . '/../vendor/autoload.php';

class InpostPay {
	private static ?InpostPay $instance = null;
	private $lib;

	public static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function getLib() {
		return $this->lib;
	}

	public static function activate() {
		register_setting( 'inpost-izi', 'izi_db_version',
			array(
				'type'    => 'string',
				'default' => '1.0'
			)
		);
		$migration = new Migration();
		$migration->run();

		register_setting( 'inpost-izi', 'izi_is_authorized', array(
			'type'    => 'bool',
			'default' => false
		) );

		if ( get_option( 'izi_client_id' ) && get_option( 'izi_client_secret' ) ) {
			update_option( 'izi_is_authorized', true );
		}
	}

	public function deactivate() {
		( new Cron() )->deactivate();
	}

	function add_sri_integrity_attribute( $html, $handle ) {

		$hashAlgorithm = 'sha256'; // The hash algorithm you used to create the SRI hash

		if ( $handle === 'InpostIziJavsscript' ) {
			$fileContents = file_get_contents( InPostIzi::getJsUrl() );
			$hashValue    = hash( $hashAlgorithm, $fileContents, true );
			$sriValue     = $hashAlgorithm . '-' . base64_encode( $hashValue );

			$html = str_replace(
				'<script',
				'<script integrity="' . $sriValue . '" crossorigin="anonymous"',
				$html
			);
		}

		return $html;
	}

	private function __construct() {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		InPostIzi::setEnvironment( esc_attr( get_option( 'izi_environment' ) ) ?: InPostIzi::ENVIRONMENT_DEVELOP );
		InPostIzi::setClientSecret( esc_attr( get_option( 'izi_client_secret' ) ) );
		InPostIzi::setClintId( esc_attr( get_option( 'izi_client_id' ) ) );
		InPostIzi::setCartSessionClass( CartSession::class );
		InPostIzi::setLoggerClass( Logger::class );
		$tokenCache = new TokenCache();
		InPostIzi::setTokenCacheObject( $tokenCache );
		$this->lib = WooCommerceInPostIzi::getInstance();
		if ( isset( $_COOKIE['BrowserId'] ) ) {
			InPostIzi::getStorage()->insertSession( 'BrowserId', $_COOKIE['BrowserId'] );
		}
		( new SettingsPage() )->register();

		define( 'INPOST_PAY_ORDER_SEE_PID_FILE', plugin_dir_path( __FILE__ ) . 'pid/' . BasketIdentification::get() );
		if ( ! is_dir( plugin_dir_path( __FILE__ ) . '/pid/' ) ) {
			mkdir( plugin_dir_path( __FILE__ ) . '/pid/' );
		}
		$this->filterVisibleMetaAtBackoffice();
		$this->addOrderMetaBox();

		if ( ! get_transient( 'check_izi_is_authorized' ) ) {
			$authorization = new Authorization();
			try {
				$authorization->getToken( true );
				update_option( 'izi_is_authorized', true );
				CacheHelper::flushCache();
			} catch ( AuthorizationException $ex ) {

			}
			set_transient( 'check_izi_is_authorized', 'checked', 60 * 5 );
		}


		if ( get_option( 'izi_is_authorized' ) ) {
			$this->attachHooks();
			$this->initiateRestApi();

			( new Cron() )->schedule();

			if ( ! isset( $_COOKIE['izi_show'] ) && isset( $_GET['showIzi'] ) && $_GET['showIzi'] == 'true' ) {
				$_COOKIE['izi_show'] = 'true';
				setcookie( 'izi_show', 'true', time() + 3600 * 24, '/' );
			}

			if ( ! is_admin() ) {
				$hideFunctionality = esc_attr( get_option( 'izi_hide_functionality' ) ) ?: 'hidden';
				$show              = $_COOKIE['izi_show'] ?? false;
				if ( $hideFunctionality == 'hidden' && $show === false ) {
					return;
				}
			}

			if ( ! InPostIzi::getStorage()->issetSession( 'binding_get' ) ) {
				$binding = BindingProvider::getBinding();
				if ( isset( $binding->basketId ) ) {
					$object = CartSession::getObjectById( $binding->basketId );
					if ( $object && $object->redirect_url == 'deleted' ) {
						Logger::log( "DROP BINDING ON DELETE" );
						BasketIdentification::drop();
						InPostIzi::getStorage()->eraseSession( 'binding_get' );
						unset( $binding );
					}
				}
			}

			add_action( 'wp_enqueue_scripts', function () {
				$version = rand( 100, 100000 );

				wp_register_script( 'InpostIziJavsscriptWoocommerce', plugin_dir_url( __FILE__ ) . '../assets/js/woocommerceizi.js?a=' . $version, [ 'jquery' ] );
				wp_enqueue_script( 'InpostIziJavsscriptWoocommerce' );
				wp_localize_script(
					'InpostIziJavsscriptWoocommerce',
					'InpostIziJavsscriptWoocommerce',
					[
						'ajaxurl'                         => \WC_Ajax::get_endpoint( 'wc_ajax_inpost_add_product' ),
						'bindingEndpoint'                 => \WC_Ajax::get_endpoint( 'wc_ajax_inpost_post_binding' ),
						'merchant_basket_confirmation'    => \WC_Ajax::get_endpoint( 'merchant_basket_confirmation' ),
						'merchant_basket_get_link'        => \WC_Ajax::get_endpoint( 'merchant_basket_get_link' ),
						'merchant_order_confirmation_get' => \WC_Ajax::get_endpoint( 'merchant_order_confirmation_get' ),
						'merchant_basket_delete_binding'  => \WC_Ajax::get_endpoint( 'merchant_basket_delete_binding' ),
						'remove_sse_pid'                  => \WC_Ajax::get_endpoint( 'remove_sse_pid' ),
						'count_basket'                    => \WC_Ajax::get_endpoint( 'inpost_count_basket' ),
						'isBind'                          => \WC_Ajax::get_endpoint( 'inpost_basket_is_bind' ),
						'home_url'                        => home_url( '/', 'absolute' ),
					]
				);
				if ( defined( 'IZI_LOCAL_SCRIPT' ) ) {
					wp_enqueue_script( 'InpostIziJavsscript', $this->getJsAssetPath() . 'inpostizi.js?a=' . $version );
				} else {
					//add_filter('script_loader_tag',  [$this, 'add_sri_integrity_attribute'], 10, 2);
					wp_enqueue_script( 'InpostIziJavsscript', InPostIzi::getJsUrl(), [], null, true );
				}

				add_action( 'wp_head', function () {
					echo "<style>.post-type-archive-product .inpostizi-bind-button {margin: 0 auto;}</style>";
				}, 100 );
			} );
		}

	}

	public function getJsAssetPath(): string {
		return plugin_dir_url( WOOCOMMERCE_INPOST_PAY_PLUGIN_FILE ) . '/assets/js/';
	}

	private function attachHooks() {
		( new OrderReceived() )->attachHook();
		( new BasketChange() )->attachHook();
		( new CartCount() )->attachHook();
		( new DisplayWidget() )->attachHook();
		( new SessionInit() )->attachHook();
		( new BillingFields )->attachHook();
		WooCommerceInPostIzi::getInstance();
		( new OrderUpdate() )->attachHook();
		( new Cron() )->attachHook();
		( new TemplateRedirect() )->attachHook();
		( new Coupon() )->attachHook();
	}

	private function initiateRestApi() {
		( new Confirmation() )->register();
		( new Create() )->register();
		( new Get() )->register();
		( new Update() )->register();
		( new rest\basket\Get() )->register();
		( new Delete() )->register();

		( new rest\merchant\basket\Confirmation() )->register();
		( new Binding() )->register();
		( new Order() )->register();
		( new Add() )->register();
		( new Link() )->register();
		( new rest\basket\Update() )->register();
		( new Count() )->register();

		( new IsBind() )->register();

		( new WidgetOrderCreate() )->register();
		( new WidgetPlaceBasketSummary() )->register();
		( new WidgetPlaceProductCard() )->register();
		( new WidgetCheckoutPage() )->register();
		( new WidgetLoginPage() )->register();
		( new WidgetMinicart() )->register();

		( new RemoveSSEPid() )->register();
	}

	private function addOrderMetaBox() {
		add_action( 'add_meta_boxes', function () {
			global $post;
			if ( 'yes' === get_option( 'woocommerce_custom_orders_table_enabled' ) ) {
				if ( is_a( $post, 'WC_Order' ) ) {
					$order_id = $post->get_id();
				}
			} else {
				if ( is_object( $post ) && $post->post_type == 'shop_order' ) {
					$order_id = $post->ID;
				}
			}
			if ( empty( $order_id ) ) {
				return;
			}
			$iziPaymentType = get_post_meta( $order_id, 'izi_payment_type', true );
			if ( ! $iziPaymentType ) {
				return;
			}
			add_meta_box( 'izi_order_fields', 'InPost Pay', function () {
				include __DIR__ . '/views/orderMetaBox.php';
			}, 'shop_order', 'side', 'core' );
		} );
	}

	private function filterVisibleMetaAtBackoffice() {
		add_filter( 'woocommerce_hidden_order_itemmeta', function ( $arr ) {
			$arr[] = 'inpost_account_info';
			$arr[] = 'inpost_consents';
			$arr[] = 'is_vat_exempt';
			$arr[] = 'izi_payment_type';
			$arr[] = 'origin_phone_number';

			return $arr;
		}, 10, 1 );
	}
}
