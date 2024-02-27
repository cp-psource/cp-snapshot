<?php

if ( 'snapshot/snapshot.php' !== WP_UNINSTALL_PLUGIN ) {
	return;
}

if ( ! isset( $psource_snapshot ) ) {
	include dirname( __FILE__ ) . '/snapshot.php';
	$psource_snapshot = PSOURCESnapshot::instance();
}

$psource_snapshot->uninstall_snapshot();