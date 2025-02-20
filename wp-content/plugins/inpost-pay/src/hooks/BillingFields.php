<?php

namespace Ilabs\Inpost_Pay\hooks;

class BillingFields extends Base
{

    public function attachHook()
    {
        add_filter('woocommerce_admin_billing_fields', function ($fields) {
            $fields['invoice_note'] = ['label' => 'Uwagi', 'show' => true];
            return $fields;
        });
    }
}