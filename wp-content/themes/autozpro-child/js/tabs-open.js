jQuery(document).ready(function ($) {
    // Usuń eventy kliknięć na zakładkach
    $(document).off('click', '.wc-tabs li a, ul.tabs li a');
    $(document).off('click', 'h2.resp-accordion');
    $('.wc-tabs li a, ul.tabs li a, h2.resp-accordion').unbind('click');

    // Wymuś otwarcie wszystkich zakładek
    $('.wc-tab, .panel:not(.panel .panel)').css({
        'display': 'block', 
        'visibility': 'visible',
        'opacity': '1',
        'height': 'auto'
    }).addClass('active');

    // Nadaj klasę aktywną wszystkim zakładkom i ich tytułom
    $('.wc-tabs li, ul.tabs li, .resp-accordion').addClass('active');

    // Zapobiegaj ukrywaniu zakładek przez WooCommerce
    $('.wc-tabs-wrapper, .woocommerce-tabs').css('display', 'block');

    // Usunięcie stylów inline, które mogły je ukrywać
    $('.wc-tab, .panel:not(.panel .panel)').removeAttr('style');

    console.log("✅ Wszystkie zakładki WooCommerce są aktywne i otwarte.");
});