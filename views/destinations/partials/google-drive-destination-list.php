<?php if ( empty( $destinations ) ) : ?>

	<div class="wps-notice">
		<p><?php _e("You haven't added a Google Drive destination yet.", 'cp-snapshot'); ?></p>
	</div>

<?php else : ?>

	<?php $required_fields = array( 'name', 'directory', 'clientid', 'clientsecret', 'redirecturi', 'access_token' ); ?>

	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="wps-destination-name"><?php _e('Name', 'cp-snapshot'); ?></th>
				<th class="wps-destination-client"><?php _e('Client ID', 'cp-snapshot'); ?></th>
				<th class="wps-destination-dir"><?php _e('Directory', 'cp-snapshot'); ?></th>
				<th class="wps-destination-shots"><?php _e('Snapshots', 'cp-snapshot'); ?></th>
				<th class="wps-destination-config"></th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ( $destinations as $id => $destination ) : ?>

				<tr>
					<td class="wps-destination-name">
						<a href="<?php echo esc_html( add_query_arg(
								array(
									'snapshot-action' => 'edit',
									'type' => urlencode( $destination['type'] ),
									'item' => urlencode( $id ),
								),
								PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-destinations' )
						) ); ?>">
							<?php echo esc_html( $destination['name'] ); ?></a>

						<?php if ( ! Snapshot_Model_Destination::has_required_fields( $destination, $required_fields ) ) : ?>
							<span class="incomplete-warning" title="<?php esc_html_e( 'This destination has not been fully configured.', 'cp-snapshot' ); ?>"></span>
						<?php endif; ?>

					</td>

					<td class="wps-destination-client" data-text="Client:"><?php echo isset( $destination['clientid'] ) ? esc_html( $destination['clientid'] ) : ''; ?></td>

					<td class="wps-destination-dir" data-text="Dir:"><?php echo isset( $destination['directory'] ) ? esc_html( $destination['directory'] ) : ''; ?></td>

					<td class="wps-destination-shots"><?php Snapshot_Model_Destination::show_destination_item_count( $id ); ?></td>

					<td class="wps-destination-config">

						<a class="button button-small button-outline button-gray" href="<?php
						echo esc_url( add_query_arg(
							array(
								'snapshot-action' => 'edit',
								'type' => urlencode( $destination['type'] ),
								'item' => urlencode( $id )
							),
							PSOURCESnapshot::instance()->snapshot_get_pagehook_url( 'snapshots-newui-destinations' )
						) ); ?>">
							<span class="dashicons dashicons-admin-generic"></span>
							<span class="wps-destination-config-text"><?php _e('Configure', 'cp-snapshot'); ?></span>
						</a>
					</td>
				</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>