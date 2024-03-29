<?php
	$ajax_nonce = wp_create_nonce( "snapshot-save-key" );
?>

<div id="ss-show-apikey">

	<div id="wps-snapshot-key" class="snapshot-three wps-popup-modal"><?php // Use "show" class to show the popup, or else remove it to hide popup ?>

		<div class="wps-popup-mask"></div>

		<div class="wps-popup-content">

			<div class="wpmud-box">

				<div class="wpmud-box-title can-close">

					<h3><?php _e('Add Snapshot Key', SNAPSHOT_I18N_DOMAIN); ?></h3>

					<i class="wps-icon i-close"></i>

				</div>

				<div class="wpmud-box-content">

					<div class="row">

						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

							<?php if (isset( $apiKey ) && !empty( $apiKey )) : ?>

							<p><?php _e('This is your Snapshot API key. If you have any issues connecting to PSOURCE’s cloud servers, just reset your key. Don’t worry, resetting your key won’t affect your backups.', SNAPSHOT_I18N_DOMAIN); ?></p>

							<?php else : ?>

								<div class="wps-snapshot-popin-content wps-snapshot-popin-content-step-1">
									<p><?php _e('To enable Managed Backups and your 10GB storage allowance on our PSOURCE cloud servers, you need to add your Snapshot key.', SNAPSHOT_I18N_DOMAIN); ?></p>

									<p><a target="_blank" href="<?php echo $apiKeyUrl ?>" class="button button-blue"><?php _e('Get My Key', SNAPSHOT_I18N_DOMAIN); ?></a></p>

									<p><?php _e('Once you\'ve got your key, enter it below:', SNAPSHOT_I18N_DOMAIN); ?></p>

								</div>

								<div class="wps-snapshot-popin-content wps-snapshot-popin-content-step-2 hidden">
									<p><?php _e('Please wait while we verify your Snapshot key...', SNAPSHOT_I18N_DOMAIN); ?></p>
								</div>

								<div class="wps-snapshot-popin-content wps-snapshot-popin-content-step-3 hidden">
									<div class="wps-snapshot-error wpmud-box-gray">
										<p><?php printf(__('We couldn’t verify your Snapshot key. Try entering it again, or reset it for this website in <a target="_blank" href="%s">The Hub</a> over at PSOURCE.', SNAPSHOT_I18N_DOMAIN ), 'https://premium.psource.org/hub/' );?></p>
									</div>
								</div>

								<div class="wps-snapshot-popin-content wps-snapshot-popin-content-step-4 hidden">
									<p><?php _e('This is your Snapshot API key. If you have any issues connecting to PSOURCE’s cloud servers, just reset your key. Don’t worry, resetting your key won’t affect your backups.', SNAPSHOT_I18N_DOMAIN); ?></p>
								</div>


							<?php endif; ?>

							<form method="post" action="?page=snapshot_settings" data-security="<?php echo $ajax_nonce;?>">

								<div class="wps-snapshot-key wpmud-box-gray">

									<input type="text" name="secret-key" id="secret-key" value="<?php echo ( isset( $apiKey ) && !empty( $apiKey ) ) ? $apiKey : '' ?>"  data-url="<?php echo ( isset( $apiKeyUrl ) && !empty( $apiKeyUrl ) ) ? $apiKeyUrl : '' ?>" placeholder="<?php _e('Enter your key here', SNAPSHOT_I18N_DOMAIN); ?>">

									<?php if ( !isset( $apiKey ) || empty( $apiKey )) : ?>

									<button type="submit" name="activate" value="yes" class="button button-gray"><?php _e('Save Key', SNAPSHOT_I18N_DOMAIN); ?></button>

								<?php endif; ?>

									<?php 	$model = new Snapshot_Model_Full_Backup; ?>
									<a href="<?php echo esc_attr( $model->get_current_secret_key_link() );?>" target='_blank' class="button button-gray wps-snapshot-popin-content-step-4 <?php echo ( isset( $apiKey ) && !empty( $apiKey ) ) ? '' : 'hidden' ?>"><?php _e('Reset Key', SNAPSHOT_I18N_DOMAIN);?></a>



								</div>

							</form>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>