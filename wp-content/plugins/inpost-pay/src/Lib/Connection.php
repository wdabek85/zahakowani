<?php

namespace Ilabs\Inpost_Pay\Lib;

use Ilabs\Inpost_Pay\Lib\exception\AuthorizationException;
use Ilabs\Inpost_Pay\Lib\exception\InvalidClientCredentialsException;
use Ilabs\Inpost_Pay\Lib\exception\InvalidClientSecretException;
use Ilabs\Inpost_Pay\Logger;

class Connection extends Fetcher {

	private Authorization $authorization;

	private ?string $token;

	public function __construct() {
		$this->authorization = new Authorization();
		try {
			$this->token = $this->authorization->getToken();
		} catch ( AuthorizationException $e ) {
			$this->token = null;
		}

		parent::__construct();
	}

	public function request(
		$command,
		$type = "GET",
		$data = [],
		$withCode = false,
		$raw = false
	) {

		Logger::request(
			$command,
			$type,
			$withCode,
			$raw,
			$data,
		);

		if ( $this->token !== null ) {

			$response = $this->fetch( InPostIzi::getApiUrl() . "/$command", $type,
				$data, $withCode, $raw );

			Logger::response(
				empty( $response ) ? '(empty)' : print_r( $response, true ),
			);

			return $response;
		}

		return [];
	}


	public function headers(): array {
		return [
			"Authorization: Bearer {$this->token}",
		];
	}
}
