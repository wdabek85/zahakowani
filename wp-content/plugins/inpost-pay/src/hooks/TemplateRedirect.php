<?php

namespace Ilabs\Inpost_Pay\hooks;

use Ilabs\Inpost_Pay\Lib\InPostIzi;

class TemplateRedirect extends Base
{

    public function attachHook()
    {
        add_action( 'template_redirect', [$this, 'thankYouPage'] );
    }

    public function thankYouPage()
    {
        if (!isset($_COOKIE['izi_basket_id'])) {
            return false;
        }
        $id = sanitize_text_field($_COOKIE['izi_basket_id']);
        $model = InPostIzi::getCartSessionClass()::getObjectById( $id );
        if ( ! $model || ! $model->id ) {
            return false;
        }
        $redirectUrl = isset( $model->confirmation_response ) && $model->confirmation_response == 'deleted' ? 'deleted' : $model->redirect_url;
        if ( $redirectUrl && $redirectUrl != 'deleted' ) {
            if ( ! InPostIzi::getCartSessionClass()::getRedirectedById( $id ) ) {
                InPostIzi::getCartSessionClass()::setRedirectedById( $id,
                    1 );

                wp_redirect( $redirectUrl );
                exit;
            }
        }

        return false;
    }
}