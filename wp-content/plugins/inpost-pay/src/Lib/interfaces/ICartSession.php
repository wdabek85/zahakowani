<?php

namespace Ilabs\Inpost_Pay\Lib\interfaces;

interface ICartSession
{
    public static function storeCurrent(): void;

    public static function setSessionByCartId($id): void;

    public static function setOrderToCart($cartId, $orderId, $redirectUrl): void;

    public static function setConfirmationToCart($cartId, $confirmation): void;

    public static function getCartOrderRedirectUrl($cartId): ?string;

    public static function getCartConfirmation($cartId): ?string;
}