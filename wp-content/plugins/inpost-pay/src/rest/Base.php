<?php

namespace Ilabs\Inpost_Pay\rest;

use Ilabs\Inpost_Pay\InpostPay;
use Ilabs\Inpost_Pay\Lib\helpers\LSCacheHelper;


abstract class Base
{
    protected array $post = [];
    protected array $get = [];
    protected array $delete = [];

    protected bool $restricted = false;

    abstract protected function describe();

    public function register(): void {
        if ($this->restricted && !$this->canAccess()) {
            return;
        }
        $this->describe();
        add_action('rest_api_init', function ($server) {
            foreach ($this->post as $path => $function) {
                $server->register_route('inpost', $path, [
                    'methods' => 'POST',
                    'callback' => function ($request) use ($function) {
                        $this->allowOriginHeader();
						LSCacheHelper::no_cache();
	                    RestRequest::setRequested();
                        return $function($request);
                    },
                    'permission_callback' => '__return_true',
                ]);
            }

            foreach ($this->get as $path => $function) {
                $server->register_route('inpost', $path, [
                    'methods' => 'GET',
                    'callback' => function ($request) use ($function) {
                        $this->allowOriginHeader();
	                    LSCacheHelper::no_cache();
	                    RestRequest::setRequested();
                        return $function($request);
                    },
                    'permission_callback' => '__return_true',
                ]);
            }

            foreach ($this->delete as $path => $function) {
                $server->register_route('inpost', $path, [
                    'methods' => 'DELETE',
                    'callback' => function ($request) use ($function) {
                        $this->allowOriginHeader();
	                    LSCacheHelper::no_cache();
	                    RestRequest::setRequested();
                        return $function($request);
                    },
                    'permission_callback' => '__return_true',
                ]);
            }
        });
    }

    /**
     * Check if the current request is allowed access.
     *
     * @return bool
     */
    private function canAccess(): bool
    {
        // Get the client IP address
        $IP = $_SERVER['X_REAL_IP'] ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

        // Define the local IP addresses
        $localIPs = ['127.0.0.1', '192.168', '10.', '172.25'];

        // Define the allowed IP addresses
        $allowedIPs = [
            '35.240.110.20',
            '35.241.226.162',
            '35.204.76.37',
            '34.76.56.163',
            '34.91.99.254',
            '35.240.60.16',
            '34.76.84.87',
            '35.190.198.36',
            '34.118.93.24',
            '34.116.145.216'
        ];

        // Check if the client IP is a local IP
        foreach ($localIPs as $localIP) {
            if (strpos($IP, $localIP) === 0) {
                return true;
            }
        }

        // Check if the client IP is an allowed IP
        return in_array( $IP, $allowedIPs, true );
    }

    protected function sseHeaders(): void {
	    @ini_set( 'zlib.output_compression', 0 );
	    @ini_set( 'implicit_flush', 1 );
	    if ( function_exists( 'apache_setenv' ) ) {
		    @apache_setenv( 'no-gzip', 1 );
	    }

	    session_write_close();
	    gc_enable();
	    ob_start();
	    ob_implicit_flush( 1 );

	    header( 'X-Accel-Buffering: no' );
	    header( 'Content-Type: text/event-stream' );


	    header( 'Connection: keep-alive' );
	    header( 'Access-Control-Expose-Headers: X-Events' );

	    header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
	    header( 'Pragma: no-cache"' );
	    header( 'X-LiteSpeed-Cache-Control: no-cache, no-store"' );

	    ob_end_flush();
	    if ( ob_get_contents() ) {
		    ob_end_clean();
	    }
    }

    protected function sendEventMessage($event, $data): void {
	    ob_start();
	    if ( ! empty( $data ) ) {
		    $jsonData = json_encode( $data );
		    print( "event: $event\n" );
		    print( "data: $jsonData\n\n" );
	    } else {
		    print( "event: time\n" );
		    print( "data: " . time() . "\n\n" );
	    }

	    $this->overrideBufferPull();
	    $this->flushMessage();

    }

    protected function sendHelloMessage(): void {
	    ob_start();
	    echo ": start\n\n";
	    $this->overrideBufferPull();
	    $this->flushMessage();
    }

    protected function overrideBufferPull(): void {
//        $this->flushMessage();
	    $ob = ob_get_status();
	    if ( ! empty( $ob ) && $ob['buffer_size'] > $ob['buffer_used'] ) {
		    echo str_pad( '', $ob['buffer_size'] - $ob['buffer_used'] ) . "\n";
	    }
    }

    protected function flushMessage(): void {
	    while ( ob_get_level() > 0 ) {
		    ob_end_flush();
		    if ( ob_get_contents() ) {
			    ob_end_clean();
		    }
	    }
	    flush();
    }

    protected function checkSignature($request, $force = false)
    {
	    $authorized = false;
	    $headers    = $request->get_headers();

	    $request_key_hash  = ( ! empty( $headers['x_public_key_hash'][0] ) ) ? $headers['x_public_key_hash'][0] : '';
	    $request_signature = ( ! empty( $headers['x_signature'][0] ) ) ? $headers['x_signature'][0] : '';
	    $request_time      = ( ! empty( $headers['x_signature_timestamp'][0] ) ) ? $headers['x_signature_timestamp'][0] : '';
	    $request_ver       = ( ! empty( $headers['x_public_key_ver'][0] ) ) ? $headers['x_public_key_ver'][0] : '';

	    $cached_keys = $this->getSignatureKeys( $force );

	    if ( ! empty( $cached_keys->hashes ) && in_array( $request_key_hash, $cached_keys->hashes ) ) {
		    $body                = $request->get_body();
		    $request_body        = ( ! empty( $body ) ) ? $body : '';
		    $request_body_hash   = hash( 'sha256', $request_body, true );
		    $digest              = base64_encode( $request_body_hash );
		    $merchant_id         = $cached_keys->merchant_external_id;
		    $generated_signature = base64_encode( "$digest,$merchant_id,$request_ver,$request_time" );
		    $api_key             = ( ! empty( $cached_keys->public_keys[0]->public_key_base64 ) ) ? $cached_keys->public_keys[0]->public_key_base64 : '';

		    $publicKey         = "-----BEGIN PUBLIC KEY-----\n" . $api_key . "\n-----END PUBLIC KEY-----";
		    $publicKeyResource = openssl_get_publickey( $publicKey );

		    if ( $publicKeyResource !== false ) {
			    $verifyResult = openssl_verify( $generated_signature, base64_decode( $request_signature ), $publicKeyResource, OPENSSL_ALGO_SHA256 );
			    if ( $verifyResult === 1 ) {
				    $request_timestamp = strtotime( $request_time );
				    if ( $request_timestamp <= time() + 240 ) {
					    $authorized = true;
				    }
			    }
		    }
	    }

	    if ( ! $authorized ) {
		    if ( ! $force ) {
			    $this->checkSignature( $request, true );
		    }
		    http_response_code( 401 );
		    die( json_encode( array( 'error_code' => 'INVALID_SIGNATURE' ) ) );
	    }

	    return true;
    }

    private function allowOriginHeader(): void
    {
        if ($this->restricted) {
            header("Access-Control-Allow-Origin: *");
        } else {
            if (array_key_exists("HTTP_ORIGIN", $_SERVER)) {
                $origin = $_SERVER["HTTP_ORIGIN"];
            } else if (array_key_exists("HTTP_REFERER", $_SERVER)) {
                $origin = $_SERVER["HTTP_REFERER"];
            } else {
                $origin = $_SERVER["REMOTE_ADDR"];
            }
            header("Access-Control-Allow-Origin: $origin");
        }
    }

    public function getSignatureKeys($force = false)
    {
        if ($force) {
	        delete_transient( 'izi_signing_keys' );
        }
        $keys = get_transient('izi_signing_keys');
        if (!$keys) {
            $response = InpostPay::getInstance()->getLib()->getController()->getSignatureKeys();

            if (!empty($response) && !empty($response->public_keys)) {
                $hashes = [];
                foreach ($response->public_keys as $key => $value) {
                    $hashes[] = hash('sha256', $value->public_key_base64);
                }
                $response->hashes = $hashes;
                set_transient('izi_signing_keys', $response, HOUR_IN_SECONDS);
                $keys = $response;
            }
        }
        return $keys;
    }
}
