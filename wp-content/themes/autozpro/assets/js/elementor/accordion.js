(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/accordion.default', ($scope) => {
            let $accordion = $scope.find('.elementor-accordion');
            let $first_child = $scope.find('.elementor-accordion-item:first-child');
            $first_child.addClass("elementor-active");

            $accordion.find('.elementor-accordion-item').on('click', function () {
                $accordion.find('.elementor-accordion-item').removeClass('elementor-active');
                $(this).addClass('elementor-active');
            });
        });
    });

})(jQuery);
