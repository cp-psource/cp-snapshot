<?php

$requirements_test = Snapshot_Helper_Utility::check_system_requirements();
$checks = $requirements_test['checks'];
$all_good = $requirements_test['all_good'];
$warning = $requirements_test['warning'];

?>

<section id="header">
	<h1><?php esc_html_e( 'Managed Backups', 'cp-snapshot' ); ?></h1>
</section>

<?php $this->render( "managed-backups/partials/restore-backup-progress", false, array( 'item' => $item ), false, false ); ?>

<form id="managed-backup-restore" method="post" action="">

	<input type="hidden" id="archive" name="archive" class="widefat archive" value="<?php echo sanitize_text_field( $_GET['item'] ) ?>" />
	<?php request_filesystem_credentials(home_url()); ?>

	<div id="container" class="snapshot-three wps-page-wizard">

		<section class="wpmud-box new-snapshot-main-box">

			<div class="wpmud-box-title has-button">

				<h3><?php _e( 'Restore Wizard', 'cp-snapshot' ); ?></h3>

				<a href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-managed-backups') ); ?>" class="button button-small button-gray button-outline"><?php _e( 'Back', 'cp-snapshot' ); ?></a>

			</div>

			<div class="wpmud-box-content">

				<?php $this->render( "common/requirements-test", false, $requirements_test, false, false ); ?>

				<div class="wpmud-box-tab configuration-box<?php if ( $all_good ) { echo ' open'; } ?>">

					<div class="wpmud-box-tab-title can-toggle">

						<h3>
							<?php _e( 'Configuration', 'cp-snapshot' ); ?>
							<?php if ( !$all_good ) { ?>
							<span class="wps-restore-backup-notice">
								<?php _e( 'You must meet the server requirements before proceeding.', 'cp-snapshot' ); ?>
							</span>
							<?php } ?>
							<?php if ( $all_good && $warning ) { ?>
							<span class="wps-restore-backup-notice">
								<?php _e( 'You have 1 or more requirements warnings. You can proceed, however Snapshot may run into issues due to the warnings.', 'cp-snapshot' ); ?>
							</span>
							<?php } ?>
						</h3>

						<?php if ( $all_good ): ?>
						<i class="wps-icon i-arrow-right"></i>
						<?php endif; ?>
					</div>

					<?php if ( $all_good ): ?>

					<div class="wpmud-box-tab-content">

						<div id="wps-new-location" class="row">

							<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

								<label class="label-box"><?php _e( 'Location', 'cp-snapshot' ); ?></label>

							</div>

							<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">

								<div class="wpmud-box-mask">

									<label class="label-title"><?php _e( 'Choose which folder you would like to restore your website to.', 'cp-snapshot' ); ?></label>

									<div class="wps-restore-folder">

										<div class="wps-restore-folder-label">
											<label><?php _e( 'Restore to', 'cp-snapshot' ); ?></label>
										</div>

										<div class="wps-restore-folder-input">

										<input type="text" id="location" placeholder="<?php echo apply_filters('snapshot_home_path', get_home_path()); ?>" name="location" class="widefat location" value="<?php echo apply_filters('snapshot_home_path', get_home_path()); ?>" />

										</div>

									</div>

									<p><small><?php _e( 'You need to enter the full path to the directory you want to restore your website to. Note: this will be the new root directory for your site.', 'cp-snapshot' ); ?></small></p>

								</div>

							</div>

						</div>

						<div class="row">

							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="form-button-container">
									<a href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-managed-backups' ) ); ?>" class="button button-outline button-gray"><?php _e( 'Cancel', 'cp-snapshot' ); ?></a>
									<button type="submit" class="button button-blue"><?php _e( 'Restore Now', 'cp-snapshot' ); ?></button>
								</div>
							</div>

						</div>

					</div>

					<?php endif; ?>

				</div>

			</div>
		</section>
	</div>
</form>