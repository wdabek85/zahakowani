(function ($) {
    'use strict';

    function autozproquantity() {

        var $quantityBoxes = $('.quantity:not(.buttons_added):not(.hidden)').find('.qty');

        if ($quantityBoxes && 'date' != $quantityBoxes.prop('type')) {

            // Add plus and minus boxes

            $quantityBoxes.parent().addClass('buttons_added').prepend('<button type="button" class="minus" ><i class="autozpro-icon-minus"></i></button>');
            $quantityBoxes.addClass('input-text').after('<button type="button" class="plus"><i class="autozpro-icon-plus"></i></button>');

            // Target quantity inputs on product pages
            $('input' + '.qty' + ':not(.product-quantity input' + '.qty' + ')').each(function () {
                var $min = parseFloat($(this).attr('min'));

                if ($min && $min > 0 && parseFloat($(this).val()) < $min) {
                    $(this).val($min);
                }
            });


            $('.plus, .minus').unbind('click');

            $('.plus, .minus').on('click', function () {

                    // Get values
                    var $quantityBox = $(this).parent().find('.qty'),
                        $currentQuantity = parseFloat($quantityBox.val()),
                        $maxQuantity = parseFloat($quantityBox.attr('max')),
                        $minQuantity = parseFloat($quantityBox.attr('min')),
                        $step = $quantityBox.attr('step');

                    // Fallback default values
                    if (!$currentQuantity || '' === $currentQuantity || 'NaN' === $currentQuantity) {
                        $currentQuantity = 0;
                    }
                    if ('' === $maxQuantity || 'NaN' === $maxQuantity) {
                        $maxQuantity = '';
                    }

                    if ('' === $minQuantity || 'NaN' === $minQuantity) {
                        $minQuantity = 0;
                    }
                    if ('any' === $step || '' === $step || undefined === $step || 'NaN' === parseFloat($step)) {
                        $step = 1;
                    }

                    // Change the value
                    if ($(this).is('.plus')) {

                        if ($maxQuantity && ($maxQuantity == $currentQuantity || $currentQuantity > $maxQuantity)) {
                            $quantityBox.val($maxQuantity);
                        } else {
                            $quantityBox.val($currentQuantity + parseFloat($step));
                        }

                    } else {

                        if ($minQuantity && ($minQuantity == $currentQuantity || $currentQuantity < $minQuantity)) {
                            $quantityBox.val($minQuantity);
                        } else if ($currentQuantity > 0) {
                            $quantityBox.val($currentQuantity - parseFloat($step));
                        }

                    }

                    // Trigger change event
                    $quantityBox.trigger('change');
                }
            );
        }
    }
    autozproquantity();

    $(document).on('woosq_loaded qv_loader_stop updated_wc_div autozpro-products-loaded',function () {
        autozproquantity();
    });

})(jQuery);
