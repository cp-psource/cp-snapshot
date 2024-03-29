<?php
$backup_folder = PSOURCESnapshot::instance()->config_data['config']['backupFolder'];
$backup_folder = isset($backup_folder) ? $backup_folder : 'snapshots';
$use_folder = isset(PSOURCESnapshot::instance()->config_data['config']['backupUseFolder']) ? PSOURCESnapshot::instance()->config_data['config']['backupUseFolder'] :
	(($backup_folder !== 'snapshots') ? 2 : 1);
$custom_directory = $use_folder;
?>

<section id="header">
	<h1><?php esc_html_e( 'Settings', SNAPSHOT_I18N_DOMAIN ); ?></h1>
</section>

<div id="container" class="snapshot-three wps-page-settings">

	<section class="wpmud-box">

		<div class="wpmud-box-title">

			<h3><?php _e('General', SNAPSHOT_I18N_DOMAIN);?> </h3>

		</div>

		<div class="wpmud-box-content">

			<form action="?page=snapshot_settings" method="post">

				<input type="hidden" name="snapshot-action" value="settings-update"/>

				<input type="hidden" name="snapshot-sub-action" value="backupFolder"/>

				<?php wp_nonce_field( 'snapshot-settings', 'snapshot-noonce-field' ); ?>

				<div id="wps-settings-localdir" class="row">

					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

						<label class="label-box"><?php _e('Local Directory', SNAPSHOT_I18N_DOMAIN); ?></label>

					</div>

					<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">

						<div class="wpmud-box-mask">

							<label class="label-title"><?php _e('Choose where your snapshots will be stored whilst they are being uploaded to your third party integrations.', SNAPSHOT_I18N_DOMAIN); ?></label>

							<div id="wps-localdir-options" class="wps-input--group">

								<div id="wps-dir-default" class="wps-input--item current">

									<div class="wps-input--radio">

										<input type="radio" <?php checked( $custom_directory, 1 ); ?> name="files" id="no_files" class="" value="1" />

										<label for="no_files"></label>

									</div>

									<label for="no_files"><?php _e('Use default directory', SNAPSHOT_I18N_DOMAIN);?></label>

								</div>

								<div id="wps-dir-custom" class="wps-input--item">

									<div class="wps-input--radio">

										<input type="radio" name="files" id="common_files" class="" value="2" <?php checked( $custom_directory, 2 ); ?> />

										<label for="common_files"></label>

									</div>

									<label for="common_files"><?php _e('Use custom directory', SNAPSHOT_I18N_DOMAIN);?></label>

								</div>

							</div>

							<div class="wpmud-box-gray hidden">

								<input type="text" name="backupFolder" id="snapshot-settings-backupFolder" value="<?php echo $backup_folder; ?>" placeholder="<?php _e('Enter directory URL here', SNAPSHOT_I18N_DOMAIN);?>" />

								<p><small><?php printf(__('Your current snapshot directory lives at: <a href="#">%s</a>. If you choose a custom directory, Snapshot will automatically transfer any archives to the new directory for you.', SNAPSHOT_I18N_DOMAIN),trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupBaseFolderFull' ) ));?></small></p>

							</div>

						</div>

					</div>

				</div><!-- #wps-settings--localdir -->

				<div id="wps-settings--exclusions" class="row">

					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

						<label class="label-box"><?php _e('Global File Exclusions', SNAPSHOT_I18N_DOMAIN); ?></label>

					</div>

					<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">

						<div class="wpmud-box-mask">

							<label class="label-title"><?php _e('Define specific files or folders you want to exclude from any Snapshot or Full Backup.', SNAPSHOT_I18N_DOMAIN); ?></label>

							<textarea name="filesIgnore" id="filesIgnore" placeholder="<?php _e('Enter file URLs to be excluded, one per line.', SNAPSHOT_I18N_DOMAIN);?>"><?php if ( ( isset( PSOURCESnapshot::instance()->config_data['config']['filesIgnore'] ) ) && ( is_array( PSOURCESnapshot::instance()->config_data['config']['filesIgnore'] ) ) && ( count( PSOURCESnapshot::instance()->config_data['config']['filesIgnore'] ) ) ) { echo implode( "\n", PSOURCESnapshot::instance()->config_data['config']['filesIgnore'] ); } ?></textarea>

							<p><small><?php _e('The exclude feature uses pattern matching so you can easily select files to exclude from your backups. Example: to exclude the Twenty Ten theme, you can use twentyten, theme/twentyten or public/wp-content/theme/twentyten. <strong>The local folder is excluded from Snapshot backups by default.</strong>', SNAPSHOT_I18N_DOMAIN);?></small></p>

						</div>

					</div>

				</div><!-- #wps-settings--exclusions -->

				<?php $error_reporting_errors = array(
					E_ERROR   => array(
						'label_log' => __( 'Errors', SNAPSHOT_I18N_DOMAIN ),
						'description' => __( 'Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation problem. Execution of the script is halted.', SNAPSHOT_I18N_DOMAIN ),
						'label_stop' => __( 'Stop the backup process if an error occurs', SNAPSHOT_I18N_DOMAIN )
					),
					E_WARNING => array(
						'label_log' => __( 'Warnings', SNAPSHOT_I18N_DOMAIN ),
						'description' => __( 'Run-time warnings (non-fatal errors). Executeion of the script is not halted.', SNAPSHOT_I18N_DOMAIN ),
						'label_stop' => __( 'Stop the backup process if a warning occurs', SNAPSHOT_I18N_DOMAIN )
					),
					E_NOTICE  => array(
						'label_log' => __( 'Notices', SNAPSHOT_I18N_DOMAIN ),
						'description' => __( 'Run-time notices. Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script.', SNAPSHOT_I18N_DOMAIN ),
						'label_stop' => __( 'Stop the backup process if a notice occurs', SNAPSHOT_I18N_DOMAIN )
					),
				); ?>

				<div id="wps-settings--error" class="row">

					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

						<label class="label-box"><?php _e('Error Reporting', SNAPSHOT_I18N_DOMAIN); ?></label>

					</div>

					<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">

						<div class="wpmud-box-mask">

							<label class="label-title"><?php _e('Choose how you want Snapshot to handle error conditions during the backup and restore process.', SNAPSHOT_I18N_DOMAIN); ?></label>

							<?php foreach ( $error_reporting_errors as $error_key => $error_label ){

								$checked_log = $checked_stop = false;
								$error_class = 'hidden';

								if (isset( PSOURCESnapshot::instance()->config_data['config']['errorReporting'][ $error_key ]['log'])){
									$checked_log = true;
									$error_class = '';
								}

								$checked_stop = isset( PSOURCESnapshot::instance()->config_data['config']['errorReporting'][ $error_key ]['stop']); ?>

								<div class="wps-input--item wps-input--parent">

									<div class="wps-input--checkbox">

										<input type="checkbox" id="checkbox-<?php echo $error_key; ?>" class="input-error-log" name="errorReporting[<?php echo $error_key; ?>][log]" <?php checked( $checked_log, true ); ?>>

										<label for="checkbox-<?php echo $error_key; ?>"></label>

									</div>

									<label for="checkbox-<?php echo $error_key; ?>"><?php echo $error_label['label_log'];?></label>

									<p><small class="description"><?php echo $error_label['description'];?></small></p>

								</div>

								<div class="wpmud-box-gray">

									<div class="wps-input--item">

										<div class="wps-input--checkbox">

											<input type="checkbox" id="checkbox-<?php echo $error_key; ?>1" name="errorReporting[<?php echo $error_key; ?>][stop]" class="input-error-stop" <?php checked( $checked_stop, true ); ?>>

											<label for="checkbox-<?php echo $error_key; ?>1"></label>

										</div>

										<label for="checkbox-<?php echo $error_key; ?>1"><?php echo $error_label['label_stop'];?></label>

									</div>

								</div>

							<?php } ?>

						</div>

					</div>

				</div><!-- #wps-settings--error -->

				<div class="row">

					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

						<div class="form-button-container">

							<input class="button button-blue" type="submit" value="<?php _e('Save Changes', SNAPSHOT_I18N_DOMAIN);?>">

						</div>

					</div>

				</div>

			</form>

		</div>

	</section>

</div>