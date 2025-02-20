<div class="agreements-container">
	<?php
	if ( is_array( get_option( "izi_consents" ) ) ) {
		$consents = get_option( "izi_consents" );
	} else {
		$consents = [];
	}
	$filtered = [];
	foreach ( $consents as $id => $c ) {
		if ( $c["text"] ) {
			if ( isset( $c["additional_consent_links"] ) && count( $c["additional_consent_links"] ) > 0 ) {
				$additional_consent_links = $c["additional_consent_links"];
				unset( $c['additional_consent_links'] );
				$i = 0;
				foreach ( $additional_consent_links as $additional_link_id => $additional_link ) {
					$c['additional_consent_links'][ $i ] = $additional_link;
					$i ++;
				}
			}
			$filtered[] = $c;
		}

	}
	$consents = $filtered;
	if ( count( $consents ) < 10 ) {
		$consents[] = [
			"url"      => "",
			"text"     => "",
			"required" => "",
		];
	}

	?>

	<div id="consentList">
		<?php foreach ( $consents as $id => $consent ): ?>
			<div class="consent-item" data-consent-id="<?= $id ?>">
				<div class="d-flex-align-center">

					<div style="flex:50%" class="flex-50">
						<label><?php _e(
								"Descriptions visible in application",
								"inpost-pay"
							); ?></label>
						<br/>
						<textarea class="consentDescription" rows="2" cols="50" maxlength="500"
								  name="izi_consents[<?= $id ?>][text]"><?= $consent["text"] ?></textarea>
						<div class="input-tooltip-wrapper">
							<img src="<?php echo plugin_dir_url(
													 __FILE__
												 ) .
												 "../../assets/img/tooltip.svg"; ?>" alt="">
							<div class="input-tooltip-box">
								<p><?php _e(
										"Add a description to be displayed with the agreement in the InPost mobile application",
										"inpost-pay"
									); ?></p>
							</div>
						</div>
					</div>


					<div style="flex:30%">
						<div class="input-tooltip d-flex-align-center">
							<label class="consent-label"><?php _e(
									"Is it required",
									"inpost-pay"
								); ?></label>
							<div class="input-tooltip-wrapper">
								<img src="<?php echo plugin_dir_url(
														 __FILE__
													 ) .
													 "../../../assets/img/tooltip.svg"; ?>" alt="">
								<div class="input-tooltip-box">
									<p><?php _e(
											"Specify the page to which your customer will be redirected when clicking on a specific agreement in the InPost mobile application",
											"inpost-pay"
										); ?></p>
								</div>
							</div>
						</div>
						<select class="requirementType" name="izi_consents[<?= $id ?>][required]">
							<?php
							$selectedOption =
								$consent["required"];
							foreach (
								$consentRequirement
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
					</div>


					<div style="flex:20%">
						<button type="button" class="remove-btn" onclick="removeConsentItem( this )">
							<img src="<?php echo plugin_dir_url(
													 __FILE__
												 ) .
												 "../../../assets/img/remove.svg"; ?>" alt="">
							<?php _e( "Remove", "inpost-pay" ); ?>
						</button>
					</div>
				</div>

				<?php if ( ! empty( $consent['url'] ) && empty( $consent['additional_consent_links'] ) ) : ?>
					<input type="hidden" name="izi_consents[<?= $id ?>][url]" value="<?= $consent["url"] ?>"/>
				<?php else: ?>
					<input type="hidden" name="izi_consents[<?= $id ?>][url]" value=""/>
				<?php endif; ?>
				<?php
				if ( ! empty( $consent['url'] ) && empty( $consent['additional_consent_links'] ) ) {
					$slug                                  = get_post_field( 'post_name', (int) $consent['url'] );
					$consent['additional_consent_links'][] = [
						'id'    => $slug,
						'label' => get_the_title( (int) $consent['url'] ),
						'url'   => $consent['url']
					];
				}
				if ( ! empty( $consent['additional_consent_links'] ) ) {
					$lastKey = key( array_slice( array_keys( $consent['additional_consent_links'] ), - 1, 1, true ) );
				} else {
					$lastKey = 0;
				}
				?>
				<div class="additional-consent-links">
					<!-- <label><?php _e( "Additional Consent Links:", "inpost-pay" ); ?></label> -->
					<div id="additionalLinks<?= $id ?>" class="justify-content-between additional-links-container"
						 data-last-key="<?= $lastKey ?>">
						<?php

						if ( isset( $consent['additional_consent_links'] ) && count( $consent['additional_consent_links'] ) > 0 ):
							foreach ( $consent['additional_consent_links'] as $additional_link_id => $additional_link ): ?>
								<div class="d-flex-align-center additional-link-container">
									<div>
										<label><?php _e( "Link identifier", "inpost-pay" ); ?></label>
										<br/>
										<input type="text"
											   name="izi_consents[<?= $id ?>][additional_consent_links][<?= $additional_link_id ?>][id]"
											   class="additional-link-identifier"
											   value="<?= empty( $additional_link["id"] ) ? $additional_link_id : $additional_link["id"] ?>">
									</div>
									<div>
										<label><?php _e( "Link label", "inpost-pay" ); ?></label>
										<br/>
										<input type="text"
											   name="izi_consents[<?= $id ?>][additional_consent_links][<?= $additional_link_id ?>][label]"
											   class="additional-link-label"
											   value="<?= $additional_link["label"] ?? '' ?>"
											   placeholder="<?= get_the_title( (int) $additional_link["url"] ) ?>">
									</div>
									<div>
										<label for="consentLink" class="consent-label"><?php _e(
												"Agreement address",
												"inpost-pay"
											); ?></label>
										<br/>
										<?php
										wp_dropdown_pages( [
											"name"             =>
												"izi_consents[$id][additional_consent_links][$additional_link_id][url]",
											"selected"         => $additional_link["url"],
											"show_option_none" => __(
												"Select",
												"inpost-pay"
											),
											"class"            => "additional-consent-link",
										] );
										?>
									</div>
									<div>
										<button type="button"
												class="remove-additional-link-btn"
												onclick="removeAdditionalLink( this )"><?php _e( "Remove", "inpost-pay" ); ?>
										</button>
									</div>
								</div>
							<?php
							endforeach;
						endif;
						?>
					</div>
					<hr>
					<?php
					$displayButton = '';
					if ( isset( $consent['additional_consent_links'] ) && count( $consent['additional_consent_links'] ) >= 3 ) {
						$displayButton = 'style="display: none;"';
					}
					?>
					<button type="button" class="add-additional-link-btn" <?= $displayButton ?>
							onclick="addAdditionalLink(<?= $id ?>)">
						+ <?php _e( "Add additional link", "inpost-pay" ); ?></button>
				</div>

			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $consents ) > 9 ) {
		$displayButtonAddConsentButton = 'style="display: none;"';
	} ?>

	<button id="addConsentButton" type="button" onclick="addConsentItem()" <?= $displayButtonAddConsentButton ?? '' ?>>
		+ <?php _e( "Add Consent", "inpost-pay" ); ?></button>


	<div id="additionalLinkTemplate">
		<div class="d-flex-align-center justify-content-between additional-link-container">
			<div style="flex: 25%">
				<label><?php _e( "Link identifier", "inpost-pay" ); ?></label>
				<br/>
				<input type="text" name="none" class="additional-link-identifier">
			</div>
			<div style="flex: 25%">
				<label><?php _e( "Link label", "inpost-pay" ); ?></label>
				<br/>
				<input type="text" name="none" class="additional-link-label">
			</div>
			<div style="flex: 25%">
				<label for="consentLink" class="consent-label"><?php _e(
						"Agreement address",
						"inpost-pay"
					); ?></label>
				<br/>
				<?php wp_dropdown_pages( [
					"name"             => "none",
					"show_option_none" => __(
						"Select",
						"inpost-pay"
					),
					"class"            => "additional-consent-link",
				] ); ?>
			</div>
			<div style="flex: 25%">
				<button type="button" class="remove-additional-link-btn">
					<img src="<?php echo plugin_dir_url(
											 __FILE__
										 ) .
										 "../../../assets/img/remove.svg"; ?>" alt="">
					<?php _e( "Remove", "inpost-pay" ); ?>
				</button>
			</div>
		</div>
	</div>


	<div id="consentTemplate">
		<div class="consent-item" data-consent-id="1">
			<div class="d-flex-align-center">

				<div style="flex:50%" class="flex-50">


					<label><?php _e(
							"Descriptions visible in application",
							"inpost-pay"
						); ?></label>
					<textarea class="consentDescription" rows="2" cols="50" maxlength="500"></textarea>
					<div class="input-tooltip-wrapper">
						<img src="<?php echo plugin_dir_url(
												 __FILE__
											 ) .
											 "../../assets/img/tooltip.svg"; ?>" alt="">
						<div class="input-tooltip-box">
							<p><?php _e(
									"Add a description to be displayed with the agreement in the InPost mobile application",
									"inpost-pay"
								); ?></p>
						</div>
					</div>


				</div>
				<div style="flex:30%" class="flex-30">


					<div class="input-tooltip d-flex-align-center">
						<label><?php _e(
								"Is it required",
								"inpost-pay"
							); ?></label>

						<div class="input-tooltip-wrapper">
							<img src="<?php echo plugin_dir_url(
													 __FILE__
												 ) .
												 "../../../assets/img/tooltip.svg"; ?>" alt="">
							<div class="input-tooltip-box">
								<p><?php _e(
										"Specify whether the agreement is required or optional",
										"inpost-pay"
									); ?></p>
							</div>
						</div>
						<br/>
						<select class="requirementType" name="izi_consents[<?= $id ?>][required]">
							<?php
							foreach (
								$consentRequirement
								as $value => $label
							) {
								$selected =
									$value == $selectedOption
										? "selected"
										: "";
								echo "<option value='{$value}'>{$label}</option>";
							}
							?>
						</select>
					</div>


				</div>
				<div style="flex:20%" class="flex-20">


					<button type="button" class="remove-btn">
						<img src="<?php echo plugin_dir_url(
												 __FILE__
											 ) .
											 "../../../assets/img/remove.svg"; ?>" alt="">
						<?php _e( "Remove", "inpost-pay" ); ?>
					</button>


				</div>
			</div>

			<div class="form-row input-tooltip d-flex-align-center">
				<label for="consentLink"><?php _e(
						"Agreement address",
						"inpost-pay"
					); ?></label>
				<?php wp_dropdown_pages( [
					"name"             => "none",
					"show_option_none" => __(
						"Select",
						"inpost-pay"
					),
					"class"            => "consentLink",
				] ); ?>
				<div class="input-tooltip-wrapper">
					<img src="<?php echo plugin_dir_url(
											 __FILE__
										 ) .
										 "../../../assets/img/tooltip.svg"; ?>" alt="">
					<div class="input-tooltip-box">
						<p><?php _e(
								"Specify the page to which your customer will be redirected when clicking on a specific agreement in the InPost mobile application",
								"inpost-pay"
							); ?></p>
					</div>
				</div>
			</div>

			<div class="additional-consent-links">
				<label><?php _e( "Additional Consent Links:", "inpost-pay" ); ?></label>
				<div class="justify-content-between additional-links-container">
				</div>
				<hr>
				<button type="button"
						class="add-additional-link-btn">+ <?php _e( "Add additional link", "inpost-pay" ); ?></button>
			</div>
		</div>
	</div>
</div>
