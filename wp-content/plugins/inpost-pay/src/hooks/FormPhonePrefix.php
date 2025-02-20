<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\WooCommerceInPostIzi;

class FormPhonePrefix extends Base
{
    public function attachHook()
    {
        add_filter( 'woocommerce_checkout_fields' , [$this, 'addBillingPhonePrefix'] );
        add_action( 'woocommerce_checkout_process', [$this, 'alterPhoneNumber'] );
    }
    public function addBillingPhonePrefix($fields)
    {
        $fields['billing']['billing_phone_prefix'] = [
            'type' => 'tel',
            'label' => 'Prefiks kraju',
            'placeholder' => '48',
            'priority' => 99,
            'class' => [
                0 => 'form-row-first'
            ],
            'required' => true
        ];

        $fields['billing']['billing_phone']['class'][0] = 'form-row-last';

        return $fields;
    }

    public function alterPhoneNumber()
    {
      $_POST['billing_phone'] = $_POST['billing_phone_prefix']." ".$_POST['billing_phone'];
    }
}
