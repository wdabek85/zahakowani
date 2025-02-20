<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class CartCount extends Base
{
    public function attachHook()
    {

        add_action('wp_enqueue_scripts',  [$this, 'enqueue_cart_count_script']);
        add_action('wp_ajax_update_cart_count', [$this, 'update_cart_count_function']);
        add_action('wp_ajax_nopriv_update_cart_count', [$this, 'update_cart_count_function']);
    }


    public function update_cart_count_function()
    {
        echo $this->get_cart_count();
        wp_die();
    }

    public function enqueue_cart_count_script()
    {
        $plugin_url = plugins_url('', dirname(dirname(__FILE__))) . '/assets/js/izi-cart-count.js';

        wp_enqueue_script('izi-cart-count', $plugin_url, array('jquery'), '1.0', true);

        wp_localize_script('izi-cart-count', 'iziCartCountData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'cart_count' => $this->get_cart_count()
        ));
    }

    public function get_cart_count()
    {
        if (function_exists('WC')) {
            return WC()->cart->get_cart_contents_count();
        }
        return 0;
    }
}
