<div id="container" class="hidden snapshot-three wps-page-builder">

	<section class="wpmud-box">

		<div class="wpmud-box-title has-button">

			<div class="wps-title-progress">
				<h3><?php _e('Create Backup', SNAPSHOT_I18N_DOMAIN); ?></h3>
			</div>

		</div>

		<div class="wpmud-box-content">
			<div class="row">

				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

						<div id="wps-build-error" class="hidden">
							<div class="wps-auth-message error"><p></p></div>

							<p>
								<a id="wps-build-error-back" class="button button-outline button-gray"><?php _e('Back', SNAPSHOT_I18N_DOMAIN); ?></a>
								<a href="#" id="wps-build-error-again" class="button button-gray"><?php _e('Try Again', SNAPSHOT_I18N_DOMAIN); ?></a>
							</p>
						</div>

						<div id="wps-build-progress">

							<p><?php _e('Your backup is in progress. <strong>You need to keep this page open for the backup to complete.</strong> Once your website has been backed up, it will be uploaded to PSOURCE servers. If your site is small, this will only take a few minutes, but could take a couple of hours for larger sites.', SNAPSHOT_I18N_DOMAIN); ?></p>

							<div class="wpmud-box-gray">

								<div class="wps-loading-status wps-total-status wps-spinner">
									<p class="wps-loading-number">0%</p>

									<div class="wps-loading-bar">
										<div class="wps-loader">
											<span style="width: 0;"></span>
										</div>
									</div>
								</div>

							</div>

							<p>
								<a id="wps-build-progress-cancel" class="button button-outline button-gray">
									<?php esc_html_e('Cancel', SNAPSHOT_I18N_DOMAIN); ?>
								</a>
							</p>

						</div>

						<div id="wps-build-success" class="hidden">

							<div class="wps-auth-message success">

								<p><?php _e('Your backup has been successfully created and uploaded to PSOURCE servers!.', SNAPSHOT_I18N_DOMAIN); ?></p>

							</div>

							<div class="wpmud-box-gray">

								<div class="wps-loading-status">
									<p class="wps-loading-number">100%</p>

									<div class="wps-loading-bar">
										<div class="wps-loader done">
											<span style="width: 100%"></span>
										</div>
									</div>
								</div>

							</div>


							<p>
								<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-managed-backups'); ?>" class="button button-gray"><?php _e('View Backups', SNAPSHOT_I18N_DOMAIN); ?></a>
							</p>

						</div>

				</div>

			</div>

		</div>

	</section>

</div>