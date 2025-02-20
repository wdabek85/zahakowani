<div class="button-wrapper">
    <div class="button-wrapper-left-side">
        <h2>
            <?php _e("Button appearance", "inpost-pay"); ?>
        </h2>
        <h3>
            <?php _e("Display", "inpost-pay"); ?>
        </h3>
        <table class="gui-settings-table">
            <tr>
                <td>
                    <?php _e("Background", "inpost-pay"); ?>
                </td>
                <td class="input-tooltip d-flex-align-center">
                    <select id="izi-background-select" name="izi_background">
                        <?php
                        $selectedOption = esc_attr(
                            get_option("izi_background")
                        );
                        foreach (
                            $availableBackgrounds
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
                                    "Determines the background theme",
                                    "inpost-pay"
                                ); ?></p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php _e("Variant", "inpost-pay"); ?>
                </td>
                <td class="input-tooltip d-flex-align-center">
                    <select id="izi-variant-select" name="izi_variant">
                        <?php
                        $selectedOption = esc_attr(
                            get_option("izi_variant")
                        );
                        foreach (
                            $availableVariants
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
                                    "Determines the variant of button",
                                    "inpost-pay"
                                ); ?></p>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <?php _e("Round style", "inpost-pay"); ?>
                </td>
                <td class="input-tooltip d-flex-align-center">
                    <select id="izi-frame-style-select" name="izi_frame_style">
                        <?php
                        $selectedOption = esc_attr(
                            get_option("izi_frame_style")
                        );
                        foreach (
                            $availableFrameStyle
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
                                    "Determines the button frame style",
                                    "inpost-pay"
                                ); ?></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="button-wrapper-right-side">
    </div>
</div>
<hr>