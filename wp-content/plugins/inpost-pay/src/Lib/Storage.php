<?php

namespace Ilabs\Inpost_Pay\Lib;

class Storage
{

    private \WC_Session $woocommerce_session_handler;

    public function __construct()
    {
        \WC()->initialize_session();
        $this->woocommerce_session_handler = \WC()->session;
    }

    public function insertSession($key, $value)
    {
        $this->woocommerce_session_handler->set($key, $value);
    }

    public function issetSession($key): bool
    {

        return $this->woocommerce_session_handler->__isset($key);
    }

    public function findSession($key)
    {
        if ($this->issetSession($key)) {
            return $this->woocommerce_session_handler->get($key);
        }

        return null;
    }

    public function eraseSession($key)
    {
        $this->woocommerce_session_handler->__unset($key);
    }

    public function destroySession()
    {
        if (function_exists( 'wc_empty_cart' ) ) {
            $this->woocommerce_session_handler->destroy_session();
        }

    }

    public function initSession()
    {
        $this->woocommerce_session_handler->init();
    }

    public function getSessionCustomerId(): string
    {
        return $this->woocommerce_session_handler->get_customer_id();
    }

    public function sessionClose()
    {
        $this->woocommerce_session_handler->save_data();
    }
}
