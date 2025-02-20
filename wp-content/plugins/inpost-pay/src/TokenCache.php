<?php

namespace Ilabs\Inpost_Pay;

use Ilabs\Inpost_Pay\Lib\Authorization;

class TokenCache
{
    function getCachedToken(bool $renew = false)
    {
        $date = get_option('izi_keyclock_token_date');
        $maxInterval = intval(get_option('izi_keyclock_token_expiration'));
        if (!$date) {
            Logger::log("TOKEN: No date for token");
            if (!$renew) {
                Logger::log("TOKEN: try to renew token");
                $this->renewToken();
                $this->getCachedToken(true);
            }
            return null;
        }
        if (!$maxInterval) {
            Logger::log("TOKEN: No expiration set");
            if (!$renew) {
                Logger::log("TOKEN: try to renew token");
                $this->renewToken();
                $this->getCachedToken(true);
            }
            return null;
        }

        $now = time();
        $interval = ($date - $now);
        if ($interval <= 0) {
            Logger::log("TOKEN: token too old");
            if (!$renew) {
                Logger::log("TOKEN: try to renew token");
                $this->renewToken();
                $this->getCachedToken(true);
            }
            return null;
        }
        return get_option('izi_keyclock_token');
    }

    function setCachedToken($token, $expiration)
    {
        update_option('izi_keyclock_token', $token);
        update_option('izi_keyclock_token_date', time()+$expiration);
        update_option('izi_keyclock_token_expiration', $expiration);
    }

    /**
     * A private function to renew the token.
     */
    private function renewToken(): void
    {
        $authorization = new Authorization();
        $authorization->getToken();
    }
}
