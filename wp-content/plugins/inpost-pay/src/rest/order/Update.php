<?php

namespace Ilabs\Inpost_Pay\rest\order;

use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\rest\Base;
use Ilabs\Inpost_Pay\StatusTranslator;
use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class Update extends Base
{
    public function __construct()
    {
        $this->restricted = true;
    }

    protected function describe()
    {
        $this->post['/inpost/v1/izi/order/(?P<id>[a-zA-Z0-9-]+)/event'] = function ($request) {

            $this->checkSignature($request);

            $id = $request->get_param('id');
            $data = $request->get_body();
            $date = date("Y-m-d H:i:s");
            Logger::orderEvent($data, "Event dla orderu {$id} z {$date}");
            $data = json_decode($data);
            $order = wc_get_order($id);

            if (!$order) {
                http_response_code(404);
                die(json_encode([
                    'error_code' => '404',
                    'error_message' => 'Order Not Found'
                ]));
            }
            $statusFromSettings = esc_attr(get_option('izi_event_' . $data->event_data->payment_status));

            if (property_exists($data->event_data, 'payment_status')) {
                $order->update_meta_data('izi_payment_status', $data->event_data->payment_status);
            }

            if (property_exists($data->event_data, 'order_status')) {
                $order->update_meta_data('izi_order_status', $data->event_data->order_status);
            }

			if (property_exists($data->event_data, 'payment_id')) {
				$order->update_meta_data('izi_payment_id', $data->event_data->payment_id);
			}

			if (property_exists($data->event_data, 'payment_reference')) {
				$order->update_meta_data('izi_payment_reference', $data->event_data->payment_reference);
			}

			if (property_exists($data->event_data, 'payment_type')) {
				$order->update_meta_data('izi_payment_type', $data->event_data->payment_type);
			}

            $order->set_status($statusFromSettings);
            $order->save();
            do_action('inpost_pay_order_updated', $id, $data);
            $status = $order->get_status();
            $status_labels = get_option('izi_status_map');
            $status = (!empty($status_labels['wc-' . $status])) ? $status_labels['wc-' . $status] : $status;
            $data = [
                'order_merchant_status_description' => $status,
            ];
            die(mb_convert_encoding(json_encode($data), 'UTF-8'));
        };
    }
}
