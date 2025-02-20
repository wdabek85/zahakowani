(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/autozpro-brand.default', ($scope) => {
            var $carousel = $('.autozpro-carousel', $scope);

            if ($carousel.length > 0) {
                var data = $carousel.data('settings'),
                    rtl = $('body').hasClass('rtl') ? true : false;
                $carousel.slick({
                    rtl: rtl,
                    dots: data.navigation == 'both' || data.navigation == 'dots' ? true : false,
                    arrows: data.navigation == 'both' || data.navigation == 'arrows' ? true : false,
                    infinite: data.loop,
                    speed: 300,
                    slidesToShow: parseInt(data.items),
                    autoplay: data.autoplay,
                    autoplaySpeed: data.autoplaySpeed,
                    slidesToScroll: 1,
                    lazyLoad: 'ondemand',
                    responsive: [
                        {
                            breakpoint: parseInt(data.breakpoint_laptop),
                            settings: {
                                slidesToShow: parseInt(data.items_laptop),
                            }
                        },
                        {
                            breakpoint: parseInt(data.breakpoint_tablet_extra),
                            settings: {
                                slidesToShow: parseInt(data.items_tablet_extra),
                            }
                        },
                        {
                            breakpoint: parseInt(data.breakpoint_tablet),
                            settings: {
                                slidesToShow: parseInt(data.items_tablet),
                            }
                        },
                        {
                            breakpoint: parseInt(data.breakpoint_mobile_extra),
                            settings: {
                                slidesToShow: parseInt(data.items_mobile_extra),
                            }
                        },
                        {
                            breakpoint: parseInt(data.breakpoint_mobile),
                            settings: {
                                slidesToShow: parseInt(data.items_mobile),
                            }
                        }
                    ]
                }).on('setPosition', function (event, slick) {
                    slick.$slides.css('height', slick.$slideTrack.height() + 'px');
                    $carousel.find(".slick-slide").css('height', slick.$slideTrack.height() + 'px');
                });
            }
        });
    });

})(jQuery);
