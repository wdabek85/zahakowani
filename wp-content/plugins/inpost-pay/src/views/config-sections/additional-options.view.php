<div class="input-wrapper">
    <div class="form-group">
        <label>
            <?php _e(
                "SEE delay time response:",
                "inpost-pay"
            ); ?>
        </label>
        <div class="input-tooltip">
            <input type="number" min="0" max="3" step="0.1" name="izi_sse_sleep_time"
                   value="<?= esc_attr(
                       get_option("izi_sse_sleep_time")
                   ) ?>">
            <div class="input-tooltip-wrapper">
                <img src="<?php echo plugin_dir_url(
                        __FILE__
                    ) .
                    "../../../assets/img/tooltip.svg"; ?>" alt="">
                <div class="input-tooltip-box">
                    <p><?php _e(
                            'The pause time in seconds between server responses. Can be between 0.1 to 3 seconds',
                            "inpost-pay"
                        ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
