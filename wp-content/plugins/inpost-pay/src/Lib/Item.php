<?php

namespace Ilabs\Inpost_Pay\Lib;

use ErrorException;
use JsonException;

class Item {
	/**
	 * @throws ErrorException
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		} else {
			$this->throwNonExistent( $property );
		}
	}

	public function __isset( $property ) {
		return property_exists( $this, $property );
	}

	/**
	 * @throws ErrorException
	 */
	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}

		$this->throwNonExistent( $property );
	}

	public function toArray(): array {
		$vars = get_object_vars( $this );

		foreach ( $vars as $key => $value ) {
			if ( $value instanceof self ) {
				$vars[ $key ] = $value->toArray();
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $smallKey => $smallValue ) {
					if ( $smallValue instanceof self ) {
						$vars[ $key ][ $smallKey ] = $smallValue->toArray();
					}
				}
			}
		}

		return $vars;
	}

	/**
	 * @throws JsonException
	 */
	public function encode() {
		$data = $this->toArray();

		return json_encode( $data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE );
	}

	public function getProducts() {
		return $this->toArray()["products"];
	}

	/**
	 * @throws JsonException
	 */
	public function compareProduct( $product ): bool {
		return json_encode( $this->getProducts(), JSON_THROW_ON_ERROR ) === json_encode( $product, JSON_THROW_ON_ERROR );
	}

	/**
	 * @param $property
	 *
	 * @throws ErrorException
	 */
	protected function throwNonExistent( $property ): void {
		$class = get_class( $this );
		throw new ErrorException( "Property not existing {$property} in {$class}" );
	}
}
