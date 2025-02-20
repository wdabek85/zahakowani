<?php
$attribution_config    = new \Ilabs\Inpost_Pay\Lib\config\attribution\AttributionConfig();
$attribution_enable    = $attribution_config->get_form_field();
$attribution_overrides_config = new \Ilabs\Inpost_Pay\Lib\config\attribution\AttributionOverridesConfig();
$attribution_overrides = $attribution_overrides_config->get_form_field();

?>
<div>
	<p>
	<p><?php $attribution_enable->print_label_text() ?></p>
	<div class="toggleWrapper">
		<?php $attribution_enable->print_field(); ?>
		<label for="<?= $attribution_enable->get_label_name() ?>"></label>
	</div>
	<div><?php echo __( $attribution_config->get_description(), 'inpost-pay' ) ?></div>

</div>

<div>
	<p><?php $attribution_overrides->print_label_text() ?></p>
	<div class="toggleWrapper">
		<?php $attribution_overrides->print_field(); ?>
		<label for="<?= $attribution_overrides->get_label_name() ?>"></label>
	</div>
	<div><?php echo __( $attribution_overrides_config->get_description(), 'inpost-pay' ) ?></div>
</div>
