(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/autozpro-products-tabs.default', ($scope) => {

            let $tabs = $scope.find('.elementor-tabs-wrapper');
            let $contents = $scope.find('.elementor-tabs-content-wrapper');
            $contents.find('.elementor-tab-content').hide();
            // Active tab
            $contents.find('.elementor-active').show();
            let $carousel = $('.woocommerce-carousel ul', $scope);
            let $carousel_setting = $('.elementor-tabs-content-wrapper', $scope);
            let data = $carousel_setting.data('settings');

            $tabs.find('.elementor-tab-title').on('click', function () {
                $tabs.find('.elementor-tab-title').removeClass('elementor-active');
                $contents.find('.elementor-tab-content').removeClass('elementor-active').hide();
                $(this).addClass('elementor-active');
                let id = $(this).attr('aria-controls');
                $contents.find('#' + id).addClass('elementor-active').show();
                $carousel.slick('refresh');
            });


            if (typeof data === 'undefined') {
                return;
            }
            if (data['layout_carousel'] === true) {
                $carousel.slick({
                    dots: data.navigation === 'both' || data.navigation === 'dots' ? true : false,
                    arrows: data.navigation === 'both' || data.navigation === 'arrows' ? true : false,
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
                    // $carousel.find(".slick-slide").css('height', slick.$slideTrack.height() + 'px');
                });
            } else if (data['layout_carousel'] === false) {
                $carousel.slick({
                    dots: data.navigation === 'both' || data.navigation === 'dots' ? true : false,
                    arrows: data.navigation === 'both' || data.navigation === 'arrows' ? true : false,
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
                            breakpoint: parseInt(data.breakpoint_mobile),
                            settings: {
                                slidesToShow: parseInt(data.items_mobile),
                            }
                        }
                    ]
                }).on('setPosition', function (event, slick) {
                    slick.$slides.css('height', slick.$slideTrack.height() + 'px');
                    // $carousel.find(".slick-slide").css('height', slick.$slideTrack.height() + 'px');
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
