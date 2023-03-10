<?php

/** @var PSOURCESnapshot_New_Ui_Tester $this */

?>


<div class="form-content">

	<div id="wps-destination-type" class="form-row">

		<div class="form-col-left">
			<label><?php esc_html_e( 'Type', 'cp-snapshot' ); ?></label>
		</div>

		<div class="form-col">
			<i class="wps-typecon dropbox"></i>
			<label><?php esc_html_e( 'Dropbox', 'cp-snapshot' ); ?></label>
		</div>

	</div>

	<div id="wps-destination-name" class="form-row">
		<div class="form-col-left">
			<label for="snapshot-destination-name">
				<?php esc_html_e( 'Name', 'cp-snapshot' ); ?> <span class="required">*</span>
			</label>
		</div>

		<div class="form-col">
			<input readonly="readonly" type="text" class="inline<?php $this->input_error_class( 'name' ); ?>" name="snapshot-destination[name]" id="snapshot-destination-name"
				value="<?php
					echo !empty($item['name'])
						? esc_attr( stripslashes( $item['name'] ) )
						: ''
					;
				?>">
			<?php $this->input_error_message( 'name' ); ?>
		</div>
	</div>

	<div id="wps-destination-auth" class="form-row">
		<div class="wps-auth-message error">
			<p><?php esc_html_e( 'Due to the new requirement from Dropbox API V2, you need to use PHP 5.5 or newer to be able to add Dropbox destination.', 'cp-snapshot' ); ?></p>
		</div>
	</div>
</div>
