<?php $destinations = array();

foreach ( PSOURCESnapshot::instance()->config_data['destinations'] as $key => $item ){
	$type = $item['type'];

	if ( ! isset( $destinations[ $type ] ) ){
		$destinations[ $type ] = array();
	}

	$destinations[ $type ][ $key ] = $item;
} ?>

<section class="wpmud-box wpsd-widget-google">

	<div class="wpmud-box-title has-typecon has-button">

		<i class="wps-typecon google"></i>

		<h3><?php _e( 'Google Drive', 'cp-snapshot' ); ?></h3>

		<a class="button button-small button-outline" href="<?php echo add_query_arg( array( 'snapshot-action' => 'add' , 'type' => 'google-drive' ), PSOURCESnapshot::instance()->snapshot_get_pagehook_url('snapshots-newui-destinations') ); ?>" class="button button-outline"><?php _e( 'Add Destination', 'cp-snapshot' ); ?></a>

	</div>

	<div class="wpmud-box-content">

		<div class="row">

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<?php $this->render("destinations/partials/google-drive-destination-list", false, array('item' => $item,'destinations' => ( isset( $destinations[ 'google-drive' ] ) ? $destinations[ 'google-drive' ] : array() ) ), false, false); ?>

			</div>

		</div>

	</div>

</section>