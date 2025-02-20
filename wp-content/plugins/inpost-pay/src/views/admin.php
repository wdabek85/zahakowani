<?php
/**
 * @var $checked callable
 * @var $availableShippingMethods array
 * @var $availableAligns array
 * @var $availableBackgrounds array
 * @var $availableVariants array
 * @var $consentRequirement array
 * @var $daysOfWeek array
 * @var $hoursOfDay array
 * @var $button_cart_margin array
 * @var $button_cart_padding array
 * @var $button_details_margin array
 * @var $button_details_padding array
 */
?>
<div id="inpostpayadmin" class="wrap">
    <div class="banner">
        <img src="<?php echo plugin_dir_url(__FILE__) .
            "../../assets/img/banner.png"; ?>" alt="">
    </div>
    <div class="content">

        <?php
		if (isset($_GET['settings-updated'])) {
			$this->check_authorization();
			if (empty(get_settings_errors('izi_messages'))) {
				add_settings_error('izi_messages', 'izi_message', __('Settings Saved', 'inpost-pay'), 'updated');
			}
		}


        settings_errors('izi_messages');
        ?>
        <form method="post" action="options.php" data-duplicate-validation-message="<?php esc_attr_e("The value repeats itself", "inpost-pay"); ?>" data-required-validation-message="<?php esc_attr_e("This field is required", "inpost-pay"); ?>">
            <?php settings_fields("inpost-izi"); ?>
            <?php do_settings_sections("inpost-izi"); ?>
            <nav class="nav-tab-wrapper">
                <button type="button" data-tab-id="api" class="nav-tab nav-tab-active"><?php _e("Settings", "inpost-pay"); ?></button>
                <button type="button" data-tab-id="agreements" class="nav-tab"><?php _e("Agreements", "inpost-pay"); ?></button>
                <button type="button" data-tab-id="prices" class="nav-tab"><?php _e("Price of transport", "inpost-pay"); ?></button>
                <button type="button" data-tab-id="gui" class="nav-tab"><?php _e("Button appearance", "inpost-pay"); ?></button>
				<?php  if ( version_compare( WC()->version, '8.6', '>' ) ) {  ?>
				<button type="button" data-tab-id="marketing" class="nav-tab"><?php _e("Marketing", "inpost-pay"); ?></button>
				<?php } ?>

				<button type="button" data-tab-id="support" class="nav-tab"><?php _e("Support", "inpost-pay"); ?></button>
            </nav>
            <div data-tab-id="api" class="tab-content">
                <h2>
                    <?php _e("Settings", "inpost-pay"); ?>
                </h2>
                <?php
                require_once(__DIR__ . '/config-sections/environment.view.php');
                require_once(__DIR__ . '/config-sections/payment.view.php');
                require_once(__DIR__ . '/config-sections/statuses.view.php');
                require_once(__DIR__ . '/config-sections/product-desc-source.view.php');
				require_once(__DIR__ . '/config-sections/suggested-products.view.php');
                require_once(__DIR__ . '/config-sections/additional-options.view.php');
                ?>
            </div>
            <div data-tab-id="agreements" class="tab-content tab-content-hidden tab-content-no-background">
                <h2>
                    <?php _e("Agreements", "inpost-pay"); ?>
                </h2>
                <?php
                require_once(__DIR__ . '/config-sections/agreements.view.php');
                ?>
            </div>
            <div data-tab-id="prices" class="tab-content tab-content-hidden tab-content-no-background">
                <h2>
                    <?php _e("Net transport price", "inpost-pay"); ?>
                </h2>
                <?php
                require_once(__DIR__ . '/config-sections/net-transport-price.view.php');

				require_once(__DIR__ . '/config-sections/tm-courier.view.php');
                require_once(__DIR__ . '/config-sections/tm-apm.view.php');

				//require_once(__DIR__ . '/config-sections/tm-courier-new.view.php');
                //require_once(__DIR__ . '/config-sections/tm-apm-new.view.php');


				require_once(__DIR__ . '/config-sections/tm-additional-options.view.php');
                ?>


            </div>

            <div data-tab-id="gui" class="tab-content tab-content-hidden">
                <?php
                require_once(__DIR__ . '/config-sections/button-appearance.view.php');
                require_once(__DIR__ . '/config-sections/cart-gui.view.php');
                require_once(__DIR__ . '/config-sections/order-gui.view.php');
				require_once(__DIR__ . '/config-sections/checkout-gui.view.php');
				require_once(__DIR__ . '/config-sections/login-page-gui.view.php');
				require_once(__DIR__ . '/config-sections/minicart-gui.view.php');
                require_once(__DIR__ . '/config-sections/product-card-gui.view.php');
                ?>
            </div>
			<?php if ( version_compare( WC()->version, '8.6', '>' ) ) { ?>
			<div data-tab-id="marketing" class="tab-content tab-content-hidden">
				<?php
				require_once(__DIR__ . '/config-sections/marketing-order-attribution.view.php');
				?>
			</div>
			<?php } ?>
            <div data-tab-id="support" class="tab-content tab-content-bg-gray tab-content-hidden">
                <div class="support-tab">
                    <div class="support-tab-box div1">
                        <div class="support-tab-left-side">
                            <h3 class="support-tab-title"><?php _e(
                                    "The configuration of the InPost Pay plugin",
                                    "inpost-pay"
                                ); ?></h3>
                            <p><?php esc_html_e("Ensure proper exposure of the InPost Pay service, allowing for:", "inpost-pay"); ?></p>
                            <ul>
                                <li><?php esc_html_e("Buyers quickly recognize that by making a purchase in your store, they can take advantage of a fast and secure purchasing and delivery service through a company they know and trust, directly contributing to their purchasing decisions.", "inpost-pay"); ?></li>
                                <li><?php esc_html_e("With the widget placed correctly, you help your customers notice the InPost Pay service, allowing them to complete purchases on your website without the need to provide their data, which is especially important for customers who prefer anonymous shopping without logging in.", "inpost-pay"); ?></li>
                            </ul>
                            <p><?php esc_html_e('Please familiarize yourself with the dedicated Merchant Guide regarding the proper implementation of the InPost Pay service. It gathers information on the current branding of InPost Pay, guidelines for the visual implementation of the widget, and best practices worth applying to build a positive user experience among online buyers. You will find the Merchant Guide in the "useful links" section.', "inpost-pay"); ?></p>
                        </div>
                        <div class="support-tab-right-side video-container">
                            <iframe src="https://www.youtube.com/embed/xw25aFIUIIo?si=UPHLJuBICpqn_Z4M"
                                    title="YouTube video player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="div2">
                        <div class="support-tab-box">
                            <div class="support-tab-top-bar support-tab-top-bar-flex-start">
                                <div class="support-tab-icon-wrapper">
                                    <img src="<?php echo plugin_dir_url(
                                            __FILE__
                                        ) .
                                        "../../assets/img/accept.svg"; ?>" alt="">
                                </div>
                                <h3 class="support-tab-secondary-title"><?php _e(
                                        "Plugin status",
                                        "inpost-pay"
                                    ); ?>: <span class="text-green">OK</span></h3>
                            </div>
                            <p class="support-tab-info-text">
                                <?php _e(
                                    "To collect data from the log, enable debugging appropriately beforehand",
                                    "inpost-pay"
                                ); ?>
                            </p>
                            <div class="support-tab-bottom support-tab-bottom-two-columns">
                                <div class="support-tab-bottom-left-side">
                                    <a href="#">
                                        <img src="<?php echo plugin_dir_url(
                                                __FILE__
                                            ) .
                                            "../../assets/img/files.svg"; ?>" alt="">
                                        <?php _e(
                                            "Copy plugin data and logs",
                                            "inpost-pay"
                                        ); ?></a>
                                </div>
                                <div class="support-tab-bottom-right-side">
                                    <div class="toggleWrapper">
                                        <input class="mobileToggle" type="checkbox" id="debug" name="izi_debug"
                                               value="1" <?= get_option(
                                            "izi_debug"
                                        ) == 1
                                            ? "checked"
                                            : "" ?>>
                                        <label for="debug"></label>
                                    </div>
                                    <p><?php _e(
                                            "Enable debbuging",
                                            "inpost-pay"
                                        ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="support-tab-box support-tab-box-full-size">
                            <div class="support-tab-top-bar">
                                <div class="support-tab-top-bar-left-side">
                                    <div class="support-tab-icon-wrapper">
                                        <img src="<?php echo plugin_dir_url(
                                                __FILE__
                                            ) .
                                            "../../assets/img/calendar.svg"; ?>" alt="">
                                    </div>
                                    <h3 class="support-tab-secondary-title"><?php _e(
                                            "News",
                                            "inpost-pay"
                                        ); ?></h3>
                                </div>
                            </div>
                            <div class="support-tab-bottom">
                                <ul class="support-tab-bottom-list">
                                    <li class="support-tab-bottom-item">
                                        <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/132382721/InPost+Pay+-+Woocommerce"
                                           target="_blank"><?php esc_html_e("Current version of the plugin", "inpost-pay"); ?>
                                            <nr wersji>
                                        </a>
                                    </li>
                                    <li class="support-tab-bottom-item">
                                        <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/132382721/InPost+Pay+-+Woocommerce"
                                           target="_blank"><?php esc_html_e("Version", "inpost-pay"); ?> v1.2</a>
                                    </li>
                                    <li class="support-tab-bottom-item">
                                        <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/132382721/InPost+Pay+-+Woocommerce"
                                           target="_blank"><?php esc_html_e("Version", "inpost-pay"); ?> v1.1</a>
                                    </li>
                                    <li class="support-tab-bottom-item">
                                        <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/132382721/InPost+Pay+-+Woocommerce"
                                           target="_blank"><?php esc_html_e("Version", "inpost-pay"); ?> v1.0</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="support-tab-box div4">
                        <div class="support-tab-top-bar">
                            <div class="support-tab-top-bar-left-side">
                                <div class="support-tab-icon-wrapper">
                                    <img src="<?php echo plugin_dir_url(
                                            __FILE__
                                        ) .
                                        "../../assets/img/info.svg"; ?>" alt="">
                                </div>
                                <h3 class="support-tab-secondary-title"><?php _e(
                                        "Useful links",
                                        "inpost-pay"
                                    ); ?></h3>
                            </div>
                        </div>
                        <div class="support-tab-bottom">
                            <ul class="support-tab-bottom-list">
                                <li class="support-tab-bottom-item">
                                    <a href="https://inpost.pl/inpostpay"
                                       target="_blank"><?php esc_html_e("Merchant Guide - How to properly display InPost Pay in your online store", "inpost-pay"); ?></a>
                                </li>
                                <li class="support-tab-bottom-item">
                                    <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/132382721/InPost+Pay+-+Woocommerce"
                                       target="_blank"><?php esc_html_e("Instructions for Configuring the InPost Pay Plugin", "inpost-pay"); ?></a>
                                </li>
                                <li class="support-tab-bottom-item">
                                    <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/154271751/Zwroty+i+transakcje#Panel-Merchanta"
                                       target="_blank"><?php esc_html_e("Returns handling instructions", "inpost-pay"); ?></a>
                                </li>
                                <li class="support-tab-bottom-item">
                                    <a href="https://malaysia.prod.0000wpo12a.vodeno.online/centaur-web/"
                                       target="_blank"><?php esc_html_e("Merchant panel - transaction preview and returns handling", "inpost-pay"); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="support-tab-box div5">
                        <div class="support-tab-top-bar">
                            <div class="support-tab-top-bar-left-side">
                                <div class="support-tab-icon-wrapper">
                                    <img src="<?php echo plugin_dir_url(
                                            __FILE__
                                        ) .
                                        "../../assets/img/info.svg"; ?>" alt="">
                                </div>
                                <h3 class="support-tab-secondary-title"><?php _e(
                                        "Contact and Support",
                                        "inpost-pay"
                                    ); ?></h3>
                            </div>
                        </div>
                        <div class="support-tab-bottom">
                            <ul class="support-tab-bottom-list">
                                <li class="support-tab-bottom-item">
                                    <a href="https://inpostpay.pl/kontakt"
                                       target="_blank"><?php esc_html_e("Technical support - please use the contact form", "inpost-pay"); ?></a>
                                </li>
                                <li class="support-tab-bottom-item">
                                    <a href="https://inpostpay.pl/kontakt"
                                       target="_blank"><?php esc_html_e("Contact a sales representative", "inpost-pay"); ?></a>
                                </li>
                                <li class="support-tab-bottom-item">
                                    <a href="https://dokumentacja-inpost.atlassian.net/wiki/spaces/PL/pages/163905538/FAQ+InPost+Pay"
                                       target="_blank"><?php esc_html_e("FAQ", "inpost-pay"); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            </nav>
            <div class="submit-wrapper">
                <input type="submit" class="button button-primary" value="Zapisz zmiany">
            </div>
        </form>
    </div>
</div>
