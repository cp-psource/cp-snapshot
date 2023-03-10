<?php

if ( isset( $item['data'] ) ) {
	$item['data_item'] = Snapshot_Helper_Utility::latest_data_item( $item['data'] );
}

$uploaded = false;

if ( empty( $item['destination'] ) || 'local' == $item['destination'] ) {
	$uploaded = null;
}

if ( ! empty( $item['data_item']['destination-status'] ) ) {
	$destination_status = Snapshot_Helper_Utility::latest_data_item( $item['data_item']['destination-status'] );
	$uploaded = isset( $destination_status['sendFileStatus'] ) && $destination_status['sendFileStatus'];
}

?>

<section id="header">
	<h1><?php esc_html_e( 'Snapshots', 'cp-snapshot' ); ?></h1>
</section>

<div id="container" class="snapshot-three wps-page-snapshots">

	<section class="wpmud-box snapshot-info-box">

		<div class="wpmud-box-title has-button">

			<h3 class="has-button">
				<?php _e( 'Snapshot Info', 'cp-snapshot' ); ?>
				<a href="<?php echo esc_url( PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ) ); ?>" class="button button-outline button-small button-gray">
					<?php _e( 'Back', 'cp-snapshot' ); ?>
				</a>
			</h3>

			<div class="wps-menu">

				<div class="wps-menu-dots">

					<div class="wps-menu-dot"></div>

					<div class="wps-menu-dot"></div>

					<div class="wps-menu-dot"></div>

				</div>

				<div class="wps-menu-holder">

					<ul class="wps-menu-list">

						<li class="wps-menu-list-title"><?php _e( 'Options', 'cp-snapshot' ); ?></li>
						<li>
							<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ); ?>&amp;snapshot-action=edit&amp;item=<?php echo $item['timestamp']; ?>"><?php _e( 'Edit', 'cp-snapshot' ); ?></a>
						</li>
						<li>
							<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ); ?>&amp;snapshot-action=backup&amp;item=<?php echo $item['timestamp']; ?>"><?php _e( 'Regenerate', 'cp-snapshot' ); ?></a>
						</li>
						<?php if ( isset( $item['data_item']['timestamp'] ) && ! empty( $item['data_item']['timestamp'] ) ): ?>
							<li>
								<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ); ?>&snapshot-action=restore&item=<?php echo $item['timestamp']; ?>&snapshot-data-item=<?php echo $item['data_item']['timestamp']; ?>"><?php _e( 'Restore', 'cp-snapshot' ); ?></a>
							</li>
						<?php endif; ?>
						<li>
							<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ); ?>&amp;snapshot-action=delete-item&amp;item=<?php echo $item['timestamp']; ?>&amp;snapshot-noonce-field=<?php echo wp_create_nonce( 'snapshot-delete-item' ); ?>"><?php _e( 'Delete', 'cp-snapshot' ); ?></a>
						</li>

					</ul>

				</div>

			</div>

		</div>

		<div class="wpmud-box-content">

			<div class="row">

				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					<table class="has-footer" cellpadding="0" cellspacing="0">

						<tbody>

						<tr>
							<th><?php _e( 'Name', 'cp-snapshot' ); ?></th>
							<td>
								<p><?php echo esc_html( $item['name'] ); ?></p>
							</td>
						</tr>

						<?php if ( isset( $item['data_item']['filename'] ) ) {?>
						<tr>
							<th><?php _e( 'Filename', 'cp-snapshot' ); ?></th>
							<td>
								<p>
									<?php if ( isset( $item['data_item']['timestamp'] ) ) {

										printf( '<a href="%s" title="%s">%s</a>',
											esc_url( add_query_arg( array(
												'snapshot-action' => 'download-archive',
												'snapshot-item' => $item['timestamp'],
												'snapshot-data-item' => $item['data_item']['timestamp'],
											) ) ),
											esc_attr__( 'Download the snapshot archive', 'cp-snapshot' ),
											esc_html( $item['data_item']['filename'] )
										);
									} else {
										echo esc_html( $item['data_item']['filename'] );
									} ?>
								</p>
							</td>
						</tr>
						<?php } ?>

						<tr>
							<th><?php _e( 'Last run', 'cp-snapshot' ); ?></th>
							<td>
								<p>
									<?php
									if ( isset( $item['data_item']['timestamp'] ) ) {
										$date_time_format = get_option( 'date_format' ) . _x( ' @ ', 'date and time separator', 'cp-snapshot' ) . get_option( 'time_format' );
										echo Snapshot_Helper_Utility::show_date_time( $item['data_item']['timestamp'], $date_time_format );
									} else {
										echo "-";
									}
									?>
								</p>
							</td>
						</tr>

						<?php if ( ! is_null( $uploaded ) ) { ?>
						<tr>
							<th><?php _e( 'Status', 'cp-snapshot' ); ?></th>

							<td>
								<?php

								if ( isset( $destination_status ) && $destination_status['errorStatus'] ) {

									if ( $destination_status['errorArray'] ) {

										echo '<p>', __( 'An error occurred during the most recent upload attempt:', 'cp-snapshot' ), '</p>';

										foreach ( $destination_status['errorArray'] as $error_message ) {
											echo '<p class="wps-auth-message error">', esc_html( $error_message ), '</p>';
										}

										echo '<p>', __( 'Further attempts to upload will continue to be made. However, you may want to investigate this issue to ensure that they are successful.', 'cp-snapshot' ), '</p>';

									} else {
										esc_html_e( 'An unknown error occurred during the last upload attempt. Further attempts to upload will continue to be made.', 'cp-snapshot' );
									}

								} else {

									echo $uploaded ?
										'<p>' . __( 'Uploaded', 'cp-snapshot' ) . '</p>' :
										'<p class="wps-spinner">' . __( 'Uploading&hellip;', 'cp-snapshot' ) . '</p>';

								}

								?>
							</td>
						</tr>
						<?php } ?>

						<tr>
							<th><?php _e( 'Destination', 'cp-snapshot' ); ?></th>
							<td>
								<?php $destination = PSOURCESnapshot::instance()->config_data['destinations'][ $item['destination'] ]; ?>
								<p class="has-typecon">
									<span class="wps-typecon <?php echo $destination['type'] ?>"></span> <?php echo $destination['name'] ?>
								</p>
							</td>
						</tr>

						<tr>
							<th><?php _e( 'Frequency', 'cp-snapshot' ); ?></th>
							<td>
								<p>
									<?php
									$interval_text = Snapshot_Helper_Utility::get_sched_display( $item['interval'] );

									if ( $interval_text ) {
										$running_timestamp = wp_next_scheduled( 'snapshot_backup_cron', array( intval( $item['timestamp'] ) ) );
										echo $interval_text, _x( ' @ ', 'interval and time separator', 'cp-snapshot' );
										echo Snapshot_Helper_Utility::show_date_time( $running_timestamp, get_option( 'time_format' ) );
									} else {
										_e( 'Once off', 'cp-snapshot' );
									}
									?>
								</p>

							</td>
						</tr>

						<tr>
							<th><?php _e( 'Filesize', 'cp-snapshot' ); ?></th>
							<td>
								<p><?php
									if ( isset( $item['data_item']['file_size'] ) ) {
										$file_size = Snapshot_Helper_Utility::size_format( $item['data_item']['file_size'] );
										echo $file_size;
									} else {
										echo "-";
									} ?>
								</p>
							</td>
						</tr>

						<tr>
							<th><?php _e( 'Files', 'cp-snapshot' ); ?></th>
							<td>
								<p><?php if ( isset( $item['files-option'] ) ) {
										if ( $item['files-option'] == 'none' ) {
											_e( 'None', 'cp-snapshot' );
										} else if ( $item['files-option'] == 'all' ) {
											_e( 'All Files', 'cp-snapshot' );
										} else {
											if ( isset( $item['files-sections'] ) ) {
												echo ucwords( implode( ', ', $item['files-sections'] ) );
											}
										}
									} else {
										echo "-";
									}
									?>
								</p>
							</td>
						</tr>

						<tr>
							<th><?php _e( 'URL exclusions', 'cp-snapshot' ); ?></th>
							<td>
								<p>
									<?php
									if ( isset( $item['files-ignore'] ) && count( $item['files-ignore'] ) ) {
										echo implode( '<br>', $item['files-ignore'] );
									} else {
										echo '-';
									}
									?>
								</p>
							</td>
						</tr>

						<tr>
							<th><?php _e( 'Database Tables', 'cp-snapshot' ); ?></th>
							<td>
								<p>
									<?php
									if ( isset( $item['tables-option'] ) ) {
										if ( $item['tables-option'] == 'none' ) {
											_e( 'None', 'cp-snapshot' );
										} else if ( $item['tables-option'] == 'all' ) {
											_e( 'All', 'cp-snapshot' );
										} else {
											if ( isset( $item['tables-sections'] ) ) {
												foreach ( $item['tables-sections'] as $section_key => $section_tables ) {

													if ( ! empty( $section_tables ) ) {
														if ( $section_key == "wp" ) {
															_e( 'core', 'cp-snapshot' );
														} else if ( $section_key == "non" ) {
															_e( 'non-core', 'cp-snapshot' );
														} else if ( $section_key == "other" ) {
															_e( 'other', 'cp-snapshot' );
														} else if ( $section_key == "error" ) {
															_e( 'error', 'cp-snapshot' );
														} else if ( $section_key == "global" ) {
															_e( 'global', 'cp-snapshot' );
														}
														echo ': ';
														echo implode( ', ', $section_tables );
														echo '<br/>';

													}

												}
											}
										}
									} else {
										echo '-';
									}
									?>
								</p>
							</td>
						</tr>

						<?php if ( isset( $item['data_item']['timestamp'] ) && ! empty( $item['data_item']['timestamp'] ) ): ?>
							<tr>
								<th><?php _e( 'Log', 'cp-snapshot' ); ?></th>
								<td>

									<a id="wps-snapshot-log-view" class="button button-small button-outline button-gray" href="#"><?php _e( 'view', 'cp-snapshot' ) ?></a>
									<a class="button button-small button-outline button-gray" href="<?php echo '?page=snapshot_pro_snapshots&amp;snapshot-action=download-log&amp;snapshot-item=' . $item['timestamp'] . '&amp;snapshot-data-item=' . $item['data_item']['timestamp'] . '&amp;live=0' ?>"><?php _e( 'download', 'cp-snapshot' ) ?>
									</a>

								</td>
							</tr>
						<?php endif; ?>

						</tbody>

						<tfoot>

						<tr>
							<td>

								<a href="<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' ); ?>&amp;snapshot-action=delete-item&amp;item=<?php echo $item['timestamp']; ?>&amp;snapshot-noonce-field=<?php echo wp_create_nonce( 'snapshot-delete-item' ); ?>" class="button button-outline button-gray"><?php _e( 'Delete', 'cp-snapshot' ); ?></a>

							</td>
							<td>

								<?php if ( isset( $item['data_item']['timestamp'] ) && ! empty( $item['data_item']['timestamp'] ) ): ?>
									<a class="button button-blue" href="<?php
										echo esc_url( add_query_arg(
											array(
												'snapshot-action' => 'restore',
												'item' => $item['timestamp'],
												'snapshot-data-item' => $item['data_item']['timestamp'],
											), PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-snapshots' )
										) ); ?>">
										<?php _e( 'Restore', 'cp-snapshot' ); ?>
									</a>
								<?php endif; ?>

							</td>
						</tr>

						</tfoot>

					</table>
					<?php if ( isset( $item['data_item']['timestamp'] ) && ! empty( $item['data_item']['timestamp'] ) ): ?>
						<?php
						$modal_data = array(
							'modal_id' => "wps-snapshot-log",
							'modal_title' => __( 'View Logs', 'cp-snapshot' ),
							'modal_content' => __( "<p>Here's a log of events for this snapshot.</p>", 'cp-snapshot' ),
							'modal_content_ajax' => admin_url() . 'admin-ajax.php?action=snapshot_view_log_ajax&amp;snapshot-item=' . $item['timestamp'] . '&amp;snapshot-data-item=' . $item['data_item']['timestamp'],
							'modal_action_title' => __( 'Download', 'cp-snapshot' ),
							'modal_action_url' => '?page=snapshot_pro_snapshots&amp;snapshot-action=download-log&amp;snapshot-item=' . $item['timestamp'] . '&amp;snapshot-data-item=' . $item['data_item']['timestamp'] . '&amp;live=0',
							'modal_cancel_title' => __( 'Cancel', 'cp-snapshot' ),
							'modal_cancel_url' => '#',
						);
						$this->render( "boxes/modals/popup-dynamic", false, $modal_data, false, false );
						?>
					<?php endif; ?>

				</div>

			</div>

		</div>

	</section>

</div>