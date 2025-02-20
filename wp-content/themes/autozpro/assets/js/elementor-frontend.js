(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction('frontend/element_ready/column', function ($scope) {
            if ($scope.hasClass('animated-slide-column')) {
                new Waypoint({
                    element: $scope,
                    offset: '50%',
                    handler: function () {
                        $scope.addClass('col-loaded');
                    }
                });
            }

            if ($scope.hasClass('animated-bg-parallax')) {
                var $wrap = $scope.find('>[class*="elementor-column-"]');
                var linkImage = $wrap.css('backgroundImage').replace('url(', '').replace(')', '').replace(/\"/gi, "");
                if (linkImage === 'none') {
                    return;
                }
                $wrap.prepend('<img src="' + linkImage + '" class="img-banner-parallax" alt="banner-parallax"/>')

                $wrap.find('>.img-banner-parallax').panr({
                    moveTarget: $wrap,
                    sensitivity: 20,
                    scale: false,
                    scaleOnHover: true,
                    scaleTo: 1.1,
                    scaleDuration: .25,
                    panY: true,
                    panX: true,
                    panDuration: 1.25,
                    resetPanOnMouseLeave: true
                });
            }
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            $(window).load(function () {
                if ($scope.hasClass('animated-bg-parallax')) {
                    var linkImage = $scope.css('backgroundImage').replace('url(', '').replace(')', '').replace(/\"/gi, "");
                    if (linkImage === 'none') {
                        return;
                    }
                    $scope.prepend('<img src="' + linkImage + '" class="img-banner-parallax" alt="banner-parallax" />')
                    $scope.find('>.img-banner-parallax').panr({
                        moveTarget: $scope,
                        sensitivity: 20,
                        scale: false,
                        scaleOnHover: true,
                        scaleTo: 1.1,
                        scaleDuration: .25,
                        panY: true,
                        panX: true,
                        panDuration: 1.25,
                        resetPanOnMouseLeave: false
                    });
                }
            })
        })

    })

})(jQuery)
