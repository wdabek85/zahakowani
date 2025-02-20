<?php

namespace Ilabs\Inpost_Pay\Integration\Basket\Availability;

use Ilabs\Inpost_Pay\Logger;

abstract class AbstractAvailabilityIntegration implements AvailabilityIntegrationInterface {

	protected array $cart_item;

	protected ?\WC_Product $product = null;

	/**
	 * @param array $cart_item
	 *
	 * @throws ProductIsEmptyException
	 */
	public function __construct( array $cart_item ) {
		$this->cart_item = $cart_item;
		$this->setProduct();
		if ( $this->isEmpty() ) {
			throw new ProductIsEmptyException( $cart_item );
		}

	}

	private function isEmpty(): bool {
		if ( empty( $this->product ) ) {
			Logger::debug( '[Add to Basket] Empty product' );

			return true;
		}

		return false;
	}

	protected function isPurchasable(): bool {
		if ( ! $this->product->is_purchasable() ) {
			Logger::debug( '[Add to Basket] Product is not purchasable ' . $this->product->get_id() );

			return false;
		}

		return true;
	}

	protected function isInStock(): bool {
		if ( ! $this->product->is_in_stock() ) {
			Logger::debug( '[Add to Basket] Product is out of stock ' . $this->product->get_id() );

			return false;
		}

		return true;
	}


	protected function isVirtual(): bool {
		if ( $this->product->is_virtual() ) {
			Logger::debug( '[Add to Basket] Product is virtual ' . $this->product->get_id() );

			return true;
		}

		return false;
	}

	protected function isVisible(): bool {
		if ( ! $this->product->is_visible() ) {
			Logger::debug( '[Add to Basket] Product is not visible ' . $this->product->get_id() );

			return false;
		}

		return true;
	}

	private function setProduct() {
		if ( isset( $this->cart_item['data'] ) && $this->cart_item['data'] instanceof \WC_Product ) {
			$this->product = $this->cart_item['data'];
		}
	}

	public function checkAvailability(): bool {
		if ( ! $this->isInStock() ) {
			return false;
		}

		if ( ! $this->isPurchasable() ) {
			return false;
		}

		if ( ! $this->isVisible() ) {
			return false;
		}

		if ( $this->isVirtual() ) {
			return false;
		}

		return true;
	}


}
