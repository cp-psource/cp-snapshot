<?php

if ( ! class_exists( 'Snapshot_View_Settings' ) ) {

	class Snapshot_View_Settings {

		/**
		 * Panel showing form for adding new Snapshots.
		 *
		 * @since 1.0.2
		 * @uses setup in $this->admin_menu_proc()
		 * @uses $wpdb
		 *
		 * @param none
		 *
		 * @return none
		 */

		function snapshot_admin_show_add_panel() {

			global $wpdb;

//			require( PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_PLUGIN_BASE_DIR' ) . '/lib/snapshot_admin_metaboxes.php' );
			$_snapshot_metaboxes = new Snapshot_View_Metabox_Admin();

			$time_key = time();
			while ( true ) {
				if ( ! isset( PSOURCESnapshot::instance()->config_data['items'][ $time_key ] ) ) {
					break;
				}

				$time_key = time();
			}
			?>
			<div id="snapshot-new-panel" class="wrap snapshot-wrap">
				<h2><?php _ex( "Add New Snapshot", "Snapshot New Page Title", 'cp-snapshot' ); ?></h2>

				<p><?php _ex( "Use this form to create a new snapshot of your site. Fill in the optional Name and Notes fields. Select the tables to be included in this snapshot.", 'Snapshot page description', 'cp-snapshot' ); ?></p>

				<?php
				if ( ! Snapshot_Helper_Utility::check_server_timeout() ) {
					$current_timeout = ini_get( 'max_execution_time' );
					?>
					<div class='error snapshot-error'>
					<p><?php printf( __( 'Your web server timeout is set very low, %d seconds. Also, it appears this timeout cannot be adjusted via the Snapshot backup process. Attempting a snapshot backup could result in a partial backup of your tables.',
							SNAPSHOT_I18N_DOMAIN ), $current_timeout ); ?></p></div><?php
				}
				?>
				<div id="snapshot-timout-update-panel"></div>

				<?php Snapshot_Helper_UI::form_ajax_panels(); ?>

				<div id="poststuff" class="metabox-holder">
					<form id="snapshot-add-update" method="post"
					      action="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_new_panel">
						<input type="hidden" id="snapshot-action" name="snapshot-action" value="add"/>
						<input type="hidden" id="snapshot-item" name="snapshot-item" value="<?php echo $time_key; ?>"/>
						<input type="hidden" id="snapshot-data-item" name="snapshot-data-item"
						       value="<?php echo $time_key; ?>"/>

						<?php wp_nonce_field( 'snapshot-add', 'snapshot-noonce-field' ); ?>

						<?php $_snapshot_metaboxes->snapshot_metaboxes_show_item_header_information(
							__( 'Snapshot Information', 'cp-snapshot' ), null ); ?>
						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_backup_files_options(
							__( 'What Files to Archive?', 'cp-snapshot' ), null );
						?>
						<?php $_snapshot_metaboxes->snapshot_metabox_show_backup_tables_options(
							__( 'What Tables to Archive', 'cp-snapshot' ), null );
						?>
						<?php $_snapshot_metaboxes->snapshot_metabox_show_schedule_options(
							__( 'When to Archive', 'cp-snapshot' ), null );
						?>
						<?php $_snapshot_metaboxes->snapshot_metabox_show_destination_options(
							__( 'Where to save the Archive ', 'cp-snapshot' ), null );
						?>

						<input id="snapshot-add-button" class="button-primary" type="submit"
						       value="<?php _e( 'Create Snapshot', 'cp-snapshot' ); ?>"/>
					</form>
				</div>
			</div>
		<?php
		}

		/**
		 * Panel showing the table listing of all Snapshots.
		 *
		 * @since 1.0.0
		 * @uses setup in $this->admin_menu_proc()
		 * @uses $this->config_data['items'] to build output
		 *
		 * @param none
		 *
		 * @return none
		 */
		function snapshot_admin_show_items_panel() {
			// If the user has clicked the link to edit a snapshot item show the edit form...
			if ( isset( $_REQUEST['item'] ) ) {
				$item_key = intval( $_REQUEST['item'] );
			}

			if ( isset( $item_key ) ) {
				$item = PSOURCESnapshot::instance()->snapshot_get_edit_item( $item_key );
				//echo "item<pre>"; print_r($item); echo "</pre>";
				//echo "_REQUEST<pre>"; print_r($_REQUEST); echo "</pre>";

				if ( ( $item ) && ( isset( $_REQUEST['snapshot-action'] ) ) && ( sanitize_text_field( $_REQUEST['snapshot-action'] ) == 'edit' ) ) {
					$this->snapshot_admin_show_edit_panel( $item );
				} else if ( ( $item ) && ( isset( $_REQUEST['snapshot-action'] ) ) && ( sanitize_text_field( $_REQUEST['snapshot-action'] ) == 'restore-panel' ) ) {
					// ...or if the user clicked the button to show the restore form. Show it.
					$this->snapshot_admin_show_restore_panel( $item );
				} else if ( ( $item ) && ( isset( $_REQUEST['snapshot-action'] ) ) && ( sanitize_text_field( $_REQUEST['snapshot-action'] ) == 'item-archives' ) ) {
					$this->snapshot_admin_show_item_archive_panel( $item );
				} else {
					$this->snapshot_admin_show_listing();
				}
			} else {
				$this->snapshot_admin_show_listing();
			}
		}

		/**
		 * Panel showing the table listing of all Snapshots.
		 *
		 * @since 1.0.0
		 * @uses setup in $this->admin_menu_proc()
		 * @uses $this->config_data['items'] to build output
		 *
		 * @param none
		 *
		 * @return none
		 */

		function snapshot_admin_show_listing() {
			$config_data = PSOURCESnapshot::instance()->config_data;
			PSOURCESnapshot::instance()->items_table->prepare_items( PSOURCESnapshot::instance()->config_data['items'] );
			?>
			<div id="snapshot-edit-listing-panel" class="wrap snapshot-wrap">
				<h2><?php _ex( "All Snapshots", "Snapshot New Page Title", 'cp-snapshot' ); ?> <?php if ( current_user_can( 'manage_snapshots_items' ) ) {
						?><a class="add-new-h2"
						     href="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_new_panel">
							Add New</a><?php
					} ?></h2>

				<p><?php _ex( "This is a listing of all Snapshots created within your site. To delete a snapshot set the checkbox then click the 'Delete Snapshots' button below the listing. To restore a snapshot click the 'Restore' button for that snapshot. To edit a snapshot click the name of the snapshot", 'Snapshot page description', 'cp-snapshot' ); ?></p>

				<div style="float: right" class="snapshot-system-time">
					<?php echo __( 'Current time:', 'cp-snapshot' ) . ' <strong>' . Snapshot_Helper_Utility::show_date_time( time() ) . '</strong><br />'; ?>
					<?php
					$timestamp = wp_next_scheduled( PSOURCESnapshot::instance()->get_setting( 'remote_file_cron_hook' ) );
					if ( $timestamp ) {
						echo __( 'Next File Send:', 'cp-snapshot' ) . ' <strong>' . Snapshot_Helper_Utility::show_date_time( $timestamp ) . '</strong>';
					}
					?><br/>
					<?php _e( '<span class="snapshot-error">(I)</span> indicates an imported archive', 'cp-snapshot' ); ?>
				</div>

				<?php //snapshot_utility_show_panel_messages(); ?>

				<form id="snapshot-edit-listing"
				      action="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_edit_panel"
				      method="post">
					<input type="hidden" name="snapshot-action" value="delete-bulk"/>
					<?php wp_nonce_field( 'snapshot-delete', 'snapshot-noonce-field' ); ?>
					<?php PSOURCESnapshot::instance()->items_table->display(); ?>
				</form>
			</div>
		<?php
		}


		/**
		 * Metabox showing form for editing previous Snapshots.
		 *
		 * @since 1.0.2
		 * @uses metaboxes setup in $this->admin_menu_proc()
		 * @uses $_REQUEST['item']
		 * @uses $this->config_data['items']
		 *
		 * @param none
		 *
		 * @return none
		 */
		function snapshot_admin_show_edit_panel( $item ) {
			$data_time_key = time();
			while ( true ) {
				if ( ! isset( PSOURCESnapshot::instance()->config_data['items']['data'][ $data_time_key ] ) ) {
					break;
				}

				$data_time_key = time();
			}

//			require( PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_PLUGIN_BASE_DIR' ) . '/lib/snapshot_admin_metaboxes.php' );
			$_snapshot_metaboxes = new Snapshot_View_Metabox_Admin();

			?>
			<div id="snapshot-settings-metaboxes-general" class="wrap snapshot-wrap">
				<h2><?php _ex( "Edit Snapshot", "Snapshot Plugin Page Title", 'cp-snapshot' ); ?></h2>

				<p><?php _ex( "Use this form to update the details for a previous snapshot. Also, provided is a link you can use to download the snapshot for sharing or archiving.", 'Snapshot page description', 'cp-snapshot' ); ?></p>
				<?php
				$SNAPSHOT_FILE_MISSING = false;
				?>
				<?php Snapshot_Helper_UI::form_ajax_panels(); ?>

				<div id="poststuff" class="metabox-holder">

					<form id="snapshot-add-update" method="post"
					      action="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_edit_panel">
						<input type="hidden" id="snapshot-action" name="snapshot-action" value="update"/>
						<input type="hidden" id="snapshot-item" name="snapshot-item"
						       value="<?php echo $item['timestamp']; ?>"/>
						<input type="hidden" id="snapshot-data-item" name="snapshot-data-item"
						       value="<?php echo $data_time_key; ?>"/>

						<?php wp_nonce_field( 'snapshot-update', 'snapshot-noonce-field' ); ?>

						<?php //echo "item<pre>"; print_r($item); echo "</pre>"; ?>

						<?php
						$_snapshot_metaboxes->snapshot_metaboxes_show_item_header_information(
							__( 'Snapshot Information', 'cp-snapshot' ), $item ); ?>

						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_backup_files_options(
							__( 'What Files to Archive?', 'cp-snapshot' ), $item );
						?>
						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_backup_tables_options(
							__( 'What Tables to Archive', 'cp-snapshot' ), $item );
						?>
						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_schedule_options(
							__( 'When to Archive', 'cp-snapshot' ), $item );
						?>
						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_destination_options(
							__( 'Where to save the Archive ', 'cp-snapshot' ), $item );
						?>

						<?php
						$_snapshot_metaboxes->snapshot_metabox_show_archive_files(
							__( 'All Archives', 'cp-snapshot' ), $item );
						?>

						<input class="button-primary" id="snapshot-form-save-submit" type="submit"
						       value="<?php _e( 'Save Snapshot', 'cp-snapshot' ); ?>"/>
						<a class="button-secondary"
						   href="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' );
						   ?>snapshots_edit_panel"><?php _e( 'Cancel', 'cp-snapshot' ); ?></a>

					</form>
				</div>

			</div>
		<?php
		}

		/**
		 * Panel showing form to restore previous Snapshot.
		 *
		 * @since 1.0.2
		 * @uses metaboxes setup in $this->admin_menu_proc()
		 * @uses $_REQUEST['item']
		 * @uses $this->config_data['items']
		 *
		 * @param none
		 *
		 * @return none
		 */
		function snapshot_admin_show_restore_panel( $item ) {
//			require( PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_PLUGIN_BASE_DIR' ) . '/lib/snapshot_admin_metaboxes.php' );
			$this->_snapshot_metaboxes = new Snapshot_View_Metabox_Admin();

			if ( ( isset( $_GET['snapshot-data-item'] ) ) && ( isset( $item['data'][ intval( $_GET['snapshot-data-item'] ) ] ) ) ) {
				$data_item_key = intval( $_GET['snapshot-data-item'] );
				?>
				<div id="snapshot-settings-metaboxes-general" class="wrap snapshot-wrap">
					<h2><?php _ex( "Restore Snapshot", "Snapshot Plugin Page Title", 'cp-snapshot' ); ?></h2>

					<p class="snapshot-restore-description"><?php _ex( "On this page you can restore a previous snapshot. Using the 'Restore Options' section below you can also opt to turn off all plugins as well as switch to a different theme as part of the restore.", 'Snapshot page description', 'cp-snapshot' ); ?></p>

					<div id='snapshot-ajax-warning' class='updated fade'>
						<p><?php _e( 'You are about to restore a previous version of your ClassicPress database. This will remove any new information added since the snapshot backup.', 'cp-snapshot' ); ?></p>
					</div>

					<?php
					if ( ! Snapshot_Helper_Utility::check_server_timeout() ) {
						$current_timeout = ini_get( 'max_execution_time' );
						?>
						<div class='error snapshot-error'>
						<p><?php printf( __( 'Your web server timeout is set very low, %d seconds. Also, it appears this timeout cannot be adjusted via the Snapshot restore process. Attempting a snapshot restore could result in a partial restore of your tables.', 'cp-snapshot' ), $current_timeout ); ?></p>
						</div><?php
					}
					?>
					<?php Snapshot_Helper_UI::form_ajax_panels(); ?>
					<?php
					if ( isset( $_GET['snapshot-data-item'] ) ) {
						$data_item = $item['data'][ $_GET['snapshot-data-item'] ];
					}

					$backupFolder = PSOURCESnapshot::instance()->snapshot_get_item_destination_path( $item, $data_item );
					if ( empty( $backupFolder ) ) {
						$backupFolder = PSOURCESnapshot::instance()->get_setting( 'backupBaseFolderFull' );
					}

					if ( ( isset( $data_item['filename'] ) ) && ( strlen( $data_item['filename'] ) ) ) {
						$manifest_filename = Snapshot_Helper_Utility::extract_archive_manifest( trailingslashit( $backupFolder ) . $data_item['filename'] );
						if ( $manifest_filename ) {
							//echo "manifest_filename=[". $manifest_filename ."]<br />";
							$manifest_data = Snapshot_Helper_Utility::consume_archive_manifest( $manifest_filename );
							if ( $manifest_data ) {
								//echo "manifest_data<pre>"; print_r($manifest_data); echo "</pre>";
								$item['MANIFEST'] = $manifest_data;
							}
						}
					}

					?>


					<div id="poststuff" class="metabox-holder">

						<form id="snapshot-edit-restore" action="<?php
						echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_edit_panel"
						      method="post">
							<input type="hidden" name="snapshot-action" value="restore-request"/>
							<input type="hidden" name="item" value="<?php echo $item['timestamp']; ?>"/>
							<?php wp_nonce_field( 'snapshot-restore', 'snapshot-noonce-field' ); ?>

							<?php $this->_snapshot_metaboxes->snapshot_metaboxes_show_item_header_information(
								__( 'Snapshot Information', 'cp-snapshot' ), $item, true ); ?>

							<?php
							$this->_snapshot_metaboxes->snapshot_metabox_show_archive_files(
								__( 'Selected Archive to Restore', 'cp-snapshot' ), $item, true );
							?>
							<?php
							//if (is_multisite()) {
							$this->_snapshot_metaboxes->snapshot_metabox_restore_blog_options(
								__( 'Restore Blog Options', 'cp-snapshot' ), $item );
							//}
							?>
							<?php
							$this->_snapshot_metaboxes->snapshot_metabox_show_restore_tables_options(
								__( 'What Tables to Restore?', 'cp-snapshot' ), $item, $data_item_key );
							?>
							<?php
							$this->_snapshot_metaboxes->snapshot_metabox_show_restore_files_options(
								__( 'What Files to Restore?', 'cp-snapshot' ), $item, $data_item_key );

							?>
							<?php
							$this->_snapshot_metaboxes->snapshot_metabox_restore_options( __( 'Restore Theme Options', 'cp-snapshot' ), $item );
							?>
							<input id="snapshot-form-restore-submit" class="button-primary"
								<?php
								if ( ! $data_item_key ) { ?> disabled="disabled" <?php } ?>
								   type="submit" value="<?php _e( 'Restore Snapshot', 'cp-snapshot' ); ?>"/>
							<a class="button-secondary"
							   href="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>
									snapshots_edit_panel"><?php _e( 'Cancel', 'cp-snapshot' ); ?></a>
						</form>
					</div>
				</div>
			<?php
			} else {
				?>
				<div id="snapshot-settings-metaboxes-general" class="wrap snapshot-wrap">
					<h2><?php _ex( "Restore Snapshot", "Snapshot Plugin Page Title", 'cp-snapshot' ); ?></h2>

					<p class="snapshot-restore-description"><?php _ex( "ERROR: Missing argument. Please return to the main Snapshot panel and select the archive to restore. ", 'Snapshot page description', 'cp-snapshot' ); ?>
						<a href="?page=snapshots_edit_panel">Snapshot</a>.</p>
				</div>
			<?php
			}
		}


		/**
		 * Metabox showing form for Settings.
		 *
		 * @since 1.0.2
		 * @uses metaboxes setup in $this->admin_menu_proc()
		 * @uses $_REQUEST['item']
		 * @uses $this->config_data['items']
		 *
		 * @param none
		 *
		 * @return none
		 */
		function snapshot_admin_show_settings_panel() {
			?>
			<div id="snapshot-settings-metaboxes-general" class="wrap snapshot-wrap">
				<h2><?php _ex( "Snapshot Settings", "Snapshot Plugin Page Title", 'cp-snapshot' ); ?></h2>

				<p><?php _ex( "The Settings panel provides access to a number of configuration options you can customize Snapshot to meet you site needs.", 'Snapshot page description', 'cp-snapshot' ); ?></p>

				<div id="poststuff" class="metabox-holder">
					<div id="post-body" class="">
						<div id="post-body-content" class="snapshot-metabox-holder-main">
							<?php do_meta_boxes( PSOURCESnapshot::instance()->snapshot_get_pagehook( 'snapshots-settings' ), 'normal', '' ); ?>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					jQuery(document).ready(function ($) {
						// close postboxes that should be closed
						$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

						// postboxes setup
						postboxes.add_postbox_toggles('<?php echo PSOURCESnapshot::instance()->snapshot_get_pagehook('snapshots-settings'); ?>');
					});
					//]]>
				</script>
			</div>
		<?php
		}

		function snapshot_admin_show_item_archive_panel( $item ) {
			PSOURCESnapshot::instance()->archives_data_items_table->prepare_items( $item );
			?>
			<div class="wrap snapshot-wrap">
				<h2>Snapshot Item Archive <a class="add-new-h2" href="<?php
					echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_edit_panel&amp;snapshot-action=edit&amp;item=<?php echo $item['timestamp']; ?>">Edit
						Item</a></h2>
				<?php //echo "_REQUEST<pre>"; print_r($_REQUEST); echo "</pre>"; ?>
				<form id="snapshot-item-archives-form" method="get">
					<input type="hidden" name="snapshot-action" value="item-archives"/>
					<input type="hidden" name="page" value="snapshots_edit_panel"/>
					<input type="hidden" name="item" value="<?php echo $item['timestamp']; ?>"/>

					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>


					<div style="float: right"
					     class="snapshot-system-time"><?php _e( 'Current time:', 'cp-snapshot' ); ?>
						<strong><?php
							echo Snapshot_Helper_Utility::show_date_time( time() ) ?></strong></div>

					<?php if ( $item['destination-sync'] == "mirror" ) {
						?>
						<p><?php _e( 'This Snapshot item is setup as <strong>files sync</strong>. You cannot perform a resend of the individual items like on a normal archive. But you can click the <strong>resend</strong> on any item below to clear the last send dates on all files. This will force all files to be re-synced.', 'cp-snapshot' ); ?></p><?php
					}
					?>
					<!-- Now we can render the completed list table -->
					<?php PSOURCESnapshot::instance()->archives_data_items_table->display(); ?>
				</form>
			</div>
		<?php
		}

		function snapshot_admin_show_import_panel() {
//			require( PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_PLUGIN_BASE_DIR' ) . '/lib/snapshot_admin_metaboxes.php' );
			$_snapshot_metaboxes = new Snapshot_View_Metabox_Admin();

			?>
			<div id="snapshot-settings-metaboxes-general" class="wrap snapshot-wrap">
				<h2><?php _ex( "Snapshot Scan / Import", "Snapshot Plugin Page Title", 'cp-snapshot' ); ?></h2>

				<div id="poststuff" class="metabox-holder">
					<div id="post-body" class="">
						<div id="post-body-content" class="snapshot-metabox-holder-main">
							<p><?php _e( 'The Snapshot import form below is used to import snapshot archives from outside of this environment into view of the snapshot plugin. If you are attempting to restore an archive from a remote server for example you first need to import the archive here. This will then show the archive in the All Snapshots listing. From that page you can then perform the needed restore.', 'cp-snapshot' ); ?></p>

							<form
								action="<?php echo PSOURCESnapshot::instance()->get_setting( 'SNAPSHOT_MENU_URL' ); ?>snapshots_import_panel"
								method="post">
								<input type="hidden" value="archives-import" name="snapshot-action">
								<?php wp_nonce_field( 'snapshot-import', 'snapshot-noonce-field' ); ?>

								<?php $_snapshot_metaboxes->snapshot_metaboxes_show_import(
									__( 'Import Options', 'cp-snapshot' ) ); ?>

								<input id="snapshot-add-button" class="button-primary" type="submit"
								       value="<?php _e( 'Scan / Import Snapshots', 'cp-snapshot' ); ?>"/>
							</form>
						</div>
					</div>
					<?php

					if ( isset( $_REQUEST['snapshot-action'] ) && 'archives-import' === esc_attr( $_REQUEST['snapshot-action'] ) ) {
						if ( wp_verify_nonce( $_POST['snapshot-noonce-field'], 'snapshot-import' ) ) {

							if ( ! empty( $_POST['snapshot-import-archive-remote-url'] ) ) {

								if ( substr( $_POST['snapshot-import-archive-remote-url'], 0, 4 ) == 'http' ) {

									if ( function_exists( 'curl_version' ) ) {

										$remoteFile = esc_url_raw( $_POST['snapshot-import-archive-remote-url'] );

										@set_time_limit( 900 ); // 15 minutes Technically, server to server should be quick for large files.

										?><p><?php _e( "PHP max_execution_time", 'cp-snapshot' ); ?>
										: <?php echo ini_get( 'max_execution_time' ); ?>s</p><?php

										?><p><?php _e( "Attempting to download remote file", 'cp-snapshot' ); ?>
										: <?php echo $remoteFile; ?></p><?php
										flush();

										$restoreFile = trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupBaseFolderFull' ) ) . basename( $remoteFile );
										//echo "remoteFile=[". $remoteFile ."]<br />";
										//echo "restoreFile=[". $restoreFile ."]<br />";

										Snapshot_Helper_Utility::remote_url_to_local_file( $remoteFile, $restoreFile );
										if ( file_exists( $restoreFile ) ) {

											$restoreFolder = trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupRestoreFolderFull' ) ) . "_imports";

											echo "<ol>";
											echo "<li><strong>" . __( 'Processing archive', 'cp-snapshot' ) . ": ", basename( $restoreFile ) . "</strong> (" .
											                                                                               Snapshot_Helper_Utility::size_format( filesize( $restoreFile ) ) . ")<ul><li>";
											flush();
											$error_status = Snapshot_Helper_Utility::archives_import_proc( $restoreFile, $restoreFolder );
											//echo "error_status<pre>"; print_r($error_status); echo "</pre>";
											if ( ( isset( $error_status['errorStatus'] ) ) && ( $error_status['errorStatus'] === true ) ) {
												if ( ( isset( $error_status['errorText'] ) ) && ( strlen( $error_status['errorText'] ) ) ) {
													echo '<span class="snapshot-error">Error: ' . $error_status['errorText'] . '</span></br />';
												}
											} else if ( ( isset( $error_status['errorStatus'] ) ) && ( $error_status['errorStatus'] === false ) ) {
												if ( ( isset( $error_status['responseText'] ) ) && ( strlen( $error_status['responseText'] ) ) ) {
													echo '<span class="snapshot-success">Success: ' . $error_status['responseText'] . '</span></br />';
												} else {

												}
											}
											echo "</li></ul></li>";
											echo "</ol>";
										} else {
											echo '<p>' . __( 'Error: Your server does not have lib_curl installed. So the import process cannot retrieve remote file.', 'cp-snapshot' ) . '</p>';
										}
									} else {
										echo "<p>" . __( 'local import file not found. This could mean either the entered URL was not valid or the file was not publicly accessible.', 'cp-snapshot' ) . "</p>";
									}
								} else {
									// Then a local directory

									// Are we dealing with a absolote path...
									if ( substr( $_POST['snapshot-import-archive-remote-url'], 0, 1 ) == "/" ) {
										$dir = trailingslashit( esc_attr( $_POST['snapshot-import-archive-remote-url'] ) );
									} else {
										$dir = trailingslashit( trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupBaseFolderFull' ) ) . esc_attr( $_POST['snapshot-import-archive-remote-url'] ) );
									}
									//echo "dir[". $dir ."]<br />";
									if ( is_dir( $dir ) ) {
										echo "<p>" . __( 'Importing archives from', 'cp-snapshot' ) . ": " . $dir . "</p>";

										if ( $dh = opendir( $dir ) ) {
											$restoreFolder = trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupRestoreFolderFull' ) ) . "_imports";

											echo "<ol>";
											while ( ( $file = readdir( $dh ) ) !== false ) {

												if ( ( $file == '.' ) || ( $file == '..' ) || ( $file == 'index.php' ) || ( $file[0] == '.' ) ) {
													continue;
												}

												if ( pathinfo( $file, PATHINFO_EXTENSION ) != "zip" ) {
													continue;
												}

												$restoreFile = $dir . $file;
												if ( is_dir( $restoreFile ) ) {
													continue;
												}

												// Check if the archive is full backup - we don't import those
												if (Snapshot_Helper_Backup::is_full_backup($file)) continue;


												echo "<li><strong>" . __( 'Processing archive', 'cp-snapshot' ) . ": ", basename( $restoreFile ) . "</strong> (" .
												                                                                               Snapshot_Helper_Utility::size_format( filesize( $restoreFile ) ) . ")<ul><li>";
												flush();
												$error_status = Snapshot_Helper_Utility::archives_import_proc( $restoreFile, $restoreFolder );
												//echo "error_status<pre>"; print_r($error_status); echo "</pre>";
												if ( ( isset( $error_status['errorStatus'] ) ) && ( $error_status['errorStatus'] === true ) ) {
													if ( ( isset( $error_status['errorText'] ) ) && ( strlen( $error_status['errorText'] ) ) ) {
														echo '<span class="snapshot-error">Error: ' . $error_status['errorText'] . '</span></br />';
													}
												} else if ( ( isset( $error_status['errorStatus'] ) ) && ( $error_status['errorStatus'] === false ) ) {
													if ( ( isset( $error_status['responseText'] ) ) && ( strlen( $error_status['responseText'] ) ) ) {
														echo '<span class="snapshot-success">Success: ' . $error_status['responseText'] . '</span></br />';
													} else {

													}
												}
												echo "</li></ul></li>";
											}
											echo "</ol>";

											closedir( $dh );

										}
									} else {
										echo "<p>" . sprintf( __( 'local import file not found %s. This could mean either the entered path was not valid or accessible.', 'cp-snapshot' ), $dir ) . "</p>";

									}
								}

							} else {

								$dir = trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupBaseFolderFull' ) );
								printf( '<p>%s: %s</p>', __( 'Importing archives from', 'cp-snapshot' ), $dir );

								if ( $dh = opendir( $dir ) ) {
									$restoreFolder = trailingslashit( PSOURCESnapshot::instance()->get_setting( 'backupRestoreFolderFull' ) ) . "_imports";

									echo '<ol>';
									while ( false !== ( $file = readdir( $dh ) ) ) {

										if ( $file == '.' || $file == '..' || $file == 'index.php' || $file[0] == '.' ) {
											continue;
										}

										if ( 'zip' !== pathinfo( $file, PATHINFO_EXTENSION ) ) {
											continue;
										}

										$restoreFile = $dir . $file;

										if ( is_dir( $restoreFile ) ) {
											continue;
										}

										// Check if the archive is full backup - we don't import those
										if (Snapshot_Helper_Backup::is_full_backup($file)) continue;

										printf( '<li><strong>%s: %s</strong> (%s)<ul><li>',
											__( 'Processing archive', 'cp-snapshot' ),
											basename( $restoreFile ),
											Snapshot_Helper_Utility::size_format( filesize( $restoreFile ) )
										);

										flush();
										$error_status = Snapshot_Helper_Utility::archives_import_proc( $restoreFile, $restoreFolder );

										if ( isset( $error_status['errorStatus'] ) ) {

											if ( $error_status['errorStatus'] ) {
												if ( ! empty( $error_status['errorText'] ) ) {
													echo '<span class="snapshot-error">', sprintf( __( 'Error: %s', 'cp-snapshot' ), $error_status['errorText'] ), '</span></br />';
												}
											} else {
												if ( ! empty( $error_status['responseText'] ) ) {
													echo '<span class="snapshot-success">', sprintf( __( 'Success: %s', 'cp-snapshot' ), $error_status['errorText'] ), '</span></br />';
												}
											}
										}

										echo '</li></ul></li>';
									}
									echo "</ol>";

									closedir( $dh );

								}
							}
						}
						echo "<p>" . __( 'No errors were encountered during the import process.', 'cp-snapshot' ) . "</p>";
					}
					?>
				</div>
			</div>
		<?php
		}
	}
}