(function ($) {
    'use strict';
    
    function shippingPanel() {
        var $shippingPanel = $('#shipping-panel');
        var $overlay = $('.side-panel-overlay');
        var $body = $('body');

        if (!$shippingPanel.length || !$overlay.length) {
            return;
        }

        // Otwieranie panelu + pokazanie overlay
        $body.on('click', '#open-shipping-panel', function (e) {
            e.preventDefault();
            $shippingPanel.addClass('active');
            $overlay.addClass('active'); // Teraz overlay nie będzie migał
        });

        // Zamykanie panelu + ukrycie overlay
        $('.close-cart-side, .side-panel-overlay').on('click', function (e) {
            e.preventDefault();
            $shippingPanel.removeClass('active');
            $overlay.removeClass('active'); // Ukrycie overlay bez migotania
        });

        // Zamknięcie panelu po kliknięciu poza nim
        $(document).mouseup(function (e) {
            if (!$shippingPanel.is(e.target) && $shippingPanel.has(e.target).length === 0) {
                $shippingPanel.removeClass('active');
                $overlay.removeClass('active');
            }
        });
    }

    $(document).ready(function () {
        shippingPanel();
    });

})(jQuery);


