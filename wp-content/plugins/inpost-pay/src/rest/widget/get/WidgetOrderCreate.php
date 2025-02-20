<?php

namespace Ilabs\Inpost_Pay\rest\widget\get;

use Ilabs\Inpost_Pay\hooks\DisplayWidget;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\rest\Base;

class WidgetOrderCreate extends Base {

	protected function describe() {

		$this->get['/inpost/v1/izi/widget/place_order_create'] = function (
			$request
		) {
			if ( esc_attr( get_option( 'izi_show_order' ) ) ) {

				ob_start();
				( new DisplayWidget() )->displayOrder();
				header('Content-Type:text/html; charset=UTF-8');
				die( ob_get_clean() );
			}
			die;
		};
	}

}
