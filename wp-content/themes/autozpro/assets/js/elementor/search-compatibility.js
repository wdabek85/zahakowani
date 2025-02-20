(function ($) {
    "use strict";
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/autozpro-search-compatibility.default', ($scope) => {
            // Vehicle select.
            const widget = $('.search_compatibility', $scope);

            widget.each(function () {
                const ajaxUrl = widget.data('ajax-url');
                const nonce = widget.data('nonce');
                const selects = widget.find('select');
                const button = widget.find('button');
                selects.select2({width: 'resolve'});

                let selectedVehicle;
                widget.on('autozpro-vehicle-select.change', function (event, vehicle) {
                    selectedVehicle = vehicle;
                });

                button.on('click', function (event) {
                    event.preventDefault();
                    if (selectedVehicle) {
                        window.location.href = selectedVehicle;
                    }
                });

                selects.on('change', function () {
                    const control = $(this);
                    const item = control.closest('.autozpro-vehicle-select__item');
                    const index = widget.find('.autozpro-vehicle-select__item').index(item);
                    const value = control.val();
                    const currentItem = widget.find('.autozpro-vehicle-select__item').slice(index, index + 1);
                    const nextItem = widget.find('.autozpro-vehicle-select__item').slice(index + 1, index + 2);
                    const nextAllItems = widget.find('.autozpro-vehicle-select__item').slice(index + 1);

                    const data = {};

                    widget.find('.autozpro-vehicle-select__item').slice(0, index + 1).find('select').each(function () {
                        data[this.name] = JSON.parse(this.value);
                    });

                    if (value !== 'null') {
                        nextAllItems.addClass('autozpro-vehicle-select__item--disabled');
                        nextAllItems.removeClass('autozpro-vehicle-select__item--activate');
                        nextAllItems.find('select').prop('disabled', true).val('null');
                        nextItem.removeClass('autozpro-vehicle-select__item--disabled');
                        nextItem.removeClass('autozpro-vehicle-select__item--activate');
                        nextItem.find('select').prop('disabled', false);
                        currentItem.addClass('autozpro-vehicle-select__item--activate');
                    } else {
                        nextAllItems.addClass('autozpro-vehicle-select__item--disabled');
                        nextAllItems.removeClass('autozpro-vehicle-select__item--activate');
                        nextAllItems.find('select').prop('disabled', true).val('null');
                    }

                    nextAllItems.find('select').trigger('change.select2');

                    if ('null' === value) {
                        widget.trigger('autozpro-vehicle-select.change', null);
                    } else if (index + 1 === widget.find('.autozpro-vehicle-select__item').length) {
                        widget.trigger('autozpro-vehicle-select.change', JSON.parse(item.find('select').val()));
                    } else {
                        widget.trigger('autozpro-vehicle-select.change', null);
                        nextItem.addClass('autozpro-vehicle-select__item--loading');

                        $.post(ajaxUrl, {
                            action: 'autozpro_sputnik_vehicle_select_load_data',
                            nonce: nonce,
                            data: {
                                for: nextItem.find('select').prop('name'),
                                values: data,
                            },
                        }, function (response) {
                            if (response.success) {
                                nextItem.find('select option:not([value="null"])').remove();
                                response.data.forEach(function (optionData) {
                                    const option = $('<option></option>');
                                    // alert(optionData.title.__( 'Could not set that as the thumbnail image. Try a different attachment.' ) );
                                    option.text(optionData.title);
                                    option.attr('value', JSON.stringify(optionData.value));
                                    nextItem.find('select').append(option);
                                });

                                nextItem.removeClass('autozpro-vehicle-select__item--loading');
                            }
                        });
                    }
                });
            });

        });
    });

})(jQuery);
