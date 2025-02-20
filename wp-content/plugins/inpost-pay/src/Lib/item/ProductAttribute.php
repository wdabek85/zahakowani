<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class ProductAttribute extends Item {
	protected string $attribute_name;
	protected string $attribute_value;

	/**
	 * @param $attribute_name
	 * @param $attribute_value
	 */
	public function __construct( $attribute_name, $attribute_value ) {
		$this->attribute_name  = wc_attribute_label( $attribute_name ) ?: $attribute_name;
		$this->attribute_value = strip_tags( $attribute_value );

		if ( strlen( $this->attribute_name ) === 0 && strlen( $this->attribute_value ) > 0 ) {
			$this->attribute_name = 'O';
		}
	}


}
