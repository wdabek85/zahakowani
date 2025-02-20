<?php

namespace Ilabs\Inpost_Pay\rest\widget\get;

use Ilabs\Inpost_Pay\hooks\DisplayWidget;
use Ilabs\Inpost_Pay\Lib\BasketIdentification;
use Ilabs\Inpost_Pay\rest\Base;

class WidgetMinicart extends Base {

	protected function describe() {

		$this->get['/inpost/v1/izi/widget/place_minicart'] = function (
			$request
		) {
			if ( esc_attr( get_option( 'izi_show_minicart' ) ) ) {

				ob_start();
				( new DisplayWidget() )->displayMinicart();
				header('Content-Type:text/html; charset=UTF-8');
				die( ob_get_clean() );
			}
			die;
		};
	}

}
