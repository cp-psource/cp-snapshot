<?php

if ( 'snapshot/cp-snapshot.php' !== WP_UNINSTALL_PLUGIN ) {
	return;
}

if ( ! isset( $psource_snapshot ) ) {
	include dirname( __FILE__ ) . '/cp-snapshot.php';
	$psource_snapshot = PSOURCESnapshot::instance();
}

$psource_snapshot->uninstall_snapshot();