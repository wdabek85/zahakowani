<?php

namespace Ilabs\Inpost_Pay\Lib;

use Ilabs\Inpost_Pay\Logger;
use Ilabs\Inpost_Pay\models\CartSession;

class BasketIdentification
{
    const INPOSTIZI_BASKET_ID = "inpostizi_basket_id";
    public static string $inpostiziBasketId = '';

    public static string $browserId = '';

    public static function getFromSession()
    {
        $identificationStored = InPostIzi::getStorage()->findSession(self::INPOSTIZI_BASKET_ID);
        if ($identificationStored && InPostIzi::getCartSessionClass()::getRedirectedById($identificationStored) != 0) {
            InPostIzi::getStorage()->eraseSession(self::INPOSTIZI_BASKET_ID);
            InPostIzi::getStorage()->eraseSession('binding_get');
            unset($_COOKIE[self::INPOSTIZI_BASKET_ID]);
        }

        if ($identificationStored && InPostIzi::getCartSessionClass()::getCartOrderRedirectUrl($identificationStored) === 'deleted') {
            InPostIzi::getStorage()->eraseSession(self::INPOSTIZI_BASKET_ID);
            InPostIzi::getStorage()->eraseSession('binding_get');
        }

        $identificationStored = InPostIzi::getStorage()->findSession(self::INPOSTIZI_BASKET_ID);

        if ($identificationStored) {
            self::$inpostiziBasketId = $identificationStored;
            return $identificationStored;
        }

        $identificationGenerated = IdentificationGenerator::generate();
        InPostIzi::getStorage()->insertSession(self::INPOSTIZI_BASKET_ID, $identificationGenerated);
        self::$inpostiziBasketId = $identificationGenerated;

        return $identificationGenerated;
    }
    public static function get(): string
    {
        if (!self::$inpostiziBasketId) {
            return self::getFromSession();
        }
        return self::$inpostiziBasketId;
    }

	public static function set($id): void {
		self::$inpostiziBasketId = $id;
	}

    public static function drop()
    {
        Logger::log("DROPPING IDENTIFICATION");
        InPostIzi::getStorage()->eraseSession(self::INPOSTIZI_BASKET_ID);
        InPostIzi::getStorage()->eraseSession('binding_get');
    }

}
