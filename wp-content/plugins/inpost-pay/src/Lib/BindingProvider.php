<?php

namespace Ilabs\Inpost_Pay\Lib;

class BindingProvider
{
    private static $binding;

    public static function getBinding($force = false)
    {
        if (!self::$binding || $force) {
            self::$binding = InPostIzi::getInstance()->getController()->basketBindingGet($force);
        }
        return self::$binding;
    }
}