<h3>
    <?php _e("Minicart", "inpost-pay"); ?>
</h3>
<table class="gui-settings-table">
    <tr class="d-flex-align-center">
        <td>
            <?php _e("Show", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <input <?= esc_attr(
                get_option("izi_show_minicart")
            ) == 1
                ? "checked"
                : "" ?> type="checkbox" name="izi_show_minicart" value="1">
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            "To increase conversion, we recommend displaying InPost Pay on both the cart and product pages",
                            "inpost-pay"
                        ); ?></p>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e("Placement", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <select name="izi_place_minicart">
                <option>
                    <?php _e("Select", "inpost-pay"); ?>
                </option>
                <?php
                $minicartPlaces = [
                    "woocommerce_before_mini_cart" => __(
                        "Before minicart",
                        "inpost-pay"
                    ),
                    "woocommerce_before_mini_cart_contents" => __(
                        "Before minicart contents",
                        "inpost-pay"
                    ),
                    "woocommerce_mini_cart_contents" => __(
                        "Minicart contents",
                        "inpost-pay"
                    ),
                    "woocommerce_after_minicart_billing_form" => __(
                        "After billing form",
                        "inpost-pay"
                    ),
                    "woocommerce_before_minicart_shipping_form" => __(
                        "Before shipping form",
                        "inpost-pay"
                    ),
                    "woocommerce_after_minicart_shipping_form" => __(
                        "After shipping form",
                        "inpost-pay"
                    ),
                    "woocommerce_minicart_after_customer_details" => __(
                        "After customer details",
                        "inpost-pay"
                    ),
                    "woocommerce_minicart_before_order_review" => __(
                        "Before order review",
                        "inpost-pay"
                    ),
                    "woocommerce_minicart_after_order_review" => __(
                        "After order review",
                        "inpost-pay"
                    ),
                    "woocommerce_after_minicart_form" => __(
                        "After minicart form",
                        "inpost-pay"
                    ),
                ];
                $selectedMinicartPlace = esc_attr(
                    get_option("izi_place_minicart")
                );
                foreach (
                    $minicartPlaces
                    as $value => $label
                ) {
                    $selected =
                        $value == $selectedMinicartPlace
                            ? "selected"
                            : "";
                    echo "<option {$selected} value='{$value}'>{$label}</option>";
                }
                ?>
            </select>
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            "For WooCommerce cart subpages, you can add widgets in various parts of the page. Choose a location that fits your template, following the instructions available in the Merchant Guide",
                            "inpost-pay"
                        ); ?></p>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e("Alignment", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <select name="izi_align_minicart">
                <option>
                    <?php _e("Select", "inpost-pay"); ?>
                </option>
                <?php
                foreach (
                    $availableAligns
                    as $value => $label
                ) {
                    $selected =
                        $value == esc_attr(
							get_option("izi_align_minicart")
						)
                            ? "selected"
                            : "";
                    echo "<option {$selected} value='{$value}'>{$label}</option>";
                }
                ?>
            </select>
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            "Specify the orientation of the widget in the available space. If your template allocates a narrow space for the widget, the setting will not affect the appearance",
                            "inpost-pay"
                        ); ?></p>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e("Button width", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <input name="izi_button_minicart_max_width" type="number" value="<?= get_option("izi_button_minicart_max_width"); ?>" min="220" max="600">
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            "Specifies the maximum width that the widget should occupy. Note: The widget adjusts its width to the container it is in. If the parent container has a width smaller than max_width, then the widget will reach the dimensions of the parent container. A good idea is to use the additional min-width css style directly on the inpost-izi-button to get the best matching effects. It takes values from 220 to 600.",
                            "inpost-pay"
                        ); ?></p>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e("Button height", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <input name="izi_button_minicart_min_height" type="number" value="<?= get_option("izi_button_minicart_min_height"); ?>" min="48" max="64">
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            "Specifies the minimum height that the widget should occupy. It should take values from 48 to 64.",
                            "inpost-pay"
                        ); ?></p>
                </div>
            </div>
        </td>
    </tr>
</table>
<hr>
