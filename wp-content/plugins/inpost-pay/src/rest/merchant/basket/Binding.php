<?php
//basketBindingGet

namespace Ilabs\Inpost_Pay\rest\merchant\basket;

use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\Lib\BindingProvider;
use Ilabs\Inpost_Pay\Lib\BrowserIdStorage;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;
use Ilabs\Inpost_Pay\Lib\InPostIzi;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\Lib\Storage;
use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\models\CartSession;
use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class Binding extends Base
{

    protected function describe()
    {
        add_action( 'wc_ajax_wc_ajax_inpost_post_binding', [$this, 'wc_ajax_inpost_post_binding']);
        add_action( 'wc_ajax_merchant_basket_delete_binding', [$this, 'merchant_basket_delete_binding']);



        $this->get['/inpost/v1/izi/merchant/basket/get/binding'] = function ($request) {
            $response = InpostPay::getInstance()->getLib()->getController()->basketBindingGet();
            return json_encode($response);
        };

//        $this->post['/inpost/v1/izi/merchant/basket/post/binding/(?P<id>[a-zA-Z0-9-]+)'] = function ($request) {
//            CartSession::forceBasketStore();
//            $response = InpostIziPayWoocommerce::getInstance()->getLib()->getController()->basketBindingPost();
//            return json_encode($response);
//        };

//        $this->post['/inpost/v1/izi/merchant/basket/post/binding/(?P<prefix>[\w]+)/(?P<number>[\w]+)'] = function ($request) {
//            CartSession::forceBasketStore();
//            $browserId = Storage::findSession('BrowserId');
//            if (!$browserId && isset($_COOKIE['BrowserId'])) {
//                $browserId = $_COOKIE['BrowserId'];
//            }
//            if ($browserId) {
//                Storage::eraseSession('binding_get');
//                $binding = BindingProvider::getBinding(true);
//                if ($binding && $binding->browser_trusted) {
//                    die(json_encode([]));
//                }
//            }
//
//            $prefix = $request->get_param('prefix');
//            $number = $request->get_param('number');
//            $response = InpostIziPayWoocommerce::getInstance()->getLib()->getController()->basketBindingPost($prefix, $number);
//            return json_encode($response);
//        };

//        $this->post['/inpost/v1/izi/merchant/basket/post/binding'] = function ($request) {
//            CartSession::forceBasketStore();
//            $response = InpostIziPayWoocommerce::getInstance()->getLib()->getController()->basketBindingPost();
//            return json_encode($response);
//        };
    }

    function wc_ajax_inpost_post_binding() {
	    LSCacheHelper::no_cache();
        CartSession::forceBasketStore();
        $browserId = BrowserIdStorage::get();
        if (!$browserId && isset($_COOKIE['BrowserId'])) {
            $browserId = $_COOKIE['BrowserId'];
        }
        if ($browserId) {
            InPostIzi::getStorage()->eraseSession('binding_get');
            $binding = BindingProvider::getBinding();
            if ($binding && isset($binding->browser_trusted) && $binding->browser_trusted) {
                InPostIzi::unblockPut();
                $response = InpostPay::getInstance()->getLib()->getController()->basketBindingPost();
                $binding = BindingProvider::getBinding(true);
                foreach ($binding->client_details as $innerKey => $innerData) {
                    $binding->$innerKey = $innerData;
                }
                CartSession::setConfirmationToCart(BasketIdentification::get(), json_encode($binding));
                InPostIzi::getStorage()->sessionClose();
                header('Content-Type: application/json; charset=utf-8');
                die(json_encode($response));
            }
        }

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        $prefix = $data->prefix;
        $number = $data->number;
        $response = InpostPay::getInstance()->getLib()->getController()->basketBindingPost($prefix, $number);
        InPostIzi::getStorage()->sessionClose();
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($response));
    }

    function merchant_basket_delete_binding() {
        CartSession::dropCartConfirmation(BasketIdentification::get());
        $response = InpostPay::getInstance()->getLib()->getController()->basketBindingDelete();
        if (isset($_COOKIE['BrowserId'])) {
            InpostPay::getInstance()->getLib()->getController()->browserBindingDelete($_COOKIE['BrowserId']);
            unset($_COOKIE['BrowserId']);
        }

        BasketIdentification::drop();
        InPostIzi::getStorage()->sessionClose();
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($response));
    }
}
