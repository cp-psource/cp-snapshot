<div class="wpmud-box-tab requirements-check-box<?php if ( !$all_good || $warning ) { echo ' open'; } ?>">
	<div class="wpmud-box-tab-title can-toggle">
		<h3><?php _e( 'Requirements Check', 'cp-snapshot' ); ?>
		<span class="wps-tag wps-tag--<?php if ( !$all_good ) { echo 'red'; } else if ( $warning ) { echo 'yellow'; } else { echo 'green'; } ?>">
		<?php
			if ( !$all_good ) {
			_e( 'FAIL', 'cp-snapshot' );
			} else if ( $warning ) {
			_e( 'WARNING', 'cp-snapshot' );
			} else {
			_e( 'PASS', 'cp-snapshot' );
			} ?>
		</span></h3>
		<i class="wps-icon i-arrow-right"></i>
	</div>
	<div class="wpmud-box-tab-content">
		<div class="wps-requirements-list">
			<div class="wpmud-box-gray">
				<table class="wps-table" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<th>
								<?php _e( 'PHP Version', 'cp-snapshot' ); ?>
								<?php if( !$checks['PhpVersion']['test'] ) : ?>
								<span class="wps-tag wps-tag--red"><?php _e( 'FAIL', 'cp-snapshot' ); ?></span>
								<?php else : ?>
								<span class="wps-tag wps-tag--green"><?php _e( 'PASS', 'cp-snapshot' ); ?></span>
								<?php endif; ?>
							</th>
							<?php if( !$checks['PhpVersion']['test'] ) : ?>
							<td>
								<?php printf( __( 'Your PHP version is out of date.
									Your current version is %s and we require 5.2 or newer.
									You\'ll need to update your PHP version to proceed.
									If you use a managed host, contact them directly to have it updated.', 'cp-snapshot' ) ,$checks['PhpVersion']['value'] ); ?>
							</td>
							<?php endif; ?>
						</tr>
						<tr>
							<th <?php if( $checks['MaxExecTime']['test'] ) : ?> colspan="2" <?php endif; ?> >
								<?php _e( 'Max Execution Time', 'cp-snapshot' ); ?>
								<?php if( !$checks['MaxExecTime']['test'] ) : ?>
								<span class="wps-tag wps-tag--yellow"><?php _e( 'WARNING', 'cp-snapshot' ); ?></span>
								<?php else : ?>
								<span class="wps-tag wps-tag--green"><?php _e( 'PASS', 'cp-snapshot' ); ?></span>
								<?php endif; ?>
							</th>
							<?php if( !$checks['MaxExecTime']['test'] ) : ?>
							<td>
								<?php printf( __( '<b><code>max_execution_time</code> is set to %s which is too low</b>.
									A minimum execution time of 150 seconds is recommended to give the migration process the
									best chance of succeeding. If you use a managed host, contact them directly to have it updated.', 'cp-snapshot' ) ,$checks['MaxExecTime']['value'] ); ?>
							</td>
							<?php endif; ?>
						</tr>
						<tr>
							<th <?php if( $checks['Mysqli']['test'] ) : ?> colspan="2" <?php endif; ?> >
								<?php _e( 'MySQLi', 'cp-snapshot' ); ?>
								<?php if( !$checks['Mysqli']['test'] ) : ?>
								<span class="wps-tag wps-tag--red"><?php _e( 'FAIL', 'cp-snapshot' ); ?></span>
								<?php else : ?>
								<span class="wps-tag wps-tag--green"><?php _e( 'PASS', 'cp-snapshot' ); ?></span>
								<?php endif; ?>
							</th>
							<?php if( !$checks['Mysqli']['test'] ) : ?>
							<td>
								<?php _e( '<b>PHP MySQLi module not found</b>.
									Snapshot needs the MySQLi module to be installed and enabled
									on the target server. If you use a managed host, contact them
									directly to have this module installed and enabled.', 'cp-snapshot' );
									?>
							</td>
							<?php endif; ?>
						</tr>
						<tr>
							<th <?php if( $checks['Zip']['test'] ) : ?> colspan="2" <?php endif; ?> >
								<?php _e( 'GZip', 'cp-snapshot' ); ?>
								<?php if( !$checks['Zip']['test'] ) : ?>
								<span class="wps-tag wps-tag--red"><?php _e( 'FAIL', 'cp-snapshot' ); ?></span>
								<?php else : ?>
								<span class="wps-tag wps-tag--green"><?php _e( 'PASS', 'cp-snapshot' ); ?></span>
								<?php endif; ?>
							</th>
							<?php if( !$checks['Zip']['test'] ) : ?>
							<td>
								<?php _e( '<b>PHP Zip module not found</b>.
									To unpack the zip file, Snapshot needs the Zip module to be installed and enabled.
									If you use a managed host, contact them directly to have it updated.', 'cp-snapshot' );
									?>
							</td>
							<?php endif; ?>
						</tr>
					</tbody>
				</table>
			</div>
			<p><a href="" class="button button-outline button-gray"><?php _e('Re-Check', 'cp-snapshot'); ?></a></p>
		</div>
	</div>
</div>