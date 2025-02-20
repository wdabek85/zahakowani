<h3>
    <?php _e("Order", "inpost-pay"); ?>
</h3>
<table class="gui-settings-table">
    <tr class="d-flex-align-center">
        <td>
            <?php _e("Show", "inpost-pay"); ?>
        </td>
        <td class="input-tooltip d-flex-align-center">
            <input <?= esc_attr(
                get_option("izi_show_order")
            ) == 1
                ? "checked"
                : "" ?> type="checkbox" name="izi_show_order" value="1">
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
            <select name="izi_place_order">
                <option>
                    <?php _e("Select", "inpost-pay"); ?>
                </option>
                <?php
                $orderPlaces = [
                    "woocommerce_before_order_notes" => __(
                        "Before order notes",
                        "inpost-pay"
                    ),
                    "woocommerce_after_order_notes" => __(
                        "After order notes",
                        "inpost-pay"
                    ),
                    "woocommerce_checkout_after_customer_details" => __(
                        "After customer details",
                        "inpost-pay"
                    ),
                    "woocommerce_checkout_before_order_review" => __(
                        "Before order review",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_before_cart_contents" => __(
                        "Before cart contents",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_after_cart_contents" => __(
                        "After cart contents",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_before_shipping" => __(
                        "Before shipping",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_after_shipping" => __(
                        "After shipping",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_before_order_total" => __(
                        "Before order total",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_after_order_total" => __(
                        "After order total",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_before_payment" => __(
                        "Before payment",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_before_submit" => __(
                        "Before submit",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_after_submit" => __(
                        "After submit",
                        "inpost-pay"
                    ),
                    "woocommerce_review_order_after_payment" => __(
                        "After payment",
                        "inpost-pay"
                    ),
                ];
                $selectedOrderPlace = esc_attr(
                    get_option("izi_place_order")
                );
                foreach (
                    $orderPlaces
                    as $value => $label
                ) {
                    $selected =
                        $value == $selectedOrderPlace
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
            <select name="izi_align_order">
                <option>
                    <?php _e("Select", "inpost-pay"); ?>
                </option>
                <?php

                foreach (
                    $availableAligns
                    as $value => $label
                ) {
                    $selected =
                        $value == esc_attr(get_option("izi_align_order"))
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
            <input name="izi_button_order_max_width" type="number" value="<?= get_option("izi_button_order_max_width"); ?>" min="220" max="600">
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
            <input name="izi_button_order_min_height" type="number" value="<?= get_option("izi_button_order_min_height"); ?>" min="48" max="64">
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
<?php
/* ?>
                            <h3>Lista produktów</h3>
                            <table>
                                <tr>
                                    <td>Wyświetlaj</td>
                                    <td>Wyrównanie</td>
                                    <td>Tło</td>
                                    <td>Wariant</td>

                                </tr>
                                <tr>
                                    <td>
                                        <input <?= esc_attr(get_option('izi_show_list')) == 1 ? 'checked' : '' ?> type="checkbox"
                                            name="izi_show_list" value="1">
                                    </td>
                                    <td>
                                        <select name="izi_align_list">
                                            <option>Wybierz</option>
                                            <?php
                                        $selectedOption = esc_attr(get_option('izi_align_list'));
                                        foreach ($availableAligns as $value => $label) {
                                            $selected = $value == $selectedOption ? 'selected' : '';
                                            echo "<option {$selected} value='{$value}'>{$label}</option>";
                                        }
                                        ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="izi_background_list">
                                            <option>Wybierz</option>
                                            <?php
                                        $selectedOption = esc_attr(get_option('izi_background_list'));
                                        foreach ($availableBackgrounds as $value => $label) {
                                            $selected = $value == $selectedOption ? 'selected' : '';
                                            echo "<option {$selected} value='{$value}'>{$label}</option>";
                                        }
                                        ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="izi_variant_list">
                                            <option>Wybierz</option>
                                            <?php
                                        $selectedOption = esc_attr(get_option('izi_variant_list'));
                                        foreach ($availableVariants as $value => $label) {
                                            $selected = $value == $selectedOption ? 'selected' : '';
                                            echo "<option {$selected} value='{$value}'>{$label}</option>";
                                        }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <?php */
?>
