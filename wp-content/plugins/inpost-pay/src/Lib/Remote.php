<?php

namespace Ilabs\Inpost_Pay\Lib;

use Ilabs\Inpost_Pay\Logger;
use JsonException;

class Remote extends Connection
{

    protected $basketId;

    public static bool $done = false;
    private string $orderId;

    public function __construct($basketId = null)
    {
        if ($basketId === null) {
            $this->basketId = BasketIdentification::get();
            //Logger::log("BASKET ID IN Remote Class {$this->basketId}");
        } else {
            $this->basketId = $basketId;
        }
        parent::__construct();
    }

    public function basketGet()
    {
        return $this->request("v1/izi/basket/{$this->basketId}");
    }

    public function basketPut($data, $raw = false)
    {
        if (self::$done) {
            return null;
        }
        self::$done = true;

        if ($raw) {
            $toSend = $data;
        } else {
            $toSend = json_decode($data);
        }
        [
            $response,
            $code,
        ] = $this->request("v1/izi/basket/{$this->basketId}", "PUT", $toSend,
            true, $raw);
        Logger::response($data,
            'Merchant sends basket put for ' . $this->basketId);
        Logger::response($code,
            'Basket app code for basket put');
        Logger::response(json_encode($response),
            'Basket app response for basket put');

        return $response;
    }

	/**
	 * @throws JsonException
	 */
	public function orderEvent( $orderId, $status, $refList, $order_status = null ): void {
		$data = [
			'event_id'        => time(),
			'event_data_time' => gmdate( "Y-m-d\TH:i:s.000\Z" ),
			'event_data'      => [
				'order_merchant_status_description' => $status,
				'delivery_references_list'          => $refList,
			],
		];
		if ( $order_status ) {
			$data['event_data']['order_status'] = $order_status;
		}
		[ $response, $code ] = $this->request( "v1/izi/order/{$orderId}/event",
			"POST", $data, true );
		Logger::response( json_encode( $data, JSON_THROW_ON_ERROR ),
			'Merchant sends event for ' . $orderId );
		Logger::response( $code,
			'Basket app code for event' );
	}

    public function basketBindingGet($force = false)
    {
        if (!$force && InPostIzi::getStorage()->issetSession('binding_get')) {
            $session = InPostIzi::getStorage()->findSession('binding_get');
            Logger::debug(
                sprintf('[GET BINDING FROM CACHE] [basketId: %s] [session: %s]',
                    $this->basketId,
                    $session,
                ));

            return json_decode($session);
        }

        $browserId = BrowserIdStorage::get();

        $getParam = '';
        if ($browserId) {
            $getParam = '?browser_id=' . $browserId;
        }

        $response = $this->request("v1/izi/basket/{$this->basketId}/binding{$getParam}");

        $jsonResponse = json_encode($response);

        Logger::log(
            sprintf('[GET BINDING FROM REMOTE] [basketId: %s] [response: %s]',
                $this->basketId,
                print_r($jsonResponse, true),
            ));

        InPostIzi::getStorage()->insertSession('binding_get', $jsonResponse);

        return $response;
    }

    public function basketBindingPost($prefix = null, $number = null)
    {
        InPostIzi::getStorage()->eraseSession('binding_get');
        $browser = json_decode(base64_decode($_GET['browser']), true);
        $browserArray = [
            "user_agent" => $browser['user_agent'],
            "description" => $browser['description'],
            "platform" => $browser['platform'],
            "architecture" => $browser['architecture'],
            "data_time" => date("Y-m-d\TH:i:s.000\Z"),
            "location" => "-",
            "customer_ip" => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'],
            "port" => $_SERVER['SERVER_PORT'],
        ];
        if ($prefix && $number) {
            return $this->request("v1/izi/basket/{$this->basketId}/binding",
                "POST", [
                    "binding_method" => "PHONE",
                    'binding_place' => ($_GET['binding_place'] == '' ? null : $_GET['binding_place']),
                    "phone_number" => [
                        "country_prefix" => "+48",
                        "phone" => $number,
                    ],
                    "browser" => $browserArray,
                ]);
        } else {

            return $this->request("v1/izi/basket/{$this->basketId}/binding",
                "POST", [
                    "binding_method" => "DEEP_LINK",
                    'binding_place' => $_GET['binding_place'],
                    "browser" => $browserArray,
                ]);
        }
    }

    public function basketBindingDelete()
    {
        return $this->request("v1/izi/basket/{$this->basketId}/binding",
            "DELETE");
    }

    public function browserBindingDelete($browserId)
    {
        return $this->request("v1/izi/browser/{$browserId}/binding",
            "DELETE");
    }

    public function basketConfirmation($data)
    {
        return $this->request("v1/private/izi/basket/binding/{$this->basketId}/confirmation",
            "POST", $data);
    }

    public function orderPost($data)
    {
        $response = $this->request("v1/private/izi/inpostpay-order", "POST",
            $data);

        $this->orderId = $response->order_id;

        return $response;
    }

    public function orderGet()
    {
        return $this->request("/inpostpay-order/{$this->orderId}");
    }
}
