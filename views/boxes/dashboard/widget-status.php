<?php

$backups = PSOURCESnapshot::instance()->config_data['items'];

$backup_status = array(
	'title' => __( 'No Backups', 'cp-snapshot' ),
	'content' => __( "You haven't backed up your site yet. Create your first backup now<br>– it'll only take a minute.", 'cp-snapshot' ),
	'date' => __( 'Never', 'cp-snapshot' ),
	'size' => __( '-', 'cp-snapshot' ),
);

$model = new Snapshot_Model_Full_Backup();

$is_dashboard_active = $model->is_dashboard_active();
$is_dashboard_installed = $is_dashboard_active && $model->is_dashboard_installed();
$has_dashboard_key = $model->has_dashboard_key();

$is_client = $is_dashboard_active && $has_dashboard_key;

$apiKey = $model->get_config( 'secret-key', '' );

$has_snapshot_key = $is_client && Snapshot_Model_Full_Remote_Api::get()->get_token() != false && ! empty( $apiKey );

if ( ! empty( $latest_backup ) && $latest_backup ) {

	$one_week_ago = strtotime( '-1 week' );
	if ( $latest_backup['timestamp'] > $one_week_ago ) {
		$backup_status['title'] = __( 'All Backed up', 'cp-snapshot' );
		$backup_status['content'] = __( 'Your last backup was created less than a week ago. Excellent work!', 'cp-snapshot' );
	} else {
		$backup_status['title'] = __( 'Getting Older', 'cp-snapshot' );
		$backup_status['content'] = __( 'Your last backup was over a week ago. Make sure you\'re backing up regulary!', 'cp-snapshot' );
	}
	$backup_status['date'] = sprintf( _x( '%s ago', '%s = human-readable time difference', 'cp-snapshot' ), human_time_diff( $latest_backup['timestamp'] ) );
	$backup_status['size'] = size_format( $latest_backup['file_size'] );
}

$snapshot = PSOURCESnapshot::instance()->config_data['items'];
$latest_snapshot = Snapshot_Helper_Utility::latest_backup( $snapshot );

?>

<section class="wps-backups-status<?php if ( ! $is_client ) : echo ' wps-backups-status-free'; endif; ?> wpmud-box">

	<div class="wpmud-box-content">
		<div class="wps-backups-summary">

			<div class="wps-backups-summary-align">

				<h3><?php printf( __( 'Hello, %s!', 'cp-snapshot' ), wp_get_current_user()->display_name ); ?></h3>

				<?php if ( $is_client ) : ?>
					<p><?php _e( 'Welcome to the Dashboard. Here you can manage all your snapshots and backups.', 'cp-snapshot' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'Welcome to the Dashboard. Here you can manage all your snapshots.', 'cp-snapshot' ); ?></p>
				<?php endif; ?>

			</div>
		</div>

		<div class="wps-backups-details">
			<table cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<th><?php _e( 'Last Snapshot', 'cp-snapshot' ); ?></th>

					<?php if ( isset( $latest_snapshot['timestamp'] ) ) : ?>
						<td class="fancy-date-time">
							<?php echo Snapshot_Helper_Utility::show_date_time( $latest_snapshot['timestamp'], 'F j, Y ' ) ?>
							<span><?php
								printf(
									esc_html__( 'at %s', 'cp-snapshot' ),
									Snapshot_Helper_Utility::show_date_time( $latest_snapshot['timestamp'], 'g:ia' )
								); ?></span>
						</td>
					<?php else: ?>
						<td><?php esc_html_e( 'Never', 'cp-snapshot' ); ?></span></td>
					<?php endif; ?>
				</tr>

				<tr>
					<th><?php _e( 'Available Destinations', 'cp-snapshot' ); ?></th>
					<td>
						<span class="wps-count"><?php echo count( PSOURCESnapshot::instance()->config_data['destinations'] ); ?></span>
					</td>
				</tr>

				<?php if ( $is_client ) : ?>
					<tr>
						<th><?php _e( 'Managed Backups Schedule', 'cp-snapshot' ); ?></th>

						<?php if ( ! $has_snapshot_key ) { ?>
							<td>
								<a id="view-snapshot-key" class="button button-small button-blue"><?php _e( 'Activate', 'cp-snapshot' ) ?></a>
							</td>
						<?php } elseif ( $model->get_config( 'disable_cron', false ) ) { ?>

							<td>
								<a id="wps-managed-backups-configure" class="button button-outline button-small button-gray"
								   href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-managed-backups' ) . '#wps-backups-settings-schedule' ); ?>">
									<?php esc_html_e( 'Enable', 'cp-snapshot' ); ?>
								</a>
							</td>

						<?php } else { ?>

							<td class="fancy-date-time">
								<?php $frequencies = $model->get_frequencies(); echo esc_html( $frequencies[$model->get_frequency()] ); ?>
								<span><?php
									$schedule_times = $model->get_schedule_times();
									printf(
										esc_html__( 'at %s', 'cp-snapshot' ),
										$schedule_times[$model->get_schedule_time()]
									);

									?></span>
							</td>

						<?php } ?>

					</tr>
				<?php endif; ?>

				</tbody>
			</table>

		</div>
	</div>

</section>