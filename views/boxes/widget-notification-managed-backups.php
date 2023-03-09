<?php

/* Don't show the notice if managed backups are already enabled */
$model = new Snapshot_Model_Full_Backup();
$is_client = $model->is_dashboard_active() && $model->has_dashboard_key();
$api_key = $model->get_config( 'secret-key', '' );
if ( $is_client && false !== Snapshot_Model_Full_Remote_Api::get()->get_token() && ! empty( $api_key ) ) {
	return;
}

/* Set disable disable nonce */
$ajax_nonce = wp_create_nonce( "snapshot-disable-notif" );
$disable_notif_snapshot_page = get_option( 'snapshot-disable_notif_snapshot_page', null );

if ( isset( $disable_notif_snapshot_page ) ) {
	return;
}

?>