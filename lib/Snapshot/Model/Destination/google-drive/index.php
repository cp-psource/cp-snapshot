<?php
/*
Snapshots Plugin Destinations Google Drive
Author: DerN3rd (PSOURCE)
*/

if ( ! defined( 'PSOURCE_SNAPSHOT_DESTINATION_GOOGLE_DRIVE_LOAD_LIB' ) ) {
	define( 'PSOURCE_SNAPSHOT_DESTINATION_GOOGLE_DRIVE_LOAD_LIB', 'init' );
}

if ( ! class_exists( 'SnapshotDestinationGoogleDrive' ) && version_compare( phpversion(), '5.2', '>' )
	 && stristr( PSOURCE_SNAPSHOT_DESTINATIONS_EXCLUDE, 'SnapshotDestinationGoogleDrive' ) === false ) {

	if ( PSOURCE_SNAPSHOT_DESTINATION_GOOGLE_DRIVE_LOAD_LIB == 'head' ) {
		set_include_path( dirname( __FILE__ ) . PATH_SEPARATOR . get_include_path() );
		require_once( dirname( __FILE__ ) . '/Google/Client.php' );
		require_once( dirname( __FILE__ ) . '/Google/Http/MediaFileUpload.php' );
		require_once( dirname( __FILE__ ) . '/Google/Service/Drive.php' );
	}

	class SnapshotDestinationGoogleDrive extends Snapshot_Model_Destination {

		// The slug and name are used to identify the Destination Class
		public $name_slug;
		public $name_display;

		public $snapshot_logger;
		public $snapshot_locker;

		/**
		 * @public Google_0814_Client
		 */
		public $client;

		public $connection;

		public $SCOPES = array(
			'https://www.googleapis.com/auth/drive.file',
		);

		// These vars are used when connecting and sending file to the destination. There is an
		// interface function which populates these from the destination data.
		public $destination_info;
		public $error_array;
		public $form_errors;

		function on_creation() {
			//private destination slug. Lowercase alpha (a-z) and dashes (-) only please!
			$this->name_slug = 'google-drive';

			// The display name for listing on admin panels
			$this->name_display = __( 'Google Drive', SNAPSHOT_I18N_DOMAIN );
		}

		function init() {

			if ( PSOURCE_SNAPSHOT_DESTINATION_GOOGLE_DRIVE_LOAD_LIB == __FUNCTION__ ) {
				set_include_path( dirname( __FILE__ ) . PATH_SEPARATOR . get_include_path() );
				require_once( dirname( __FILE__ ) . '/Google/Client.php' );
				require_once( dirname( __FILE__ ) . '/Google/Http/MediaFileUpload.php' );
				require_once( dirname( __FILE__ ) . '/Google/Service/Drive.php' );
			}

			if ( isset( $this->destination_info ) ) {
				unset( $this->destination_info );
			}
			$this->destination_info = array();

			if ( isset( $this->error_array ) ) {
				unset( $this->error_array );
			}
			$this->error_array = array();

			$this->error_array['errorStatus'] = false;
			$this->error_array['sendFileStatus'] = false;
			$this->error_array['errorArray'] = array();
			$this->error_array['responseArray'] = array();

			// Kill our instance of the AWS connection
			if ( isset( $this->client ) ) {
				unset( $this->client );
			}

			if ( isset( $this->connection ) ) {
				unset( $this->connection );
			}

			set_error_handler( array( &$this, 'ErrorHandler' ) );

		}

		function ErrorHandler( $errno, $errstr, $errfile, $errline ) {
			if ( ! error_reporting() ) {
				return;
			}

			$errType = '';
			switch ( $errno ) {
				case E_USER_ERROR:
					$errType = "Error";
					break;

				case E_USER_WARNING:
					$errType = "Warning";
					break;

				case E_USER_NOTICE:
					$errType = "Notice";
					break;

				default:
					$errType = "Unknown";
					break;
			}

			if ( ! ( error_reporting() & $errno ) ) {
				return;
			}

			$error_string = $errType . ": errno:" . $errno . " " . $errstr . " " . $errfile . " on line " . $errline;

			$this->error_array['errorStatus'] = true;
			$this->error_array['errorArray'][] = $error_string;

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				echo json_encode( $this->error_array );
				die();
			}
		}

		function getAuthorizationUrl() {

			$this->login();
			if ( is_object( $this->client ) ) {
				$auth_url = $this->client->createAuthUrl();

				return $auth_url;
			}
		}

		function destination_ajax_proc() {
			$this->init();

			if ( ! isset( $_POST['snapshot_action'] ) ) {
				$this->error_array['errorStatus'] = true;
				$this->error_array['errorArray'][] = "Error: Missing 'snapshot_action' value.";
				echo json_encode( $this->error_array );
				die();
			}

			if ( ! isset( $_POST['destination_info'] ) ) {
				$this->error_array['errorStatus'] = true;
				$this->error_array['errorArray'][] = "Error: Missing 'destination_info' values.";
				echo json_encode( $this->error_array );
				die();
			}
			$destination_info = $_POST['destination_info'];

			if ( ! $this->validate_form_data( $destination_info ) ) {
				$this->error_array['errorStatus'] = true;
				$this->error_array['errorArray'][] = implode( ', ', $this->form_errors );
				echo json_encode( $this->error_array );
				die();
			}

			$this->load_class_destination( $destination_info );

			if ( $_POST['snapshot_action'] == "connection-test" ) {

				if ( ! $this->login() ) {
					echo json_encode( $this->error_array );
					die();
				}

				$tmpfname = tempnam( sys_get_temp_dir(), 'Snapshot_' );
				$handle = fopen( $tmpfname, "w" );
				fwrite( $handle, "PSOURCE Snapshot Test connection file." );
				fclose( $handle );

				$this->send_file( $tmpfname );
				echo json_encode( $this->error_array );
				die();

			} else if ( $_POST['snapshot_action'] == "aws-get-bucket-list" ) {

				if ( ! $this->login() ) {
					echo json_encode( $this->error_array );
					die();
				}

				echo json_encode( $this->error_array );
				die();
			}

			echo json_encode( $this->error_array );
			die();
		}

		function login() {

			try {

				$this->client = new Google_0814_Client();
				$this->client->setClientId( $this->destination_info['clientid'] );
				$this->client->setClientSecret( $this->destination_info['clientsecret'] );
				$this->client->setRedirectUri( $this->destination_info['redirecturi'] );
				$this->client->setAccessType( 'offline' );
				$this->client->setState( 'token' );
				$this->client->setApprovalPrompt( 'force' );
				$this->client->setScopes( $this->SCOPES );

				if ( ! empty( $this->destination_info['access_token'] ) ) {
					$this->client->setAccessToken( $this->destination_info['access_token'] );
					if ( $this->client->isAccessTokenExpired() ) {

						// IF the current access_token is no longer valid we refresh using the refresh_token we saved the first time.
						$access_token_current = json_decode( $this->destination_info['access_token'] );
						$this->client->refreshToken( $access_token_current->refresh_token );

						$access_token_new = $this->client->getAccessToken();
						$this->client->setAccessToken( $access_token_new );

						if ( $this->client->isAccessTokenExpired() ) {
							echo "access_token2 has expired #2<br />";
						} else {
							//echo "access_token2 NOT expired<br />";
							//return true;
						}
					} else {
						//echo "access_token1 NOT expired<br />";
					}
				}

			} catch ( Exception $e ) {
				//echo "e<pre>"; print_r($e); echo "</pre>";
				$this->error_array['errorStatus'] = true;
//				$this->error_array['errorArray'][] 		= sprintf(__("Error: Could not connect to %s :", SNAPSHOT_I18N_DOMAIN), $this->name_display) . $e->getMessage();

				if ( isset( $this->snapshot_logger ) ) {
					$this->snapshot_logger->log_message( sprintf( __( "Error: Could not connect to %s: Error: %s", SNAPSHOT_I18N_DOMAIN ), $this->name_display, $e ) );
				}

				//echo "error_array<pre>"; print_r($error_array); echo "</pre>";
				return false;
			}

			$this->connection = new Google_0814_Service_Drive( $this->client );

			return true;
		}

		function sendfile_to_remote( $destination_info, $filename ) {
			$this->init();
			$this->load_class_destination( $destination_info );

			$this->snapshot_logger->log_message( sprintf( __( "Connecting to %s", SNAPSHOT_I18N_DOMAIN ), $this->name_display ) );

			if ( ! $this->login() ) {
				return $this->error_array;
			}

			$this->send_file( $filename );

			//$this->snapshot_logger->log_message( "1 error_array :<pre>". print_r($this->error_array, true) ."</pre>");
			return $this->error_array;
		}

		function send_file( $filename ) {

			$this->snapshot_logger->log_message( "Sending file to directory: " . $this->destination_info['directory'] );

			//$this->error_array['responseArray'][] = "Sending file to: Directory: ". $this->destination_info['directory'];

			try {

				$file = new Google_0814_Service_Drive_DriveFile();
				$file->title = basename( $filename );
				$chunkSizeBytes = 1 * 1024 * 1024;
				//echo "chunkSizeBytes[". $chunkSizeBytes ."]<br />";

				if ( ! empty( $this->destination_info['directory'] ) ) {
					$parent_directories = explode( ',', $this->destination_info['directory'] );
					$parent = new Google_0814_Service_Drive_ParentReference();
					foreach ( $parent_directories as $parent_directory ) {
						$parent_directory = trim( $parent_directory );
						if ( ! empty( $parent_directory ) ) {
							$parent->setId( $parent_directory );
						}
					}
					$file->setParents( array( $parent ) );
				}

				// Call the API with the media upload, defer so it doesn't immediately return.
				$this->client->setDefer( true );
				$request = $this->connection->files->insert( $file );
				if ( is_object( $request ) ) {
					// Create a media file upload to represent our upload process.
					$media = new Google_0814_Http_MediaFileUpload(
						$this->client,
						$request,
						'application/x-zip',
						null,
						true,
						$chunkSizeBytes
					);

					$filename_size = filesize( $filename );
					$media->setFileSize( $filename_size );

					// Upload the various chunks. $status will be false until the process is
					// complete.
					$status = false;
					$handle = fopen( $filename, "rb" );
					$chunk_int = 0;
					$chunk_parts_sum = 0;
					while ( ! $status && ! feof( $handle ) ) {
						$chunk = fread( $handle, $chunkSizeBytes );
						$status = $media->nextChunk( $chunk );
						$chunk_int += 1;
						$chunk_parts_sum += strlen( $chunk );
						//echo "[". $chunk_int ."] [". number_format(($chunk_parts_sum/$filename_size)*100, 4) ."%] status[". $status ."]<br />";

						$this->snapshot_logger->log_message( "progeess: " . number_format( ( $chunk_parts_sum / $filename_size ) * 100, 2 ) . "%" );

						$this->progress_of_files( array( 'file_offset' => $chunk_parts_sum ) );
					}
					fclose( $handle );

					$httpResultCode = $media->getHttpResultCode();
					if ( ( $httpResultCode == 200 ) && ( $status != false ) ) {
						//echo "status<pre>"; print_r($status); echo "</pre>";

						//$this->snapshot_logger->log_message( "Send file success: " . basename($filename));
						$this->error_array['responseArray'][] = "Send file success: " . basename( $filename );

						//$this->snapshot_logger->log_message( "Google Drive Link: " . $status->selfLink );
						$this->error_array['responseArray'][] = "Google Drive File ID[" . $status->id . "] Link: " . $status->selfLink;

						//$file = $service->files->get($fileId);
						//$file->setTitle($newTitle);

						$this->error_array['sendFileStatus'] = true;

						return true;

					} else {
						$this->error_array['errorStatus'] = true;

						//$this->snapshot_logger->log_message( "HTTP bad response:" . $httpResultCode ." :<pre>". $status ."</pre>");
						$this->error_array['responseArray'][] = 'HTTP bad response:' . $httpResultCode . " :<pre>" . $status . "</pre>";

						//$this->error_array['errorArray'][] 		= $this->dropbox->last_result;

						return false;
					}
				}
			} catch ( Exception $e ) {
				$this->error_array['errorStatus'] = true;
				$this->error_array['errorArray'][] = sprintf( __( 'Error: Could not send file <pre>%s</pre> :', SNAPSHOT_I18N_DOMAIN ), $e ) . $e->getMessage();

				return false;
			}
		}

		function progress_of_files( $file_array ) {

			if ( is_object( $this->snapshot_locker ) ) {
				$locker_info = $this->snapshot_locker->get_locker_info();
				foreach ( $file_array as $_key => $_val ) {
					$locker_info[ $_key ] = $_val;
				}
				$this->snapshot_locker->set_locker_info( $locker_info );
			}
		}

		function load_fields( $input, $result = null ) {
			$result = is_array( $result ) ? $result : array();
			$fields = array( 'type', 'name', 'directory', 'clientid', 'clientsecret', 'redirecturi', 'access_token' );

			foreach ( $fields as $field ) {
				$result[ $field ] = empty( $input[ $field ] ) ? '' : sanitize_text_field( stripslashes( $input[ $field ] ) );
			}

			return $result;
		}

		function load_class_destination( $d_info ) {
			$this->destination_info = $this->load_fields( $d_info, $this->destination_info );

			$text_fields = array( 'type', 'name', 'directory', 'clientid', 'clientsecret', 'redirecturi', 'access_token' );
			$special_fields = array( 'clientsecret', 'access_token' );

			foreach ( $text_fields as $field ) {
				if ( empty( $d_info[ $field ] ) ) {
					$this->destination_info[ $field ] =  '';
				} elseif ( in_array( $field, $special_fields) ) {
					$this->destination_info[ $field ] =  $d_info[ $field ];
				} else {
					$this->destination_info[ $field ] =  sanitize_text_field( stripslashes( $d_info[ $field ] ) );
				}
			}
		}

		function validate_form_data( $d_info ) {
			$this->init();
			$this->form_errors = array();

			$destination_info = $this->load_fields( $d_info, array() );
			$form_step = isset( $d_info['form-step'] ) ? intval( $d_info['form-step'] ) : 1;
			$form_step = max( $form_step, 1 );

			$advance_form = true;

			if ( $form_step >= 3 ) {

				if ( ! $destination_info['access_token'] ) {
					$this->form_errors['access_token'] = __( 'An access token from Google is required', SNAPSHOT_I18N_DOMAIN );
					$advance_form = false;
				}
			}

			if ( $form_step >= 2 ) {

				if ( ! $destination_info['clientid'] || ! $destination_info['clientsecret'] || ! $destination_info['directory'] ) {
					if ( empty( $destination_info['clientid'] ) ) {
						$this->form_errors['clientid'] = esc_html__( 'A client ID is required', SNAPSHOT_I18N_DOMAIN );
					}
					if ( empty( $destination_info['clientsecret'] ) ) {
						$this->form_errors['clientsecret'] = esc_html__( 'A client secret ID is required', SNAPSHOT_I18N_DOMAIN );
					}
					if ( empty( $destination_info['directory'] ) ) {
						$this->form_errors['directory'] = esc_html__( 'A directory is required', SNAPSHOT_I18N_DOMAIN );
					}

					$advance_form = false;
				}
			}

			if ( $form_step >= 1 ) {

				if ( ! $destination_info['name'] ) {
					$this->form_errors['name'] = esc_html__( 'A name for the destination is required', SNAPSHOT_I18N_DOMAIN );
					$advance_form = false;
				}

				if ( ! $destination_info['directory'] ) {
					$this->form_errors['directory'] = esc_html__( 'A directory ID is required', SNAPSHOT_I18N_DOMAIN );
					$advance_form = false;
				}
			}

			if ( $advance_form ) {
				$form_step = min( 4, $form_step + 1 );
			}

			if ( $form_step < 4 ) {
				$destination_info['form-step-url'] = esc_url_raw( add_query_arg( array( 'step' => $form_step, 'snapshot-action' => 'edit' ) ) );
			}

			return $destination_info;
		}

		function display_listing_table( $destinations, $edit_url, $delete_url ) {

			?>
			<table class="widefat">
				<thead>
				<tr class="form-field">
					<th class="snapshot-col-delete"><?php _e( 'Delete', SNAPSHOT_I18N_DOMAIN ); ?></th>
					<th class="snapshot-col-name"><?php _e( 'Name', SNAPSHOT_I18N_DOMAIN ); ?></th>
					<th class="snapshot-col-access-key"><?php _e( 'Client ID', SNAPSHOT_I18N_DOMAIN ); ?></th>
					<th class="snapshot-col-directory"><?php _e( 'Directory', SNAPSHOT_I18N_DOMAIN ); ?></th>
					<th class="snapshot-col-used"><?php _e( 'Used', SNAPSHOT_I18N_DOMAIN ); ?></th>
				</tr>
				<thead>
				<tbody>
				<?php
				if ( ( isset( $destinations ) ) && ( count( $destinations ) ) ) {

					foreach ( $destinations as $idx => $item ) {

						if ( ! isset( $row_class ) ) {
							$row_class = "";
						}
						$row_class = ( $row_class == '' ? 'alternate' : '' );

						?>
						<tr class="<?php echo $row_class; ?><?php
						if ( isset( $item['type'] ) ) {
							echo ' snapshot-row-filter-type-' . $item['type'];
						} ?>">
							<td class="snapshot-col-delete">
								<input type="checkbox" name="delete-bulk-destination[<?php echo $idx; ?>]" id="delete-bulk-destination-<?php echo $idx; ?>">
							</td>

							<td class="snapshot-col-name"><a
										href="<?php echo $edit_url; ?>item=<?php echo $idx; ?>"><?php echo stripslashes( $item['name'] ) ?></a>

								<div class="row-actions" style="margin:0; padding:0;">
									<span class="edit"><a
												href="<?php echo $edit_url; ?>item=<?php echo $idx; ?>"><?php _e( 'edit', SNAPSHOT_I18N_DOMAIN ); ?></a></span>
									| <span class="delete"><a
												href="<?php echo $delete_url; ?>item=<?php echo $idx; ?>&amp;snapshot-noonce-field=<?php echo wp_create_nonce( 'snapshot-delete-destination' ); ?>"><?php _e( 'delete', SNAPSHOT_I18N_DOMAIN ); ?></a></span>
								</div>
							</td>
							<td class="snapshot-col-server"><?php
								if ( isset( $item['clientid'] ) ) {
									echo $item['clientid'];
								} ?></td>
							<td class="snapshot-col-directory"><?php
								if ( isset( $item['directory'] ) ) {
									echo $item['directory'];
								} ?></td>
							<td class="snapshot-col-used"><?php Snapshot_Model_Destination::show_destination_item_count( $idx ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr class="form-field">
					<td colspan="4"><?php
						echo sprintf( __( 'No %s Destinations', SNAPSHOT_I18N_DOMAIN ), $this->name_display ); ?></td>
					</tr><?php
				}
				?>
				</tbody>
			</table>
			<?php
			if ( ( isset( $destinations ) ) && ( count( $destinations ) ) ) {
				?>
				<div class="tablenav">
					<div class="alignleft actions">
						<input class="button-secondary" type="submit"
							   value="<?php _e( 'Delete Destination', SNAPSHOT_I18N_DOMAIN ); ?>"/>
					</div>
				</div>
				<?php
			}
			?>
			<?php
		}

		function display_details_form( $item = 0 ) {

			$this->init();

			if ( ( ! isset( $_GET['item'] ) ) || ( empty( $item['name'] ) ) ) {
				$form_step = 1;
			} else if ( ( empty( $item['clientid'] ) ) || ( empty( $item['clientsecret'] ) ) ) {
				$form_step = 2;
			} else if ( empty( $item['access_token'] ) ) {
				$form_step = 3;
			} else {
				$form_step = 4;
			}

			?>
			<input type="hidden" name="snapshot-destination[form-step]" id="snapshot-destination-form-step" value="<?php echo $form_step ?>"/>

			<p><?php _e( 'Define an Google Drive destination connection. You can define multiple destinations which use Google Drive. Each destination can use different security keys and/or directory.', SNAPSHOT_I18N_DOMAIN ); ?></p>
			<div id="poststuff" class="metabox-holder">
			<div style="display: none" id="snapshot-destination-test-result"></div>
			<div class="postbox" id="snapshot-destination-item">

				<h3 class="hndle"><span><?php _e( 'Google Drive Destination', SNAPSHOT_I18N_DOMAIN ); ?></span></h3>

				<div class="inside">
					<input type="hidden" name="snapshot-destination[type]" id="snapshot-destination-type"
						   value="<?php echo $this->name_slug; ?>"/>
					<?php
					if ( ( ! isset( $_GET['item'] ) ) || ( empty( $item['name'] ) ) ) {
						$form_step = 1;
						?>
						<p><?php _e( 'Step 1: Define a name for this Destination', SNAPSHOT_I18N_DOMAIN ) ?><?php if ( $form_step > 1 ) {
								echo ' - ' . __( '<strong>COMPLETE</strong>' );
							} ?></p>
						<?php
					}
					/*
					else {
						?>
						<p class="description"><?php _e('In the form below provide the Client ID, Client Secret from the Google. The Redirect URI provided below needs to be added to the settings in Google for the same Client ID and Client Secret.', SNAPSHOT_I18N_DOMAIN) ?></p>
						<?php
					} */
					?>
					<table class="form-table">
						<tr class="form-field">
							<th scope="row"><label
										for="snapshot-destination-name"><?php _e( 'Destination Name', SNAPSHOT_I18N_DOMAIN ); ?></label>
							</th>
							<td>
								<?php if ( $form_step == 1 ) { ?>
									<input type="text" name="snapshot-destination[name]" id="snapshot-destination-name"
										   value="<?php if ( isset( $item['name'] ) ) {
											   echo stripslashes( sanitize_text_field( $item['name'] ) );
										   } ?>"/>
								<?php } else if ( $form_step > 1 ) {
									echo stripslashes( sanitize_text_field( $item['name'] ) )
									?><input type="hidden" name="snapshot-destination[name]"
											 id="snapshot-destination-name" value="<?php if ( isset( $item['name'] ) ) {
										echo stripslashes( sanitize_text_field( $item['name'] ) );
									} ?>" /><?php
								} ?>
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row" style="width:10%"><label
										for="snapshot-destination-directory"><?php _e( 'Directory ID (optional)', SNAPSHOT_I18N_DOMAIN ); ?></label>
							</th>
							<td style="width:40%"><input type="text" name="snapshot-destination[directory]"
														 id="snapshot-destination-directory"
														 value="<?php if ( isset( $item['directory'] ) ) {
															 echo $item['directory'];
														 } ?>"/><br/>

								<p class="description"><?php echo sprintf( __( 'Note: This is not a traditional directory path like /usr/local/path but a unique ID assigned by Google for the directory with your Drive. See the instructions to the right on how to obtain the Directory ID.', SNAPSHOT_I18N_DOMAIN ) ) ?></p>
							</td>
							<td style="width:50%">
								<p><?php _e( 'Instructions', SNAPSHOT_I18N_DOMAIN ) ?></p>
								<ol>
									<li><?php echo sprintf( __( 'Go to your %s', SNAPSHOT_I18N_DOMAIN ), '<a href="https://drive.google.com/#my-drive">' . __( 'Drive account. Navigate to or create a new directory where you want to upload the Snapshot archives. Make sure you are viewing the destination directory.', SNAPSHOT_I18N_DOMAIN ) . '</a>' ) ?></li>
									<li><?php _e( 'The URL for the directory will be something similar to <em>https://drive.google.com/#folders/0B6GD66ctHXXCOWZKNDRIRGJJXS3</em>. The Directory ID would be the last part after /#folders/ <strong><em>0B6GD66ctHXXCOWZKNDRIRGJJXS3</em></strong>.', SNAPSHOT_I18N_DOMAIN ) ?></li>
									<li><?php _e( 'You can define multiple Directory IDs seperated by comma', SNAPSHOT_I18N_DOMAIN ) ?></li>
								</ol>
							</td>
						</tr>
					</table>
					<?php
					if ( $form_step > 1 ) {
						?>
						<p><?php _e( 'Step 2: Google Drive Access Credentials', SNAPSHOT_I18N_DOMAIN ) ?><?php if ( $form_step > 2 ) {
								echo ' - ' . __( '<strong>COMPLETE</strong>' );
							} ?></p>
						<table class="form-table">
							<tr class="form-field">
								<th scope="row" style="width:10%"><label
											for="snapshot-destination-clientid"><?php _e( 'Client ID', SNAPSHOT_I18N_DOMAIN ); ?></label>
								</th>
								<td style="width:40%"><input type="text" name="snapshot-destination[clientid]"
															 id="snapshot-destination-clientid"
															 value="<?php if ( isset( $item['clientid'] ) ) {
																 echo sanitize_text_field( $item['clientid'] );
															 } ?>"/></td>
								<td rowspan="3" style="width: 50%">
									<p><?php _e( 'Instructions', SNAPSHOT_I18N_DOMAIN ) ?></p>
									<ol>
										<li><?php echo sprintf( __( 'Go to the %s', SNAPSHOT_I18N_DOMAIN ), '<a href="https://console.developers.google.com/project">' . __( 'Google Project Console', SNAPSHOT_I18N_DOMAIN ) . '</a>' ) ?></li>
										<li><?php _e( 'Select existing or Add a new Project. If you add a new project you will see a popup. Enter a project name. The Project ID is not important and can be ignored.', SNAPSHOT_I18N_DOMAIN ); ?></li>
										<li><?php _e( 'Once the Project creation is completed go to the <strong>API Manager</strong>. Here you need to enable the <strong>Drive API</strong>', SNAPSHOT_I18N_DOMAIN ) ?></li>
										<li><?php _e( 'Next, go to the <strong>API Manager > Credentials</strong> section. Click <strong>Add New Credentials > OAuth 2.0 client ID</strong>. In the popup select the <strong>Application Type</strong> as <strong>Web application</strong>. In the field <strong>Authorized redirect URI</strong> copy the value from the <strong>Redirect URI</strong> field to the left. Then click the <strong>Create Client ID</strong> button.', SNAPSHOT_I18N_DOMAIN ) ?></li>
										<li><?php _e( 'After the popup closes copy the Client ID and Client Secret from the Google page and paste into the form fields on the left.', SNAPSHOT_I18N_DOMAIN ) ?></li>
									</ol>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row"><label
											for="snapshot-destination-clientsecret"><?php _e( 'Client Secret', SNAPSHOT_I18N_DOMAIN ); ?></label>
								</th>
								<td><input type="password" name="snapshot-destination[clientsecret]"
										   id="snapshot-destination-clientsecret"
										   value="<?php if ( isset( $item['clientsecret'] ) ) {
											   echo sanitize_text_field( $item['clientsecret'] );
										   } ?>"/></td>
							</tr>

							<tr class="form-field">
								<th scope="row"><label
											for="snapshot-destination-redirecturi"><?php _e( 'Redirect URI', SNAPSHOT_I18N_DOMAIN ); ?></label>
								</th>
								<td><?php
									if ( ( is_multisite() ) && ( is_network_admin() ) ) {
										$item['redirecturi'] = network_admin_url( 'admin.php' );
									} else {
										$item['redirecturi'] = admin_url( 'admin.php' );
									}

									if ( isset( $_GET['page'] ) ) {
										$item['redirecturi'] = esc_url_raw( add_query_arg( 'page', $_GET['page'], $item['redirecturi'] ) );
									}

									if ( isset( $_GET['snapshot-action'] ) ) {
										$item['redirecturi'] = esc_url_raw( add_query_arg( 'snapshot-action', $_GET['snapshot-action'], $item['redirecturi'] ) );
									}

									if ( isset( $_GET['type'] ) ) {
										$item['redirecturi'] = esc_url_raw( add_query_arg( 'type', $_GET['type'], $item['redirecturi'] ) );
									}

									if ( isset( $_GET['item'] ) ) {
										$item['redirecturi'] = esc_url_raw( add_query_arg( 'item', $_GET['item'], $item['redirecturi'] ) );
									}


									echo $item['redirecturi'];
									?>
									<input type="hidden" name="snapshot-destination[redirecturi]" id="snapshot-destination-redirecturi"
										   value="<?php echo sanitize_text_field( $item['redirecturi'] ) ?>"/>

								</td>
							</tr>
						</table>
						<?php
					}
					?>
					<?php
					if ( $form_step > 2 ) {
						?>
						<p><?php _e( 'Step 3: Google Authorize', SNAPSHOT_I18N_DOMAIN ) ?><?php if ( $form_step > 3 ) {
								echo ' - ' . __( '<strong>COMPLETE</strong>' );
							} ?></p>
						<table class="form-table">
							<tr class="form-field" id="snapshot-destination-test-connection-container">
								<th scope="row">&nbsp;</th>
								<td><?php
									$this->load_class_destination( $item );
									if ( ( isset( $_GET['code'] ) ) && ( ! empty( $_GET['code'] ) ) ) {
										//echo "code[". $_GET['code'] ."]<br />";

										$this->login();
										if ( is_object( $this->client ) ) {
											$this->client->authenticate( $_GET['code'] );
											$this->destination_info['access_token'] = $this->client->getAccessToken();
											//echo "access_token<pre>"; "[". $this->destination_info['access_token'] ."]<br />";
											if ( ! empty( $this->destination_info['access_token'] ) ) {
												?>
												<p><?php _e( 'Success. The Google Access Token has been received. <strong>You must save this form one last time to retain the token.</strong> The stored token will be used in the future when connecting to Google', SNAPSHOT_I18N_DOMAIN ); ?></p>
												<input type="hidden" name="snapshot-destination[access_token]"
													   id="snapshot-destination-access_token"
													   value="<?php echo urlencode( $this->destination_info['access_token'] ) ?>" /><?php
											}
										}
									} else {
										if ( ! empty( $this->destination_info['access_token'] ) ) {
											$auth_button_label = __( 'Re-Authorize', SNAPSHOT_I18N_DOMAIN );
										} else {
											$auth_button_label = __( 'Authorize', SNAPSHOT_I18N_DOMAIN );
										}

										$auth_url = $this->getAuthorizationUrl();
										if ( ! empty( $auth_url ) ) {

											?><a id="snapshot-destination-authorize-connection" class="button-secondary"
												 href="<?php echo $auth_url; ?>"><?php echo $auth_button_label ?></a><?php
										} else {
											_e( 'Unable to obtain Authorization URL from Google', SNAPSHOT_I18N_DOMAIN );
										}
									}
									?></td>
							</tr>
						</table>
						<?php
					}
					?>
					<?php
					if ( ( $form_step > 3 ) && ( ! empty( $this->destination_info['access_token'] ) ) ) {
						?>
						<p><?php _e( 'Authorization complete.', SNAPSHOT_I18N_DOMAIN ) ?></p>
						<p>
							<strong><?php _e( 'You must save this form one last time to retain the token.', SNAPSHOT_I18N_DOMAIN ); ?></strong>
						</p>
						<input type="hidden" name="snapshot-destination[access_token]"
							   id="snapshot-destination-access_token"
							   value="<?php echo urlencode( $this->destination_info['access_token'] ) ?>"/>
						<?php

					}
					?>
				</div>
			</div>
			<?php
		}
	}

	do_action( 'snapshot_register_destination', 'SnapshotDestinationGoogleDrive' );
}