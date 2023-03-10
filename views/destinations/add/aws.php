<?php /** @var PSOURCESnapshot_New_Ui_Tester $this */ ?>

<div class="form-content">

	<div id="wps-destination-type" class="form-row">

		<div class="form-col-left">
			<label><?php _e( 'Type', 'cp-snapshot' ); ?></label>
		</div>

		<div class="form-col">
			<i class="wps-typecon aws"></i>
			<label><?php _e( 'Amazon S3', 'cp-snapshot' ); ?></label>
		</div>

	</div>

	<div id="wps-destination-name" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-name"><?php _e( 'Name', 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">

			<input name="snapshot-destination[name]" id="snapshot-destination-name" type="text" class="<?php $this->input_error_class( 'name' ); ?>"
			       value="<?php if ( isset( $item['name'] ) ) { echo esc_attr( stripslashes( sanitize_text_field( $item['name'] ) ) ); } ?>">
			<?php $this->input_error_message( 'name' ); ?>
		</div>

	</div>

	<div id="wps-destination-id" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-awskey"><?php _e( 'AWS Access Key ID', 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">
			<input type="text" name="snapshot-destination[awskey]" id="snapshot-destination-awskey" class="<?php $this->input_error_class( 'awskey' ); ?>"
			       value="<?php if ( isset( $item['awskey'] ) ) { echo esc_attr( sanitize_text_field( $item['awskey'] ) ); } ?>">

			<?php $this->input_error_message( 'awskey' ); ?>

			<p><small><?php echo sprintf( __( 'You can get your access keys via the <a target="_blank" href="%s">AWS Console</a>', 'cp-snapshot' ), esc_url( 'https://aws-portal.amazon.com/gp/aws/securityCredentials' ) ); ?></small></p>
		</div>

	</div>

	<div id="wps-destination-key" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-secretkey"><?php _e( "AWS Secret Access Key", 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">
			<input type="password" name="snapshot-destination[secretkey]" id="snapshot-destination-secretkey" class="<?php $this->input_error_class( 'secretkey' ); ?>" value="<?php if ( isset( $item['secretkey'] ) ) { echo sanitize_text_field( $item['secretkey'] ); } ?>" />
			<?php $this->input_error_message( 'secretkey' ); ?>
		</div>

	</div>
	<?php if ( ! isset( $item['ssl'] ) ) {
			$item['ssl'] = "yes";
	} ?>
	<div id="wps-destination-ssl" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-ssl"><?php _e( "Use SSL Connection", 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">
			<div class="wps-input--checkbox">
				<input name="snapshot-destination[ssl]" type="hidden" value="no" <?php checked( $item['ssl'], "no" ); ?> />
				<input name="snapshot-destination[ssl]" id="snapshot-destination-ssl" type="checkbox" <?php checked( $item['ssl'], "yes" ); ?> value="yes" />
				<label for="snapshot-destination-ssl"></label>
				<?php $this->input_error_message( 'ssl' ); ?>
			</div>
		</div>

	</div>

	<?php if ( ! isset( $item['region'] ) ) { $item['region'] = AmazonS3::REGION_US_E1; } ?>

	<div id="wps-destination-region" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-region"><?php _e( 'AWS Region', 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">

			<select class="inline<?php $this->input_error_class( 'region' ); ?>" name="snapshot-destination[region]" id="snapshot-destination-region">

				<?php foreach ( $item_object->get_regions() as $_key => $_name ) : ?>

					<option value="<?php echo esc_attr( $_key ); ?>" <?php if ( $item['region'] == $_key ) { echo ' selected="selected" '; } ?>>
						<?php echo esc_html( $_name ); ?> (<?php echo esc_html( $_key ); ?>)
					</option>

				<?php endforeach; ?>

			</select>

			<?php $this->input_error_message( 'region' ); ?>

			<div id="snapshot-destination-region-other-container" <?php
			if ( $item['region'] !== 'other' ) {
				echo ' style="display: none;" ';
			} ?>>
				<br/><label
					id="snapshot-destination-region-other"><?php _e( 'Alternate Region host', 'cp-snapshot' ) ?></label><br/>
				<input name="snapshot-destination[region-other]"
				       id="snapshot-destination-region-other"
				       value="<?php echo esc_attr( $item['region-other'] ); ?>"/>
			</div>

		</div>

	</div>

	<?php if ( ! isset( $item['storage'] ) ) { $item['storage'] = AmazonS3::STORAGE_STANDARD; } ?>

	<div id="wps-destination-storage" class="form-row">

		<div class="form-col-left">
			<label for="snapshot-destination-storage"><?php _e( 'Storage Type', 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">

			<select class="inline<?php $this->input_error_class( 'storage' ); ?>" name="snapshot-destination[storage]" id="snapshot-destination-storage">

				<?php foreach ( $item_object->get_storage() as $_key => $_name ) : ?>

					<option value="<?php echo esc_attr( $_key ); ?>" <?php if ( $item['storage'] == $_key ) { echo ' selected="selected" '; } ?>>
						<?php echo esc_html( $_name ); ?> (<?php echo esc_attr( $_key ); ?>)
					</option>

				<?php endforeach; ?>

			</select>

			<?php $this->input_error_message( 'storage' ); ?>
		</div>
	</div>

	<div id="wps-destination-bucket" class="form-row">

		<div class="form-col-left">
			<label><?php _e( 'Bucket', 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">

			<div class="wps-aws-bucket-align">

				<?php
				if ( isset( $item['bucket'] ) ) { ?>
					<span id="snapshot-destination-bucket-display"><?php echo esc_html( $item['bucket'] ); ?></span>
					<input
					type="hidden" name="snapshot-destination[bucket]"
					id="snapshot-destination-bucket"
					value="<?php if ( isset( $item['bucket'] ) ) {
						echo esc_attr( $item['bucket'] );
					} ?>" /><?php
				} ?>

				<button id="snapshot-destination-aws-get-bucket-list" class="button-seconary button button-gray<?php if ( empty ( esc_html( $item['bucket'] ) ) ) { echo ' wps-last-item'; } ?>" name=""><?php _e( 'Select Bucket', 'cp-snapshot' ); ?></button>

			</div>

			<div id="snapshot-ajax-destination-bucket-error" style="display:none" class="inline-notice err"></div>
			<div id="snapshot-ajax-destination-bucket-result" style="display:none">
				<select name="snapshot-destination[bucket]" id="snapshot-destination-bucket-list"></select>
			</div>

			<?php $this->input_error_message( 'bucket' ); ?>
		</div>

	</div>

	<div id="wps-destination-permission" class="form-row">
		<div class="form-col-left">
			<label><?php _e( "File Permissions", 'cp-snapshot' ); ?> <span class="required">*</span></label>
		</div>

		<div class="form-col">

			<?php if ( ! isset( $item['acl'] ) ) {
				$item['acl'] = AmazonS3::ACL_PRIVATE;
			} ?>
			<select name="snapshot-destination[acl]" id="snapshot-destination-acl" class="<?php $this->input_error_class( 'acl' ); ?>">
				<option value="<?php echo esc_attr( AmazonS3::ACL_PRIVATE ); ?>" <?php selected( $item['acl'], AmazonS3::ACL_PRIVATE ); ?>>
					<?php _e( 'Private', 'cp-snapshot' ) ?></option>
				<option value="<?php echo esc_attr( AmazonS3::ACL_PUBLIC ); ?>" <?php selected( $item['acl'], AmazonS3::ACL_PUBLIC ); ?>>
					<?php _e( 'Public Read', 'cp-snapshot' ) ?></option>
				<option value="<?php echo esc_attr( AmazonS3::ACL_OPEN ); ?>" <?php selected( $item['acl'], AmazonS3::ACL_OPEN ); ?>>
					<?php _e( 'Public Read/Write', 'cp-snapshot' ) ?></option>
				<option value="<?php echo esc_attr( AmazonS3::ACL_AUTH_READ ); ?>" <?php selected( $item['acl'], AmazonS3::ACL_AUTH_READ ); ?>>
					<?php _e( 'Authenticated Read', 'cp-snapshot' ) ?></option>
			</select>

			<?php $this->input_error_message( 'acl' ); ?>

			<p><small><?php _e('Control who will have access to your backup files.', 'cp-snapshot'); ?></small></p>
		</div>

	</div>

	<div id="wps-destination-dir" class="form-row">

		<div class="form-col-left">
			<label><?php _e( "Directory (optional)", 'cp-snapshot' ); ?></label>
		</div>

		<div class="form-col">
			<input type="text" name="snapshot-destination[directory]" id="snapshot-destination-directory" placeholder="i.e. static/files" value="<?php if ( isset( $item['directory'] ) ) { echo $item['directory']; } ?>"/>

			<p><small><?php _e( "If directory is blank the snapshot file will be stored at the bucket root. If the directory is provided it will be created inside the bucket. This is a global setting and will be used by all snapshot configurations using this destination. You can also define a director used by a specific snapshot.", 'cp-snapshot' ); ?></small></p>

			<button id="snapshot-destination-test-connection" class="button button-gray"><?php _e( "Test Connection", 'cp-snapshot' ); ?></button>
			<div id="snapshot-ajax-destination-test-result" style="display:none"></div>
		</div>

	</div>

	<input type="hidden" name="snapshot-destination[type]" id="snapshot-destination-type" value="<?php echo $item['type'] ?>"/>


</div>