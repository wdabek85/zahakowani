<?php

namespace Ilabs\Inpost_Pay;

use Exception;
use Ilabs\Inpost_Pay\Lib\InPostIzi;

class Logger
{
    private static ?string $clientSessionId = null;

    private static ?bool $nullLogger = null;

    public function __construct()
    {

    }

    public static function log($data)
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[general: %s]',
			(is_array($data) || is_object($data))?print_r($data, true):$data
        ));
    }


    public static function debug($data, $debugTrace = false)
    {
        if (self::isNullLogger()) return;

        if ($debugTrace) {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $trace = ob_get_contents();
            ob_end_clean();
            self::write(sprintf('[debug: %s][trace: %s]',
                $data,
                $trace
            ));
        } else {
            self::write($data);
        }



    }

    public static function spam($data)
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[spam: %s]',
            print_r($data, true)
        ));
    }

    public static function response($data, $info = '')
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[response_get: %s][info: %s]',
            var_export($data, true),
            $info
        ));
    }

    /**
     * @param string $command
     * @param string $type
     * @param $withCode
     * @param $raw
     * @param mixed $data
     * @throws Exception
     */
    public static function request(
        string $command,
        string $type,
               $withCode,
               $raw,
               $data
    ): void
    {
        if (self::isNullLogger()) return;

        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $trace = ob_get_contents();
        ob_end_clean();
        self::write(sprintf('[Connection] [request] [url: %s] [type: %s] [withCode: %s] [raw: %s] [data: %s] [backtrace: %s]',
            InPostIzi::getApiUrl() . "/$command",
            $type,
            print_r($withCode, true),
            var_export($raw, true),
            var_export($data, true),
            var_export($trace,
                true)
        ));
    }

    public static function dataRead($data, $header = '')
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[data_event: %s]',
            print_r($data, true)
        ));
    }

    public static function orderEvent($data, $header = '')
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[order_event: %s]',
            print_r($data, true)
        ));
    }

    public static function basketEvent($data, $header = '')
    {
        if (self::isNullLogger()) return;

        self::write(sprintf('[basket_event: %s]',
            print_r($data, true)
        ));
    }

    public static function log_headers_sent()
    {
        self::isNullLogger();

        $headers = headers_list();

        self::write(
            sprintf('[headers_sent: %s]',
                print_r($headers, true)
            ));

    }

    /**
     * @throws Exception
     */
    private static function write($data)
    {

        $logger = inpost_pay()->get_woocommerce_logger();
        $data = sprintf('[ID: %s] %s', self::getSessionCustomerId(), $data);
        $logger->log_debug($data);

    }

    private static function getSessionCustomerId(): ?string
    {
        if (self::$clientSessionId === null) {
            self::$clientSessionId = InPostIzi::getStorage()->getSessionCustomerId();
        }
        return self::$clientSessionId;
    }

    private static function isNullLogger(): bool
    {
        if (self::$nullLogger === null) {
            self::$nullLogger = !get_option('izi_debug');
        }
        if (self::$nullLogger) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public static function rawData($data, $header = '')
    {
        self::write(sprintf('[header: %s][raw: %s]',
			$header,
            print_r($data, true)
        ));
    }

    private static function addTimestamp($header)
    {
        $date = date('Y-m-d H:i:s');
        return "[{$date}] " . $header;
    }
}
