(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/autozpro-product-related.default', ($scope) => {
            let $carousel = $('.woocommerce-carousel', $scope);
            if ($carousel.length > 0) {
                let data = $carousel.data('settings'),
                rtl = $('body').hasClass('rtl') ? true : false;
                $('ul.products', $carousel).slick({
                    rtl: rtl,
                    dots: data.navigation == 'both' || data.navigation == 'dots' ? true : false,
                    arrows: data.navigation == 'both' || data.navigation == 'arrows' ? true : false,
                    infinite: data.loop,
                    speed: 300,
                    slidesToShow: parseInt(data.items),
                    autoplay: data.autoplay,
                    autoplaySpeed: parseInt(data.autoplayTimeout),
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
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                            }
                        },
                        {
                            breakpoint: 500,
                            settings: {
                                slidesToShow: 1,
                            }
                        }
                    ]
                }).on('setPosition', function (event, slick) {
                    slick.$slides.css('height', slick.$slideTrack.height() + 'px');
                    $carousel.find(".slick-slide").css('height', slick.$slideTrack.height() + 'px');
                });

            }

            $('.product-block', $scope).each(function (i, obj) {
                let $this = $(this);
                let heightHideInfo = $('.product-caption-bottom', $this).outerHeight();

                $('.content-product-imagin', $this).css({
                    marginBottom: -heightHideInfo
                });
            });
        });
    });

})(jQuery);
