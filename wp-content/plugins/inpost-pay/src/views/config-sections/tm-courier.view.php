<div class="consent-item">
    <h3 class="mb-2 text-bold">
        <?php _e(
            "Package on Weekend (PWW) Courier:",
            "inpost-pay"
        ); ?>
    </h3>
    <table class="net-transport-price-table">
        <tr>
            <td>
                <?php _e("Price", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <input type="text" name="izi_transport_price_pww_courier" value="<?= esc_attr(
                    get_option(
                        "izi_transport_price_pww_courier"
                    )
                ) ?>">
            </td>
        </tr>
        <tr>
            <td>
                <?php _e("Available from", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <select name="izi_transport_available_from_day_pww_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_from_day_pww_courier"
                        )
                    );
                    foreach (
                        $daysOfWeek
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}</option>";
                    }
                    ?>
                </select>
                <select name="izi_transport_available_from_hour_pww_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_from_hour_pww_courier"
                        )
                    );
                    foreach (
                        $hoursOfDay
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}:00</option>";
                    }
                    ?>
                </select>
            </td>
            </td>
        </tr>
        <script src="https://izi.inpost.pl/inpostizi.js"></script>
        <tr>
            <td>
                <?php _e("Available to", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <select name="izi_transport_available_to_day_pww_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_to_day_pww_courier"
                        )
                    );
                    foreach (
                        $daysOfWeek
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}</option>";
                    }
                    ?>
                </select>
                <select name="izi_transport_available_to_hour_pww_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_to_hour_pww_courier"
                        )
                    );
                    foreach (
                        $hoursOfDay
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}:00</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
</div>
<div class="consent-item">
    <h3 class="my-2 text-bold">
        <?php _e(
            "Cash on Delivery (COD) Courier:",
            "inpost-pay"
        ); ?>
    </h3>
    <table class="net-transport-price-table">
        <tr>
            <td>
                <?php _e("Price", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <input type="text" name="izi_transport_price_cod_courier" value="<?= esc_attr(
                    get_option(
                        "izi_transport_price_cod_courier"
                    )
                ) ?>">
            </td>
        </tr>
        <tr>
            <td>
                <?php _e("Available from", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <select name="izi_transport_available_from_day_cod_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_from_day_cod_courier"
                        )
                    );
                    foreach (
                        $daysOfWeek
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}</option>";
                    }
                    ?>
                </select>
                <select name="izi_transport_available_from_hour_cod_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_from_hour_cod_courier"
                        )
                    );
                    foreach (
                        $hoursOfDay
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}:00</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <?php _e("Available to", "inpost-pay"); ?>
            </td>
            <td class="input-tooltip d-flex-align-center">
                <select name="izi_transport_available_to_day_cod_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_to_day_cod_courier"
                        )
                    );
                    foreach (
                        $daysOfWeek
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}</option>";
                    }
                    ?>
                </select>
                <select name="izi_transport_available_to_hour_cod_courier">
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_available_to_hour_cod_courier"
                        )
                    );
                    foreach (
                        $hoursOfDay
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
                                ? "selected"
                                : "";
                        echo "<option {$selected} value='{$value}'>{$label}:00</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
</div>
    <div class="consent-item">
    <div class="input-wrapper">
        <div class="form-group form-group--row">
            <div class="input-tooltip d-flex-align-center">
                <select name="izi_transport_method_courier">
                    <option>
                        <?php _e(
                            "Select",
                            "inpost-pay"
                        ); ?>
                    </option>
                    <?php
                    $selectedOption = esc_attr(
                        get_option(
                            "izi_transport_method_courier"
                        )
                    );
                    foreach (
                        $availableShippingMethods
                        as $value => $label
                    ) {
                        $selected =
                            $value == $selectedOption
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
                                "Determines which shipping method is to be associated",
                                "inpost-pay"
                            ); ?></p>
                    </div>
                </div>
            </div>
            <label>
                <?php _e(
                    "Prices and courier shipping availability map with:",
                    "inpost-pay"
                ); ?>
            </label>
        </div>
    </div>
</div>
<hr>