<?php

$plugin = PSOURCESnapshot::instance();

/* Don't display this notice if it has already been seen */
if ( isset( $plugin->config_data['seen_welcome'] ) && $plugin->config_data['seen_welcome'] ) {
	return;
}

$plugin->config_data['seen_welcome'] = true;
$plugin->save_config();

?>
<div id="wps-welcome-message" class="snapshot-three wps-popup-modal show">

	<div class="wps-popup-mask"></div>

	<div class="wps-popup-content">
		<div class="wpmud-box">
			<div class="wpmud-box-title has-button can-close">
				<h3><?php _e('Welcome to Snapshot', 'cp-snapshot'); ?></h3>
				<a href="#" class="button button-small button-outline button-gray wps-popup-close wps-dismiss-welcome">
					<?php _e('Skip', 'cp-snapshot'); ?>
				</a>
			</div>

			<div class="wpmud-box-content">
				<div class="row">

					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

						<?php if ( $is_client && ! $has_snapshot_key) : ?>

							<p><?php _e('Welcome to Snapshot Pro, the hottest backups plugin for ClassicPress! Let’s start by choosing what type of backup you’d like to make - there are two types…', 'cp-snapshot'); ?></p>

						<?php else : ?>

							<p><?php _e('Welcome to Snapshot, the hottest backups plugin for ClassicPress! With this plugin you can backup and migrate bits and pieces of your website to third party destinations like Dropbox, Google Drive, Amazon S3 & more.', 'cp-snapshot'); ?></p>

						<?php endif; ?>

						<?php if ( $is_client && ! $has_snapshot_key) : ?>

							<div class="wps-welcome-message-pro">
								<h3><?php _e('PSOURCE Managed Backups', 'cp-snapshot'); ?></h3>
								<p><small><?php _e('As part of your PSOURCE membership you get 10GB free cloud storage to back up and store your entire ClassicPress website - including ClassicPress itself. You can schedule these backups to run daily, monthly or weekly and should you ever need it you can restore an entire website in just a few clicks.', 'cp-snapshot'); ?></small></p>
								<p><a class="button button-blue button-small wps-dismiss-welcome"
									  href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-managed-backups') ); ?>">
										<?php _e( 'Activate Managed Backups', 'cp-snapshot' ); ?>
									</a>
								</p>
							</div>

							<div class="wps-welcome-message-pro">
								<h3><?php _e('Snapshots', 'cp-snapshot'); ?></h3>
								<p><small><?php _e('With Snapshots you can backup and migrate bits and pieces of your website. You can choose what files, plugins/themes and database tables to backup and then store them on third party destinations. To get started, let’s add your first destination.', 'cp-snapshot'); ?></small></p>
							</div>

						<?php endif; ?>

							<p><?php _e("<strong>Let’s start by adding a new destination</strong>; where would you like to store your first snapshot?", 'cp-snapshot'); ?></p>

						<table cellpadding="0" cellspacing="0">
							<tbody>
								<tr><?php // Dropbox ?>
									<td class="start-icon"><i class="wps-typecon dropbox"></i></td>
									<td class="start-name"><?php _e('Dropbox', 'cp-snapshot'); ?></td>
									<td class="start-btn">
										<a class="button button-blue button-small wps-dismiss-welcome"
										   href="<?php echo esc_url( add_query_arg( array( 'snapshot-action' => 'add' , 'type' => 'dropbox' ), PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-destinations') ) ); ?>">
											<?php _e('Add Destination', 'cp-snapshot'); ?>
										</a>
									</td>
								</tr>

								<tr><?php // Google Drive ?>
									<td class="start-icon"><i class="wps-typecon google"></i></td>
									<td class="start-name"><?php _e('Google', 'cp-snapshot'); ?></td>
									<td class="start-btn">
										<a class="button button-blue button-small wps-dismiss-welcome"
										   href="<?php echo esc_url( add_query_arg( array( 'snapshot-action' => 'add' , 'type' => 'google-drive' ), PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-destinations') ) ); ?>">
											<?php _e('Add Destination', 'cp-snapshot'); ?>
											</a>
									</td>
								</tr>

								<tr><?php // Amazon S3 ?>
									<td class="start-icon"><i class="wps-typecon aws"></i></td>
									<td class="start-name"><?php _e('Amazon S3', 'cp-snapshot'); ?></td>
									<td class="start-btn">
										<a class="button button-blue button-small wps-dismiss-welcome"
										   href="<?php echo esc_url( add_query_arg( array( 'snapshot-action' => 'add' , 'type' => 'aws' ), PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-destinations') ) ); ?>">
											<?php _e('Add Destination', 'cp-snapshot'); ?>
										</a>
									</td>
								</tr>

								<tr><?php // sFTP ?>
									<td class="start-icon"><i class="wps-typecon sftp"></i></td>
									<td class="start-name"><?php _e('FTP / sFTP', 'cp-snapshot'); ?></td>
									<td class="start-btn">
										<a class="button button-blue button-small wps-dismiss-welcome"
										   href="<?php echo esc_url( add_query_arg( array( 'snapshot-action' => 'add' , 'type' => 'ftp' ), PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-destinations') ) ); ?>">
											<?php _e('Add Destination', 'cp-snapshot'); ?>
										</a>
									</td>
								</tr>

								<tr><?php // Local ?>
									<td class="start-icon"><i class="wps-typecon local"></i></td>
									<td class="start-name"><?php _e('Local', 'cp-snapshot'); ?></td>
									<td class="start-btn">
										<a class="button button-gray button-small button-outline wps-dismiss-welcome"
										   href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-new-snapshot') ); ?>" >
											<?php _e('Use Destination', 'cp-snapshot'); ?></a>
									</td>
								</tr>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>