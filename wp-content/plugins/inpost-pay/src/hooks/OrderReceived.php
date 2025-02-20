<?php

namespace Ilabs\Inpost_Pay\hooks;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\Lib\helpers\HPOSHelper;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;

class OrderReceived extends Base
{
    public function attachHook()
    {
        add_filter('woocommerce_thankyou_order_received_text', [$this, 'text'], 20, 2);
        add_filter('woocommerce_locate_template', [$this, 'custom_thankyou_page_template'], 10, 1);
    }

    public function text($fields, $order)
    {
        if (is_order_received_page()) {
            BasketIdentification::drop();
            InpostPay::getInstance()->getLib()->getController()->basketBindingDelete();
        }
        return $fields;
    }

    public function custom_thankyou_page_template($template)
    {
        global $wp;

        if (is_order_received_page() && (strpos($template, "order-received.php") || strpos($template, "thankyou.php"))) {
            BasketIdentification::drop();
            $order_id = absint($wp->query_vars['order-received']);

            $data = (new HPOSHelper($order_id))->get_meta('inpost_consents');

            if ($data) {
	            LSCacheHelper::set_private_cache();
                if (isset($_COOKIE['izi_basket_id'])) {
                    unset($_COOKIE['izi_basket_id']);
                }
                $new_template = plugin_dir_path(__FILE__) . '../views/thankyou-page.php';
                if (file_exists($new_template)) {
                    return $new_template;
                }
            }
        }
        return $template;
    }
}
