(function ($, elementorFrontend, elementorModules) {
    'use strict';
    var _sticky = elementorModules.frontend.handlers.Base.extend({
        bindEvents() {
            elementorFrontend.addListenerOnce(this.getUniqueHandlerID() + 'sticky', 'resize', this.run);
        },

        unbindEvents() {
            elementorFrontend.removeListeners(this.getUniqueHandlerID() + 'sticky', 'resize', this.run);
        },

        isStickyInstanceActive() {
            return undefined !== this.$element.data('sticky');
        },

        /**
         * Get the current active setting value for a responsive control.
         *
         * @param {string} setting
         *
         * @return {any} - Setting value.
         */
        getResponsiveSetting(setting) {
            const elementSettings = this.getElementSettings();
            return elementorFrontend.getCurrentDeviceSetting(elementSettings, setting);
        },

        /**
         * Return an array of settings names for responsive control (e.g. `settings`, `setting_tablet`, `setting_mobile` ).
         *
         * @param {string} setting
         *
         * @return {string[]} - List of settings.
         */
        getResponsiveSettingList(setting) {
            const breakpoints = Object.keys(elementorFrontend.config.responsive.activeBreakpoints);
            return ['', ...breakpoints].map(suffix => {
                return suffix ? `${setting}_${suffix}` : setting;
            });
        },

        activate() {
            var elementSettings = this.getElementSettings(),
                stickyOptions = {
                    to: elementSettings.sticky,
                    offset: this.getResponsiveSetting('sticky_offset'),
                    effectsOffset: this.getResponsiveSetting('sticky_effects_offset'),
                    classes: {
                        sticky: 'elementor-sticky',
                        stickyActive: 'elementor-sticky--active elementor-section--handles-inside',
                        stickyEffects: 'elementor-sticky--effects',
                        spacer: 'elementor-sticky__spacer'
                    }
                },
                $wpAdminBar = elementorFrontend.elements.$wpAdminBar;

            if (elementSettings.sticky_parent) {
                stickyOptions.parent = '.elementor-widget-wrap';
            }

            if ($wpAdminBar.length && 'top' === elementSettings.sticky && 'fixed' === $wpAdminBar.css('position')) {
                stickyOptions.offset += $wpAdminBar.height();
            }

            this.$element.sticky(stickyOptions);
        },

        deactivate() {
            if (!this.isStickyInstanceActive()) {
                return;
            }

            this.$element.sticky('destroy');
        },

        run(refresh) {
            if (!this.getElementSettings('sticky')) {
                this.deactivate();
                return;
            }

            var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
                activeDevices = this.getElementSettings('sticky_on');

            if (-1 !== activeDevices.indexOf(currentDeviceMode)) {
                if (true === refresh) {
                    this.reactivate();
                } else if (!this.isStickyInstanceActive()) {
                    this.activate();
                }
            } else {
                this.deactivate();
            }
        },

        reactivate() {
            this.deactivate();
            this.activate();
        },

        onElementChange(settingKey) {
            if (-1 !== ['sticky', 'sticky_on'].indexOf(settingKey)) {
                this.run(true);
            } // Settings that trigger a re-activation when changed.


            const settings = [...this.getResponsiveSettingList('sticky_offset'), ...this.getResponsiveSettingList('sticky_effects_offset'), 'sticky_parent'];

            if (-1 !== settings.indexOf(settingKey)) {
                this.reactivate();
            }
        },

        /**
         * Listen to device mode changes and re-initialize the sticky.
         *
         * @return {void}
         */
        onDeviceModeChange() {
            // Wait for the call stack to be empty.
            // The `run` function requests the current device mode from the CSS so it's not ready immediately.
            // (need to wait for the `deviceMode` event to change the CSS).
            // See `elementorFrontend.getCurrentDeviceMode()` for reference.
            setTimeout(() => {
                this.run(true);
            });
        },

        onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

            if (elementorFrontend.isEditMode()) {
                elementor.listenTo(elementor.channels.deviceMode, 'change', () => this.onDeviceModeChange());
            }

            this.run();
        },

        onDestroy() {
            elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
            this.deactivate();
        }
    });

    $( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {
            elementorFrontend.elementsHandler.addHandler( _sticky, {
                $element,
            } );
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/section', addHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', addHandler );
    } );

}(jQuery, window.elementorFrontend, window.elementorModules));
